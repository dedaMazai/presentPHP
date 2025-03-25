<?php

namespace App\Providers;

use App\Services\Account\AccountDocumentRepository;
use App\Services\Crm\CrmClient;
use Illuminate\Support\ServiceProvider;

/**
 * Class AccountDocumentServiceProvider
 *
 * @package App\Providers
 */
class AccountDocumentServiceProvider extends ServiceProvider
{
    /**
     * @inheritDoc
     */
    public function register()
    {
        $this->registerRepositories();
    }

    private function registerRepositories(): void
    {
        $this->app->singleton(AccountDocumentRepository::class, function () {
            return new AccountDocumentRepository(
                $this->app->make(CrmClient::class),
                $this->app->make('cache.store')
            );
        });
    }
}
