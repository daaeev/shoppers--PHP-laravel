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
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->string('subname')->unique();
            $table->text('description');
            $table->unsignedFloat('price');
            $table->unsignedFloat('discount_price')->nullable();
            $table->unsignedBigInteger('category_id');
            $table->unsignedBigInteger('size_id');
            $table->unsignedBigInteger('color_id');
            $table->unsignedSmallInteger('count');
            $table->string('main_image');
            $table->string('preview_image')->nullable();
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
            $table->dropIndex('fk-products-category_id');

            $table->dropForeign('fk-products-size_id');
            $table->dropIndex('fk-products-size_id');

            $table->dropForeign('fk-products-color_id');
            $table->dropIndex('fk-products-color_id');
        });

        Schema::dropIfExists('products');
    }
};
