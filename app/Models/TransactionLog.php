<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransactionLog extends Model
{
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class, 'pic_user_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function bin()
    {
        return $this->belongsTo(Bin::class, 'bin_id');
    }

    public function pallet()
    {
        return $this->belongsTo(Pallet::class, 'pallet_id');
    }

    public function box()
    {
        return $this->belongsTo(Box::class, 'box_id');
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id');
    }
}
