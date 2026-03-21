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
        Schema::create('prompt_templates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->unsignedBigInteger('platform_id')->nullable();
            $table->foreign('platform_id')->references('id')->on('platforms')->onDelete('set null');
            $table->unsignedBigInteger('content_type_id')->nullable();
            $table->foreign('content_type_id')->references('id')->on('content_types')->onDelete('set null');
            $table->string('name');
            $table->enum('scope', ['system', 'user']);
            $table->integer('version')->default(1);
            $table->longText('prompt_template');
            $table->json('output_schema')->nullable();
            $table->json('variables')->nullable();
            $table->json('model_preferences')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_default')->default(false);
            $table->boolean('status')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prompt_templates');
    }
};
