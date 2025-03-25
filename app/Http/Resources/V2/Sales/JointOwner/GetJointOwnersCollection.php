<?php

namespace App\Http\Resources\V2\Sales\JointOwner;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class GetJointOwnersCollection extends ResourceCollection
{
    public static $wrap = null;
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $result = [];

        foreach ($this->collection as $ownerItem) {
            // Преобразуем одного владельца через JointOwnerResource
            $ownerArray = (new GetJointOwnersResource($ownerItem))->toArray($request);

            // Если ресурс вернул "список" (массив объектов) — сливаем в общий результат
            // Если вернул ассоциативный массив (физлицо) — добавляем одним элементом
            if ($this->isNumericArray($ownerArray)) {
                $result = array_merge($result, $ownerArray);
            } else {
                $result[] = $ownerArray;
            }
        }

        return $result;
    }

    /**
     * Проверяем, "числовой" ли массив (индексированный 0..n-1).
     */
    private function isNumericArray(array $array): bool
    {
        return array_keys($array) === range(0, count($array) - 1);
    }
}
