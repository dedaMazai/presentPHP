<?php

namespace Database\Factories\Project;

use App\Models\City;
use App\Models\Image;
use App\Models\Mortgage\MortgageType;
use App\Models\Project\Project;
use App\Models\Project\ProjectType;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProjectFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Project::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'type_id' => ProjectType::factory(),
            'is_published' => true,
            'name' => $this->faker->word(),
            'showcase_image_id' => Image::factory(),
            'main_image_id' => Image::factory(),
            'map_image_id' => Image::factory(),
            'metro' => $this->faker->word(),
            'metro_color' => $this->faker->hexColor(),
            'crm_ids' => $this->faker->uuid(),
            'mortgage_calculator_id' => $this->faker->numberBetween(1),
            'lat' => $this->faker->latitude(),
            'long' => $this->faker->longitude(),
            'office_phone' => $this->faker->phoneNumber(),
            'office_address' => $this->faker->address(),
            'office_lat' => $this->faker->latitude(),
            'office_long' => $this->faker->longitude(),
            'office_work_hours' => $this->faker->sentence(),
            'property_type_params' => [],
            'color' => $this->faker->hexColor(),
            'description' => $this->faker->text(),
            'city_id' => City::factory(),
            'mortgage_types' => [MortgageType::standard()],
            'payroll_bank_programs' => [],
            'mortgage_min_property_price' => 9,
            'mortgage_max_property_price' => 65,
            'min_property_price' => 34463532,
        ];
    }
}
