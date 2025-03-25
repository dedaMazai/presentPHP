<?php

namespace App\Http\Resources\Sales\File;

use Illuminate\Http\Resources\Json\JsonResource;

class FileResource extends JsonResource
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
            'name' => $this->getName(),
            'document' => $this->getDocument(),
            'mime_type' => $this->getMimeType(),
            'document_type' => (int)$this->getType(),
            'document_processing_status' => (int)$this->getProcessingStatus(),
            'status' => (int)$this->getStatus(),
            'url' => $this->getUrl(),
        ];
    }
}
