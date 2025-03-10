<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddQuantityToProductLabels extends Migration
{
    public function up()
    {
        Schema::table('product_labels', function (Blueprint $table) {
            $table->integer('quantity')->default(1)->after('unique_id');
        });
    }

    public function down()
    {
        Schema::table('product_labels', function (Blueprint $table) {
            $table->dropColumn('quantity');
        });
    }
}