<?php

namespace App\Services;

use App\Clients\SpotifyClient;
use App\Repositories\TrackRepository;

class TrackService extends BaseService
{
    public function __construct(TrackRepository $repo, protected SpotifyClient $spotify)
    {
        parent::__construct($repo);
    }

    /**
     * Importa do Spotify e persiste em DB.
     *
     * @param  string[]  $isrcs
     * @return array<string,\App\Models\Track|null>
     */
    public function importTracks(array $isrcs): array
    {
        $results  = [];
        $messages = [];

        foreach ($isrcs as $index => $isrc) {
            $item = $this->spotify->findTrackByIsrc($isrc);

            if (! $item) {
                $messages[] = sprintf(
                    '%d - %s não encontrado.',
                    $index + 1,
                    $isrc
                );

                $results[$isrc] = null;
                continue;
            }

            $messages[] = sprintf(
                '%d - %s encontrado.',
                $index + 1,
                $isrc
            );

            $data = [
                'isrc'           => $isrc,
                'title'          => $item['name'],
                'artists'        => array_column($item['artists'], 'name'),
                'album_thumb'    => $item['album']['images'][0]['url'] ?? null,
                'release_date'   => $item['album']['release_date'],
                'duration'       => gmdate('i:s', $item['duration_ms'] / 1000),
                'preview_url'    => $item['preview_url'],
                'spotify_url'    => $item['external_urls']['spotify'],
                'available_in_br'=> in_array('BR', $item['available_markets'] ?? [], true),
            ];

            $existing = $this->repo->findByIsrc($isrc);
            $model    = $this->save($data, $existing?->id);

            $results[$isrc] = $model;
        }

        return [
            'results'  => $results,
            'messages' => $messages,
        ];
    }
}
