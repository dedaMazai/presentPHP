<?php

namespace App\Models\Notification;

use Spatie\Enum\Enum;

/**
 * Class NotificationDestinationType
 *
 * @method static self allUsers()
 * @method static self customersByProjects()
 * @method static self ownersByUkProjects()
 * @method static self ownersByAccountRealtyTypes()
 * @method static self singleCrmUser()
 * @method static self singleUserByPhone()
 * @method static self companyNewsSubscribers()
 * @method static self allUkUsers()
 * @method static self usersByUkAndBuilding()
 * @method static self unauthorizedUsers()
 *
 * @package App\Models\Notification
 */
class NotificationDestinationType extends Enum
{
    protected static function values(): array
    {
        return [
            'allUsers' => 'all_users',
            'customersByProjects' => 'customers_by_projects',
            'ownersByUkProjects' => 'owners_by_uk_projects',
            'ownersByAccountRealtyTypes' => 'owners_by_account_realty_types',
            'singleCrmUser' => 'single_crm_user',
            'singleUserByPhone' => 'single_user_by_phone',
            'companyNewsSubscribers' => 'company_news_subscribers',
            'allUkUsers' => 'all_uk_users',
            'usersByUkAndBuilding' => 'users_by_uk_and_building',
            'unauthorizedUsers' => 'unauthorized_users',
        ];
    }
    protected static function labels(): array
    {
        return [
            'allUsers' => 'Все пользователи',
            'customersByProjects' => 'Покупатель недвижимости, находящийся на этапе покупки',
            'ownersByUkProjects' => 'Пользователи, имеющие недвижимость',
            'ownersByAccountRealtyTypes' => 'Клиенты, купившие недвижимость определенного типа',
            'singleCrmUser' => 'Пользователь',
            'singleUserByPhone' => 'Пользователь (по номеру телефона)',
            'companyNewsSubscribers' => 'Получатели новостей компании',
            'allUkUsers' => 'Все пользователи УК',
            'usersByUkAndBuilding' => 'Настроить аудиторию по ЖК и корпусу',
            'unauthorizedUsers' => 'Неавторизованные пользователи',
        ];
    }
}
