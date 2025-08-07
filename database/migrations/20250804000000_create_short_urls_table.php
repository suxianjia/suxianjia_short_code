<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShortUrlsTable extends Migration {
    public function up() {
        Schema::create('short_urls', function (Blueprint $table) {
            $table->string('short_code', 6)->primary();
            $table->text('original_url');
            $table->timestamp('created_at');
        });
    }

    public function down() {
        Schema::dropIfExists('short_urls');
    }
}