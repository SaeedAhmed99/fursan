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
        Schema::create('contract_p_d_f_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->nullable(); 
            $table->text('description')->nullable(); 
            $table->string('file_name')->nullable();
            $table->boolean('assign_to_all')->default(true);
            $table->json('selected_employees')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contract_p_d_f_details');
    }
};
