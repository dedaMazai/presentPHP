<?php

namespace Tests\Unit\Services\Notification;

use App\Models\Notification\Notification;
use App\Models\Notification\NotificationDestinationType;
use App\Models\Notification\NotificationType;
use App\Models\User\NotificationChannel;
use App\Models\User\User;
use App\Services\Notification\TargetResolver;
use App\Services\PushNotifier\Notification\Target\ChannelTarget;
use App\Services\PushNotifier\Notification\Target\DeviceTarget;
use Tests\TestCase;

class TargetResolverTest extends TestCase
{
    private TargetResolver $resolver;

    protected function setUp(): void
    {
        parent::setUp();

        $this->resolver = new TargetResolver();
    }

    /**
     * @dataProvider deviceDestinationTypes
     */
    public function testResolveByNotification(NotificationDestinationType $destinationType)
    {
        /** @var Notification $notification */
        $notification = Notification::factory()->create([
            'destination_type' => $destinationType,
        ]);
        $user = User::factory()->create([
            'push_token' => 'token',
        ]);
        $notification->recipients()->save($user);

        $targets = $this->resolver->resolveByNotification($notification);
        $this->assertCount(1, $targets);

        $target = $targets[0];
        $this->assertInstanceOf(DeviceTarget::class, $target);
        $this->assertEquals('token', $target->getValue());
    }

    public function deviceDestinationTypes()
    {
        return [
            [NotificationDestinationType::singleCrmUser()],
        ];
    }

    public function testResolveByNotificationForAllUsers()
    {
        /** @var Notification $notification */
        $notification = Notification::factory()->create([
            'destination_type' => NotificationDestinationType::allUsers(),
        ]);

        $targets = $this->resolver->resolveByNotification($notification);
        $this->assertCount(1, $targets);

        $target = $targets[0];
        $topic_general = app()->environment(['local', 'staging']) ? 'General_test' : 'General';

        $this->assertInstanceOf(ChannelTarget::class, $target);
        $this->assertEquals($topic_general, $target->getValue());
    }

    public function testResolveByNotificationForCompanyNewsSubscribers()
    {
        /** @var Notification $notification */
        $notification = Notification::factory()->create([
            'destination_type' => NotificationDestinationType::companyNewsSubscribers(),
        ]);

        $targets = $this->resolver->resolveByNotification($notification);
        $this->assertCount(1, $targets);

        $target = $targets[0];
        $topic_company_news = app()->environment(['local', 'staging']) ? 'News_company_news_test' : 'News_company_news';

        $this->assertInstanceOf(ChannelTarget::class, $target);
        $this->assertEquals($topic_company_news, $target->getValue());
    }

    public function testResolveByNotificationForObjectNewsSubscribers()
    {
        /** @var Notification $notification */
        $notification = Notification::factory()->create([
            'type' => NotificationType::news(),
            'destination_type' => NotificationDestinationType::singleCrmUser(),
        ]);
        /** @var User $user1 */
        $user1 = User::factory()->create([
            'push_token' => 'token1',
        ]);
        // allow user1 to receive object news notifications
        $user1->enabled_notifications = [NotificationChannel::pushNewsObject()];
        $user1->save();
        // assign notification to user1
        $notification->recipients()->save($user1);

        /** @var User $user2 */
        $user2 = User::factory()->create([
            'push_token' => 'token2',
        ]);
        // deny user2 to receive object news notifications
        $user2->enabled_notifications = [];
        $user2->save();
        // assign notification to user2
        $notification->recipients()->save($user2);

        $targets = $this->resolver->resolveByNotification($notification);
        $this->assertCount(1, $targets);

        $target = $targets[0];
        $this->assertInstanceOf(DeviceTarget::class, $target);
        $this->assertEquals('token1', $target->getValue());
    }
}
