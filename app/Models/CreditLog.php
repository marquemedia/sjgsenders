<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CreditLog extends Model
{
    use HasFactory;


    protected $fillable = [
       'user_id', 'credit_type', 'credit', 'trx_number', 'post_credit', 'details'
    ];


    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
