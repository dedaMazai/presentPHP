<?php

namespace Tests\Feature\Api\External;

use App\Models\Role;
use App\Models\User\User;
use App\Services\Claim\ClaimCatalogueRepository;
use App\Services\DynamicsCrm\DynamicsCrmClient;
use App\Services\DynamicsCrm\Exceptions\BadRequestException;
use App\Services\DynamicsCrm\Exceptions\NotFoundException;
use Illuminate\Http\UploadedFile;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class ClaimControllerTest extends TestCase
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
//    public function testStoreMarketplaceClaimCatalogueItemCountAllowsFloat()
//    {
//        $data = [
//            'claim_catalogue_items' => [
//                [
//                    'id' => 'catalogue_id',
//                    'count' => 12.21,
//                ],
//            ],
//        ];
//
//        $response = $this->json('post', '/api/v1/accounts/123/claims/marketplace', $data);
//        $this->assertNotEquals($response->status(), Response::HTTP_UNPROCESSABLE_ENTITY);
//    }
//
//    public function testStoreClaimWithBadRequestException()
//    {
//        $this->prepareClaimCatalogueMock();
//
//        $mock = $this->mock(DynamicsCrmClient::class);
//        $mock->allows('getClaimCatalogue')->andReturn($this->loadFixture('raw_claim_cataogue.json'));
//        $mock->allows('saveClaimRequest')->andThrow(new BadRequestException('Client error'));
//
//        $data = [
//            'claim_catalogue_item_ids' => ['8fbbcd55-82c0-ec11-bba9-005056bfae62'],
//            'comment' => 'some comment'
//        ];
//        $response = $this->json('post', '/api/v1/accounts/123/claims/request', $data);
//        $response->assertStatus(Response::HTTP_BAD_REQUEST);
//    }
//
//    public function testStoreClaimWithNotFoundException()
//    {
//        $this->prepareClaimCatalogueMock();
//
//        $mock = $this->mock(DynamicsCrmClient::class);
//        $mock->allows('getClaimCatalogue')->andReturn($this->loadFixture('raw_claim_cataogue.json'));
//        $mock->allows('saveClaimRequest')->andThrow(new NotFoundException('Not found'));
//
//        $data = [
//            'claim_catalogue_item_ids' => ['8fbbcd55-82c0-ec11-bba9-005056bfae62'],
//            'comment' => 'some comment'
//        ];
//        $response = $this->json('post', '/api/v1/accounts/123/claims/request', $data);
//        $response->assertNotFound();
//    }
//
//    public function testStoreClaimWithLargePhotos()
//    {
//        $this->prepareClaimCatalogueMock();
//
//        $mock = $this->mock(DynamicsCrmClient::class);
//        $mock->allows('getClaimCatalogue')->andReturn($this->loadFixture('raw_claim_cataogue.json'));
//        $mock->allows('saveClaimRequest')->andThrow(new NotFoundException('Not found'));
//
//        $photo = UploadedFile::fake()->create('image.jpg',25000);
//
//        $data = [
//            'claim_catalogue_item_ids' => ['8fbbcd55-82c0-ec11-bba9-005056bfae62'],
//            'comment' => 'some comment',
//            'images' => [
//                $photo,
//                $photo
//            ]
//        ];
//        $response = $this->json('post', '/api/v1/accounts/123/claims/request', $data);
//        $response->assertUnprocessable();
//        $response->assertJsonValidationErrorFor('images');
//    }
//
//    public function testUpdateCacheWhenCreatingClaim()
//    {
//        $this->markTestSkipped('Need complete test');
//        $this->prepareClaimCatalogueMock();
//
//        $mock = $this->mock(DynamicsCrmClient::class);
//        $mock->allows('getClaimCatalogue')->andReturn($this->loadFixture('raw_claim_cataogue.json'));
//        $mock->allows('getClaimById')->andReturn($this->loadFixture('raw_claim.json'));
//        $mock->allows('saveClaimRequest')->andReturn($this->loadFixture('raw_claim.json'));
//
//        $data = [
//            'claim_catalogue_item_ids' => ['8fbbcd55-82c0-ec11-bba9-005056bfae62'],
//            'comment' => 'some comment',
//        ];
//
//        $response = $this->json('post', '/api/v1/accounts/123/claims/request', $data);
//    }
//
//    private function prepareClaimCatalogueMock(): void
//    {
//        $mock = $this->mock(ClaimCatalogueRepository::class);
//        $mock->makePartial();
//        $mock->shouldAllowMockingProtectedMethods();
//        $mock->allows([
//            'getRawData' => $this->loadFixture('raw_claim_cataogue.json'),
//        ]);
//    }
//
//    public function testClaimReceipt()
//    {
////        $this->mockClaimService();
////        $response = $this->json('get', '/api/v1/claims/1');
////        $response->assertOk();
////        $response->assertJsonPath(
////            'pdf',
////            'https://crm2016.pioneer.ru/DOC/УК_Заявканаобслуживание/оплата.docx',
////        );
//    }
//
//    private function mockClaimService(): void
//    {
//        $mock = $this->mock(DynamicsCrmClient::class);
//        $mock
//            ->allows([
//                'getClaimReceipts' => $this->loadFixture('raw_claim_receipts.json'),
//                'getClaimById' => $this->loadFixture('concrete_claim.json'),
//                'getDocumentsByClaimId' => [
//                    'documentList' => [],
//                ],
//                'getClaimCatalogue' => [],
//            ]);
//        app()->bind(DynamicsCrmClient::class, fn() => $mock);
//    }
//
//    private function loadFixture(string $path): array
//    {
//        $path = base_path("tests/Feature/Api/External/fixtures/$path");
//
//        return json_decode(file_get_contents($path), true);
//    }
}
