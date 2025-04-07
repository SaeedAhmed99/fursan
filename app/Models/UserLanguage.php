<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserLanguage extends Model
{
    use HasFactory;
    protected $fillable = ['language_name', 'level', 'user_id', 'employee_id'];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
