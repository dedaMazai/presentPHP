<?php

namespace App\Http\Resources\Contract\SignApp;

use App\Models\Document;
use Illuminate\Http\Resources\Json\JsonResource;

class SignAppManualResource extends JsonResource
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
            'name' => $this['name'],
            'url' => $this['document_id'],
            'code' => $this['code'],
        ];
    }
}
