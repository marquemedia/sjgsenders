<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class PricingPlan extends Model
{
    use HasFactory;
    const ENABLED = "1";
    const DISABLED  = "0";
    const USER  = "0";
    const ADMIN  = "1";

    protected $fillable = [
        'name',
        'type',
        'description',
        'amount',
        'sms', 
        'email', 
        'whatsapp', 
        'duration',
        'status',
        'carry_forward',
        'recommended_status'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        
        'sms' => 'object',
        'email' => 'object',
        'whatsapp' => 'object',
    ];

    public static function columnExists($columnName)
    {
        $table = (new static)->getTable();
        $columnExists = Schema::hasColumn($table, $columnName);

        return $columnExists;
    }
    
}
