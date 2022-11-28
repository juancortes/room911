<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRecordsAccessesTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('records_accesses', function (Blueprint $table) {
            $table->id('id');
            $table->integer('cantidad')->default(0);
            $table->unsignedInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('records_accesses');
    }
}
