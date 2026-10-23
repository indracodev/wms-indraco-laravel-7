<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pallet extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';
    protected $guarded = [];

    protected $casts = [
        'is_active' => 'boolean',
        'max_capacity' => 'integer',
    ];

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

    public function activeBoxes()
    {
        return $this->hasMany(Box::class, 'pallet_id')->where('status', 'active');
    }

    public function isInTransit(): bool
    {
        return $this->status === 'in_transit' || empty($this->bin_id);
    }

    public function isOnRack(): bool
    {
        return $this->status === 'on_rack' && !empty($this->bin_id);
    }

    public function getTotalActiveQtyAttribute(): float
    {
        return (float) $this->activeBoxes()->sum('qty');
    }

    public function getActiveProductIdsAttribute(): array
    {
        return $this->activeBoxes()->pluck('product_id')->unique()->values()->all();
    }
}
