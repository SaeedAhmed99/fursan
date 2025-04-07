<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContractPDFDetails extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'file_name', 'assign_to_all', 'selected_employees'];

    protected $casts = [
        'selected_employees' => 'array', 
        'assign_to_all' => 'boolean',
    ];

    public function signedFiles()
{
    return $this->hasMany(SignedFiles::class, 'contract_p_d_f_details_id');
}
}
