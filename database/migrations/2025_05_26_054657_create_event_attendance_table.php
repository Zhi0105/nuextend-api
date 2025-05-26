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
        Schema::create('event_attendance', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('participant_id');
            $table->date('attendance_date');
            $table->boolean('is_attended')->default(false);


            // Constraints
            $table->foreign('participant_id')->references('id')->on('participants')->onDelete('cascade');

            // Optional: Ensure one log per day per participant
            $table->unique(['participant_id', 'attendance_date']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_attendance');
    }
};
