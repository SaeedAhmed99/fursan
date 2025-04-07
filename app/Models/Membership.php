<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Membership extends Model
{
    use HasFactory;
    protected $fillable = [
        'employee_id', 'organization_name', 'member_since', 'role_in_organization', 'user_id'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

}
