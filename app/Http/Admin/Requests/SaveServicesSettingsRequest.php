<?php

namespace App\Http\Admin\Requests;

/**
 * Class SaveServicesSettingsRequest
 *
 * @package App\Http\Admin\Requests
 */
class SaveServicesSettingsRequest extends Request
{
    public function rules(): array
    {
        return [
            'claim_root_category_crm_id' => 'required|string',
            'claim_pass_car_crm_service_id' => 'required|string',
            'claim_pass_human_crm_service_id' => 'required|string',
        ];
    }
}
