<?php

namespace App\Http\Api\External\V1\Controllers;

use App\Http\Resources\Uk\UkProjectBCollection;
use App\Models\Account\AccountInfo;
use App\Models\UkProject;
use App\Services\Contract\ContractRepository;
use App\Services\Contract\ContractService;
use Symfony\Component\HttpFoundation\Response;

class UkProjectController extends Controller
{
    public function index(
        ContractRepository $contractRepository,
        ContractService $contractService,
    ): Response {
        $user = $this->getAuthUser();
        $contracts = $contractRepository->getContracts($user->crm_id);
        $accountsNumbers = $contractService->getAccountsFromContracts($contracts);
        $ukProjectsId = AccountInfo::whereIn('account_number', $accountsNumbers)->distinct()->pluck('uk_project_id');
        $ukProjects = UkProject::whereIn('id', $ukProjectsId)->get();
        $buildId = AccountInfo::whereIn('account_number', $accountsNumbers)->distinct()->pluck('build_id')->toArray();

        $ukProjects = $ukProjects->map(function ($ukProject) use ($buildId) {
            $ukProject->buildings_id = $buildId;
            return $ukProject;
        });

        return response()->json(new UkProjectBCollection($ukProjects));
    }
}
