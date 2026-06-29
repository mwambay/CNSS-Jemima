@extends('layouts.app')

@section('title', 'Affiliations SDT | CNSS')
@section('page_title', 'Affiliations a examiner')
@section('page_subtitle', 'Avis consultatif SDT sur demande de l Agent SES')

@push('styles')
<style>
    .panel { background: #fff; border: 1px solid #e7eef2; border-radius: 16px; padding: 1rem; box-shadow: 0 1px 3px rgba(6, 52, 109, .1); }
    .intro { display: flex; justify-content: space-between; gap: 1rem; align-items: center; margin-bottom: 1rem; flex-wrap: wrap; }
    .intro h2 { margin: 0; color: #06346d; font-size: 1.05rem; }
    .intro p { margin: .2rem 0 0; color: #667085; font-size: .88rem; }
    .table-wrap { overflow: auto; border: 1px solid #e4e7ec; border-radius: 12px; }
    table { width: 100%; border-collapse: collapse; min-width: 880px; }
    th, td { text-align: left; padding: .72rem .75rem; border-bottom: 1px solid #e4e7ec; font-size: .88rem; color: #344054; vertical-align: top; }
    th { font-size: .77rem; text-transform: uppercase; color: #667085; background: #f9fafb; }
    .badge { display: inline-flex; border-radius: 999px; padding: .2rem .58rem; font-size: .74rem; font-weight: 800; }
    .badge-requested { color: #b54708; background: #fffaeb; }
    .badge-favorable { color: #027a48; background: #ecfdf3; }
    .badge-unfavorable { color: #b42318; background: #fef3f2; }
    .btn { display: inline-flex; text-decoration: none; border-radius: 10px; padding: .42rem .64rem; font-size: .78rem; font-weight: 700; color: #344054; background: #fff; border: 1px solid #d0d5dd; }
    .btn-hot { color: #fff; background: #06346d; border-color: #06346d; }
    .empty { text-align: center; padding: 1.2rem; color: #667085; }
</style>
@endpush

@section('content')
<article class="panel">
    <div class="intro">
        <div>
            <h2>Demandes transmises au SDT</h2>
            <p>Le SDT donne un avis, mais la decision finale reste chez l Agent SES.</p>
        </div>
    </div>

    <div class="table-wrap">
        <table>
            <thead>
            <tr>
                <th>Suivi</th>
                <th>Employeur</th>
                <th>Activite</th>
                <th>Demande par</th>
                <th>Avis</th>
                <th>Date demande</th>
                <th>Action</th>
            </tr>
            </thead>
            <tbody>
            @forelse($affiliationRequests as $request)
                @php
                    $opinion = $request->sdt_opinion_status ?? 'REQUESTED';
                    $opinionLabel = match ($opinion) {
                        'FAVORABLE' => 'Favorable',
                        'UNFAVORABLE' => 'Defavorable',
                        default => 'A traiter',
                    };
                @endphp
                <tr>
                    <td>{{ $request->tracking_number }}</td>
                    <td>{{ $request->legal_name ?: ($request->physical_employer_name ?: '-') }}</td>
                    <td>{{ $request->primary_activity ?? '-' }}</td>
                    <td>{{ $request->sdtOpinionRequestedBy?->full_name ?? '-' }}</td>
                    <td><span class="badge badge-{{ strtolower($opinion) }}">{{ $opinionLabel }}</span></td>
                    <td>{{ $request->sdt_opinion_requested_at?->format('Y-m-d H:i') }}</td>
                    <td>
                        <a class="btn {{ $request->sdt_opinion_given_at ? '' : 'btn-hot' }}" href="{{ route('sdt.affiliations.show', $request) }}">
                            {{ $request->sdt_opinion_given_at ? 'Consulter' : 'Donner avis' }}
                        </a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="empty">Aucune demande d'avis SDT.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    {{ $affiliationRequests->links() }}
</article>
@endsection
