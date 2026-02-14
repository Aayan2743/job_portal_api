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
        Schema::create('postedjobs', function (Blueprint $table) {

            $table->id();

            // Who posted (Recruiter)
            $table->foreignId('recruiter_id')
                ->constrained('users')
                ->onDelete('cascade');

            $table->string('job_title');
            $table->string('company');
            $table->string('location');

            $table->enum('job_type', [
                'full-time',
                'part-time',
                'contract',
                'internship',
            ]);

            $table->decimal('salary_min', 10, 2)->nullable();
            $table->decimal('salary_max', 10, 2)->nullable();

            $table->longText('job_description');
            $table->longText('requirements')->nullable();

            $table->boolean('status')->default(1); // active/inactive

            $table->timestamps();
            $table->softDeletes();
      
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('postedjobs');
    }
};