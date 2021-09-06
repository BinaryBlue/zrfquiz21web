<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSupplierBillTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lte_supplier_bill', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('date');
            $table->year('fy');
            $table->integer('supplier_id');
            $table->string('supplier_name');
            $table->float('bill');
            $table->string('statement');

            

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
        Schema::dropIfExists('lte_supplier_bill');
    }
}
