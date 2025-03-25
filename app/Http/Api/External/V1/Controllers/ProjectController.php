<?php

namespace App\Http\Api\External\V1\Controllers;

use App\Http\Resources\InstructionCollection;
use App\Http\Resources\InstructionResource;
use App\Http\Resources\Project\DetailProjectResource;
use App\Http\Resources\Project\ProjectTypeCollection;
use App\Http\Resources\Project\ProjectTypeResource;
use App\Models\Instruction\Instruction;
use App\Models\Project\Project;
use App\Models\Project\ProjectType;
use App\Services\DynamicsCrm\Exceptions\BadRequestException;
use App\Services\DynamicsCrm\Exceptions\NotFoundException;
use App\Services\Project\ProjectAddressRepository;
use Psr\SimpleCache\InvalidArgumentException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class ProjectController
 *
 * @package App\Http\Api\External\V1\Controllers
 */
class ProjectController extends Controller
{
    public function index(): Response
    {
        return response()->json(new ProjectTypeCollection(ProjectType::all()));
    }

    /**
     * @throws NotFoundException
     * @throws InvalidArgumentException
     * @throws BadRequestException
     */
    public function show(int $id, ProjectAddressRepository $addressRepository): Response
    {
        $project = $this->findProject($id);
        $projectAddresses = $addressRepository->getAllByProject($project);

        return response()->json(new DetailProjectResource(
            ['project' => $project, 'address' => $projectAddresses]
        ));
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
