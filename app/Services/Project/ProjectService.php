<?php

namespace App\Services\Project;

use App\Models\Project\Project;
use App\Models\Sales\Property\PropertyStatus;
use App\Models\Sales\Property\PropertyType;
use App\Services\DynamicsCrm\Exceptions\BadRequestException;
use App\Services\DynamicsCrm\Exceptions\NotFoundException;
use Exception;
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use Psr\SimpleCache\InvalidArgumentException;
use RuntimeException;

/**
 * Class ProjectService
 *
 * @package App\Services\Project
 */
class ProjectService
{
    private HttpClient $httpClient;

    public function __construct(private ProjectAddressRepository $addressRepository, string $baseUri)
    {
        $client = new HttpClient([
            'base_uri' => $baseUri,
        ]);

        $this->httpClient = $client;
    }

    /**
     * @throws NotFoundException
     * @throws InvalidArgumentException
     * @throws BadRequestException
     */
    public function updateMinPropertyPrice(Project $project): void
    {
        $projectAddresses = $this->addressRepository->getAllByProject($project);

        $minPrice = 0;
        $maxPrice = 0;

        foreach ($projectAddresses as $projectAddress) {
            $properties = $this->getPropertiesByAddressId($projectAddress->getId());
            foreach ($properties as $property) {
                if (isset($property['articleType']) &&
                    $property['articleType'] == PropertyType::living()->value &&
                    $property['statusCode'] == PropertyStatus::free()->value
                ) {
                    $sum = $property['cost'] * $property['quantity'];
                    if (!$minPrice || $sum < $minPrice) {
                        $minPrice = $sum;
                    }

                    if (!$minPrice || $sum > $maxPrice) {
                        $maxPrice = $sum;
                    }
                }
            }
        }

        $project->min_property_price = $minPrice;
        $project->max_property_price = $maxPrice;
        $project->save();
    }

    private function getPropertiesByAddressId(string $addressId): array
    {
        return $this->request(
            'GET',
            $addressId,
        );
    }

    private function request(string $method, string $uri, array $options = []): array
    {
        try {
            $response = $this->httpClient->request($method, $uri, $options);

            return json_decode($response->getBody(), true);
        } catch (Exception | RequestException | GuzzleException $e) {
            throw new RuntimeException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
