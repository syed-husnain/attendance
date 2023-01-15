<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalariesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('salaries', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->date('from_date');
            $table->date('to_date');
            $table->double('basic_salary',10,2);
            $table->double('travel_allowance',10,2)->default(0.00);
            $table->double('medical_allowance',10,2)->default(0.00);
            $table->double('bonus',10,2)->default(0.00);
            $table->double('working_days',10,2)->default(0.00);
            $table->time('working_hours')->nullable();
            $table->integer('late')->default(0);
            $table->integer('absent')->default(0);
            $table->double('salary',10,2)->default(0.00);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('salaries');
    }
}
