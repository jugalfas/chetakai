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
        Schema::table('conversations', function (Blueprint $table) {
            $table->text('system_prompt')->nullable()->after('title');
            $table->tinyInteger('pinned')->default(0)->after('system_prompt');
            $table->softDeletes()->after('updated_at');
        });

        Schema::table('messages', function (Blueprint $table) {
            $table->unsignedBigInteger('parent_message_id')->nullable()->after('conversation_id');
            $table->integer('version')->default(1)->after('content');
            $table->tinyInteger('is_superseded')->default(0)->after('version');
            $table->softDeletes()->after('updated_at');

            $table->foreign('parent_message_id')->references('id')->on('messages')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->dropForeign(['parent_message_id']);
            $table->dropColumn(['parent_message_id', 'version', 'is_superseded', 'deleted_at']);
        });

        Schema::table('conversations', function (Blueprint $table) {
            $table->dropColumn(['system_prompt', 'pinned', 'deleted_at']);
        });
    }
};
