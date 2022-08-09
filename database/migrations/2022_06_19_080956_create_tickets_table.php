<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId("patient_id")->nullable()->references("id")->on("users");
            $table->foreignId("therapist_id")->nullable()->references("id")->on("therapists");
            $table->foreignId("ticket_department_id")->nullable()->references("id")->on("ticket_departments");
            $table->string("location")->nullable();
            $table->string("language");
            $table->date("date");
            $table->string("strike");
            $table->string("strike_history")->nullable();
            $table->string("ticket_history")->nullable();
            $table->string("remarks")->nullable();
            $table->boolean("status");
            $table->unsignedBigInteger("created_by")->nullable();
            $table->unsignedBigInteger("updated_by")->nullable();
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
        Schema::dropIfExists('tickets');
    }
};
