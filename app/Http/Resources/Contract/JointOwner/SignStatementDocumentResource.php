<?php

namespace App\Http\Resources\Contract\JointOwner;

use App\Http\Resources\Meter\MeterEnterPeriodResource;
use App\Models\DocumentsName;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class SignStatementDocumentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $documentName = DocumentsName::where('code', '=', '65538')->first();
        return [
            'name' => $documentName?->name,
            'id' => $this->getId(),
            'description' => $documentName?->description,
        ];
    }
}
