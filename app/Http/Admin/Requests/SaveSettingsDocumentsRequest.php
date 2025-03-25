<?php

namespace App\Http\Admin\Requests;

/**
 * Class SaveSettingsDocumentsRequest
 *
 * @package App\Http\Admin\Requests
 */
class SaveSettingsDocumentsRequest extends Request
{
    public function rules(): array
    {
        return [
            'offer_url' => 'required|string',
            'consent_url' => 'required|string',
            'confidant_url' => 'string',
        ];
    }
}
