<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUniqueIdToProductLabels extends Migration
{
    public function up()
    {
        Schema::table('product_labels', function (Blueprint $table) {
            $table->string('unique_id', 10)->unique()->after('type');
        });
    }

    public function down()
    {
        Schema::table('product_labels', function (Blueprint $table) {
            $table->dropColumn('unique_id');
        });
    }
}