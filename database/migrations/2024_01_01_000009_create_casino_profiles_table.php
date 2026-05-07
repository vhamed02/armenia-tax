<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('casino_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('service_provider_id')->constrained()->cascadeOnDelete();
            $table->bigInteger('wallet_balance')->default(0);
            $table->enum('kyc_status', ['not_started', 'in_progress', 'verified', 'failed'])->default('not_started');
            $table->string('kyc_session_token')->nullable();
            $table->timestamp('kyc_verified_at')->nullable();
            $table->string('national_id_verified')->nullable();
            $table->string('casino_username')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'service_provider_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('casino_profiles');
    }
};
