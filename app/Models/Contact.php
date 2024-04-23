<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use HasFactory;
    const ACTIVE = 1;
    const BANNED = 2;
    protected $fillable = [
        'uid',
        'user_id',
        'group_id',
        'attributes',
        'whatsapp_contact',
        'email_contact',
        'sms_contact',
        'last_name',
        'first_name',
        'status'
    ];

    protected $casts = [
        "attributes" => "object"
    ];

    protected static function booted()
    {
        static::creating(function ($contact) {
            $contact->uid = str_unique();
        });
    }


    public function group()
    {
    	return $this->belongsTo(Group::class, 'group_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     *scope filter
     */

     public function scopefilter($q,$request){
       
        return $q->when($request->status &&  $request->status !='All', function($q) use($request) {

            return $q->where('status', $request->status);
            })->when($request->search !=null,function ($q) use ($request) {
              
            return $q->where('first_name', 'like', '%' .$request->search.'%')
            ->orWhere('whatsapp_contact', 'like', '%' .$request->search.'%')
            ->orWhere('sms_contact', 'like', '%' .$request->search.'%')
            ->orWhere('email_contact', 'like', '%' .$request->search.'%');
        
            
        });
    }
}
