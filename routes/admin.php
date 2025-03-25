<?php

/** @var Illuminate\Routing\Router $router */

use App\Http\Admin\Controllers\Account\AccountController;
use App\Http\Admin\Controllers\AdController;
use App\Http\Admin\Controllers\Article\ArticleContentItemController;
use App\Http\Admin\Controllers\Article\ArticleController;
use App\Http\Admin\Controllers\Auth\LoginController;
use App\Http\Admin\Controllers\BankInfoController;
use App\Http\Admin\Controllers\BannerController;
use App\Http\Admin\Controllers\BlockingController;
use App\Http\Admin\Controllers\FileController;
use App\Http\Admin\Controllers\InstructionController;
use App\Http\Admin\Controllers\MortgageProgramController;
use App\Http\Admin\Controllers\News\NewsContentItemController;
use App\Http\Admin\Controllers\News\NewsController;
use App\Http\Admin\Controllers\NotificationController;
use App\Http\Admin\Controllers\PaymentController;
use App\Http\Admin\Controllers\ProjectController;
use App\Http\Admin\Controllers\ProjectTypeController;
use App\Http\Admin\Controllers\SecuredPaymentController;
use App\Http\Admin\Controllers\Settings\SettingsBuildsController;
use App\Http\Admin\Controllers\Settings\SettingsCacheController;
use App\Http\Admin\Controllers\Settings\SettingsContactController;
use App\Http\Admin\Controllers\Settings\SettingsContentItemController;
use App\Http\Admin\Controllers\Settings\SettingsController;
use App\Http\Admin\Controllers\Settings\SettingsDeletingReasonController;
use App\Http\Admin\Controllers\Settings\SettingsDocumentsController;
use App\Http\Admin\Controllers\Settings\SettingsServicesController;
use App\Http\Admin\Controllers\UkProject\UkBuildingController;
use App\Http\Admin\Controllers\UkArticle\UkArticleContentItemController;
use App\Http\Admin\Controllers\UkArticle\UkArticleController;
use App\Http\Admin\Controllers\UkProject\UkProjectContactController;
use App\Http\Admin\Controllers\UkProject\UkProjectController;
use App\Http\Admin\Controllers\RealityTypes\RealityTypesController;
use App\Http\Admin\Controllers\GroupingRealityTypes\GroupingRealityTypesController;
use App\Http\Admin\Controllers\ClientRoleTypes\ClientRoleTypesController;
use App\Http\Admin\Controllers\Finishing\FinishingController;
use App\Http\Admin\Controllers\Banks\BanksController;
use App\Http\Admin\Controllers\EscrowBanks\EscrowBanksController;
use App\Http\Admin\Controllers\DocumentsName\DocumentsNameController;
use App\Http\Admin\Controllers\SignDocuments\SignDocumentsController;
use App\Http\Admin\Controllers\SupportTopics\SupportTopicsController;
use App\Http\Admin\Controllers\UserController;
use App\Http\Middleware\RedirectIfAuthenticated;
use App\Models\Banner\BannerPlace;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Redirect;

$router->get('/', function () {
    return response(null, 204);
})->middleware(RedirectIfAuthenticated::class);

$router->get('/ios', function () {
    return Redirect::to(config('mobile.ios_url'));
});

$router->get('/android', function () {
    return Redirect::to(config('mobile.android_url'));
});

$router->get('/.well-known/apple-app-site-association', function () {
    echo '{
    "applinks": {
        "apps": [],
        "details": [
            {
                "appID": "HTLN879BNY.ru.pioneer.my",
                "paths": ["*/balance/success/*", "*/payment/success/*", "*/pay-booking/success/*"]
            }
        ]
    }
}';
})->name('ios_pay');

$router->get('/balance/success', function () {
});

$router->get('/payment/success', function () {
});

$router->get('/pay-booking/success', function () {
});

$router->get('/balance/checkout-success', [PaymentController::class, 'balanceCheckout']);

$router->get('/payment/checkout-success', [PaymentController::class, 'balanceCheckout']);

$router->get('/pay-booking/checkout-success', [PaymentController::class, 'balanceBookingCheckout']);

// Login
$router->get(
    'login',
    [LoginController::class, 'showLoginForm']
)->name('login')->middleware(RedirectIfAuthenticated::class);
$router->post('login', [LoginController::class, 'login']);
$router->post('logout', [LoginController::class, 'logout'])->name('logout');

$router->post('/images', [FileController::class, 'uploadImage'])
    ->name('images.upload');
$router->post('/documents', [FileController::class, 'uploadDocument'])
    ->name('documents.upload');
$router->post('/admin-documents', [FileController::class, 'uploadAdminDocument'])
    ->name('admin-documents.upload');
$router->post('/builds', [FileController::class, 'uploadBuild'])
    ->name('builds.upload');

//payments
$router->get('payments', [SecuredPaymentController::class, 'index'])->name('payments');
$router->get('payments/export', [SecuredPaymentController::class, 'export'])->name('payments.export');

//accounts
$router->get('accounts', [AccountController::class, 'index'])->name('accounts');
$router->post('accounts/update', [AccountController::class, 'update'])->name('accounts.update');


$router->group(['middleware' => ['role:admin|uk']], function (Router $router) {
    //news
    $router->get('/news', [NewsController::class, 'index'])->name('news');
    $router->get('/news/create', [NewsController::class, 'create'])->name('news.create');
    $router->post('/news', [NewsController::class, 'store'])->name('news.store');
    $router->get('/news/{id}/edit', [NewsController::class, 'edit'])->name('news.edit');
    $router->put('/news/{id}', [NewsController::class, 'update'])->name('news.update');
    $router->delete('/news/{id}', [NewsController::class, 'destroy'])->name('news.destroy');
    $router->post('/news/{id}/status', [NewsController::class, 'updateStatus'])->name('news.update-status');
    $router->post('/news/{id}/copy', [NewsController::class, 'copy'])->name('news.copy');
    $router->post('/news/{id}/store-from-edit', [NewsController::class, 'storeFromEdit'])
        ->name('news.store-from-edit');



    //news content items
    $router->put('/news/{id}/content-items/sort', [NewsContentItemController::class, 'sort'])
        ->name('news.content-items.sort');
    $router->post('/news/{id}/content-items/{type}', [NewsContentItemController::class, 'store'])
        ->name('news.content-items.store');
    $router->put('/news/{id}/content-items/{itemId}', [NewsContentItemController::class, 'update'])
        ->name('news.content-items.update');
    $router->delete('/news/{id}/content-items/{itemId}', [NewsContentItemController::class, 'destroy'])
        ->name('news.content-items.destroy');

    //offers
    $router->get('offers', [BannerController::class, 'places'])->name('banners.places');
    $router->middleware('banner_place')->group(function (Router $router) {
        $router->get('offers/{place}', [BannerController::class, 'index'])->name('banners')
            ->where([
                'place' => BannerPlace::getAllowedValuesRegex(),
            ]);
        $router->put('offers/{place}/sort', [BannerController::class, 'sort'])->name('banners.sort')
            ->where([
                'place' => BannerPlace::getAllowedValuesRegex(),
            ]);
        $router->get('offers/{place}/create', [BannerController::class, 'create'])->name('banners.create')
            ->where([
                'place' => BannerPlace::getAllowedValuesRegex(),
            ]);
        $router->post('offers/{place}', [BannerController::class, 'store'])->name('banners.store')
            ->where([
                'place' => BannerPlace::getAllowedValuesRegex(),
            ]);
        $router->get('offers/{place}/{id}/edit', [BannerController::class, 'edit'])->name('banners.edit')
            ->where([
                'place' => BannerPlace::getAllowedValuesRegex(),
            ]);
        $router->put('offers/{place}/{id}', [BannerController::class, 'update'])->name('banners.update')
            ->where([
                'place' => BannerPlace::getAllowedValuesRegex(),
            ]);
        $router->delete('offers/{place}/{id}', [BannerController::class, 'destroy'])->name('banners.destroy')
            ->where([
                'place' => BannerPlace::getAllowedValuesRegex(),
            ]);
        $router->post('offers/{place}/{id}/status', [BannerController::class, 'updateStatus'])
            ->name('banners.update-status')
            ->where([
                'place' => BannerPlace::getAllowedValuesRegex(),
            ]);
    });

    //announcements
    $router->get('announcements', [AdController::class, 'index'])
        ->name('ads');
    $router->get('announcements/create', [AdController::class, 'create'])
        ->name('ads.create');
    $router->post('announcements', [AdController::class, 'store'])
        ->name('ads.store');
    $router->get('announcements/{id}/edit', [AdController::class, 'edit'])
        ->name('ads.edit');
    $router->put('announcements/{id}', [AdController::class, 'update'])
        ->name('ads.update');
    $router->delete('announcements/{id}', [AdController::class, 'destroy'])
        ->name('ads.destroy');
    $router->post('announcements/{id}/status', [AdController::class, 'updateStatus'])
        ->name('ads.update-status');

    //uk projects
    $router->get('uk-projects', [UkProjectController::class, 'index'])
        ->name('uk-projects');
    $router->get('uk-projects/create', [UkProjectController::class, 'create'])
        ->name('uk-projects.create');
    $router->post('uk-projects', [UkProjectController::class, 'store'])
        ->name('uk-projects.store');
    $router->get('uk-projects/{id}/edit', [UkProjectController::class, 'edit'])
        ->name('uk-projects.edit');
    $router->put('uk-projects/{id}', [UkProjectController::class, 'update'])
        ->name('uk-projects.update');
    $router->delete('uk-projects/{id}', [UkProjectController::class, 'destroy'])
        ->name('uk-projects.destroy');
    $router->post('uk-projects/{id}/status', [UkProjectController::class, 'updateStatus'])
        ->name('uk-projects.update-status');

    //uk project buildings
    $router->get(
        'uk-projects/{ukProjectId}/buildings',
        [UkBuildingController::class, 'index']
    )->name('uk-projects.buildings');
    $router->put(
        'uk-projects/{ukProjectId}/buildings/sort',
        [UkBuildingController::class, 'sort']
    )->name('uk-projects.buildings.sort');
    $router->get(
        'uk-projects/{ukProjectId}/buildings/create',
        [UkBuildingController::class, 'create']
    )->name('uk-projects.buildings.create');
    $router->post(
        'uk-projects/{ukProjectId}/buildings',
        [UkBuildingController::class, 'store']
    )->name('uk-projects.buildings.store');
    $router->get(
        'uk-projects/{ukProjectId}/buildings/{id}/edit',
        [UkBuildingController::class, 'edit']
    )->name('uk-projects.buildings.edit');
    $router->put(
        'uk-projects/{ukProjectId}/buildings/{id}',
        [UkBuildingController::class, 'update']
    )->name('uk-projects.buildings.update');
    $router->delete(
        'uk-projects/{ukProjectId}/buildings/{id}',
        [UkBuildingController::class, 'destroy']
    )->name('uk-projects.buildings.destroy');

    //uk project articles
    $router->get('uk-projects/{ukProjectId}/articles', [UkArticleController::class, 'index'])
        ->name('uk-projects.articles');
    $router->put('uk-projects/{ukProjectId}/articles/sort', [UkArticleController::class, 'sort'])
        ->name('uk-projects.articles.sort');
    $router->get('uk-projects/{ukProjectId}/articles/create', [UkArticleController::class, 'create'])
        ->name('uk-projects.articles.create');
    $router->post('uk-projects/{ukProjectId}/articles', [UkArticleController::class, 'store'])
        ->name('uk-projects.articles.store');
    $router->get('uk-projects/{ukProjectId}/articles/{id}/edit', [UkArticleController::class, 'edit'])
        ->name('uk-projects.articles.edit');
    $router->put('uk-projects/{ukProjectId}/articles/{id}', [UkArticleController::class, 'update'])
        ->name('uk-projects.articles.update');
    $router->delete('uk-projects/{ukProjectId}/articles/{id}', [UkArticleController::class, 'destroy'])
        ->name('uk-projects.articles.destroy');
    $router->post('uk-projects/{ukProjectId}/articles/{id}/status', [UkArticleController::class, 'updateStatus'])
        ->name('uk-projects.articles.update-status');

    //uk project articles content items
    $router->put(
        'uk-projects/{ukProjectId}/articles/{id}/content-items/sort',
        [UkArticleContentItemController::class, 'sort']
    )->name('uk-projects.articles.content-items.sort');
    $router->post(
        'uk-projects/{ukProjectId}/articles/{id}/content-items/{type}',
        [UkArticleContentItemController::class, 'store']
    )->name('uk-projects.articles.content-items.store');
    $router->put(
        'uk-projects/{ukProjectId}/articles/{id}/content-items/{itemId}',
        [UkArticleContentItemController::class, 'update']
    )->name('uk-projects.articles.content-items.update');
    $router->delete(
        'uk-projects/{ukProjectId}/articles/{id}/content-items/{itemId}',
        [UkArticleContentItemController::class, 'destroy']
    )->name('uk-projects.articles.content-items.destroy');

    //uk project contacts
    $router->get('uk-projects/{ukProjectId}/contacts', [UkProjectContactController::class, 'index'])
        ->name('uk-projects.contacts');
    $router->put('uk-projects/{ukProjectId}/contacts/sort', [UkProjectContactController::class, 'sort'])
        ->name('uk-projects.contacts.sort');
    $router->get('uk-projects/{ukProjectId}/contacts/create', [UkProjectContactController::class, 'create'])
        ->name('uk-projects.contacts.create');
    $router->post('uk-projects/{ukProjectId}/contacts', [UkProjectContactController::class, 'store'])
        ->name('uk-projects.contacts.store');
    $router->get('uk-projects/{ukProjectId}/contacts/{id}/edit', [UkProjectContactController::class, 'edit'])
        ->name('uk-projects.contacts.edit');
    $router->put('uk-projects/{ukProjectId}/contacts/{id}', [UkProjectContactController::class, 'update'])
        ->name('uk-projects.contacts.update');
    $router->delete('uk-projects/{ukProjectId}/contacts/{id}', [UkProjectContactController::class, 'destroy'])
        ->name('uk-projects.contacts.destroy');

    //reality types
    $router->get('reality-types', [RealityTypesController::class, 'index'])
        ->name('reality-types');
    $router->get('reality-types/create', [RealityTypesController::class, 'create'])
        ->name('reality-types.create');
    $router->post('reality-types', [RealityTypesController::class, 'store'])
        ->name('reality-types.store');
    $router->get('reality-types/{id}/edit', [RealityTypesController::class, 'edit'])
        ->name('reality-types.edit');
    $router->put('reality-types/{id}', [RealityTypesController::class, 'update'])
        ->name('reality-types.update');
    $router->delete('reality-types/{id}', [RealityTypesController::class, 'destroy'])
        ->name('reality-types.destroy');

    //grouping reality types
    $router->get('grouping-reality-types', [GroupingRealityTypesController::class, 'index'])
        ->name('grouping-reality-types');
    $router->get('grouping-reality-types/create', [GroupingRealityTypesController::class, 'create'])
        ->name('grouping-reality-types.create');
    $router->post('grouping-reality-types', [GroupingRealityTypesController::class, 'store'])
        ->name('grouping-reality-types.store');
    $router->get('grouping-reality-types/{id}/edit', [GroupingRealityTypesController::class, 'edit'])
        ->name('grouping-reality-types.edit');
    $router->put('grouping-reality-types/{id}', [GroupingRealityTypesController::class, 'update'])
        ->name('grouping-reality-types.update');
    $router->delete('grouping-reality-types/{id}', [GroupingRealityTypesController::class, 'destroy'])
        ->name('grouping-reality-types.destroy');

    //client role types
    $router->get('client-role-types', [ClientRoleTypesController::class, 'index'])
        ->name('client-role-types');
    $router->get('client-role-types/create', [ClientRoleTypesController::class, 'create'])
        ->name('client-role-types.create');
    $router->post('client-role-types', [ClientRoleTypesController::class, 'store'])
        ->name('client-role-types.store');
    $router->get('client-role-types/{id}/edit', [ClientRoleTypesController::class, 'edit'])
        ->name('client-role-types.edit');
    $router->put('client-role-types/{id}', [ClientRoleTypesController::class, 'update'])
        ->name('client-role-types.update');
    $router->delete('client-role-types/{id}', [ClientRoleTypesController::class, 'destroy'])
        ->name('client-role-types.destroy');

    //finishings
    $router->get('finishings', [FinishingController::class, 'index'])
        ->name('finishings');
    $router->get('finishings/create', [FinishingController::class, 'create'])
        ->name('finishings.create');
    $router->post('finishings', [FinishingController::class, 'store'])
        ->name('finishings.store');
    $router->get('finishings/{id}/edit', [FinishingController::class, 'edit'])
        ->name('finishings.edit');
    $router->put('finishings/{id}', [FinishingController::class, 'update'])
        ->name('finishings.update');
    $router->post('finishings/{id}/status', [FinishingController::class, 'updateStatus'])
        ->name('finishings.update-status');
    $router->delete('finishings/{id}', [FinishingController::class, 'destroy'])
        ->name('finishings.destroy');

    //banks
    $router->get('banks', [BanksController::class, 'index'])
        ->name('banks');
    $router->get('banks/create', [BanksController::class, 'create'])
        ->name('banks.create');
    $router->post('banks', [BanksController::class, 'store'])
        ->name('banks.store');
    $router->get('banks/{id}/edit', [BanksController::class, 'edit'])
        ->name('banks.edit');
    $router->put('banks/{id}', [BanksController::class, 'update'])
        ->name('banks.update');
    $router->delete('banks/{id}', [BanksController::class, 'destroy'])
        ->name('banks.destroy');

    //escrow-banks
    $router->get('escrow-banks', [EscrowBanksController::class, 'index'])
        ->name('escrow-banks');
    $router->get('escrow-banks/create', [EscrowBanksController::class, 'create'])
        ->name('escrow-banks.create');
    $router->post('escrow-banks', [EscrowBanksController::class, 'store'])
        ->name('escrow-banks.store');
    $router->get('escrow-banks/{id}/edit', [EscrowBanksController::class, 'edit'])
        ->name('escrow-banks.edit');
    $router->put('escrow-banks/{id}', [EscrowBanksController::class, 'update'])
        ->name('escrow-banks.update');
    $router->delete('escrow-banks/{id}', [EscrowBanksController::class, 'destroy'])
        ->name('escrow-banks.destroy');

    //documents-name
    $router->get('documents-name', [DocumentsNameController::class, 'index'])
        ->name('documents-name');
    $router->get('documents-name/create', [DocumentsNameController::class, 'create'])
        ->name('documents-name.create');
    $router->post('documents-name', [DocumentsNameController::class, 'store'])
        ->name('documents-name.store');
    $router->get('documents-name/{id}/edit', [DocumentsNameController::class, 'edit'])
        ->name('documents-name.edit');
    $router->put('documents-name/{id}', [DocumentsNameController::class, 'update'])
        ->name('documents-name.update');
    $router->delete('documents-name/{id}', [DocumentsNameController::class, 'destroy'])
        ->name('documents-name.destroy');

    //sign-documents
    $router->get('sign-documents', [SignDocumentsController::class, 'index'])
        ->name('sign-documents');
    $router->get('sign-documents/create', [SignDocumentsController::class, 'create'])
        ->name('sign-documents.create');
    $router->post('sign-documents', [SignDocumentsController::class, 'store'])
        ->name('sign-documents.store');
    $router->get('sign-documents/{id}/edit', [SignDocumentsController::class, 'edit'])
        ->name('sign-documents.edit');
    $router->put('sign-documents/{id}', [SignDocumentsController::class, 'update'])
        ->name('sign-documents.update');
    $router->delete('sign-documents/{id}', [SignDocumentsController::class, 'destroy'])
        ->name('sign-documents.destroy');

    //support-topics
    $router->get('support-topics', [SupportTopicsController::class, 'index'])
        ->name('support-topics');
    $router->get('support-topics/create', [SupportTopicsController::class, 'create'])
        ->name('support-topics.create');
    $router->post('support-topics', [SupportTopicsController::class, 'store'])
        ->name('support-topics.store');
    $router->get('support-topics/{id}/edit', [SupportTopicsController::class, 'edit'])
        ->name('support-topics.edit');
    $router->put('support-topics/{id}', [SupportTopicsController::class, 'update'])
        ->name('support-topics.update');
    $router->post('support-topics/{id}/status', [SupportTopicsController::class, 'updateStatus'])
        ->name('support-topics.update-status');
    $router->delete('support-topics/{id}', [SupportTopicsController::class, 'destroy'])
        ->name('support-topics.destroy');
});

$router->group(['middleware' => ['role:admin|marketing']], function (Router $router) {
    //project types
    $router->get('project-types', [ProjectTypeController::class, 'index'])
        ->name('project-types');
    $router->put('project-types/sort', [ProjectTypeController::class, 'sort'])
        ->name('project-types.sort');
    $router->get('project-types/create', [ProjectTypeController::class, 'create'])
        ->name('project-types.create');
    $router->post('project-types', [ProjectTypeController::class, 'store'])
        ->name('project-types.store');
    $router->get('project-types/{id}/edit', [ProjectTypeController::class, 'edit'])
        ->name('project-types.edit');
    $router->put('project-types/{id}', [ProjectTypeController::class, 'update'])
        ->name('project-types.update');
    $router->delete('project-types/{id}', [ProjectTypeController::class, 'destroy'])
        ->name('project-types.destroy');

    //projects
    $router->get('projects/{projectType}', [ProjectController::class, 'index'])
        ->name('projects');
    $router->put('projects/{projectType}/sort', [ProjectController::class, 'sort'])
        ->name('projects.sort');
    $router->get('projects/{projectType}/create', [ProjectController::class, 'create'])
        ->name('projects.create');
    $router->post('projects/{projectType}', [ProjectController::class, 'store'])
        ->name('projects.store');
    $router->get('projects/{projectType}/{id}/edit', [ProjectController::class, 'edit'])
        ->name('projects.edit');
    $router->put('projects/{projectType}/{id}', [ProjectController::class, 'update'])
        ->name('projects.update');
    $router->delete('projects/{projectType}/{id}', [ProjectController::class, 'destroy'])
        ->name('projects.destroy');
    $router->post('projects/{projectType}/{id}/status', [ProjectController::class, 'updateStatus'])
        ->name('projects.update-status');

    //project articles
    $router->get('projects/{projectType}/{projectId}/articles', [ArticleController::class, 'index'])
        ->name('projects.articles');
    $router->put('projects/{projectType}/{projectId}/articles/sort', [ArticleController::class, 'sort'])
        ->name('projects.articles.sort');
    $router->get('projects/{projectType}/{projectId}/articles/create', [ArticleController::class, 'create'])
        ->name('projects.articles.create');
    $router->post('projects/{projectType}/{projectId}/articles', [ArticleController::class, 'store'])
        ->name('projects.articles.store');
    $router->get('projects/{projectType}/{projectId}/articles/{id}/edit', [ArticleController::class, 'edit'])
        ->name('projects.articles.edit');
    $router->put('projects/{projectType}/{projectId}/articles/{id}', [ArticleController::class, 'update'])
        ->name('projects.articles.update');
    $router->delete('projects/{projectType}/{projectId}/articles/{id}', [ArticleController::class, 'destroy'])
        ->name('projects.articles.destroy');
    $router
        ->post('projects/{projectType}/{projectId}/articles/{id}/status', [ArticleController::class, 'updateStatus'])
        ->name('projects.articles.update-status');

    //project mortgage programs
    $router->get(
        'projects/{projectType}/{projectId}/mortgage-programs',
        [MortgageProgramController::class, 'index']
    )->name('projects.mortgage-programs');
    $router->put(
        'projects/{projectType}/{projectId}/mortgage-programs/sort',
        [MortgageProgramController::class, 'sort']
    )->name('projects.mortgage-programs.sort');
    $router->get(
        'projects/{projectType}/{projectId}/mortgage-programs/create',
        [MortgageProgramController::class, 'create']
    )->name('projects.mortgage-programs.create');
    $router->post(
        'projects/{projectType}/{projectId}/mortgage-programs',
        [MortgageProgramController::class, 'store']
    )->name('projects.mortgage-programs.store');
    $router->get(
        'projects/{projectType}/{projectId}/mortgage-programs/{id}/edit',
        [MortgageProgramController::class, 'edit']
    )->name('projects.mortgage-programs.edit');
    $router->put(
        'projects/{projectType}/{projectId}/mortgage-programs/{id}',
        [MortgageProgramController::class, 'update']
    )->name('projects.mortgage-programs.update');
    $router->delete(
        'projects/{projectType}/{projectId}/mortgage-programs/{id}',
        [MortgageProgramController::class, 'destroy']
    )->name('projects.mortgage-programs.destroy');

    //project articles content items
    $router->put(
        'projects/{projectType}/{projectId}/articles/{id}/content-items/sort',
        [ArticleContentItemController::class, 'sort']
    )->name('projects.articles.content-items.sort');
    $router->post(
        'projects/{projectType}/{projectId}/articles/{id}/content-items/{type}',
        [ArticleContentItemController::class, 'store']
    )->name('projects.articles.content-items.store');
    $router->put(
        'projects/{projectType}/{projectId}/articles/{id}/content-items/{itemId}',
        [ArticleContentItemController::class, 'update']
    )->name('projects.articles.content-items.update');
    $router->delete(
        'projects/{projectType}/{projectId}/articles/{id}/content-items/{itemId}',
        [ArticleContentItemController::class, 'destroy']
    )->name('projects.articles.content-items.destroy');

    //instructions
    $router->get('instructions', [InstructionController::class, 'index'])
        ->name('instructions');
    $router->put('instructions/sort', [InstructionController::class, 'sort'])
        ->name('instructions.sort');
    $router->get('instructions/create', [InstructionController::class, 'create'])
        ->name('instructions.create');
    $router->post('instructions', [InstructionController::class, 'store'])
        ->name('instructions.store');
    $router->get('instructions/{id}/edit', [InstructionController::class, 'edit'])
        ->name('instructions.edit');
    $router->put('instructions/{id}', [InstructionController::class, 'update'])
        ->name('instructions.update');
    $router->delete('instructions/{id}', [InstructionController::class, 'destroy'])
        ->name('instructions.destroy');
    $router->post('instructions/{id}/status', [InstructionController::class, 'updateStatus'])
        ->name('instructions.update-status');

    //bank infos
    $router->get('bank-info', [BankInfoController::class, 'index'])
        ->name('bank-info');
    $router->put('bank-info/sort', [BankInfoController::class, 'sort'])
        ->name('bank-info.sort');
    $router->get('bank-info/create', [BankInfoController::class, 'create'])
        ->name('bank-info.create');
    $router->post('bank-info', [BankInfoController::class, 'store'])
        ->name('bank-info.store');
    $router->get('bank-info/{id}/edit', [BankInfoController::class, 'edit'])
        ->name('bank-info.edit');
    $router->put('bank-info/{id}', [BankInfoController::class, 'update'])
        ->name('bank-info.update');
    $router->delete('bank-info/{id}', [BankInfoController::class, 'destroy'])
        ->name('bank-info.destroy');
    $router->post('bank-info/{id}/status', [BankInfoController::class, 'updateStatus'])
        ->name('bank-info.update-status');

    //notifications
    $router->get('notifications', [NotificationController::class, 'index'])
        ->name('notifications');
    $router->get('notifications/create', [NotificationController::class, 'create'])
        ->name('notifications.create');
    $router->post('notifications/store', [NotificationController::class, 'store'])
        ->name('notifications.store');
    $router->get('notifications/{id}/show', [NotificationController::class, 'show'])
        ->name('notifications.show');
});

$router->group(['middleware' => ['role:admin']], function (Router $router) {
    //users
    $router->get('users', [UserController::class, 'index'])->name('users');
    $router->get('users/export', [UserController::class, 'export'])->name('users.export');
    $router->get('users/create', [UserController::class, 'create'])->name('users.create');
    $router->post('users', [UserController::class, 'store'])->name('users.store');
    $router->get('users/{id}/edit', [UserController::class, 'edit'])->name('users.edit');
    $router->post('users/{id}/unlock', [UserController::class, 'unlock'])->name('users.unlock');
    $router->put('users/{id}', [UserController::class, 'update'])->name('users.update');

    //blocks
    $router->get('blocking', [BlockingController::class, 'index'])->name('blocking');
    $router->delete('blocking/{id}', [BlockingController::class, 'destroy'])->name('blocking.destroy');
    $router->get('blocking/{id}/unlock', [BlockingController::class, 'edit'])->name('blocking.edi');
    $router->post('blocking/{id}/unlock', [BlockingController::class, 'destroy'])->name('blocking.edit');

    //settings: general
    $router->get('settings', [SettingsController::class, 'edit'])->name('settings.edit');
    $router->put('settings', [SettingsController::class, 'update'])->name('settings.update');

    //settings: about content items
    $router->put('settings/about-company/content-items/sort', [SettingsContentItemController::class, 'sort'])
        ->name('settings.content-items.sort');
    $router->post('settings/about-company/content-items/{type}', [SettingsContentItemController::class, 'store'])
        ->name('settings.content-items.store');
    $router->put('settings/about-company/content-items/{itemId}', [SettingsContentItemController::class, 'update'])
        ->name('settings.content-items.update');
    $router
        ->delete('settings/about-company/content-items/{itemId}', [SettingsContentItemController::class, 'destroy'])
        ->name('settings.content-items.destroy');

    //settings: contacts
    $router->put('settings/contacts/sort', [SettingsContactController::class, 'sort'])
        ->name('settings.contacts.sort');
    $router->post('settings/contacts', [SettingsContactController::class, 'store'])
        ->name('settings.contacts.store');
    $router->put('settings/contacts/{id}', [SettingsContactController::class, 'update'])
        ->name('settings.contacts.update');
    $router->delete('settings/contacts/{id}', [SettingsContactController::class, 'destroy'])
        ->name('settings.contacts.destroy');

    //settings: cache
    $router->post(
        'settings/cache/reload-catalogue-tree',
        [SettingsCacheController::class, 'reloadCatalogueTreeCache']
    )->name('settings.cache.reload-catalogue-tree');

    //settings: services
    $router->put('settings/services', [SettingsServicesController::class, 'update'])
        ->name('settings.services.update');

    //settings: documents
    $router->put('settings/documents', [SettingsDocumentsController::class, 'update'])
        ->name('settings.documents.update');

    //settings: builds
    $router->put('settings/builds', [SettingsBuildsController::class, 'update'])
        ->name('settings.builds.update');

    //settings: deleting reasons
    $router->post('settings/deleting-reasons', [SettingsDeletingReasonController::class, 'store'])
        ->name('settings.deleting-reasons.store');
    $router->put('settings/deleting-reasons/{id}', [SettingsDeletingReasonController::class, 'update'])
        ->name('settings.deleting-reasons.update');
    $router->delete('settings/deleting-reasons/{id}', [SettingsDeletingReasonController::class, 'destroy'])
        ->name('settings.deleting-reasons.destroy');
});
