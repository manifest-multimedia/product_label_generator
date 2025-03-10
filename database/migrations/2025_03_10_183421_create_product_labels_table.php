<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductLabelsTable extends Migration
{
    public function up()
    {
        Schema::create('product_labels', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('barcode');
            $table->integer('type'); // 0 = Code128, 1 = QR
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('product_labels');
    }
}