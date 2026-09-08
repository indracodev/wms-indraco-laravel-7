<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Box extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';
    protected $guarded = [];

    protected $casts = [
        'qty' => 'float',
        'initial_qty' => 'float',
    ];

    public function pallet()
    {
        return $this->belongsTo(Pallet::class, 'pallet_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
