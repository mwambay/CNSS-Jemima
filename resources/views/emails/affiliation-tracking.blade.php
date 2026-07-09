@php
    $name = $affiliationRequest->legal_name ?: $affiliationRequest->physical_employer_name;
@endphp

<p>Bonjour {{ $name ?: 'Madame, Monsieur' }},</p>

<p>
    Votre demande d'affiliation à la CNSS a bien été reçue.
</p>

<p>
    <strong>Numéro de suivi :</strong> {{ $affiliationRequest->tracking_number }}
</p>

<p>
    Veuillez conserver ce numéro. Il permet d'identifier votre demande pendant son traitement par la CNSS.
</p>

<p>Cordialement,<br>CNSS</p>
