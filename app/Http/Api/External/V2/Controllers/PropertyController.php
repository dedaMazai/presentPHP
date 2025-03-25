<?php

namespace App\Http\Api\External\V2\Controllers;

use App\Http\Resources\V2\FavoritePropertyCollection;
use App\Models\User\FavoriteProperty;
use App\Services\DynamicsCrm\Exceptions\BadRequestException;
use App\Services\V2\Sales\Property\PropertyRepository;
use Illuminate\Auth\AuthenticationException;
use Psr\SimpleCache\InvalidArgumentException;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class PropertyController
 *
 * @package App\Http\Api\V2\Controllers
 */
class PropertyController extends Controller
{
    public function __construct(
        private PropertyRepository $propertyRepository,
    ) {
    }

    /**
     * @throws AuthenticationException
     * @throws BadRequestException
     * @throws InvalidArgumentException
     */
    public function getFavorites(): Response
    {
        /** @var FavoriteProperty[] $favoriteProperties */
        $favoriteProperties = $this->getAuthUser()->favoriteProperties()->get();

        $propertyUrls = [];
        foreach ($favoriteProperties as $favoriteProperty) {
            $propertyUrls[$favoriteProperty->property_crm_id] = $favoriteProperty->url;
        }

        $properties = $this->propertyRepository->getAllCachedByIds(array_keys($propertyUrls));

        foreach ($properties as &$rawProperty) {
            if ($propertyUrls[$rawProperty->getId()]) {
                $rawProperty = $rawProperty->setUrl($propertyUrls[$rawProperty->getId()]);
            }
        }

        return response()->json(new FavoritePropertyCollection($properties));
    }
}
