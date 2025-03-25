<?php

namespace App\Http\Admin\Requests;

use App\Models\Contact\ContactType;
use Illuminate\Validation\Rule;

/**
 * Class SaveContactRequest
 *
 * @package App\Http\Admin\Requests
 */
abstract class SaveContactRequest extends Request
{
    public function rules(): array
    {
        $defaultRules = [
            'title' => 'required|string',
            'type' => [
                'required',
                Rule::in(ContactType::toValues()),
            ],
            'icon_image_id' => 'required|integer|exists:images,id',
        ];
        switch ($this->type) {
            case 'map':
                return array_merge($defaultRules, [
                    'lat' => 'required|numeric',
                    'long' => 'required|numeric'
                ]);
            case 'phone':
                return
                    array_merge($defaultRules, [
                        'phone' => 'required|string',
                    ]);
            case 'email':
                return array_merge($defaultRules, [
                    'email' => 'required|string',
                ]);
            default:
                return $defaultRules;
        }
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'type' => ContactType::from($this->type),
        ]);
    }
}
