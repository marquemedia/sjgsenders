<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WhatsappCreditLog extends Model
{
    use HasFactory;

    /**
     * @var string[]
     */
    protected  $fillable = [
        'user_id', 'type', 'credit', 'trx_number', 'post_credit', 'details'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
