<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Mail::fake();
        Http::fake([
            'https://api.telegram.org/*' => Http::response(['ok' => true, 'result' => ['message_id' => 101]], 200),
            'http://ip-api.com/*' => Http::response(['status' => 'success', 'city' => 'Hanoi', 'country' => 'Vietnam'], 200),
        ]);
    }
}
