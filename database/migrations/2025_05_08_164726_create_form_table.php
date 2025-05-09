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
        Schema::create('forms', function (Blueprint $table) {
            $table->id(); // Primary key

            $table->unsignedBigInteger('event_id'); // Foreign key
            // Foreign key constraint
            $table->foreign('event_id')->references('id')->on('events')->onDelete('cascade');
            $table->string('name');
            $table->string('code');
            $table->string('file'); // Assuming this stores the path to the PDF
            $table->boolean('is_dean')->default(false);
            $table->boolean('is_asd')->default(false);
            $table->boolean('is_ad')->default(false);
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('forms');
    }
};
