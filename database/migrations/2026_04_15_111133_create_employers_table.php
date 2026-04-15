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
        Schema::create('employers', function (Blueprint $table) {
            $table->id();
            $table->string('affiliation_number', 30)->unique();
            $table->string('legal_name', 200);
            $table->string('tax_id', 50)->nullable()->unique();
            $table->string('registration_number', 50)->nullable();
            $table->string('legal_form', 50)->nullable();
            $table->string('sector', 100)->nullable();
            $table->string('status', 20)->default('ACTIVE');
            $table->string('verification_status', 20)->default('PENDING');
            $table->string('phone', 30)->nullable();
            $table->string('email', 150)->nullable();
            $table->text('address')->nullable();
            $table->timestamps();

            $table->index(['status', 'verification_status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employers');
    }
};
