<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *   schema="Track",
 *   type="object",
 *   title="Track",
 *   required={"isrc","title","spotify_url"},
 *   @OA\Property(property="id",             type="integer", readOnly=true, example=1),
 *   @OA\Property(property="isrc",           type="string",  example="US7VG1846811"),
 *   @OA\Property(property="title",          type="string",  example="Shape of You"),
 *   @OA\Property(property="artists",        type="array",   @OA\Items(type="string"), example={"Ed Sheeran"}),
 *   @OA\Property(property="album_thumb",    type="string",  format="uri", example="https://i.scdn.co/image/…"),
 *   @OA\Property(property="release_date",   type="string",  format="date", example="2017-01-06"),
 *   @OA\Property(property="duration",       type="string",  description="mm:ss", example="03:53"),
 *   @OA\Property(property="preview_url",    type="string",  format="uri", example="https://p.scdn.co/mp3-preview/…"),
 *   @OA\Property(property="spotify_url",    type="string",  format="uri", example="https://open.spotify.com/track/…"),
 *   @OA\Property(property="available_in_br",type="boolean", example=true),
 *   @OA\Property(property="created_at",     type="string",  format="date-time"),
 *   @OA\Property(property="updated_at",     type="string",  format="date-time"),
 *   @OA\Property(property="deleted_at",     type="string",  format="date-time", nullable=true)
 * )
 */
class Track extends BaseModel
{
    use HasFactory;

    protected $table = 'tracks';

    protected $fillable = [
        'isrc',
        'title',
        'artists',
        'album_thumb',
        'release_date',
        'duration',
        'preview_url',
        'spotify_url',
        'available_in_br',
    ];

    protected $casts = [
        'artists'         => 'array',
        'release_date'    => 'date',
        'available_in_br' => 'boolean',
    ];
}
