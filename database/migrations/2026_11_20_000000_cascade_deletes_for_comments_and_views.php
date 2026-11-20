<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Comments and views blocked deleting their post or author; they now go with the post,
 * comments go with their author, and views of a deleted reader stay anonymous.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('comments', function (Blueprint $table) {
            $table->dropForeign(['post_id']);
            $table->dropForeign(['user_id']);
            $table->foreign('post_id')->references('id')->on('posts')->cascadeOnDelete();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });

        Schema::table('post_views', function (Blueprint $table) {
            $table->dropForeign(['post_id']);
            $table->dropForeign(['user_id']);
            $table->foreign('post_id')->references('id')->on('posts')->cascadeOnDelete();
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('comments', function (Blueprint $table) {
            $table->dropForeign(['post_id']);
            $table->dropForeign(['user_id']);
            $table->foreign('post_id')->references('id')->on('posts');
            $table->foreign('user_id')->references('id')->on('users');
        });

        Schema::table('post_views', function (Blueprint $table) {
            $table->dropForeign(['post_id']);
            $table->dropForeign(['user_id']);
            $table->foreign('post_id')->references('id')->on('posts');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }
};
