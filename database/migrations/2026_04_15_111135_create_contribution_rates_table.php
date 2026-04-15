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
        Schema::create('contribution_rates', function (Blueprint $table) {
            $table->id();
            $table->string('regime_code', 50);
            $table->date('effective_from');
            $table->date('effective_to')->nullable();
            $table->decimal('employer_rate', 7, 4);
            $table->decimal('worker_rate', 7, 4);
            $table->decimal('ceiling_amount', 14, 2)->nullable();
            $table->decimal('floor_amount', 14, 2)->nullable();
            $table->boolean('is_active')->default(true);

            $table->index(['regime_code', 'effective_from']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contribution_rates');
    }
};

