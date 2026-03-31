# Project Context

## Doel Van Dit Bestand

Dit document is de actuele baseline van het Hermes Results-project. Gebruik dit bestand als startpunt voor vervolgwerk, onboarding, bugfixes, deploys en context-herstel na een pauze.

De inhoud hieronder beschrijft de huidige functionele en technische status zoals die nu in de codebase aanwezig is.

## Korte Samenvatting

Hermes Results is een Laravel 13-applicatie voor organisatiegebonden questionnaires, response-opslag, rapportage, CSV-export en een afgeschermde Academy met e-learnings.

De app bevat nu:
- publieke branded homepage
- contactformulier
- Fortify-auth met registratie, login, wachtwoord reset en e-mailverificatie
- dashboard voor gewone gebruikers
- Academy-catalogus voor ingelogde gebruikers
- admin-portal voor `Admin` en `Beheerder`
- questionnaire-bibliotheek en beschikbaarheid per organisatie
- meerstaps invulflow voor questionnaires
- response-overzicht, statistieken en export
- meertalige interface in `nl`, `en`, `de` en `fr`

## Technische Stack

- PHP 8.5 in projectconfiguratie
- Laravel 13
- Laravel Fortify
- Livewire 4
- Flux UI 2
- Blade views
- Vite
- Tailwind CSS 4
- Pest
- Pint
- MySQL

Belangrijke observatie:
- de kern van de publieke site, auth, Academy en questionnaire-flow is momenteel vooral Blade- en controller-gedreven
- Livewire en Flux zijn beschikbaar, maar nog niet de dominante aanpak in de hoofdflows

## Live En Hosting

Bekende productiecontext:
- live domein: `https://hermesresults.com`
- hostingprovider: Hostnet
- database: MySQL
- productie draait momenteel via Hostnet-structuur binnen de webroot

Belangrijke live paden:
- publieke webroot: `/webroots/sites/hermesresults.com`
- Laravel app-map: `/webroots/sites/hermesresults.com/hermesresults-app`

Historische noot:
- er bestaat nog een oudere kopie in `/home/cl1myceal_u/hermesresults-app`
- die map is niet de actieve live-map

## Domeinen En Taalgedrag

Doelbeelddomeinen:
- `hermesresults.com`
- `hermesresults.nl`
- `hermesresults.eu`

Actuele codebasis:
- in [locales.php](/Users/robert/Desktop/MyApp-001/config/locales.php) zijn host-defaults opgenomen:
- `.com` -> `en`
- `.nl` -> `nl`
- `.eu` -> `de`

Belangrijke caveat:
- deze host-gebaseerde taalkeuze werkt alleen echt wanneer `.com`, `.nl` en `.eu` direct naar dezelfde Laravel-app verwijzen
- zolang `.nl` of `.eu` via een kale `301 redirect` naar `.com` gaan, ziet Laravel de oorspronkelijke host niet en werkt dit niet betrouwbaar

## Meertaligheid

De applicatie is nu voorbereid op vier talen:
- Nederlands
- Engels
- Duits
- Frans

Huidige implementatie:
- vertalingen via `lang/{locale}/hermes.php`
- talen switchen via header-switcher
- switcher toont compacte labels: `NL`, `EN`, `DE`, `FR`
- gastvoorkeur wordt in sessie opgeslagen
- ingelogde voorkeur wordt opgeslagen op `users.locale`
- middleware [SetApplicationLocale.php](/Users/robert/Desktop/MyApp-001/app/Http/Middleware/SetApplicationLocale.php) bepaalt de taal in deze volgorde:
- user voorkeur
- sessievoorkeur
- host-default
- app-default

Status van vertaalwerk:
- homepage is meertalig gekoppeld
- auth-schermen zijn meertalig gekoppeld
- dashboard, Academy, admin-portal, questionnaire-flow en rapportages zijn meertalig gekoppeld
- de belangrijkste Duitse en Franse ontbrekende teksten zijn toegevoegd

Standaardinstelling:
- `APP_LOCALE=nl`
- `APP_FALLBACK_LOCALE=nl`

## Gebruikersrollen

Er zijn drie rollen:
- `User`
- `Beheerder`
- `Admin`

Rolgedrag:
- `User`
  gebruikt `/dashboard`, ziet de Academy en heeft geen admin-portaltoegang
- `Beheerder`
  heeft admin-portaltoegang binnen de eigen organisatie-scope
- `Admin`
  heeft volledige toegang over organisaties, users, questionnaires, beschikbaarheid, responses en Academy-beheer

Technische basis:
- rolwaarden staan in [User.php](/Users/robert/Desktop/MyApp-001/app/Models/User.php)
- admin-portaltoegang loopt via `canAccessAdminPortal()`
- middleware [EnsureUserIsAdmin.php](/Users/robert/Desktop/MyApp-001/app/Http/Middleware/EnsureUserIsAdmin.php) laat `Admin` en `Beheerder` toe
- middleware [EnsureUserIsGlobalAdmin.php](/Users/robert/Desktop/MyApp-001/app/Http/Middleware/EnsureUserIsGlobalAdmin.php) begrenst Academy-beheer expliciet tot globale `Admin`

Belangrijke afspraak:
- nieuwe registraties krijgen standaard rol `User`

## Gebruikersflow

### Publieke Bezoeker

Niet-ingelogde bezoekers landen op `/` en zien de publieke homepage.

Publieke acties:
- contactformulier via `POST /contact`
- registratie
- login
- wachtwoord reset

### Gewone Gebruiker

Een ingelogde `User` komt op `/dashboard`.

Daar ziet de gebruiker:
- questionnaires die beschikbaar zijn voor de eigen organisatie
- invulstatus per questionnaire
- een link naar de invulflow
- een Academy-blok met link naar de catalogus

Via `/academy` ziet de gebruiker:
- e-learningtegels met doelgroep, doel, samenvatting, leerdoelen, inhoud en gemiddelde duur
- een link naar de gekoppelde web-export van de training

### Admin En Beheerder

Gebruikers met admin-portaltoegang worden vanaf `/` of `/dashboard` doorgestuurd naar:
- `/admin-portal`

Van daaruit is toegang tot:
- users
- organisaties
- questionnaires
- availability
- responses
- statistieken
- exports

Extra beperking:
- alleen globale `Admin` ziet en beheert Academy-cursussen in de admin-portal

## Organisatie- En Scope-logica

De applicatie is organisatiegebonden.

Belangrijkste afspraak:
- elke gebruiker hoort bij precies één organisatie via `users.org_id`

Scope-regels:
- `Admin` ziet alles
- `Beheerder` werkt binnen de eigen organisatie-scope
- `User` ziet alleen questionnaires die voor de eigen organisatie beschikbaar zijn

Academy-scope:
- de Academy-catalogus is zichtbaar voor ingelogde gebruikers
- het beheer van Academy-cursussen is niet organisatie-specifiek maar globaal
- alleen `Admin` mag de tabel `academy_courses` zien en onderhouden

## Academy

De Academy-module is nu een apart domeindeel naast questionnaires.

Functionele status:
- catalogusroute: `/academy`
- catalogus is alleen toegankelijk voor ingelogde gebruikers
- dashboard van gewone gebruikers bevat een Academy-promoblok met link naar de catalogus
- Academy-cursussen staan in MySQL in plaats van in configbestanden
- startcursussen voor adaptability en digitale weerbaarheid zijn als seeddata aanwezig

Datamodel:
- `academy_courses`

Belangrijke velden in `academy_courses`:
- `slug`
- `theme`
- `path`
- `estimated_minutes`
- `sort_order`
- `is_active`
- meertalige JSON-velden voor `title`, `audience`, `goal`, `summary`, `learning_goals` en `contents`

Belangrijke implementaties:
- [AcademyCourse.php](/Users/robert/Desktop/MyApp-001/app/Models/AcademyCourse.php)
- [AcademyController.php](/Users/robert/Desktop/MyApp-001/app/Http/Controllers/AcademyController.php)
- [AcademyCourseController.php](/Users/robert/Desktop/MyApp-001/app/Http/Controllers/Admin/AcademyCourseController.php)
- [AcademyCourseSeeder.php](/Users/robert/Desktop/MyApp-001/database/seeders/AcademyCourseSeeder.php)

Belangrijke afspraak:
- de Academy-catalogus wordt nu primair vanuit MySQL opgebouwd
- de daadwerkelijke e-learningcontent blijft een web-export in een eigen publieke map, bijvoorbeeld onder `public/academy-courses/...`

## Questionnaires

De questionnaire-module is een kernonderdeel van het product.

Baseline-questionnaires in code:
- `Adaptability Scan volgens het A.C.E.-model`
- `Quick scan digitale weerbaarheid`

Belangrijke implementaties:
- [SyncAdaptabilityAceQuestionnaire.php](/Users/robert/Desktop/MyApp-001/app/Actions/Questionnaires/SyncAdaptabilityAceQuestionnaire.php)
- [SyncDigitalResilienceQuickScanQuestionnaire.php](/Users/robert/Desktop/MyApp-001/app/Actions/Questionnaires/SyncDigitalResilienceQuickScanQuestionnaire.php)

Belangrijke afspraak:
- product-brede baseline-questionnaires blijven bij voorkeur reproduceerbaar in code

## Questionnaire-structuur

Datamodel:
- `questionnaires`
- `questionnaire_categories`
- `questionnaire_questions`
- `organization_questionnaires`
- `questionnaire_responses`
- `questionnaire_response_answers`

Actuele invulflow:
1. questionnaire bestaat in bibliotheek
2. questionnaire wordt beschikbaar gemaakt voor een organisatie
3. gebruiker ziet questionnaire op dashboard
4. gebruiker vult questionnaire in
5. response en answers worden opgeslagen
6. admin-portal toont rapportage en export

## Questionnaire UX-status

De questionnaire-weergave op `/questionnaires/{organizationQuestionnaire}` is aangepast naar een meerstapsflow.

Huidige status:
- invulinstructie staat boven de questionnaire en gebruikt volle breedte
- questionnaire gebruikt volle contentbreedte
- elke categorie is een eigen stap
- gebruiker kan terug naar vorige stap
- gebruiker kan alleen door naar volgende stap wanneer verplichte vragen op de huidige stap zijn ingevuld
- de tekst `Kies een antwoord` is verwijderd
- header op de questionnairepagina is gelijkgetrokken met de homepage-header

Belangrijke view:
- [show.blade.php](/Users/robert/Desktop/MyApp-001/resources/views/questionnaires/show.blade.php)

## Routes

Belangrijkste actuele routes:
- `/`
- `/dashboard`
- `/academy`
- `/questionnaires/{organizationQuestionnaire}`
- `/admin-portal`
- `/admin-portal/academy-courses`
- `/admin-portal/users`
- `/admin-portal/organizations`
- `/admin-portal/questionnaires`
- `/admin-portal/questionnaire-responses`
- `/verify-email/{id}/{hash}`
- `POST /contact`
- `POST /locale`

Routebasis:
- [web.php](/Users/robert/Desktop/MyApp-001/routes/web.php)

## Belangrijkste Controllers En Domeindelen

Admin:
- [AdminPortalController.php](/Users/robert/Desktop/MyApp-001/app/Http/Controllers/Admin/AdminPortalController.php)
- [AcademyCourseController.php](/Users/robert/Desktop/MyApp-001/app/Http/Controllers/Admin/AcademyCourseController.php)
- [UserController.php](/Users/robert/Desktop/MyApp-001/app/Http/Controllers/Admin/UserController.php)
- [OrganizationController.php](/Users/robert/Desktop/MyApp-001/app/Http/Controllers/Admin/OrganizationController.php)
- [QuestionnaireController.php](/Users/robert/Desktop/MyApp-001/app/Http/Controllers/Admin/QuestionnaireController.php)
- [QuestionnaireCategoryController.php](/Users/robert/Desktop/MyApp-001/app/Http/Controllers/Admin/QuestionnaireCategoryController.php)
- [QuestionnaireQuestionController.php](/Users/robert/Desktop/MyApp-001/app/Http/Controllers/Admin/QuestionnaireQuestionController.php)
- [OrganizationQuestionnaireController.php](/Users/robert/Desktop/MyApp-001/app/Http/Controllers/Admin/OrganizationQuestionnaireController.php)
- [QuestionnaireResponseReportController.php](/Users/robert/Desktop/MyApp-001/app/Http/Controllers/Admin/QuestionnaireResponseReportController.php)

Publiek en user:
- [AcademyController.php](/Users/robert/Desktop/MyApp-001/app/Http/Controllers/AcademyController.php)
- [ContactRequestController.php](/Users/robert/Desktop/MyApp-001/app/Http/Controllers/ContactRequestController.php)
- [LocaleController.php](/Users/robert/Desktop/MyApp-001/app/Http/Controllers/LocaleController.php)
- [QuestionnaireResponseController.php](/Users/robert/Desktop/MyApp-001/app/Http/Controllers/QuestionnaireResponseController.php)
- [EmailVerificationController.php](/Users/robert/Desktop/MyApp-001/app/Http/Controllers/Auth/EmailVerificationController.php)

## Authenticatie

Authenticatie is gebaseerd op Laravel Fortify.

Actieve onderdelen:
- registratie
- login
- password reset
- e-mailverificatie

Belangrijke actuele functionele keuzes:
- minimum wachtwoordlengte bij registratie is `8`
- overige wachtwoordeisen zijn behouden
- nieuwe registraties sturen een melding naar `robert.van.westering@outlook.com`

Belangrijke registratie-implementatie:
- [CreateNewUser.php](/Users/robert/Desktop/MyApp-001/app/Actions/Fortify/CreateNewUser.php)
- [NewAccountRegistered.php](/Users/robert/Desktop/MyApp-001/app/Mail/NewAccountRegistered.php)

## Mail En Contact

Contactformulier:
- ontvanger staat op `robert.van.westering@outlook.com`

Nieuwe accountregistratie:
- verstuurt melding naar `robert.van.westering@outlook.com`
- onderwerp: `Nieuw account Hermes Results`

Belangrijke config:
- [contact.php](/Users/robert/Desktop/MyApp-001/config/contact.php)

## Rapportage En Exports

Het admin-portal bevat response-overzichten, statistiekweergave en CSV-export.

Actuele status:
- detail export
- summary export
- stats export
- belangrijke labels en CSV-headers zijn meertalig gekoppeld

Belangrijke controller:
- [QuestionnaireResponseReportController.php](/Users/robert/Desktop/MyApp-001/app/Http/Controllers/Admin/QuestionnaireResponseReportController.php)

## UI En Branding

De app gebruikt een duidelijke Hermes Results-stijl.

Homepage-palette:
- primaire kleuren:
- `#1E473D`
- `#102A23`
- `#BC5B2C`
- `#8D3F18`
- ondersteunende kleuren:
- `#F4EDE3`
- `#EFE5D7`
- `#172321`
- `#56655F`
- `#D6B37A`

Belangrijke ontwerpkenmerken:
- warme zandtinten
- diep groen als vertrouwenskleur
- clay als actieaccent
- goud voor verfijning
- afgeronde panelen
- editorial typografie

Er is ook een eerste PowerPoint-templatebestand gegenereerd in:
- [hermes-results-powerpoint-template.pptx](/Users/robert/Desktop/MyApp-001/resources/presentation/hermes-results-powerpoint-template.pptx)

Belangrijke caveat:
- dit bestand is technisch als pakket opgebouwd, maar is nog niet bevestigd als bruikbaar in PowerPoint op een Windows-laptop met Microsoft Office

## Teststatus En Tooling

Actuele lokale status in deze werkmap:
- `vendor/bin/pint` is beschikbaar
- Pest is beschikbaar via `vendor/bin/pest`
- `php artisan test` is in deze omgeving niet de primaire runner; gerichte tests zijn recent uitgevoerd via Pest

Wel aanwezige testbasis:
- featuretests voor registratie, locale-gedrag, questionnaire-flow, dashboard en Academy
- Academy-tests dekken publiekscatalogus en adminbeheerflow

Belangrijke testfiles:
- [LocalePreferenceTest.php](/Users/robert/Desktop/MyApp-001/tests/Feature/LocalePreferenceTest.php)
- [AcademyTest.php](/Users/robert/Desktop/MyApp-001/tests/Feature/AcademyTest.php)
- [AcademyAdminManagementTest.php](/Users/robert/Desktop/MyApp-001/tests/Feature/AcademyAdminManagementTest.php)

## Openstaande Aandachtspunten

- host-based locale defaults werken pas volledig zodra `.nl` en `.eu` direct naar dezelfde app verwijzen in plaats van via kale redirects
- PowerPoint-template moet nog in echte Microsoft PowerPoint worden gevalideerd
- verdere vertaalpolish kan later nog nodig zijn per taal en scherm
- de daadwerkelijke web-exportbestanden van e-learnings blijven file-based en moeten per cursus in de juiste publieke map worden geplaatst
- voor productie moeten Academy-migratie en Academy-seeder ook op live worden uitgevoerd

## Werkafspraken Voor Vervolg

Gebruik voor vervolgwerk deze baseline:
- behoud Laravel/Fortify/Blade-conventies van de huidige codebase
- behandel `Project_Context.md` als functionele startcontext
- ga uit van meertaligheid als vaste systeemkeuze
- houd questionnaires, Academy, rapportage en organisatie-scope centraal
- behoud de bestaande Hermes Results branding
