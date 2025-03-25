<?php

namespace App\Http\Api\External\V1\Requests;

use App\Models\Document\DocumentType;
use Illuminate\Validation\Rule;

/**
 * Class UploadUserDocumentRequest
 *
 * @package App\Http\Api\External\V1\Requests
 */
class UploadUserDocumentRequest extends Request
{
    public function rules(): array
    {
        return [
            'type' => ['required', Rule::in(DocumentType::toValues())],
            'file' => 'required|mimes:pdf,jpeg,png|max:10240', //10MB
        ];
    }
}
