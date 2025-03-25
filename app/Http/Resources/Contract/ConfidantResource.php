<?php

namespace App\Http\Resources\Contract;

use App\Http\Resources\Sales\Contract\DocumentCollection;
use App\Http\Resources\Sales\Contract\TemplateCollection;
use App\Models\Settings;
use App\Models\V2\Sales\Customer\Customer;
use Illuminate\Http\Resources\Json\JsonResource;

class ConfidantResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $user = $this['user'];

        /** @var Customer $user */
        return [
            'id' => $user->getId(),
            'joint_owner_id' => $user->getJointOwnerId(),
            'full_name' => $user->getFullName(),
            'confidant_url' => Settings::first()?->confidant_url,
//            'templates' => new TemplateCollection($this['templates']),
            'documents' => new DocumentCollection($this['documents']),
        ];
    }
}
