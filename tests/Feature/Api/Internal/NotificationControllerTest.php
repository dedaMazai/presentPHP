<?php

namespace Tests\Feature\Api\Internal;

use App\Jobs\SendPush;
use App\Models\Action;
use App\Models\Notification\Notification;
use App\Models\User\User;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class NotificationControllerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->withHeader('X-Access-Key', config('internal_api.access_key'));
    }

    /**
     * @dataProvider notificationTypes
     */
    public function testPlainNotification(string $notificationType)
    {
        Queue::fake();

        /** @var User $user */
        User::factory()->create([
            'crm_id' => '1234',
        ]);

        $response = $this->post('/api/internal/users/1234/notifications', [
            'title' => 'Title',
            'text' => 'Text',
            'type' => $notificationType,
        ]);

        $response->assertStatus(204);

        Queue::assertPushed(SendPush::class);

        // check that notification is created
        $this->assertDatabaseHas('notifications', [
            'title' => 'Title',
            'text' => 'Text',
            'type' => $notificationType,
            'destination_type' => 'single_crm_user',
        ]);

        // check that payload is recorded right
        /** @var Notification $notification */
        $notification = Notification::first();
        $this->assertEquals(
            ['crm_id' => '1234'],
            $notification->destination_type_payload
        );
    }

    public function notificationTypes(): array
    {
        return [
            ['marketing_activity'],
            ['purchase_process'],
            ['uk'],
            ['news'],
            ['claim']
        ];
    }

    public function testNotificationWithExternalLinkAction()
    {
        User::factory()->create([
            'crm_id' => '1234',
        ]);

        $response = $this->post('/api/internal/users/1234/notifications', [
            'title' => 'Title',
            'text' => 'Text',
            'type' => 'marketing_activity',
            'action' => [
                'type' => 'external_link',
                'payload' => ['url' => 'https://site.com'],
            ],
        ]);
        $response->assertStatus(204);

        // check that notification is created
        $this->assertDatabaseHas('notifications', [
            'title' => 'Title',
            'text' => 'Text',
            'type' => 'marketing_activity',
            'destination_type' => 'single_crm_user',
        ]);

        // check that action is recorded right
        /** @var Action $action */
        $action = Notification::first()->action;;
        $this->assertEquals('external_link', $action->type);
        $this->assertEquals(['url' => 'https://site.com'], $action->payload);
    }

    public function testNotificationWithClaimAction()
    {
        User::factory()->create([
            'crm_id' => '1234',
        ]);

        $response = $this->post('/api/internal/users/1234/notifications', [
            'title' => 'Title',
            'text' => 'Text',
            'type' => 'marketing_activity',
            'action' => [
                'type' => 'claim_status_changed',
                'payload' => ['claim_id' => 'a9e5b66b-be84-ec11-bba9-005056bf672e'],
            ],
        ]);
        $response->assertStatus(204);

        // check that notification is created
        $this->assertDatabaseHas('notifications', [
            'title' => 'Title',
            'text' => 'Text',
            'type' => 'marketing_activity',
            'destination_type' => 'single_crm_user',
        ]);

        // check that action is recorded right
        /** @var Action $action */
        $action = Notification::first()->action;
        $this->assertEquals('claim_status_changed', $action->type);
        $this->assertEquals(['claim_id' => 'a9e5b66b-be84-ec11-bba9-005056bf672e'], $action->payload);
    }

    public function testNotificationWithDebtAction()
    {
        User::factory()->create([
            'crm_id' => '1234',
        ]);

        $response = $this->post('/api/internal/users/1234/notifications', [
            'title' => 'Title',
            'text' => 'Text',
            'type' => 'uk',
            'action' => [
                'type' => 'debt',
                'payload' => ['account_number' => '7783722675618'],
            ],
        ]);
        $response->assertStatus(204);

        // check that notification is created
        $this->assertDatabaseHas('notifications', [
            'title' => 'Title',
            'text' => 'Text',
            'type' => 'uk',
            'destination_type' => 'single_crm_user',
        ]);

        // check that action is recorded right
        /** @var Action $action */
        $action = Notification::first()->action;
        $this->assertEquals('debt', $action->type);
        $this->assertEquals(['account_number' => '7783722675618'], $action->payload);
    }

    public function testNotificationWithBirthdayLinkAction()
    {
        User::factory()->create([
            'crm_id' => '1234',
        ]);

        $response = $this->post('/api/internal/users/1234/notifications', [
            'title' => 'Title',
            'text' => 'Text',
            'type' => 'uk',
            'action' => [
                'type' => 'birthday_link',
                'payload' => ['url' => 'https://site.com'],
            ],
        ]);
        $response->assertStatus(204);

        // check that notification is created
        $this->assertDatabaseHas('notifications', [
            'title' => 'Title',
            'text' => 'Text',
            'type' => 'uk',
            'destination_type' => 'single_crm_user',
        ]);

        // check that action is recorded right
        /** @var Action $action */
        $action = Notification::first()->action;;
        $this->assertEquals('birthday_link', $action->type);
        $this->assertEquals(['url' => 'https://site.com'], $action->payload);
    }

    public function testNotificationWithInsuranceExpireLinkAction()
    {
        User::factory()->create([
            'crm_id' => '1234',
        ]);

        $response = $this->post('/api/internal/users/1234/notifications', [
            'title' => 'Title',
            'text' => 'Text',
            'type' => 'uk',
            'action' => [
                'type' => 'insurance_expire_link',
                'payload' => ['url' => 'https://site.com'],
            ],
        ]);
        $response->assertStatus(204);

        // check that notification is created
        $this->assertDatabaseHas('notifications', [
            'title' => 'Title',
            'text' => 'Text',
            'type' => 'uk',
            'destination_type' => 'single_crm_user',
        ]);

        // check that action is recorded right
        /** @var Action $action */
        $action = Notification::first()->action;;
        $this->assertEquals('insurance_expire_link', $action->type);
        $this->assertEquals(['url' => 'https://site.com'], $action->payload);
    }
}
