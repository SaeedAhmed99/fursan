<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Edu extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id', 'educational_level', 'university', 'academic_major', 'country_of_graduation', 'graduation_date', 'user_id'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
