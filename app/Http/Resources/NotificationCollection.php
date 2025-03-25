<?php

namespace App\Http\Resources;

use App\Models\User\User;
use Illuminate\Http\Resources\Json\ResourceCollection;

class NotificationCollection extends ResourceCollection
{
    public function __construct($resource)
    {
        $notifications = $resource['notifications'];
        $user = $resource['user'];

        $this->pagination = ['pagination' => [
            'total' => $notifications->total(), // всего записей
            'count' => $notifications->count(), // всего на странице
            'per_page' => $notifications->perPage(), // лимит страницы
            'current_page' => $notifications->currentPage(), // текущая страница
            'total_pages' => $notifications->lastPage(), // последняя страница
            'links' => [
                'next' => $notifications->nextPageUrl()
            ],
        ]];

        $resource = $notifications->getCollection();

        foreach ($resource as $key => $value) {
            $resource[$key] = ['notification' => $value, 'user' => $user];
        }

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
            'notifications' => $this->collection,
            'meta' => $this->pagination,
        ];
    }
}
