<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WhatsappDevice extends Model
{
    use HasFactory;
    protected $table = 'wa_device';
    protected $guarded = []; 

    const DISCONNECTED = "disconnected";
    const INITIATED    = "initiated";
    const CONNECTED    = "connected";

    const NODE     = 0;
    const BUSINESS = 1;

    protected $casts = [
        "credentials" => "json"
    ];

    protected static function booted()
    {
        static::creating(function ($contact) {
            $contact->uid = str_unique();
        });
    }

    public function template()
    {
        return $this->hasMany(WhatsappTemplate::class, 'cloud_id');
    }
}
