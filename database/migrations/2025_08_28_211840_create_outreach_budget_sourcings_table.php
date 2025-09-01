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
        Schema::create('outreach_budget_sourcings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('outreach_proposals_id'); // Foreign key

            // Foreign key constraint
            $table->foreign('outreach_proposals_id')->references('id')->on('outreach_proposals')->onDelete('cascade');
            $table->string('university')->nullable();
            $table->string('outreachGroup')->nullable();
            $table->string('service')->nullable();
            $table->string('other')->nullable();
            $table->string('total')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('outreach_budget_sourcings');
    }
};
