<?php

namespace App\Http\Resources\News;

use App\Http\Resources\ContentItemCollection;
use Illuminate\Http\Resources\Json\ResourceCollection;

class DetailNewsCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return new ContentItemCollection($this->contentItems);
    }
}
