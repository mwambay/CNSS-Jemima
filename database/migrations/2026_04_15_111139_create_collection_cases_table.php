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
        Schema::create('collection_cases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employer_id')->constrained('employers')->cascadeOnDelete();
            $table->foreignId('declaration_id')->nullable()->constrained('declarations')->nullOnDelete();
            $table->timestamp('opened_at')->useCurrent();
            $table->string('status', 20)->default('OPEN');
            $table->decimal('amount_due', 16, 2)->default(0);
            $table->decimal('amount_recovered', 16, 2)->default(0);
            $table->timestamp('closed_at')->nullable();

            $table->index(['employer_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('collection_cases');
    }
};

