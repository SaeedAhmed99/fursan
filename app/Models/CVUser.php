<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CVUser extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id', 'cv_file', 'user_id'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
