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
        Schema::create('declarations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employer_id')->constrained('employers')->cascadeOnDelete();
            $table->unsignedInteger('period_year');
            $table->unsignedTinyInteger('period_month');
            $table->timestamp('submitted_at')->nullable();
            $table->date('due_date');
            $table->string('status', 20)->default('DRAFT');
            $table->decimal('total_declared_salary', 16, 2)->default(0);
            $table->decimal('total_declared_contribution', 16, 2)->default(0);
            $table->text('validation_message')->nullable();
            $table->timestamps();

            $table->unique(['employer_id', 'period_year', 'period_month']);
            $table->index(['employer_id', 'period_year', 'period_month']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('declarations');
    }
};

