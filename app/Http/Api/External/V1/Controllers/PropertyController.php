<?php

namespace App\Http\Api\External\V1\Controllers;

use App\Http\Resources\FavoritePropertyCollection;
use App\Models\User\FavoriteProperty;
use App\Services\DynamicsCrm\Exceptions\BadRequestException;
use App\Services\Sales\Property\PropertyRepository;
use App\Services\User\Exceptions\PropertyAlreadyInFavoritesException;
use App\Services\User\UserService;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Psr\SimpleCache\InvalidArgumentException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;

/**
 * Class PropertyController
 *
 * @package App\Http\Api\V1\Controllers
 */
class PropertyController extends Controller
{
    public function __construct(
        private PropertyRepository $propertyRepository,
        private UserService $userService,
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

    /**
     * @throws AuthenticationException
     * @throws ValidationException
     */
    public function addToFavorites(string $id, Request $request): Response
    {
        $this->validate($request, [
            'url' => 'required|string',
        ]);

        try {
            $this->userService->addPropertyToFavorites($this->getAuthUser(), $id, $request->input('url'));
        } catch (PropertyAlreadyInFavoritesException) {
            throw new ConflictHttpException('Property already in favorites.');
        }

        return $this->empty();
    }

    /**
     * @throws AuthenticationException
     */
    public function removeFromFavorites(string $id): Response
    {
        $this->userService->removePropertyFromFavorites($this->getAuthUser(), $id);

        return $this->empty();
    }
}
