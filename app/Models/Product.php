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
        'manage_batch_no' => 'boolean',
        'purchase_item' => 'boolean',
        'sales_item' => 'boolean',
        'inventory_item' => 'boolean',
        'is_manual_uom' => 'boolean',
        'qty_per_uom_group' => 'float',
        'no_of_units' => 'integer',
        'length_cm' => 'float',
        'width_cm' => 'float',
        'height_cm' => 'float',
        'volume_m3' => 'float',
        'weight_gross_kg' => 'float',
        'weight_net_g' => 'float',
    ];

    /**
     * Accessor for backward compatibility with default_unit
     */
    public function getDefaultUnitAttribute()
    {
        return $this->uom_inventory;
    }

    public function getIsManualUomAttribute()
    {
        return $this->attributes['is_manual_uom'] ?? ($this->uom_group === 'Manual' || strtolower((string)$this->karton_to_pcs) === 'manual');
    }

    public function getFormattedDimensionsAttribute()
    {
        if ($this->length_cm > 0 || $this->width_cm > 0 || $this->height_cm > 0) {
            return "{$this->length_cm} × {$this->width_cm} × {$this->height_cm} cm";
        }
        return '-';
    }

    public function getVolumeFormattedAttribute()
    {
        if ($this->volume_m3 > 0) {
            return number_format($this->volume_m3, 4) . ' m³';
        }
        return '-';
    }

    public function conversions()
    {
        return $this->hasMany(ProductUomConversion::class, 'product_id')->orderBy('level', 'asc');
    }

    public function boxes()
    {
        return $this->hasMany(Box::class, 'product_id');
    }
}
