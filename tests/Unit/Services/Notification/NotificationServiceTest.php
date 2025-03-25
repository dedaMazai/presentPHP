<?php

namespace Tests\Unit\Services\Notification;

use App\Models\Action;
use App\Models\News\News;
use App\Models\News\NewsType;
use App\Models\Notification\NotificationDestinationType;
use App\Models\Notification\NotificationType;
use App\Models\UkProject;
use App\Models\User\User;
use App\Services\Notification\NotificationService;
use Tests\TestCase;

class NotificationServiceTest extends TestCase
{
    private NotificationService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = app(NotificationService::class);
    }

    public function testCreateNewsNotificationForPersonalNews()
    {
        /** @var UkProject $project */
        $project = UkProject::factory()->create();
        /** @var News $news */
        $news = News::factory()->create([
            'title' => 'Some Title',
            'description' => 'Some text',
            'type' => NewsType::uk(),
            'uk_project_id' => $project->id,
            'destination' => NotificationDestinationType::allUkUsers(),
        ]);

        $notification = $this->service->createNewsNotification($news);

        $this->assertDatabaseHas('notifications', [
            'title' => 'Some Title',
            'text' => 'Some text',
            'type' => NotificationType::news(),
            'destination_type' => NotificationDestinationType::allUkUsers()->value,
        ]);

        // check that payload is recorded right
        $this->assertEquals(
            ['uk_project_ids' => [$project->id], 'buildings_id' => null],
            $notification->destination_type_payload
        );

        // check that action is recorded right
        /** @var Action $action */
        $action = $notification->action;
        $this->assertEquals('news', $action->type);
        $this->assertEquals(['news_id' => $news->id], $action->payload);
    }

    /**
     * @dataProvider newsTypes
     */
    public function testCreateNewsNotificationForCommonNews(NewsType $newsType)
    {
        /** @var News $news */
        $news = News::factory()->create([
            'title' => 'Some Title',
            'description' => 'Some text',
            'type' => $newsType,
            'destination' => NotificationDestinationType::companyNewsSubscribers(),
        ]);

        $notification = $this->service->createNewsNotification($news);

        $this->assertDatabaseHas('notifications', [
            'title' => 'Some Title',
            'text' => 'Some text',
            'type' => NotificationType::news(),
            'destination_type' => NotificationDestinationType::companyNewsSubscribers(),
        ]);

        // check that action is recorded right
        /** @var Action $action */
        $action = $notification->action;
        $this->assertEquals('news', $action->type);
        $this->assertEquals(['news_id' => $news->id], $action->payload);
    }

    public function newsTypes(): array
    {
        return [
            [NewsType::general()],
            [NewsType::pioneer()],
            [NewsType::projects()],
        ];
    }

    public function testCreateSingleCrmUserNotification()
    {
        $notification = $this->service->createSingleCrmUserNotification(
            title: 'Some Title',
            text: 'Some text',
            type: NotificationType::marketingActivity(),
            crmUserId: '1234',
            actionType: 'external_link',
            actionPayload: ['url' => 'https://some-site.com'],
        );

        $this->assertDatabaseHas('notifications', [
            'title' => 'Some Title',
            'text' => 'Some text',
            'type' => NotificationType::marketingActivity(),
            'destination_type' => NotificationDestinationType::singleCrmUser(),
        ]);

        // check that payload is recorded right
        $this->assertEquals(
            ['crm_id' => '1234'],
            $notification->destination_type_payload
        );

        // check that action is recorded right
        /** @var Action $action */
        $action = $notification->action;
        $this->assertEquals('external_link', $action->type);
        $this->assertEquals(['url' => 'https://some-site.com'], $action->payload);
    }

    public function testCreateNewClaimMessageNotification()
    {
        $notification = $this->service->createNewClaimMessageNotification(
            title: 'Some Title',
            text: 'Some text',
            claimId: 'b81a2e76-e131-4608-8d14-0909a790d3e7',
            crmUserId: '1234',
            accountNumber: '1234'
        );

        $this->assertDatabaseHas('notifications', [
            'title' => 'Some Title',
            'text' => 'Some text',
            'type' => NotificationType::claim(),
            'destination_type' => NotificationDestinationType::singleCrmUser(),
        ]);

        // check that payload is recorded right
        $this->assertEquals(
            ['crm_id' => '1234'],
            $notification->destination_type_payload
        );

        // check that action is recorded right
        /** @var Action $action */
        $action = $notification->action;
        $this->assertEquals('new_claim_message', $action->type);
        $this->assertEquals(
            ['claim_id' => 'b81a2e76-e131-4608-8d14-0909a790d3e7', 'account_number' => '1234'],
            $action->payload
        );
    }

    public function testSetRecipients()
    {
        /** @var User $user */
        $user = User::factory()->create([
            'crm_id' => '1234',
        ]);

        // create notification
        $notification = $this->service->createSingleCrmUserNotification(
            title: 'Some Title',
            text: 'Some text',
            type: NotificationType::marketingActivity(),
            crmUserId: '1234',
        );

        $this->service->setRecipients($notification);

        $notification->fresh();
        $recipients = $notification->recipients()->get()->all();

        $this->assertCount(1, $recipients);
        $this->assertTrue($recipients[0]->is($user));
    }
}
