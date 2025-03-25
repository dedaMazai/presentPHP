<?php

namespace App\Http\Resources\User;

use App\Models\User\UserSignInfo;
use Illuminate\Http\Resources\Json\JsonResource;

class UserSignInfoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        /** @var UserSignInfo $this */

        return [
            'full_name' => $this->getFullName(),
            'reissue_guide_url' => $this->getReissueGuideUrl(),
            'sign_info' => new UserSignResource($this->getSignInfo()),
            'sign_statement_document' => $this->getSignStatementDocument() ? new SignInfoStatementDocumentResource($this->getSignStatementDocument()) : null,
        ];
    }
}
