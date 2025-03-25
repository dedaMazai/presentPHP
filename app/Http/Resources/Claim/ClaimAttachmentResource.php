<?php

namespace App\Http\Resources\Claim;

use App\Services\DynamicsCrm\DynamicsCrmClient;
use Illuminate\Http\Resources\Json\JsonResource;

class ClaimAttachmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this['id'],
            'name' => $this['name'],
            'file_name' => $this['fileName']??null,
            'file_size' => $this['fileSize']??null,
            'type' => $this['documentType']->code??null,
            'is_read' => $this['isReadDocument']??null,
            'created_at' => $this['createdOn']??null,
            'url' => $this['url']??null,
            'preview' => $this['urlPreview']??null,
        ];
    }
}
