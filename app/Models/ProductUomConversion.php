<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductUomConversion extends Model
{
    protected $table = 'product_uom_conversions';
    protected $guarded = [];

    protected $casts = [
        'level' => 'integer',
        'qty_convert' => 'float',
        'multiplier_to_base' => 'float',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
