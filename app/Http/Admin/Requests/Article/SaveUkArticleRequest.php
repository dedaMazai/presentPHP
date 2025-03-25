<?php

namespace App\Http\Admin\Requests\Article;

/**
 * Class SaveUkArticleRequest
 *
 * @package App\Http\Admin\Requests\Article
 */
class SaveUkArticleRequest extends ArticleManipulationRequest
{
    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'icon_image_id' => 'required|integer|exists:images,id',
        ]);
    }
}
