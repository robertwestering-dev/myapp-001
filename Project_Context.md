# Project Context

## Doel Van Dit Bestand

Dit bestand beschrijft de actuele status van de applicatie en dient als vast referentiepunt voor toekomstig werk. Het is bedoeld als startdocument voor nieuwe modules, uitbreidingen, refactors en samenwerking met ontwikkelaars of AI-agents.

Gebruik dit document om snel te begrijpen:
- wat de applicatie nu doet
- welke technische keuzes al zijn gemaakt
- welke UI- en flowafspraken al bestaan
- waar rekening mee gehouden moet worden bij vervolgontwikkeling

## Korte Baseline Samenvatting

De applicatie staat nu op een stevige Laravel 13-basis met werkende authenticatie, rollen en een eerste admin-omgeving. Niet-ingelogde bezoekers landen op een publieke homepage in Hermes-stijl; gewone gebruikers gaan na login naar hun dashboard en admins naar de admin-portal.

Wat nu werkt:
- login, registratie, logout, forgot-password, reset-password en e-mailverificatie werken end-to-end
- nieuwe accounts krijgen standaard de rol `User`; admins hebben een aparte beveiligde portal
- de admin-module voor gebruikersbeheer is aanwezig:
  - gebruikerslijst
  - zoeken
  - paginatie
  - CSV-export
  - gebruiker toevoegen
  - gebruiker wijzigen
  - gebruiker verwijderen met extra bevestigingsstap
- mail wordt via echte SMTP verstuurd met Hermes Results-branding

UI en structuur:
- header, footer en branding zijn inmiddels grotendeels gestandaardiseerd via herbruikbare Blade-componenten
- het Hermes-logo wordt overal consistent gebruikt
- de header is op alle custom pagina's sticky
- de admin-portal en admin users-pagina's zijn visueel gelijkgetrokken met de homepage en gebruiken nu een meer centrale layout-structuur

Huidige focus voor vervolg:
- dashboard verder uitbouwen naar een echte ingelogde app-omgeving
- admin-portal verder modulair uitbreiden
- overige losse styling verder centraliseren waar nog nodig
- later gericht Livewire/Flux inzetten voor rijkere beheerinteracties

Kort samengevat:
We hebben nu geen los prototype meer, maar een werkende basisapp met consistente branding, stabiele auth-flow, rolgebaseerde routing en een eerste beheermodule waarop we veilig kunnen doorbouwen.

## Huidige Applicatiestatus

De applicatie is een Laravel 13-project met MySQL als database en Fortify voor authenticatie. Pest wordt gebruikt voor feature tests en Pint voor formatting. Livewire en Flux zijn aanwezig in de stack, maar de belangrijkste publieke, auth- en admin-pagina's zijn momenteel grotendeels als zelfstandige Blade-views opgebouwd met handmatige styling.

De huidige versie van de applicatie biedt nu een stevigere basis dan voorheen:
- een publieke homepage voor niet-ingelogde bezoekers
- een werkende login-, registratie-, logout- en forgot-password flow
- een werkende reset-password flow
- een werkende e-mailverificatieflow
- een dashboard voor gewone ingelogde gebruikers
- een admin-portal voor gebruikers met de rol `Admin`
- een eerste admin-module voor gebruikersbeheer

## Huidige Gebruikersflow

### Voor Niet-Ingelogde Bezoekers

Niet-ingelogde bezoekers zien op `/` een publieke homepage in marketing/landingpage-stijl. Deze pagina is visueel geinspireerd op de stijl van `hermesresults.com`.

De homepage bevat:
- branding in Hermes-stijl
- een marketinggerichte hero-sectie
- ondersteunende contentblokken
- een login-knop

### Voor Gewone Ingelogde Gebruikers

Als een gebruiker met de rol `User` is ingelogd en `/` bezoekt, wordt deze direct doorgestuurd naar `/dashboard`.

De dashboardpagina toont momenteel:
- een welkomsttekst met het e-mailadres van de ingelogde gebruiker
- een logout-knop
- dezelfde visuele merkstijl als de publieke pagina's

### Voor Ingelogde Admins

Als een gebruiker met de rol `Admin` is ingelogd en `/` bezoekt, wordt deze direct doorgestuurd naar `/admin-portal`.

De admin-portal:
- is uitsluitend toegankelijk voor ingelogde users met de rol `Admin`
- gebruikt dezelfde Hermes-stijl als de rest van de applicatie
- bevat een menu aan de linkerkant
- bevat nu een gebruikersmodule voor beheer

## Rollen En Autorisatie

De `users`-tabel is uitgebreid met een rolveld:
- standaardrol voor nieuwe accounts: `User`
- aanvullende rol: `Admin`

Belangrijke afspraken:
- nieuwe accounts krijgen altijd automatisch de rol `User`
- admins worden na login automatisch doorgestuurd naar de admin-portal
- gewone users blijven in de standaard dashboardflow
- admin-routes zijn beschermd met `auth` en aanvullende admin-controle

Belangrijke implementatie:
- [app/Models/User.php](/Users/robert/Desktop/MyApp-001/app/Models/User.php)
- [app/Http/Middleware/EnsureUserIsAdmin.php](/Users/robert/Desktop/MyApp-001/app/Http/Middleware/EnsureUserIsAdmin.php)
- [app/Actions/Fortify/LoginResponse.php](/Users/robert/Desktop/MyApp-001/app/Actions/Fortify/LoginResponse.php)

## Routes En Structuur

De belangrijkste routes zijn momenteel:

- `/`
  Publieke homepage voor gasten. Ingelogde users worden rolafhankelijk doorgestuurd naar `/dashboard` of `/admin-portal`.

- `/dashboard`
  Alleen toegankelijk voor ingelogde gebruikers in de gewone userflow.

- `/admin-portal`
  Alleen toegankelijk voor ingelogde admins.

- `/admin-portal/users`
  Gebruikersoverzicht voor admins.

- `/admin-portal/users/create`
  Nieuwe gebruiker toevoegen vanuit admin.

- `/admin-portal/users/{user}/edit`
  Bestaande gebruiker wijzigen vanuit admin.

- Fortify-auth-routes
  Onder meer:
  - `/login`
  - `/register`
  - `/forgot-password`
  - reset-password routes
  - logout
  - verify-email notice route

- publieke signed verificatieroute
  Een eigen publieke route verwerkt de link uit de verificatiemail zonder dat de gebruiker eerst ingelogd hoeft te zijn.

De hoofdroute-definitie staat in:
- [routes/web.php](/Users/robert/Desktop/MyApp-001/routes/web.php)

## Authenticatie

Authenticatie is gebaseerd op Laravel Fortify.

Wat nu werkt:
- gebruikers kunnen een account aanmaken
- registratie vraagt nu om naam, e-mailadres en wachtwoord
- gebruikers kunnen inloggen
- gebruikers kunnen uitloggen
- gebruikers kunnen een reset-link aanvragen voor hun wachtwoord
- gebruikers kunnen hun wachtwoord resetten
- gebruikers ontvangen een verificatiemail bij registratie
- gebruikers kunnen hun e-mailadres verifiëren via de link uit de mail

Validatieregels:
- naam is verplicht bij registratie
- gebruikersnaam is een e-mailadres
- het e-mailadres moet geldig zijn
- het e-mailadres moet uniek zijn
- het wachtwoord wordt veilig gehasht opgeslagen

Belangrijke implementatie:
- [app/Actions/Fortify/CreateNewUser.php](/Users/robert/Desktop/MyApp-001/app/Actions/Fortify/CreateNewUser.php)
- [app/Models/User.php](/Users/robert/Desktop/MyApp-001/app/Models/User.php)
- [app/Http/Controllers/Auth/EmailVerificationController.php](/Users/robert/Desktop/MyApp-001/app/Http/Controllers/Auth/EmailVerificationController.php)
- [app/Providers/AppServiceProvider.php](/Users/robert/Desktop/MyApp-001/app/Providers/AppServiceProvider.php)

## E-Mail En Mailverzending

Mail wordt niet langer alleen gelogd, maar via echte SMTP verstuurd.

Actuele mailstatus:
- mailer: `smtp`
- host: `smtp.hostnet.nl`
- poort: `587`
- transport voor STARTTLS via standaard SMTP-configuratie
- afzenderadres: `support@hermesresults.com`
- afzendernaam: `Hermes Results`

Wat nu via echte mail werkt:
- forgot-password mail
- e-mailverificatie na registratie

Branding van verstuurde mails:
- afzendernaam is `Hermes Results`
- de standaard Laravel-afbeelding bovenaan wordt niet meer getoond
- de footer toont `Hermes Results` in plaats van `Laravel`

Belangrijke bestanden:
- [.env](/Users/robert/Desktop/MyApp-001/.env)
- [resources/views/vendor/mail/html/header.blade.php](/Users/robert/Desktop/MyApp-001/resources/views/vendor/mail/html/header.blade.php)
- [resources/views/vendor/mail/html/message.blade.php](/Users/robert/Desktop/MyApp-001/resources/views/vendor/mail/html/message.blade.php)

## Wachtwoord Reset Status

De forgot-password functionaliteit werkt technisch en functioneel:
- de resetmail wordt echt verzonden
- de reset-link opent de juiste reset-password pagina
- het wachtwoord kan succesvol worden gewijzigd

De reset-password pagina is inmiddels ook visueel gelijkgetrokken met de andere auth-pagina's.

Belangrijke view:
- [resources/views/pages/auth/reset-password.blade.php](/Users/robert/Desktop/MyApp-001/resources/views/pages/auth/reset-password.blade.php)

## Database Status

De applicatie gebruikt MySQL.

Bevestigd:
- de databaseverbinding werkt
- migraties zijn uitgevoerd
- de `users`-tabel bestaat
- authenticatie gebruikt de database correct
- de `users`-tabel bevat nu ook een `role`-kolom

Belangrijke database-informatie:
- connectie: `mysql`
- database: `myapp001`

Belangrijke migraties:
- [database/migrations/2026_03_27_134457_add_role_to_users_table.php](/Users/robert/Desktop/MyApp-001/database/migrations/2026_03_27_134457_add_role_to_users_table.php)
- [database/migrations/2026_03_27_134638_promote_existing_user_to_admin.php](/Users/robert/Desktop/MyApp-001/database/migrations/2026_03_27_134638_promote_existing_user_to_admin.php)

## Admin-Module: Gebruikersbeheer

De admin-portal bevat nu een eerste beheermodule voor gebruikers.

Wat deze module nu kan:
- lijst van gebruikers tonen
- zoeken op naam of e-mailadres
- sorteren op kolom naam
- paginatie met 15 gebruikers per pagina
- CSV-export van de lijst
- nieuwe gebruiker toevoegen
- bestaande gebruiker wijzigen
- bestaande gebruiker verwijderen

De lijst toont:
- naam
- e-mailadres
- rol
- datum e-mailverificatie

Belangrijke implementatie:
- [app/Http/Controllers/Admin/UserController.php](/Users/robert/Desktop/MyApp-001/app/Http/Controllers/Admin/UserController.php)
- [app/Http/Requests/Admin/StoreUserRequest.php](/Users/robert/Desktop/MyApp-001/app/Http/Requests/Admin/StoreUserRequest.php)
- [app/Http/Requests/Admin/UpdateUserRequest.php](/Users/robert/Desktop/MyApp-001/app/Http/Requests/Admin/UpdateUserRequest.php)
- [resources/views/admin/users/index.blade.php](/Users/robert/Desktop/MyApp-001/resources/views/admin/users/index.blade.php)
- [resources/views/admin/users/form.blade.php](/Users/robert/Desktop/MyApp-001/resources/views/admin/users/form.blade.php)

## UI En Branding

De huidige UI van homepage, login, register, forgot-password, reset-password, dashboard, admin-portal en admin-gebruikerspagina's is handmatig in Blade opgebouwd en gebruikt inline CSS.

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
- [resources/views/pages/auth/reset-password.blade.php](/Users/robert/Desktop/MyApp-001/resources/views/pages/auth/reset-password.blade.php)
- [resources/views/admin-portal.blade.php](/Users/robert/Desktop/MyApp-001/resources/views/admin-portal.blade.php)
- [resources/views/admin/users/index.blade.php](/Users/robert/Desktop/MyApp-001/resources/views/admin/users/index.blade.php)

## Tests En Stabiliteit

De belangrijkste flows zijn getest en functioneel bevestigd:
- homepage rendering
- login
- logout
- registratie
- verplichte naam bij registratie
- dashboardtoegang
- admin-portal toegang
- admin gebruikersbeheer
- forgot-password flow
- reset-password flow
- e-mailverificatieflow
- branding van reset- en verificatiemails
- verificatielink vanuit mail zonder voorafgaande login

De projectaanpak tot nu toe volgt:
- wijzigingen testen met gerichte Pest feature tests
- PHP-bestanden formatteren met Pint

Belangrijke tests:
- [tests/Feature/Auth/RegistrationTest.php](/Users/robert/Desktop/MyApp-001/tests/Feature/Auth/RegistrationTest.php)
- [tests/Feature/Auth/AuthenticationTest.php](/Users/robert/Desktop/MyApp-001/tests/Feature/Auth/AuthenticationTest.php)
- [tests/Feature/Auth/PasswordResetTest.php](/Users/robert/Desktop/MyApp-001/tests/Feature/Auth/PasswordResetTest.php)
- [tests/Feature/Auth/EmailVerificationTest.php](/Users/robert/Desktop/MyApp-001/tests/Feature/Auth/EmailVerificationTest.php)
- [tests/Feature/AdminPortalAccessTest.php](/Users/robert/Desktop/MyApp-001/tests/Feature/AdminPortalAccessTest.php)
- [tests/Feature/AdminUserManagementTest.php](/Users/robert/Desktop/MyApp-001/tests/Feature/AdminUserManagementTest.php)

## Belangrijke Technische Afspraken Voor Nieuwe Modules

Nieuwe modules moeten aansluiten op de bestaande basis.

### Functionele Richtlijnen

- Bepaal altijd eerst of een nieuwe module publiek, user-only of admin-only is.
- Publieke pagina's moeten passen bij de bestaande homepage-stijl.
- Gewone ingelogde flows moeten logisch aansluiten op `/dashboard`.
- Adminflows moeten logisch aansluiten op `/admin-portal`.
- Auth-functionaliteit moet blijven aansluiten op Fortify.

### UI Richtlijnen

- Houd de bestaande visuele stijl vast tenzij er bewust gekozen wordt voor een redesign.
- Gebruik dezelfde branding, kleurwereld en typografische richting.
- Wees voorzichtig met het toevoegen van een tweede, afwijkende designrichting.

### Architectuur Richtlijnen

- Volg bestaande Laravel-conventies.
- Houd routes overzichtelijk.
- Voeg tests toe voor nieuwe functionaliteit.
- Gebruik bestaande auth-, mail- en userstructuren waar mogelijk opnieuw.
- Gebruik rolgebaseerde toegang als een module admin-only is.

## Bekende Verbeterkansen

De applicatie werkt, maar er zijn nog duidelijke verbeterpunten voor de toekomst:

- inline CSS centraliseren in herbruikbare layout/componentstructuren
- branding-element herbruikbaar maken als Blade-component
- auth-pagina's delen nog steeds veel visuele code en kunnen verder worden samengebracht
- dashboard uitbouwen naar een echte ingelogde app-omgeving
- admin-portal verder modulair opbouwen
- eventueel later Livewire/Flux doelgericht inzetten voor rijkere beheerinteracties

## Aanbevolen Gebruik In De Toekomst

Gebruik dit document bij elk nieuw verzoek als eerste contextbron.

Beste werkwijze:
- lees dit bestand voordat je een nieuwe module ontwerpt
- bepaal of de uitbreiding publiek, user-only of admin-only is
- controleer of de nieuwe functionaliteit de bestaande flow verandert
- gebruik dit bestand als baseline voordat je nieuwe routes, modellen, tabellen of pagina's toevoegt

## Aanbevolen Volgende Stap

Als de applicatie verder groeit, zijn logische vervolgstappen:

1. een herbruikbare layout- of componentlaag maken voor branding en pagina-opbouw
2. het onderscheid tussen publieke marketingpagina's, user-pagina's en admin-pagina's verder structureren
3. nieuwe modules bouwen vanuit de bestaande dashboard- of admin-shell in plaats van losse pagina's toe te voegen

## Laatst Bekende Baseline

Deze context weerspiegelt de status van de applicatie na:
- het opzetten van auth met e-mail en wachtwoord
- het toevoegen van verplichte naam bij registratie
- het bouwen van een publieke homepage
- het opzetten van dashboard + logout
- het restylen van login, register, forgot-password en reset-password
- het uniform maken van branding en typografie
- het toevoegen van rolgebaseerde toegang met `User` en `Admin`
- het bouwen van een admin-portal
- het bouwen van een admin gebruikersmodule
- het configureren van echte SMTP-mailverzending
- het oplossen van verificatielinks vanuit e-mail zonder 403

## Module Checklist

Gebruik deze checklist als vaste leidraad bij het ontwerpen en bouwen van nieuwe modules.

### 1. Doel En Toegang

- Wat is het doel van de module?
- Is de module publiek toegankelijk, alleen voor gewone ingelogde users, of alleen voor admins?
- Moet de module zichtbaar zijn vanaf de homepage, dashboard, admin-portal of meerdere plekken?

### 2. Gebruikersflow

- Hoe komt de gebruiker in deze module terecht?
- Waar gaat de gebruiker heen na een succesvolle actie?
- Past de flow logisch binnen de bestaande homepage -> login -> dashboard/admin-portal structuur?

### 3. Routes

- Welke routes zijn nodig?
- Moeten routes beschermd zijn met `auth`, admin-middleware of signed/throttle middleware?
- Past de naamgeving binnen de bestaande Laravel-conventies?

### 4. Data En Database

- Is een nieuwe tabel nodig?
- Is een nieuw model nodig?
- Zijn extra validatieregels nodig?
- Zijn er unieke velden, relaties of indexen nodig?

### 5. UI En Stijl

- Past de module visueel binnen de bestaande Hermes-geinspireerde stijl?
- Moet branding bovenaan zichtbaar zijn?
- Is de pagina publiek-marketingachtig, user-functioneel of admin-functioneel?

### 6. Hergebruik

- Kan bestaande auth-, admin-, branding- of paginacode worden hergebruikt?
- Moet een nieuw stuk UI eigenlijk een component worden?
- Voorkomt deze aanpak duplicatie?

### 7. Beveiliging

- Is validatie aanwezig?
- Is toegang goed afgeschermd?
- Worden gevoelige gegevens veilig verwerkt en opgeslagen?

### 8. E-mail Of Notificaties

- Verstuurt de module e-mails of meldingen?
- Moeten die echt verzonden worden of alleen lokaal gecontroleerd?
- Past de mailbranding bij `Hermes Results`?

### 9. Testen

- Welke feature tests zijn nodig?
- Welke bestaande tests moeten worden bijgewerkt?
- Is het minimale functionele pad volledig getest?

### 10. Afronding

- Zijn routes, views, validatie, mails en databasewijzigingen op elkaar afgestemd?
- Is de code geformatteerd?
- Is de module in lijn met dit contextdocument gebouwd?
