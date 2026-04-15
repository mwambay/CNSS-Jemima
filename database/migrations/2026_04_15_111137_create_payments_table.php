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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employer_id')->constrained('employers')->cascadeOnDelete();
            $table->string('payment_ref', 100)->unique();
            $table->date('payment_date');
            $table->decimal('amount', 16, 2);
            $table->string('channel', 30)->nullable();
            $table->string('status', 20)->default('RECEIVED');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('validated_at')->nullable();

            $table->index(['employer_id', 'payment_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};

