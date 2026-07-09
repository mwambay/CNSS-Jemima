<?php

namespace App\Http\Controllers;

use App\Http\Requests\ApproveAffiliationRequest;
use App\Http\Requests\RejectAffiliationRequest;
use App\Http\Requests\StoreAffiliationRequest;
use App\Models\AffiliationRequest;
use App\Models\Employer;
use App\Services\MailDispatchService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AffiliationRequestController extends Controller
{
    public function __construct(private readonly MailDispatchService $mailDispatchService)
    {
    }

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

        $this->mailDispatchService->sendAffiliationTracking($affiliationRequest);

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
            ->with(['sdtOpinionRequestedBy:id,full_name', 'sdtOpinionGivenBy:id,full_name'])
            ->latest()
            ->paginate(15);

        return view('affiliations.index', [
            'affiliationRequests' => $affiliationRequests,
        ]);
    }

    public function show(AffiliationRequest $affiliationRequest): View
    {
        $affiliationRequest->load([
            'employer',
            'processedBy',
            'sdtOpinionRequestedBy:id,full_name',
            'sdtOpinionGivenBy:id,full_name',
        ]);

        return view('affiliations.show', [
            'affiliationRequest' => $affiliationRequest,
            'sdtMode' => false,
        ]);
    }

    public function approve(ApproveAffiliationRequest $request, AffiliationRequest $affiliationRequest): RedirectResponse
    {
        $this->ensureSdtOpinionIsAnswered($affiliationRequest, 'affiliation_number');

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

        $affiliationRequest->refresh()->load('employer');
        $mailSent = $this->mailDispatchService->sendAffiliationDecision($affiliationRequest);

        return redirect()
            ->route('affiliations.show', $affiliationRequest)
            ->with('status', $mailSent
                ? 'Demande approuvee, employeur cree et mail envoye.'
                : 'Demande approuvee et employeur cree. Mail non envoye: verifiez l email ou la configuration Gmail.'
            );
    }

    public function reject(RejectAffiliationRequest $request, AffiliationRequest $affiliationRequest): RedirectResponse
    {
        $this->ensureSdtOpinionIsAnswered($affiliationRequest, 'rejection_reason');

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

        $affiliationRequest->refresh();
        $mailSent = $this->mailDispatchService->sendAffiliationDecision($affiliationRequest);

        return redirect()
            ->route('affiliations.show', $affiliationRequest)
            ->with('status', $mailSent
                ? 'Demande rejetee et mail envoye.'
                : 'Demande rejetee. Mail non envoye: verifiez l email ou la configuration Gmail.'
            );
    }

    public function requestSdtOpinion(AffiliationRequest $affiliationRequest): RedirectResponse
    {
        if ($affiliationRequest->status !== 'PENDING') {
            throw ValidationException::withMessages([
                'sdt_opinion' => 'Cette demande a deja une decision finale.',
            ]);
        }

        if ($affiliationRequest->sdt_opinion_status && ! $affiliationRequest->sdt_opinion_given_at) {
            return redirect()
                ->route('affiliations.show', $affiliationRequest)
                ->with('status', 'Une demande d avis SDT est deja en attente.');
        }

        $affiliationRequest->update([
            'sdt_opinion_status' => 'REQUESTED',
            'sdt_opinion_requested_at' => now(),
            'sdt_opinion_requested_by_user_id' => auth()->id(),
            'sdt_opinion_note' => null,
            'sdt_opinion_given_at' => null,
            'sdt_opinion_given_by_user_id' => null,
        ]);

        return redirect()
            ->route('affiliations.show', $affiliationRequest)
            ->with('status', 'Avis SDT demande. La decision finale est suspendue en attendant la reponse.');
    }

    public function sdtIndex(): View
    {
        $affiliationRequests = AffiliationRequest::query()
            ->with(['sdtOpinionRequestedBy:id,full_name', 'sdtOpinionGivenBy:id,full_name'])
            ->whereNotNull('sdt_opinion_requested_at')
            ->latest('sdt_opinion_requested_at')
            ->paginate(15);

        return view('affiliations.sdt-index', [
            'affiliationRequests' => $affiliationRequests,
        ]);
    }

    public function sdtShow(AffiliationRequest $affiliationRequest): View
    {
        if (! $affiliationRequest->sdt_opinion_requested_at) {
            abort(403);
        }

        $affiliationRequest->load([
            'employer',
            'processedBy',
            'sdtOpinionRequestedBy:id,full_name',
            'sdtOpinionGivenBy:id,full_name',
        ]);

        return view('affiliations.show', [
            'affiliationRequest' => $affiliationRequest,
            'sdtMode' => true,
        ]);
    }

    public function submitSdtOpinion(Request $request, AffiliationRequest $affiliationRequest): RedirectResponse
    {
        if (! $affiliationRequest->sdt_opinion_requested_at) {
            abort(403);
        }

        if ($affiliationRequest->status !== 'PENDING') {
            throw ValidationException::withMessages([
                'sdt_opinion_status' => 'Cette demande a deja une decision finale.',
            ]);
        }

        if ($affiliationRequest->sdt_opinion_given_at) {
            throw ValidationException::withMessages([
                'sdt_opinion_status' => 'L avis SDT a deja ete transmis.',
            ]);
        }

        $data = $request->validate([
            'sdt_opinion_status' => ['required', Rule::in(['FAVORABLE', 'UNFAVORABLE'])],
            'sdt_opinion_note' => ['required', 'string', 'min:10', 'max:5000'],
        ]);

        $affiliationRequest->update([
            'sdt_opinion_status' => $data['sdt_opinion_status'],
            'sdt_opinion_note' => $data['sdt_opinion_note'],
            'sdt_opinion_given_at' => now(),
            'sdt_opinion_given_by_user_id' => auth()->id(),
        ]);

        return redirect()
            ->route('sdt.affiliations.show', $affiliationRequest)
            ->with('status', 'Avis SDT transmis a l Agent SES.');
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

    private function ensureSdtOpinionIsAnswered(AffiliationRequest $affiliationRequest, string $field): void
    {
        if ($affiliationRequest->sdt_opinion_requested_at && ! $affiliationRequest->sdt_opinion_given_at) {
            throw ValidationException::withMessages([
                $field => 'Impossible de donner un verdict avant la reponse du SDT.',
            ]);
        }
    }
}
