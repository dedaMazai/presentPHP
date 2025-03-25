<?php

namespace App\Services\DynamicsCrm;

use App\Http\Api\External\V1\Requests\Sales\SendContractDraftRequest;
use App\Http\Api\External\V1\Requests\Sales\TradeInRequest;
use App\Models\Claim\ClaimPass\ClaimPassType;
use App\Models\Claim\ClaimStatus;
use App\Models\Claim\ClaimTheme;
use App\Models\Contract\ContractType;
use App\Models\Sales\Bank\BankType;
use App\Models\Sales\Demand\Demand;
use App\Models\Sales\Demand\DemandBookingType;
use App\Models\Sales\Demand\DemandStatus;
use App\Models\Sales\PaymentMode;
use App\Models\Sales\SubType;
use App\Models\User\DeletingReason;
use App\Models\User\User;
use App\Notifications\SendTelegramNotification;
use App\Services\Claim\Dto\ClaimImageDto;
use App\Services\Claim\Dto\SaveClaimAppealDto;
use App\Services\Claim\Dto\SaveClaimAttachmentDto;
use App\Services\Claim\Dto\SaveClaimMarketplaceDto;
use App\Services\Claim\Dto\SaveClaimPassDto;
use App\Services\Claim\Dto\SaveClaimRequestDto;
use App\Services\Claim\Dto\SaveClaimSosDto;
use App\Services\Claim\Dto\SaveClaimVisitDto;
use App\Services\Claim\Dto\SaveClaimWarrantyDto;
use App\Services\Claim\Dto\SetClaimPaidDto;
use App\Services\Deal\Dto\SetBankDto;
use App\Services\DynamicsCrm\Exceptions\BadRequestException;
use App\Services\DynamicsCrm\Exceptions\NotFoundException;
use App\Services\DynamicsCrm\Exceptions\UnableToCancelClaimException;
use App\Services\DynamicsCrm\Exceptions\UnableToCreateUserException;
use App\Services\DynamicsCrm\Exceptions\UnableToDeleteUserException;
use App\Services\DynamicsCrm\Exceptions\UnableToRestoreUserException;
use App\Services\Feedback\Dto\SaveFeedbackDto;
use App\Services\Pass\Dto\SavePassDto;
use App\Services\RelationshipInvite\Dto\CreateContractJointOwnerDto;
use App\Services\Sales\Contract\Dto\CreateCourierAddressDto;
use App\Services\Sales\Demand\Dto\CreateBookingContractDto;
use App\Services\Sales\Demand\Dto\CreateBookingDto;
use App\Services\Sales\Demand\Dto\CreateMortgageDemandDto;
use App\Services\Sales\Demand\Dto\JointOwnerDto;
use App\Services\Sales\Demand\Dto\JointOwnerMeetingDto;
use App\Services\Sales\JointOwner\Dto\CreateJointOwnerLeadDto;
use App\Services\Sales\JointOwner\Dto\UpdateJointOwnerDto;
use App\Services\Sales\JointOwner\Exceptions\PhoneExistException;
use App\Services\SettingsService;
use App\Services\User\Dto\CreateUserDto;
use App\Services\User\Dto\UploadUserDocumentDto;
use Carbon\Carbon;
use Exception;
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\RequestOptions;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use RuntimeException;

/**
 * Class DynamicsCrmClient
 *
 * @package App\Services\DynamicsCrm
 */
class DynamicsCrmClient
{
    private HttpClient $httpClient;

    public function __construct(string $baseUri, private SettingsService $settingsService)
    {
        $client = new HttpClient([
            'base_uri' => $baseUri,
        ]);

        $this->httpClient = $client;
    }

    /**
     * @param CreateUserDto $dto
     *
     * @return array
     * @throws BadRequestException
     * @throws NotFoundException
     * @throws UnableToCreateUserException
     */
    public function createUser(CreateUserDto $dto): array
    {
        $response = $this->request(
            'POST',
            'Customer',
            [
                RequestOptions::JSON => [
                    'firstName' => $dto->firstName,
                    'lastName' => $dto->lastName,
                    'middleName' => $dto->middleName,
                    'phone' => ltrim($dto->phone, '+'),
                    'email' => $dto->email,
                    'birthDate' => $dto->birthDate->toIso8601String(),
                    'personalDataProcconSent' => true,
                    'pdProcconSentDate' => Carbon::now()->toIso8601String()
                ],
            ],
        );

        if (isset($response['typeMessage']) && $response['typeMessage'] === 0) {
            throw new UnableToCreateUserException();
        }

        return $response;
    }

    /**
     * @throws NotFoundException
     * @throws BadRequestException
     */
    public function updateUserEmail(User $user, string $email): array
    {
        return $this->request(
            'PUT',
            'Customer/' . $user->crm_id,
            [
                RequestOptions::JSON => [
                    'Email' => $email,
                ],
            ],
        );
    }

    /**
     * @throws NotFoundException
     * @throws BadRequestException
     */
    public function setUserSberClient(User $user, bool $isSberClient): array
    {
        return $this->request(
            'PUT',
            'Customer/' . $user->crm_id,
            [
                RequestOptions::JSON => [
                    'isSberClient' => $isSberClient,
                ],
            ],
        );
    }

    /**
     * @throws NotFoundException
     * @throws BadRequestException
     */
    public function updateUserEmailByCrmId(string $crmId, string $email): array
    {
        return $this->request(
            'PUT',
            'Customer/' . $crmId,
            [
                RequestOptions::JSON => [
                    'Email' => $email,
                ],
            ],
        );
    }

    /**
     * @param User                  $user
     * @param DeletingReason|string $reason
     *
     * @return array
     * @throws BadRequestException
     * @throws NotFoundException
     * @throws UnableToDeleteUserException
     */
    public function deleteUser(User $user, DeletingReason | string $reason): array
    {
        $response = $this->request(
            'PUT',
            'Customer/' . $user->crm_id,
            [
                RequestOptions::JSON => [
                    'isMobileAppProfileDeleted' => true,
                    'mobileAppProfileDeletionReason' => $reason->title ?? $reason
                ],
            ],
        );

        if (isset($response['typeMessage']) && $response['typeMessage'] === 0) {
            throw new UnableToDeleteUserException();
        }

        return $response;
    }

    /**
     * @param User $user
     *
     * @return array
     * @throws BadRequestException
     * @throws NotFoundException
     * @throws UnableToRestoreUserException
     */
    public function restoreUser(User $user): array
    {
        $response = $this->request(
            'PUT',
            'Customer/' . $user->crm_id,
            [
                RequestOptions::JSON => [
                    'isMobileAppProfileDeleted' => false,
                    'mobileAppProfileDeletionReason' => null
                ],
            ],
        );

        if (isset($response['typeMessage']) && $response['typeMessage'] === 0) {
            throw new UnableToRestoreUserException();
        }

        return $response;
    }

    /**
     * @throws BadRequestException
     * @throws NotFoundException
     */
    public function getContractsByType(ContractType $type, string $customerId): array
    {
        return $this->request(
            'GET',
            'Contracts/operationtypes/' . $type->value . '/customers/' . $customerId,
        );
    }

    public function getCustomerByAccountId(string $id): array
    {
        return $this->request(
            'GET',
            'Accounts/' . $id,
        );
    }

    /**
     * @throws BadRequestException
     * @throws NotFoundException
     */
    public function getContractsByTypes($types, string $customerId): array
    {
        return $this->request(
            'GET',
            'Contracts/operationtypes/' . $types . '/customers/' . $customerId,
        );
    }

    /**
     * @throws BadRequestException
     * @throws NotFoundException
     */
    public function getContractsByTypesV2(string $customerId): array
    {
        return $this->request(
            'GET',
            'Contracts/operationtypes/128,256/customers/' . $customerId,
        );
    }

    /**
     * @throws BadRequestException
     * @throws NotFoundException
     */
    public function getDetailContracts(string $contractId): array
    {
        return $this->request(
            'GET',
            'PersonalAccounts/contracts/' . $contractId
        );
    }

    /**
     * @throws BadRequestException
     * @throws NotFoundException
     */
    public function setContractJointOwner(string $contractId, CreateContractJointOwnerDto $dto): array
    {
        return $this->request(
            'POST',
            'JointOwners/Contracts/' . $contractId . '/Customers/' . $dto->customerId,
            [
                RequestOptions::JSON => [
                    'JointOwner' => [
                        'lastName' => $dto->lastName,
                        'firstName' => $dto->firstName,
                        'middleName' => $dto->middleName,
                        'phone' => ltrim($dto->phone, '+'),
                        'birthDate' => $dto->birthDate->toDateString(),
                        'email' => $dto->email,
                        'roleCode' => [
                            'code' => $dto->role->value,
                            'name' => $dto->role->label,
                        ],
                    ],
                    'Annotations' => [],
                ],
            ],
            10,
        );
    }

    /**
     * @throws BadRequestException
     * @throws NotFoundException
     */
    public function createContractJointOwnerFromArray(string $contractId, array $jointOwner): array
    {
        return $this->request(
            'POST',
            'JointOwners/Contracts/' . $contractId . '/Customers',
            [
                RequestOptions::JSON => $jointOwner,
            ],
            10,
        );
    }

    /**
     * @throws BadRequestException
     * @throws NotFoundException
     */
    public function updateContractJointOwnerFromArray(string $contractId, string $customerId, array $role): array
    {
        return $this->request(
            'POST',
            'JointOwners/Contracts/' . $contractId . '/Customers/' . $customerId,
            [
                RequestOptions::JSON => $role,
            ],
            10,
        );
    }

    /**
     * @throws BadRequestException
     * @throws NotFoundException
     */
    public function deleteContractJointOwner(string $contractId, string $jointOwnerId): array
    {
        return $this->request(
            'DELETE',
            'JointOwners/' . $jointOwnerId . '/Contracts/' . $contractId
        );
    }

    /**
     * @throws NotFoundException
     * @throws BadRequestException
     */
    public function getCustomerById(string $id): array
    {
        return $this->request(
            'GET',
            'Customer/' . $id,
        );
    }

    /**
     * @throws NotFoundException
     * @throws BadRequestException
     */
    public function getCustomerByPhone(string $phone): array
    {
        return $this->request(
            'GET',
            'Customers/' . $phone,
        );
    }

    /**
     * @throws NotFoundException
     * @throws BadRequestException
     */
    public function getDocumentByUri(string $uri): array
    {
        return $this->request(
            'POST',
            'Documents',
            [
                RequestOptions::JSON => $uri,
            ],
        );
    }

    /**
     * @throws BadRequestException
     * @throws NotFoundException
     */
    public function getClaimCatalogue(): array
    {
        return $this->request(
            'GET',
            'Claims/GetServiceCatalog',
        );
    }

    /**
     * @throws BadRequestException
     * @throws NotFoundException
     */
    public function getAccountCatalogue($accountNumber): array
    {
        return $this->request(
            'GET',
            'PersonalAccounts/'. $accountNumber .'/GetServiceCatalog',
        );
    }

    /**
     * @throws BadRequestException
     * @throws NotFoundException
     */
    public function getClaimsByAccountNumber(string $accountNumber): array
    {
        return $this->request(
            'GET',
            'Claims/PersonalAccounts/' . $accountNumber,
        );
    }

    /**
     * @throws BadRequestException
     * @throws NotFoundException
     */
    public function getAccountThemesByAccountNumber(string $accountNumber): array
    {
        return $this->request(
            'GET',
            'PersonalAccounts/' . $accountNumber . '/GetServiceCatalog/root',
        );
    }

    /**
     * @throws NotFoundException
     * @throws BadRequestException
     */
    public function getClaimsModifiedDateByAccountNumber(string $accountNumber): array
    {
        return $this->request(
            'GET',
            "Claims/PersonalAccountsModifiedDate/$accountNumber"
        );
    }

    /**
     * @throws BadRequestException
     * @throws NotFoundException
     */
    public function getClaimMessagesByClaimId(string $claimId): array
    {
        return $this->request(
            'GET',
            'Messages/GetList/Claims/' . $claimId,
        );
    }

    /**
     * @throws BadRequestException
     * @throws NotFoundException
     */
    public function sendClaimMessage(string $claimId, string $text, User $user): array
    {
        return $this->request(
            'POST',
            'Messages/Send/Claims/' . $claimId,
            [
                RequestOptions::JSON => [
                    'senderId' => $user->crm_id,
                    "senderName" => $user->getFullName(),
                    "subject" => "Сообщение по заявке",
                    'text' => $text,
                ],
            ],
        );
    }

    /**
     * @throws BadRequestException
     * @throws NotFoundException
     */
    public function getClaimById(string $id): array
    {
        return $this->request(
            'GET',
            'Claims/' . $id,
        );
    }

    /**
     * @throws BadRequestException
     * @throws NotFoundException
     */
    public function getClaimsByPersonalAccounts(array $accountNumbers): array
    {
        return $this->request(
            'POST',
            'Claims/ByPersonalAccounts',
            [
                RequestOptions::JSON => $accountNumbers,
            ],
        );
    }

    public function readMessages(array $messages): array
    {
        return $this->request(
            'PUT ',
            'Messages/IsRead',
            [
                RequestOptions::JSON => $messages,
            ],
        );
    }

    public function readDocuments(array $documents): array
    {
        return $this->request(
            'PUT ',
            'Documents/IsRead',
            [
                RequestOptions::JSON => $documents,
            ],
        );
    }

    /**
     * @throws BadRequestException
     * @throws NotFoundException
     */
    public function getDocumentsByClaimId(string $claimId): array
    {
        $data = $this->request(
            'GET',
            'Documents/DocumentTypes/524500,524501/Claims/' . $claimId,
        );

        if ($data === null) {
            logger()->error('DynamicsCrmClient: a document card has no reference to the document', [
                'method' => 'getDocumentsByClaimId',
                'arguments' => func_get_args(),
            ]);
        }

        return [];
    }

    public function getFilesByClaimId(string $claimId): ?array
    {
        $data = $this->request(
            'GET',
            'Documents/DocumentTypes/524751,524504,524500,524750/Claims/' . $claimId,
        );

        if ($data === null) {
            logger()->error('DynamicsCrmClient: a document card has no reference to the document', [
                'method' => 'getDocumentsByClaimId',
                'arguments' => func_get_args(),
            ]);
        }

        return $data;
    }

    public function getBodyByUrl(string $url): ?array
    {
        $data = $this->request(
            'POST',
            'Documents',
            [
                RequestOptions::JSON => $url,
            ],
        );

        return $data;
    }

    public function getClaimReceipts(string $claimId): ?array
    {
        $data = $this->request(
            'GET',
            'Documents/DocumentTypes/524502/Claims/' . $claimId,
        );

        if ($data === null) {
            logger()->error('DynamicsCrmClient: a document card has no reference to the document', [
                'method' => 'getDocumentsByClaimId',
                'arguments' => func_get_args(),
            ]);
        }

        return $data;
    }

    /**
     * @param string $id
     * @param int    $rating
     *
     * @return array
     * @throws BadRequestException
     * @throws NotFoundException
     */
    public function rateClaimById(string $id, int $rating): array
    {
        return $this->request(
            'PUT',
            'Claims/' . $id . '/Update',
            [
                RequestOptions::JSON => [
                    'qualityCode' => [
                        'code' => $rating,
                    ],
                ],
            ],
        );
    }

    public function paidClaimById(string $id): void
    {
        $this->request(
            'PUT',
            'Claims/' . $id . '/Update',
            [
                RequestOptions::JSON => [
                    'invoiceStatus' => [
                        'code' => 5,
                        'name' => 'Оплачен On-line'
                    ],
                ],
            ],
        );
    }

    /**
     * @param string $id
     * @param string $comment
     *
     * @return array
     * @throws BadRequestException
     * @throws NotFoundException
     */
    public function commentClaimById(string $id, string $comment): array
    {
        return $this->request(
            'PUT',
            'Claims/' . $id . '/Update',
            [
                RequestOptions::JSON => [
                    'commentQuality' => $comment,
                ],
            ],
        );
    }

    /**
     * @param string          $id
     * @param ClaimImageDto[] $imageDtos
     *
     * @return array
     * @throws BadRequestException
     * @throws NotFoundException
     */
    public function reopenClaimById(string $id, array $imageDtos): array
    {
        return $this->request(
            'PUT',
            'Claims/' . $id . '/Update',
            [
                RequestOptions::JSON => [
                    'status' => [
                        'code' => ClaimStatus::reopened()->value,
                        'name' => ClaimStatus::reopened()->label,
                    ],
                    'photos' => $this->getClaimImagesData($imageDtos),
                ],
            ],
        );
    }

    /**
     * @throws BadRequestException
     * @throws NotFoundException
     */
    public function acceptClaimById(string $id, int $code): array
    {
        return $this->request(
            'PUT',
            'Claims/' . $id . '/Update',
            [
                RequestOptions::JSON => [
                    'confirmationCode' => $code,
                    'status' => [
                        'code' => ClaimStatus::closedByClient()->value,
                        'name' => ClaimStatus::closedByClient()->label,
                    ],
                    'state' => [
                        'code' => 1,
                    ],
                ],
            ],
        );
    }

    /**
     * @throws BadRequestException
     * @throws NotFoundException
     */
    public function saveClaimPass(string $accountNumber, User $user, SaveClaimPassDto $dto): array
    {
        $data = [
            'personalAccount' => $accountNumber,
            'incidentClassificationCode' => [
                'code' => ClaimTheme::pass()->value,
                'name' => ClaimTheme::pass()->label,
            ],
            'passTypeCode' => [
                'code' => $dto->passType->value,
                'name' => $dto->passType->label,
            ],
            'arrivalDate' => $dto->arrivalDate->toAtomString(),
        ];
        if ($dto->passType->equals(ClaimPassType::car())) {
            $data['car'] = [
                'carTypeCode' => [
                    'code' => $dto->carItemDto->carType->value,
                    'name' => $dto->carItemDto->carType->label,
                ],
                'number' => $dto->carItemDto->number,
            ];
            $data['services'] = [
                [
                    'id' => $this->settingsService->getClaimPassCarCrmServiceId(),
                    'quantity' => 1,
                ],
            ];
        } elseif ($dto->passType->equals(ClaimPassType::human())) {
            $data['human'] = [
                'fullNameGuest' => $dto->humanItemDto->fullName,
            ];
            $data['services'] = [
                [
                    'id' => $this->settingsService->getClaimPassHumanCrmServiceId(),
                    'quantity' => 1,
                ],
            ];
        }
        $data['description'] = $dto->comment;
        $data['declarantId'] = $user->crm_id;
        $data['photos'] = $this->getClaimImagesData($dto->imageDtos);

        return $this->request(
            'POST',
            'Claims/Create',
            [
                RequestOptions::JSON => $data,
            ],
        );
    }

    /**
     * @throws BadRequestException
     * @throws NotFoundException
     */
    public function saveClaimSos(string $accountNumber, User $user, SaveClaimSosDto $dto): array
    {
        return $this->request(
            'POST',
            'Claims/Create',
            [
                RequestOptions::JSON => [
                    'personalAccount' => $accountNumber,
                    'incidentClassificationCode' => [
                        'code' => ClaimTheme::sos()->value,
                        'name' => ClaimTheme::sos()->label,
                    ],
                    'services' => [
                        [
                            'id' => $dto->claimCatalogueItem->getId(),
                            'quantity' => 1,
                        ],
                    ],
                    'description' => $dto->comment,
                    'declarantId' => $user->crm_id,
                    'photos' => $this->getClaimImagesData($dto->imageDtos),
                ],
            ],
        );
    }

    /**
     * @throws BadRequestException
     * @throws NotFoundException
     */
    public function saveClaimRequest(string $accountNumber, User $user, SaveClaimRequestDto $dto): array
    {
        $services = [];
        foreach ($dto->claimCatalogueItems as $claimCatalogueItem) {
            $services[] = [
                'id' => $claimCatalogueItem->getId(),
                'quantity' => 1,
            ];
        }

        return $this->request(
            'POST',
            'Claims/Create',
            [
                RequestOptions::JSON => [
                    'personalAccount' => $accountNumber,
                    'incidentClassificationCode' => [
                        'code' => ClaimTheme::request()->value,
                        'name' => ClaimTheme::request()->label,
                    ],
                    'arrivalDate' => $dto->arrivalDate?->toAtomString(),
                    'services' => $services,
                    'description' => $dto->comment,
                    'declarantId' => $user->crm_id,
                    'photos' => $this->getClaimImagesData($dto->imageDtos),
                ],
            ],
        );
    }

    /**
     * @throws BadRequestException
     * @throws NotFoundException
     */
    public function saveCreateRequest(string $accountNumber, User $user, SaveClaimRequestDto $dto): array
    {
        $services = [];
        foreach ($dto->claimCatalogueItems as $claimCatalogueItem) {
            $services[] = [
                'id' => $claimCatalogueItem->getId(),
                'quantity' => 1,
            ];
        }

        return $this->request(
            'POST',
            'Claims/Create',
            [
                RequestOptions::JSON => [
                    'personalAccount' => $accountNumber,
                    'incidentClassificationCode' => $dto->theme,
                    'arrivalDate' => $dto->arrivalDate?->toAtomString(),
                    'services' => $services,
                    'description' => $dto->comment,
                    'declarantId' => $user->crm_id,
                ],
            ],
        );
    }

    /**
     * @throws BadRequestException
     * @throws NotFoundException
     */
    public function saveClaimAppeal(string $accountNumber, User $user, SaveClaimAppealDto $dto): array
    {
        $services = [];
        foreach ($dto->claimCatalogueItems as $claimCatalogueItem) {
            $services[] = [
                'id' => $claimCatalogueItem->getId(),
                'quantity' => 1,
            ];
        }

        return $this->request(
            'POST',
            'Claims/Create',
            [
                RequestOptions::JSON => [
                    'personalAccount' => $accountNumber,
                    'incidentClassificationCode' => [
                        'code' => ClaimTheme::question()->value,
                        'name' => ClaimTheme::question()->label,
                    ],
                    'services' => $services,
                    'description' => $dto->comment,
                    'declarantId' => $user->crm_id,
                    'photos' => $this->getClaimImagesData($dto->imageDtos),
                ],
            ],
        );
    }

    /**
     * @throws BadRequestException
     * @throws NotFoundException
     */
    public function saveClaimVisit(string $accountNumber, User $user, SaveClaimVisitDto $dto): array
    {
        return $this->request(
            'POST',
            'Claims/Create',
            [
                RequestOptions::JSON => [
                    'personalAccount' => $accountNumber,
                    'incidentClassificationCode' => [
                        'code' => ClaimTheme::visit()->value,
                        'name' => ClaimTheme::visit()->label,
                    ],
                    'arrivalDate' => $dto->arrivalDate->toAtomString(),
                    'services' => [
                        [
                            'id' => $dto->claimCatalogueItem->getId(),
                            'quantity' => 1,
                        ],
                    ],
                    'description' => $dto->comment,
                    'declarantId' => $user->crm_id,
                    'photos' => $this->getClaimImagesData($dto->imageDtos),
                ],
            ],
        );
    }

    /**
     * @throws BadRequestException
     * @throws NotFoundException
     */
    public function saveClaimWarranty(string $accountNumber, User $user, SaveClaimWarrantyDto $dto): array
    {
        $services = [];
        foreach ($dto->claimCatalogueItems as $claimCatalogueItem) {
            $services[] = [
                'id' => $claimCatalogueItem->getId(),
                'quantity' => 1,
            ];
        }

        return $this->request(
            'POST',
            'Claims/Create',
            [
                RequestOptions::JSON => [
                    'personalAccount' => $accountNumber,
                    'incidentClassificationCode' => [
                        'code' => ClaimTheme::warranty()->value,
                        'name' => ClaimTheme::warranty()->label,
                    ],
                    'arrivalDate' => $dto->arrivalDate?->toAtomString(),
                    'services' => $services,
                    'description' => $dto->comment,
                    'declarantId' => $user->crm_id,
                    'photos' => $this->getClaimImagesData($dto->imageDtos),
                ],
            ],
        );
    }

    /**
     * @throws BadRequestException
     * @throws NotFoundException
     */
    public function saveClaimMarketplace(string $accountNumber, User $user, SaveClaimMarketplaceDto $dto): array
    {
        $services = [];
        foreach ($dto->claimMarketplaceItemDtos as $claimMarketplaceItemDto) {
            $services[] = [
                'id' => $claimMarketplaceItemDto->claimCatalogueItem->getId(),
                'quantity' => $claimMarketplaceItemDto->count,
            ];
        }

        return $this->request(
            'POST',
            'Claims/Create',
            [
                RequestOptions::JSON => [
                    'personalAccount' => $accountNumber,
                    'incidentClassificationCode' => [
                        'code' => ClaimTheme::marketplace()->value,
                        'name' => ClaimTheme::marketplace()->label,
                    ],
                    'arrivalDate' => $dto->arrivalDate?->toAtomString(),
                    'services' => $services,
                    'declarantId' => $user->crm_id,
                    'photos' => $this->getClaimImagesData($dto->imageDtos),
                    'description' => $dto->comment
                ],
            ],
        );
    }

    /**
     * @throws BadRequestException
     * @throws NotFoundException
     */
    public function saveClaimAttachment(SaveClaimAttachmentDto $dto): array
    {
        return $this->request(
            'POST',
            'Feedback/customers/'.$dto->crm_user_id,
            [
                RequestOptions::JSON => [
                    'ObjectId' => $dto->claim_id,
                    'ObjectTypeCode' => 10126,
                    'IsCallBack' => false,
                    'Annotations' => [[
                        'Name' => $dto->file_name,
                        'FileName' => $dto->file_name,
                        'DocumentType' => [
                            'code' => $dto->document_type_code,
                            'name' => $dto->document_type_name,
                        ],
                        'IsCustomerAvailable' => true,
                        'DocumentBody' => $dto->file_body,
                        'MimeType' => $dto->mime_type,
                    ]]
                ],
            ],
        );
    }

    /**
     * @throws NotFoundException
     * @throws BadRequestException
     */
    public function sendTradeInRequest(TradeInRequest $request): array
    {
        return $this->request(
            'POST',
            'Feedback/customers/' . $request->customer_id,
            [
                RequestOptions::JSON => [
                    'ObjectId' => $request->demand_id,
                    'ObjectTypeCode' => 4,
                    'AppealType' => 'Трейд-ин',
                    'AppealText' => 'Запрос на предоставление информации по Трейд-ин',
                ],
            ],
        );
    }

    /**
     * @throws NotFoundException
     * @throws BadRequestException
     */
    public function setContractDraft(string $demandId, string $userCrmId): array
    {
        return $this->request(
            'POST',
            'Feedback/customers/' . $userCrmId,
            [
                RequestOptions::JSON => [
                    'ObjectId' => $demandId,
                    'ObjectTypeCode' => 4,
                    'AppealType' => 'Запрос на подготовку проекта договора от клиента МП',
                    'AppealText' => 'Запрос на подготовку проекта договора от клиента МП',
                ],
            ],
        );
    }

    /**
     * @throws BadRequestException
     * @throws NotFoundException
     */
    public function setClaimPaid(User $user, SetClaimPaidDto $dto, string $paymentMethod): array
    {
        return $this->request(
            'POST',
            'Claims/' . $dto->claim->getId() . '/Pay',
            [
                RequestOptions::JSON => [
                    'payment_acquirer_id' => $dto->paymentId,
                    'payment_date' => $dto->paymentDateTime->toDateTimeString(),
                    'client_crm_id' => $user->crm_id,
                    'payment_method' => $paymentMethod,
                ],
            ],
        );
    }

    /**
     * @throws UnableToCancelClaimException
     * @throws BadRequestException
     * @throws NotFoundException
     */
    public function cancelClaim(string $id): array
    {
        $response = $this->request(
            'PUT',
            'Claims/' . $id . '/Cancel',
            [
                RequestOptions::JSON => [
                    'status' => [
                        'code' => ClaimStatus::cancelledByClient()->value,
                        'name' => ClaimStatus::cancelledByClient()->label,
                    ],
                ],
            ],
        );

        if (!isset($response['typeMessage'])) {
            throw new UnableToCancelClaimException();
        }

        return $response;
    }

    /**
     * @throws UnableToCancelClaimException
     * @throws BadRequestException
     * @throws NotFoundException
     */
    public function declineClaim(string $id): array
    {
        $response = $this->request(
            'PUT',
            'Claims/' . $id . '/Update',
            [
                RequestOptions::JSON => [
                    'status' => [
                        'code' => ClaimStatus::notAccepted()->value,
                        'name' => ClaimStatus::notAccepted()->label,
                    ],
                ],
            ],
        );

        return $response;
    }

    /**
     * @throws BadRequestException
     * @throws NotFoundException
     */
    public function getDemands(User $user): array
    {
        return $this->request(
            'GET',
            'Demands/Customers/' . $user->crm_id . '/Statuses/3,8,32',
        );
    }

    /**
     * @throws BadRequestException
     * @throws NotFoundException
     */
    public function getDemandsV2(User $user): array
    {
        return $this->request(
            'GET',
            'Demands/Customers/' . $user->crm_id . '/Statuses/8,32',
        );
    }

    /**
     * @throws BadRequestException
     * @throws NotFoundException
     */
    public function getDemandsByStatus(User $user, DemandStatus $status): array
    {
        return $this->request(
            'GET',
            'Demands/Customers/' . $user->crm_id . '/Statuses/' . $status->value,
        );
    }

    /**
     * @throws BadRequestException
     * @throws NotFoundException
     */
    public function getDemandById(string $id, User $user): array
    {
        return $this->request(
            'GET',
            'Demand/' . $id . '/Customers/' . $user->crm_id,
        );
    }

    /**
     * @throws BadRequestException
     * @throws NotFoundException
     */
    public function getPropertyById(string $id): array
    {
        $data = $this->request(
            'GET',
            'Article/' . $id,
        );

        if (!isset($data['id'])) {
            throw new NotFoundException();
        }

        return $data;
    }

    /**
     * @throws BadRequestException
     * @throws NotFoundException
     */
    public function getContractById(string $id): array
    {
        return $this->request(
            'GET',
            'Contracts/' . $id,
        );
    }

    /**
     * @throws BadRequestException
     * @throws NotFoundException
     */
    public function getContractsByPropertyId(string $propertyId, string $customerId): array
    {
        return $this->request(
            'GET',
            'Contracts/Customers/' . $customerId . '/Articles/' . $propertyId,
        );
    }

    /**
     * @throws BadRequestException
     * @throws NotFoundException
     */
    public function getCharacteristicSalesByPropertyId(string $propertyId): array
    {
        return $this->request(
            'GET',
            'CharacteristicSales/Articles/' . $propertyId,
        );
    }

    /**
     * @throws BadRequestException
     * @throws NotFoundException
     */
    public function getAddressesByProjectCrmId(string $projectCrmId): array
    {
        return $this->request(
            'GET',
            'Services/GetAddressesByGkId/' . $projectCrmId,
        );
    }

    /**
     * @throws BadRequestException
     * @throws NotFoundException
     */
    public function getApproveByIds(array $ids): ?array
    {
        return $this->request(
            'PUT',
            'Documents/IsApprove/',
            [
                RequestOptions::JSON => $ids,
            ],
        );
    }

    /**
     * @throws BadRequestException
     * @throws NotFoundException
     */
    public function getBanksByAddressId(string $addressId): array
    {
        return $this->request(
            'GET',
            'Accounts/AccreditationBank/' . $addressId,
        );
    }

    /**
     * @throws BadRequestException
     * @throws NotFoundException
     */
    public function createBooking(CreateBookingDto $dto): array
    {
        return $this->request(
            'POST',
            'Demand',
            [
                RequestOptions::JSON => [
                    'demandType' => [
                        'code' => $dto->type->value,
                        'name' => $dto->type->label,
                    ],
                    'demandSubType' => [
                        'code' => $dto->bookingType->value,
                        'name' => $dto->bookingType->label,
                    ],
                    'customerId' => $dto->user->crm_id,
                    'articleId' => $dto->propertyBookingDto->id,
                    'subject' => 'Заявка из МП'
                ],
            ],
        );
    }

    /**
     * @throws BadRequestException
     * @throws NotFoundException
     */
    public function createMortgageDemand(CreateMortgageDemandDto $dto): array
    {
        return $this->request(
            'POST',
            'Demand',
            [
                RequestOptions::JSON => [
                    'demandType' => [
                        'code' => $dto->type->value,
                        'name' => $dto->type->label,
                    ],
                    'demandMainId' => $dto->demand->getId(),
                    'customerId' => $dto->customer->getId(),
                    'articleId' => $dto->property->getId(),
                    'lastName' => $dto->customer->getLastName(),
                    'firstName' => $dto->customer->getFirstName(),
                    'middleName' => $dto->customer->getMiddleName(),
                    'phone' => ltrim($dto->customer->getPhone(), '+'),
                    'email' => $dto->customer->getEmail(),
                    'birthDate' => $dto->customer->getBirthDate(),
                    'subject' => 'Ипотека для заявки ' . $dto->demand->getId(),
                    'description' => 'Ипотека для заявки ' . $dto->demand->getId(),
                    'dhIsIntegration' => true,
                ],
            ],
        );
    }

    /**
     * @throws BadRequestException
     * @throws NotFoundException
     */
    public function createBookingContract(CreateBookingContractDto $dto): array
    {
        return $this->request(
            'POST',
            'Contract',
            [
                RequestOptions::JSON => [
                    'demandId' => $dto->demandId,
                    'demandSubType' => [
                        'code' => $dto->bookingType->value,
                        'name' => $dto->bookingType->label
                    ],
                    'articleId' => $dto->propertyBookingDto->id,
                    'customerId' => $dto->user->crm_id,
                ],
            ],
        );
    }

    /**
     * @throws BadRequestException
     * @throws NotFoundException
     */
    public function createBookingContractFromData(array $data): array
    {
        return $this->request(
            'POST',
            'Contract',
            [
                RequestOptions::JSON => [
                    'demandId' => $data['demandId'],
                    'demandSubType' => [
                        'code' => $data['demandSubType']['code'],
                    ],
                    'articleId' => $data['articleId'],
                    'customerId' => $data['customerId'],
                ],
            ],
        );
    }

    /**
     * @throws BadRequestException
     * @throws NotFoundException
     */
    public function addFinishVariant(string $demandId, string $finishId)
    {
        return $this->request(
            'PUT',
            'Demand/' . $demandId . '/AddFinishVariant/' . $finishId
        );
    }

    /**
     * @throws BadRequestException
     * @throws NotFoundException
     */
    public function setDemandBank(string $demandId, string $bankId, BankType $type): array
    {
        $bankTypeUrl = match ($type->value) {
            BankType::letterOfCredit()->value => 'LetterOfCreditBank',
            default => 'HypothecBank',
        };

        return $this->request(
            'PUT',
            'Demand/' . $demandId . '/' . $bankTypeUrl . '/' . $bankId,
        );
    }

    /**
     * @throws BadRequestException
     * @throws NotFoundException
     */
    public function setDemandCharacteristic(string $demandId, string $characteristicId): array
    {
        return $this->request(
            'POST',
            'CharacteristicSales/Demands/' . $demandId,
            [
                RequestOptions::JSON => [
                    'CharacteristicSaleList' => [
                        [
                            'id' => $characteristicId,
                        ],
                    ],
                ],
            ],
        );
    }

    /**
     * @throws BadRequestException
     * @throws NotFoundException
     */
    public function setDemandBookingPaid(string $demandId): array
    {
        return $this->request(
            'PUT',
            'Demand/PaymentReservationContract/' . $demandId,
        );
    }

    /**
     * @throws BadRequestException
     * @throws NotFoundException
     */
    public function setDemandFreeBooking(Demand $demand): array
    {
        return $this->request(
            'PUT',
            'Demand/' . $demand->getId(),
            [
                RequestOptions::JSON => [
                    'demandSubType' => [
                        'code' => DemandBookingType::free()->value,
                        'name' => DemandBookingType::free()->label,
                    ],
                    'beginDate' => $demand->getDeal()->initial_begin_date,
                    'endDate' => $demand->getDeal()->initial_end_date,
                ],
            ],
        );
    }

    /**
     * @throws BadRequestException
     * @throws NotFoundException
     */
    public function getDemandMortgageUrl(string $demandId): array
    {
        return $this->request(
            'GET',
            'Surveys/Demands/' . $demandId,
        );
    }

    /**
     * @throws BadRequestException
     * @throws NotFoundException
     */
    public function getDemandMortgageApprovals(string $demandId): array
    {
        return $this->request(
            'GET',
            'Approvals/Demands/' . $demandId,
        );
    }

    /**
     * @throws BadRequestException
     * @throws NotFoundException
     */
    public function getBankApprovals(string $approvalId): array
    {
        return $this->request(
            'GET',
            'Approvals/SelectedBank/' . $approvalId,
        );
    }

    /**
     * @throws BadRequestException
     * @throws NotFoundException
     */
    public function setDemandPaymentMode(string $demandId, PaymentMode $paymentMode): array
    {
        return $this->request(
            'PUT',
            'Demand/' . $demandId,
            [
                RequestOptions::JSON => [
                    'paymentModeCode' => [
                        'code' => $paymentMode->value,
                        'name' => $paymentMode->label,
                    ],
                ],
            ],
        );
    }

    /**
     * @throws BadRequestException
     * @throws NotFoundException
     */
    public function setDemandSubType(string $demandId, SubType $subType): array
    {
        return $this->request(
            'PUT',
            'Demand/' . $demandId,
            [
                RequestOptions::JSON => [
                    'demandSubType' => [
                        'code' => $subType->value,
                        'name' => $subType->label,
                    ],
                ],
            ],
        );
    }

    /**
     * @throws BadRequestException
     * @throws NotFoundException
     */
    public function changeDemandSubType(string $demandId, array $data): array
    {
        return $this->request(
            'PUT',
            'Demand/' . $demandId,
            [
                RequestOptions::JSON => $data,
            ],
        );
    }


    /**
     * @throws BadRequestException
     * @throws NotFoundException
     */
    public function setDemandDealBank(string $demandId, array $banksId): array
    {
        return $this->request(
            'PUT',
            'Demand/' . $demandId,
            [
                RequestOptions::JSON => $banksId,
            ],
        );
    }

    /**
     * @throws BadRequestException
     * @throws NotFoundException
     */
    public function setDealBank(string $demandId, string $bank, string $bankId): array
    {
        return $this->request(
            'PUT',
            "Demand/$demandId/$bank/$bankId"
        );
    }

    /**
     * @throws BadRequestException
     * @throws NotFoundException
     */
    public function setDemandDealDepositor(string $demandId, array $depositor): array
    {
        return $this->request(
            'PUT',
            'Demand/' . $demandId,
            [
                RequestOptions::JSON => $depositor,
            ],
        );
    }

    /**
     * @throws BadRequestException
     * @throws NotFoundException
     */
    public function setBankId(SetBankDto $dto): array
    {
        return $this->request(
            'PUT',
            'Demand/' . $dto->demandId . '/' . $dto->bankType->value . '/' . $dto->bankId,
        );
    }

    /**
     * @throws BadRequestException
     * @throws NotFoundException
     */
    public function clearArticleId(string $demandId): array
    {
        return $this->request(
            'PUT',
            'Demand/' . $demandId . '/ClearArticleId',
        );
    }

    /**
     * @throws BadRequestException
     * @throws NotFoundException
     */
    public function setCharacteristicSalesClear(string $demandId, ?array $characteristicSaleIds): array
    {
        return $this->request(
            'PUT',
            'CharacteristicSales/Clear/demands/' . $demandId,
            [
                RequestOptions::JSON => ['CharacteristicSaleList' => $characteristicSaleIds],
            ],
        );
    }

    /**
     * @throws BadRequestException
     * @throws NotFoundException
     */
    public function setCharacteristicSalesDemand(string $demandId, ?array $characteristicSaleIds): array
    {
        return $this->request(
            'POST',
            'CharacteristicSales/demands/' . $demandId,
            [
                RequestOptions::JSON => ['CharacteristicSaleList' => [$characteristicSaleIds]],
            ],
        );
    }

    /**
     * @throws BadRequestException
     * @throws NotFoundException
     */
    public function setDemandJointOwners(string $demandId, JointOwnerDto $dto): array
    {
        $data = [];
        foreach ($dto->documents as $document) {
            $data[] = [
                'Name' => $document->name,
                'FileName' => $document->fileName,
                'DocumentType' => [
                    'code' => $document->type->value,
                    'name' => $document->type->label,
                ],
                'DocumentSubType' => $document->subtype ? [
                    'code' => $document->subtype->value,
                    'name' => $document->subtype->label,
                ] : null,
                'IsCustomerAvailable' => $document->isCustomerAvailable,
                'DocumentBody' => $document->documentBody,
                'MimeType' => $document->mimeType,
            ];
        }

        return $this->request(
            'POST',
            'JointOwners/Leads/' . $demandId . '/Customers/' . $dto->customerId,
            [
                RequestOptions::JSON => [
                    'JointOwner' => [
                        'lastName' => $dto->lastName,
                        'firstName' => $dto->firstName,
                        'middleName' => $dto->middleName,
                        'phone' => ltrim($dto->phone, '+'),
                        'birthDate' => $dto->birthDate?->toDateString(),
                        'email' => $dto->email,
                        'inn' => $dto->inn,
                        'citizenship' => $dto->citizenship,
                        'familyStatus' => $dto->familyStatus ? [
                            'code' => $dto->familyStatus->value,
                            'name' => $dto->familyStatus->label,
                        ] : null,
                        'part' => $dto->part,
                        'roleCode' => [
                            'code' => $dto->role->value,
                            'name' => $dto->role->label,
                        ],
                        'ownerType' => [
                            'code' => $dto->ownerType->value,
                            'name' => $dto->ownerType->label,
                        ],
                    ],
                    'Annotations' => $data,
                ],
            ],
        );
    }

    /**
     * @throws BadRequestException
     * @throws NotFoundException
     */
    public function setDemandJointOwnerMeetings(JointOwnerMeetingDto $dto): array
    {
        return $this->request(
            'PUT',
            'JointOwners/' . $dto->id,
            [
                RequestOptions::JSON => [
                    'id' => $dto->id,
                    'part' => $dto->part,
                    'roleCode' => [
                        'code' => $dto->role->value,
                        'name' => $dto->role->label,
                    ],
                    'ownerType' => [
                        'code' => $dto->ownerType->value,
                        'name' => $dto->ownerType->label,
                    ],
                    'ownerTypeComment' => $dto->ownerTypeComment,
                    'addressCourier' => $dto->addressCourier,
                    'MeetingDate' => $dto->meetingDate->toDateTimeString(),
                ],
            ],
        );
    }

    /**
     * @throws BadRequestException
     * @throws NotFoundException
     */
    public function storeHypothec($body): array
    {
        return $this->request(
            'POST',
            'Demand',
            [
                RequestOptions::JSON => $body
            ],
        );
    }

    /**
     * @throws BadRequestException
     * @throws NotFoundException
     */
    public function getDemandHypothec(string $demandId): array
    {
        return $this->request(
            'GET',
            "Surveys/demands/$demandId"
        );
    }

    /**
     * @param CreateCourierAddressDto $dto
     * @return array
     * @throws BadRequestException
     * @throws NotFoundException
     */
    public function setCourierAddress(CreateCourierAddressDto $dto): array
    {
        return $this->request(
            'PUT',
            'JointOwners/' . $dto->jointOwnerId,
            [
                RequestOptions::JSON => [
                    'Id' => $dto->id,
                    'cityCourier' => $dto->city,
                    'AddressCourier' => $dto->address,
                    'description' => $dto->description,
                ],
            ],
        );
    }

    /**
     * @throws BadRequestException
     * @throws NotFoundException
     */
    public function deleteJointOwner(string $leadId, string $jointOwnerId): array
    {
        return $this->request('DELETE', "JointOwners/$jointOwnerId/leads/$leadId");
    }

    /**
     * @throws BadRequestException
     * @throws NotFoundException
     */
    public function deleteDemandJointOwner(string $demandId, string $jointOwnerId): array
    {
        return $this->request('DELETE', "demands/$demandId/jointowners/$jointOwnerId");
    }

    /**
     * @throws BadRequestException
     * @throws NotFoundException
     */
    public function emailCheck(string $email)
    {
        return $this->request('GET', "Customers/$email/IsExistEmail");
    }

    /**
     * @throws BadRequestException
     * @throws NotFoundException
     */
    public function setJointOwnerCustomer(string $leadId, string $customerId): array
    {
        return $this->request('POST', "JointOwners/leads/$leadId/customers/$customerId", [
            RequestOptions::JSON => [
                'JointOwner' => [
                    'id' => $customerId
                ],
            ],
        ]);
    }

    /**
     * @throws BadRequestException
     * @throws NotFoundException
     */
    public function setJointOwnerByDefault(string $leadId, string $customerId): array
    {
        return $this->request('POST', "JointOwners/leads/$leadId/customers/$customerId", [
            RequestOptions::JSON => [
                'JointOwner' => [
                    'OwnerType' => [
                        'code' => '-1'
                    ]
                ],
                'part' => ''
            ],
        ]);
    }

    /**
     * @throws BadRequestException
     * @throws NotFoundException
     */
    public function setJointOwnerWithBody(string $leadId, string $customerId, array $body): array
    {
        return $this->request('POST', "JointOwners/leads/$leadId/customers/$customerId", [
            RequestOptions::JSON => $body,
        ]);
    }

    /**
     * @throws BadRequestException
     * @throws NotFoundException
     * @throws PhoneExistException
     */
    public function setJointOwnerLead(string $leadId, CreateJointOwnerLeadDto $leadDto): array
    {
        $body = ['JointOwner' => []];

        if (isset($leadDto->ownerType?->value)) {
            $body['JointOwner']['ownerType']['code'] = $leadDto->ownerType->value;
        }
        if (isset($leadDto->firstName)) {
            $body['JointOwner']['FirstName'] = $leadDto->firstName;
        }
        if (isset($leadDto->lastName)) {
            $body['JointOwner']['LastName'] = $leadDto->lastName;
        }
        if (isset($leadDto->middleName)) {
            $body['JointOwner']['MiddleName'] = $leadDto->middleName;
        }
        if (isset($leadDto->gender)) {
            $body['JointOwner']['GenderCode']['code'] = $leadDto->gender;
        }
        if (isset($leadDto->phone)) {
            $body['JointOwner']['Phone'] = $leadDto->phone;
        }
        if (isset($leadDto->email)) {
            $body['JointOwner']['Email'] = $leadDto->email;
        }
        if (isset($leadDto->birthDate)) {
            $body['JointOwner']['BirthDate'] = $leadDto->birthDate;
        }
        if (isset($leadDto->inn)) {
            $body['JointOwner']['inn'] = $leadDto->inn;
        }
        if (isset($leadDto->snils)) {
            $body['JointOwner']['snils'] = $leadDto->snils;
        }
        if (isset($leadDto->role?->value) && $leadDto->role?->value == 8) {
            $body['JointOwner']['roleCode']['code'] = $leadDto->role->value;
        }
        if (isset($leadDto->married) && $leadDto->married) {
            $body['JointOwner']['familyStatus']['code'] = 2;
        } elseif (isset($leadDto->married) && !$leadDto->married) {
            $body['JointOwner']['familyStatus']['code'] = 1;
        }
        if (isset($leadDto->isRus)) {
            $body['JointOwner']['citizenShip'] = $leadDto->isRus;
        }
        if (isset($leadDto->part)) {
            $body['JointOwner']['part'] = $leadDto->part;
        }

        $request = $this->request('POST', "JointOwners/leads/$leadId/customers", [
            RequestOptions::JSON => $body
        ]);

        if (($request['TypeMessage'] ?? null) == 3) {
            throw new PhoneExistException();
        } else {
            return $request;
        }
    }

    /**
     * @throws BadRequestException
     * @throws NotFoundException
     * @throws PhoneExistException
     */
    public function updateJointOwnerLead(string $leadId, string $jointOwnerId, UpdateJointOwnerDto $leadDto): array
    {
        $body = ['JointOwner' => []];

        if (isset($leadDto->ownerType?->value)) {
            $body['JointOwner']['ownerType']['code'] = $leadDto->ownerType->value;
        }
        if (isset($leadDto->firstName)) {
            $body['JointOwner']['FirstName'] = $leadDto->firstName;
        }
        if (isset($leadDto->lastName)) {
            $body['JointOwner']['LastName'] = $leadDto->lastName;
        }
        if (isset($leadDto->middleName)) {
            $body['JointOwner']['MiddleName'] = $leadDto->middleName;
        }
        if (isset($leadDto->gender)) {
            $body['JointOwner']['GenderCode']['code'] = $leadDto->gender;
        }
        if (isset($leadDto->phone)) {
            $body['JointOwner']['Phone'] = $leadDto->phone;
        }
        if (isset($leadDto->email)) {
            $body['JointOwner']['Email'] = $leadDto->email;
        }
        if (isset($leadDto->birthDate)) {
            $body['JointOwner']['BirthDate'] = $leadDto->birthDate;
        }
        if (isset($leadDto->inn)) {
            $body['JointOwner']['inn'] = $leadDto->inn;
        }
        if (isset($leadDto->snils)) {
            $body['JointOwner']['snils'] = $leadDto->snils;
        }
        if (isset($leadDto->role?->value) && $leadDto->role?->value == 8) {
            $body['JointOwner']['roleCode']['code'] = $leadDto->role->value;
        }
        if (isset($leadDto->married)) {
            $body['JointOwner']['familyStatus']['code'] = $leadDto->married ? 2 : 1;
        }
        if (isset($leadDto->isRus)) {
            $body['JointOwner']['citizenShip'] = $leadDto->isRus;
        }
        if (isset($leadDto->part)) {
            $body['JointOwner']['part'] = $leadDto->part;
        }

        $request = $this->request('POST', "JointOwners/leads/$leadId/customers/$jointOwnerId", [
            RequestOptions::JSON => $body
        ]);

        if (($request['TypeMessage'] ?? null) == 3) {
            throw new PhoneExistException();
        } else {
            return $request;
        }
    }

    /**
     * @throws BadRequestException
     * @throws NotFoundException
     */
    public function putCustomer(string $customerId, $data): array
    {
        return $this->request('PUT', "customer/$customerId", [
            RequestOptions::JSON => $data
        ]);
    }

    /**
     * @throws BadRequestException
     * @throws NotFoundException
     */
    public function getCustomerDocuments(string $id): array
    {
        return $this->request('GET', "Documents/List/2/$id");
    }

    /**
     * @throws BadRequestException
     * @throws NotFoundException
     */
    public function getContractDocuments(string $id): array
    {
        return $this->request('GET', "Documents/List/3/$id");
    }

    /**
     * @throws BadRequestException
     * @throws NotFoundException
     */
    public function getContractDocumentsWithCode(string $code, string $id): array
    {
        return $this->request('GET', "Documents/List/$code/$id");
    }

    /**
     * @throws BadRequestException
     * @throws NotFoundException
     */
    public function putAccounts(string $accountId, $data): array
    {
        return $this->request('PUT', "Accounts/$accountId/Update", [
            RequestOptions::JSON => $data
        ]);
    }

    public function createAccount(array $data): array
    {
        return $this->requestCreate(
            'POST',
            "/api/Accounts/Create",
            [
                RequestOptions::JSON => $data
            ]
        );
    }

    /**
     * @throws NotFoundException
     * @throws BadRequestException
     */
    public function putDemand(string $id, array $data): array
    {
        return $this->request(
            'PUT',
            "/api/Demand/$id",
            [
                RequestOptions::JSON => $data
            ]
        );
    }

    /**
     * @throws NotFoundException
     * @throws BadRequestException
     */
    public function putJointOwner(string $jointOwnerId, array $data): array
    {
        return $this->request(
            'PUT',
            "/api/JointOwners/$jointOwnerId",
            [
                RequestOptions::JSON => $data
            ]
        );
    }

    /**
     * @throws NotFoundException
     * @throws BadRequestException
     */
    public function getDemandByUserId(string $id, string $userId): array
    {
        return $this->request(
            'GET',
            'Demand/' . $id . '/Customers/' . $userId,
        );
    }

    /**
     * @throws BadRequestException
     * @throws NotFoundException
     */
    public function getJointOwnerDocuments(string $id): array
    {
        return $this->request('GET', "Documents/List/10035/$id");
    }

    /**
     * @throws BadRequestException
     * @throws NotFoundException
     */
    public function getDocumentById(string $id): array
    {
        return $this->request('GET', "Documents/$id");
    }

    public function getClaimByIds(array $claimIds): array
    {
        return $this->request(
            'POST',
            'Claims',
            [
                RequestOptions::JSON => $claimIds,
            ]
        );
    }

    /**
     * @throws BadRequestException
     * @throws NotFoundException
     */
    public function uploadDocumentByUserId(string $userCrmId, UploadUserDocumentDto $dto): array
    {
        return $this->request(
            'POST',
            'Feedback/Customers/' . $userCrmId,
            [
                RequestOptions::JSON => [
                    'ObjectId' => $userCrmId,
                    'ObjectTypeCode' => 2,
                    'Annotations' => [
                        [
                            'Name' => "Document-$dto->fileName",
                            'FileName' => $dto->fileName,
                            'DocumentType' => [
                                'code' => $dto->type->value,
                            ],
                            'MimeType' => $dto->mimeType,
                            'DocumentBody' => $dto->body,
                            'IsCustomerAvailable' => true,
                        ]
                    ],
                ],
            ],
        );
    }

    /**
     * @throws BadRequestException
     * @throws NotFoundException
     */
    public function uploadSaleFile(array $data, string $userId): array
    {
        return $this->request(
            'POST',
            'Feedback/customers/' . $userId,
            [
                RequestOptions::JSON => $data
            ],
        );
    }

    /**
     * @throws BadRequestException
     * @throws NotFoundException
     */
    public function sendSmsCode(array $data, string $userId): array
    {
        return $this->request(
            'POST',
            'Feedback/customers/' . $userId,
            [
                RequestOptions::JSON => $data
            ],
        );
    }

    public function getExistUnread(string $customerId):bool
    {
        return $this->request(
            'GET',
            "Customers/$customerId/ExistUnread",
        );
    }

    /**
     * @throws NotFoundException
     * @throws BadRequestException
     */
    public function orderCallback(User $user, string $phone): array
    {
        return $this->request(
            'POST',
            'Feedback/Customers/' . $user->crm_id,
            [
                RequestOptions::JSON => [
                    'ObjectId' => $user->crm_id,
                    'ObjectTypeCode' => 2,
                    'AppealType' => 'Обращение',
                    'AppealText' => ltrim($phone, '+'),
                    'State' => [
                        'code' => 0,
                        'name' => 'Открыть',
                    ],
                    'IsCallBack' => true,
                    'WhoShouldCallBack' => [
                        'code' => 1,
                        'name' => 'Управляющая компания',
                    ],
                ],
            ]
        );
    }

    /**
     * @param ClaimImageDto[] $imageDtos
     *
     * @return array
     */
    private function getClaimImagesData(array $imageDtos): array
    {
        $photos = [];
        foreach ($imageDtos as $imageDto) {
            $photos[] = [
                'Name' => $imageDto->name,
                'FileName' => $imageDto->fileName,
                'DocumentType' => [
                    'code' => $imageDto->documentType->value,
                ],
                'IsCustomerAvailable' => $imageDto->isCustomerAvailable,
                'DocumentBody' => base64_encode($imageDto->documentBody),
                'MimeType' => $imageDto->mimeType,
                'sender' => $imageDto->sender
            ];
        }

        return $photos;
    }

    private function getSalesDocumentsData(SaveClaimRequestDto | SaveClaimSosDto | SaveClaimWarrantyDto $dto): array
    {
        $photos = [];
        foreach ($dto->imageDtos as $imageDto) {
            $photos[] = [
                'Name' => $imageDto->name,
                'FileName' => $imageDto->fileName,
                'DocumentType' => [
                    'code' => $imageDto->documentType->value,
                ],
                'IsCustomerAvailable' => $imageDto->isCustomerAvailable,
                'DocumentBody' => base64_encode($imageDto->documentBody),
                'MimeType' => $imageDto->mimeType,
            ];
        }

        return $photos;
    }

    public function getPasses(string $accountNumber): array
    {
        return $this->request(
            'GET',
            'Passes/PersonalAccounts/'. $accountNumber
        );
    }

    public function cancelPass(string $passId): array
    {
        return $this->request(
            'PUT',
            '/api/Passes/' . $passId . '/Cancel'
        );
    }

    public function storePass(string $accountNumber, SavePassDto $dto): array
    {
        $body = [];
        $dto->passType ? $body['passOptionCode'] = [
            'code' => $dto->passType->value,
            'name' => $dto->passType->label,
        ]:null;
        $dto->assignment ? $body['passTypeCode'] = [
            'code' => $dto->assignment->value,
            'name' => $dto->assignment->label,
        ]:null;
        $dto->carType ? $body['carTypeCode'] = [
            'code' => $dto->carType->value,
            'name' => $dto->carType->label,
        ]:null;
        $dto->carNumber ? $body['carNumber'] = $dto->carNumber:null;
        $dto->name ? $body['fullNameGuest'] = $dto->name:null;
        $dto->arrivalDate ? $body['arrivalDate'] = $dto->arrivalDate->toDateString():null;
        $dto->startDate ? $body['passStartDate'] = $dto->startDate->toDateString():null;
        $dto->endDate ? $body['passEndDate'] = $dto->endDate->toDateString():null;
        $dto->comment ? $body['description'] = $dto->comment:null;

        return $this->request(
            'POST',
            '/api/Passes/Create/PersonalAccounts/'. $accountNumber,
            [
                RequestOptions::JSON => $body,
            ]
        );
    }

    public function inspectionDate(string $articleId): array
    {
        return $this->request(
            'GET',
            "/api/bn/articles/$articleId/inspectionsPeriods?freeOnly=true",
        );
    }

    public function inspection(string $articleId): array
    {
        return $this->request(
            'GET',
            "/api/bn/Inspections/$articleId",
        );
    }

    public function inspectionDateTimes(string $articleId, string $date): array
    {
        return $this->request(
            'GET',
            "/api/bn/articles/$articleId/inspectionsDays/$date/times",
        );
    }

    public function createInspection(array $data): array
    {
        return $this->request(
            'POST',
            "/api/bn/Inspections/",
            [
                RequestOptions::JSON => $data
            ]
        );
    }

    /**
     * @throws NotFoundException
     * @throws BadRequestException
     */
    public function updateInspection(array $data): array
    {
        return $this->request(
            'PUT',
            "/api/bn/Inspections/".$data['id'],
            [
                RequestOptions::JSON => $data
            ]
        );
    }

    /**
     * @throws NotFoundException
     * @throws BadRequestException
     */
    public function checkInn(int $inn): array
    {
        return $this->request(
            'GET',
            "/api/Accounts/$inn"
        );
    }

    /**
     * @throws BadRequestException
     * @throws NotFoundException
     */
    private function request(string $method, string $uri, array $options = [], int $timeout = 60)
    {
        try {
            //TODO: remove when CRM will be stable
            $options[RequestOptions::VERIFY] = false;
            $options[RequestOptions::TIMEOUT] = 120;

            $start = microtime(true);
            $response = $this->httpClient->request($method, $uri, $options);
            $time_elapsed_secs = microtime(true) - $start;

            if (isset($_SERVER)) {
                $request_url = $_SERVER['REQUEST_URI'] ?? null;
            } else {
                $request_url = '';
            }

            $str_options = json_encode($options);
            $str_response = json_encode(json_decode($response->getBody(), true));

            $text = date('Y-m-d H:i:s').' | request: '.
                $request_url. " | CRM request: $uri | " . "options: $str_options | " .
                "response: $str_response | " . round($time_elapsed_secs, 2). " s";

            $this->getLogger()->info($text);

            if ($response->getStatusCode() === 201) {
                return null;
            }

            return json_decode($response->getBody(), true);
        } catch (Exception | RequestException | GuzzleException $e) {
            if ($e->getCode() === 404) {
                throw new NotFoundException($e->getResponse()->getBody()->getContents(), $e->getCode(), $e);
            } elseif ($e->getCode() === 400) {
                throw new BadRequestException($e->getResponse()->getBody()->getContents(), $e->getCode(), $e);
            }

            throw new RuntimeException($e->getMessage(), $e->getCode(), $e);
        }
    }

    private function requestCreate(string $method, string $uri, array $options = [], int $timeout = 60)
    {
        try {
            //TODO: remove when CRM will be stable
            $options[RequestOptions::VERIFY] = false;
            $options[RequestOptions::TIMEOUT] = 180;

            $start = microtime(true);
            $response = $this->httpClient->request($method, $uri, $options);
            $time_elapsed_secs = microtime(true) - $start;

            if (isset($_SERVER)) {
                $request_url = $_SERVER['REQUEST_URI'] ?? null;
            } else {
                $request_url = '';
            }

            $str_options = json_encode($options);
            $str_response = json_encode(json_decode($response->getBody(), true));

            $text = date('Y-m-d H:i:s').' | request: '.
                $request_url. " | CRM request: $uri | " . "options: $str_options | " .
                "response: $str_response | " . round($time_elapsed_secs, 2). " s";

            $this->getLogger()->info($text);


            return json_decode($response->getBody(), true);
        } catch (Exception | RequestException | GuzzleException $e) {
            if ($e->getCode() === 404) {
                throw new NotFoundException($e->getResponse()->getBody()->getContents(), $e->getCode(), $e);
            } elseif ($e->getCode() === 400) {
                throw new BadRequestException($e->getResponse()->getBody()->getContents(), $e->getCode(), $e);
            }

            throw new RuntimeException($e->getMessage(), $e->getCode(), $e);
        }
    }

    private function getLogger()
    {
        $dateString = now()->format('d_m_Y');
        $filePath = 'crm_requests_' . $dateString . '.log';
        $dateFormat = "m/d/Y H:i:s";
        $output = "[%datetime%] %channel%.%level_name%: %message%\n";
        $formatter = new LineFormatter($output, $dateFormat);
        $stream = new StreamHandler(storage_path('logs/' . $filePath), Logger::DEBUG);
        umask(0002);
        $stream->setFormatter($formatter);
        $processId = Str::random(5);
        $logger = new Logger($processId);
        $logger->pushHandler($stream);

        return $logger;
    }
}
