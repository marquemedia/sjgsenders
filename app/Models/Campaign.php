<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
    use HasFactory;
    protected $guarded = [];

    const EMAIL = "email";
    const SMS = 'sms';
    const WHATSAPP = 'whatsapp';

    protected $casts = [
      'post_data' => 'json',
      'json_body' => 'object',
    ];

     /**
      * define campaign & contact realtion
      */

      public function contacts(){
        return $this->hasMany(CampaignContact::class, 'campaign_id', 'id')->latest();
      }

     /**
      * get subscried conatacts
      */
      public function subscribed(){
        return $this->belongsToMany(Contact::class, 'campaign_contacts', 'campaign_id', 'contact_id')->withPivot(['contact','unsubscribed','sent_at','delivered_at','response_message','status'])->wherePivot('unsubscribed','No')->using(CampaignContact::class);;
      }

      /**
       * get campaign schedule
       */
      public function schedule(){
        return $this->hasOne(CampaignSchedule::class, 'campaign_id', 'id');
      }


      /**
       * Summary of scopeActive
       * @param mixed $q
       * @return mixed
       */
      public function scopeActive($q){
        return $q->where('status', 'Active');
      }
      /**
       * Summary of scopeActive
       * @param mixed $q
       * @return mixed
       */
      public function scopeOngoing($q){
        return $q->where('status', 'Ongoing');
      }
      /**
       * Summary of scopeActive
       * @param mixed $q
       * @return mixed
       */
      public function scopedrafts($q){
        return $q->where('draft', 'Yes');
      }



      /**
       * Summary of scopeDeActive
       * @param mixed $q
       * @return mixed
       */
      public function scopeDeActive($q){
        return $q->where('status', 'DeActive');
      }


    /**
     *scope filter
     */

    public function scopefilter($q,$request){
        return $q->when(
            $request->type &&   $request->type != 'All' ,
            function ($q) use ($request) {
                return $q->where('channel', $request->type);
            },
            function ($q) {
                return $q;
            })
            ->when($request->status &&  $request->status !='All', function($q) use($request){
              return $q->where('status', $request->status);
            })
            ->when($request->searchData !=null,function ($q) use ($request) {
            return $q->where('name', 'like', '%' .$request->searchData.'%')
            ->orWhere('schedule_status', 'like', '%' .$request->searchData.'%')
            ->orWhere('last_corn_run', 'like', '%' .$request->searchData.'%')
            ->orWhere('subject', 'like', '%' .$request->searchData.'%')
            ;
        });
    }

}
