@php
    $name = $affiliationRequest->legal_name ?: $affiliationRequest->physical_employer_name;
@endphp

<p>Bonjour {{ $name ?: 'Madame, Monsieur' }},</p>

@if($decision === 'APPROVED')
    <p>
        Votre demande d'affiliation a la CNSS a ete approuvee.
        Votre entreprise est maintenant enregistree dans le systeme CNSS.
    </p>

    <p>
        <strong>Numero de suivi :</strong> {{ $affiliationRequest->tracking_number }}<br>
        <strong>Numero d'affiliation CNSS :</strong> {{ $affiliationRequest->employer?->affiliation_number ?? '-' }}
    </p>

    <p>
        Veuillez conserver ce numero d'affiliation. Il sera utilise pour vos declarations et vos cotisations.
    </p>
@else
    <p>
        Apres examen, votre demande d'affiliation a la CNSS n'a pas ete approuvee.
    </p>

    <p>
        <strong>Numero de suivi :</strong> {{ $affiliationRequest->tracking_number }}<br>
        <strong>Motif :</strong> {{ $affiliationRequest->rejection_reason }}
    </p>

    <p>
        Vous pouvez corriger les informations indiquees et introduire une nouvelle demande si necessaire.
    </p>
@endif

<p>Cordialement,<br>CNSS</p>
