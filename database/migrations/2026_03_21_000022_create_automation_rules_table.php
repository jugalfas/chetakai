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
        Schema::create('automation_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('template_id')->constrained();
            $table->foreignId('social_account_id')->nullable()->constrained();
            $table->enum('frequency', ['daily', 'weekly', 'monthly', 'custom']);
            $table->json('days_json')->nullable();
            $table->json('time_json')->nullable();
            $table->string('timezone');
            $table->boolean('auto_generate')->default(true);
            $table->boolean('auto_publish')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_run_at')->nullable();
            $table->timestamp('next_run_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('automation_rules');
    }
};
