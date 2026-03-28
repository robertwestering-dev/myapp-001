# Project Context

## Doel Van Dit Bestand

Dit document beschrijft de actuele status van het project en dient als baseline voor vervolgwerk. Het is bedoeld als snel startpunt voor nieuwe features, refactors en verdere uitbouw van het Hermes Results-platform.

Gebruik dit document om direct te begrijpen:
- welke functionele basis er nu staat
- hoe rollen en organisatie-scope werken
- hoe de questionnaire-modules zijn opgebouwd
- welke UI- en technische patronen nu leidend zijn

## Korte Baseline Samenvatting

De applicatie is nu een werkende Laravel 13-app met:
- Fortify-authenticatie
- e-mailverificatie en password reset
- een publieke homepage in Hermes Results-stijl
- een dashboard voor gewone gebruikers
- een admin-portal voor `Admin` en `Beheerder`
- organisatiebeheer
- gebruikersbeheer
- een volledige questionnaire-bibliotheek
- user-invulflows voor questionnaires
- rapportage, statistiek en CSV-exports voor responses

De kern van de huidige bedrijfslogica:
- elke user hoort bij exact één organisatie via `users.org_id`
- data is functioneel gescopeerd op organisatie
- `Admin` beheert alles over alle organisaties heen
- `Beheerder` werkt alleen binnen de eigen organisatie
- questionnaires worden inhoudelijk beheerd door `Admin`
- beschikbaarheid van standaard-questionnaires per organisatie kan door `Admin` en `Beheerder` worden ingesteld binnen hun toegestane scope

Kort samengevat:
de app is niet meer alleen auth + admin-basis, maar een werkend questionnaire-platform met organisatie-scope, invullen door users en rapportage in het admin-portal.

## Huidige Applicatiestatus

De applicatie draait op:
- Laravel 13
- PHP 8.5
- MySQL
- Laravel Fortify voor authenticatie
- Pest voor tests
- Pint voor formatting

De UI bestaat grotendeels uit Blade-views met Hermes Results-branding. Livewire en Flux zitten in de stack, maar de huidige admin- en questionnaire-modules zijn vooral klassiek opgebouwd met controllers, form requests en Blade.

## Huidige Gebruikersflow

### Niet-Ingelogde Bezoekers

Niet-ingelogde bezoekers landen op `/` en zien een publieke homepage in Hermes-stijl.

### Gewone Gebruikers

Een ingelogde gebruiker met rol `User` wordt vanaf `/` doorgestuurd naar `/dashboard`.

Op het dashboard ziet de user:
- eigen accountcontext
- beschikbare questionnaires voor de eigen organisatie
- per questionnaire een link naar het invulscherm
- eventuele reeds opgeslagen response-status

### Admin En Beheerder

Gebruikers met rol `Admin` of `Beheerder` worden vanaf `/` doorgestuurd naar `/admin-portal`.

Belangrijk verschil:
- `Admin` heeft volledig overzicht over alle organisaties, users, questionnaires en responses
- `Beheerder` werkt in het admin-portal alleen binnen de eigen organisatie-scope

## Rollen En Autorisatie

Er zijn nu drie rollen:
- `User`
- `Admin`
- `Beheerder`

Gedrag per rol:
- `User`
  Geen toegang tot het admin-portal.
- `Admin`
  Ziet en beheert alle organisaties, users, questionnaires, beschikbaarheid en responses.
- `Beheerder`
  Heeft toegang tot het admin-portal, maar alleen voor records met dezelfde `org_id` als de eigen user.

Belangrijke huidige scope-regels:
- userlijsten voor `Beheerder` tonen alleen users uit de eigen organisatie
- organisatieoverzicht voor `Beheerder` toont alleen de eigen organisatie
- een `Beheerder` kan geen `Admin`-rol toekennen
- een `Beheerder` kan users niet aan een andere organisatie koppelen
- een `Beheerder` kan geen nieuwe organisaties aanmaken of verwijderen
- een `Beheerder` kan wel de eigen organisatie wijzigen
- een `Beheerder` kan questionnaire-inhoud niet wijzigen
- een `Beheerder` kan wel een bestaande questionnaire beschikbaar stellen voor de eigen organisatie
- responses en statistieken voor `Beheerder` zijn beperkt tot de eigen organisatie

Belangrijke implementatie:
- [app/Models/User.php](/Users/robert/Desktop/MyApp-001/app/Models/User.php)
- [app/Http/Middleware/EnsureUserIsAdmin.php](/Users/robert/Desktop/MyApp-001/app/Http/Middleware/EnsureUserIsAdmin.php)
- [app/Actions/Fortify/LoginResponse.php](/Users/robert/Desktop/MyApp-001/app/Actions/Fortify/LoginResponse.php)

## Organisatie-Model En Datamodel

### Tabel `organizations`

De applicatie bevat een organisatiestructuur in `organizations` met onder andere:
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

De `users`-tabel bevat aanvullend:
- `role`
- `org_id`

`org_id` verwijst naar `organizations.org_id`.

Belangrijke afspraken:
- elke user hoort bij één organisatie
- standaard wordt `Hermes Results` gebruikt als organisatie-default
- bestaande en nieuwe standaard-users worden hieraan gekoppeld als geen andere organisatie wordt gekozen

Belangrijke implementatie:
- [database/migrations/2026_03_28_073541_create_organizations_table.php](/Users/robert/Desktop/MyApp-001/database/migrations/2026_03_28_073541_create_organizations_table.php)
- [database/migrations/2026_03_28_074008_add_org_id_to_users_table.php](/Users/robert/Desktop/MyApp-001/database/migrations/2026_03_28_074008_add_org_id_to_users_table.php)
- [app/Models/Organization.php](/Users/robert/Desktop/MyApp-001/app/Models/Organization.php)
- [app/Models/User.php](/Users/robert/Desktop/MyApp-001/app/Models/User.php)

## Questionnaire-Domein

De questionnaire-module bestaat uit vier lagen:
- questionnaire-bibliotheek
- categorieën en vragen
- beschikbaarheid per organisatie
- user-responses en antwoorden

### Inhoudelijke Bibliotheek

Een questionnaire bestaat uit:
- een questionnaire-record
- meerdere categorieën
- meerdere vragen per categorie

Ondersteunde vraagtypes:
- korte tekst
- lange tekst
- enkele keuze
- meerdere keuzes
- getal
- ja / nee
- datum

Alle inhoudelijke opbouw van questionnaires ligt bij `Admin`. Een `Beheerder` kan categorieën en vragen niet zelf aanpassen.

Belangrijke tabellen:
- `questionnaires`
- `questionnaire_categories`
- `questionnaire_questions`

Belangrijke implementatie:
- [app/Models/Questionnaire.php](/Users/robert/Desktop/MyApp-001/app/Models/Questionnaire.php)
- [app/Models/QuestionnaireCategory.php](/Users/robert/Desktop/MyApp-001/app/Models/QuestionnaireCategory.php)
- [app/Models/QuestionnaireQuestion.php](/Users/robert/Desktop/MyApp-001/app/Models/QuestionnaireQuestion.php)
- [app/Http/Controllers/Admin/QuestionnaireController.php](/Users/robert/Desktop/MyApp-001/app/Http/Controllers/Admin/QuestionnaireController.php)
- [app/Http/Controllers/Admin/QuestionnaireCategoryController.php](/Users/robert/Desktop/MyApp-001/app/Http/Controllers/Admin/QuestionnaireCategoryController.php)
- [app/Http/Controllers/Admin/QuestionnaireQuestionController.php](/Users/robert/Desktop/MyApp-001/app/Http/Controllers/Admin/QuestionnaireQuestionController.php)

### Beschikbaarheid Per Organisatie

Een questionnaire wordt pas bruikbaar voor users nadat deze beschikbaar is gesteld voor een organisatie via `organization_questionnaires`.

Deze laag bepaalt onder meer:
- voor welke organisatie een questionnaire beschikbaar is
- of de questionnaire actief is
- eventuele beschikbaarheidsperiode

Regels:
- `Admin` kan beschikbaarheid voor alle organisaties beheren
- `Beheerder` kan beschikbaarheid voor de eigen organisatie beheren
- `User` kan alleen questionnaires zien die voor de eigen organisatie actief en beschikbaar zijn

Belangrijke implementatie:
- [app/Models/OrganizationQuestionnaire.php](/Users/robert/Desktop/MyApp-001/app/Models/OrganizationQuestionnaire.php)
- [app/Http/Controllers/Admin/OrganizationQuestionnaireController.php](/Users/robert/Desktop/MyApp-001/app/Http/Controllers/Admin/OrganizationQuestionnaireController.php)

### Responses En Antwoorden

Wanneer een user een questionnaire invult, wordt één response per user per organisatie-questionnaire opgeslagen, met losse antwoordregels per vraag.

Belangrijke tabellen:
- `questionnaire_responses`
- `questionnaire_response_answers`

Belangrijke afspraken:
- responses zijn gekoppeld aan `organization_questionnaire_id`
- responses zijn gekoppeld aan `user_id`
- per user is er effectief één actuele response per beschikbare questionnaire
- bij opnieuw invullen wordt de bestaande response bijgewerkt
- validatie gebeurt server-side op basis van het vraagtype

Belangrijke implementatie:
- [app/Models/QuestionnaireResponse.php](/Users/robert/Desktop/MyApp-001/app/Models/QuestionnaireResponse.php)
- [app/Models/QuestionnaireResponseAnswer.php](/Users/robert/Desktop/MyApp-001/app/Models/QuestionnaireResponseAnswer.php)
- [app/Http/Controllers/QuestionnaireResponseController.php](/Users/robert/Desktop/MyApp-001/app/Http/Controllers/QuestionnaireResponseController.php)
- [app/Http/Requests/SubmitQuestionnaireResponseRequest.php](/Users/robert/Desktop/MyApp-001/app/Http/Requests/SubmitQuestionnaireResponseRequest.php)

## Routes En Navigatie

Belangrijkste routes:
- `/`
  Publieke homepage of rolafhankelijke redirect
- `/dashboard`
  Dashboard voor `User`
- `/questionnaires/{organizationQuestionnaire}`
  Invulscherm voor een beschikbare questionnaire
- `/admin-portal`
  Portal voor `Admin` en `Beheerder`
- `/admin-portal/users`
  Users-overzicht
- `/admin-portal/organizations`
  Organisaties-overzicht
- `/admin-portal/questionnaires`
  Questionnaire-bibliotheek
- `/admin-portal/questionnaire-responses`
  Response-overzicht
- `/admin-portal/questionnaire-responses/stats`
  Visuele statistiekpagina

De hoofdroute-definitie staat in:
- [routes/web.php](/Users/robert/Desktop/MyApp-001/routes/web.php)

Het admin-menu bevat nu:
- `Gebruikers`
- `Organisaties`
- `Questionnaires`
- `Responses`

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

## Admin-Module: Questionnairebeheer

De questionnaire-module bevat nu:
- questionnaire-overzicht
- questionnaire aanmaken, wijzigen en verwijderen
- categorie CRUD binnen questionnaires
- vraag CRUD binnen categorieën
- beschikbaarheid CRUD per organisatie

Belangrijke beheerregels:
- alleen `Admin` kan questionnaires, categorieën en vragen inhoudelijk wijzigen
- `Admin` en `Beheerder` kunnen availability beheren binnen hun scope
- organisatie-scope wordt overal gerespecteerd

Belangrijke implementatie:
- [resources/views/admin/questionnaires/index.blade.php](/Users/robert/Desktop/MyApp-001/resources/views/admin/questionnaires/index.blade.php)
- [resources/views/admin/questionnaires/form.blade.php](/Users/robert/Desktop/MyApp-001/resources/views/admin/questionnaires/form.blade.php)
- [resources/views/admin/questionnaire-categories/form.blade.php](/Users/robert/Desktop/MyApp-001/resources/views/admin/questionnaire-categories/form.blade.php)
- [resources/views/admin/questionnaire-questions/form.blade.php](/Users/robert/Desktop/MyApp-001/resources/views/admin/questionnaire-questions/form.blade.php)
- [resources/views/admin/organization-questionnaires/form.blade.php](/Users/robert/Desktop/MyApp-001/resources/views/admin/organization-questionnaires/form.blade.php)

## User-Module: Questionnaire Invullen

Users kunnen questionnaires invullen via:
- het dashboard
- een directe URL naar een beschikbare `organization_questionnaire`

Gedrag:
- alleen questionnaires van de eigen organisatie zijn toegankelijk
- alleen actieve en beschikbare questionnaires zijn zichtbaar
- bestaande antwoorden worden teruggeladen
- antwoorden worden opnieuw opgeslagen bij herinzending

Belangrijke implementatie:
- [resources/views/dashboard.blade.php](/Users/robert/Desktop/MyApp-001/resources/views/dashboard.blade.php)
- [resources/views/questionnaires/show.blade.php](/Users/robert/Desktop/MyApp-001/resources/views/questionnaires/show.blade.php)

## Admin-Module: Responses, Rapportage En Export

De responses-module bevat nu:
- een overzichtspagina met filters op questionnaire, organisatie en gebruiker
- een detailpagina per response
- een visuele statistiekpagina
- CSV-export per antwoordregel
- CSV-export met één rij per user-response
- CSV-export met statistiek per vraag en keuze-optie

Rapportageregels:
- `Admin` ziet responses over alle organisaties
- `Beheerder` ziet alleen responses van de eigen organisatie
- statistieken en exports respecteren dezelfde scope
- voor open vragen toont de visuele statistiekpagina recente antwoorden
- voor keuzevragen toont de visuele statistiekpagina aantallen en percentages per optie

Belangrijke implementatie:
- [app/Http/Controllers/Admin/QuestionnaireResponseReportController.php](/Users/robert/Desktop/MyApp-001/app/Http/Controllers/Admin/QuestionnaireResponseReportController.php)
- [resources/views/admin/questionnaire-responses/index.blade.php](/Users/robert/Desktop/MyApp-001/resources/views/admin/questionnaire-responses/index.blade.php)
- [resources/views/admin/questionnaire-responses/show.blade.php](/Users/robert/Desktop/MyApp-001/resources/views/admin/questionnaire-responses/show.blade.php)
- [resources/views/admin/questionnaire-responses/stats.blade.php](/Users/robert/Desktop/MyApp-001/resources/views/admin/questionnaire-responses/stats.blade.php)

## UI, Branding En Componenten

De belangrijkste publieke, auth-, dashboard-, questionnaire- en admin-pagina's gebruiken momenteel:
- Blade-views
- inline CSS per pagina/layout
- Hermes Results-branding
- gedeelde Hermes-layouts en componenten

Visuele kenmerken:
- warm premium kleurenpalet
- zakelijke uitstraling
- sticky topbar
- consistente Hermes-logo-integratie
- gedeelde header/footer op belangrijke Hermes-pagina's
- favicon en app-icon verwijzen nu centraal naar Hermes Results

Belangrijke componenten en layouts:
- [resources/views/components/layouts/hermes-admin.blade.php](/Users/robert/Desktop/MyApp-001/resources/views/components/layouts/hermes-admin.blade.php)
- [resources/views/components/layouts/hermes-auth.blade.php](/Users/robert/Desktop/MyApp-001/resources/views/components/layouts/hermes-auth.blade.php)
- [resources/views/components/hermes-header.blade.php](/Users/robert/Desktop/MyApp-001/resources/views/components/hermes-header.blade.php)
- [resources/views/components/hermes-footer.blade.php](/Users/robert/Desktop/MyApp-001/resources/views/components/hermes-footer.blade.php)
- [resources/views/components/admin-menu.blade.php](/Users/robert/Desktop/MyApp-001/resources/views/components/admin-menu.blade.php)
- [resources/views/components/hermes-fact.blade.php](/Users/robert/Desktop/MyApp-001/resources/views/components/hermes-fact.blade.php)
- [resources/views/components/favicon-links.blade.php](/Users/robert/Desktop/MyApp-001/resources/views/components/favicon-links.blade.php)

## Mail En Infrastructuur

Mail wordt via SMTP verstuurd met Hermes Results-branding.

Relevante punten:
- forgot-password mails werken
- verificatiemails werken
- `.env` bevat actieve SMTP-configuratie

## Teststatus

Het project gebruikt Pest-featuretests als primaire veiligheidslaag.

Belangrijk:
- auth-redirects en portaltoegang zijn getest
- users-beheer is getest
- organisaties-beheer is getest
- questionnaire-beheer is getest
- availability per organisatie is getest
- questionnaire-invulflow is getest
- response-rapportage en exports zijn getest
- favicon-head-output is getest

Belangrijke testbestanden:
- [tests/Feature/DashboardTest.php](/Users/robert/Desktop/MyApp-001/tests/Feature/DashboardTest.php)
- [tests/Feature/AdminUserManagementTest.php](/Users/robert/Desktop/MyApp-001/tests/Feature/AdminUserManagementTest.php)
- [tests/Feature/OrganizationManagementTest.php](/Users/robert/Desktop/MyApp-001/tests/Feature/OrganizationManagementTest.php)
- [tests/Feature/QuestionnaireManagementTest.php](/Users/robert/Desktop/MyApp-001/tests/Feature/QuestionnaireManagementTest.php)
- [tests/Feature/OrganizationQuestionnaireAvailabilityTest.php](/Users/robert/Desktop/MyApp-001/tests/Feature/OrganizationQuestionnaireAvailabilityTest.php)
- [tests/Feature/QuestionnaireResponseFlowTest.php](/Users/robert/Desktop/MyApp-001/tests/Feature/QuestionnaireResponseFlowTest.php)
- [tests/Feature/QuestionnaireResponseReportTest.php](/Users/robert/Desktop/MyApp-001/tests/Feature/QuestionnaireResponseReportTest.php)

Typische verificatie in deze codebase:
- `php artisan test --compact ...`
- `vendor/bin/pint --dirty --format agent`

## Praktische Baseline Voor Vervolgwerk

Als vervolgwerk op deze baseline ligt het voor de hand om voort te bouwen op:
- verdere analyse of dashboards op questionnaire-responses
- extra vraagtypes of conditional logic
- response-versiegeschiedenis in plaats van één actuele response per user
- exports of rapportage per organisatie of periode
- verdere componentisering van Hermes Blade-views

Belangrijke bestaande conventies:
- volg organisatie-scope consequent via `org_id`
- gebruik bestaande admin-portal patronen met controllers + form requests + Blade
- voeg nieuwe wijzigingen altijd toe met tests
- behoud Hermes Results-visuele stijl tenzij expliciet anders gevraagd
