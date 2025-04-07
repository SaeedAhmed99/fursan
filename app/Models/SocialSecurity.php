<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SocialSecurity extends Model
{
    use HasFactory;

    protected $fillable = [ 
        'employee_id',
        'title',
        'amount',
        'start_date',
        'end_date',
        'type',
        'created_by',
    ];

    public function employee()
    {
        return $this->hasOne('App\Models\Employee', 'id', 'employee_id')->first();
    }
}
