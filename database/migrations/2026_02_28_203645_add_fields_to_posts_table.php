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
        Schema::table('posts', function (Blueprint $table) {
            $table->text('quote')->after('id');
            $table->integer('user_id')->default(1)->after('quote');
            $table->dropForeign(['quote_id']);
            $table->dropColumn('quote_id');
        });

        Schema::dropIfExists('quotes');
        Schema::dropIfExists('categories');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('quotes', function (Blueprint $table) {
            $table->id();
            $table->text('quote');
            $table->string('category')->nullable();
            $table->enum('status', ['unused', 'posted'])->default('unused');
            $table->softDeletes();
            $table->timestamps();
        });
        Schema::table('posts', function (Blueprint $table) {
            $table->foreignId('quote_id')->constrained()->cascadeOnDelete()->nullable()->after('id');
        });
    }
};
