<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Http;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TrackControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Http::fake([
            'https://accounts.spotify.com/api/token' => Http::response(
                ['access_token'=>'tok'], 200
            ),
            'https://api.spotify.com/v1/search*' => Http::response([
                'tracks'=>['items'=>[[
                    'name'            => 'Quase Nua',
                    'artists'         => [['name'=>'Clarisse Grova']],
                    'album'           => ['images'=>[['url'=>'https://i.scdn.co/image/...']], 'release_date'=>'2015-08-27'],
                    'duration_ms'     => 253000,
                    'preview_url'     => null,
                    'external_urls'   => ['spotify'=>'https://open.spotify.com/track/7u8QESpplZWyZZDsoHeIAX'],
                    'available_markets'=> ['BR'],
                ]]]
            ], 200),
        ]);

        $this->postJson('/api/tracks/import', [
            'isrcs' => ['BR1SP1500002'],
        ])->assertStatus(200);
    }

    public function test_import_endpoint_and_response_structure(): void
    {
        Http::fake([
            'https://accounts.spotify.com/api/token' => Http::response(['access_token'=>'tok'], 200),
            'https://api.spotify.com/v1/search*'     => Http::response([
                'tracks'=>[
                    'items'=>[[
                        'name'=>'Ce ta doido','artists'=>[['name'=>'Panda']],
                        'album'=>['images'=>[['url'=>'https://thumb.pandinha.jpg']],'release_date'=>'2025-01-01'],
                        'duration_ms'=>25030,'preview_url'=>'p','external_urls'=>['spotify'=>'s'],'available_markets'=>['BR']
                    ]]
                ]
            ], 200),
        ]);

        $resp = $this->postJson('/api/tracks/import', [
            'isrcs'=>['MA0000000001']
        ]);

        $resp->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => ['results','messages']
            ])
            ->assertJsonFragment([
                '1 - MA0000000001 encontrado.'
            ]);
    }

    public function test_index_returns_paginated_tracks(): void
    {
        $response = $this->getJson('/api/tracks?page=1&per_page=15');

        $response
            ->assertStatus(200)
            ->assertJson([
                'status'  => true,
                'message' => 'Operação bem‑sucedida.',
            ])
            ->assertJsonStructure([
                'data' => [
                    'data' => [
                        '*' => [
                            'id',
                            'isrc',
                            'title',
                            'artists',
                            'album_thumb',
                            'release_date',
                            'duration',
                            'preview_url',
                            'spotify_url',
                            'available_in_br',
                            'created_at',
                            'updated_at',
                            'deleted_at',
                        ]
                    ],
                    'meta' => [
                        'current_page',
                        'per_page',
                        'last_page',
                        'total',
                    ],
                ],
            ]);

        $this->assertEquals(
            'BR1SP1500002',
            $response->json('data.data.0.isrc')
        );
    }

    public function test_show_returns_single_track_and_404(): void
    {
        $id = $this->getJson('/api/tracks?page=1&per_page=15')
            ->json('data.data.0.id');

        $this->getJson("/api/tracks/{$id}")
            ->assertStatus(200)
            ->assertJson([
                'status'  => true,
                'message' => 'Operação bem‑sucedida.',
                'data'    => [
                    'id'   => $id,
                    'isrc' => 'BR1SP1500002',
                    'title'=> 'Quase Nua',
                ],
            ])
            ->assertJsonStructure([
                'data' => [
                    'artists',
                    'album_thumb',
                    'release_date',
                    'duration',
                    'preview_url',
                    'spotify_url',
                    'available_in_br',
                    'created_at',
                    'updated_at',
                    'deleted_at',
                ],
            ]);

        $this->getJson('/api/tracks/99999')
            ->assertStatus(404)
            ->assertExactJson([
                'status'  => false,
                'message' => 'Registro não encontrado.',
            ]);
    }
}
