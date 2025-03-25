<?php

namespace App\Http\Admin\Controllers;

use App\Components\QueryBuilder\Filters\FilterByActive;
use App\Components\QueryBuilder\Filters\FiltersCreatedBetween;
use App\Components\QueryBuilder\Filters\FiltersStartDate;
use App\Models\User\BanHistory;
use App\Models\User\BanIp;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use Inertia\Response;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * Class BannerController
 *
 * @package App\Http\Admin\Controllers
 */
class BlockingController extends Controller
{
    public function index(Request $request): Response
    {

        return inertia('Blocking/List', [
            'blocking' => QueryBuilder::for(BanIp::class)
                ->allowedFilters([
                    AllowedFilter::exact('ip_address'),
                    AllowedFilter::custom('unlock_time', new FilterByActive()),
                ])
                ->where('unlock_time', '>', \Illuminate\Support\Carbon::now()->toDateTimeString())
                ->allowedSorts(['block_time', 'unlock_time'])
                ->paginate(15),
            'defaultSorter' => $request->input('sort'),
            'defaultFilters' => $request->input('filter')
        ]);
    }

    public function edit(int $id): Response
    {
        $ban = BanIp::find($id);
        $phones = BanHistory::where('ip_address', $ban->ip_address)->pluck('phone_number');
        $ban['phone_numbers'] = $phones;

        return inertia('Blocking/Edit', [
            'blocking' => $ban,
        ]);
    }


    public function destroy(string $ip): RedirectResponse
    {
        BanIp::where('id', $ip)->delete();

        return redirect()->route('blocking');
    }

    public function paginate($items, $perPage = 5, $page = null, $options = [])
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);
        return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
    }
}
