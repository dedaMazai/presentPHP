<?php

namespace App\Http\Resources\Pass;

use Illuminate\Http\Resources\Json\ResourceCollection;

class PassCollection extends ResourceCollection
{
    public function __construct($resource)
    {
        $sort = '';
        if (isset($_GET['sort'])) {
            $sort = $_GET['sort'] == 'by_date' && $resource->nextPageUrl() != null?'&sort=by_date':'';
        }

        $this->pagination = [
            'total' => $resource->total(), // всего записей
            'count' => $resource->count(), // всего на странице
            'per_page' => $resource->perPage(), // лимит страницы
            'current_page' => $resource->currentPage(), // текущая страница
            'total_pages' => $resource->lastPage(), // последняя страница
            'links' => [
                'next' => url()->current().str_replace('/', '', $resource->nextPageUrl()).$sort
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
            'data' => $this->collection->all(),
            'meta' => ['pagination' => $this->pagination],
        ];
    }
}
