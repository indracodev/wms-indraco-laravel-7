<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    public $incrementing = false;
    protected $keyType = 'string';
    protected $guarded = [];

    protected $hidden = [
        'password', 'remember_token',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function hasPermission(string $module, string $action = 'read'): bool
    {
        if ($this->role_id === 'super_admin') {
            return true;
        }

        $perm = RolePermission::where('role_id', $this->role_id)
                    ->where('module', $module)
                    ->first();

        if (!$perm) {
            return false;
        }

        return (bool) $perm->{"can_{$action}"};
    }
}
