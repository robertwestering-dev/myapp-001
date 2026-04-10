# Questionnaire Import/Export Handleiding

## Doel

Met deze workflow kun je questionnaires lokaal op je MacBook bouwen, ze later laten vertalen naar andere talen, en daarna als JSON exporteren om in de live-omgeving te importeren.

De export bevat:

- questionnaire titel
- beschrijving
- taal
- actief/inactief status
- categorieen
- vragen
- antwoordopties
- sortering
- conditionele vraaglogica

De export bevat niet:

- organisatiekoppelingen
- ingevulde responses
- rapportages

## Aanbevolen workflow

1. Bouw of wijzig questionnaires lokaal in de adminomgeving.
2. Laat de vragenlijst daarna vertalen naar `en` en `de`.
3. Exporteer de gewenste questionnaires naar JSON.
4. Zet het JSON-bestand over naar de live-omgeving.
5. Importeer het bestand op live met het importcommand.
6. Koppel de geimporteerde questionnaires daarna in live aan de juiste organisaties.

## Exporteren

Exporteer alle questionnaires:

```bash
php artisan questionnaires:export --path=storage/app/questionnaires/library-export.json
```

Exporteer alleen specifieke questionnaires op ID:

```bash
php artisan questionnaires:export --questionnaire=12 --questionnaire=15 --path=storage/app/questionnaires/selected-export.json
```

## Importeren

Importeer een exportbestand:

```bash
php artisan questionnaires:import storage/app/questionnaires/library-export.json
```

Je mag hiervoor ook een absoluut pad gebruiken.

## Hoe import werkt

- questionnaires worden bijgewerkt op combinatie van `title + locale`
- categorieen worden gesynchroniseerd op `sort_order` binnen die questionnaire
- vragen worden gesynchroniseerd op `sort_order` binnen die categorie
- conditionele logica wordt opnieuw gekoppeld op basis van categorie-volgorde en vraag-volgorde
- categorieen en vragen die niet meer in het importbestand staan, worden verwijderd binnen de geimporteerde questionnaire
- questionnaires die niet in het importbestand staan, blijven ongemoeid

## Belangrijke werkwijze

- Houd per taal de titel stabiel zolang je dezelfde questionnaire wilt blijven updaten.
- Gebruik sortering bewust; de import ziet `sort_order` als vaste structuur.
- Test lokaal altijd eerst of de questionnaire goed rendert en conditionele vragen correct werken.
- Importeer daarna pas op live.

## Slim gebruik met vertalingen

De handigste praktijk is:

1. Eerst de Nederlandse versie lokaal bouwen.
2. Daarna op basis daarvan een Engelse en Duitse versie aanmaken.
3. Daarna alle taalvarianten samen exporteren.

Zo houd je de inhoud per taal synchroon.

## Live checklist

Voor live import:

1. deploy de laatste code en migraties
2. upload het JSON-bestand naar de server
3. voer het importcommand uit
4. controleer in admin of de questionnaires zichtbaar zijn
5. koppel de juiste questionnaires aan organisaties
6. test met een gebruiker per taal

## Toekomstige uitbreiding

Als je later nog strakker wilt werken, kunnen we ook een vaste `external_key` per questionnaire toevoegen. Dan worden imports nog stabieler bij titelwijzigingen.
