<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('content_publications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('generated_content_id');
            $table->foreign('generated_content_id')->references('id')->on('generated_contents');
            $table->unsignedBigInteger('social_account_id');
            $table->foreign('social_account_id')->references('id')->on('social_accounts');
            $table->unsignedBigInteger('platform_id');
            $table->foreign('platform_id')->references('id')->on('platforms');
            $table->enum('publish_status', ['queued', 'processing', 'published', 'failed']);
            $table->timestamp('scheduled_for')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->string('external_container_id')->nullable();
            $table->string('external_post_id')->nullable();
            $table->longText('api_response')->nullable();
            $table->text('failure_reason')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('content_publications');
    }
};
