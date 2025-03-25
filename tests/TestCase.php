<?php

namespace Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    use RefreshDatabase;

    protected $fakeQueue = true;

    protected function setUp(): void
    {
        parent::setUp();

        if ($this->fakeQueue) {
            Queue::fake();
        }
        Storage::fake();
    }
}
