<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';
    protected $guarded = [];

    public function office()
    {
        return $this->belongsTo(Office::class, 'office_id');
    }

    public function warehouses()
    {
        return $this->hasMany(Warehouse::class, 'branch_id');
    }
}
