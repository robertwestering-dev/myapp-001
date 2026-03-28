# Project Context

## Doel Van Dit Bestand

Dit document beschrijft de actuele status van het project en dient als baseline voor toekomstig werk. Het is bedoeld als snel startpunt voor nieuwe features, refactors en verdere uitbouw van het questionnaire-platform.

Gebruik dit document om direct te begrijpen:
- welke functionele basis er nu staat
- hoe rollen en organisatie-scope werken
- welke admin-modules al bestaan
- welke technische en productafspraken al gelden

## Korte Baseline Samenvatting

De applicatie is nu een werkende Laravel 13-basisapp met:
- Fortify-authenticatie
- e-mailverificatie en password reset
- een dashboard voor gewone gebruikers
- een admin-portal voor beheerdersrollen
- organisatiebeheer
- gebruikersbeheer
- rol- en organisatiegebaseerde datascope

De belangrijkste huidige bedrijfslogica:
- elke user hoort bij exact één organisatie via `users.org_id`
- organisaties staan in `organizations`
- `Hermes Results` is de standaardorganisatie
- er zijn nu drie rollen: `User`, `Admin` en `Beheerder`
- `Admin` ziet alle organisaties en alle users
- `Beheerder` ziet in het admin-portal alleen data van de eigen organisatie

Kort samengevat:
we hebben nu niet alleen auth en branding staan, maar ook een eerste multitenant-achtige beheerstructuur op basis van `org_id`, waarop de questionnaire-modules veilig kunnen voortbouwen.

## Huidige Applicatiestatus

De applicatie draait op:
- Laravel 13
- PHP 8.5
- MySQL
- Laravel Fortify voor auth
- Pest voor tests
- Pint voor formatting

De UI bestaat op dit moment grotendeels uit Blade-views met handmatige styling in Hermes Results-stijl. Livewire en Flux zitten in de stack, maar zijn nog niet de drijvende kracht achter de huidige admin-modules.

## Huidige Gebruikersflow

### Niet-Ingelogde Bezoekers

Niet-ingelogde bezoekers landen op `/` en zien een publieke homepage in Hermes-stijl.

### Gewone Gebruikers

Een ingelogde gebruiker met rol `User` wordt vanaf `/` doorgestuurd naar `/dashboard`.

### Admin En Beheerder

Gebruikers met rol `Admin` of `Beheerder` worden vanaf `/` doorgestuurd naar `/admin-portal`.

Belangrijk verschil:
- `Admin` heeft volledig overzicht over alle organisaties en alle users
- `Beheerder` werkt alleen binnen de eigen organisatie

## Rollen En Autorisatie

Er zijn nu drie rollen:
- `User`
- `Admin`
- `Beheerder`

Gedrag per rol:
- `User`
  Geen toegang tot het admin-portal.
- `Admin`
  Ziet en beheert alle organisaties en alle users.
- `Beheerder`
  Heeft toegang tot het admin-portal, maar alleen voor records met dezelfde `org_id` als de eigen user.

Belangrijke huidige scope-regels:
- userlijsten voor `Beheerder` tonen alleen users uit de eigen organisatie
- organisatieoverzicht voor `Beheerder` toont alleen de eigen organisatie
- een `Beheerder` kan geen `Admin`-rol toekennen
- een `Beheerder` kan users niet aan een andere organisatie koppelen
- een `Beheerder` kan geen nieuwe organisaties aanmaken of organisaties verwijderen
- een `Beheerder` kan wel de eigen organisatie wijzigen

Belangrijke implementatie:
- [app/Models/User.php](/Users/robert/Desktop/MyApp-001/app/Models/User.php)
- [app/Http/Middleware/EnsureUserIsAdmin.php](/Users/robert/Desktop/MyApp-001/app/Http/Middleware/EnsureUserIsAdmin.php)
- [app/Actions/Fortify/LoginResponse.php](/Users/robert/Desktop/MyApp-001/app/Actions/Fortify/LoginResponse.php)

## Organisatie-Model En Datamodel

### Tabel `organizations`

De applicatie bevat nu een organisatiestructuur in `organizations` met:
- `org_id`
- `naam`
- `adres`
- `postcode`
- `plaats`
- `land`
- `telefoon`
- `contact_id`
- timestamps

`contact_id` verwijst naar `users.id`.

### Tabel `users`

De `users`-tabel bevat nu aanvullend:
- `role`
- `org_id`

`org_id` verwijst naar `organizations.org_id`.

Belangrijke afspraken:
- elke user hoort bij één organisatie
- standaard wordt `Hermes Results` gebruikt als organisatie-default
- bestaande users zijn ook aan `Hermes Results` gekoppeld

Belangrijke implementatie:
- [database/migrations/2026_03_28_073541_create_organizations_table.php](/Users/robert/Desktop/MyApp-001/database/migrations/2026_03_28_073541_create_organizations_table.php)
- [database/migrations/2026_03_28_074008_add_org_id_to_users_table.php](/Users/robert/Desktop/MyApp-001/database/migrations/2026_03_28_074008_add_org_id_to_users_table.php)
- [app/Models/Organization.php](/Users/robert/Desktop/MyApp-001/app/Models/Organization.php)
- [app/Models/User.php](/Users/robert/Desktop/MyApp-001/app/Models/User.php)

## Routes En Navigatie

Belangrijkste routes:
- `/`
  Publieke homepage of rolafhankelijke redirect
- `/dashboard`
  Dashboard voor `User`
- `/admin-portal`
  Portal voor `Admin` en `Beheerder`
- `/admin-portal/users`
  Users-overzicht
- `/admin-portal/organizations`
  Organisaties-overzicht

De hoofdroute-definitie staat in:
- [routes/web.php](/Users/robert/Desktop/MyApp-001/routes/web.php)

Het admin-menu bevat nu:
- `Gebruikers`
- `Organisaties`

## Authenticatie

Wat nu werkt:
- registratie
- login
- logout
- forgot-password
- reset-password
- e-mailverificatie

Belangrijke auth-afspraken:
- nieuwe accounts krijgen standaard rol `User`
- nieuwe accounts krijgen standaard `org_id` van `Hermes Results`
- `Admin` en `Beheerder` gaan na login naar het admin-portal
- `User` blijft in de dashboardflow

Belangrijke implementatie:
- [app/Actions/Fortify/CreateNewUser.php](/Users/robert/Desktop/MyApp-001/app/Actions/Fortify/CreateNewUser.php)
- [app/Actions/Fortify/LoginResponse.php](/Users/robert/Desktop/MyApp-001/app/Actions/Fortify/LoginResponse.php)
- [app/Http/Controllers/Auth/EmailVerificationController.php](/Users/robert/Desktop/MyApp-001/app/Http/Controllers/Auth/EmailVerificationController.php)

## Admin-Module: Gebruikersbeheer

De users-module bevat nu:
- overzichtslijst
- zoeken op naam of e-mail
- paginatie
- CSV-export
- user aanmaken
- user wijzigen
- user verwijderen met bevestigingsstap
- organisatiekeuze in het user-form

Huidige beheerregels:
- `Admin` kan alle users zien en beheren
- `Beheerder` ziet alleen users met hetzelfde `org_id`
- `Beheerder` kan geen users buiten de eigen organisatie beheren
- `Beheerder` kan geen `Admin` als rol kiezen in het user-form

Belangrijke implementatie:
- [app/Http/Controllers/Admin/UserController.php](/Users/robert/Desktop/MyApp-001/app/Http/Controllers/Admin/UserController.php)
- [app/Http/Requests/Admin/StoreUserRequest.php](/Users/robert/Desktop/MyApp-001/app/Http/Requests/Admin/StoreUserRequest.php)
- [app/Http/Requests/Admin/UpdateUserRequest.php](/Users/robert/Desktop/MyApp-001/app/Http/Requests/Admin/UpdateUserRequest.php)
- [resources/views/admin/users/index.blade.php](/Users/robert/Desktop/MyApp-001/resources/views/admin/users/index.blade.php)
- [resources/views/admin/users/form.blade.php](/Users/robert/Desktop/MyApp-001/resources/views/admin/users/form.blade.php)

## Admin-Module: Organisaties

De organisatiesmodule bevat nu:
- overzichtslijst
- organisatie aanmaken
- organisatie wijzigen
- organisatie verwijderen met bevestigingsstap

Huidige beheerregels:
- `Admin` ziet en beheert alle organisaties
- `Beheerder` ziet alleen de eigen organisatie
- `Beheerder` kan de eigen organisatie wijzigen
- `Beheerder` kan geen nieuwe organisaties aanmaken
- `Beheerder` kan geen organisaties verwijderen
- `Hermes Results` kan niet verwijderd worden
- organisaties met gekoppelde users kunnen niet verwijderd worden

Belangrijke implementatie:
- [app/Http/Controllers/Admin/OrganizationController.php](/Users/robert/Desktop/MyApp-001/app/Http/Controllers/Admin/OrganizationController.php)
- [app/Http/Requests/Admin/StoreOrganizationRequest.php](/Users/robert/Desktop/MyApp-001/app/Http/Requests/Admin/StoreOrganizationRequest.php)
- [app/Http/Requests/Admin/UpdateOrganizationRequest.php](/Users/robert/Desktop/MyApp-001/app/Http/Requests/Admin/UpdateOrganizationRequest.php)
- [resources/views/admin/organizations/index.blade.php](/Users/robert/Desktop/MyApp-001/resources/views/admin/organizations/index.blade.php)
- [resources/views/admin/organizations/form.blade.php](/Users/robert/Desktop/MyApp-001/resources/views/admin/organizations/form.blade.php)

## UI En Branding

De belangrijkste publieke, auth- en admin-pagina's gebruiken momenteel:
- Blade-views
- inline CSS
- Hermes Results-branding
- een gedeelde admin-layout

Visuele kenmerken:
- warm premium kleurenpalet
- zakelijke uitstraling
- sticky topbar
- consistente Hermes-logo-integratie
- herbruikbare componenten voor layout, facts, sections en admin-menu

Belangrijke layout-bestanden:
- [resources/views/components/layouts/hermes-admin.blade.php](/Users/robert/Desktop/MyApp-001/resources/views/components/layouts/hermes-admin.blade.php)
- [resources/views/components/admin-menu.blade.php](/Users/robert/Desktop/MyApp-001/resources/views/components/admin-menu.blade.php)

## Mail En Infrastructuur

Mail wordt via SMTP verstuurd met Hermes Results-branding.

Relevante punten:
- forgot-password mails werken
- verificatiemails werken
- `.env` bevat actieve SMTP-configuratie

## Teststatus

Het project gebruikt Pest-featuretests als primaire veiligheidslaag.

Belangrijk:
- users-beheer is getest
- organisaties-beheer is getest
- auth-redirects en portaltoegang zijn getest
- rol- en org-scope voor `Beheerder` zijn getest

Typische verificatie in deze codebase:
- `php artisan test --compact ...`
- `vendor/bin/pint --dirty --format agent`

## Belangrijkste Open Richting Voor Vervolg

De volgende logische uitbreidingen bouwen direct voort op de huidige basis:
- questionnaire-domein modelleren met meerdere gerelateerde tabellen
- admin-portal uitbreiden met scoped datasets per organisatie
- bepalen welke questionnaire-acties `Admin` versus `Beheerder` mogen uitvoeren
- later eventueel delen van het admin-portal migreren naar Livewire/Flux voor rijkere interactie

## Aandachtspunten Voor Toekomstig Werk

- Houd altijd rekening met `org_id`-scope.
- Nieuwe admin-modules moeten expliciet onderscheid maken tussen `Admin` en `Beheerder`.
- Als een feature records toont of wijzigt, moet worden vastgesteld:
  - ziet `Admin` alles?
  - ziet `Beheerder` alleen eigen organisatie-data?
  - mag `Beheerder` records alleen bekijken, of ook wijzigen?
- `Hermes Results` is op dit moment een systeemkritische default-organisatie.
- Nieuwe user-creatie en user-beheer moeten compatibel blijven met de bestaande rol- en org-logica.
