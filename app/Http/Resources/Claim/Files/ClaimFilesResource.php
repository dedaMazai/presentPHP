<?php

namespace App\Http\Resources\Claim\Files;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class ClaimFilesResource extends JsonResource
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
            'id' => $this['id'],
            'message_type' => 'document',
            'file_name' => $this['fileName']??null,
            'file_size' => $this['fileSize']??null,
            'mime_type' => $this['mimeType']??null,
            'url' => $this['url']??null,
            'preview' => $this['urlPreview']??null,
            'is_read' => $this['isReadDocument']??null,
            'message_date' =>  $this['createdOn']?(Carbon::parse($this['createdOn'])
                ->setTimezone('6')
                ->shiftTimezone(3)
                ->toDateTimeString()):null,
            'type' => in_array($this['documentType']['code'], [524751,524504])?'manager':'client',
            'sender_name' => $this['sender']??null,
            'sender_position' => $this['senderPosition']??null,
        ];
    }
}
//id - из documentList.id
//message_type : document - для всех сообщений, полученных от сервиса списка документов по заявке
//file_name из fileName
//file_size из fileSize (??? пока не добавлено)
//mime_type - из documentList.mimeType
//url - из documentList.url
//preview - из documentList.preview (???пока не добавлено)
//is_read - из documentList.isReadDocument
//message_date - из documentList.createdOn
//type - определяем по documentList.documentType.code - если (524751,524504), то manager; если (524500, 524750), то client
//sender_name - ???
//sender_position - ???
