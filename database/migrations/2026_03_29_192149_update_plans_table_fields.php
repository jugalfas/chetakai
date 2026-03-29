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
        // 1. Rename existing columns to match form state
        Schema::table('plans', function (Blueprint $table) {
            $table->renameColumn('yearly_price', 'annual_price');
            $table->renameColumn('max_content_profiles', 'max_brand_profiles');
            $table->renameColumn('max_posts_per_month', 'post_limit_count');
            $table->renameColumn('features_json', 'features');
        });

        // 2. Add new columns
        Schema::table('plans', function (Blueprint $table) {
            $table->string('type')->default('paid')->after('name');
            $table->string('badge_label')->nullable()->after('description');
            $table->boolean('is_most_popular')->default(false)->after('badge_label');
            
            $table->integer('trial_days')->default(0)->after('annual_price');
            $table->string('currency', 3)->default('USD')->after('trial_days');
            $table->string('stripe_price_id')->nullable()->after('currency');
            $table->string('post_limit_type')->default('Monthly')->after('stripe_price_id');
            $table->integer('max_platforms')->default(0)->after('post_limit_count');
            $table->boolean('allow_new_signups')->default(true)->after('status');
            $table->boolean('show_on_pricing')->default(true)->after('allow_new_signups');
            $table->text('internal_notes')->nullable()->after('show_on_pricing');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->dropColumn([
                'type', 'badge_label', 'is_most_popular', 'trial_days', 'currency', 
                'stripe_price_id', 'post_limit_type', 'max_platforms', 
                'allow_new_signups', 'show_on_pricing', 'internal_notes'
            ]);
        });

        Schema::table('plans', function (Blueprint $table) {
            $table->renameColumn('annual_price', 'yearly_price');
            $table->renameColumn('max_brand_profiles', 'max_content_profiles');
            $table->renameColumn('post_limit_count', 'max_posts_per_month');
            $table->renameColumn('features', 'features_json');
        });
    }
};
