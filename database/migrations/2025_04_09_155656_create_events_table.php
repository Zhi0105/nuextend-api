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
            $table->string('program_model_name')->nullable();
            $table->string('name');
            $table->string('address');
            $table->string('term');
            $table->string('start_date');
            $table->string('end_date');
            $table->text('description')->nullable(); // Optional, remove nullable() if required
            $table->text('remarks')->nullable(); // Optional, remove nullable() if required
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
