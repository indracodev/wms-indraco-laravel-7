<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PalletType extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';
    protected $guarded = [];

    public function pallets()
    {
        return $this->hasMany(Pallet::class, 'pallet_type_id');
    }
}
