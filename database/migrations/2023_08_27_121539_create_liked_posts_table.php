<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLikedPostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * предназначена для отслеживания "лайков",
     * которые пользователи ставят различным постам в системе
     *
     * @return void
     */
    public function up()
    {
        Schema::create('liked_posts', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->index()->constrained('users');
            $table->foreignId('post_id')->index()->constrained('posts');

            // Это уникальное ограничение для комбинации user_id и post_id
            // гарантирует, что один и тот же пользователь не сможет лайкнуть один и
            // тот же пост несколько раз. Каждая комбинация пользователя и поста будет уникальной.
            $table->unique(['user_id','post_id']);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('liked_posts');
    }
}
