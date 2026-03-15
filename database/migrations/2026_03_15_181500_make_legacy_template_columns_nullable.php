<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('templates', function (Blueprint $table) {
            $table->string('content_type')->nullable()->change();
            $table->json('settings_json')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('templates', function (Blueprint $table) {
            $table->string('content_type')->nullable(false)->change();
            $table->json('settings_json')->nullable(false)->change();
        });
    }
};
