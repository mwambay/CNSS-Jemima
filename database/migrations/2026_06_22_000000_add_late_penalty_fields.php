<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contribution_rates', function (Blueprint $table): void {
            $table->decimal('late_penalty_daily_rate', 7, 4)->default(0.5)->after('worker_rate');
        });

        Schema::table('declarations', function (Blueprint $table): void {
            $table->date('global_contribution_date')->nullable()->after('global_amount_due');
            $table->unsignedInteger('global_late_days')->nullable()->after('global_contribution_date');
            $table->decimal('global_late_penalty_rate', 7, 4)->nullable()->after('global_late_days');
            $table->decimal('global_late_penalty_amount', 16, 2)->nullable()->after('global_late_penalty_rate');
            $table->decimal('global_total_payable', 16, 2)->nullable()->after('global_late_penalty_amount');
        });

        DB::table('declarations')
            ->select(['id', 'period_year', 'period_month'])
            ->orderBy('id')
            ->each(function (object $declaration): void {
                $dueDate = Carbon::create((int) $declaration->period_year, (int) $declaration->period_month, 1)
                    ->addMonth()
                    ->day(15)
                    ->toDateString();

                DB::table('declarations')->where('id', $declaration->id)->update(['due_date' => $dueDate]);
            });

        DB::table('declarations')
            ->where('contribution_entry_mode', 'GLOBAL')
            ->whereNotNull('global_amount_due')
            ->update([
                'global_late_days' => 0,
                'global_late_penalty_rate' => 0.5,
                'global_late_penalty_amount' => 0,
                'global_total_payable' => DB::raw('global_amount_due'),
            ]);
    }

    public function down(): void
    {
        Schema::table('declarations', function (Blueprint $table): void {
            $table->dropColumn([
                'global_contribution_date',
                'global_late_days',
                'global_late_penalty_rate',
                'global_late_penalty_amount',
                'global_total_payable',
            ]);
        });

        Schema::table('contribution_rates', function (Blueprint $table): void {
            $table->dropColumn('late_penalty_daily_rate');
        });
    }
};
