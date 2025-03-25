<?php

namespace App\Http\Resources\Uk;

use App\Http\Resources\Building\BuildingCollection;
use App\Models\Building\Building;
use Illuminate\Http\Resources\Json\JsonResource;

class UkProjectBResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $buildings = Building::where('project_id', $this->id)
            ->whereIn('id', $this->buildings_id)
            ->get();

        return [
            'id' => $this->id,
            'name' => $this->name,
            'buildings' => new BuildingCollection($buildings),
        ];
    }
}
