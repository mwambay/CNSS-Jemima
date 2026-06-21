<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('declarations', function (Blueprint $table): void {
            $table->string('contribution_entry_mode', 20)->default('DETAILED')->after('status');
            $table->decimal('global_contribution_amount', 16, 2)->nullable()->after('total_declared_contribution');
        });
    }

    public function down(): void
    {
        Schema::table('declarations', function (Blueprint $table): void {
            $table->dropColumn(['contribution_entry_mode', 'global_contribution_amount']);
        });
    }
};
