<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AndroidApiSimInfo extends Model
{
    use HasFactory;

    const ACTIVE   = 1;
    const INACTIVE = 2;

    protected $fillable = [
        'android_gateway_id',
        'sim_number',
        'time_interval',
        'send_sms',
        'status'
    ];

    public function androidGatewayName()
    {
    	return $this->belongsTo(AndroidApi::class, 'android_gateway_id');
    }
}
