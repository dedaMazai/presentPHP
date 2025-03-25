## Pioneer

### Система очередей
- `php artisan queue:work` запуск процесса обработки очереди 

Подключение Keycloak:

- Получить `Public Key` для realm
- Включить авторизацию по `Client Secret` в `admin-cli` scope
- Включить `Service Accounts` для `admin-cli` scope
- Добавить роль `admin` в `Service Account Roles` для `admin-cli` scope
