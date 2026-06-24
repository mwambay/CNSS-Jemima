<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mail_dispatches', function (Blueprint $table): void {
            $table->id();
            $table->string('type', 80);
            $table->string('recipient_email', 150);
            $table->string('recipient_name', 150)->nullable();
            $table->morphs('emailable');
            $table->timestamp('sent_at');
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->unique(['type', 'emailable_type', 'emailable_id'], 'mail_dispatches_unique_event');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mail_dispatches');
    }
};
