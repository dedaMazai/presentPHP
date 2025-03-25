<?php

namespace Tests\Feature\Api\Internal;

use App\Jobs\SendPush;
use App\Models\Action;
use App\Models\Notification\Notification;
use App\Models\User\User;
use App\Services\DynamicsCrm\DynamicsCrmClient;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class ClaimMessageControllerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->withHeader('X-Access-Key', 'test');
    }

    public function testPlainNotification()
    {
        $mock = $this->mock(DynamicsCrmClient::class);
        $mock->allows(['getClaimMessagesByClaimId' => []]);
        app()->bind(DynamicsCrmClient::class, fn() => $mock);

        Queue::fake();

        /** @var User $user */
        User::factory()->create([
            'crm_id' => '1234',
        ]);

        $response = $this->post(
            '/api/internal/users/1234/claim/a9e5b66b-be84-ec11-bba9-005056bf672e/manager-messages',
            [
                'title' => 'Title',
                'text' => 'Text',
                'account_number' => '1234'
            ]
        );

        $response->assertStatus(204);

        Queue::assertPushed(SendPush::class);

        // check that notification is created
        $this->assertDatabaseHas('notifications', [
            'title' => 'Title',
            'text' => 'Text',
            'type' => 'claim',
            'destination_type' => 'single_crm_user',
        ]);

        // check that action is recorded right
        /** @var Action $action */
        $action = Notification::first()->action;;
        $this->assertEquals('new_claim_message', $action->type);
        $this->assertEquals(
            ['claim_id' => 'a9e5b66b-be84-ec11-bba9-005056bf672e', 'account_number' => '1234'],
            $action->payload
        );
    }
}
