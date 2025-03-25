<?php

namespace App\Http\Api\External\V1\Controllers;

use App\Http\Api\External\V1\Requests\GetLoanOffersRequest;
use App\Http\Resources\LoanOfferCollection;
use App\Http\Resources\LoanOfferResource;
use App\Models\Mortgage\EmploymentType;
use App\Models\Project\Project;
use App\Services\Mortgage\Dto\GetLoanOffersRequestDto;
use App\Services\Mortgage\MortgageService;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class MortgageController
 *
 * @package App\Http\Api\External\V1\Controllers
 */
class MortgageController extends Controller
{
    public function __construct(private MortgageService $mortgageService)
    {
    }

    public function getLoanOffers(GetLoanOffersRequest $request, int $projectId): Response
    {
        $offers = $this->mortgageService->getLoanOfferList(
            $this->prepareLoanOffersRequestDto($request, $this->findProject($projectId))
        );

        return response()->json(new LoanOfferCollection($offers));
    }

    private function prepareLoanOffersRequestDto(
        GetLoanOffersRequest $request,
        Project $project
    ): GetLoanOffersRequestDto {
        return new GetLoanOffersRequestDto(
            age: $request->input('age'),
            agendaType:  $request->input('agenda_type'),
            cost:  $request->input('cost'),
            housingComplexId:  $project->mortgage_calculator_id ?: null,
            initialPayment:  $request->input('initial_payment'),
            isInsured:  $request->input('is_insured'),
            isRfCitizen:  $request->input('is_rf_citizen'),
            loanPeriod:  $request->input('loan_period'),
            lastJobExp:  $request->input('last_job_exp'),
            overallExp:  $request->input('overall_exp'),
            mortgageType:  Str::upper($request->input('mortgage_type')),
            proofOfIncome:  $request->input('proof_of_income'),
            employmentType:  EmploymentType::employee()->value,
            payrollProgramBankId: $request->input('payroll_program_bank_id')
                ? $request->input('payroll_program_bank_id') : null,
        );
    }

    private function findProject(int $id): Project
    {
        /* @var Project $project */
        $project = Project::find($id);
        if ($project === null) {
            throw new NotFoundHttpException('Project not found.');
        }

        return $project;
    }
}
