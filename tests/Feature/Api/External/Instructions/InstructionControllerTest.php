<?php

namespace Tests\Feature\Api\External\Instructions;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class InstructionControllerTest extends TestCase
{
    public function testGettingAListOfInstructions()
    {
        $req = $this->get('api/v1/instructions');
        $req->assertOk();
    }
}
