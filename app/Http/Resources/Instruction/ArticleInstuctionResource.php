<?php

namespace App\Http\Resources\Instruction;

use App\Models\Document;
use Illuminate\Http\Resources\Json\JsonResource;

class ArticleInstuctionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        /** @var Document $this */
        return [
            'name' => $this?->name,
            'mimetype' => $this?->mime_type,
            'link' => $this?->url,
        ];
    }
}
