<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->string('first_name', 30);
            $table->string('last_name', 30);
            $table->string('email', 256);
            $table->text('title')->nullable();
            $table->text('content');
            $table->unsignedBigInteger('user_id');
            $table->boolean('answered')->default(false);
            $table->timestamps();

            $table->foreign('user_id', 'fk-messages-user_id')
                ->references('id')
                ->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->dropForeign('fk-messages-user_id');
        });

        Schema::dropIfExists('messages');
    }
};
