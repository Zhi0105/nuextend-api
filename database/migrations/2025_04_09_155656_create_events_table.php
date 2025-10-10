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
        Schema::create('events', function (Blueprint $table) {
            $table->id(); // Primary Key
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('organization_id')->nullable();
            $table->unsignedBigInteger('model_id')->nullable();
            $table->unsignedBigInteger('event_type_id')->nullable();
            $table->unsignedBigInteger('event_status_id')->nullable();
            $table->unsignedBigInteger('target_group_id')->nullable(); // step 1: make it nullable
            $table->bigInteger('budget_proposal')->nullable();
            // $table->string('program_model_name')->nullable();
            $table->string('term');
            $table->string('location');
            $table->date('implement_date')->nullable(); 
            $table->text('description')->nullable(); // Optional, remove nullable() if required

            $table->timestamps(); // created_at and updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
