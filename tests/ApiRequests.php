<?php

namespace Tests;

trait ApiRequests
{
    private function acceptJson(): void
    {
        $this->withHeader('Accept', "application/json");
    }
}
