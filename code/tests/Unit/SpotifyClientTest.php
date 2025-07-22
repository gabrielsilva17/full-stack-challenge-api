<?php

namespace Tests\Unit;

use App\Clients\SpotifyClient;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\RequestException;
use Tests\TestCase;

class SpotifyClientTest extends TestCase
{
    public function test_successfully_obtains_access_token_and_searches_track(): void
    {
        Http::fake([
            'https://accounts.spotify.com/api/token' => Http::response([
                'access_token' => 'fake-token'
            ], 200),
            'https://api.spotify.com/v1/search*' => Http::response([
                'tracks' => [
                    'items' => [[
                        'name'            => 'Test Song',
                        'artists'         => [['name' => 'Artist X']],
                        'album'           => ['images' => [['url' => 'thumb.jpg']], 'release_date' => '2021-01-01'],
                        'duration_ms'     => 180000,
                        'preview_url'     => 'https://preview.mp3',
                        'external_urls'   => ['spotify' => 'https://open.spotify.com/track/123'],
                        'available_markets'=> ['BR','US'],
                    ]]
                ]
            ], 200),
        ]);

        $client = new SpotifyClient();
        $item   = $client->findTrackByIsrc('US7VG1846811');

        $this->assertIsArray($item);
        $this->assertEquals('Test Song', $item['name']);
    }

    public function test_throws_on_invalid_token_response(): void
    {
        Http::fake([
            'https://accounts.spotify.com/api/token' => Http::response(null, 401),
        ]);

        $this->expectException(\RuntimeException::class);
        new SpotifyClient();
    }

    public function test_returns_null_when_search_fails(): void
    {
        Http::fake([
            'https://accounts.spotify.com/api/token' => Http::response(['access_token' => 'tok'], 200),
            'https://api.spotify.com/v1/search*'     => Http::response(null, 500),
        ]);

        $client = new SpotifyClient();
        $item   = $client->findTrackByIsrc('US7VG1846811');

        $this->assertNull($item);
    }
}
