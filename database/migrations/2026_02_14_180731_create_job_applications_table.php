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
        Schema::create('job_applications', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('postedjob_id');
            $table->unsignedBigInteger('recruiter_id');

            $table->string('full_name');
            $table->string('email');
            $table->string('phone');
            $table->integer('years_of_experience')->nullable();
            $table->string('resume')->nullable();
            $table->text('cover_letter')->nullable();

            $table->timestamps();

            // $table->foreign('postedjob_id')
            //     ->references('id')
            //     ->on('postedjob')
            //     ->onDelete('cascade');

            // $table->foreign('recruiter_id')
            //     ->references('id')
            //     ->on('recruiters')
            //     ->onDelete('cascade');JobApplication
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_applications');
    }
};