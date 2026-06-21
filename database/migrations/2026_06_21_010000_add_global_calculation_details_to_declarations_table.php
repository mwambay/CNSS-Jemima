<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('declarations', function (Blueprint $table): void {
            $table->decimal('global_salary_envelope', 16, 2)->nullable()->after('global_contribution_amount');
            $table->decimal('global_employer_rate', 7, 4)->nullable()->after('global_salary_envelope');
            $table->decimal('global_worker_rate', 7, 4)->nullable()->after('global_employer_rate');
            $table->unsignedInteger('global_worker_count')->nullable()->after('global_worker_rate');
        });
    }

    public function down(): void
    {
        Schema::table('declarations', function (Blueprint $table): void {
            $table->dropColumn([
                'global_salary_envelope',
                'global_employer_rate',
                'global_worker_rate',
                'global_worker_count',
            ]);
        });
    }
};
