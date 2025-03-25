<?php

namespace App\Http\Api\External\V1\Requests;

use App\Models\Mortgage\LoanPurpose;
use App\Models\Mortgage\MortgageType;
use App\Models\Mortgage\ProofOfIncome;
use Illuminate\Validation\Rule;

/**
 * Class GetLoanOffersRequest
 *
 * @package App\Http\Api\External\V1\Requests
 */
class GetLoanOffersRequest extends Request
{
    public function rules(): array
    {
        return [
            'age' => 'required|integer',
            'agenda_type' => ['required', Rule::in(LoanPurpose::toValues())],
            'cost' => 'required|integer',
            'initial_payment' => 'required|integer',
            'is_insured' => 'required|bool',
            'is_rf_citizen' => 'required|bool',
            'loan_period' => 'required|integer',
            'last_job_exp' => 'required|integer',
            'overall_exp' => 'required|integer',
            'mortgage_type' => ['required', Rule::in(MortgageType::toValues())],
            'proof_of_income' => ['required', Rule::in(ProofOfIncome::toValues())],
            'payroll_program_bank_id' => 'sometimes|integer'
        ];
    }
}
