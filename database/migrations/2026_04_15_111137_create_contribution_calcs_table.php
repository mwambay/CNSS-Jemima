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
        Schema::create('contribution_calcs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('declaration_line_id')->constrained('declaration_lines')->cascadeOnDelete();
            $table->foreignId('rate_id')->constrained('contribution_rates')->restrictOnDelete();
            $table->decimal('employer_amount', 14, 2);
            $table->decimal('worker_amount', 14, 2);
            $table->decimal('total_amount', 14, 2);
            $table->timestamp('calculated_at')->useCurrent();

            $table->unique('declaration_line_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contribution_calcs');
    }
};
