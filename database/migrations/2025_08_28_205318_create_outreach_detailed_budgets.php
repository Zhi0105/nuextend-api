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
        Schema::create('outreach_detailed_budgets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('outreach_proposals_id'); // Foreign key

            // Foreign key constraint
            $table->foreign('outreach_proposals_id')->references('id')->on('outreach_proposals')->onDelete('cascade');
            $table->string('item')->nullable();
            $table->string('details')->nullable();
            $table->string('quantity')->nullable();
            $table->string('amount')->nullable();
            $table->string('total')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('outreach_detailed_budgets');
    }
};
