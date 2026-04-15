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
        Schema::create('fraud_alerts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rule_id')->constrained('fraud_rules')->restrictOnDelete();
            $table->foreignId('employer_id')->nullable()->constrained('employers')->nullOnDelete();
            $table->foreignId('worker_id')->nullable()->constrained('workers')->nullOnDelete();
            $table->foreignId('declaration_id')->nullable()->constrained('declarations')->nullOnDelete();
            $table->decimal('score', 5, 2)->default(0);
            $table->string('status', 20)->default('OPEN');
            $table->json('details')->nullable();
            $table->timestamp('detected_at')->useCurrent();
            $table->foreignId('assigned_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('resolved_at')->nullable();

            $table->index(['status', 'score']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fraud_alerts');
    }
};
