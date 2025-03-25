<?php

namespace App\Http\Resources;

use App\Models\ContentItem\ContentItemType;
use Illuminate\Http\Resources\Json\JsonResource;

class ContentItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $fields = [
            'type' => $this->type,
        ];

        if ($this->type->equals(
            ContentItemType::text(),
            ContentItemType::title1lvl(),
            ContentItemType::title2lvl(),
            ContentItemType::title3lvl(),
        )) {
            $fields['text'] = $this->text;
        } elseif ($this->type->equals(ContentItemType::video())) {
            $fields['video_url'] = $this->video_url;
        } elseif ($this->type->equals(ContentItemType::image())) {
            $fields['image'] = (new ImageResource($this->image));
        } elseif ($this->type->equals(ContentItemType::document())) {
            $fields['document'] = (new DocumentResource($this->document));
        } elseif ($this->type->equals(ContentItemType::gallery())) {
            foreach ($this->images as $image) {
                $fields['gallery'][] = (new ImageResource($image));
            }
        } elseif ($this->type->equals(
            ContentItemType::factoids(),
            ContentItemType::numberedList(),
            ContentItemType::unnumberedList(),
        )) {
            $fields['content'] = $this->content;
        }

        return $fields;
    }
}
