<?php

namespace Database\Factories\TransactionLog;

use App\Models\PaymentMethodType;
use App\Models\TransactionLog\TransactionLog;
use App\Models\TransactionLog\TransactionLogStatus;
use App\Models\User\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TransactionLogFactory extends Factory
{
    protected $model = TransactionLog::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'account_number' => $this->faker->word(),
            'payment_method_type' => $this->faker->randomElement(PaymentMethodType::cases()),
            'title' => $this->faker->title,
            'subtitle' => $this->faker->title,
            'amount' => mt_rand(100, 100000),
            'remote_order_id' => $this->faker->word(),
            'status' => $this->faker->randomElement(TransactionLogStatus::cases()),
            'claim_id' => $this->faker->word(),
            'claim_number' => $this->faker->word(),
            'claim_category_name' => $this->faker->word(),
            'account_service_seller_id' => $this->faker->word(),
        ];
    }
}
