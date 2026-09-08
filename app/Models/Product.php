<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';
    protected $guarded = [];

    protected $casts = [
        'is_active' => 'boolean',
        'qty_per_uom_group' => 'float',
    ];

    /**
     * Accessor for backward compatibility with default_unit
     */
    public function getDefaultUnitAttribute()
    {
        return $this->uom_inventory;
    }

    public function conversions()
    {
        return $this->hasMany(ProductUomConversion::class, 'product_id');
    }

    public function boxes()
    {
        return $this->hasMany(Box::class, 'product_id');
    }
}
