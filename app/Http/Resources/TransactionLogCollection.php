<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class TransactionLogCollection extends ResourceCollection
{
    public function __construct($resource)
    {
        $this->pagination = [
            'total' => $resource->total(), // всего записей
            'count' => $resource->count(), // всего на странице
            'per_page' => $resource->perPage(), // лимит страницы
            'current_page' => $resource->currentPage(), // текущая страница
            'total_pages' => $resource->lastPage(), // последняя страница
            'links' => [
                'next' => $resource->nextPageUrl()
            ],
        ];
        $resource = $resource->getCollection();

        parent::__construct($resource);
    }
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'transaction_logs' => $this->collection,
            'meta' => ["pagination" => $this->pagination],
        ];
    }
}
