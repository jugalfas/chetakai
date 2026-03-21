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
        Schema::create('templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('platform_id')->nullable()->constrained();
            $table->foreignId('content_type_id')->constrained();
            $table->foreignId('category_id')->nullable()->constrained();
            $table->foreignId('goal_id')->nullable()->constrained();
            $table->foreignId('tone_id')->nullable()->constrained();
            $table->foreignId('audience_id')->nullable()->constrained();
            $table->foreignId('style_id')->nullable()->constrained();
            $table->foreignId('brand_kit_id')->nullable()->constrained();
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('posting_mode', ['manual', 'scheduled', 'auto']);
            $table->integer('posts_per_day')->default(1);
            $table->time('preferred_time')->nullable();
            $table->string('timezone')->nullable();
            $table->string('length')->nullable();
            $table->text('custom_instructions')->nullable();
            $table->json('generation_settings')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('templates');
    }
};
