<?php

namespace App\Http\Api\External\V1\Controllers;

use App\Http\Resources\TransactionLogCollection;
use App\Models\TransactionLog\TransactionLog;
use App\Models\TransactionLog\TransactionLogStatus;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class TransactionLogController
 *
 * @package App\Http\Api\External\V1\Controllers
 */
class TransactionLogController extends Controller
{
    public function index(string $accountNumber): Response
    {
        $log = TransactionLog::byAccountNumber($accountNumber)
            ->byTransactionLogStatus(TransactionLogStatus::paid())
            ->latest()
            ->paginate();

        return response()->json(new TransactionLogCollection($log));
    }
}
