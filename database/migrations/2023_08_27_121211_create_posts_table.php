<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('content');

            // для хранения id из таблицы "users",
            // Устанавливает индекс на колонку user_id для ускорения поиска
            // Устанавливает внешний ключ, связывающий user_id с полем id в таблице users,
            // это обеспечивает ссылочную целостность данных.
            $table->foreignId('user_id')->index()->constrained('users');

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
        Schema::dropIfExists('posts');
    }
}
