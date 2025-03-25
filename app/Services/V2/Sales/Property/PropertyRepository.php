<?php

namespace App\Services\V2\Sales\Property;

use App\Models\Banks;
use App\Models\EscrowBanks;
use App\Models\Image;
use App\Models\Project\Project;
use App\Models\Sales\CharacteristicSale\CharacteristicSaleType;
use App\Models\Sales\Property\ObjectPlan;
use App\Models\V2\Sales\LetterOfCreditBank;
use App\Models\V2\Sales\Property\Property;
use App\Models\V2\Sales\Property\PropertyImage;
use App\Models\V2\Sales\Property\PropertyReserveDuration;
use App\Models\V2\Sales\Property\PropertyReserveType;
use App\Models\V2\Sales\Property\PropertyStatus;
use App\Models\V2\Sales\Property\PropertyType;
use App\Models\V2\Sales\Property\PropertyVariant;
use App\Services\Contract\ContractRepository;
use App\Services\DynamicsCrm\DynamicsCrmClient;
use App\Services\DynamicsCrm\Exceptions\BadRequestException;
use App\Services\DynamicsCrm\Exceptions\NotFoundException;
use App\Services\Sales\AddressRepository;
use App\Services\Sales\CharacteristicSaleRepository;
use App\Services\V2\Sales\Property\Dto\PropertyBookingDto;
use Carbon\Carbon;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Str;
use Psr\SimpleCache\CacheInterface;
use Psr\SimpleCache\InvalidArgumentException;

/**
 * Class PropertyRepository
 *
 * @package App\Services\V2\Sales\Property
 */
class PropertyRepository
{
    public function __construct(
        private DynamicsCrmClient $dynamicsCrmClient,
        private ContractRepository $contractRepository,
        private CharacteristicSaleRepository $characteristicSaleRepository,
        private AddressRepository $addressRepository,
        private CacheInterface $cache,
    ) {
    }

    /**
     * @throws BadRequestException
     * @throws NotFoundException
     */
    public function getById(string $id): Property
    {
        $data = $this->dynamicsCrmClient->getPropertyById($id);

        return $this->makeProperty($data);
    }

    /**
     * @throws BadRequestException
     * @throws NotFoundException
     */
    public function getPropertyForBooking(string $id): PropertyBookingDto
    {
        $data = $this->dynamicsCrmClient->getPropertyById($id);

        return $this->makePropertyBookingDto($data);
    }

    /**
     * @return Property[]
     * @throws InvalidArgumentException
     * @throws BadRequestException
     */
    public function getAllCachedByIds(array $ids): array
    {
        $data = [];
        foreach ($ids as $id) {
            $property = $this->cache->get($this->key($id));
            if ($property === null) {
                try {
                    $propertyData = $this->dynamicsCrmClient->getPropertyById($id);
                    $property = $this->makeProperty($propertyData);
                    $this->cacheProperty($property);
                } catch (NotFoundException) {
                }
            }

            if ($property) {
                $data[] = $property;
            }
        }

        return $data;
    }

    private function makeProperty(array $data): Property
    {
        $plan = null;
        if (isset($data['planUrl'])) {
            $plan = $this->makePropertyImage($data['planUrl']);
        }

        $images = [];
        if (isset($data['images'])) {
            foreach ($data['images'] as $image) {
                $images[] = $this->makePropertyImage($image);
            }
        }

        $contracts = [];
        if (isset($data['contracts'])) {
            foreach ($data['contracts'] as $contract) {
                $contracts[] = $this->contractRepository->makeContract($contract);
            }
        }

        $plans = $this->makeObjectPlan($data);

        $instalments = $this->characteristicSaleRepository->getAllByPropertyId(
            propertyId: $data['id'],
            type: CharacteristicSaleType::instalments(),
        );

        $finishes = $this->characteristicSaleRepository->getAllByPropertyId(
            propertyId: $data['id'],
            type: CharacteristicSaleType::finishing(),
        );

        $nameLk = $this->getNameLk($data['articleTypeCode']['code'], $data['articleVariantTm1Code']['code']);

        $escrowBanksArr = null;
        if (($data['escrowBankId']??null) != null) {
            $bank = Banks::where('bank_id', '=', $data['escrowBankId'])->first();

            if ($bank != null) {
                $escrowBanks = EscrowBanks::where('escrow_bank_id', '=', $bank->id)->first();
            } else {
                $escrowBanks = null;
            }

            if ($escrowBanks != null) {
                foreach ($escrowBanks->letterofbank_ids as $letterofbank_id) {
                    $bank = Banks::find($letterofbank_id);

                    if ($bank != null) {
                        $escrowBanksArr[] = new LetterOfCreditBank(
                            id: $bank->bank_id,
                            name: $bank->name,
                            image: Image::find($bank->image_id)?->url
                        );
                    }
                }
            } else {
                $escrowBanksArr = null;
            }
        } else {
            $bank = Banks::where('bank_id', '=', null)->first();
            $escrowBanks = EscrowBanks::where('escrow_bank_id', '=', $bank->id)->first();

            if ($escrowBanks != null) {
                foreach ($escrowBanks->letterofbank_ids as $letterofbank_id) {
                    $bank = Banks::find($letterofbank_id);

                    if ($bank != null) {
                        $escrowBanksArr[] = new LetterOfCreditBank(
                            id: $bank->bank_id,
                            name: $bank->name,
                            image: Image::find($bank->image_id)?->url
                        );
                    }
                }
            } else {
                $escrowBanksArr = null;
            }
        }

        $inspectionId = null;
        if (Str::contains(Request::fullUrl(), 'inspect')) {
            try {
                $bn = $this->dynamicsCrmClient->inspection($data['id']);
            } catch (\Throwable $exception) {
                $bn = null;
            }

            if ($bn != null) {
                $bn = collect($bn)->where('type_id', '=', 1)->sortByDesc('createdAt')->first();
                $inspectionId = $bn->statusId != 7 || $bn->statusId != 5 ? null : $bn->id;
            } else {
                $inspectionId = null;
            }
        }

        return new Property(
            id: $data['id'],
            code: $data['code'],
            address: $this->addressRepository->makeAddress($data['address']),
            project: Project::whereJsonContains('crm_ids', $data['address']['gk']['id'])->first(),
            status: PropertyStatus::tryFrom($data['status']['code'] ?? ''),
            number: $data['number'],
            layoutId: $data['layout']['id'],
            layoutNumber: $data['layout']['number'],
            floor: $data['floor'],
            platformNumber: $data['platformNumber'] ?? null,
            rooms: $data['rooms'],
            quantity: $data['quantity'] ?? null,
            spaceBtiWoBalcony: $data['spaceBtiWoBalcony'] ?? null,
            spaceBti: $data['spaceBti'] ?? null,
            spaceDeviation: $data['spaceDeviation'] ?? null,
            payDeviation: $data['payDeviation'] ?? null,
            cost: $data['cost'] ?? null,
            price: $data['price'] ?? null,
            unitCode: $data['unitCode'] ?? null,
            bookingEndAt: isset($data['dateTimeEnd']) ? new Carbon($data['dateTimeEnd']) : null,
            type: PropertyType::tryFrom($data['articleTypeCode']['code'] ?? ''),
            subtype: $data['articleSubType'] ?? null,
            plan: $plan,
            variant: PropertyVariant::tryFrom($data['articleVariantTm1Code']['code'] ?? ''),
            images: $images,
            contracts: $contracts,
            isEscrow: $data['isEscrow'] ?? null,
            isReservationPaid: $data['isReservationPaid'] ?? null,
            escrowBankId: $data['escrowBankId'] ?? null,
            escrowBankName: isset($data['escrowBankName']) ? Banks::where('bank_id', '=', $data['escrowBankId'])
                ->first()?->name:null,
            reserveDuration: PropertyReserveDuration::tryFrom($data['reserveDuration']['code'] ?? ''),
            reserveType: PropertyReserveType::tryFrom($data['reserveType']['code'] ?? ''),
            sumDiscount: $data['sumDiscount'] ?? null,
            isReadyForTransfer: $data['isReadyForTransfer'] ?? null,
            instalments: $instalments,
            finishes: $finishes,
            priceDiscount: $data['sumDiscount'] != ($data['price'] ?? null) ? $data['sumDiscount'] : null,
            planObject: $data['imageUrlSite'] ?? null,
            freeBookingPeriod: [
                'type' => PropertyReserveType::tryFrom($data['reserveType']['code'] ?? ''),
                'duration' => $data['reserveDuration']['code']
            ],
            nameLk: $nameLk,
            isBookingAvailible: $data['status']['code'] == 4,
            plans: $plans,
            articleVariantTm1Code: $data['articleVariantTm1Code']['code'] ?? null,
            addressPost: $data['address']['addressPost']??null,
            letterOfCreditBanks: $escrowBanksArr,
            roomId: $data['bnId'] ?? null,
            inspectionId: $inspectionId,
            articleStatusReception: $data['articleStatusReception']['code'] ?? null
        );
    }

    private function makePropertyImage(array $data): PropertyImage
    {
        return new PropertyImage(
            name: $data['image'] ?? null,
            url: $data['src'] ?? null,
        );
    }

    private function makeObjectPlan(array $data): ObjectPlan
    {
        return new ObjectPlan(
            common: $data['planUrl']['url'] ?? null,
            object: $data['imageUrlSite'] ?? null,
            floor: $data['imageUrlFloorSite'] ?? null
        );
    }

    private function makePropertyBookingDto(array $data): PropertyBookingDto
    {
        return new PropertyBookingDto(
            id: $data['id'],
            project: Project::whereJsonContains('crm_ids', $data['address']['gk']['id'])->first(),
            status: PropertyStatus::tryFrom($data['status']['code'] ?? ''),
            isEscrow: $data['isEscrow'] ?? null,
        );
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

    private function key(string $id): string
    {
        return "properties.{$id}";
    }

    /**
     * @throws InvalidArgumentException
     */
    private function cacheProperty(Property $property): void
    {
        $this->cache->set($this->key($property->getId()), $property, now()->addDay());
    }
}
