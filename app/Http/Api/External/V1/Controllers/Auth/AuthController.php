<?php

namespace App\Http\Api\External\V1\Controllers\Auth;

use App\Auth\ThrottlesAuth\AuthCase;
use App\Auth\ThrottlesAuth\ThrottleAuth;
use App\Auth\ThrottlesAuth\ThrottlesAuth;
use App\Auth\VerificationCode\VerificationCase;
use App\Auth\VerificationCode\VerificationCodeManager;
use App\Http\Api\External\V1\Controllers\Controller;
use App\Http\Api\External\V1\Requests\Auth\RegisterRequest;
use App\Http\Api\External\V1\Traits\VerifyCode;
use App\Http\Middleware\VerificationCode\LimitVerificationCodeSending;
use App\Models\User\User;
use App\Services\DynamicsCrm\Exceptions\BadRequestException;
use App\Services\DynamicsCrm\Exceptions\NotFoundException;
use App\Services\User\Dto\CreateUserDto;
use App\Services\User\Exceptions\UserRegistrationBadRequestException;
use App\Services\User\UserService;
use Carbon\Carbon;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Throwable;
use RuntimeException;

/**
 * Class AuthController
 *
 * @package App\Http\Api\External\V1\Controllers\Auth
 */
class AuthController extends Controller
{
//    use ThrottlesAuth;
    use ThrottleAuth;
    use VerifyCode;

    public function __construct(
        private VerificationCodeManager $verificationCodeManager,
        private UserService $userService,
        private int $maxAttempts,
        private int $tokenExpiration,
    ) {
        $this->middleware(
            LimitVerificationCodeSending::class . ':phone,case'
        )->only('sendVerificationCode');
        $this->middleware('auth.attempts')->only('loginByPassword');
    }

    /**
     * @param RegisterRequest $request
     *
     * @return Response
     * @throws ValidationException
     * @throws BadRequestException
     * @throws NotFoundException
     * @throws Throwable
     */
    public function register(RegisterRequest $request): Response
    {
        if ($request->input('case') == 'create') {
            try {
                $this->checkKeyRegistration($request->input('phone'), $request->input('key_registration'));
            } catch (RuntimeException) {
                return $this->response()->setStatusCode(409);
            }
        }

        $dto = new CreateUserDto(
            firstName: $request->input('first_name'),
            lastName: $request->input('last_name'),
            middleName: $request->input('middle_name'),
            phone: $request->input('phone'),
            email: $request->input('email'),
            birthDate: Carbon::parse($request->input('birth_date')),
        );

        if ($request->input('case') == 'create') {
            try {
                $user = $this->userService->createUser($dto);
            } catch (UserRegistrationBadRequestException) {
                throw ValidationException::withMessages([
                    'phone' => ['The user data is probably incorrect.'],
                ])->status(409);
            }
        } elseif ($request->input('case') == 'validate') {
            $users = $this->userService->getUsersForVerification(
                str_replace("+", "", $request->input('phone'))
            );
            foreach ($users as $user) {
                if (!isset($user['birthDate'])) {
                    continue;
                }

                $dateCrm = new Carbon($user['birthDate']);

                $date = new Carbon($request->input('birth_date'));
                $percentage = 0;

                if (isset($user['firstName'])) {
                    $percentage += strtolower($user['firstName']) == strtolower($request->input('first_name'))? 1: 0;
                }

                if (isset($user['lastName'])) {
                    $percentage += strtolower($user['lastName']) == strtolower($request->input('last_name'))? 1: 0;
                }

                $percentage += $dateCrm->format('Y-m-d') == $date->format('Y-m-d')? 1: 0;

                if ($percentage == 3) {
                    $rightUser = $user;
                }
            }

            if (!isset($rightUser)) {
                return $this->response(['message' => 'User with this phone number already exists.',
                    'errors' =>
                        ['phone' =>
                            ['User with this phone number already exists.']
                        ]
                ])->setStatusCode(425);
            }

            if ($request->input('email') != ($rightUser['email'] ?? '')) {
                $this->userService->updateUserEmailByCrmId($rightUser['id'], $request->input('email'));
            }

            $user = $this->userService->createDbUser($rightUser);
        }

        $this->userService->syncUser($user);

        return $this->response($this->getTokenDataByUser($user));
    }

    /**
     * @param Request $request
     *
     * @return Response
     * @throws BadRequestException
     * @throws NotFoundException
     * @throws ValidationException
     */
    public function loginByPassword(Request $request): Response
    {
        $this->validate($request, [
            'phone' => 'required|phone_number',
            'password' => 'required|string',
        ]);

        $user = $this->findUser($request->input('phone'));
        if ($user === null || !Hash::check($request->input('password'), $user->password)) {
            $this->assertLimitOfAuthAttempts($request);
            $this->fail('password');
        }

        $this->userService->syncUser($user);
        $this->clearAuthAttempts($request->ip(), $user->phone);
        return $this->response($this->getTokenDataByUser($user));
    }

    /**
     * @param Request $request
     *
     * @return Response
     * @throws BadRequestException
     * @throws NotFoundException
     * @throws ValidationException
     */
    public function loginByVerificationCode(Request $request): Response
    {
        $this->validate($request, [
            'phone' => 'required|phone_number',
            'verification_code' => 'required|string',
        ]);

        $this->verifyCode(VerificationCase::login(), $request->phone, $request->input('verification_code'));
        $this->verificationCodeManager->forget(VerificationCase::login(), $request->input('phone'));
        $phone = $request->input('phone');

        $user = $this->findUser($phone);

        $checkUser = $this->userService->fillProfile($phone);

        if ($user != null) {
            if (!$user?->status) {
                return $this->response([
                    'token' => null,
                    'expires_at' => null,
                    'fill_profile' => true,
                    'case' => 'deactivated',
                    'key_registration' => null,
                ]);
            }

            $checkUser['case'] = null;
            $checkUser['token'] = 'true';
            $checkUser['fill_profile'] = false;
            $this->userService->syncUser($user);

            $blocker = sha1($request->route()->getDomain().'|'.$request->ip());
            Cache::forget($blocker);
            Cache::forget($blocker.':timer');
        }

        if ($checkUser['case'] == 'crm') {
            $user = $this->userService->getUsersForVerification($phone)[0];
            $user = $this->userService->createDbUser($user);
        }

        $authData = $checkUser['token']?array_merge($checkUser, $this->getTokenDataByUser($user)):$checkUser;

        $this->clearAuthAttempts(request()->ip(), $phone);

        return $this->response($authData);
    }

    /**
     * @param Request $request
     *
     * @return Response
     * @throws AuthenticationException
     * @throws ValidationException
     */
    public function setPassword(Request $request): Response
    {
        $user = $this->getAuthUser();
        $this->validate($request, [
            'password' => ['required', $this->passwordValidator()],
        ]);

        if ($user->hasPassword()) {
            throw new AccessDeniedHttpException('User already set password.');
        }

        $user->changePassword($request->input('password'));
        $user->save();

        return $this->empty();
    }

    /**
     * @param Request $request
     *
     * @return Response
     * @throws ValidationException
     */
    public function resetPassword(Request $request): Response
    {
        $this->validate($request, [
            'phone' => 'required|phone_number|exists:users',
            'verification_code' => 'required|string',
            'password' => ['required', $this->passwordValidator()],
        ]);

        $user = $this->findUser($request->input('phone'));
        if ($user === null) {
            $this->fail('verification_code');
        }

        $this->verifyCode(VerificationCase::passwordReset(), $user->phone, $request->input('verification_code'));

        $user->changePassword($request->input('password'));
        $user->save();

        $this->verificationCodeManager->forget(VerificationCase::passwordReset(), $request->input('phone'));

        return $this->response($this->getTokenDataByUser($user));
    }

    /**
     * @param Request $request
     *
     * @return Response
     * @throws ValidationException
     */
    public function verifyCodeToResetPassword(Request $request): Response
    {
        $this->validate($request, [
            'phone' => 'required|phone_number|exists:users',
            'verification_code' => 'required|string',
        ]);

        $this->verifyCode(
            VerificationCase::passwordReset(),
            $request->input('phone'),
            $request->input('verification_code')
        );

        return $this->empty();
    }

    /**
     * @param Request $request
     *
     * @return Response
     * @throws ValidationException
     * @throws AuthenticationException
     */
    public function changePassword(Request $request): Response
    {
        $user = $this->getAuthUser();
        $this->validate($request, [
            'current_password' => ['required', 'string'],
            'new_password' => ['required', $this->passwordValidator()],
        ]);

        if (!Hash::check($request->input('current_password'), $user->password)) {
            $this->fail('current_password');
        }

        $user->changePassword($request->input('new_password'));
        $user->save();

        return $this->empty();
    }

    public function logout(Request $request): Response
    {
        $request->user()->currentAccessToken()->delete();

        return $this->empty();
    }

    public function refresh(Request $request): Response
    {
        $request->user()->currentAccessToken()->delete();

        /** @var User $user */
        $user = $this->guard()->user();

        return $this->response($this->getTokenDataByUser($user));
    }

    protected function username(): string
    {
        return 'phone';
    }

    private function passwordValidator(): Password
    {
        return Password::min(8)->numbers()->letters()->mixedCase();
    }

    /**
     * @param string $failedFor
     *
     * @return void
     * @throws ValidationException
     */
    private function fail(string $failedFor): void
    {
        throw ValidationException::withMessages([
            $failedFor => 'These credentials do not match our records.',
        ]);
    }

    private function findUser(string $phone): ?User
    {
        if ($user = User::onlyTrashed()->wherePhone($phone)->first()) {
            $this->userService->restoreByPhone($user);
        }

        $variant = [
            $phone, "+$phone", str_replace("+", "", $phone)
        ];

        return User::whereIn('phone', $variant)->first();
    }

    private function getTokenDataByUser(User $user): array
    {
        return [
            'token' => $user->createToken('api_token')->plainTextToken,
            'expires_at' => $this->tokenExpiration ?
                Carbon::now()->addMinutes($this->tokenExpiration)->timestamp : null,
        ];
    }

    private function checkKeyRegistration(string $phone, string $keyRegistration)
    {
        $phone = str_replace("+", "", $phone);
        if (Cache::get($phone.'key_registration') == $keyRegistration) {
            Cache::forget($phone.'key_registration');
        } else {
            throw new RuntimeException('Undefined registration key.');
        }
    }

    private function compareStrings($s1, $s2)
    {
        //one is empty, so no result
        if (strlen($s1)==0 || strlen($s2)==0) {
            return 0;
        }

        //replace none alphanumeric charactors
        //i left - in case its used to combine words
        $s1clean = preg_replace("/[^A-Za-z0-9-]/", ' ', $s1);
        $s2clean = preg_replace("/[^A-Za-z0-9-]/", ' ', $s2);

        //remove double spaces
        while (strpos($s1clean, "  ")!==false) {
            $s1clean = str_replace("  ", " ", $s1clean);
        }
        while (strpos($s2clean, "  ")!==false) {
            $s2clean = str_replace("  ", " ", $s2clean);
        }

        //create arrays
        $ar1 = explode(" ", $s1clean);
        $ar2 = explode(" ", $s2clean);
        $l1 = count($ar1);
        $l2 = count($ar2);

        //flip the arrays if needed so ar1 is always largest.
        if ($l2>$l1) {
            $t = $ar2;
            $ar2 = $ar1;
            $ar1 = $t;
        }

        //flip array 2, to make the words the keys
        $ar2 = array_flip($ar2);


        $maxwords = max($l1, $l2);
        $matches = 0;

        //find matching words
        foreach ($ar1 as $word) {
            if (array_key_exists($word, $ar2)) {
                $matches++;
            }
        }

        return ($matches / $maxwords) * 100;
    }
}
