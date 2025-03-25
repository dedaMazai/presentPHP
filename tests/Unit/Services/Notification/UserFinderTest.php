<?php

namespace Tests\Unit\Services\Notification;

use App\Models\Account\AccountInfo;
use App\Models\Account\AccountRealtyType;
use App\Models\Notification\Notification;
use App\Models\Notification\NotificationDestinationType;
use App\Models\Project\Project;
use App\Models\Role;
use App\Models\Sales\Deal;
use App\Models\Sales\Demand\DemandBookingType;
use App\Models\Sales\Demand\DemandStatus;
use App\Models\UkProject;
use App\Models\User\User;
use App\Services\Notification\UserFinder;
use Tests\TestCase;

class UserFinderTest extends TestCase
{
    private UserFinder $finder;

    protected function setUp(): void
    {
        parent::setUp();

        $this->finder = new UserFinder();
    }

    public function testFindAllByNotificationForSingleSrmUser()
    {
        /** @var User $user */
        $user = User::factory()->create([
            'crm_id' => '1234'
        ]);
        /** @var Notification $notification */
        $notification = Notification::factory()->create([
            'destination_type' => NotificationDestinationType::singleCrmUser(),
            'destination_type_payload' => ['crm_id' => '1234'],
        ]);

        $users = $this->finder->findAllByNotification($notification);

        $this->assertCount(1, $users);
        $this->assertEquals($user->id, $users[0]->id);
    }

    public function testFindAllByNotificationForOwnersByAccountRealtyTypes()
    {
        $user1 = $this->createOwnerOfPropertyOfType(AccountRealtyType::flat());
        $user2 = $this->createOwnerOfPropertyOfType(AccountRealtyType::parking());
        // create user without flat or parking
        $this->createOwnerOfPropertyOfType(AccountRealtyType::storeroom());
        // create user without properties
        User::factory()->create();

        /** @var Notification $notification */
        $notification = Notification::factory()->create([
            'destination_type' => NotificationDestinationType::ownersByAccountRealtyTypes(),
            'destination_type_payload' => [
                'account_realty_types' => [
                    AccountRealtyType::flat()->value,
                    AccountRealtyType::parking()->value,
                ],
            ],
        ]);

        $users = $this->finder->findAllByNotification($notification);

        $this->assertCount(0, $users);
    }

    public function testFindAllByNotificationForOwnersByUkProjects()
    {
        [$user1, $ukProject1] = $this->createOwnerOfPropertyInUkProject();
        [$user2, $ukProject2] = $this->createOwnerOfPropertyInUkProject();
        // create user with property in some another project
        $this->createOwnerOfPropertyInUkProject();
        // create user without properties
        User::factory()->create();

        /** @var Notification $notification */
        $notification = Notification::factory()->create([
            'destination_type' => NotificationDestinationType::ownersByUkProjects(),
            'destination_type_payload' => [
                'uk_project_ids' => [$ukProject1->id, $ukProject2->id],
            ],
        ]);

        $users = $this->finder->findAllByNotification($notification);

        $this->assertCount(0, $users);
    }

    public function testFindAllByNotificationForCustomersByProjects()
    {
        [$user1, $project1] = $this->createCustomerOfPropertyInProject();
        [$user2, $project2] = $this->createCustomerOfPropertyInProject();
        // create customer of property in some another project
        $this->createCustomerOfPropertyInProject();
        // create non-customer user
        User::factory()->create();

        /** @var Notification $notification */
        $notification = Notification::factory()->create([
            'destination_type' => NotificationDestinationType::customersByProjects(),
            'destination_type_payload' => [
                'project_ids' => [$project1->id, $project2->id],
            ],
        ]);

        $users = $this->finder->findAllByNotification($notification);

        $this->assertCount(0, $users);
    }

    private function createOwnerOfPropertyOfType(AccountRealtyType $type): User
    {
        /** @var User $user */
        $user = User::factory()->create();
        /** @var AccountInfo $accountInfo */
        $accountInfo = AccountInfo::factory()->create([
            'realty_type' => $type,
        ]);
        $user->relationships()->create([
            'account_number' => $accountInfo->account_number,
            'role' => Role::owner(),
        ]);

        return $user;
    }

    private function createCustomerOfPropertyInProject(): array
    {
        /** @var User $user */
        $user = User::factory()->create();
        /** @var Project $project */
        $project = Project::factory()->create();

        Deal::factory()->create([
            'user_id' => $user->id,
            'project_id' => $project->id,
            'demand_status' => DemandStatus::confirmation(),
            'demand_booking_type' => DemandBookingType::paid(),
            'initial_begin_date' => now(),
            'initial_end_date' => now(),
        ]);

        return [$user, $project];
    }

    private function createOwnerOfPropertyInUkProject(): array
    {
        /** @var User $user */
        $user = User::factory()->create();
        /** @var UkProject $project */
        $project = UkProject::factory()->create();

        /** @var AccountInfo $accountInfo */
        $accountInfo = AccountInfo::factory()->create([
            'uk_project_id' => $project->id,
        ]);
        $user->relationships()->create([
            'account_number' => $accountInfo->account_number,
            'role' => Role::owner(),
        ]);

        return [$user, $project];
    }
}
