<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductUomConversion extends Model
{
    protected $table = 'product_uom_conversions';
    protected $guarded = [];

    protected $casts = [
        'qty_convert' => 'float',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
