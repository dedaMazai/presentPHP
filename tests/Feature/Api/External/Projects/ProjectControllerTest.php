<?php

namespace Tests\Feature\Api\External\Projects;

use App\Models\Article\Article;
use App\Models\Project\Project;
use App\Models\Project\ProjectAddress;
use App\Services\Mortgage\MortgageService;
use App\Services\Project\ProjectAddressRepository;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Collection;
use Tests\TestCase;

class ProjectControllerTest extends TestCase
{
    private string $project_id;
    private array $projectAddresses;
    private string $article_id;

    protected function setUp(): void
    {
        parent::setUp(); // TODO: Change the autogenerated stub

        $this->project_id = Project::factory(1)->create()->first()->id;

        $this->projectAddresses[] = new ProjectAddress(
            id: fake()->numerify,
            post: fake()->word,
            prefixUK: fake()->word,
            addressPostShort: fake()->word,
            ukId: fake()->word,
            ukName: fake()->word,
            addressNumber: fake()->word,
            floorCount: fake()->numerify,
            sectionCount: fake()->numerify,
            dateExploitation: new Carbon(fake()->date),
        );

        $this->article_id = Article::factory(1)->state([
            'articlable_id' => $this->project_id,
            'articlable_type' => 'project'
        ])->create()->first()->id;
    }

    public function testGettingAListOfProjects()
    {
        $req = $this->get('api/v1/projects');
        $req->assertOk();
    }

    public function testGettingAProjectById()
    {
        $mock = $this->mock(ProjectAddressRepository::class);
        $mock->makePartial();
        $mock->shouldAllowMockingProtectedMethods();
        $mock->allows([
            'getAllByProject' => $this->projectAddresses,
        ]);
        app()->bind(ProjectAddressRepository::class, fn() => $mock);

        $req = $this->get('api/v1/projects/'. $this->project_id);
        $req->assertOk();
    }

    public function testGettingAListOfProjectArticles()
    {
        $req = $this->get('api/v1/projects/'. $this->project_id. '/articles');
        $req->assertOk();
    }

    public function testGettingArticleOfProjects()
    {
        $req = $this->get('api/v1/projects/' . $this->project_id . '/articles/' . $this->article_id);
        $req->assertOk();
    }

    public function testGettingAListOfLoanOffers()
    {
        $this->withoutExceptionHandling();
        $params = $this->loadFixture('raw_loan_offers.json');
        $query = (http_build_query($params));

        $mock = $this->mock(MortgageService::class);
        $mock->makePartial();
        $mock->shouldAllowMockingProtectedMethods();
        $mock->allows([
            'getLoanOfferList' => new Collection(),
        ]);
        app()->bind(MortgageService::class, fn() => $mock);

        $req = $this->get('api/v1/projects/' . $this->project_id . '/loan-offers?' . $query);
        $req->assertOk();
    }

    private function loadFixture(string $path): array
    {
        $path = base_path("tests/Feature/Api/External/fixtures/$path");

        return json_decode(file_get_contents($path), true);
    }
}
