<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('form11', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('event_id');
            $table->foreign('event_id')->references('id')->on('events')->onDelete('cascade');

            $table->string('transportation_medium')->nullable();
            $table->string('driver')->nullable();

            $table->boolean('is_commex')->default(false);
            $table->boolean('is_dean')->default(false);
            $table->boolean('is_asd')->default(false);
            $table->boolean('is_ad')->default(false);
            $table->text('commex_remarks')->nullable();
            $table->text('dean_remarks')->nullable();
            $table->text('asd_remarks')->nullable();
            $table->text('ad_remarks')->nullable();
             // Idiomatic FK columns
            $table->foreignId('commex_approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('dean_approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('asd_approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('ad_approved_by')->nullable()->constrained('users')->nullOnDelete();

            $table->date('commex_approve_date')->nullable();
            $table->date('dean_approve_date')->nullable();
            $table->date('asd_approve_date')->nullable();
            $table->date('ad_approve_date')->nullable();


            $table->timestamps();
        });

        Schema::create('form11_travel_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('form11_id');
            $table->foreignId('form11_id')->constrained('form11')->onDelete('cascade');

            $table->date('date')->nullable();
            $table->string('from')->nullable();
            $table->string('to')->nullable();
            $table->dateTime('departure')->nullable();
            $table->dateTime('arrival')->nullable();
            $table->text('trip_duration')->nullable(); // optional, can compute instead
            $table->string('purpose')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('form11_travel_details');
        Schema::dropIfExists('form11');
    }
};
