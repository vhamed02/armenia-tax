<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tax_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('report_period_start');
            $table->date('report_period_end');
            $table->bigInteger('total_income')->default(0);
            $table->bigInteger('income_limit')->default(0);
            $table->bigInteger('excess_income')->default(0);
            $table->decimal('tax_rate', 5, 2)->default(0);
            $table->bigInteger('tax_amount')->default(0);
            $table->enum('status', ['pending', 'submitted', 'acknowledged'])->default('pending');
            $table->timestamp('submitted_to_gov_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tax_reports');
    }
};
