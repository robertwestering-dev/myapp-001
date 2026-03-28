<x-mail::message>
# Nieuw contactformulierbericht

Er is een nieuw bericht verzonden via de website van Hermes Results.

<x-mail::panel>
Naam: {{ $name }}

E-mailadres: {{ $email }}

Akkoord verwerking gegevens: {{ $consentGiven ? 'Ja' : 'Nee' }}
</x-mail::panel>

## Bericht

{{ $messageBody }}
</x-mail::message>
