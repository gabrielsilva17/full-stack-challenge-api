<?php

use App\Http\Controllers\Api\CommunityConfigurationController;
use App\Http\Controllers\Api\CommunityController;
use App\Http\Controllers\Api\CommunityUserController;
use App\Http\Controllers\Api\FollowController;
use App\Http\Controllers\Api\TrackController;
use App\Http\Controllers\Api\TypeCommunityController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\GovBrAuthController;
use App\Http\Controllers\Api\FileController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\LogController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\LikeController;
use App\Http\Controllers\Api\TipoCursoController;
use App\Http\Controllers\Api\TipoCapacitacaoController;
use App\Http\Controllers\Api\TipoIdiomaController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::post('tracks/import', [TrackController::class, 'import']);
Route::get('tracks',         [TrackController::class, 'index']);
Route::get('tracks/{id}',    [TrackController::class, 'show']);
