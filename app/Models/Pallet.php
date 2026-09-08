<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pallet extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';
    protected $guarded = [];

    public function bin()
    {
        return $this->belongsTo(Bin::class, 'bin_id');
    }

    public function type()
    {
        return $this->belongsTo(PalletType::class, 'pallet_type_id');
    }

    public function boxes()
    {
        return $this->hasMany(Box::class, 'pallet_id');
    }
}
