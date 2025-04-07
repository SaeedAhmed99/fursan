<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Traning extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id', 'training_topic', 'institution', 'start_date', 'certificate_file', 'hours', 'user_id'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
