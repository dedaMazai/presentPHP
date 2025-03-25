<?php

namespace Database\Factories\News;

use App\Models\News\News;
use App\Models\News\NewsCategory;
use App\Models\News\NewsType;
use Illuminate\Database\Eloquent\Factories\Factory;

class NewsFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = News::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'is_published' => true,
            'type' => NewsType::general(),
            'title' => $this->faker->title(),
            'description' => $this->faker->text(),
            'category' => NewsCategory::news(),
            'uk_project_id' => null,
            'should_send_notification' => false,
        ];
    }
}
