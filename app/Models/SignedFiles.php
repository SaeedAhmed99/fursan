<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SignedFiles extends Model
{
    use HasFactory;

    protected $fillable = ['contract_p_d_f_details_id', 'employee_id', 'file_name'];


    public function file()
    {
        return $this->belongsTo(ContractPDFDetails::class, 'contract_p_d_f_details_id',);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
