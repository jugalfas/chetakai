<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('templates', function (Blueprint $table) {
            if (!Schema::hasColumn('templates', 'platform_id')) {
                $table->unsignedBigInteger('platform_id')->nullable()->after('user_id');
            }
            if (!Schema::hasColumn('templates', 'content_type_id')) {
                $table->unsignedBigInteger('content_type_id')->nullable()->after('platform_id');
            }
            if (!Schema::hasColumn('templates', 'category_id')) {
                $table->unsignedBigInteger('category_id')->nullable()->after('content_type_id');
            }
            if (!Schema::hasColumn('templates', 'goal_id')) {
                $table->unsignedBigInteger('goal_id')->nullable()->after('category_id');
            }
            if (!Schema::hasColumn('templates', 'tone_id')) {
                $table->unsignedBigInteger('tone_id')->nullable()->after('goal_id');
            }
            if (!Schema::hasColumn('templates', 'audience_id')) {
                $table->unsignedBigInteger('audience_id')->nullable()->after('tone_id');
            }
            if (!Schema::hasColumn('templates', 'style_id')) {
                $table->unsignedBigInteger('style_id')->nullable()->after('audience_id');
            }
            if (!Schema::hasColumn('templates', 'length')) {
                $table->string('length')->nullable()->after('template_name');
            }
            if (!Schema::hasColumn('templates', 'bulk_generate')) {
                $table->integer('bulk_generate')->default(0)->after('length');
            }

            // The existing table from studio has `content_type` and `settings_json`, we keep them unchanged.
        });
    }

    public function down(): void
    {
        Schema::table('templates', function (Blueprint $table) {
            $columns = ['platform_id', 'content_type_id', 'category_id', 'goal_id', 'tone_id', 'audience_id', 'style_id', 'length', 'bulk_generate'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('templates', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
