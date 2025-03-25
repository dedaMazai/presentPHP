<?php

namespace App\Services\Sales;

use App\Models\Sales\Address\Address;

/**
 * Class AddressRepository
 *
 * @package App\Services\Sales
 */
class AddressRepository
{
    public function makeAddress(array $data): Address
    {
        return new Address(
            id: $data['id'],
            name: $data['name'],
            gkId: $data['gk']['id'],
            address: $data['addressPost'] ?? null,
            regionCode: $data['regioncode']['code'],
            regionName: $data['regioncode']['name'],
            website: $data['website'] ?? null,
            office: $data['office'] ?? null,
            latitude: $data['latitude'] ?? null,
            longitude: $data['longitude'] ?? null,
        );
    }
}
