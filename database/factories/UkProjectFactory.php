<?php

namespace Database\Factories;

use App\Models\Image;
use App\Models\UkProject;
use Illuminate\Database\Eloquent\Factories\Factory;

class UkProjectFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = UkProject::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'is_published' => true,
            'name' => $this->faker->word(),
            'crm_1c_id' => $this->faker->uuid(),
            'description' => $this->faker->text(),
            'postcode' => '1234',
            'image_id' => Image::factory(),
        ];
    }
}
