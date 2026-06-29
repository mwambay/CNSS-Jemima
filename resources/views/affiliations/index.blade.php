@extends('layouts.app')

@section('title', 'Affiliations | CNSS')
@section('page_title', 'Demandes d affiliation')
@section('page_subtitle', 'Traitement des demandes publiques employeurs')

@push('styles')
<style>
    .panel { background: #fff; border: 1px solid #e7eef2; border-radius: 16px; padding: 1rem; box-shadow: 0 1px 3px rgba(6, 52, 109, .1); }
    .table-wrap { overflow: auto; border: 1px solid #e4e7ec; border-radius: 12px; }
    table { width: 100%; border-collapse: collapse; min-width: 820px; }
    th, td { text-align: left; padding: .72rem .75rem; border-bottom: 1px solid #e4e7ec; font-size: .88rem; color: #344054; }
    th { font-size: .77rem; text-transform: uppercase; color: #667085; background: #f9fafb; }
    .badge { display: inline-flex; border-radius: 999px; padding: .2rem .58rem; font-size: .74rem; font-weight: 700; }
    .badge-pending { color: #b54708; background: #fffaeb; }
    .badge-approved { color: #027a48; background: #ecfdf3; }
    .badge-rejected { color: #b42318; background: #fef3f2; }
    .badge-requested { color: #b54708; background: #fffaeb; }
    .badge-favorable { color: #027a48; background: #ecfdf3; }
    .badge-unfavorable { color: #b42318; background: #fef3f2; }
    .btn { display: inline-flex; text-decoration: none; border-radius: 10px; padding: .42rem .64rem; font-size: .78rem; font-weight: 700; color: #344054; background: #fff; border: 1px solid #d0d5dd; }
    .empty { text-align: center; padding: 1.2rem; color: #667085; }
</style>
@endpush

@section('content')
<article class="panel">
    <div class="table-wrap">
        <table>
            <thead>
            <tr>
                <th>Suivi</th>
                <th>Employeur</th>
                <th>Telephone</th>
                <th>Activite</th>
                <th>Statut</th>
                <th>Avis SDT</th>
                <th>Date</th>
                <th>Action</th>
            </tr>
            </thead>
            <tbody>
            @forelse($affiliationRequests as $request)
                @php
                    $status = $request->status ?? 'PENDING';
                    $opinion = $request->sdt_opinion_status;
                    $opinionLabel = match ($opinion) {
                        'REQUESTED' => 'En attente',
                        'FAVORABLE' => 'Favorable',
                        'UNFAVORABLE' => 'Defavorable',
                        default => '-',
                    };
                @endphp
                <tr>
                    <td>{{ $request->tracking_number }}</td>
                    <td>{{ $request->legal_name ?: ($request->physical_employer_name ?: '-') }}</td>
                    <td>{{ $request->phone ?? '-' }}</td>
                    <td>{{ $request->primary_activity ?? '-' }}</td>
                    <td><span class="badge badge-{{ strtolower($status) }}">{{ $status }}</span></td>
                    <td>
                        @if($opinion)
                            <span class="badge badge-{{ strtolower($opinion) }}">{{ $opinionLabel }}</span>
                        @else
                            -
                        @endif
                    </td>
                    <td>{{ $request->created_at?->format('Y-m-d') }}</td>
                    <td><a class="btn" href="{{ route('affiliations.show', $request) }}">Ouvrir</a></td>
                </tr>
            @empty
                <tr><td colspan="8" class="empty">Aucune demande d'affiliation.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    {{ $affiliationRequests->links() }}
</article>
@endsection
