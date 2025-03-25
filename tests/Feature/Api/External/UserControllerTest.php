<?php

namespace Tests\Feature\Api\External;

use App\Auth\VerificationCode\VerificationCase;
use App\Auth\VerificationCode\VerificationCode;
use App\Auth\VerificationCode\VerificationCodeRepository;
use App\Models\User\User;
use App\Services\DynamicsCrm\DynamicsCrmClient;
use Tests\ApiRequests;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
//    use ApiRequests;
//
//    public User $user;
//    public string $code;
//    public string $case;
//    public string $key_registration;
//    public VerificationCodeRepository $codeRepository;
//
//    protected function setUp(): void
//    {
//        parent::setUp();
//
//        /** @var User $user */
//        $user = User::factory(1)->state(['phone' => '+79999999999'])->create()->first();
//        $this->user = $user;
//        $token = $this->user->createToken('auth_token')->plainTextToken;
//        $this->withHeader('Authorization', "Bearer {$token}");
//
//        $this->code = config('verification_code.generators.fixed.code');
//        $this->case = 'create';
//        $this->key_registration = 'test';
//        $this->codeRepository = app(VerificationCodeRepository::class);
//    }
//
//    public function testItMustDeleteUser(): void
//    {
//        $this->mock(DynamicsCrmClient::class, fn($mock) => $mock->shouldReceive('deleteUser')->once());
//
//        $this->codeRepository->remember(
//            VerificationCase::deleteAccount(),
//            $this->user->phone,
//            new VerificationCode($this->code)
//        );
//        $resp = $this->delete('/api/v1/user', ['code' => $this->code, 'reason' => 'some reason']);
//        $resp->assertStatus(204);
//
//        $this->assertSoftDeleted($this->user);
//    }
//
//    public function testItMustRestoreDeletedUser(): void
//    {
//        $this->mock(DynamicsCrmClient::class, fn($mock) => $mock->shouldReceive('restoreUser')->once());
//        //soft delete user
//        $this->user->delete();
//
//        $this->codeRepository->remember(
//            VerificationCase::registration(),
//            $this->user->phone,
//            new VerificationCode($this->code)
//        );
//
//        $registrationData = [
//            'phone' => $this->user->phone,
//            'verification_code' => $this->code,
//            'key_registration' => $this->key_registration,
//            'case' => $this->case,
//            'first_name' => 'new name',
//            'last_name' => 'new name',
//            'middle_name' => 'new name',
//            'birth_date' => now(),
//            'birthDate' => now(),
//            'email' => 'email@email.com'
//        ];
//
//        $resp = $this->post('/api/v1/auth/register', $registrationData);
//        $resp->assertOk();
//
//        $this->assertModelExists($this->user);
//        $this->user = $this->user->fresh();
//        unset($registrationData['verification_code']);
//        unset($registrationData['birth_date']);
//
//        collect($registrationData)->map(fn ($value, $key) => $this->assertEquals($this->user->$key, $value));
//    }
}
