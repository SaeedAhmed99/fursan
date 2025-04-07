<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('signed_files', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('contract_p_d_f_details_id');
            $table->unsignedBigInteger('employee_id');
            $table->string('file_name'); 
            $table->timestamps();

            $table->foreign('contract_p_d_f_details_id')->references('id')->on('contract_p_d_f_details')->onDelete('cascade');
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('signed_files');
    }
};
