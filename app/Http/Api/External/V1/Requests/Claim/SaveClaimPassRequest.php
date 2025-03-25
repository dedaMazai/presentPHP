<?php

namespace App\Http\Api\External\V1\Requests\Claim;

use App\Models\Claim\ClaimPass\ClaimPassCarType;
use App\Models\Claim\ClaimPass\ClaimPassType;
use Illuminate\Validation\Rule;

/**
 * Class SaveClaimPassRequest
 *
 * @package App\Http\Api\External\V1\Requests\Claim
 */
class SaveClaimPassRequest extends ClaimManipulationRequest
{
    public function rules(): array
    {
        $rules = [
            'pass_type' => [
                'required',
                Rule::in(ClaimPassType::toValues()),
            ],
            'arrival_date' => 'required|date',
            'comment' => 'nullable|string',
            'images' => 'array',
            'images.*' => 'file',
        ];

        if (ClaimPassType::from($this->pass_type)->equals(ClaimPassType::car())) {
            $rules = array_merge($rules, $this->getCarRules());
        } else {
            $rules = array_merge($rules, $this->getHumanRules());
        }

        return $rules;
    }

    private function getCarRules(): array
    {
        return [
            'car_type' => [
                'required',
                Rule::in(ClaimPassCarType::toValues()),
            ],
            'car_number' => 'required|string',
        ];
    }

    private function getHumanRules(): array
    {
        return [
            'full_name' => 'required|string',
        ];
    }
}
