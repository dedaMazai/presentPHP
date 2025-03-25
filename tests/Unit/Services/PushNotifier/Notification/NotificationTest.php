<?php

namespace Tests\Unit\Services\PushNotifier\Notification;

use App\Services\PushNotifier\Notification\Message;
use App\Services\PushNotifier\Notification\Notification;
use App\Services\PushNotifier\Notification\Target\ChannelTarget;
use App\Services\PushNotifier\Notification\Target\DeviceTarget;
use Tests\TestCase;

class NotificationTest extends TestCase
{
    public function testCreate()
    {
        $message = Message::create(title: 'title', body: 'body', clickAction: 'action_name');
        $target = new DeviceTarget('token');

        $notification = Notification::create($message, $target);

        $this->assertEquals($message, $notification->message());
        $this->assertEquals($target, $notification->target());
        $this->assertFalse($notification->hasMeta());
        $this->assertTrue($notification->meta()->isEmpty());
        $this->assertFalse($notification->isMulticasting());
    }

    public function testCreateMulticast()
    {
        $message = Message::create(title: 'title', body: 'body');
        $target1 = new DeviceTarget('token1');
        $target2 = new DeviceTarget('token2');

        $notification = Notification::create($message, $target1, $target2);

        $this->assertEquals($message, $notification->message());
        $this->assertEquals([$target1, $target2], $notification->target());
        $this->assertFalse($notification->hasMeta());
        $this->assertTrue($notification->meta()->isEmpty());
        $this->assertTrue($notification->isMulticasting());
    }

    public function testWithMeta()
    {
        $message = Message::create(title: 'title', body: 'body');
        $target = new ChannelTarget('channel');

        $notification = Notification::create($message, $target);

        $meta = ['prop1' => 2, 'prop2' => 'value'];
        $notification->withMeta($meta);

        $this->assertTrue($notification->hasMeta());
        $this->assertTrue($notification->meta()->isFilled());
        $this->assertEquals($meta, $notification->meta()->toArray());
    }
}
