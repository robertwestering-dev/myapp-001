# Project Context

## Doel Van Dit Bestand

Dit document is de actuele baseline van het Hermes Results-project. Het beschrijft wat er nu functioneel en technisch staat, hoe de live-omgeving is ingericht, en welke aandachtspunten belangrijk zijn voor vervolgwerk.

Gebruik dit bestand als startpunt voor:
- nieuwe features
- bugfixes
- vervolgdeploys
- onboarding of context-herstel na een pauze

## Korte Samenvatting

Hermes Results is nu een werkende Laravel 13-applicatie voor organisatiegebonden vragenlijsten en rapportage.

De huidige basis omvat:
- publieke homepage op `https://hermesresults.com`
- Fortify-authenticatie met registratie, login, wachtwoordreset en e-mailverificatie
- dashboard voor gewone gebruikers
- admin-portal voor `Admin` en `Beheerder`
- beheer van organisaties
- beheer van gebruikers
- questionnaire-bibliotheek
- beschikbaarheid van questionnaires per organisatie
- invulflow voor gebruikers
- rapportage, statistiek en CSV-export van responses

De applicatie staat live op Hostnet en werkt via het domein `hermesresults.com`.

## Huidige Live Status

De applicatie is live gedeployed en functioneert.

Bekende live basis:
- domein: `https://hermesresults.com`
- hostingprovider: Hostnet
- PHP: 8.4
- database: MySQL
- app draait succesvol in productie
- admin-login werkt
- de twee baseline-questionnaires zijn aanwezig op live via migrations

Belangrijke live paden:
- publieke webroot: `/webroots/sites/hermesresults.com`
- Laravel app-map: `/webroots/sites/hermesresults.com/hermesresults-app`

Belangrijke deploy-keuze:
- de volledige Laravel-app staat binnen de webroot in een submap
- de root-`index.php` van de site laadt vervolgens de app vanuit `hermesresults-app`
- deze structuur is gekozen omdat Hostnet de eerdere variant buiten de webroot niet correct kon laden

Er staat daarnaast nog een oudere app-kopie in:
- `/home/cl1myceal_u/hermesresults-app`

Die oude map is geen actieve live-map meer en kan later worden verwijderd zodra hij niet meer nodig is als extra backup.

## Technische Stack

- PHP 8.5 in projectconfiguratie, live draait op PHP 8.4
- Laravel 13
- Laravel Fortify
- Livewire 4
- Flux UI 2
- Blade views
- Vite
- Tailwind CSS 4
- Pest
- Pint

Belangrijke observatie:
- Livewire en Flux zijn beschikbaar in de stack
- de huidige kern van het admin- en questionnaire-gedeelte is vooral controller- en Blade-gedreven

## Gebruikersrollen

Er zijn drie rollen:
- `User`
- `Beheerder`
- `Admin`

Rolgedrag:
- `User`
  Ziet geen admin-portal en werkt vanuit `/dashboard`.
- `Beheerder`
  Heeft toegang tot het admin-portal, maar alleen binnen de eigen organisatie-scope.
- `Admin`
  Heeft volledige toegang over alle organisaties, gebruikers, questionnaires, beschikbaarheid en responses.

Technische basis:
- rolwaarden staan in [User.php](/Users/robert/Desktop/MyApp-001/app/Models/User.php)
- admin-toegang loopt via `canAccessAdminPortal()`
- middleware [EnsureUserIsAdmin.php](/Users/robert/Desktop/MyApp-001/app/Http/Middleware/EnsureUserIsAdmin.php) laat zowel `Admin` als `Beheerder` toe

Belangrijke afspraak:
- nieuwe registraties krijgen standaard rol `User`
- een nieuwe live omgeving bevat dus niet automatisch een admin-account
- een gebruiker moet desnoods na registratie via Artisan/Tinker naar `Admin` worden gepromoveerd

## Gebruikersflow

### Publieke Bezoeker

Niet-ingelogde bezoekers landen op `/` en zien de publieke homepage.

Daarnaast is er een publiek contactformulier op:
- `POST /contact`

### Gewone Gebruiker

Een ingelogde `User` komt op `/dashboard`.

Op het dashboard ziet de gebruiker:
- questionnaires die beschikbaar zijn voor de eigen organisatie
- of een questionnaire actief en beschikbaar is
- eventuele bestaande eigen response
- een link naar het invulscherm

### Admin En Beheerder

Gebruikers met toegang tot het admin-portal worden vanaf `/` of `/dashboard` doorgestuurd naar:
- `/admin-portal`

Vanuit het admin-portal is toegang tot:
- gebruikersbeheer
- organisatiebeheer
- questionnaire-bibliotheek
- questionnaire-beschikbaarheid per organisatie
- response-overzicht
- statistieken
- exportfunctionaliteit

## Organisatie- En Scope-logica

De applicatie is organisatiegebonden.

Belangrijkste modelafspraak:
- elke gebruiker hoort bij precies één organisatie via `users.org_id`

Scope-regels:
- `Admin` werkt over alle organisaties heen
- `Beheerder` werkt alleen binnen de eigen `org_id`
- `User` ziet alleen questionnaires die voor de eigen organisatie beschikbaar zijn

Praktische gevolgen:
- `Beheerder` ziet alleen relevante users en organisaties binnen eigen scope
- `Beheerder` kan geen globale admin-acties uitvoeren
- `Beheerder` kan geen questionnaire-inhoud beheren
- `Beheerder` kan wel beschikbaarheid van questionnaires binnen de eigen organisatie beheren

## Questionnaires

De questionnaire-module is nu een belangrijke kern van de applicatie.

Er zijn op dit moment twee baseline-questionnaires:
- `Adaptability Scan volgens het A.C.E.-model`
- `Quick scan digitale weerbaarheid`

Belangrijke implementatie:
- [SyncAdaptabilityAceQuestionnaire.php](/Users/robert/Desktop/MyApp-001/app/Actions/Questionnaires/SyncAdaptabilityAceQuestionnaire.php)
- [SyncDigitalResilienceQuickScanQuestionnaire.php](/Users/robert/Desktop/MyApp-001/app/Actions/Questionnaires/SyncDigitalResilienceQuickScanQuestionnaire.php)

Belangrijke eigenschap:
- deze baseline-questionnaires worden vanuit de code aangemaakt
- ze zijn dus reproduceerbaar via migrations
- daardoor kwamen ze automatisch mee naar live tijdens `php artisan migrate --force`

Dit is belangrijk voor vervolgwerk:
- questionnaires die structureel onderdeel van het product zijn, moeten bij voorkeur in code blijven
- niet alleen in een lokale ontwikkel-database

## Questionnaire-structuur

De inhoud is gelaagd opgebouwd:
- `questionnaires`
- `questionnaire_categories`
- `questionnaire_questions`

Beschikbaarheid per organisatie loopt via:
- `organization_questionnaires`

Responses lopen via:
- `questionnaire_responses`
- `questionnaire_response_answers`

De huidige flow is:
1. een questionnaire bestaat in de bibliotheek
2. de questionnaire wordt beschikbaar gemaakt voor een organisatie
3. een gebruiker uit die organisatie ziet de questionnaire op het dashboard
4. de gebruiker vult de questionnaire in
5. antwoorden en response worden opgeslagen
6. admin-portal toont rapportage en exports

## Routes

Belangrijkste actuele routes:
- `/`
- `/dashboard`
- `/questionnaires/{organizationQuestionnaire}`
- `/admin-portal`
- `/admin-portal/users`
- `/admin-portal/organizations`
- `/admin-portal/questionnaires`
- `/admin-portal/questionnaire-responses`
- `/verify-email/{id}/{hash}`

De routes staan in [web.php](/Users/robert/Desktop/MyApp-001/routes/web.php).

Belangrijk routegedrag:
- `/` stuurt ingelogde gebruikers rolafhankelijk door
- `/dashboard` stuurt admin-achtige rollen ook door naar `/admin-portal`
- questionnaire-invullen vereist auth
- admin-portal vereist auth plus middleware voor admin-portaltoegang

## Belangrijkste Controllers En Domeindelen

Admin:
- [AdminPortalController.php](/Users/robert/Desktop/MyApp-001/app/Http/Controllers/Admin/AdminPortalController.php)
- [UserController.php](/Users/robert/Desktop/MyApp-001/app/Http/Controllers/Admin/UserController.php)
- [OrganizationController.php](/Users/robert/Desktop/MyApp-001/app/Http/Controllers/Admin/OrganizationController.php)
- [QuestionnaireController.php](/Users/robert/Desktop/MyApp-001/app/Http/Controllers/Admin/QuestionnaireController.php)
- [QuestionnaireCategoryController.php](/Users/robert/Desktop/MyApp-001/app/Http/Controllers/Admin/QuestionnaireCategoryController.php)
- [QuestionnaireQuestionController.php](/Users/robert/Desktop/MyApp-001/app/Http/Controllers/Admin/QuestionnaireQuestionController.php)
- [OrganizationQuestionnaireController.php](/Users/robert/Desktop/MyApp-001/app/Http/Controllers/Admin/OrganizationQuestionnaireController.php)
- [QuestionnaireResponseReportController.php](/Users/robert/Desktop/MyApp-001/app/Http/Controllers/Admin/QuestionnaireResponseReportController.php)

Publiek en user:
- [ContactRequestController.php](/Users/robert/Desktop/MyApp-001/app/Http/Controllers/ContactRequestController.php)
- [QuestionnaireResponseController.php](/Users/robert/Desktop/MyApp-001/app/Http/Controllers/QuestionnaireResponseController.php)
- [EmailVerificationController.php](/Users/robert/Desktop/MyApp-001/app/Http/Controllers/Auth/EmailVerificationController.php)

## Authenticatie

Authenticatie is gebaseerd op Laravel Fortify.

Actieve onderdelen:
- registratie
- login
- password reset
- e-mailverificatie
- two-factor-authenticatie

Belangrijke implementatiepunten:
- registratie maakt standaard een `User`
- login stuurt gebruikers rolafhankelijk door
- e-mailverificatie heeft een publieke verify-route

## UI-status

De applicatie heeft een branded Blade-interface in Hermes Results-stijl.

Belangrijke schermen:
- publieke homepage
- dashboard
- admin-portal
- CRUD-schermen voor users, organisaties en questionnaires
- response-rapportages

Designstatus:
- de app heeft een duidelijke branded basis
- de structuur is functioneel werkend
- de frontend is niet volledig Livewire-first opgebouwd

## Database-status

De live database is succesvol aangemaakt op Hostnet.

Belangrijk:
- migrations zijn uitgevoerd op live
- de baseline-questionnaires zijn daardoor automatisch aanwezig
- admin-accounts worden niet automatisch seed-based aangemaakt

Gevolg:
- op een nieuwe omgeving moet een eerste admin handmatig worden aangemaakt of gepromoveerd

## Deploy-status En Aanpak

De eerste productie-deploy is succesvol uitgevoerd naar Hostnet.

De werkende aanpak is nu:
1. lokaal dependencies en frontend-build gereed maken
2. bestanden uploaden naar `/webroots/sites/hermesresults.com/hermesresults-app`
3. productie-`.env` gebruiken
4. indien nodig migrations draaien
5. Laravel-caches verversen
6. Hostnet-cache legen via `cache-purge`

Belangrijke deploy-opmerking:
- de actieve Laravel-app staat live binnen de webroot-submap
- de site-root bevat de publieke bestanden plus een aangepaste `index.php`
- eerdere poging met een app-map buiten de webroot werkte niet betrouwbaar op Hostnet

## Praktische Deploy-checklist

Globale volgorde voor volgende deploys:
1. lokaal `npm run build`
2. gewijzigde bestanden uploaden naar `/webroots/sites/hermesresults.com/hermesresults-app`
3. SSH naar Hostnet
4. indien nodig `php artisan migrate --force`
5. `php artisan optimize:clear`
6. `php artisan config:cache`
7. `php artisan route:cache`
8. `php artisan view:cache`
9. `cache-purge`

Belangrijke loglocatie bij problemen:
- `/logs/sites/php_error.log`

## Bekende Operationele Aandachtspunten

- productie gebruikt een eigen `.env`; die niet onbedoeld overschrijven
- databasewachtwoord is na de eerste deploy best opnieuw te wijzigen voor veiligheid
- de oude map `/home/cl1myceal_u/hermesresults-app` is nu alleen nog backup
- bij frontend-wijzigingen moet `public/build` opnieuw meegeüpload worden
- bij nieuwe baseline-questionnaires is code-first aanmaken via migrations of seeders de voorkeursroute

## Aanbevolen Vervolgprioriteiten

- deployproces verder vereenvoudigen en documenteren
- eventuele echte productie-seeders of sync-acties explicieter structureren
- bepalen welke questionnaires product-standaard zijn en dus in code horen
- deployment en rollback-procedure nog verder standaardiseren
- later eventueel de oude home-directory app-map opruimen zodra die niet meer nodig is

## Baseline-conclusie

Hermes Results is nu geen prototype meer, maar een functionele live Laravel-applicatie met:
- publieke homepage
- werkende auth-flow
- organisatiegescopeerd dashboard
- admin-portal voor `Admin` en `Beheerder`
- questionnaire-bibliotheek
- questionnaire-beschikbaarheid per organisatie
- invulflow en response-opslag
- rapportage en exports
- een werkende productie-deploy op Hostnet

Dit document moet voorlopig worden behandeld als de functionele en operationele baseline voor vervolgwerk.
