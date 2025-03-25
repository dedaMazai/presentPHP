<?php

namespace App\Providers;

use App\Models\Project\Project;
use App\Models\Settings;
use App\Models\UkProject;
use App\Validation\MaxFilesSizeValidator;
use App\Validation\PhoneNumberValidator;
use Illuminate\Contracts\Validation\Factory as ValidatorFactory;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->bootValidators();
        $this->bootMorphMap();
        $this->forceHttps();
    }

    private function forceHttps(): void
    {
        if (config('app.force_https') === true) {
            URL::forceScheme('https');
        }
    }

    private function bootValidators(): void
    {
        /** @var ValidatorFactory $validator */
        $validator = $this->app->make('validator');

        $validator->extend(
            'phone_number',
            PhoneNumberValidator::class . '@__invoke',
            'Invalid phone number.'
        );

        $validator->extend('max_files_size', MaxFilesSizeValidator::class);
    }

    private function bootMorphMap(): void
    {
        Relation::morphMap([
            'project' => Project::class,
            'uk_project' => UkProject::class,
            'setting' => Settings::class,
        ]);
    }
}
