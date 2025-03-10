<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductLabel extends Model
{
    protected $fillable=[
        "name",
        "barcode",
        "type"
    ];
}
