<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShortUrlVisitsTable extends Migration {
    public function up() {
        Schema::create('short_url_visits', function (Blueprint $table) {
            $table->id();
            $table->string('short_code', 6);
            $table->timestamp('visited_at');
        });
    }

    public function down() {
        Schema::dropIfExists('short_url_visits');
    }
}