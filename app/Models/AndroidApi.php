<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Passport\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;

class AndroidApi extends Authenticatable
{
    use HasFactory,HasApiTokens;
    const ACTIVE   = 1;
    const INACTIVE = 2;

    protected $fillable = [
        'name',
        'password',
        'show_password',
        'admin_id',
        'user_id',
        'status',
    ];

    /**
     * @return HasMany
     */
    public function simInfo(): HasMany
    {
        return $this->hasMany(AndroidApiSimInfo::class, 'android_gateway_id');
    }
}
