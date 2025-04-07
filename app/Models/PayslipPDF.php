<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayslipPDF extends Model
{
    use HasFactory;
    protected $fillable = ['pdf_file', 'user_id', 'employee_id'];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
