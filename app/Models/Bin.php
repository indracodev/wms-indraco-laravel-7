<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bin extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';
    protected $guarded = [];

    public function rack()
    {
        return $this->belongsTo(Rack::class, 'rack_id');
    }

    public function pallets()
    {
        return $this->hasMany(Pallet::class, 'bin_id')->where('is_active', true);
    }

    public function isFull(): bool
    {
        return $this->pallets()->count() >= $this->max_pallet;
    }
}
