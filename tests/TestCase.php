<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Testing\TestResponse;

abstract class TestCase extends BaseTestCase
{
    protected function logResponse(string $method, string $endpoint, TestResponse $response): void
    {
        $httpCode = $response->getStatusCode();
        $content = $response->getContent();
        echo "\n* $method $endpoint --> *$httpCode*"
            . (mb_strlen($content) > 50 ? "\n" : ' ')
            . "$content\n";
    }
}
