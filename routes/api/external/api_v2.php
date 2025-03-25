<?php


use App\Http\Api\External\V2\Controllers\AccountController;
use App\Http\Api\External\V2\Controllers\PropertyController;
use App\Http\Api\External\V2\Controllers\AdController;
use App\Http\Api\External\V2\Controllers\RelationshipInviteController;
use App\Http\Api\External\V2\Controllers\Sales\ContractController;
use App\Http\Api\External\V2\Controllers\Sales\DemandController;
use App\Http\Api\External\V2\Controllers\Sales\DeponentController;
use App\Http\Api\External\V2\Controllers\Sales\JointOwnerController;
use App\Http\Api\External\V2\Controllers\UserController;
use App\Http\Api\External\V2\Controllers\UserDocumentController;
use App\Http\Api\External\V2\Controllers\SettingsController;
use App\Models\Ad\AdPlace;
use Illuminate\Routing\Router;

/** @var Router $router */

$router->group(['middleware' => 'auth:sanctum'], function (Router $router) {
    //announcements
    // phpcs:disable
    $router->get('announcements/{place}', [AdController::class, 'show'])->where([
        'place' => AdPlace::getAllowedValuesRegex(),
    ]);

    $router->get('user', [UserController::class, 'view']);

    $router->get('accounts', [AccountController::class, 'index']);// User

    $router->get('user/favorite-properties', [PropertyController::class, 'getFavorites']);
    $router->get('user/documents', [UserDocumentController::class, 'index']);

    $router->get('accounts/{accountNumber}/relationship-invites', [RelationshipInviteController::class, 'index']);
    $router->post('accounts/{accountNumber}/relationship-invites', [RelationshipInviteController::class, 'store']);
    $router->delete('/accounts/{accountNumber}/relationship-invites/{id}', [RelationshipInviteController::class, 'delete']);

    $router->get('demands', [DemandController::class, 'index']);
    $router->get('demands/{id}', [DemandController::class, 'show']);

    $router->get('contracts/{id}', [ContractController::class, 'show']);
    $router->get('contracts/{id}/jointowners/{jointOwnersId}/confidant', [ContractController::class, 'getConfidant']);
    $router->get('contracts/{id}/jointowners-info', [ContractController::class, 'getJointOwnersInfo']);
    $router->get('contracts/{id}/jointowners/sign-info', [ContractController::class, 'getJointOwnersSignInfo']);

    $router->get('user/archive-contracts/{id}', [ContractController::class, 'getArchiveContract']);

    $router->get('/demands/{demandId}/jointowners', [JointOwnerController::class, 'index']);
    $router->post('deponent', [DeponentController::class, 'getDeponent']);
});

//settings
$router->get('settings/general', [SettingsController::class, 'showGeneral']);
