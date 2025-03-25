<?php


use App\Http\Api\External\V3\Controllers\Sales\ContractController;
use App\Http\Api\External\V3\Controllers\Sales\DemandController;
use Illuminate\Routing\Router;

/** @var Router $router */

$router->group(['middleware' => 'auth:sanctum'], function (Router $router) {
    $router->get('demands', [DemandController::class, 'index']);
    $router->get('demands/{id}', [DemandController::class, 'show']);

    $router->get('contracts/{id}', [ContractController::class, 'show']);
});
