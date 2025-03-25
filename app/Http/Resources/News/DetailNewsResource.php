<?php

namespace App\Http\Resources\News;

use App\Http\Resources\ContentItemCollection;
use App\Http\Resources\ContentItemResource;
use App\Http\Resources\ImageResource;
use App\Http\Resources\UkProjectResource;
use App\Models\UkProject;
use Illuminate\Http\Resources\Json\JsonResource;

class DetailNewsResource extends JsonResource
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
            'type' => $this->type->value,
            'category' => $this->category?->value,
            'title' => $this->title,
            'description' => $this->description,
            'tag' => $this->tag,
            'url' => $this->url,
            'preview_image' => $this->previewImage ? (new ImageResource($this->previewImage)) : null,
            'uk_project' => new UkProjectResource(UkProject::find($this->uk_project_id)),
            'created_at' => $this->created_at->toDateTimeString(),
            'content_items' => new ContentItemCollection($this->contentItems)
        ];
    }
}
