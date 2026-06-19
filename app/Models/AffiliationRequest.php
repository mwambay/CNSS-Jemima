<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AffiliationRequest extends Model
{
    protected $fillable = [
        'tracking_number',
        'management_center',
        'registration_number',
        'status',
        'created_employer_id',
        'processed_by_user_id',
        'processed_at',
        'rejection_reason',
        'legal_name',
        'abbreviation',
        'physical_employer_name',
        'street',
        'district',
        'municipality',
        'city',
        'province',
        'phone',
        'fax',
        'transferor_names',
        'transferor_affiliation_numbers',
        'takeover_date',
        'postal_box',
        'legal_form',
        'rccm_number',
        'rccm_delivered_at',
        'rccm_delivered_on',
        'approval_order_ref',
        'creation_act_ref',
        'head_office',
        'operating_sites_count',
        'operating_sites_addresses',
        'email',
        'website',
        'primary_activity',
        'secondary_activity',
        'activity_start_date',
        'personnel_employment_start_date',
        'workers_count',
        'assimilated_workers_count',
        'monthly_workers_gross_pay',
        'monthly_assimilated_workers_gross_income',
        'monthly_contribution_base_total',
        'signature_place',
        'signed_at',
    ];

    protected function casts(): array
    {
        return [
            'processed_at' => 'datetime',
            'takeover_date' => 'date',
            'rccm_delivered_on' => 'date',
            'activity_start_date' => 'date',
            'personnel_employment_start_date' => 'date',
            'signed_at' => 'date',
            'monthly_workers_gross_pay' => 'decimal:2',
            'monthly_assimilated_workers_gross_income' => 'decimal:2',
            'monthly_contribution_base_total' => 'decimal:2',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function employer(): BelongsTo
    {
        return $this->belongsTo(Employer::class, 'created_employer_id');
    }

    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by_user_id');
    }
}
