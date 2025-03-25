<?php

namespace Tests\Feature\Api\External;

use App\Models\Role;
use App\Models\User\User;
use App\Services\Claim\ClaimCatalogueRepository;
use Tests\TestCase;

class ClaimCatalogueControllerTest extends TestCase
{
//    protected function setUp(): void
//    {
//        parent::setUp();
//
//        /** @var User $user */
//        $user = User::factory(1)->state(['phone' => '+79999999999'])->create()->first();
//        $user->relationships()->create([
//            'account_number' => 123,
//            'role' => Role::owner(),
//            'joint_owner_id' => 212,
//        ]);
//        $token = $user->createToken('auth_token')->plainTextToken;
//        $this->withHeader('Authorization', "Bearer {$token}");
//    }
//
//    public function testCheckClaimCatalogueWithChildrenIsNotAService()
//    {
//        $catalogueWithChildrenId = 'a09a7619-80c0-ec11-bba9-005056bfae62';
//        $catalogueWithoutChildrenId = '8fbbcd55-82c0-ec11-bba9-005056bfae62';
//        $mock = $this->mock(ClaimCatalogueRepository::class);
//        $mock->makePartial();
//        $mock->shouldAllowMockingProtectedMethods();
//        $mock->allows([
//            'getAllByProject' => $this->loadFixture('raw_claim_cataogue.json'),
//        ]);
//        app()->bind(ClaimCatalogueRepository::class, fn() => $mock);
//
//        $response = $this->json('get', "/api/v1/claims/catalogue/$catalogueWithChildrenId");
//        $response->assertOk();
//        $response->assertJsonPath('is_service', false);
//
//        $response = $this->json('get', "api/v1/claims/catalogue/$catalogueWithoutChildrenId");
//        $response->assertOk();
//        $response->assertJsonPath('is_service', true);
//    }
//
//    private function loadFixture($path)
//    {
//        $path = base_path("tests/Feature/Api/External/fixtures/$path");
//
//        return json_decode(file_get_contents($path), true);
//    }
}
