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
        Schema::rename('admins', 'admin_users');

        Schema::table('admin_users', function (Blueprint $table) {
            $table->dropColumn('name');
            $table->string('first_name')->after('id');
            $table->string('last_name')->after('first_name')->nullable();
            $table->string('role')->default('support')->after('password');
            $table->string('status')->default('active')->after('role'); // active, suspended
            $table->timestamp('last_login_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('admin_users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('admin_users', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropColumn(['first_name', 'last_name', 'role', 'status', 'last_login_at', 'created_by']);
            $table->string('name')->after('id')->nullable();
        });

        Schema::rename('admin_users', 'admins');
    }
};
