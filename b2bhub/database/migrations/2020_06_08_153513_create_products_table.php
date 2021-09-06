<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lte_products', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('code');
            $table->string('name');
            $table->mediumText('description');
            $table->integer('cat_id');
            $table->string('cat_name');
            $table->integer('size_id');
            $table->string('size_name');
            $table->integer('supplier_id');
            $table->string('supplier_name');
            $table->integer('metric_id');
            $table->string('metric_name');

            $table->string('photo');
            
            $table->float('mrp');
            $table->json('rpp');

            $table->integer('stock');

            $table->enum('active', ['yes', 'no']);

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
        Schema::dropIfExists('lte_products');
    }
}
