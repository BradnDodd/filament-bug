<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('comments_posts', function (Blueprint $table) {
            $table->id();

            $table->foreignId('post_id')->references('id')->on('posts');
            $table->foreignId('comment_id')->references('id')->on('comments');

            $table->boolean('pinned')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comments_posts');
    }
};
