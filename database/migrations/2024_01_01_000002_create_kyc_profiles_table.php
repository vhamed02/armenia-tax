<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kyc_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('status', ['pending', 'verified', 'rejected'])->default('pending');
            $table->bigInteger('annual_income_limit');
            $table->string('occupation')->nullable();
            $table->string('employer_name')->nullable();
            $table->timestamp('kyc_verified_at')->nullable();
            $table->enum('risk_level', ['low', 'medium', 'high'])->default('low');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kyc_profiles');
    }
};
