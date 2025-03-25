<?php

namespace Database\Factories\Account;

use App\Models\Account\AccountInfo;
use App\Models\Account\AccountRealtyType;
use App\Models\UkProject;
use Illuminate\Database\Eloquent\Factories\Factory;

class AccountInfoFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = AccountInfo::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'account_number' => $this->faker->numerify('#############'),
            'uk_project_id' => UkProject::factory(),
            'realty_type' => AccountRealtyType::flat(),
        ];
    }
}
