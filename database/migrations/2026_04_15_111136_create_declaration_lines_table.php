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
        Schema::create('declaration_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('declaration_id')->constrained('declarations')->cascadeOnDelete();
            $table->foreignId('worker_id')->constrained('workers')->restrictOnDelete();
            $table->decimal('gross_salary', 14, 2);
            $table->decimal('contributable_salary', 14, 2);
            $table->unsignedInteger('worked_days')->nullable();
            $table->boolean('anomaly_flag')->default(false);
            $table->text('anomaly_reason')->nullable();
            $table->timestamps();

            $table->unique(['declaration_id', 'worker_id']);
            $table->index(['worker_id', 'anomaly_flag']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('declaration_lines');
    }
};
