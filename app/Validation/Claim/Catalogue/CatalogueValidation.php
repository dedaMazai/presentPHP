<?php

namespace App\Validation\Claim\Catalogue;

use App\Models\Claim\ClaimFilter\ClaimFilterStatus;
use App\Models\Claim\ClaimTheme;
use App\Validation\BaseValidation;
use App\Validation\ValidationInterface;
use Illuminate\Validation\Rule;

class CatalogueValidation extends BaseValidation
{
    public function validateThemeId(bool $required = true): ValidationInterface
    {
        $key = 'theme_id';

        if (!$required && !isset($this->inputs[$key])) {
            return $this;
        }

        $this->validation_rules["$key"] = ['required', Rule::in(ClaimTheme::toValues())];

        return $this;
    }

    public function validateQuery(bool $required = true): ValidationInterface
    {
        $key = 'query';

        if (!$required and !isset($this->inputs[$key])) {
            return $this;
        }

        $this->validation_rules["$key"] = ['required', 'string'];

        return $this;
    }

    public function validateDateFrom(bool $required = true): ValidationInterface
    {
        $key = 'date_from';

        if (!$required and !isset($this->inputs[$key])) {
            return $this;
        }

        $this->validation_rules["$key"] = ['date'];

        return $this;
    }

    public function validateDateTo(bool $required = true): ValidationInterface
    {
        $key = 'date_to';

        if (!$required and !isset($this->inputs[$key])) {
            return $this;
        }

        $this->validation_rules["$key"] = ['required', 'string'];

        return $this;
    }

    public function validateStatuses(bool $required = true): ValidationInterface
    {
        $key = 'statuses';

        if (!$required and !isset($this->inputs[$key])) {
            return $this;
        }

        $this->validation_rules["$key"] = ['array'];

        return $this;
    }

    public function validateStatusesAll(bool $required = true): ValidationInterface
    {
        $key = 'statuses.*';

        if (!$required and !isset($this->inputs[$key])) {
            return $this;
        }

        $this->validation_rules["$key"] = [Rule::in(ClaimFilterStatus::toValues())];

        return $this;
    }

    public function validateUri(bool $required = true): ValidationInterface
    {
        $key = 'uri';

        if (!$required and !isset($this->inputs[$key])) {
            return $this;
        }

        $this->validation_rules["$key"] = ['required','string'];

        return $this;
    }

    public function validateImages(bool $required = true): ValidationInterface
    {
        $key = 'images';

        if (!$required and !isset($this->inputs[$key])) {
            return $this;
        }

        $this->validation_rules["$key"] = ['array'];

        return $this;
    }

    public function validateImagesAll(bool $required = true): ValidationInterface
    {
        $key = 'images.*';

        if (!$required and !isset($this->inputs[$key])) {
            return $this;
        }

        $this->validation_rules["$key"] = ['file'];

        return $this;
    }

    public function validateRating(bool $required = true): ValidationInterface
    {
        $key = 'rating';

        if (!$required and !isset($this->inputs[$key])) {
            return $this;
        }

        $this->validation_rules["$key"] = ['required','integer','min:1','max:5'];

        return $this;
    }

    public function validateCommentQuality(bool $required = true): ValidationInterface
    {
        $key = 'comment_quality';

        if (!$required and !isset($this->inputs[$key])) {
            return $this;
        }

        $this->validation_rules["$key"] = ['required','string'];

        return $this;
    }
}
