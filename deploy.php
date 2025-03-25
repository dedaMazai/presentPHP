<?php

namespace Deployer;

require 'recipe/laravel.php';
require 'contrib/php-fpm.php';
require 'contrib/yarn.php';

set('application', 'pioneer');
set('php_fpm_version', '8.0');
set('default_timeout', 0);
set('update_code_strategy', 'clone');
set('shared_files', [
    '.env',
    'google-service-account.json',
    'sberbank.json'
]);

task('deploy', [
    'deploy:prepare',
    'deploy:vendors',
    'artisan:storage:link',
    'artisan:view:cache',
    'artisan:config:cache',
    'artisan:migrate',
    'yarn:install',
    'yarn:run:prod',
    'deploy:publish',
    'php-fpm:reload',
    'supervisor:restart',
]);

task('supervisor:restart', function () {
    run('sudo service supervisor restart');
})->desc('Restart Supervisor');

task('yarn:run:prod', function () {
    run('cd {{release_path}} && yarn run prod');
})->desc('Build frontend static');

import('servers.yml');
