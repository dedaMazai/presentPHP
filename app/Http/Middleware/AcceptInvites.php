<?php

namespace App\Http\Middleware;

use App\Models\Relationship\RelationshipInvite;
use App\Models\User\User;
use App\Services\DynamicsCrm\Exceptions\BadRequestException;
use App\Services\DynamicsCrm\Exceptions\NotFoundException;
use App\Services\RelationshipInvite\Exceptions\UnableToFindContractException;
use App\Services\RelationshipInvite\Exceptions\UnableToSetJointOwnerException;
use App\Services\RelationshipInvite\RelationshipInviteService;
use Closure;
use Exception;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class AcceptInvites
 *
 * @package App\Http\Middleware
 */
class AcceptInvites
{
    public function __construct(private RelationshipInviteService $relationshipInviteService)
    {
    }

    /**
     * @throws UnableToFindContractException
     * @throws UnableToSetJointOwnerException
     * @throws BadRequestException
     * @throws NotFoundException
     */
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::guest()) {
            /** @var User $user */
            $user = Auth::user();

            $relationshipInvites = RelationshipInvite::where(['phone' => $user->phone])
                ->unaccepted()
                ->get();
            foreach ($relationshipInvites as $relationshipInvite) {
                try {
                    $this->relationshipInviteService->accept($user, $relationshipInvite);
                } catch (Exception) {
                }
            }
        }

        return $next($request);
    }
}
