<?php

namespace App\Validation\News;

use App\Models\News\NewsCategory;
use App\Models\News\NewsType;
use App\Validation\BaseValidation;
use App\Validation\ValidationInterface;
use Illuminate\Validation\Rule;

class NewsValidation extends BaseValidation
{
    public function validateType(bool $required = true): ValidationInterface
    {
        $key = 'type';

        if (!$required and !isset($this->inputs[$key])) {
            return $this;
        }

        $this->validation_rules["$key"] = [Rule::in(NewsType::toValues())];

        return $this;
    }

    public function validateCategory(bool $required = true): ValidationInterface
    {
        $key = 'category';

        if (!$required and !isset($this->inputs[$key])) {
            return $this;
        }

        $this->validation_rules["$key"] = [Rule::in(NewsCategory::toValues())];

        return $this;
    }

    public function validateUkProjectId(bool $required = true): ValidationInterface
    {
        $key = 'uk_project_id';

        if (!$required and !isset($this->inputs[$key])) {
            return $this;
        }

        $this->validation_rules["$key"] = ['integer', 'exists:uk_projects,id'];

        return $this;
    }
}
