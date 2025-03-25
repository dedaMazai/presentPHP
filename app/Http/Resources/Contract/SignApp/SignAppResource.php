<?php

namespace App\Http\Resources\Contract\SignApp;

use App\Http\Resources\Meter\MeterEnterPeriodResource;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class SignAppResource extends JsonResource
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
            'sign_app_url' => new SignAppUrlResource($this['signAppUrl']),
            'sign_app_manual' => new SignAppManualCollection($this['signAppManual']),
        ];
    }
}
