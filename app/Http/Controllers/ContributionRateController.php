<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpsertContributionRateRequest;
use App\Models\ContributionRate;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ContributionRateController extends Controller
{
    public function index(): View
    {
        return view('contribution-rates.index', [
            'rates' => ContributionRate::query()
                ->orderByDesc('effective_from')
                ->orderByDesc('id')
                ->get(),
        ]);
    }

    public function store(UpsertContributionRateRequest $request): RedirectResponse
    {
        ContributionRate::query()->create($request->validated());

        return redirect()
            ->route('contribution-rates.index')
            ->with('status', 'Modalite de cotisation creee.');
    }

    public function update(UpsertContributionRateRequest $request, ContributionRate $contributionRate): RedirectResponse
    {
        $contributionRate->update($request->validated());

        return redirect()
            ->route('contribution-rates.index')
            ->with('status', 'Modalite de cotisation mise a jour.');
    }
}
