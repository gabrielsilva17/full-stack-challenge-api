<?php

namespace Tests\Unit;

use App\Services\TrackService;
use App\Clients\SpotifyClient;
use App\Repositories\TrackRepository;
use App\Models\Track;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TrackServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_import_tracks_creates_and_generates_messages(): void
    {
        $mockSpotify = $this->createMock(SpotifyClient::class);
        $mockSpotify->method('findTrackByIsrc')
            ->willReturnOnConsecutiveCalls(
                [
                    'name'            => 'Pelados em Santos',
                    'artists'         => [['name' => 'Mamonas Assassinas']],
                    'album'           => [
                        'images'       => [['url' => 'https://thumb.pelados.jpg']],
                        'release_date' => '1995-06-01'
                    ],
                    'duration_ms'     => 210000,
                    'preview_url'     => 'https://preview.pelados.mp3',
                    'external_urls'   => ['spotify' => 'https://open.spotify.com/track/pelados'],
                    'available_markets'=> ['BR'],
                ],
                null
            );

        $repo    = new TrackRepository();
        $service = new TrackService($repo, $mockSpotify);

        $payload = $service->importTracks(['MA0000000001','MA0000000002']);

        $this->assertEquals([
            '1 - MA0000000001 encontrado.',
            '2 - MA0000000002 não encontrado.',
        ], $payload['messages']);

        $this->assertDatabaseHas('tracks', [
            'isrc'  => 'MA0000000001',
            'title' => 'Pelados em Santos',
        ]);

        $this->assertNull($payload['results']['MA0000000002']);
        $this->assertInstanceOf(Track::class, $payload['results']['MA0000000001']);
    }
}
