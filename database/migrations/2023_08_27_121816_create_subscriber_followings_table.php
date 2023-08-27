<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubscriberFollowingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * В целом, таблица subscriber_followings позволяет отслеживать подписки пользователей на других пользователей.
     * Например, если в этой таблице есть запись с subscriber_id равным 1 и followings_id равным 2,
     * это означает, что пользователь с ID 1 подписан на пользователя с ID 2.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subscriber_followings', function (Blueprint $table) {
            $table->id();

            $table->foreignId('subscriber_id')->index()->constrained('users');
            $table->foreignId('followings_id')->index()->constrained('users');

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
        Schema::dropIfExists('subscriber_followings');
    }
}
