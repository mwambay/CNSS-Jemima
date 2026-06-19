<?php

namespace App\Http\Controllers;

use App\Http\Requests\ApproveAffiliationRequest;
use App\Http\Requests\RejectAffiliationRequest;
use App\Http\Requests\StoreAffiliationRequest;
use App\Models\AffiliationRequest;
use App\Models\Employer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AffiliationRequestController extends Controller
{
    public function create(): View
    {
        return view('affiliations.create');
    }

    public function store(StoreAffiliationRequest $request): RedirectResponse
    {
        $affiliationRequest = AffiliationRequest::query()->create([
            ...$request->validated(),
            'tracking_number' => $this->generateTrackingNumber(),
            'status' => 'PENDING',
        ]);

        return redirect()->route('affiliation.submitted', $affiliationRequest);
    }

    public function submitted(AffiliationRequest $affiliationRequest): View
    {
        return view('affiliations.submitted', [
            'affiliationRequest' => $affiliationRequest,
        ]);
    }

    public function index(): View
    {
        $affiliationRequests = AffiliationRequest::query()
            ->latest()
            ->paginate(15);

        return view('affiliations.index', [
            'affiliationRequests' => $affiliationRequests,
        ]);
    }

    public function show(AffiliationRequest $affiliationRequest): View
    {
        $affiliationRequest->load(['employer', 'processedBy']);

        return view('affiliations.show', [
            'affiliationRequest' => $affiliationRequest,
        ]);
    }

    public function approve(ApproveAffiliationRequest $request, AffiliationRequest $affiliationRequest): RedirectResponse
    {
        if ($affiliationRequest->status !== 'PENDING') {
            throw ValidationException::withMessages([
                'affiliation_number' => 'Cette demande a deja ete traitee.',
            ]);
        }

        DB::transaction(function () use ($request, $affiliationRequest): void {
            $employer = Employer::query()->create([
                'affiliation_number' => $request->validated('affiliation_number'),
                'legal_name' => $affiliationRequest->legal_name ?: $affiliationRequest->physical_employer_name,
                'registration_number' => $affiliationRequest->rccm_number,
                'legal_form' => $affiliationRequest->legal_form,
                'sector' => $affiliationRequest->primary_activity,
                'status' => 'ACTIVE',
                'verification_status' => 'VERIFIED',
                'phone' => $affiliationRequest->phone,
                'email' => $affiliationRequest->email,
                'address' => $this->buildAddress($affiliationRequest),
            ]);

            $affiliationRequest->update([
                'status' => 'APPROVED',
                'created_employer_id' => $employer->id,
                'processed_by_user_id' => auth()->id(),
                'processed_at' => now(),
                'rejection_reason' => null,
            ]);
        });

        return redirect()
            ->route('affiliations.show', $affiliationRequest)
            ->with('status', 'Demande approuvee et employeur cree.');
    }

    public function reject(RejectAffiliationRequest $request, AffiliationRequest $affiliationRequest): RedirectResponse
    {
        if ($affiliationRequest->status !== 'PENDING') {
            throw ValidationException::withMessages([
                'rejection_reason' => 'Cette demande a deja ete traitee.',
            ]);
        }

        $affiliationRequest->update([
            'status' => 'REJECTED',
            'processed_by_user_id' => auth()->id(),
            'processed_at' => now(),
            'rejection_reason' => $request->validated('rejection_reason'),
        ]);

        return redirect()
            ->route('affiliations.show', $affiliationRequest)
            ->with('status', 'Demande rejetee.');
    }

    private function generateTrackingNumber(): string
    {
        do {
            $trackingNumber = 'AFF-'.now()->format('Ymd').'-'.Str::upper(Str::random(6));
        } while (AffiliationRequest::query()->where('tracking_number', $trackingNumber)->exists());

        return $trackingNumber;
    }

    private function buildAddress(AffiliationRequest $affiliationRequest): ?string
    {
        $parts = array_filter([
            $affiliationRequest->street,
            $affiliationRequest->district,
            $affiliationRequest->municipality,
            $affiliationRequest->city,
            $affiliationRequest->province,
        ]);

        return $parts === [] ? null : implode(', ', $parts);
    }
}
