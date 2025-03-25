<?php

use App\Http\Api\External\V1\Controllers\AccountController;
use App\Http\Api\External\V1\Controllers\AccountDocumentController;
use App\Http\Api\External\V1\Controllers\AccountThemesController;
use App\Http\Api\External\V1\Controllers\AdController;
use App\Http\Api\External\V1\Controllers\ArticleController;
use App\Http\Api\External\V1\Controllers\Auth\AuthController;
use App\Http\Api\External\V1\Controllers\BannerController;
use App\Http\Api\External\V1\Controllers\CityController;
use App\Http\Api\External\V1\Controllers\ClaimController;
use App\Http\Api\External\V1\Controllers\ClaimMessageController;
use App\Http\Api\External\V1\Controllers\ContactController;
use App\Http\Api\External\V1\Controllers\Deals\DealsController;
use App\Http\Api\External\V1\Controllers\FeedbackController;
use App\Http\Api\External\V1\Controllers\IndividualOwner\IndividualOwnerController;
use App\Http\Api\External\V1\Controllers\InstructionController;
use App\Http\Api\External\V1\Controllers\LegalPersonController;
use App\Http\Api\External\V1\Controllers\ManagerController;
use App\Http\Api\External\V1\Controllers\MeterController;
use App\Http\Api\External\V1\Controllers\MeterStatisticsController;
use App\Http\Api\External\V1\Controllers\MeterTariffController;
use App\Http\Api\External\V1\Controllers\MetricController;
use App\Http\Api\External\V1\Controllers\MortgageController;
use App\Http\Api\External\V1\Controllers\NewsController;
use App\Http\Api\External\V1\Controllers\NotificationController;
use App\Http\Api\External\V1\Controllers\PassController;
use App\Http\Api\External\V1\Controllers\Payment\PSBPaymentController;
use App\Http\Api\External\V1\Controllers\Payment\SBPPaymentController;
use App\Http\Api\External\V1\Controllers\PaymentController;
use App\Http\Api\External\V1\Controllers\ProjectController;
use App\Http\Api\External\V1\Controllers\PropertyController;
use App\Http\Api\External\V1\Controllers\ReceiptController;
use App\Http\Api\External\V1\Controllers\RelationshipInviteController;
use App\Http\Api\External\V1\Controllers\Sales\BankController;
use App\Http\Api\External\V1\Controllers\Sales\ContractController;
use App\Http\Api\External\V1\Controllers\Sales\DemandController;
use App\Http\Api\External\V1\Controllers\Sales\FileController;
use App\Http\Api\External\V1\Controllers\Sales\InspectionController;
use App\Http\Api\External\V1\Controllers\Sales\JointOwnerController;
use App\Http\Api\External\V1\Controllers\Sales\MortgageController as SalesMortgageController;
use App\Http\Api\External\V1\Controllers\Sales\PaymentController as SalesPaymentController;
use App\Http\Api\External\V1\Controllers\SettingsController;
use App\Http\Api\External\V1\Controllers\TransactionLogController;
use App\Http\Api\External\V1\Controllers\UkProjectController;
use App\Http\Api\External\V1\Controllers\UserController;
use App\Http\Api\External\V1\Controllers\UserDocumentController;
use App\Http\Api\External\V1\Controllers\VerificationCodeController;
use App\Models\Ad\AdPlace;
use App\Models\Banner\BannerPlace;
use App\Models\User\NotificationChannel;
use Illuminate\Routing\Router;

/** @var Router $router */
// phpcs:disable

$router->post('/');
$router->get('/ping', [MetricController::class, 'ping']);
//auth
$router->post('verification-code/send', [VerificationCodeController::class, 'send']);
$router->post('auth/register', [AuthController::class, 'register'])->middleware('block.free.phone');
$router->post('auth/login-by-code', [AuthController::class, 'loginByVerificationCode']);
$router->post('auth/login-by-password', [AuthController::class, 'loginByPassword']);
$router->post('auth/set-password', [AuthController::class, 'setPassword']);
$router->put('auth/reset-password', [AuthController::class, 'resetPassword']);
$router->post('auth/password-reset/verify-code', [AuthController::class, 'verifyCodeToResetPassword']);
$router->put('auth/change-password', [AuthController::class, 'changePassword']);
$router->delete('auth/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
$router->post('auth/refresh', [AuthController::class, 'refresh'])->middleware('auth:sanctum');

//news
$router->get('news', [NewsController::class, 'index']);
$router->get('news/{id}', [NewsController::class, 'show']);
$router->get('news-category', [NewsController::class, 'category']);

//settings
$router->get('settings/general', [SettingsController::class, 'showGeneral']);
$router->get('settings/about-company', [SettingsController::class, 'showAboutCompany']);

//instructions
$router->get('instructions', [InstructionController::class, 'index']);

//projects
$router->get('projects', [ProjectController::class, 'index']);
$router->get('projects/{id}', [ProjectController::class, 'show']);
$router->get('projects/{projectId}/articles', [ArticleController::class, 'index']);
$router->get('projects/{projectId}/articles/{id}', [ArticleController::class, 'show']);
$router->get('projects/{projectId}/loan-offers', [MortgageController::class, 'getLoanOffers']);

//claims
$router->get('claims/catalogue', [ClaimController::class, 'getCatalogue']);
$router->get('claims/catalogue/search', [ClaimController::class, 'getCatalogueSearch']);
$router->get('claims/catalogue/{id}', [ClaimController::class, 'getCatalogueItem']);
$router->get('claims/popular-services', [ClaimController::class, 'getPopularServices']);

//cities
$router->get('/cities', [CityController::class, 'index']);

//payments
$router->get('payments/callback', [PaymentController::class, 'callback']);
$router->post('accounts/{accountNumber}/psb/callback', [PSBPaymentController::class, 'callback']);
$router->post('accounts/{accountNumber}/sbp/callback', [SBPPaymentController::class, 'callback']);


//notifications
$router->get('notifications', [NotificationController::class, 'index']);// User not found

//feedback appeal
$router->post('feedback-appeal', [FeedbackController::class, 'sendAppeal']);

$router->post('/manager-contacts', [ManagerController::class, 'show']);

$router->get('offers/{place}', [BannerController::class, 'show'])->where([
    'place' => BannerPlace::getAllowedValuesRegex(),
]);

//user
$router->get('user/deleting-reasons', [UserController::class, 'getDeletingReasons']);
$router->group(['middleware' => 'auth:sanctum'], function (Router $router) {
    $router->get('user', [UserController::class, 'view']);
    $router->put('user', [UserController::class, 'update']);
    $router->delete('user', [UserController::class, 'delete']);
    $router->put('user/push-token', [UserController::class, 'updatePushToken']);
    $router->post('user/notifications/{channel}/toggle', [UserController::class, 'toggleNotification'])->where([
        'channel' => NotificationChannel::getAllowedValuesRegex(),
    ]);
    $router->get('user-full-info', [UserController::class, 'fullInfo']);
    $router->post('user/notifications', [NotificationController::class, 'view']);
    $router->get('user/notifications/state', [NotificationController::class, 'getState']);
    $router->post('user/notification/{id}', [NotificationController::class, 'read']);
    $router->get('user/documents', [UserDocumentController::class, 'index']);
    $router->post('user/documents', [UserDocumentController::class, 'upload']);
    $router->get('user/documents/{id}', [UserDocumentController::class, 'download']);
    $router->delete('user/documents/{id}', [UserDocumentController::class, 'delete']);
    $router->get('user/favorite-properties', [PropertyController::class, 'getFavorites']);// Property url
    $router->post('user/favorite-properties/{id}', [PropertyController::class, 'addToFavorites']);
    $router->delete('user/favorite-properties/{id}', [PropertyController::class, 'removeFromFavorites']);
    $router->get('user/archive-contracts', [ContractController::class, 'getUserArchiveContracts']);
    $router->get('user/archive-contracts/{id}', [ContractController::class, 'getArchiveContract']);
    $router->get('user/archive-contracts/{id}/final-contract', [ContractController::class, 'getUserArchiveFinalContract']);
    $router->get('user/sign-info', [ContractController::class, 'getUserSignInfo']);
    $router->get('user/archive-contracts/{contractId}/jointowners/{jointOwnersId}/documents', [ContractController::class, 'getArchiveDocuments']);
    $router->get('user/archive-contracts/{id}/general-contract-documents', [ContractController::class, 'getArchiveGeneralDocuments']);
    $router->get('jointowners/{id}/documents', [ContractController::class, 'getJointOwnerDocuments']);
    $router->post('order-callback', [FeedbackController::class, 'orderCallback']);


    $router->get('feedback-history', [FeedbackController::class, 'getFeedbackHistory']);

    //announcements
    $router->get('announcements/{place}', [AdController::class, 'show'])->where([
        'place' => AdPlace::getAllowedValuesRegex(),
    ]);

    //accounts
    $router->get('accounts', [AccountController::class, 'index']);// User
    $router->get('accounts/uk-projects', [UkProjectController::class, 'index']);// User
    $router->get('accounts/{accountNumber}', [AccountController::class, 'show']);// User

    //account claims
    $router->get('claims/{accountNumber}/catalogue', [ClaimController::class, 'getAccountCatalogue']);
    $router->get('claims/catalogue/{accountNumber}/search', [ClaimController::class, 'getAccountCatalogueSearch']);
    $router->get('claims/{accountNumber}/catalogue/{id}', [ClaimController::class, 'getAccountCatalogueItem']);
    $router->get('claims/{accountNumber}/popular-services', [ClaimController::class, 'getAccountPopularServices']);

    //payments
    $router->post('accounts/{accountNumber}/payments', [PaymentController::class, 'payByCard']);
    $router->post('accounts/{accountNumber}/psb-payments', [PSBPaymentController::class, 'payByCard']);
    $router->post('accounts/{accountNumber}/psb-sbp-payments', [SBPPaymentController::class, 'payByCard']);
//TODO: not in MVP
//    $router->post('accounts/{accountNumber}/payments/apple-pay', [PaymentController::class, 'payByApplePay']);
//    $router->post(
//        'accounts/{accountNumber}/payments/apple-pay-validate',
//        [PaymentController::class, 'validateApplePay']
//    );

    //account additional info
    $router->get('accounts/{accountNumber}/additional-info', [AccountController::class, 'showAdditionalInfo']);
    $router->get(
        'accounts/{accountNumber}/additional-info/articles/{articleId}',
        [AccountController::class, 'showAdditionalInfoArticle']
    );

    //account contacts
    $router->get('accounts/{accountNumber}/contacts', [ContactController::class, 'index']);

    //documents
    $router->get('accounts/{accountNumber}/documents', [AccountDocumentController::class, 'index']);

    //file
    $router->get('/file/{fileId}', [FileController::class, 'show']);
    $router->post('/file', [FileController::class, 'store']);

    //получения списка доступных типов услуг по лицевому счету
    $router->get('accounts/{accountNumber}/claims_themes', [AccountThemesController::class, 'index']);

    //relationships
    $router->get('accounts/{accountNumber}/relationship-invites', [RelationshipInviteController::class, 'index']);
    $router->post('accounts/{accountNumber}/relationship-invites', [RelationshipInviteController::class, 'store']);
    $router->delete(
        'accounts/{accountNumber}/relationship-invites/{id}',
        [RelationshipInviteController::class, 'destroy']
    );
    $router->get('relationship-invites-description', [RelationshipInviteController::class, 'description']);


    //receipts
    $router->get('accounts/{accountNumber}/receipts', [ReceiptController::class, 'index']);
    $router->get('accounts/{accountNumber}/receipts/pdf', [ReceiptController::class, 'getPdf']);

    //meters
    $router->get('accounts/{accountNumber}/meters', [MeterController::class, 'index']);
    $router->post('accounts/{accountNumber}/meters', [MeterController::class, 'save']);
    $router->put('accounts/{accountNumber}/meters/{meterId}/name', [MeterController::class, 'saveName']);
    $router->get('accounts/{accountNumber}/meters/tariffs', [MeterTariffController::class, 'index']);
    $router->get('accounts/{accountNumber}/meters/statistics', [MeterStatisticsController::class, 'index']);
    $router->get('accounts/{account_number}/meters-statistics-check', [MeterStatisticsController::class, 'check']);
    $router->get('accounts/{account_number}/meters/statistics-type', [MeterStatisticsController::class, 'getStatisticType']);

    //transaction logs
    $router->get('accounts/{accountNumber}/transaction-logs', [TransactionLogController::class, 'index']);

    //claims
    $router->get('claims/last-created', [ClaimController::class, 'getLastClaim']);
    $router->get('claims/image', [ClaimController::class, 'getImage']);
    $router->get('accounts/{accountNumber}/claims', [ClaimController::class, 'index']);
    $router->get('claims/{id}', [ClaimController::class, 'show']);
//    $router->put('claims/{id}/reopen', [ClaimController::class, 'reopen']);
    $router->put('claims/{id}/accept', [ClaimController::class, 'accept']);
    $router->put('claims/{id}/rate', [ClaimController::class, 'rate']);
    $router->put('claims/{id}/comment', [ClaimController::class, 'comment']);
    $router->put('claims/{id}/cancel', [ClaimController::class, 'cancel']);
    $router->post('claims/{id}/decline', [ClaimController::class, 'decline']);
    $router->get('claims/{claim_id}/attachments', [ClaimController::class, 'attachment']);
    $router->post('claims/{claim_id}/attachments', [ClaimController::class, 'saveAttachment']);
    $router->get('claims/{claim_id}/attachments/{document_id}', [ClaimController::class, 'getAttachmentById']);

    //claims create
    $router->post('accounts/{accountNumber}/claims/pass', [ClaimController::class, 'storePass']);
    $router->post('accounts/{accountNumber}/claims/sos', [ClaimController::class, 'storeSos']);
    $router->post('accounts/{accountNumber}/claims/request', [ClaimController::class, 'storeRequest']);
    $router->post('accounts/{accountNumber}/claims/create', [ClaimController::class, 'storeCreate']);
    $router->post('accounts/{accountNumber}/claims/appeal', [ClaimController::class, 'storeAppeal']);
    $router->post('accounts/{accountNumber}/claims/visit', [ClaimController::class, 'storeVisit']);
    $router->post('accounts/{accountNumber}/claims/warranty', [ClaimController::class, 'storeWarranty']);
    $router->post('accounts/{accountNumber}/claims/marketplace', [ClaimController::class, 'storeMarketplace']);

    //legal person
    $router->post('accounts/{id}', [LegalPersonController::class, 'update']);
    $router->post('demands/{demandId}/add-account', [LegalPersonController::class, 'addAccount']);
    $router->get('accounts/{inn}/check', [LegalPersonController::class, 'checkInn']);



    //contracts
    $router->get('contracts/sign-info', [ContractController::class, 'getSignInfo']);
    $router->get('contracts/{id}', [ContractController::class, 'show']);
    $router->put('contracts/{contractId}/set-courier-address', [ContractController::class, 'setCourierAddress']);
    $router->post('contracts/send-sms-code/{type}', [ContractController::class, 'sendSmsCode']);
    $router->get('contracts/{id}/payments', [ContractController::class, 'getPayments']);
    $router->get('contracts/{id}/payment_plan', [ContractController::class, 'getPaymentPlan']);
    $router->post('demand-summary', [ContractController::class, 'getDemandSummary']);
    $router->get('contracts/{id}/hypothec-sup', [ContractController::class, 'getHypothecSup']);
    $router->get('contracts/{id}/uk-documents', [ContractController::class, 'getUkDocuments']);
    $router->get('contracts/{id}/all-version-contracts', [ContractController::class, 'getAllVersion']);
    $router->get('contracts/{id}/general-contract-documents', [ContractController::class, 'getGeneralDocuments']);
    $router->get('contracts/{contractId}/jointowners/{jointOwnersId}/documents', [ContractController::class, 'getDocuments']);
    $router->get('contracts/{id}/sign-registration-info', [ContractController::class, 'getSignRegistrationInfo']);
    $router->get('contracts/{id}/jointowners/sign-info', [ContractController::class, 'getJointOwnersSignInfo']);
    $router->get('contracts/{id}/jointowners', [ContractController::class, 'getJointOwners']);
    $router->get('contracts/{id}/jointowners-info', [ContractController::class, 'getJointOwnersInfo']);
    $router->post('contracts/{id}/additional-contracts', [ContractController::class, 'getAdditionalContracts']);
    $router->get('additional-contracts/{id}', [ContractController::class, 'getAdditionalContract']);
    $router->get('additional-contracts/{id}/sign-registration-info', [ContractController::class, 'getAddSignRegInfo']);
    $router->get('additional-contracts/{id}/draft', [ContractController::class, 'getAddDraft']);
    $router->get('additional-contracts/{id}/all-version-additional-contracts', [ContractController::class, 'getAllVersionAddDraft']);
    $router->POST('contracts/approve', [ContractController::class, 'getApprove']);
    $router->get('contracts/{id}/jointowners/{jointOwnersId}/confidant', [ContractController::class, 'getConfidant']);

    //inspection
    $router->get('inspections/{articleId}/date', [InspectionController::class, 'getInspectionDate']);
    $router->delete('inspections/{inspectionId}', [InspectionController::class, 'delInspection']);
    $router->get('inspections/{articleId}/date/{date}/times', [InspectionController::class, 'getDateTimes']);
    $router->get('inspections/{articleId}', [InspectionController::class, 'getInspection']);
    $router->post('inspections', [InspectionController::class, 'createInspection']);
    $router->post('inspections/{inspectionId}', [InspectionController::class, 'updateInspection']);
    $router->get('article/{id}/instruction', [InstructionController::class, 'getArticleInstruction']);


    //pass
    $router->get('/pass/{account_number}', [PassController::class, 'index']);
    $router->post('/pass/create/{account_number}', [PassController::class, 'store']);
    $router->put('/pass/{id}/cancel', [PassController::class, 'cancel']);

    //claim messages
    $router->get('claims/{id}/messages/state', [ClaimMessageController::class, 'getMessageState']);
    $router->get('claims/{id}/messages', [ClaimMessageController::class, 'getMessages']);
    $router->get('claims/{id}/messages/poll', [ClaimMessageController::class, 'getPoolMessages']);
    $router->post('claims/{id}/messages', [ClaimMessageController::class, 'sendMessage']);
    $router->post('claims/{id}/messages/read_communications', [ClaimMessageController::class, 'readCommunications']);

    //sales demands
    $router->get('demands', [DemandController::class, 'index']);
    $router->get('demands/{id}', [DemandController::class, 'show']);
    $router->post('demands', [DemandController::class, 'store']);
    $router->get('demands/{id}/steps', [DemandController::class, 'getSteps']);
    $router->post('demands/tradein-request', [DemandController::class, 'storeTradeIn']);
    $router->post('/demands/contract-draft-request', [DemandController::class, 'sendContractDraft']);
    $router->post('deponent', [DemandController::class, 'getDeponent']);
    $router->get('/demands/{demandId}/borrowers', [DemandController::class, 'getBorrowers']);
    $router->get('/demands/{demandId}/hypothec-info', [DemandController::class, 'getHypothecInfo']);
    $router->get('/demands/{id}/payment_plan', [DemandController::class, 'getPaymentPlan']);
    $router->patch('/demands/{id}/payment-by-default', [DemandController::class, 'setPaymentByDefault']);
    $router->patch('/demands/{demandId}/jointowners-by-default', [DemandController::class, 'setJointOwnersByDefault']);

    $router->get('/demands/{demandId}/finishing', [DealsController::class, 'index']);
    $router->patch('/demands/{demandId}/finishing/{finishingId}', [DealsController::class, 'addFinishVariant']);
    $router->patch('/demands/{demandId}/payment/{paymentType}', [DealsController::class, 'setPaymentType']);
    $router->patch('/demands/{demandId}/installment/{installmentId}', [DealsController::class, 'setInstallmet']);
    $router->patch('/demands/{demandId}/banks', [DealsController::class, 'setBankId']);
    $router->patch('/demands/{demandId}/depositor', [DealsController::class, 'setDepositor']);
    $router->put('/demands/{demandId}/cancel', [DealsController::class, 'cancelDemand']);

    //hypotec
    $router->post('demands/{demandId}/hypothec-demand', [DemandController::class, 'storeHypothec']);
    $router->get('demands/hypothec/{demandId}', [DemandController::class, 'getHypothec']);
    $router->get('demands/hypothec-approvals/{demandId}', [DemandController::class, 'getHypothecApprovals']);
    $router->get('demands/hypothec-bank/{approvalId}', [DemandController::class, 'getHypothecBankApprovals']);

    // phpcs:disable
    $router->get('/demands/{demandId}/jointowners', [JointOwnerController::class, 'index']);
    $router->post('/demands/{demandId}/jointowners', [JointOwnerController::class, 'store']);
    $router->post('/demands/{demandId}/jointowners/customers/{customerId}', [JointOwnerController::class, 'storeCustomer']);
    $router->get('/demands/{demandId}/jointowners/{jointownerId}', [JointOwnerController::class, 'show']);
    $router->patch('/demands/{demandId}/jointowners/{jointownerId}', [JointOwnerController::class, 'update']);
    $router->delete('/demands/{demandId}/jointowners/{jointownerId}', [JointOwnerController::class, 'destroy']);
    $router->post('/participants/{jointownerId}/demandid/{demandid}', [JointOwnerController::class, 'storeParticipants']);
    // phpcs:enable

    //sales payment
    $router->post('demands/{id}/pay-booking', [SalesPaymentController::class, 'payBooking']);

    //sales mortgage
    $router->post('demands/{id}/create-mortgage-demand', [SalesMortgageController::class, 'createMortgageDemand']);
    $router->get('demands/{id}/get-mortgage-url', [SalesMortgageController::class, 'getMortgageUrl']);
    $router->get('demands/{id}/get-mortgage-approvals', [SalesMortgageController::class, 'getMortgageApprovals']);

    //sales terms
    $router->get('demands/{id}/banks', [BankController::class, 'index']);
    $router->post('demands/{id}/set-terms', [DemandController::class, 'setTerms']);
    $router->put('demands/{id}/set-finishing', [DemandController::class, 'setFinishing']);

    //sales contract
    $router->put('demands/{id}/set-contract-read', [DemandController::class, 'setContractRead']);

    //sales meeting
    $router->put('demands/{id}/set-meeting', [DemandController::class, 'setMeeting']);

    $router->post('demands/{id}/confidant-edit', [DemandController::class, 'confidantEdit']);

    $router->post('jointowners/email-check', [JointOwnerController::class, 'emailCheck']);

    $router->get('individual-owner-info', [IndividualOwnerController::class, 'getInfo']);
    $router->post('individual-owner-edit', [IndividualOwnerController::class, 'setInfo']);
});
