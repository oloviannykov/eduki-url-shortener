<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class UrlShortenerTest extends TestCase
{

    public function testApiPing(): void
    {
        $endpoint = '/api/';
        $response = $this->get($endpoint);

        $this->logResponse('GET', $endpoint, $response);

        $response->assertStatus(200);
        $response->assertSeeText('API is available');
    }

    public function testGetUrlsList(): void
    {
        $endpoint = '/api/urls';
        $response = $this->json('GET', $endpoint, []);

        $this->logResponse('GET', $endpoint, $response);

        $response->assertStatus(200);
        /* //error response check
        $response->assertJson(
            fn(AssertableJson $json) =>
            $json->whereType('error', 'array')->missing("success")->etc()
        );
        $r = $response->json();
        $this->assertStringContainsString(mb_strtolower($substring), mb_strtolower($r['message']));
        */
        $response->assertJson(
            fn(AssertableJson $json) =>
            $json->where('success', true)->whereType('items', 'array')->missing("error")->etc()
        );
        
        $r = $response->json();
        //$this->assertTrue(count($r['items']) > 0, "items count should be > 0");

        //items are not required
        foreach ($r['items'] as $item) {
            $this->assertArrayHasKey('id', $item);
            $this->assertArrayHasKey('original_url', $item);
            $this->assertArrayHasKey('short_url', $item);
            $this->assertArrayHasKey('created_at', $item);
            $this->assertArrayHasKey('usage_counter', $item);
            $this->assertArrayHasKey('last_usage_date', $item);
        }
    }

    public function testUrlCreateListAndRemove(): void
    {
        $url = 'https://laravel.com/docs/10.x/artisan#' . uniqid();
        $endpoint = '/api/urls/new';

        //first request
        $response = $this->json('POST', $endpoint, [
            'url' => $url,
        ]);
        $this->logResponse('POST', $endpoint, $response);

        $response->assertStatus(200);
        $response->assertJson(
            fn(AssertableJson $json) => $json
                ->where('success', true)
                ->whereType('url', 'string')
                ->whereType('id', 'string')
                ->missing("error")
                ->etc()
        );
        $r = $response->json();
        $shortUrl1 = $r['url'];
        $id1 = $r['id'];

        //second request with same full URL, must return same short URL
        $response = $this->json('POST', $endpoint, [
            'url' => $url,
        ]);
        $this->logResponse('POST', $endpoint, $response);
        $response->assertStatus(200);
        $response->assertJson(
            fn(AssertableJson $json) => $json
                ->where('success', true)
                ->whereType('url', 'string')
                ->whereType('id', 'string')
                ->missing("error")
                ->etc()
        );
        $r = $response->json();
        $shortUrl2 = $r['url'];
        $id2 = $r['id'];

        //compare both responces
        $this->assertEquals(
            $shortUrl1,
            $shortUrl2,
            "second request should return same result"
        );

        //trying to get full URL by hash
        $endpoint = "/api/urls/$id1/url";
        $response = $this->json('GET', $endpoint);
        $this->logResponse('GET', $endpoint, $response);
        $response->assertStatus(200);
        $response->assertJson(
            fn(AssertableJson $json) => $json
                ->where('success', true)
                ->missing("error")
                ->whereType('url', 'string')
                ->etc()
        );
        $r = $response->json();
        $urlByHash = $r['url'];
        $this->assertEquals($url, $urlByHash, "should equal the original URL");

        //The URL should be present in last records list
        $endpoint = '/api/urls';
        $response = $this->json('GET', $endpoint);
        $this->logResponse('GET', $endpoint, $response);
        $response->assertStatus(200);
        $response->assertJson(
            fn(AssertableJson $json) =>
            $json->where('success', true)->whereType('items', 'array')->missing("error")->etc()
        );
        $r = $response->json();
        $this->assertTrue(count($r['items']) > 0, "items count should be > 0");

        //searching record by full and short urls
        $found = false;
        foreach ($r['items'] as $item) {
            if ($item['original_url'] === $url && $item['short_url'] === $shortUrl1) {
                $found = true;
                break;
            }
        }
        $this->assertTrue($found, "just created URL not found in list");

        //remove the record with method DELETE
        $endpoint = '/api/urls/' . $id1;
        $response = $this->deleteJson($endpoint);
        $this->logResponse('DELETE', $endpoint, $response);
        $response->assertStatus(200);
        $response->assertJson(
            fn(AssertableJson $json) => $json
                ->where('success', true)
                ->missing("error")
                ->etc()
        );
    }
}
