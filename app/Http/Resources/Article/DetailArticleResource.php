<?php

namespace App\Http\Resources\Article;

use App\Http\Resources\ContentItemCollection;
use App\Http\Resources\ImageResource;
use App\Models\Image;
use App\Models\Project\Project;
use Illuminate\Http\Resources\Json\JsonResource;

class DetailArticleResource extends JsonResource
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
            'id' => $this->id,
            'title' => $this->title,
            'icon_image' => new ImageResource(Image::find($this->icon_image_id)),
            'content_items' => new ContentItemCollection($this->contentItems)
        ];
    }
}
