<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRepostedIdToPostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('posts', function (Blueprint $table) {
            // Каждый пост, который является репостом, ссылается на оригинальный пост через поле 'reposted_id'.
            // может быть nullable, так как не все посты будут репостами.
            $table->foreignId('reposted_id')
                ->after('user_id')
                ->nullable()
                ->index()
                ->constrained('posts')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropForeign(['reposted_id']); // Удаление внешнего ключа
            $table->dropColumn('reposted_id'); // Удаление столбца
        });
    }
}
