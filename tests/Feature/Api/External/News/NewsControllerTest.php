<?php

namespace Tests\Feature\Api\External\News;

use App\Models\News\News;
use App\Models\Role;
use App\Models\User\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class NewsControllerTest extends TestCase
{
//    private $news_id;
//    private $type;
//
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
//        $this->type = 'pioneer';
//        $this->news_id = News::factory(1)->create()->first()->id;
//        $token = $user->createToken('auth_token')->plainTextToken;
//        $this->withHeader('Authorization', "Bearer {$token}");
//    }

//    public function testGettingAListOfNews()
//    {
//        $req = $this->get('api/v1/news?type='.$this->type);
//        $req->assertOk();
//    }
//
//    public function testGettingNewsById()
//    {
//        $req = $this->get('api/v1/news/'.$this->news_id);
//        $req->assertOk();
//    }
}
