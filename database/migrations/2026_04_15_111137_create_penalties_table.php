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
        Schema::create('penalties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employer_id')->constrained('employers')->cascadeOnDelete();
            $table->foreignId('declaration_id')->constrained('declarations')->cascadeOnDelete();
            $table->string('penalty_type', 30);
            $table->decimal('base_amount', 14, 2);
            $table->decimal('penalty_rate', 7, 4);
            $table->unsignedInteger('days_late')->default(0);
            $table->decimal('amount', 14, 2);
            $table->string('status', 20)->default('PENDING');
            $table->timestamp('assessed_at')->useCurrent();
            $table->timestamp('paid_at')->nullable();

            $table->index(['employer_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penalties');
    }
};

