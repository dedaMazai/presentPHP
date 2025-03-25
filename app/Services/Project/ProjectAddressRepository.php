<?php

namespace App\Services\Project;

use App\Models\Project\Project;
use App\Models\Project\ProjectAddress;
use App\Services\DynamicsCrm\DynamicsCrmClient;
use App\Services\DynamicsCrm\Exceptions\BadRequestException;
use App\Services\DynamicsCrm\Exceptions\NotFoundException;
use Carbon\Carbon;
use Exception;
use Psr\SimpleCache\CacheInterface;
use Psr\SimpleCache\InvalidArgumentException;

/**
 * Class ProjectAddressRepository
 *
 * @package App\Services\Project
 */
class ProjectAddressRepository
{
    public function __construct(
        private DynamicsCrmClient $dynamicsCrmClient,
        private CacheInterface $cache,
    ) {
    }

    /**
     * @param Project $project
     *
     * @return ProjectAddress[]
     * @throws InvalidArgumentException
     */
    public function getAllByProject(Project $project): array
    {
        $data = [];
        foreach ($project->crm_ids as $crmId) {
            $projectAddresses = $this->cache->get($this->key($crmId));
            if (!$projectAddresses) {
                try {
                    $addressesList = $this->dynamicsCrmClient->getAddressesByProjectCrmId($crmId);
                } catch (Exception) {
                    $addressesList = [
                        'addressList' => [],
                    ];
                }
                $projectAddresses = $addressesList['addressList'];

                $this->cacheProjectAddresses($crmId, $projectAddresses);
            }

            $data = array_merge($data, $projectAddresses);
        }

        return array_map(fn($data) => $this->makeProjectAddress($data), $data);
    }

    private function makeProjectAddress(array $data): ProjectAddress
    {
        return new ProjectAddress(
            id: $data['id'],
            post: $data['post'] ?? null,
            prefixUK: $data['prefixUK'] ?? null,
            addressPostShort: $data['addressPostShort'] ?? null,
            ukId: $data['classifierUKId'] ?? null,
            ukName: $data['classifierUKIdName'] ?? null,
            addressNumber: $data['addressNumber'] ?? null,
            floorCount: $data['floorCount'] ?? null,
            sectionCount: $data['sectionCount'] ?? null,
            dateExploitation: isset($data['dateExploitation']) ? new Carbon($data['dateExploitation']) : null,
        );
    }

    private function key(string $projectCrmId): string
    {
        return "projects.{$projectCrmId}.addresses";
    }

    /**
     * @param string           $projectCrmId
     * @param ProjectAddress[] $projectAddresses
     *
     * @return void
     * @throws InvalidArgumentException
     */
    private function cacheProjectAddresses(string $projectCrmId, array $projectAddresses): void
    {
        $this->cache->set($this->key($projectCrmId), $projectAddresses, now()->addDay());
    }
}
