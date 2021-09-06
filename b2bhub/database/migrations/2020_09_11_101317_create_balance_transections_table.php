<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBalanceTransectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lte_balance_transections', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->year('fy');
            $table->string('date');
            $table->string('transection_no');
            $table->integer('outlet');
            $table->float('amount');
            $table->integer('type');
            $table->string('order_no');
            $table->integer('balance_method');

            $table->timestamp('entry_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->string('entry_by');
            $table->timestamp('update_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->string('update_by');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lte_balance_transections');
    }
}
