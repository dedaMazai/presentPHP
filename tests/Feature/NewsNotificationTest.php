<?php

namespace Tests\Feature;

use App\Models\Action;
use App\Models\News\News;
use App\Models\News\NewsType;
use App\Models\Notification\Notification;
use App\Models\Notification\NotificationDestinationType;
use App\Models\UkProject;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class NewsNotificationTest extends TestCase
{
//    /**
//     * @dataProvider commonNewsTypes
//     */
//    public function testCreateNotPublishedNews(NewsType $type)
//    {
//        Queue::fake();
//
//        News::factory()->create([
//            'type' => $type,
//            'is_published' => false,
//            'should_send_notification' => true,
//            'destination' => 'all_uk_users',
//        ]);
//        Queue::assertNothingPushed();
//        $this->assertNull(Notification::first());
//    }
//
//    /**
//     * @dataProvider commonNewsTypes
//     */
//    public function testCreateNewsWithoutSendPushNotification(NewsType $type)
//    {
//        Queue::fake();
//
//        News::factory()->create([
//            'type' => $type,
//            'should_send_notification' => false,
//            'destination' => 'all_uk_users',
//        ]);
//        Queue::assertNothingPushed();
//        $this->assertNull(Notification::first());
//    }

//    /**
//     * @dataProvider commonNewsTypes
//     */
//    public function testCreatedCommonNewsWithSendPushNotification(NewsType $type)
//    {
//        Queue::fake();
//
//        /** @var News $news */
//        $news = News::factory()->create([
//            'title' => 'Title',
//            'description' => 'Text',
//            'type' => $type,
//            'should_send_notification' => true,
//            'destination' => NotificationDestinationType::companyNewsSubscribers(),
//        ]);
//
//        // check that notification is created
//        $this->assertDatabaseHas('notifications', [
//            'title' => 'Title',
//            'text' => 'Text',
//            'type' => 'news',
//            'destination_type' => NotificationDestinationType::companyNewsSubscribers()->value,
//        ]);
//
//        // check that action is recorded right
//        /** @var Action $action */
//        $action = Notification::first()->action;
//        $this->assertEquals('news', $action->type);
//        $this->assertEquals(['news_id' => $news->id], $action->payload);
//    }
//
//    public function commonNewsTypes(): array
//    {
//        return [
//            [NewsType::general()],
//            [NewsType::pioneer()],
//            [NewsType::projects()],
//        ];
//    }
//
//    public function testCreatedPersonalNewsWithSendPushNotification()
//    {
//        /** @var UkProject $project1 */
//        $project1 = UkProject::factory()->create();
//
//        Queue::fake();
//
//        /** @var News $news */
//        $news = News::factory()->create([
//            'title' => 'Title',
//            'description' => 'Text',
//            'type' => NewsType::uk(),
//            'uk_project_id' => $project1->id,
//            'should_send_notification' => true,
//            'destination' => NotificationDestinationType::ownersByUkProjects(),
//        ]);
//
//        // check that notification is created
//        $this->assertDatabaseHas('notifications', [
//            'title' => 'Title',
//            'text' => 'Text',
//            'type' => 'news',
//            'destination_type' => NotificationDestinationType::ownersByUkProjects()->value,
//        ]);
//
//        // check thar payload is recorded right
//        /** @var Notification $notification */
//        $notification = Notification::first();
//        $this->assertEquals(
//            ['uk_project_ids' => [$project1->id], 'buildings_id' => null],
//            $notification->destination_type_payload
//        );
//
//        // check that action is recorded right
//        /** @var Action $action */
//        $action = $notification->action;
//        $this->assertEquals('news', $action->type);
//        $this->assertEquals(['news_id' => $news->id], $action->payload);
//    }
}



