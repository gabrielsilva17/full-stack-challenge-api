<?php

namespace App\Clients;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\RequestException;

class SpotifyClient
{
    protected string $accessToken;

    public function __construct()
    {
        try {
            $response = Http::timeout(5)
            ->asForm()
                ->withHeaders([
                    'Authorization' => 'Basic ' . base64_encode(
                            config('services.spotify.client_id') . ':' .
                            config('services.spotify.client_secret')
                        ),
                ])
                ->post('https://accounts.spotify.com/api/token', [
                    'grant_type' => 'client_credentials',
                ]);

            $response->throw();

            $this->accessToken = $response->json('access_token');
        } catch (RequestException $e) {
            \Log::error('Spotify auth failed: '.$e->getMessage());
            throw new \RuntimeException('Não foi possível autenticar no Spotify.');
        }
    }

    public function findTrackByIsrc(string $isrc): ?array
    {
        try {
            $resp = Http::timeout(5)
                ->withToken($this->accessToken)
                ->get('https://api.spotify.com/v1/search', [
                    'q'     => 'isrc:' . $isrc,
                    'type'  => 'track',
                    'limit' => 1,
                ]);

            $resp->throw();

            return $resp->json('tracks.items.0');
        } catch (RequestException $e) {
            \Log::warning("Erro ao buscar ISRC {$isrc}: ".$e->getMessage());
            return null;
        }
    }
}
