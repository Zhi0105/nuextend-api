<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('department_id')->nullable();
            $table->unsignedBigInteger('program_id')->nullable();
            $table->unsignedBigInteger('role_id')->nullable();
            $table->string('school_id')->nullable();
            $table->string('firstname');
            $table->string('middlename');
            $table->string('lastname');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('contact');
            $table->boolean('status')->default(false);
            $table->boolean('is_EsVolunteer')->default(false);
            $table->binary('esign')->nullable(); // will be changed to MEDIUMBLOB
            $table->rememberToken();
            $table->timestamps();
        });

        // Convert the binary column to MEDIUMBLOB
        DB::statement('ALTER TABLE users MODIFY esign MEDIUMBLOB NULL');
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
