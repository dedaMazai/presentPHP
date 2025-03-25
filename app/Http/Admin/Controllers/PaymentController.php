<?php

namespace App\Http\Admin\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Inertia\Response;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * Class FileController
 *
 * @package App\Http\Admin\Controllers
 */
class PaymentController
{
    public function balanceCheckout()
    {
        return inertia('Payment/Check');
    }

    public function balanceBookingCheckout()
    {
        Artisan::call('crm:check-order-status');

        return inertia('Payment/CheckPayBooking');
    }
}
