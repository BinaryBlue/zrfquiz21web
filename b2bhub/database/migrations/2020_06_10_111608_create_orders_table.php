<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lte_orders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->year('fy');
            $table->string('date');
            $table->string('order_no');
            $table->integer('customer');
            $table->string('mobile');
            $table->integer('outlet');
            $table->json('items');
            $table->double('sub_total')->default(0.0);
            $table->double('discount')->default(0.0);
            $table->double('delivery_fee')->default(0.0);
            $table->double('vat')->default(0.0);
            $table->double('net_payable')->default(0.0);
            $table->double('paid')->default(0.0);
            $table->longText('remarks');
            
            $table->enum('status', ['Pending', 'Confirmed','Completed','Canceled']); // 0,pending, 1 delivery chanel, 2 delivered, 3 canceled

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
        Schema::dropIfExists('lte_orders');
    }
}
