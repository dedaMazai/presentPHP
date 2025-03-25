<?php

namespace App\Console\Commands;

use App\Models\Project\Project;
use App\Services\DynamicsCrm\Exceptions\BadRequestException;
use App\Services\DynamicsCrm\Exceptions\NotFoundException;
use App\Services\Project\ProjectService;
use Illuminate\Console\Command;
use Psr\SimpleCache\InvalidArgumentException;

/**
 * Class UpdateProjectMinPropertyPrice
 *
 * @package App\Console\Commands
 */
class UpdateProjectMinPropertyPrice extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'projects:update-min-prices';

    /**
     * @throws NotFoundException
     * @throws InvalidArgumentException
     * @throws BadRequestException
     */
    public function handle(ProjectService $projectService): void
    {
        $projects = Project::all();
        foreach ($projects as $project) {
            $projectService->updateMinPropertyPrice($project);
        }

        $this->info('Projects min property price were updated.');
    }
}
