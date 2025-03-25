<?php

namespace App\Http\Admin\Requests;

use App\Models\Mortgage\MortgageType;
use App\Models\Project\ProjectPropertyType;
use Illuminate\Validation\Rule;

/**
 * Class SaveProjectRequest
 *
 * @package App\Http\Admin\Requests
 */
class SaveProjectRequest extends Request
{
    public function rules(): array
    {
        return [
            'is_published' => 'required|boolean',
            'name' => 'required|string|max:255',
            'showcase_image_id' => 'required|integer|exists:images,id',
            'main_image_id' => 'required|integer|exists:images,id',
            'map_image_id' => 'nullable|integer|exists:images,id',
            'metro' => 'nullable|string|max:255',
            'crm_ids' => 'required|array',
            'crm_ids.*' => 'string',
            'mortgage_calculator_id' => 'nullable|integer',
            'lat' => 'required|numeric',
            'long' => 'required|numeric',
            'office_phone' => 'nullable|string|max:255',
            'office_address' => 'nullable|string|max:255',
            'office_lat' => 'nullable|numeric',
            'office_long' => 'nullable|numeric',
            'office_work_hours' => 'nullable|string',
            'property_type_params' => 'required|array',
            'property_type_params.*.type' => [
                'required',
                Rule::in(ProjectPropertyType::toValues()),
            ],
            'property_type_params.*.url' => 'nullable|string|max:255',
            'color' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image_ids' => 'nullable|array',
            'image_ids.*' => 'integer',
            'city_id' => 'required|exists:cities,id',
            'mortgage_types' => 'required|array',
            'mortgage_types.*' => ['required', 'string', Rule::in(MortgageType::toValues())],
            'payroll_bank_programs' => 'array',
            'payroll_bank_programs.*.id' => 'required|integer',
            'payroll_bank_programs.*.name' => 'required|string',
            'mortgage_min_property_price' => 'nullable|numeric',
            'mortgage_max_property_price' => 'nullable|numeric',
            'booking_property' => 'sometimes|array',
            'is_premium' => 'boolean',
            'url_memo' => 'nullable|string'
        ];
    }
}
