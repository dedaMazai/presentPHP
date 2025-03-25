<?php

namespace App\Http\Resources\Claim;

use Illuminate\Http\Resources\Json\JsonResource;

class ClaimCatalogueItemResource extends JsonResource
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
            'id' => $this->getId(),
            'name' => $this->getName(),
            'parent_id' => $this->getParentId(),
            'theme' => $this->getTheme()?->value,
            'group' => $this->getGroup()->value,
            'price_type' => $this->getPriceType()?->value,
            'price' => $this->getPrice(),
            'is_popular' => $this->getIsPopular(),
            'is_service' => $this->getGroup()->value==30,
//            'is_service' => $this->getIsService(),
            'description' => $this->getDescription(),
            'time_localization' => $this->getTimeLocalization(),
            'time_reaction' => $this->getTimeReaction(),
            'time_solution' => $this->getTimeSolution(),
            'unit' => $this->getUnit(),
            'select_option' => $this->getSelectOption()?->value,
            'work_schedule' => $this->getWorkSchedule(),
            'title' => $this->getTitle(),
            'order' => $this->getOrder(),
            'execution_norm' => $this->getExecutionNorm(),
            'is_execute_time' => $this->getIsDisplay(),
            'nds' => $this->getNds(),
            'images' => new ClaimCatalogueItemImagesResource($this->getImages()),
            'children' => $this->getIsDisplayedInLk() ? new ClaimCatalogueItemCollection($this->getChildren()) : []
        ];
    }
}
