<?php

namespace App\Services\User;

use App\Models\Account\Account;
use App\Models\Building\Building;
use App\Models\Document\Document;
use App\Models\Role;
use App\Models\Sales\Deal;
use App\Models\Sales\StepMapper;
use App\Models\User\DeletingReason;
use App\Models\User\User;
use App\Services\Customer\CustomerRepository;
use App\Services\Document\DocumentRepository;
use App\Services\DynamicsCrm\DynamicsCrmClient;
use App\Services\DynamicsCrm\Exceptions\BadRequestException;
use App\Services\DynamicsCrm\Exceptions\NotFoundException;
use App\Services\DynamicsCrm\Exceptions\UnableToCreateUserException;
use App\Services\DynamicsCrm\Exceptions\UnableToDeleteUserException;
use App\Services\DynamicsCrm\Exceptions\UnableToRestoreUserException;
use App\Services\User\Dto\CreateUserDto;
use App\Services\User\Dto\UploadUserDocumentDto;
use App\Services\User\Exceptions\PropertyAlreadyInFavoritesException;
use App\Services\User\Exceptions\UserRegistrationBadRequestException;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Psr\SimpleCache\InvalidArgumentException;
use RuntimeException;
use Throwable;

/**
 * Class UserService
 *
 * @package App\Services\User
 */
class UserService
{

    public function __construct(
        private readonly DynamicsCrmClient  $dynamicsCrmClient,
        private readonly DocumentRepository $userDocumentRepository,
        private readonly CustomerRepository $customerRepository,
    ) {
    }

    /**
     * @param CreateUserDto $dto
     *
     * @return User
     * @throws BadRequestException
     * @throws NotFoundException
     * @throws Throwable
     */
    public function createUser(CreateUserDto $dto): User
    {
        if ($user = User::onlyTrashed()->wherePhone($dto->phone)->first()) {
            return $this->restore($user, $dto);
        }

        try {
            $response = $this->dynamicsCrmClient->createUser($dto);

            if (isset($response['TypeMessage']) && $response['TypeMessage'] === 3) {
                throw new UserRegistrationBadRequestException();
            }
        } catch (UnableToCreateUserException) {
            throw new RuntimeException('Can\'t get user from CRM.');
        }

        /** @var User $user */
        $user = User::create([
            'phone' => $dto->phone,
            'first_name' => $dto->firstName,
            'last_name' => $dto->lastName,
            'middle_name' => $dto->middleName??null,
            'birth_date' => $dto->birthDate,
            'email' => $dto->email,
            'crm_id' => $response['id'],
        ]);

        return $user;
    }

    /**
     * @param array $userFromCrm
     * @return User
     */
    public function createDbUser(array $userFromCrm): User
    {
        /** @var User $user */
        $user = User::create([
            'phone' => $userFromCrm['phone'],
            'first_name' => $userFromCrm['firstName'],
            'last_name' => $userFromCrm['lastName'],
            'middle_name' => $userFromCrm['middleName']??null,
            'birth_date' => $userFromCrm['birthDate']??Carbon::parse('00:00:00 01.01.1970'),
            'email' => $userFromCrm['email']??'',
            'crm_id' => $userFromCrm['id'],
        ]);

        return $user;
    }

    /**
     * @throws NotFoundException
     * @throws BadRequestException
     */
    public function updateUserEmail(User $user, string $email): void
    {
        $this->dynamicsCrmClient->updateUserEmail($user, $email);
    }

    /**
     * @throws NotFoundException
     * @throws BadRequestException
     */
    public function updateUserEmailByCrmId(string $userCrmId, string $email): void
    {
        $this->dynamicsCrmClient->updateUserEmailByCrmId($userCrmId, $email);
    }

    /**
     * @param User          $user
     * @param CreateUserDto $dto
     *
     * @return User
     * @throws BadRequestException
     * @throws NotFoundException
     */
    public function restore(User $user, CreateUserDto $dto): User
    {
        try {
            $this->dynamicsCrmClient->restoreUser($user);
        } catch (UnableToRestoreUserException) {
            throw new RuntimeException('Can\'t delete user from CRM.');
        }

        $user->update([
            'first_name' => $dto->firstName,
            'last_name' => $dto->lastName,
            'middle_name' => $dto->middleName,
            'birth_date' => $dto->birthDate,
            'email' => $dto->email,
        ]);
        $user->restore();

        return $user->fresh();
    }

    /**
     * @param User $user
     * @return User
     * @throws BadRequestException
     * @throws NotFoundException
     */
    public function restoreByPhone(User $user): User
    {
        try {
            $this->dynamicsCrmClient->restoreUser($user);
        } catch (UnableToRestoreUserException) {
            throw new RuntimeException('Can\'t delete user from CRM.');
        }

        $user->restore();

        return $user->fresh();
    }

    /**
     * @param User                  $user
     * @param DeletingReason|string $reason
     *
     * @return void
     * @throws BadRequestException
     * @throws NotFoundException
     */
    public function delete(User $user, DeletingReason | string $reason): void
    {
        try {
            $this->dynamicsCrmClient->deleteUser($user, $reason);
        } catch (UnableToDeleteUserException) {
            throw new RuntimeException('Can\'t delete user from CRM.');
        }

        $user->forceDelete();
    }

    /**
     * @param User      $user
     * @param Account[] $accounts
     */
    public function syncAccount(User $user, array $accounts): void
    {
        $actualAccountNumbers = [];
        foreach ($accounts as $account) {
            $user->relationships()->firstOrCreate([
                'account_number' => $account->getNumber(),
            ], [
                'account_number' => $account->getNumber(),
                'role' => Role::owner(),
            ]);

            $actualAccountNumbers[] = $account->getNumber();
        }

        $user->relationships()->whereNotIn('account_number', $actualAccountNumbers)->delete();
    }

    /**
     * @param User      $user
     * @param array $accounts
     */
    public function syncAccountForUser(User $user, array $accounts): void
    {
        $actualAccountNumbers = [];
        foreach ($accounts as $account) {
            $user->relationships()->firstOrCreate([
                'account_number' => $account,
            ], [
                'account_number' => $account,
                'role' => Role::owner(),
            ]);

            $actualAccountNumbers[] = $account;
        }

        $user->relationships()->whereNotIn('account_number', $actualAccountNumbers)->delete();
    }

    /**
     * @throws NotFoundException
     * @throws BadRequestException
     */
    public function syncUser(User $user): void
    {
        $customer = $this->customerRepository->getById($user->crm_id);

        $phone = $customer->getPhone();
        if (str_starts_with($phone, '8')) {
            $phone = '+7' . $phone;
        } elseif (!str_starts_with($phone, '+')) {
            $phone = '+' . $phone;
        }

        $user->update([
            'first_name' => $customer->getFirstName(),
            'last_name' => $customer->getLastName(),
            'middle_name' => $customer->getMiddleName(),
            'birth_date' => $customer->getBirthDate(),
            'email' => $customer->getEmail(),
            'phone' => $phone,
        ]);
    }

    /**
     * @throws NotFoundException
     * @throws BadRequestException
     */
    public function fillProfile(string $phone): array
    {
        $phone = str_replace("+", "", $phone);

        $customers = $this->customerRepository->getByPhone($phone)['customerList'];

        if (count($customers) === 1) {
            return [
                'token' => true,
                'fill_profile' => false,
                'case' => 'crm',
                'key_registration' => null
            ];
        }

        if (count($customers) > 1) {
            return [
                'token' => null,
                'expires_at' => null,
                'fill_profile' => true,
                'case' => 'validate',
                'key_registration' => null,
            ];
        }

        $key = sha1($phone.time());
        Cache::put($phone.'key_registration', $key, now()->addMinutes(10));

        return [
            'token' => null,
            'expires_at' => null,
            'fill_profile' => true,
            'case' => 'create',
            'key_registration' => $key
        ];
    }

    /**
     * @throws NotFoundException
     * @throws BadRequestException
     */
    public function getUsersForVerification(string $phone): array
    {
        $phone = str_replace("+", "", $phone);

        return $this->customerRepository->getByPhone($phone)['customerList'];
    }

    /**
     * @param User $user
     *
     * @return Document[]
     * @throws BadRequestException
     * @throws InvalidArgumentException
     * @throws NotFoundException
     */
    public function getDocuments(User $user): array
    {
        $documents = $this->userDocumentRepository->getDocumentsByUser($user);
        $this->userDocumentRepository->setDocumentsCacheIdsByUser($user, $documents);

        return $documents;
    }

    /**
     * @param User                  $user
     * @param UploadUserDocumentDto $dto
     *
     * @throws BadRequestException
     * @throws InvalidArgumentException
     * @throws NotFoundException
     */
    public function uploadDocument(User $user, UploadUserDocumentDto $dto): void
    {
        $this->userDocumentRepository->setDocumentsCacheIdsByUser($user, []);

        $this->dynamicsCrmClient->uploadDocumentByUserId($user->crm_id, $dto);
    }

    /**
     * @throws NotFoundException
     * @throws InvalidArgumentException
     * @throws BadRequestException
     */
    public function hasDocument(User $user, string $documentId): bool
    {
        return in_array($documentId, $this->userDocumentRepository->getDocumentIdsByUser($user));
    }

    /**
     * @throws PropertyAlreadyInFavoritesException
     */
    public function addPropertyToFavorites(User $user, string $id, string $url): void
    {
        $favoriteProperty = $user->favoriteProperties()->firstWhere([
            'property_crm_id' => $id,
        ]);
        if ($favoriteProperty) {
            throw new PropertyAlreadyInFavoritesException();
        }

        $user->favoriteProperties()->firstOrCreate([
            'property_crm_id' => $id,
            'url' => $url,
        ]);
    }

    public function removePropertyFromFavorites(User $user, string $id): void
    {
        $user->favoriteProperties()->where([
            'property_crm_id' => $id,
        ])->delete();
    }

    /**
     * @param User      $user
     * @param Account[] $accounts
     */
    public function subscribeToPushTopicsV1(User $user, array $accounts): void
    {
        // phpcs:disable
        $topic_test = app()->environment(['local', 'staging']) ? '_test' : '';

        $general = 'General';

        $news = 'News';
        $notif = 'Notification';

        $project_uk = 'ProjectUK';
        $owners = 'Owners';
        $customers = 'Customers';
        $reality_types = 'RealtyTypes';

        $build = 'Corp';
        $role = 'Role';

        $subscribed_to_push_topics = [];

        $general_topic = $general . $topic_test;

        if (!in_array($general_topic, $subscribed_to_push_topics)) {
            $subscribed_to_push_topics[] = $general_topic;
        }

        $news_general = $news . '_' . $general . $topic_test;

        if ($user->canReceiveObjectNewsPush() && !in_array($news_general, $subscribed_to_push_topics)) {
            $subscribed_to_push_topics[] = $news_general;
        }

        if ($accounts) {
            foreach ($accounts as $account) {
                $role_id = $account->role->value ?? "";

                $news_project_uk = $news . '_' . $project_uk . $topic_test;

                if (!in_array($news_project_uk, $subscribed_to_push_topics)) {
                    $subscribed_to_push_topics[] = $news_project_uk;
                }

                $notification_project_uk = $notif. '_' . $owners . '_' . $project_uk . $topic_test;

                if (!in_array($notification_project_uk, $subscribed_to_push_topics)) {
                    $subscribed_to_push_topics[] = $notification_project_uk;
                }

                if (!empty($role_id)) {
                    $notification_project_uk_role_id = $notif. '_' . $owners . '_' . $project_uk . '_' .
                        $role . '_' . $role_id . $topic_test;

                    if (!in_array($notification_project_uk_role_id, $subscribed_to_push_topics)) {
                        $subscribed_to_push_topics[] = $notification_project_uk_role_id;
                    }
                }

                if ($uk_project = $account->getUkProject()) {
                    $uk_project_id = $uk_project->id;

                    if ($uk_project_id) {
                        $news_project_uk_id = $news . '_' . $project_uk . '_' . $uk_project_id . $topic_test;

                        if (!in_array($news_project_uk_id, $subscribed_to_push_topics)) {
                            $subscribed_to_push_topics[] = $news_project_uk_id;
                        }

                        $notification_project_uk_id = $notif. '_' . $owners . '_' . $project_uk . '_' .
                            $uk_project_id . $topic_test;

                        if (!in_array($notification_project_uk_id, $subscribed_to_push_topics)) {
                            $subscribed_to_push_topics[] = $notification_project_uk_id;
                        }

                        if (!empty($role_id)) {
                            $notification_project_uk_id_role_id = $notif . '_' . $owners . '_' . $project_uk . '_' .
                                $uk_project_id . '_' . $role . '_' . $role_id . $topic_test;

                            if (!in_array($notification_project_uk_id_role_id, $subscribed_to_push_topics)) {
                                $subscribed_to_push_topics[] = $notification_project_uk_id_role_id;
                            }
                        }

                        if ($building = $account->getBuildZid()) {
                            $building_id = $building->id;

                            if ($building_id) {
                                $news_project_uk_id_build_id = $news . '_' . $project_uk . '_' . $uk_project_id .
                                    '_' . $build . '_' . $building_id . $topic_test;

                                if (!in_array($news_project_uk_id_build_id, $subscribed_to_push_topics)) {
                                    $subscribed_to_push_topics[] = $news_project_uk_id_build_id;
                                }

                                $notification_project_uk_id_build_id = $notif . '_' . $owners . '_' . $project_uk
                                    . '_' . $uk_project_id . '_' . $build . '_' . $building_id . $topic_test;

                                if (!in_array($notification_project_uk_id_build_id, $subscribed_to_push_topics)) {
                                    $subscribed_to_push_topics[] = $notification_project_uk_id_build_id;
                                }

                                if (!empty($role_id)) {
                                    $notification_project_uk_id_build_id_role_id = $notif . '_' . $owners . '_' .
                                        $project_uk . '_' . $uk_project_id . '_' . $build . '_' . $building_id . '_' .
                                        $role . '_' . $role_id . $topic_test;

                                    if (!in_array($notification_project_uk_id_build_id_role_id, $subscribed_to_push_topics)) {
                                        $subscribed_to_push_topics[] = $notification_project_uk_id_build_id_role_id;
                                    }
                                }
                            }
                        }

                        $isCustomer = $user::whereIn(
                            'id',
                            Deal::select('user_id')
                                ->where('user_id', $user->id)
                                ->where('project_id', $uk_project_id)
                                ->whereIn('current_step', StepMapper::getActiveSteps())
                                ->get()
                        )
                            ->get()
                            ->all();

                        if ($isCustomer) {
                            $notification_customers_project_uk_id = $notif . '_' . $customers . '_' .
                                $uk_project_id . $topic_test;

                            if (!in_array($notification_customers_project_uk_id, $subscribed_to_push_topics)) {
                                $subscribed_to_push_topics[] = $notification_customers_project_uk_id;
                            }
                        }
                    } else {
                        $isCustomer = $user::whereIn(
                            'id',
                            Deal::select('user_id')
                                ->where('user_id', $user->id)
                                ->whereIn('current_step', StepMapper::getActiveSteps())
                                ->get()
                        )
                            ->get()
                            ->all();

                        if ($isCustomer) {
                            $notification_customers = $notif . '_' . $customers . $topic_test;

                            if (!in_array($notification_customers, $subscribed_to_push_topics)) {
                                $subscribed_to_push_topics[] = $notification_customers;
                            }
                        }
                    }
                }

                $notification_reality_type = $notif . '_' . $owners . '_' . $reality_types . $topic_test;

                if (!in_array($notification_reality_type, $subscribed_to_push_topics)) {
                    $subscribed_to_push_topics[] = $notification_reality_type;
                }

                if (!empty($role_id)) {
                    $notification_reality_type_role_id = $notif . '_' . $owners . '_' .
                        $reality_types . '_' . $role . '_' . $role_id . $topic_test;

                    if (!in_array($notification_reality_type_role_id, $subscribed_to_push_topics)) {
                        $subscribed_to_push_topics[] = $notification_reality_type_role_id;
                    }
                }

                if ($reality_type_id = $account->getRealtyType()) {
                    $notification_reality_type_id = $notif . '_' . $owners . '_' . $reality_types . '_' .
                        $reality_type_id . $topic_test;

                    if (!in_array($notification_reality_type_id, $subscribed_to_push_topics)) {
                        $subscribed_to_push_topics[] = $notification_reality_type_id;
                    }

                    if (!empty($role_id)) {
                        $notification_reality_type_id_role_id = $notif . '_' . $owners . '_' . $reality_types . '_' .
                            $reality_type_id . '_' . $role . '_' . $role_id . $topic_test;

                        if (!in_array($notification_reality_type_id_role_id, $subscribed_to_push_topics)) {
                            $subscribed_to_push_topics[] = $notification_reality_type_id_role_id;
                        }
                    }
                }
            }
        }

        $messaging = app('firebase.messaging');

        if ($user->push_token) {
            $response = $messaging->subscribeToTopics($subscribed_to_push_topics, [$user->push_token]);

            logger()?->debug('subscribeToTopics', $response);

            $user->update([
                'subscribed_to_push_topics' => $subscribed_to_push_topics,
            ]);
        }
    }

    /**
     * @param User      $user
     * @param array $accounts
     */
    public function subscribeToPushTopicsV2(User $user, array $accounts): void
    {
        // phpcs:disable
        $topic_test = app()->environment(['local', 'staging']) ? '_test' : '';

        $general = 'General';

        $news = 'News';
        $notif = 'Notification';

        $project_uk = 'ProjectUK';
        $owners = 'Owners';
        $customers = 'Customers';
        $reality_types = 'RealtyTypes';

        $build = 'Corp';
        $role = 'Role';

        $subscribed_to_push_topics = [];

        $general_topic = $general . $topic_test;

        if (!in_array($general_topic, $subscribed_to_push_topics)) {
            $subscribed_to_push_topics[] = $general_topic;
        }

        $news_general = $news . '_' . $general . $topic_test;

        if ($user->canReceiveObjectNewsPush() && !in_array($news_general, $subscribed_to_push_topics)) {
            $subscribed_to_push_topics[] = $news_general;
        }

        if ($accounts) {
            foreach ($accounts as $account) {
                $role_id = $account['role'] ?? "";

                $news_project_uk = $news . '_' . $project_uk . $topic_test;

                if (!in_array($news_project_uk, $subscribed_to_push_topics)) {
                    $subscribed_to_push_topics[] = $news_project_uk;
                }

                $notification_project_uk = $notif. '_' . $owners . '_' . $project_uk . $topic_test;

                if (!in_array($notification_project_uk, $subscribed_to_push_topics)) {
                    $subscribed_to_push_topics[] = $notification_project_uk;
                }

                if (!empty($role_id)) {
                    $notification_project_uk_role_id = $notif. '_' . $owners . '_' . $project_uk . '_' .
                        $role . '_' . $role_id . $topic_test;

                    if (!in_array($notification_project_uk_role_id, $subscribed_to_push_topics)) {
                        $subscribed_to_push_topics[] = $notification_project_uk_role_id;
                    }
                }

                if ($uk_project_id = $account['uk_project_id']) {
                    if ($uk_project_id) {
                        $news_project_uk_id = $news . '_' . $project_uk . '_' . $uk_project_id . $topic_test;

                        if (!in_array($news_project_uk_id, $subscribed_to_push_topics)) {
                            $subscribed_to_push_topics[] = $news_project_uk_id;
                        }

                        $notification_project_uk_id = $notif. '_' . $owners . '_' . $project_uk . '_' .
                            $uk_project_id . $topic_test;

                        if (!in_array($notification_project_uk_id, $subscribed_to_push_topics)) {
                            $subscribed_to_push_topics[] = $notification_project_uk_id;
                        }

                        if (!empty($role_id)) {
                            $notification_project_uk_id_role_id = $notif . '_' . $owners . '_' . $project_uk . '_' .
                                $uk_project_id . '_' . $role . '_' . $role_id . $topic_test;

                            if (!in_array($notification_project_uk_id_role_id, $subscribed_to_push_topics)) {
                                $subscribed_to_push_topics[] = $notification_project_uk_id_role_id;
                            }
                        }

                        if ($building = $account['build_zid'] ? Building::byBuildZid($account['build_zid'])->first() : null) {
                            $building_id = $building->id;

                            if ($building_id) {
                                $news_project_uk_id_build_id = $news . '_' . $project_uk . '_' . $uk_project_id .
                                    '_' . $build . '_' . $building_id . $topic_test;

                                if (!in_array($news_project_uk_id_build_id, $subscribed_to_push_topics)) {
                                    $subscribed_to_push_topics[] = $news_project_uk_id_build_id;
                                }

                                $notification_project_uk_id_build_id = $notif . '_' . $owners . '_' . $project_uk
                                    . '_' . $uk_project_id . '_' . $build . '_' . $building_id . $topic_test;

                                if (!in_array($notification_project_uk_id_build_id, $subscribed_to_push_topics)) {
                                    $subscribed_to_push_topics[] = $notification_project_uk_id_build_id;
                                }

                                if (!empty($role_id)) {
                                    $notification_project_uk_id_build_id_role_id = $notif . '_' . $owners . '_' .
                                        $project_uk . '_' . $uk_project_id . '_' . $build . '_' . $building_id . '_' .
                                        $role . '_' . $role_id . $topic_test;

                                    if (!in_array($notification_project_uk_id_build_id_role_id, $subscribed_to_push_topics)) {
                                        $subscribed_to_push_topics[] = $notification_project_uk_id_build_id_role_id;
                                    }
                                }
                            }
                        }

                        $isCustomer = $user::whereIn(
                            'id',
                            Deal::select('user_id')
                                ->where('user_id', $user->id)
                                ->where('project_id', $uk_project_id)
                                ->whereIn('current_step', StepMapper::getActiveSteps())
                                ->get()
                        )
                            ->get()
                            ->all();

                        if ($isCustomer) {
                            $notification_customers_project_uk_id = $notif . '_' . $customers . '_' .
                                $uk_project_id . $topic_test;

                            if (!in_array($notification_customers_project_uk_id, $subscribed_to_push_topics)) {
                                $subscribed_to_push_topics[] = $notification_customers_project_uk_id;
                            }
                        }
                    } else {
                        $isCustomer = $user::whereIn(
                            'id',
                            Deal::select('user_id')
                                ->where('user_id', $user->id)
                                ->whereIn('current_step', StepMapper::getActiveSteps())
                                ->get()
                        )
                            ->get()
                            ->all();

                        if ($isCustomer) {
                            $notification_customers = $notif . '_' . $customers . $topic_test;

                            if (!in_array($notification_customers, $subscribed_to_push_topics)) {
                                $subscribed_to_push_topics[] = $notification_customers;
                            }
                        }
                    }
                }

                $notification_reality_type = $notif . '_' . $owners . '_' . $reality_types . $topic_test;

                if (!in_array($notification_reality_type, $subscribed_to_push_topics)) {
                    $subscribed_to_push_topics[] = $notification_reality_type;
                }

                if (!empty($role_id)) {
                    $notification_reality_type_role_id = $notif . '_' . $owners . '_' .
                        $reality_types . '_' . $role . '_' . $role_id . $topic_test;

                    if (!in_array($notification_reality_type_role_id, $subscribed_to_push_topics)) {
                        $subscribed_to_push_topics[] = $notification_reality_type_role_id;
                    }
                }

                if ($reality_type_id = $account['realty_type']) {
                    $notification_reality_type_id = $notif . '_' . $owners . '_' . $reality_types . '_' .
                        $reality_type_id . $topic_test;

                    if (!in_array($notification_reality_type_id, $subscribed_to_push_topics)) {
                        $subscribed_to_push_topics[] = $notification_reality_type_id;
                    }

                    if (!empty($role_id)) {
                        $notification_reality_type_id_role_id = $notif . '_' . $owners . '_' . $reality_types . '_' .
                            $reality_type_id . '_' . $role . '_' . $role_id . $topic_test;

                        if (!in_array($notification_reality_type_id_role_id, $subscribed_to_push_topics)) {
                            $subscribed_to_push_topics[] = $notification_reality_type_id_role_id;
                        }
                    }
                }
            }
        }

        $messaging = app('firebase.messaging');

        if ($user->push_token) {
            $response = $messaging->subscribeToTopics($subscribed_to_push_topics, [$user->push_token]);

            logger()?->debug('subscribeToTopics', $response);

            $user->update([
                'subscribed_to_push_topics' => $subscribed_to_push_topics,
            ]);
        }
    }
}
