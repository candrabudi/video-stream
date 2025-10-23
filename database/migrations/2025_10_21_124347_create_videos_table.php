<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('videos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('channel_id')
                  ->constrained('channels')
                  ->onDelete('cascade');

            $table->foreignId('category_id')
                  ->nullable()
                  ->constrained('categories')
                  ->nullOnDelete();

            $table->foreignId('created_by')
                  ->constrained('users')
                  ->onDelete('cascade');
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('thumbnail')->nullable();
            $table->string('video_url')->nullable();
            $table->string('video_path')->nullable();
            $table->string('duration')->nullable();
            $table->text('description')->nullable();
            $table->text('report_link')->nullable();
            $table->enum('status', ['draft', 'published'])->default('draft');
            $table->timestamp('uploaded_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('videos');
    }
};
