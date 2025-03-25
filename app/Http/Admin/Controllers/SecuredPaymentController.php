<?php

namespace App\Http\Admin\Controllers;

use App\Components\QueryBuilder\Filters\FiltersCreatedBetween;
use App\Models\TransactionLog\TransactionExport;
use App\Models\TransactionLog\TransactionLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Inertia\Response;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * Class SecuredPaymentController
 *
 * @package App\Http\Admin\Controllers
 */
class SecuredPaymentController extends Controller
{
    public function index(Request $request): Response
    {
        return inertia('Payment/List', [
            'payments' => QueryBuilder::for(TransactionLog::class)
                ->allowedFilters([
                    'title',
                    'account_number',
                    AllowedFilter::exact('user_id'),
                    AllowedFilter::custom('creation_period', new FiltersCreatedBetween()),
                ])
                ->paginate(),
            'defaultSorter' => $request->input('sort'),
            'defaultFilters' => $request->input('filter'),
        ]);
    }

    public function export()
    {
        return Excel::download(new TransactionExport(), 'transaction_logs.xlsx');
    }
}
