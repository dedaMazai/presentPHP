<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DocumentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $bytes = $this->size;

        if ($bytes >= 1048576) {
            $size = number_format($bytes / 1048576, 2) . ' Мб';
        } elseif ($bytes >= 1024) {
            $size = number_format($bytes / 1024, 2) . ' Кб';
        } elseif ($bytes > 1) {
            $size = $bytes . ' б';
        }

        return [
            'name' => $this->name,
            'extension' => pathinfo($this->name, PATHINFO_EXTENSION),
            'mimetype' => $this->mime_type,
            'size' => $size,
            'link' => $this->url,
        ];
    }
}
