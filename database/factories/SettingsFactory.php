<?php

namespace Database\Factories;

use App\Models\Settings;
use Illuminate\Database\Eloquent\Factories\Factory;

class SettingsFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Settings::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'main_office_address' => $this->faker->word(),
            'main_office_phone' => $this->faker->word(),
            'main_office_email' => $this->faker->word(),
            'main_office_lat' => $this->faker->randomFloat(),
            'main_office_long' => $this->faker->randomFloat(),
            'phone' => $this->faker->word(),
            'claim_pass_car_crm_service_id' => $this->faker->word(),
            'offer_url' => $this->faker->word(),
            'consent_url' => $this->faker->word(),
            'claim_pass_human_crm_service_id' => $this->faker->word(),
            'main_office_title' => $this->faker->word(),
            'build_android_url' => $this->faker->word(),
            'build_ios_url' => $this->faker->word(),
            'refill_account_acquiring' => $this->faker->word(),
            'claim_payment_acquiring' => $this->faker->word(),
            'claim_root_category_crm_id' => $this->faker->word(),
        ];
    }
}
