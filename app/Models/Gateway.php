<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Gateway extends Model
{
    use HasFactory;

    protected $table = "gateways";
    
    protected $casts = [
        'mail_gateways' => 'object',
        'sms_gateways'  => 'object',
    ];
     /**
     * @return void
     */
    protected static function booted()
    {
        static::creating(function ($smsLog) {
            $smsLog->uid = Str::uuid();
        });
    }

    public function user()
	{
		return $this->belongsTo(User::class, 'user_id');
	}

    public function scopeMail($query)
    {
        return $query->whereNotNull('mail_gateways');
    }
    public function scopeSms($query)
    {
        return $query->whereNotNull('sms_gateways');
    }
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }
}
