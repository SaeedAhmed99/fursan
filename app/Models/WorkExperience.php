<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkExperience extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id', 'job_title', 'company_name', 'start_date', 'end_date', 'job_detail', 'user_id'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
