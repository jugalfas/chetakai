<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('generated_contents', function (Blueprint $table) {
            $table->unsignedBigInteger('template_id')->nullable()->after('user_id');
            $table->unsignedBigInteger('platform_id')->nullable()->after('template_id');
            $table->unsignedBigInteger('content_type_id')->nullable()->after('platform_id');
            $table->longText('ai_response')->nullable()->after('prompt');
            $table->string('status')->default('generated')->after('ai_response');

            $table->foreign('template_id')->references('id')->on('templates')->onDelete('set null');
            $table->foreign('platform_id')->references('id')->on('platforms')->onDelete('set null');
            $table->foreign('content_type_id')->references('id')->on('content_types')->onDelete('set null');

            $table->index(['template_id', 'platform_id', 'content_type_id']);
        });
    }

    public function down(): void
    {
        Schema::table('generated_contents', function (Blueprint $table) {
            $table->dropForeign(['template_id']);
            $table->dropForeign(['platform_id']);
            $table->dropForeign(['content_type_id']);

            $table->dropIndex(['template_id', 'platform_id', 'content_type_id']);

            $table->dropColumn(['template_id', 'platform_id', 'content_type_id', 'ai_response', 'status']);
        });
    }
};
