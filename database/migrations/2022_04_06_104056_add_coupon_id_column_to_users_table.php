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
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('coupon_id')->nullable();

            $table->foreign('coupon_id', 'fk-users-coupon_id')
                ->onDelete('set null')
                ->references('id')
                ->on('coupons');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign('fk-users-coupon_id');
            $table->dropIndex('fk-users-coupon_id');
            $table->dropColumn('coupon_id');
        });
    }
};
