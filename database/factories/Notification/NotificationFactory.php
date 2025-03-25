<?php

namespace Database\Factories\Notification;

use App\Models\Notification\Notification;
use App\Models\Notification\NotificationDestinationType;
use App\Models\Notification\NotificationType;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;

class NotificationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Notification::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $type = NotificationType::from(Arr::random(NotificationType::toValues()));

        return [
            'title' => $this->faker->title(),
            'text' => $this->faker->text(),
            'type' => $type,
            'destination_type' => NotificationDestinationType::allUsers(),
            'destination_type_payload' => null,
            'action_id' => null,
        ];
    }
}
