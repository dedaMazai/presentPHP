<?php

namespace App\Http\Resources\V2\Sales\Contract\JointOwner;

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
        $documentName = DocumentsName::where('document_type', '=', '65538');
        return [
            'name' => $documentName?->name,
            'id' => $this['id'],
            'description' => $documentName?->description,
        ];
    }
}
