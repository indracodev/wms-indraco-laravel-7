<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImpersonationLog extends Model
{
    public $timestamps = false;
    protected $guarded = [];

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_user_id');
    }

    public function target()
    {
        return $this->belongsTo(User::class, 'target_user_id');
    }
}
