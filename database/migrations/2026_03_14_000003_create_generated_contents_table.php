<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('generated_contents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('content_type');
            $table->string('category')->nullable();
            $table->longText('prompt');
            $table->longText('result')->nullable();
            $table->json('settings_json')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index(['user_id', 'content_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('generated_contents');
    }
};

