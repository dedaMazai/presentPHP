<?php

namespace App\Http\Admin\Requests;

/**
 * Class SaveSignDocumentsRequest
 *
 * @package App\Http\Admin\Requests
 */
class SaveSignDocumentsRequest extends Request
{
    public function rules(): array
    {
        return [
            'name' => 'required|string',
            'code' => 'required|string',
            'document_id' => 'required|string',
        ];
    }
}
