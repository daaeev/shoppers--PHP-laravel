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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug');
            $table->string('subname')->unique();
            $table->text('description');
            $table->integer('price');
            $table->integer('discount_price');
            $table->unsignedBigInteger('category_id');
            $table->unsignedBigInteger('size_id');
            $table->unsignedBigInteger('color_id');
            $table->smallInteger('count');
            $table->string('main_image');
            $table->string('preview_image');
            $table->timestamps();

            $table->foreign('category_id', 'fk-products-category_id')
                ->references('id')
                ->on('categories')
                ->onDelete('cascade');;

            $table->foreign('size_id', 'fk-products-size_id')
                ->references('id')
                ->on('sizes')
                ->onDelete('cascade');;

            $table->foreign('color_id', 'fk-products-color_id')
                ->references('id')
                ->on('colors')
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
            $table->dropForeign('fk-products-category_id');
            $table->dropForeign('fk-products-size_id');
            $table->dropForeign('fk-products-color_id');
        });
        Schema::dropIfExists('products');
    }
};
