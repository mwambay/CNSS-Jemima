@php
    $employer = $declaration->employer;
    $period = str_pad((string) $declaration->period_month, 2, '0', STR_PAD_LEFT).'/'.$declaration->period_year;
    $amount = $declaration->global_total_payable ?? $declaration->global_amount_due ?? $declaration->total_declared_contribution;
@endphp

<p>Bonjour {{ $employer?->legal_name ?: 'Madame, Monsieur' }},</p>

@if($reminderType === 'D_DAY')
    <p>
        Nous vous rappelons que le versement de votre cotisation CNSS pour la periode {{ $period }}
        est attendu aujourd'hui.
    </p>
@else
    <p>
        Nous vous rappelons que le versement de votre cotisation CNSS pour la periode {{ $period }}
        arrive a echeance dans 5 jours.
    </p>
@endif

<p>
    <strong>Periode :</strong> {{ $period }}<br>
    <strong>Date d'echeance :</strong> {{ $declaration->due_date?->format('d/m/Y') ?? '-' }}<br>
    <strong>Montant exigible :</strong> {{ number_format((float) $amount, 2, ',', ' ') }} CDF
</p>

<p>
    Si le versement a deja ete effectue, veuillez ne pas tenir compte de ce rappel.
</p>

<p>Cordialement,<br>CNSS</p>
