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
        Schema::create('generated_contents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('template_id')->nullable()->constrained();
            $table->foreignId('social_account_id')->nullable()->constrained();
            $table->foreignId('platform_id')->nullable()->constrained();
            $table->foreignId('content_type_id')->nullable()->constrained();
            $table->foreignId('prompt_template_id')->nullable()->constrained();
            $table->string('title')->nullable();
            $table->text('hook')->nullable();
            $table->longText('caption')->nullable();
            $table->longText('prompt');
            $table->longText('ai_response')->nullable();
            $table->json('parsed_output')->nullable();
            $table->string('media_url')->nullable();
            $table->string('thumbnail_url')->nullable();
            $table->json('metadata')->nullable();
            $table->string('generation_provider')->nullable();
            $table->enum('approval_status', ['pending', 'approved', 'rejected', 'auto_approved']);
            $table->enum('status', ['generated', 'draft', 'scheduled', 'published', 'failed', 'archived']);
            $table->timestamp('scheduled_for')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamp('failed_at')->nullable();
            $table->text('failure_reason')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('generated_contents');
    }
};
