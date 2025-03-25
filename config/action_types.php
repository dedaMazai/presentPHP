<?php

use App\Services\Action\Mappers\NewsMapper;

return [
    'external_link' => [
        'label' => 'Внешняя ссылка',
        'payload_params' => [
            'url' => [
                'label' => 'URL',
                'type' => 'string',
            ],
        ],
    ],
    'claim_status_changed' => [
        'label' => 'Изменение статуса заявки',
        'payload_params' => [
            'claim_id' => [
                'label' => 'ID заявки',
                'type' => 'string',
            ],
        ],
    ],
    'new_claim_message' => [
        'label' => 'Новое сообщение по заявке',
        'payload_params' => [
            'claim_id' => [
                'label' => 'ID заявки',
                'type' => 'string',
            ],
            'account_number' => [
                'label' => 'Номер лицевого счета',
                'type' => 'string',
            ],
        ],
    ],
    'demand_documents_ready' => [
        'label' => 'Документы для сделки подготовлены',
        'payload_params' => [
            'demand_id' => [
                'label' => 'ID заявки',
                'type' => 'string',
            ],
        ],
    ],
    'demand_document_rejected' => [
        'label' => 'Документ отклонен',
        'payload_params' => [
            'demand_id' => [
                'label' => 'ID заявки',
                'type' => 'string',
            ],
        ],
    ],
    'demand_contract_registered' => [
        'label' => 'Договор зарегистрирован',
        'payload_params' => [
            'demand_id' => [
                'label' => 'ID заявки',
                'type' => 'string',
            ],
        ],
    ],
    'news' => [
        'label' => 'Новость',
        'payload_params' => [
            'news_id' => [
                'label' => 'Новость',
                'type' => 'integer',
                'options_mapper' => NewsMapper::class
            ],
        ],
    ],
    'debt' => [
        'label' => 'Задолженность ЖКУ',
        'payload_params' => [
            'account_number' => [
                'label' => 'Номер лицевого счета',
                'type' => 'string',
            ],
        ],
    ],
    'birthday_link' => [
        'label' => 'Поздравление с днем рождения',
        'payload_params' => [
            'url' => [
                'label' => 'URL',
                'type' => 'string',
            ],
        ],
    ],
    'insurance_expire_link' => [
        'label' => 'Срок страховки истекает',
        'payload_params' => [
            'url' => [
                'label' => 'URL',
                'type' => 'string',
            ],
        ],
    ],
];
