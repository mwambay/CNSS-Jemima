<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('affiliation_requests', function (Blueprint $table): void {
            $table->string('sdt_opinion_status', 20)->nullable()->after('rejection_reason');
            $table->timestamp('sdt_opinion_requested_at')->nullable()->after('sdt_opinion_status');
            $table->foreignId('sdt_opinion_requested_by_user_id')
                ->nullable()
                ->after('sdt_opinion_requested_at')
                ->constrained('users')
                ->nullOnDelete();
            $table->text('sdt_opinion_note')->nullable()->after('sdt_opinion_requested_by_user_id');
            $table->timestamp('sdt_opinion_given_at')->nullable()->after('sdt_opinion_note');
            $table->foreignId('sdt_opinion_given_by_user_id')
                ->nullable()
                ->after('sdt_opinion_given_at')
                ->constrained('users')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('affiliation_requests', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('sdt_opinion_requested_by_user_id');
            $table->dropConstrainedForeignId('sdt_opinion_given_by_user_id');
            $table->dropColumn([
                'sdt_opinion_status',
                'sdt_opinion_requested_at',
                'sdt_opinion_note',
                'sdt_opinion_given_at',
            ]);
        });
    }
};
