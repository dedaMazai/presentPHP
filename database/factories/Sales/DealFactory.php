<?php

namespace Database\Factories\Sales;

use App\Models\Project\Project;
use App\Models\Sales\Deal;
use App\Models\Sales\StepMapper;
use App\Models\User\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class DealFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Deal::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'demand_id' => $this->faker->uuid(),
            'property_id' => $this->faker->uuid(),
            'is_escrow' => true,
            'is_escrow_bank_client' => true,
            'current_step' => StepMapper::STEP_TERMS,
            'project_id' => Project::factory(),
            'is_booking_paid' => true,
            'mortgage_demand_id' => null,
            'contract_read_at' => null,
        ];
    }
}
