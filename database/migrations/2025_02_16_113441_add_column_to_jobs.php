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
        Schema::table('jobs', function (Blueprint $table) {
            $table->string('job_type')->nullable();
            $table->integer('years_of_experience')->nullable();
            $table->string('show_pay_by')->nullable();
            $table->integer('starting_salary')->nullable();
            $table->string('currency')->nullable();
            $table->string('rate')->nullable();
            $table->string('major')->nullable();
            $table->string('degree')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jobs', function (Blueprint $table) {
            $table->dropColumn('job_type');
            $table->dropColumn('years_of_experience');
            $table->dropColumn('show_pay_by');
            $table->dropColumn('starting_salary ');
            $table->dropColumn('currency ');
            $table->dropColumn('rate');
            $table->dropColumn('major');
            $table->dropColumn('degree');
        });
    }
};
