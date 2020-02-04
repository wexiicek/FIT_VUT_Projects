<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTicketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('eventInstance_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->integer('ticket_amount');
            $table->float('price');
            $table->string('email')->nullable();
            $table->boolean('paid')->default(false);
            $table->string('seats')->nullable();
            $table->dateTime('confirmed_at')->nullable();
            $table->boolean('canceled')->default(false);
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
        Schema::dropIfExists('tickets');
    }
}
