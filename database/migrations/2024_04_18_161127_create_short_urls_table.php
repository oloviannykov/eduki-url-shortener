<?php

use App\Models\ShortUrl;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('short_urls', function (Blueprint $table) {
            $table->string('id', ShortUrl::ID_MAX_LENGTH)->primary()->comment('URL hash key');
            $table->string('original_url', ShortUrl::URL_MAX_LENGTH);
            $table->unsignedInteger('usage_counter')->default(0);
            $table->timestamps(); //fields created_at and updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('short_urls');
    }
};
