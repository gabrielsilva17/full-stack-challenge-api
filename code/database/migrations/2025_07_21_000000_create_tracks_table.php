<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTracksTable extends Migration
{
    public function up()
    {
        Schema::create('tracks', function (Blueprint $table) {
            $table->id();
            $table->string('isrc', 15)->unique();
            $table->string('title');
            $table->json('artists');
            $table->string('album_thumb')->nullable();
            $table->date('release_date')->nullable();
            $table->string('duration');
            $table->string('preview_url')->nullable();
            $table->string('spotify_url');
            $table->boolean('available_in_br')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tracks');
    }
}
