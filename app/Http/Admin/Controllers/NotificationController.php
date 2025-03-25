<?php

namespace App\Http\Admin\Controllers;

use App\Components\QueryBuilder\Filters\FiltersCreatedBetween;
use App\Http\Admin\Requests\SaveNotificationRequest;
use App\Jobs\SendPush;
use App\Models\Building\Building;
use App\Models\ClientRoleTypes;
use App\Models\Notification\Notification;
use App\Models\Notification\NotificationDestinationType;
use App\Models\Notification\NotificationType;
use App\Services\Action\ActionTypeService;
use App\Services\Notification\DestinationTypeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Response;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * Class NotificationController
 *
 * @package App\Http\Admin\Controllers
 */
class NotificationController extends Controller
{
    public function index(Request $request): Response
    {
        return inertia('Notifications/List', [
            'notifications' => QueryBuilder::for(Notification::class)
                ->allowedFilters([
                    'type',
                    'destination_type',
                    AllowedFilter::custom('creation_period', new FiltersCreatedBetween()),
                ])
                ->allowedSorts(['created_at', 'updated_at'])
                ->with(['action'])
                ->paginate(),
            'defaultSorter' => $request->input('sort'),
            'defaultFilters' => $request->input('filter'),
            'destinationTypes' => NotificationDestinationType::toArray(),
            'types' => NotificationType::clientTypesToArray(),
        ]);
    }

    public function create(
        ActionTypeService $actionTypeService,
        DestinationTypeService $destinationTypeService
    ): Response {
        $actionTypes = $actionTypeService->getAll();
        $destinationTypes = $destinationTypeService->getAvailableForAdmin();

        return inertia('Notifications/Create', [
            'types' => NotificationType::clientTypesToArray(),
            'destinationTypes' => $destinationTypes,
            'destinationTypesPayloadMap' => $destinationTypeService->getTypesPayloadParamOptions($destinationTypes),
            'actionTypes' => $actionTypes,
            'actionTypesPayloadMap' => $actionTypeService->getPayloadMapTypes($actionTypes),
            'ukBuildings' => Building::all(),
            'ukClientRoleTypes' => ClientRoleTypes::all(),
        ]);
    }

    public function store(SaveNotificationRequest $request): RedirectResponse
    {
        /** @var Notification $notification */
        $notification = Notification::create([
            'title' => $request->input('title'),
            'text' => $request->input('text'),
            'type' => $request->input('type'),
            'destination_type' => NotificationDestinationType::from($request->input('destination.type')),
            'destination_type_payload' => $request->input('destination.payload'),
        ]);

        if (!empty($request->has('action'))) {
            $notification->updateAction(
                type: $request->input('action.type') ?? '',
                payload: $request->input('action.payload') ?? []
            );
        }

        SendPush::dispatch($notification)->onQueue('send_push');

        return redirect()->route('notifications.show', [
            'id' => $notification->id,
        ]);
    }

    public function show(
        int $id,
        ActionTypeService $actionTypeService,
        DestinationTypeService $destinationTypeService
    ): Response {
        $actionTypes = $actionTypeService->getAll();
        $destinationTypes = $destinationTypeService->getAvailableForAdmin();
        return inertia('Notifications/Show', [
            'notification' => $this->findNotification($id),
            'types' => NotificationType::clientTypesToArray(),
            'destinationTypes' => $destinationTypes,
            'destinationTypesPayloadMap' => $destinationTypeService->getTypesPayloadParamOptions($destinationTypes),
            'actionTypes' => $actionTypes,
            'actionTypesPayloadMap' => $actionTypeService->getPayloadMapTypes($actionTypes),
            'ukBuildings' => Building::all(),
            'ukClientRoleTypes' => ClientRoleTypes::all(),
        ]);
    }

    private function findNotification(int $id)
    {
        return Notification::with(['action'])->findOrFail($id);
    }
}
