<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAffiliationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'management_center' => ['nullable', 'string', 'max:150'],
            'registration_number' => ['nullable', 'string', 'max:80'],
            'legal_name' => ['nullable', 'required_without:physical_employer_name', 'string', 'max:200'],
            'abbreviation' => ['nullable', 'string', 'max:80'],
            'physical_employer_name' => ['nullable', 'required_without:legal_name', 'string', 'max:200'],
            'street' => ['nullable', 'string', 'max:200'],
            'district' => ['nullable', 'string', 'max:120'],
            'municipality' => ['nullable', 'string', 'max:120'],
            'city' => ['nullable', 'string', 'max:120'],
            'province' => ['nullable', 'string', 'max:120'],
            'phone' => ['required', 'string', 'max:30'],
            'fax' => ['nullable', 'string', 'max:30'],
            'transferor_names' => ['nullable', 'string'],
            'transferor_affiliation_numbers' => ['nullable', 'string'],
            'takeover_date' => ['nullable', 'date'],
            'postal_box' => ['nullable', 'string', 'max:80'],
            'legal_form' => ['nullable', Rule::in(['SARL', 'SA', 'ASBL', 'ETS', 'AUTRE'])],
            'rccm_number' => ['nullable', 'string', 'max:80'],
            'rccm_delivered_at' => ['nullable', 'string', 'max:120'],
            'rccm_delivered_on' => ['nullable', 'date'],
            'approval_order_ref' => ['nullable', 'string', 'max:150'],
            'creation_act_ref' => ['nullable', 'string', 'max:150'],
            'head_office' => ['nullable', 'string', 'max:200'],
            'operating_sites_count' => ['nullable', 'integer', 'min:0'],
            'operating_sites_addresses' => ['nullable', 'string'],
            'email' => ['nullable', 'email', 'max:150'],
            'website' => ['nullable', 'string', 'max:200'],
            'primary_activity' => ['nullable', 'string', 'max:200'],
            'secondary_activity' => ['nullable', 'string', 'max:200'],
            'activity_start_date' => ['nullable', 'date'],
            'personnel_employment_start_date' => ['nullable', 'date'],
            'workers_count' => ['nullable', 'integer', 'min:0'],
            'assimilated_workers_count' => ['nullable', 'integer', 'min:0'],
            'monthly_workers_gross_pay' => ['nullable', 'numeric', 'min:0'],
            'monthly_assimilated_workers_gross_income' => ['nullable', 'numeric', 'min:0'],
            'monthly_contribution_base_total' => ['nullable', 'numeric', 'min:0'],
            'signature_place' => ['nullable', 'string', 'max:120'],
            'signed_at' => ['nullable', 'date'],
        ];
    }
}
