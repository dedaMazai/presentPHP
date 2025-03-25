<?php

namespace App\Http\Api\External\V1\Requests\Claim;

/**
 * Class SaveClaimRequestRequest
 *
 * @package App\Http\Api\External\V1\Requests\Clais
 */
class SaveClaimRequestRequest extends ClaimManipulationRequest
{
    public const MAX_PHOTOS_SIZE = 50000;

    public function rules(): array
    {
        return [
            'claim_catalogue_item_ids' => 'required|array',
            'claim_catalogue_item_ids.*' => 'required|string',
            'arrival_date' => 'nullable|date',
            'comment' => 'string',
            'images' => 'array|max_files_size:' . self::MAX_PHOTOS_SIZE,
            'images.*' => 'file',
        ];
    }

    public function messages(): array
    {
        return [
            'images.max_files_size' => 'Размер изображений не должен привышать ' . self::MAX_PHOTOS_SIZE / 1000 . 'MB',
        ];
    }
}
