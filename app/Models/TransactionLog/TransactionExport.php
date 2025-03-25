<?php

namespace App\Models\TransactionLog;

use Maatwebsite\Excel\Concerns\FromCollection;

class TransactionExport implements FromCollection
{
    public function collection()
    {
        return TransactionLog::all();
    }
}
