<?php

namespace App\Http\Resources\Sales\Contract;

use App\Models\Contract\ContractDocument;
use App\Models\Document\Document;
use App\Models\DocumentsName;
use App\Models\Sales\Contract\ArchiveContracts;
use Illuminate\Http\Resources\Json\JsonResource;

class UserArchiveContractResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        /** @var ArchiveContracts $this */

        return [
            'id' => $this->getId(),
            'contract_name' => $this->getContractName(),
            'contract_date' => $this->getContractDate(),
            'project_name' => $this->getProjectName(),
            'name_lk' => $this->getNameLk(),
            'number' => $this->getNumber(),
        ];
    }
}
