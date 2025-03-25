<?php

namespace Database\Factories\Article;

use App\Models\Article\Article;
use App\Models\News\News;
use App\Models\News\NewsCategory;
use App\Models\News\NewsType;
use Illuminate\Database\Eloquent\Factories\Factory;

class ArticleFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Article::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'articlable_id' => 1,
            'is_published' => true,
            'title' => $this->faker->title(),
            'order' => 1,
            'articlable_type' => 1,
            'icon_image_id' => null,
        ];
    }
}
