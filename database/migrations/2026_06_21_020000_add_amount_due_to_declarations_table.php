<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('declarations', function (Blueprint $table): void {
            $table->decimal('global_amount_due', 16, 2)->nullable()->after('global_contribution_amount');
        });

        DB::table('declarations')
            ->where('contribution_entry_mode', 'GLOBAL')
            ->whereNotNull('global_contribution_amount')
            ->update(['global_amount_due' => DB::raw('global_contribution_amount')]);
    }

    public function down(): void
    {
        Schema::table('declarations', function (Blueprint $table): void {
            $table->dropColumn('global_amount_due');
        });
    }
};
