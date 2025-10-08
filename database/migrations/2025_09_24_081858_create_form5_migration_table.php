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
        Schema::create('form5', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('event_id');
            $table->foreign('event_id')->references('id')->on('events')->onDelete('cascade');
            
            $table->boolean('a')->nullable();  
            $table->boolean('b')->nullable();
            $table->boolean('c')->nullable();
            $table->boolean('d')->nullable();
            $table->boolean('e')->nullable();
            $table->boolean('f')->nullable();
            $table->boolean('g')->nullable();
            $table->boolean('h')->nullable();
            $table->boolean('i')->nullable();
            $table->boolean('j')->nullable();
            $table->boolean('k')->nullable();
            $table->boolean('l')->nullable();
            $table->boolean('m')->nullable();
            $table->boolean('n')->nullable();

            $table->boolean('is_commex')->default(false);
            $table->boolean('is_dean')->default(false);
            $table->boolean('is_asd')->default(false);
            $table->boolean('is_ad')->default(false);

            $table->boolean('is_revised')->default(false);
            $table->boolean('is_updated')->default(false);
            
            $table->text('commex_remarks')->nullable();
            $table->text('dean_remarks')->nullable();
            $table->text('asd_remarks')->nullable();
            $table->text('ad_remarks')->nullable();
             // Idiomatic FK columns
            $table->foreignId('commex_approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('dean_approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('asd_approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('ad_approved_by')->nullable()->constrained('users')->nullOnDelete();

            $table->datetime('commex_approve_date')->nullable();
            $table->datetime('dean_approve_date')->nullable();
            $table->datetime('asd_approve_date')->nullable();
            $table->datetime('ad_approve_date')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('form5');
    }
};
