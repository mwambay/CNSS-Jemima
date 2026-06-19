<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('affiliation_requests', function (Blueprint $table): void {
            $table->id();
            $table->string('tracking_number', 40)->unique();
            $table->string('management_center', 150)->nullable();
            $table->string('registration_number', 80)->nullable();
            $table->string('status', 20)->default('PENDING');
            $table->foreignId('created_employer_id')->nullable()->constrained('employers')->nullOnDelete();
            $table->foreignId('processed_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('processed_at')->nullable();
            $table->text('rejection_reason')->nullable();

            $table->string('legal_name', 200)->nullable();
            $table->string('abbreviation', 80)->nullable();
            $table->string('physical_employer_name', 200)->nullable();
            $table->string('street', 200)->nullable();
            $table->string('district', 120)->nullable();
            $table->string('municipality', 120)->nullable();
            $table->string('city', 120)->nullable();
            $table->string('province', 120)->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('fax', 30)->nullable();
            $table->text('transferor_names')->nullable();
            $table->text('transferor_affiliation_numbers')->nullable();
            $table->date('takeover_date')->nullable();
            $table->string('postal_box', 80)->nullable();
            $table->string('legal_form', 50)->nullable();
            $table->string('rccm_number', 80)->nullable();
            $table->string('rccm_delivered_at', 120)->nullable();
            $table->date('rccm_delivered_on')->nullable();
            $table->string('approval_order_ref', 150)->nullable();
            $table->string('creation_act_ref', 150)->nullable();
            $table->string('head_office', 200)->nullable();
            $table->unsignedInteger('operating_sites_count')->nullable();
            $table->text('operating_sites_addresses')->nullable();
            $table->string('email', 150)->nullable();
            $table->string('website', 200)->nullable();

            $table->string('primary_activity', 200)->nullable();
            $table->string('secondary_activity', 200)->nullable();
            $table->date('activity_start_date')->nullable();

            $table->date('personnel_employment_start_date')->nullable();
            $table->unsignedInteger('workers_count')->nullable();
            $table->unsignedInteger('assimilated_workers_count')->nullable();

            $table->decimal('monthly_workers_gross_pay', 16, 2)->nullable();
            $table->decimal('monthly_assimilated_workers_gross_income', 16, 2)->nullable();
            $table->decimal('monthly_contribution_base_total', 16, 2)->nullable();

            $table->string('signature_place', 120)->nullable();
            $table->date('signed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('affiliation_requests');
    }
};
