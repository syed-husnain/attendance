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
            $table->double('basic_salary',2);
            $table->double('travel_allowance',2)->default(0);
            $table->double('medical_allowance',2)->default(0);
            $table->double('bonus',2)->default(0);
            $table->double('working_days',2)->default(0);
            $table->time('working_hours',2)->nullable();
            $table->integer('late')->default(0);
            $table->double('salary',2)->default(0);

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
