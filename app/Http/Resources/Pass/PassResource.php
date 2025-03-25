<?php

namespace App\Http\Resources\Pass;

use Illuminate\Http\Resources\Json\JsonResource;

class PassResource extends JsonResource
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
            'status' => $this->getStatus(),
            'arrival_date' => $this->getArrivalDate()?->locale('ru')->translatedFormat('j F Y'),
            'start_date' => $this->getStartDate()?->locale('ru')->translatedFormat('j F Y'),
            'end_date' => $this->getEndDate()?->locale('ru')->translatedFormat('j F Y'),
            'creation_date' => $this->getCreationDate()?->locale('ru')->translatedFormat('j F Y'),
            'comment' => $this->getComment(),
            'assignment' => $this->getAssignment(),
            'car_type' => $this->getCarType(),
        ];
    }
}
