<?php

use App\Models\Notification\NotificationDestinationType;
use App\Services\Notification\Mappers\AccountRealtyTypeMapper;
use App\Services\Notification\Mappers\ProjectMapper;
use App\Services\Notification\Mappers\UkProjectMapper;

return [
    (string)NotificationDestinationType::allUsers() => [
        'label' => 'Все пользователи',
        'available_for_admin' => true,
        'available_actions' => ['external_link'],
    ],
    (string)NotificationDestinationType::customersByProjects() => [
        'label' => 'Покупатель недвижимости, находящийся на этапе покупки',
        'payload_params' => [
            'project_ids' => [
                'label' => 'Проект',
                'type' => 'array',
                'options_mapper' => ProjectMapper::class,
            ],
        ],
        'available_for_admin' => true,
        'available_actions' => [],
    ],
    (string)NotificationDestinationType::ownersByAccountRealtyTypes() => [
        'label' => 'Клиенты, купившие недвижимость определенного типа',
        'payload_params' => [
            'account_realty_types' => [
                'label' => 'Тип объекта недвижимости',
                'type' => 'array',
                'options_mapper' => AccountRealtyTypeMapper::class,
            ],
        ],
        'available_for_admin' => true,
        'available_actions' => [],
    ],
    (string)NotificationDestinationType::singleCrmUser() => [
        'label' => 'Пользователь',
        'payload_params' => [
            'crm_id' => [
                'label' => 'ID пользователя в CRM',
                'type' => 'string',
            ]
        ],
        'available_for_admin' => true,
    ],
    (string)NotificationDestinationType::singleUserByPhone() => [
        'label' => 'Пользователь (по номеру телефона)',
        'payload_params' => [
            'phone' => [
                'label' => 'Телефон',
                'type' => 'string',
            ]
        ],
        'available_for_admin' => true,
    ],
    (string)NotificationDestinationType::ownersByUkProjects() => [
        'label' => 'Пользователи, имеющие недвижимость',
        'payload_params' => [
            'uk_project_ids' => [
                'label' => 'Проект УК',
                'type' => 'array',
                'options_mapper' => UkProjectMapper::class,
            ]
        ],
        'available_for_admin' => true,
        'available_actions' => [],
    ],
    (string)NotificationDestinationType::companyNewsSubscribers() => [
        'label' => 'Получатели новостей компании',
        'available_for_admin' => false,
    ],
    (string)NotificationDestinationType::allUkUsers() => [
        'label' => 'Все пользователи УК',
        'payload_params' => [
            'uk_project_ids' => [
                'label' => 'Проект УК',
                'type' => 'array',
                'options_mapper' => UkProjectMapper::class,
            ]
        ],
        'available_for_admin' => false,
    ],
    (string)NotificationDestinationType::usersByUkAndBuilding() => [
        'label' => 'Настроить аудиторию по ЖК и корпусу',
        'payload_params' => [
            'uk_project_ids' => [
                'label' => 'Проект УК',
                'type' => 'array',
                'options_mapper' => UkProjectMapper::class,
            ]
        ],
        'available_for_admin' => false,
    ],
    (string)NotificationDestinationType::unauthorizedUsers() => [
        'label' => 'Неавторизованные пользователи',
        'available_for_admin' => true,
    ],
];
