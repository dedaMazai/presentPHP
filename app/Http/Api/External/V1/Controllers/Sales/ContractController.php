<?php

namespace App\Http\Api\External\V1\Controllers\Sales;

use App\Helpers\ExceptionHelper;
use App\Http\Api\External\V1\Controllers\Controller;
use App\Http\Api\External\V1\Requests\Contract\DemandSummaryRequest;
use App\Http\Api\External\V1\Requests\Contract\GetAdditionalRequest;
use App\Http\Api\External\V1\Requests\Contract\GetApproveRequest;
use App\Http\Api\External\V1\Requests\Request;
use App\Http\Api\External\V1\Requests\Sales\SendSmsCodeRequest;
use App\Http\Api\External\V1\Requests\Sales\SetCourierAddressRequest;
use App\Http\Resources\Contract\AdditionalCollection;
use App\Http\Resources\Contract\AllVersionContractCollection;
use App\Http\Resources\Contract\AllVersionContractResource;
use App\Http\Resources\Contract\ConfidantResource;
use App\Http\Resources\Contract\ContractVersionCollection;
use App\Http\Resources\Contract\DemandSummaryResource;
use App\Http\Resources\Contract\DetailAdditionalContractResource;
use App\Http\Resources\Contract\HypothecSupResource;
use App\Http\Resources\Contract\JointOwner\ContractJointOwnerResource;
use App\Http\Resources\Contract\JointOwner\SignInfoResource;
use App\Http\Resources\Contract\PaymentCollection;
use App\Http\Resources\Contract\PaymentPlanCollection;
use App\Http\Resources\Contract\SignApp\SignAppResource;
use App\Http\Resources\Sales\AddSignRegistrationInfoResource;
use App\Http\Resources\Sales\Contract\ArchiveContractDocumentResource;
use App\Http\Resources\Sales\Contract\ArchiveGeneralDocumentCollection;
use App\Http\Resources\Sales\Contract\ContractDocumentResource;
use App\Http\Resources\Sales\Contract\DetailContractResource;
use App\Http\Resources\Sales\Contract\DetailUserArchiveContractResource;
use App\Http\Resources\Sales\Contract\TemplateResource;
use App\Http\Resources\Sales\Contract\UserArchiveContractCollection;
use App\Http\Resources\Sales\GeneralContractDocumentInfoResource;
use App\Http\Resources\Sales\GeneralContractDocumentsCollection;
use App\Http\Resources\Sales\JointOwner\ContractJointOwnerCollection;
use App\Http\Resources\Sales\JointOwner\ContractJointOwnerInfoCollection;
use App\Http\Resources\Sales\SignRegistrationInfoResource;
use App\Http\Resources\User\UserSignInfoResource;
use App\Http\Resources\V2\DocumentUserResource;
use App\Models\Document\Document;
use App\Models\DocumentsName;
use App\Models\PaymentMethodType;
use App\Models\Project\Project;
use App\Models\Sales\Contract\ArchiveContracts;
use App\Models\Sales\Customer\Customer;
use App\Models\Sales\Demand\DemandStatus;
use App\Models\SignDocuments;
use App\Models\User\User;
use App\Models\User\UserSignInfo;
use App\Services\Contract\Dto\ContractJointOwnerDto;
use App\Services\Document\DocumentRepository;
use App\Services\DynamicsCrm\Exceptions\NotFoundException;
use App\Services\Payment\Dto\CreateBookingPaymentDto;
use App\Services\Payment\Exceptions\BadRequestException as PaymentBadRequestException;
use App\Services\Payment\PaymentService;
use App\Services\Sales\ContractService;
use App\Services\Sales\Customer\CustomerRepository;
use App\Services\Sales\Demand\DemandRepository;
use App\Services\Sales\Property\PropertyRepository;
use App\Services\V2\Contract\ContractRepository;
use App\Services\V2\User\UserService;
use Carbon\Carbon;
use Exception;
use Illuminate\Auth\AuthenticationException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use function PHPUnit\Framework\isEmpty;

/**
 * Class ContractController
 *
 * @package App\Http\Api\External\V1\Controllers\Sales
 */
class ContractController extends Controller
{
    public function __construct(
        private ContractService $contractService,
        private CustomerRepository $customerRepository,
        private ContractRepository $contractRepository,
        private PropertyRepository $propertyRepository,
        private DocumentRepository $documentRepository,
        private UserService $userService,
    ) {
    }

    public function show(string $id)
    {
        $contract = $this->contractService->findContract($id);

        return response()->json(new DetailContractResource($contract));
    }

    public function getArchiveContract(string $id)
    {
        $contract = $this->contractService->findContract($id);

        $jointOwners = collect($contract->getJointOwners())->filter(function ($jointOwner) {
            /** @var Customer $jointOwner */
            return $jointOwner->getRole()->value === '1';
        });

        $property = $this->propertyRepository->getById($contract->getArticleOrders()[0]->getPropertyId());

        return response()->json(new DetailUserArchiveContractResource(
            [
                'contract' => $contract,
                'jointOwners' => $jointOwners,
                'property' => $property,
            ]
        ));
    }

    public function getUserArchiveContracts()
    {
        $contracts = $this->contractRepository->getArchiveContracts($this->getAuthUser()->crm_id);
        $archiveContracts = [];

        $articleCodes = ['020050', '020011', '020080', '020040', '020010'];

        foreach ($contracts as $contract) {
            if (in_array(($contract['serviceMain']['code'] ?? null), $articleCodes)) {
                if (($contract['transferdeed'] ?? null) != null) {
                    // phpcs:disable
                    try {
                        $property = $this->propertyRepository->getByIdWithoutObject($contract['articleOrders'][0]['articleId']);
                    } catch (\Throwable $e) {
                        continue;
                    }

                    $nameLk = $this->getNameLk($property['articleTypeCode']['code'], $property['articleVariantTm1Code']['code']);
                    // phpcs:enable
                    $archiveContracts[] = new ArchiveContracts(
                        id: $contract['id'],
                        contractName: $contract['name'],
                        contractDate: Carbon::parse($contract['contractDate'])->format('d.m.Y'),
                        projectName: Project::whereJsonContains('crm_ids', $property['address']['gk']['id'])
                            ->first()->name,
                        nameLk: $nameLk,
                        number: $property['number']
                    );
                }
            }
        }
//        foreach ($contracts as $contract) {
//            if (in_array($contract?->getService()?->value, $articleCodes)) {
//                if ($contract?->getTransferDeedDate() != null) {
//                    try {
//                        $articleOrderId = $contract->getArticleOrders()[0]->getPropertyId();
//                        $property = $this->propertyRepository->getById($articleOrderId);
//                    } catch (\Throwable $e) {
//                        continue;
//                    }
//                    $nameLk = $this->getNameLk($property->getType()->value, $property->getVariant()->value);
//
//                    $archiveContracts[] = new ArchiveContracts(
//                        id: $contract->getId(),
//                        contractName: $contract->getName(),
//                        contractDate: $contract->getDate()->format('d.m.Y'),
//                        projectName: Project::whereJsonContains('crm_ids', $property->getAddress()->getGkId())
//                            ->first()->name,
//                        nameLk: $nameLk,
//                        number: $property->getNumber()
//                    );
//                }
//            }
//        }
        return response()->json(new UserArchiveContractCollection($archiveContracts));
    }

    public function getUserArchiveFinalContract(string $id)
    {
        $allVersion = $this->contractService->getFinalContracts($id);

        return response()->json(['contract_documents' => new ContractVersionCollection($allVersion)]);
    }

    /**
     * @param string $contractId
     * @param SetCourierAddressRequest $request
     * @return Response
     * @throws \App\Services\DynamicsCrm\Exceptions\BadRequestException
     * @throws \App\Services\DynamicsCrm\Exceptions\NotFoundException
     * @throws \Illuminate\Validation\ValidationException
     */
    public function setCourierAddress(string $contractId, SetCourierAddressRequest $request): Response
    {
        $this->contractService->setCourierAddress($contractId, $request);

        return $this->response();
    }


    public function sendSmsCode(string $type, SendSmsCodeRequest $request): Response
    {
        $this->contractService->sendCode($type, $this->getAuthUser()->crm_id, $request);

        return $this->response();
    }

    public function getPayments(string $id): Response
    {
        $payments = $this->contractService->getPayments($id);

        if (isset($payments['payment'])) {
            return response()->json(new PaymentCollection($payments['payment']));
        } else {
            return response()->json(([]));
        }
    }

    public function getPaymentPlan(string $id): Response
    {
        $paymentPlans = $this->contractService->getPaymentPlan($id);

        return response()->json(new PaymentPlanCollection($paymentPlans));
    }

    public function getAllVersion(string $id): Response
    {
        $allVersion = $this->contractService->getAllVersionContracts($id);

        return response()->json(['contract_documents' => new ContractVersionCollection($allVersion)]);
    }

    public function getGeneralDocuments(string $id): Response
    {
        $generalDocuments = $this->contractService->getGeneralContractDocuments($id);
        // phpcs:disable
        return response()->json(['general-contract-documents' => new GeneralContractDocumentsCollection($generalDocuments)]);
        // phpcs:enable
    }

    public function getDocuments(string $contractId, string $jointOwnersId): Response
    {
        $documents = $this->contractService->getDocuments($contractId, $jointOwnersId, $this->getAuthUser());

        return response()->json(new ContractDocumentResource($documents));
    }

    public function getArchiveDocuments(string $contractId, string $jointOwnersId): Response
    {
        $documents = $this->contractService->getDocuments($contractId, $jointOwnersId, $this->getAuthUser());

        return response()->json(new ArchiveContractDocumentResource($documents));
    }

    public function getArchiveGeneralDocuments(string $id): Response
    {
        $documents = $this->contractService->getArchiveGeneralDocuments($id);

        return response()->json(['general-contract-documents' => new ArchiveGeneralDocumentCollection($documents)]);
    }

    public function getJointOwnerDocuments(string $id): Response
    {
        return response()->json(new DocumentUserResource([
            'document' => $this->userService->getDocuments($this->getAuthUser()),
            'user' => $this->getAuthUser()
        ]));
    }

    public function getSignRegistrationInfo(string $id): Response
    {
        $contract = $this->contractRepository->getById($id);

        return response()->json(new SignRegistrationInfoResource($contract));
    }

    public function getAddSignRegInfo(string $id): Response
    {
        $contract = $this->contractRepository->getById($id);

        return response()->json(new AddSignRegistrationInfoResource($contract));
    }

    public function getAddDraft(string $id): Response
    {
        $documents = $this->contractRepository->getAddDraftById($id);

        return response()->json(
            ['additional_contract_draft' => isset($documents[0]) ? new TemplateResource($documents[0]) : []]
        );
    }

    public function getAllVersionAddDraft(string $id): Response
    {
        $documents = $this->contractService->getAllVersionAdditionalContracts($id);

        // phpcs:disable
        return response()->json($documents != [] ? new AllVersionContractResource($documents) : ['additional_contract_documents' => []]);
        // phpcs:enable
    }

    public function getUserSignInfo(): Response
    {
        $user = $this->getAuthUser();
        $fullName = $user->getFullName();
        $customer = $this->customerRepository->getById($user->crm_id);

        $reissueGuideUrl = SignDocuments::where('code', '=', 'reissue_guide')->get()?->toArray();

        $documents = $this->documentRepository->getDocumentsByUserWithTypeCode($user, [65538]);

        if ($documents != null && count($documents) != 0) {
            $document = $documents[array_key_first($documents)];
        } else {
            $document = null;
        }

        $signInfo = new UserSignInfo(
            fullName: $fullName,
            reissueGuideUrl: $reissueGuideUrl != [] ?? '',
            signInfo: $customer,
            signStatementDocument: $document
        );

        return response()->json(new UserSignInfoResource($signInfo));
    }

    public function getSignInfo(): Response
    {
        $signAppUrl = [
            'androidUrl' => config('sign_app.android_url') ?? 'https://play.google.com/store/apps/details?id=me.sign',
            'iosUrl' => config('sign_app.ios_url') ?? 'https://apps.apple.com/ru/app/sign-me/id1502259352',
        ];

        $signAppManual = SignDocuments::all()->toArray();


        return response()->json(new SignAppResource([
            'signAppUrl' => $signAppUrl,
            'signAppManual' => $signAppManual
        ]));
    }

    public function getJointOwners(string $id): Response
    {
        $jointOwners = $this->contractService->getJointOwners($id);
        $contractJointOwners = [];

        foreach ($jointOwners as $jointOwner) {
            if ($jointOwner['roleCode']['code'] == 1) {
                $contractJointOwners[] = $jointOwner;
            }
        }

        return response()->json(new ContractJointOwnerCollection($contractJointOwners));
    }

    public function getJointOwnersInfo(string $id): Response
    {
        $jointOwners = $this->contractService->getJointOwners($id);
        $contractJointOwners = [];

        foreach ($jointOwners as $jointOwner) {
            if ($jointOwner['roleCode']['code'] == 1) {
                $customer = $this->customerRepository->getByCustomer($jointOwner);
                $contractJointOwners[] = $customer;
            }
        }

        return response()->json(new ContractJointOwnerInfoCollection($contractJointOwners));
    }

    /**
     * @throws Exception
     */
    public function getJointOwnersSignInfo(string $id): Response
    {
        try {
            $jointOwners = $this->contractService->getJointOwners($id);
            $adultsJointOwners = [];
            $contractJointOwners = [];
            $isCommonInfoSignMeAvailable = false;

            foreach ($jointOwners as $jointOwner) {
                if (isset($jointOwner['birthDate']) && Carbon::parse($jointOwner['birthDate'])->age >= 18) {
                    $documents = [];
                    $document = null;
                    $customer = $this->customerRepository->getById($jointOwner['contactId']);

                    if (isset($jointOwner['contactId'])) {
                        $documentList = $this->documentRepository->getDocumentsByCrmIdWithTypeCode(
                            $jointOwner['contactId'],
                            [65538]
                        );

                        foreach ($documentList as $docItem) {
                            if ($docItem->getType()->value == '65538') {
                                $documents[] = $docItem;
                            }
                        }

                        if (count($documents) > 1) {
                            // Обратите внимание, что compareCreatedOn должен быть определён где-то в коде
                            usort($documents, 'compareCreatedOn');
                            $document = $documents[0];
                        } elseif (count($documents) == 1) {
                            $document = $documents[0];
                        }
                    }

                    if ($isCommonInfoSignMeAvailable == false) {
                        $isCommonInfoSignMeAvailable = $customer->getSignStatus()?->value == 43;
                    }

                    $jointOwner['customer'] = $customer;
                    $jointOwner['document'] = $document;
                    $contractJointOwners[] = $jointOwner;
                }
            }

            $reissueGuideUrl = SignDocuments::where('code', '=', 'reissue_guide')->first()?->document_id;
            $courierSignInfo = 'Для выпуска эл. подписи необходимо заказать выезд курьера...';
            $dto = new ContractJointOwnerDto(
                courierSignInfo: $courierSignInfo,
                isCommonInfoSignMeAvailable: $isCommonInfoSignMeAvailable,
                reissueGuideUrl: $reissueGuideUrl,
                jointOwners: $contractJointOwners
            );

            return response()->json(new ContractJointOwnerResource($dto));
        } catch (\Throwable $e) {
            throw ExceptionHelper::rethrowWithLocation($e);
        }
    }

    public function getApprove(GetApproveRequest $request)
    {
        $ids = $request->all();

        if ($ids != null) {
            $this->contractService->getApprove($ids);
        }

        return $this->empty();
    }

    public function getConfidant(string $contractId, string $jointOwnersId)
    {
        $contract = $this->contractService->findContract($contractId);

        if ($contract != null) {
            $confidant = $this->contractService->getConfidant($contract, $jointOwnersId);

            return response()->json(new ConfidantResource($confidant));
        }

        return $this->empty();
    }

    public function getAdditionalContract(string $id)
    {
        $additionalContract = $this->contractService->getAdditionalContract($id);

        return response()->json(new DetailAdditionalContractResource($additionalContract));
    }

    public function getAdditionalContracts(string $id, GetAdditionalRequest $request)
    {
        $type = $request->get('type');

        $additionalContracts = $this->contractService->getAdditionalContracts($id, $type, $this->getAuthUser());

        return response()->json(new AdditionalCollection($additionalContracts));
    }

    public function getDemandSummary(DemandSummaryRequest $request)
    {
        $user = $this->getAuthUser();

        $summary = $this->contractService->demandSummary($user, $request);

        return response()->json(new DemandSummaryResource($summary));
    }

    public function getHypothecSup(string $id)
    {
        $contract = $this->contractService->findContract($id);

        return response()->json(new HypothecSupResource($contract));
    }

    public function getUkDocuments(string $id)
    {
        $ukDocument = $this->contractService->getUkDocuments($id, $this->getAuthUser());

        return response()->json(['uk_documents' => new ContractVersionCollection($ukDocument)]);
    }

    private function compareCreatedOn($a, $b)
    {
        return strtotime($b['createdOn']) - strtotime($a['createdOn']);
    }

    private function getNameLk(string $articleTypeCode, string $articleVariantTm1Code):string
    {
        $res = '';

        $articleTypeCode == 2 ? $res = 'Квартира': null;
        $articleTypeCode == 4 ? $res = 'Машиноместо': null;
        $articleTypeCode == 8 ? $res = 'Нежилое': null;
        $articleTypeCode == 2 && $articleVariantTm1Code == 4096? $res = 'Кладовая': null;
        $articleTypeCode == 2 && $articleVariantTm1Code == 2? $res = 'Апартаменты': null;
        $articleTypeCode == 8 && $articleVariantTm1Code == 3? $res = 'Кладовая': null;
        $articleTypeCode == 8 && $articleVariantTm1Code == 32? $res = 'Офис': null;
        $articleTypeCode == 8 && $articleVariantTm1Code == 34? $res = 'Офис': null;

        return $res;
    }
}
