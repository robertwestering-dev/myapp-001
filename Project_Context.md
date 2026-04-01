# Project Context

## Doel Van Dit Bestand

Dit document is de actuele baseline van het Hermes Results-project. Gebruik dit bestand als startpunt voor vervolgwerk, onboarding, bugfixes, deploys en context-herstel na een pauze.

De inhoud hieronder beschrijft de huidige functionele en technische status zoals die nu in de codebase en de live werkwijze aanwezig is.

## Korte Samenvatting

Hermes Results is een Laravel 13-applicatie voor organisatiegebonden questionnaires, response-opslag, rapportage, CSV-export, een afgeschermde Academy met e-learnings en een publieke blog.

De app bevat nu:
- publieke branded homepage
- publieke blog voor gasten en ingelogde gebruikers
- contactformulier
- Fortify-auth met registratie, login, wachtwoord reset en e-mailverificatie
- dashboard voor gewone gebruikers
- Academy-catalogus voor ingelogde gebruikers
- admin-portal voor `Admin` en `Beheerder`
- admin-portal voor file-based vertaalbeheer van Hermes-teksten
- admin-portal voor blogbeheer door globale admins
- questionnaire-bibliotheek en beschikbaarheid per organisatie
- meerstaps invulflow voor questionnaires
- response-overzicht, statistieken en export
- meertalige interface in `nl`, `en` en `de`

Actuele opvallende status:
- de header gebruikt nu een compact globe-taalmenu, een contactknop met envelop-icoon en een aparte afspraakknop
- de publieke navigatie bevat nu `Diensten`, `Blog`, `Academy` en `Contact`
- de publieke blog gebruikt dezelfde header, footer en look & feel als de homepage
- de blog-header is na correctie weer gelijk aan de homepage-header met een topbar van maximaal `80px`
- de locale-middleware ondersteunt nu ook `?lang=nl` en `?lang=de` als fallback voor forwards naar `.com`
- de questionnaire-flow ondersteunt nu conceptopslag, hervatten via een unieke link en conditionele vervolgvragen
- questionnaire-responses met alleen conceptstatus worden niet meegenomen in admin-rapportages en exports
- de admin-UI rond questionnaire-beschikbaarheid waarschuwt nu expliciet wanneer een questionnaire zelf nog inactief staat in de bibliotheek
- een recente live-check bevestigde dat een organisatiekoppeling alleen zichtbaar wordt op het gebruikersdashboard wanneer zowel de availability als de onderliggende questionnaire zelf actief zijn
- de live deploy op Hostnet gebeurt in de praktijk via bestand-upload met Cyberduck, niet via `git pull` op de server
- een recente blog-livegang bevestigde dat losse kritieke bestanden op live expliciet moeten worden gecontroleerd na upload
- op `2026-04-01` zijn de nieuwe questionnaire-migraties voor conceptopslag en display conditions succesvol op live uitgevoerd

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
- de kern van de publieke site, blog, auth, Academy en questionnaire-flow is momenteel vooral Blade- en controller-gedreven
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

Actuele fallback-oplossing voor forwards:
- de middleware [SetApplicationLocale.php](/Users/robert/Desktop/MyApp-001/app/Http/Middleware/SetApplicationLocale.php) ondersteunt ook een queryparameter `?lang=...`
- hierdoor kunnen forwards naar `.com` alsnog een taal forceren en in de sessie bewaren
- actuele redirect-links:
- `hermesresults.nl` -> `https://hermesresults.com?lang=nl`
- `hermesresults.eu` -> `https://hermesresults.com?lang=de`

Belangrijke caveat:
- de host-gebaseerde taalkeuze blijft de voorkeursroute
- de `?lang=`-oplossing is een fallback voor situaties waarin `.nl` en `.eu` via een `301 redirect` naar `.com` gaan

## Meertaligheid

De applicatie is nu actief voorbereid op drie talen:
- Nederlands
- Engels
- Duits

Historische noot:
- er bestaan nog vertaalbestanden onder `lang/fr`, maar `fr` staat momenteel niet meer in `config('locales.supported')` en wordt dus niet actief getoond of gevalideerd in de huidige locale-flow

Huidige implementatie:
- vertalingen via `lang/{locale}/hermes.php`
- talen switchen via header-dropdown met globe-icoon
- het dropdownmenu toont taalnamen zoals `Nederlands`, `English` en `Deutsch`
- gastvoorkeur wordt in sessie opgeslagen
- ingelogde voorkeur wordt opgeslagen op `users.locale`
- middleware [SetApplicationLocale.php](/Users/robert/Desktop/MyApp-001/app/Http/Middleware/SetApplicationLocale.php) bepaalt de taal in deze volgorde:
- user voorkeur
- queryparameter `lang`
- sessievoorkeur
- host-default
- app-default

Status van vertaalwerk:
- homepage is meertalig gekoppeld
- blog is meertalig gekoppeld
- auth-schermen zijn meertalig gekoppeld
- dashboard, Academy, admin-portal, questionnaire-flow en rapportages zijn meertalig gekoppeld
- de belangrijkste Duitse ontbrekende teksten zijn toegevoegd
- Engels is recent opnieuw gelijkgetrokken met de huidige Nederlandse homepagebasis

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
  heeft volledige toegang over organisaties, users, questionnaires, beschikbaarheid, responses, Academy-beheer en blogbeheer

Technische basis:
- rolwaarden staan in [User.php](/Users/robert/Desktop/MyApp-001/app/Models/User.php)
- admin-portaltoegang loopt via `canAccessAdminPortal()`
- middleware [EnsureUserIsAdmin.php](/Users/robert/Desktop/MyApp-001/app/Http/Middleware/EnsureUserIsAdmin.php) laat `Admin` en `Beheerder` toe
- middleware [EnsureUserIsGlobalAdmin.php](/Users/robert/Desktop/MyApp-001/app/Http/Middleware/EnsureUserIsGlobalAdmin.php) begrenst Academy-beheer, vertaalbeheer en blogbeheer expliciet tot globale `Admin`

Belangrijke afspraak:
- nieuwe registraties krijgen standaard rol `User`

## Gebruikersflow

### Publieke Bezoeker

Niet-ingelogde bezoekers landen op `/` en zien de publieke homepage.

Publieke acties:
- blogoverzicht via `/blog`
- blogdetail via `/blog/{slug}`
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
- blogposts

Extra beperking:
- alleen globale `Admin` ziet en beheert Academy-cursussen in de admin-portal
- alleen globale `Admin` ziet en beheert vertalingen in de admin-portal
- alleen globale `Admin` ziet en beheert blogposts in de admin-portal

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

Blog-scope:
- de blog is publiek toegankelijk voor gasten en ingelogde gebruikers
- de blogdetailpagina gebruikt slug-based routing
- alleen `Admin` mag de tabel `blog_posts` zien en onderhouden

## Blog

De blog-module is nu een publiek domeindeel naast homepage, questionnaires en Academy.

Functionele status:
- overzichtsroute: `/blog`
- detailroute: `/blog/{blogPost:slug}`
- blog is toegankelijk zonder account
- de publieke blog gebruikt dezelfde header, footer, kleurstelling en algemene look & feel als de homepage
- overzichtspagina bevat een featured post, zoekveld, tagfiltering, leestijd en artikelkaarten
- detailpagina toont meertalige content, tags, meta-informatie en gerelateerde artikelen
- de blog gebruikt Markdown-rendering voor artikelcontent

Admin-status:
- alleen globale `Admin` heeft toegang tot blogbeheer
- beheerroute: `/admin-portal/blog-posts`
- globale admins kunnen blogposts aanmaken, wijzigen, verwijderen, publiceren en uitlichten
- blogbeheer is meertalig voor `title`, `excerpt` en `content`

Datamodel:
- `blog_posts`

Belangrijke velden in `blog_posts`:
- `author_id`
- `slug`
- `cover_image_url`
- `tags`
- `title`
- `excerpt`
- `content`
- `is_published`
- `is_featured`
- `published_at`

Belangrijke implementaties:
- [BlogPost.php](/Users/robert/Desktop/MyApp-001/app/Models/BlogPost.php)
- [BlogController.php](/Users/robert/Desktop/MyApp-001/app/Http/Controllers/BlogController.php)
- [BlogPostController.php](/Users/robert/Desktop/MyApp-001/app/Http/Controllers/Admin/BlogPostController.php)
- [BlogPostSeeder.php](/Users/robert/Desktop/MyApp-001/database/seeders/BlogPostSeeder.php)
- [BlogPostFactory.php](/Users/robert/Desktop/MyApp-001/database/factories/BlogPostFactory.php)
- [index.blade.php](/Users/robert/Desktop/MyApp-001/resources/views/blog/index.blade.php)
- [show.blade.php](/Users/robert/Desktop/MyApp-001/resources/views/blog/show.blade.php)
- [hermes-public.blade.php](/Users/robert/Desktop/MyApp-001/resources/views/components/layouts/hermes-public.blade.php)

Belangrijke afspraken:
- de blogseeder gebruikt nu expliciete `BlogPost::query()->create(...)` records en is bewust niet afhankelijk van factories voor live seed-stabiliteit op Hostnet
- de factory is vooral bedoeld voor tests en lokale development

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

## Vertaalbeheer

De applicatie bevat nu een globale adminflow voor beheer van Hermes-teksten.

Functionele status:
- alleen globale `Admin` heeft toegang tot vertaalbeheer
- overzichtsroute: `/admin-portal/translations`
- de lijst toont per regel taal, pagina, element en content
- er zijn filters op taal, pagina en element
- er is een realtime zoekfilter op content
- globale admins kunnen individuele vertaalregels wijzigen vanuit de admin-portal

Belangrijke afspraak:
- vertalingen worden momenteel niet uit de database geladen
- het vertaalbeheer leest en schrijft direct naar `lang/{locale}/hermes.php`

Belangrijke implementaties:
- [ManageHermesTranslations.php](/Users/robert/Desktop/MyApp-001/app/Actions/Translations/ManageHermesTranslations.php)
- [TranslationController.php](/Users/robert/Desktop/MyApp-001/app/Http/Controllers/Admin/TranslationController.php)
- [index.blade.php](/Users/robert/Desktop/MyApp-001/resources/views/admin/translations/index.blade.php)
- [edit.blade.php](/Users/robert/Desktop/MyApp-001/resources/views/admin/translations/edit.blade.php)

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
3. zowel de availability als de bibliotheek-questionnaire zelf moeten actief zijn
4. gebruiker ziet questionnaire op dashboard
5. gebruiker kan tussentijds een concept opslaan
6. gebruiker kan later hervatten via login of via een unieke hervatlink
7. alleen zichtbare en conditioneel actieve vragen worden afgedwongen en opgeslagen
8. definitieve response en answers worden opgeslagen
9. admin-portal toont rapportage en export

## Questionnaire UX-status

De questionnaire-weergave op `/questionnaires/{organizationQuestionnaire}` is aangepast naar een meerstapsflow.

Huidige status:
- invulinstructie staat boven de questionnaire en gebruikt volle breedte
- questionnaire gebruikt volle contentbreedte
- elke categorie is een eigen stap
- gebruiker kan terug naar vorige stap
- gebruiker kan alleen door naar volgende stap wanneer verplichte vragen op de huidige stap zijn ingevuld
- gebruiker kan een concept opslaan zonder de hele questionnaire definitief af te ronden
- bestaande conceptantwoorden blijven zichtbaar na refresh of opnieuw inloggen
- er is een unieke hervatroute voor bestaande conceptresponses
- vervolgvragen kunnen conditioneel zichtbaar worden op basis van eerdere antwoorden
- de tekst `Kies een antwoord` is verwijderd
- header op de questionnairepagina is gelijkgetrokken met de homepage-header

Belangrijke view:
- [show.blade.php](/Users/robert/Desktop/MyApp-001/resources/views/questionnaires/show.blade.php)

Belangrijke recente implementaties:
- [QuestionnaireResponseController.php](/Users/robert/Desktop/MyApp-001/app/Http/Controllers/QuestionnaireResponseController.php)
- [SubmitQuestionnaireResponseRequest.php](/Users/robert/Desktop/MyApp-001/app/Http/Requests/SubmitQuestionnaireResponseRequest.php)
- [QuestionnaireConditionEvaluator.php](/Users/robert/Desktop/MyApp-001/app/Support/Questionnaires/QuestionnaireConditionEvaluator.php)
- [QuestionnaireResponse.php](/Users/robert/Desktop/MyApp-001/app/Models/QuestionnaireResponse.php)
- [QuestionnaireQuestion.php](/Users/robert/Desktop/MyApp-001/app/Models/QuestionnaireQuestion.php)
- [2026_04_01_143722_add_draft_fields_to_questionnaire_responses_table.php](/Users/robert/Desktop/MyApp-001/database/migrations/2026_04_01_143722_add_draft_fields_to_questionnaire_responses_table.php)
- [2026_04_01_143722_add_display_conditions_to_questionnaire_questions_table.php](/Users/robert/Desktop/MyApp-001/database/migrations/2026_04_01_143722_add_display_conditions_to_questionnaire_questions_table.php)

## Routes

Belangrijkste actuele routes:
- `/`
- `/dashboard`
- `/blog`
- `/blog/{blogPost}`
- `/academy`
- `/questionnaires/{organizationQuestionnaire}`
- `/questionnaires/resume/{token}`
- `/admin-portal`
- `/admin-portal/academy-courses`
- `/admin-portal/translations`
- `/admin-portal/blog-posts`
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
- [BlogPostController.php](/Users/robert/Desktop/MyApp-001/app/Http/Controllers/Admin/BlogPostController.php)
- [TranslationController.php](/Users/robert/Desktop/MyApp-001/app/Http/Controllers/Admin/TranslationController.php)
- [UserController.php](/Users/robert/Desktop/MyApp-001/app/Http/Controllers/Admin/UserController.php)
- [OrganizationController.php](/Users/robert/Desktop/MyApp-001/app/Http/Controllers/Admin/OrganizationController.php)
- [QuestionnaireController.php](/Users/robert/Desktop/MyApp-001/app/Http/Controllers/Admin/QuestionnaireController.php)
- [QuestionnaireCategoryController.php](/Users/robert/Desktop/MyApp-001/app/Http/Controllers/Admin/QuestionnaireCategoryController.php)
- [QuestionnaireQuestionController.php](/Users/robert/Desktop/MyApp-001/app/Http/Controllers/Admin/QuestionnaireQuestionController.php)
- [OrganizationQuestionnaireController.php](/Users/robert/Desktop/MyApp-001/app/Http/Controllers/Admin/OrganizationQuestionnaireController.php)
- [QuestionnaireResponseReportController.php](/Users/robert/Desktop/MyApp-001/app/Http/Controllers/Admin/QuestionnaireResponseReportController.php)

Publiek en user:
- [AcademyController.php](/Users/robert/Desktop/MyApp-001/app/Http/Controllers/AcademyController.php)
- [BlogController.php](/Users/robert/Desktop/MyApp-001/app/Http/Controllers/BlogController.php)
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
- draft-responses met `submitted_at = null` worden bewust uitgesloten van response-overzichten en exports

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

Actuele headerstatus:
- de header gebruikt een envelop-icoon dat linkt naar het contactformulier op de homepage
- de taalswitch is opnieuw vormgegeven als globe-menu met hover/focus-dropdown
- contactknop en taalswitch gebruiken dezelfde pill-vorm en hoogte als de login-knop
- de afspraakknop `Plan een afspraak` / `Book a meeting` staat weer als aparte actie in de header
- de homepage-header bevat nu menu-items voor `Diensten`, `Blog`, `Academy` en `Contact`
- de publieke bloglayout is expliciet gelijkgetrokken aan de homepage-header

Er is ook een eerste PowerPoint-templatebestand gegenereerd in:
- [hermes-results-powerpoint-template.pptx](/Users/robert/Desktop/MyApp-001/resources/presentation/hermes-results-powerpoint-template.pptx)

Belangrijke caveat:
- dit bestand is technisch als pakket opgebouwd, maar is nog niet bevestigd als bruikbaar in PowerPoint op een Windows-laptop met Microsoft Office

## Teststatus En Tooling

Actuele lokale status in deze werkmap:
- `vendor/bin/pint` is beschikbaar
- Pest is beschikbaar via `vendor/bin/pest`
- gerichte featuretests zijn recent uitgevoerd voor blog, Academy, locale-gedrag en adminflows
- recente featuretests bevestigen nu ook questionnaire-concepten, conditionele vragen, availability-waarschuwingen en de bestaande questionnaire-flow

Wel aanwezige testbasis:
- featuretests voor registratie, locale-gedrag, questionnaire-flow, dashboard en Academy
- featuretests voor publieke blogroutes en blogbeheer door globale admins
- Academy-tests dekken publiekscatalogus en adminbeheerflow
- locale-tests dekken nu ook `?lang=...` redirects naar `.com`
- er zijn featuretests voor admin-vertaalbeheer
- er zijn featuretests voor questionnaire drafts, hervatten via link en conditionele validatie

Belangrijke testfiles:
- [LocalePreferenceTest.php](/Users/robert/Desktop/MyApp-001/tests/Feature/LocalePreferenceTest.php)
- [AcademyTest.php](/Users/robert/Desktop/MyApp-001/tests/Feature/AcademyTest.php)
- [AcademyAdminManagementTest.php](/Users/robert/Desktop/MyApp-001/tests/Feature/AcademyAdminManagementTest.php)
- [AdminTranslationManagementTest.php](/Users/robert/Desktop/MyApp-001/tests/Feature/AdminTranslationManagementTest.php)
- [BlogTest.php](/Users/robert/Desktop/MyApp-001/tests/Feature/BlogTest.php)
- [BlogAdminManagementTest.php](/Users/robert/Desktop/MyApp-001/tests/Feature/BlogAdminManagementTest.php)
- [QuestionnaireResponseFlowTest.php](/Users/robert/Desktop/MyApp-001/tests/Feature/QuestionnaireResponseFlowTest.php)
- [QuestionnaireDraftAndConditionsTest.php](/Users/robert/Desktop/MyApp-001/tests/Feature/QuestionnaireDraftAndConditionsTest.php)
- [OrganizationQuestionnaireAvailabilityTest.php](/Users/robert/Desktop/MyApp-001/tests/Feature/OrganizationQuestionnaireAvailabilityTest.php)
- [QuestionnaireManagementTest.php](/Users/robert/Desktop/MyApp-001/tests/Feature/QuestionnaireManagementTest.php)

## Openstaande Aandachtspunten

- host-based locale defaults blijven het meest robuust wanneer `.nl` en `.eu` direct naar dezelfde app verwijzen in plaats van via forwards; `?lang=` is nu de fallback-oplossing
- PowerPoint-template moet nog in echte Microsoft PowerPoint worden gevalideerd
- verdere vertaalpolish kan later nog nodig zijn per taal en scherm
- de daadwerkelijke web-exportbestanden van e-learnings blijven file-based en moeten per cursus in de juiste publieke map worden geplaatst
- live deploy op Hostnet vereist extra aandacht voor `lang/`, `bootstrap/app.php`, `public/build`, `database/seeders/` en `database/factories/`, omdat die in de praktijk snel ontbreken bij handmatige uploads
- bij questionnaire-debugging moet altijd worden gecontroleerd of zowel `organization_questionnaires.is_active` als `questionnaires.is_active` op live correct staan

## Deploystatus En Hostinglessen

Actuele praktijk op live:
- de actieve live map op Hostnet is `/webroots/sites/hermesresults.com/hermesresults-app`
- die map is momenteel geen git-repository
- `git pull` op live werkt daar dus niet
- `npm` is niet beschikbaar op de live server
- frontend-assets moeten lokaal worden gebouwd met `npm run build` en daarna handmatig worden geüpload

Praktische deployaanpak:
- upload gewijzigde bestanden en mappen met Cyberduck
- upload bij frontend-wijzigingen ook `public/build`
- controleer na upload kritieke bestanden op live expliciet met `sed`, `grep` of `ls`
- run daarna op live minimaal:
- `composer install --no-dev --optimize-autoloader`
- `php artisan migrate --force`
- `php artisan optimize:clear`
- `php artisan config:cache`
- `php artisan route:cache`
- `php artisan view:cache`

Recente questionnaire-deployles:
- voor de questionnaire-release van `2026-04-01` moesten naast code en views ook twee nieuwe migraties live worden uitgevoerd
- de servercommando's voor die release zijn succesvol uitgevoerd zonder fouten
- na deploy moet in admin expliciet worden gecontroleerd of de relevante bibliotheek-questionnaires op `Actief` staan, anders blijven ze ondanks een organisatiekoppeling onzichtbaar voor gewone gebruikers

Belangrijke les uit recente deploy:
- een ontbrekende `lang/` map leidde tot zichtbare vertaalkeys op live
- een ontbrekende [app.php](/Users/robert/Desktop/MyApp-001/bootstrap/app.php) op live voorkwam dat `SetApplicationLocale` werd geregistreerd, waardoor de taalswitch niet werkte
- een oude versie van [BlogPostSeeder.php](/Users/robert/Desktop/MyApp-001/database/seeders/BlogPostSeeder.php) bleef tijdens een upload op live staan, waardoor de server nog `BlogPost::factory()` gebruikte terwijl lokaal de seeder al was aangepast
- de blog-livegang liet zien dat het verstandig is om seeders voor Hostnet zo min mogelijk afhankelijk te maken van factories
- `SESSION_DRIVER=file` werkt in de huidige Hostnet-omgeving, mits caches na wijziging worden vernieuwd

## Werkafspraken Voor Vervolg

Gebruik voor vervolgwerk deze baseline:
- behoud Laravel/Fortify/Blade-conventies van de huidige codebase
- behandel `Project_Context.md` als functionele startcontext
- ga uit van meertaligheid als vaste systeemkeuze
- houd questionnaires, blog, Academy, rapportage en organisatie-scope centraal
- behoud de bestaande Hermes Results branding
- ga bij live deploys op Hostnet standaard uit van handmatige upload plus server-side verificatie van kritieke bestanden
