# Project Context

## Doel Van Dit Bestand

Dit bestand beschrijft de actuele status van de applicatie en dient als vast referentiepunt voor toekomstig werk. Het is bedoeld als startdocument voor nieuwe modules, uitbreidingen, refactors en samenwerking met ontwikkelaars of AI-agents.

Gebruik dit document om snel te begrijpen:
- wat de applicatie nu doet
- welke technische keuzes al zijn gemaakt
- welke UI- en flowafspraken al bestaan
- waar rekening mee gehouden moet worden bij vervolgontwikkeling

## Huidige Applicatiestatus

De applicatie is een Laravel 13-project met MySQL als database en Fortify voor authenticatie. Livewire en Flux zijn aanwezig in de stack, maar de huidige publieke en auth-pagina's zijn grotendeels als zelfstandige Blade-views opgebouwd met inline styling.

De huidige versie van de applicatie richt zich op een eenvoudige, stabiele basis:
- een publieke homepage voor niet-ingelogde bezoekers
- een werkende login- en registratieflow
- een dashboard voor ingelogde gebruikers
- een werkende forgot-password flow

## Huidige Gebruikersflow

### Voor Niet-Ingelogde Bezoekers

Niet-ingelogde bezoekers zien op `/` een publieke homepage in een marketing/landingpage-stijl. Deze pagina is visueel geinspireerd op de stijl van `hermesresults.com`.

De homepage bevat:
- branding in Hermes-stijl
- een marketinggerichte hero-sectie
- ondersteunende contentblokken
- een login-knop

### Voor Ingelogde Gebruikers

Als een gebruiker is ingelogd en `/` bezoekt, wordt hij direct doorgestuurd naar `/dashboard`.

De dashboardpagina toont momenteel:
- een welkomsttekst met het e-mailadres van de ingelogde gebruiker
- een logout-knop
- dezelfde visuele merkstijl als de publieke pagina's

## Routes En Structuur

De belangrijkste routes zijn momenteel:

- `/`  
  Publieke homepage voor gasten. Ingelogde gebruikers worden doorgestuurd naar `/dashboard`.

- `/dashboard`  
  Alleen toegankelijk voor ingelogde gebruikers.

- Fortify-auth-routes  
  Onder meer:
  - `/login`
  - `/register`
  - `/forgot-password`
  - reset-password routes
  - logout

De hoofdroute-definitie staat in:
- [routes/web.php](/Users/robert/Desktop/MyApp-001/routes/web.php)

## Authenticatie

Authenticatie is gebaseerd op Laravel Fortify.

Wat nu werkt:
- gebruikers kunnen een account aanmaken
- gebruikers kunnen inloggen
- gebruikers kunnen uitloggen
- gebruikers kunnen een reset-link aanvragen voor hun wachtwoord

Validatieregels:
- gebruikersnaam is een e-mailadres
- het e-mailadres moet geldig zijn
- het e-mailadres moet uniek zijn
- het wachtwoord wordt veilig gehasht opgeslagen

Belangrijke implementatie:
- [app/Actions/Fortify/CreateNewUser.php](/Users/robert/Desktop/MyApp-001/app/Actions/Fortify/CreateNewUser.php)
- [app/Models/User.php](/Users/robert/Desktop/MyApp-001/app/Models/User.php)

## Wachtwoord Reset Status

De forgot-password functionaliteit werkt technisch, maar verzendt op dit moment geen echte e-mails naar een mailbox.

De reden:
- in [.env](/Users/robert/Desktop/MyApp-001/.env) staat momenteel `MAIL_MAILER=log`

Dat betekent:
- Laravel genereert de resetmail correct
- de inhoud wordt opgeslagen in `storage/logs/laravel.log`
- de mail wordt niet echt via SMTP of een externe mailprovider verzonden

Als echte ontvangst nodig is, moet later een echte mailconfiguratie worden ingesteld, bijvoorbeeld via SMTP of een lokale tool zoals Mailpit.

## Database Status

De applicatie gebruikt MySQL.

Bevestigd:
- de databaseverbinding werkt
- migraties zijn uitgevoerd
- de `users`-tabel bestaat
- authenticatie gebruikt de database correct

Belangrijke database-informatie:
- connectie: `mysql`
- database: `myapp001`

## UI En Branding

De huidige UI van homepage, login, register, forgot-password en dashboard is handmatig in Blade opgebouwd en gebruikt inline CSS.

De visuele richting is:
- warm en premium kleurenpalet
- rustige zakelijke uitstraling
- glasachtige panelen
- Hermes-geinspireerde branding

De huidige merkweergave gebruikt:
- `Hermes Results`
- `Oplossingen die werken`

De oude groene `HR`-badge is vervangen door een gestileerde Hermes-achtige wordmark-lockup.

Belangrijke viewbestanden:
- [resources/views/home.blade.php](/Users/robert/Desktop/MyApp-001/resources/views/home.blade.php)
- [resources/views/dashboard.blade.php](/Users/robert/Desktop/MyApp-001/resources/views/dashboard.blade.php)
- [resources/views/pages/auth/login.blade.php](/Users/robert/Desktop/MyApp-001/resources/views/pages/auth/login.blade.php)
- [resources/views/pages/auth/register.blade.php](/Users/robert/Desktop/MyApp-001/resources/views/pages/auth/register.blade.php)
- [resources/views/pages/auth/forgot-password.blade.php](/Users/robert/Desktop/MyApp-001/resources/views/pages/auth/forgot-password.blade.php)

## Tests En Stabiliteit

De belangrijkste flows zijn getest en functioneel bevestigd:
- homepage rendering
- login
- logout
- registratie
- dashboardtoegang
- forgot-password flow
- reset-password flow

De projectaanpak tot nu toe volgt:
- wijzigingen testen met gerichte Pest feature tests
- PHP-bestanden formatteren met Pint

## Belangrijke Technische Afspraken Voor Nieuwe Modules

Nieuwe modules moeten aansluiten op de bestaande basis.

### Functionele Richtlijnen

- Bepaal altijd eerst of een nieuwe module publiek of alleen voor ingelogde gebruikers is.
- Publieke pagina's moeten passen bij de bestaande homepage-stijl.
- Ingelogde flows moeten logisch aansluiten op `/dashboard` of een toekomstige app-shell.
- Auth-functionaliteit moet blijven aansluiten op Fortify.

### UI Richtlijnen

- Houd de bestaande visuele stijl vast tenzij er bewust gekozen wordt voor een redesign.
- Gebruik dezelfde branding, kleurwereld en typografische richting.
- Wees voorzichtig met het toevoegen van een tweede, afwijkende designrichting.

### Architectuur Richtlijnen

- Volg bestaande Laravel-conventies.
- Houd routes overzichtelijk.
- Voeg tests toe voor nieuwe functionaliteit.
- Gebruik bestaande auth- en userstructuren waar mogelijk opnieuw.

## Bekende Verbeterkansen

De applicatie werkt, maar er zijn duidelijke verbeterpunten voor de toekomst:

- inline CSS centraliseren in herbruikbare layout/componentstructuren
- branding-element herbruikbaar maken als Blade-component
- auth-pagina's delen nu veel visuele code en kunnen worden samengebracht
- mailverzending configureren voor echte levering
- dashboard uitbouwen naar een echte ingelogde app-omgeving

## Aanbevolen Gebruik In De Toekomst

Gebruik dit document bij elk nieuw verzoek als eerste contextbron.

Beste werkwijze:
- lees dit bestand voordat je een nieuwe module ontwerpt
- bepaal of de uitbreiding publiek of auth-only is
- controleer of de nieuwe functionaliteit de bestaande flow verandert
- gebruik dit bestand als baseline voordat je nieuwe routes, modellen, tabellen of pagina's toevoegt

## Aanbevolen Volgende Stap

Als de applicatie verder groeit, is de beste vervolgstap:

1. een herbruikbare layout- of componentlaag maken voor branding en pagina-opbouw
2. een duidelijk onderscheid maken tussen publieke marketingpagina's en ingelogde app-pagina's
3. nieuwe modules bouwen vanuit deze vaste basis in plaats van losse pagina's toe te voegen

## Laatst Bekende Baseline

Deze context weerspiegelt de status van de applicatie na:
- het opzetten van auth met e-mail en wachtwoord
- het bouwen van een publieke homepage
- het opzetten van dashboard + logout
- het restylen van login, register en forgot-password
- het uniform maken van branding en typografie

## Module Checklist

Gebruik deze checklist als vaste leidraad bij het ontwerpen en bouwen van nieuwe modules.

### 1. Doel En Toegang

- Wat is het doel van de module?
- Is de module publiek toegankelijk of alleen voor ingelogde gebruikers?
- Moet de module zichtbaar zijn vanaf de homepage, dashboard of beide?

### 2. Gebruikersflow

- Hoe komt de gebruiker in deze module terecht?
- Waar gaat de gebruiker heen na een succesvolle actie?
- Past de flow logisch binnen de bestaande homepage -> login -> dashboard structuur?

### 3. Routes

- Welke routes zijn nodig?
- Moeten routes beschermd zijn met `auth` of andere middleware?
- Past de naamgeving binnen de bestaande Laravel-conventies?

### 4. Data En Database

- Is een nieuwe tabel nodig?
- Is een nieuw model nodig?
- Zijn extra validatieregels nodig?
- Zijn er unieke velden, relaties of indexen nodig?

### 5. UI En Stijl

- Past de module visueel binnen de bestaande Hermes-geinspireerde stijl?
- Moet branding bovenaan zichtbaar zijn?
- Is de pagina publiek-marketingachtig of meer app-functioneel?

### 6. Hergebruik

- Kan bestaande auth-, branding- of paginacode worden hergebruikt?
- Moet een nieuw stuk UI eigenlijk een component worden?
- Voorkomt deze aanpak duplicatie?

### 7. Beveiliging

- Is validatie aanwezig?
- Is toegang goed afgeschermd?
- Worden gevoelige gegevens veilig verwerkt en opgeslagen?

### 8. E-mail Of Notificaties

- Verstuurt de module e-mails of meldingen?
- Moet dat lokaal alleen gelogd worden of echt verzonden?
- Is extra mailconfiguratie nodig?

### 9. Testen

- Welke feature tests zijn nodig?
- Welke bestaande tests moeten worden bijgewerkt?
- Is het minimale functionele pad volledig getest?

### 10. Afronding

- Zijn routes, views, validatie en databasewijzigingen op elkaar afgestemd?
- Is de code geformatteerd?
- Is de module in lijn met dit contextdocument gebouwd?
