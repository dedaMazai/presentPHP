<?php

namespace App\Http\Admin\Requests\Article;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class ArticleManipulationRequest
 *
 * @package App\Http\Admin\Requests\Article
 */
abstract class ArticleManipulationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'is_published' => 'required|boolean',
            'title' => 'required|string|max:255',
        ];
    }
}
