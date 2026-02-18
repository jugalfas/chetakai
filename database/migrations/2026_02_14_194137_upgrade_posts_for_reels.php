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

            // what kind of content
            $table->dropColumn('type');
            $table->dropColumn('media_id');

            $table->enum('content_type', ['image', 'reel', 'carousel'])->default('image')->after('hook');

            // MEDIA FILES
            $table->string('video_path')->nullable()->after('image_path');
            $table->string('audio_path')->nullable()->after('video_path');
            $table->integer('duration')->nullable()->after('audio_path'); // seconds

            // RENDER PIPELINE
            $table->enum('render_status', ['pending', 'rendering', 'completed', 'failed'])
                ->default('pending')->after('duration');

            // INSTAGRAM PROCESSING
            $table->enum('upload_status', ['waiting', 'container_created', 'processing', 'published', 'failed'])
                ->default('waiting')->after('render_status');

            $table->string('container_id')->nullable()->after('upload_status'); // IG creation_id
            $table->timestamp('published_at')->nullable()->after('container_id');

            // ANALYTICS (VERY IMPORTANT FOR GROWTH)
            $table->integer('views')->default(0)->after('published_at');
            $table->integer('likes')->default(0)->after('views');
            $table->integer('comments')->default(0)->after('likes');
            $table->integer('shares')->default(0)->after('comments');
            $table->integer('saves')->default(0)->after('shares');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {

            $table->enum('type', ['post', 'reel'])->default('post');
            $table->string('media_id')->nullable();
            // Drop columns in reverse order
            $table->dropColumn([
                'saves',
                'shares',
                'comments',
                'likes',
                'views',
                'container_id',
                'published_at',
                'upload_status',
                'render_status',
                'duration',
                'audio_path',
                'video_path',
                'content_type'
            ]);
        });
    }
};
