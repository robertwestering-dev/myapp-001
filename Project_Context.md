# Project Context

## Doel Van Dit Bestand

Gebruik dit document als actuele baseline van Hermes Results voor vervolgwerk, onboarding, deploys, bugfixes en context-herstel na een pauze.

Deze samenvatting beschrijft de actuele functionele en technische status van de codebase per `2026-04-09`.

## Product In Het Kort

Hermes Results is een Laravel 13-platform voor organisaties en individuele gebruikers met focus op:

- questionnaires per organisatie en taal
- conceptopslag, autosave en hervatten van questionnaires
- admin-rapportage en exports
- een afgeschermde Academy
- een forum voor ingelogde gebruikers
- een publieke blog
- gebruikersprofielen en meertaligheid
- centrale media-assets voor admins

De belangrijkste actieve onderdelen zijn:

- publieke homepage
- publieke pagina `/inspiratiebronnen`
- publieke pagina `/over-ons`
- publieke pagina `/prijzen`
- publieke organisatiepagina op `/voor-organisaties`
- publieke blog
- publieke privacypagina op `/privacy`
- contactformulier
- Fortify-auth met registratie, login, wachtwoord reset en e-mailverificatie
- gebruikersdashboard
- pagina `/vragenlijsten` voor ingelogde gebruikers
- Academy-catalogus voor ingelogde gebruikers
- forum voor ingelogde gebruikers
- centrale profielpagina op `/settings/profile`
- admin-portal voor `Admin` en `Beheerder`
- centrale assetbibliotheek voor globale admins

## Technische Stack

- PHP 8.5
- Laravel 13
- Laravel Fortify
- Livewire 4
- Flux UI 2
- Blade views
- Tailwind CSS 4
- Vite
- Pest
- Pint
- MySQL

Belangrijke technische lijn:

- de meeste hoofdflows zijn server-rendered met controllers en Blade
- Livewire en Flux worden gericht ingezet voor settings, modals en interactieve beheerflows
- dashboard, questionnaires, blog en admin gebruiken gedeelde Hermes UI-componenten

## Code-architectuur Afspraken

Autorisatie:

- de base `Controller` heeft de `AuthorizesRequests` trait — gebruik `$this->authorize()` in controllers
- er zijn Laravel Policies voor `Questionnaire` en `BlogPost` met één methode `manage(User $user): bool`
- policies zijn geregistreerd in `AppServiceProvider` via `Gate::policy()`
- globale admins gebruiken middleware (`EnsureUserIsGlobalAdmin`) op routeniveau voor blog, vertalingen, assets en Academy
- beheerder-scoped acties gebruiken `abort_unless($actor->canManageOrganization(...), 403)`
- gebruik nooit `request()->user()` in controllers — gebruik altijd `$request->user()` via de methode-parameter
- vermijd dubbele autorisatie: als een `FormRequest` de autorisatie al regelt via `authorize()`, roep dan geen `$this->authorize()` aan in de controller — `StoreOrganizationRequest` en `UpdateOrganizationRequest` regelen hun eigen autorisatie volledig; de controllers bevatten geen extra checks

Gedeelde FormRequest-logica:

- `app/Http/Requests/Admin/BaseLocalizedRequest.php` is de abstracte basisklasse voor alle FormRequests met meertalige velden
- `BaseLocalizedRequest` biedt: `localizedStringRules(string $attribute, ?int $maxLength, bool $primaryOnly = true)` — gebruik `$primaryOnly = false` als alle locales verplicht zijn (bijv. Academy-cursussen), de default `true` maakt alleen de primaire locale required
- `app/Http/Requests/Admin/BaseUserRequest.php` erft van `BaseLocalizedRequest` en is de basisklasse voor `StoreUserRequest` en `UpdateUserRequest`
- `BaseUserRequest` biedt: `sharedUserRules()`, `passwordRules()` (gebruikt `Password::defaults()`), `roleOptions()` en `organizationRule()`
- `StoreBlogPostRequest`, `UpdateBlogPostRequest`, `StoreAcademyCourseRequest` en `UpdateAcademyCourseRequest` erven ook van `BaseLocalizedRequest`
- gebruiker-validatie gebruikt `Rule::in()` voor `country` en `role` — voeg geen vrije tekstinvoer toe voor deze velden
- wachtwoordsterkte wordt bepaald door `Password::defaults()` in `AppServiceProvider` (productie: sterke regels + `uncompromised()`)

Admin-vertalingen:

- alle admin-flashberichten staan in `hermes.admin.*` in de lang-bestanden (nl/en/de)
- structuur flashberichten: `hermes.admin.{resource}.{created|updated|deleted}` — bijv. `__('hermes.admin.users.created')`
- structuur form-titels: `hermes.admin.form_titles.{new|edit}_{resource}` — bijv. `__('hermes.admin.form_titles.new_questionnaire')`
- gebruik nooit hardcoded Nederlandse strings in admin-controllers — gebruik altijd `__()` voor zowel flashberichten als form-titels

Gedeelde traits:

- `app/Concerns/NormalizesAnswers.php` — gedeeld door `QuestionnaireResponseController` en `SubmitQuestionnaireResponseRequest`; biedt `isEmptyAnswer()`, `normalizeScalarAnswer()` en `normalizeListAnswer()`

Organisatie-scoping:

- gebruik `Organization::query()->forActor($actor)` voor alle queries die organisaties filteren op rol
- de scope `scopeForActor(Builder $query, User $actor)` op het `Organization`-model beperkt automatisch tot de eigen org als `$actor` geen admin is
- gebruik deze scope in controllers in plaats van handmatige `->when(!$actor->isAdmin(), ...)` constructies

Statistieken en exports:

- `QuestionnaireResponseReportController::buildQuestionStatistics()` accepteert een `Builder` (niet een `Collection`) en verwerkt responses in chunks van 200 — gebruik altijd `->chunk()` voor grote response-datasets
- gebruik `$query->count()` voor aantallen, geef de builder zelf door aan statistiekfuncties
- `filteredResponsesQuery()` heeft een optionele parameter `withDisplayRelations: bool` (standaard `true`) — geef `false` door bij statistiek- en export-stats-aanroepen zodat overbodige eager loads worden overgeslagen

Services:

- `app/Services/SpotlightQuestionnaireService` beheert de spotlight-questionnaires (Adaptability Scan + Quick Scan digitale weerbaarheid)
- gebruik `$service->get(User $actor, withCounts: true)` voor index/portalpagina's
- gebruik `$service->getForFilters()` voor filterkolommen zonder counts
- `app/Support/Questionnaires/AvailableQuestionnaireCatalog` is de centrale bron voor locale-context bepaling — gebruik `$catalog->localeContext(Request $request, User $user)` in controllers; implementeer deze logica nooit opnieuw in een controller
- `app/Services/BlogPostRenderer` verzorgt alle rendering van blogcontent (Markdown, media-shortcodes) — `BlogPost::renderedContentForLocale()` delegeert aan deze service; voeg rendering-logica nooit direct toe aan het model

Configuratie-afspraken:

- `config('app.per_page')` — standaard paginatiegrootte (15), gebruik dit in alle `->paginate()` calls
- `config('app.protected_organization')` — naam van de standaardorganisatie die niet verwijderd mag worden (default: `'Hermes Results'`, te overschrijven via `PROTECTED_ORGANIZATION` in `.env`)
- `config('locales.primary')` — de vereiste locale voor primaire contentvelden (nl), gebruik dit in form requests in plaats van de hard-coded string `'nl'`
- `config('locales.supported')` — alle ondersteunde locales

## Live En Hosting

Bekende productiecontext:

- live domein: `https://hermesresults.com`
- hostingprovider: Hostnet
- database: MySQL

Actieve live paden:

- publieke webroot: `/webroots/sites/hermesresults.com`
- Laravel app-map: `/webroots/sites/hermesresults.com/hermesresults-app`

Belangrijke noot:

- `/home/cl1myceal_u/hermesresults-app` is een oude kopie en niet de actieve live-map

## Deploy Baseline

De live deploy gebeurt via upload, niet via `git pull` op de server.

Standaard werkwijze:

1. Lokaal builden met `npm run build` als Vite-assets zijn gewijzigd.
2. Gewijzigde bestanden uploaden via Cyberduck naar `/webroots/sites/hermesresults.com/hermesresults-app`.
3. Op de server runnen:
   - `composer install --no-dev --optimize-autoloader`
   - `php artisan migrate --force`
   - `php artisan optimize:clear`
   - `php artisan config:cache`
   - `php artisan route:cache`
   - `php artisan view:cache`

Praktische deployregels:

- alleen Blade- of vertaalwijzigingen vragen meestal alleen upload plus `optimize:clear` en `view:cache`
- wijzigingen in `resources/css`, `resources/js` of Vite-afhankelijke UI vragen ook upload van `public/build/`
- `php artisan storage:link` moet aanwezig zijn voor asset-URLs onder `/storage/...`

Gebruik ook `DEPLOY_HANDLEIDING.md` als checklist per wijzigingstype.

## Domeinen En Talen

Beoogde domeinen:

- `hermesresults.com`
- `hermesresults.nl`
- `hermesresults.eu`

Actuele locale-opzet:

- `.com` -> `en`
- `.nl` -> `nl`
- `.eu` -> `de`

Huidige forwarding-praktijk:

- `hermesresults.nl` -> `https://hermesresults.com?lang=nl`
- `hermesresults.eu` -> `https://hermesresults.com?lang=de`

Ondersteunde talen:

- `nl`
- `en`
- `de`

Belangrijke taalafspraak:

- `SetApplicationLocale` ondersteunt ook `?lang=...`, zodat forwards de taal in de sessie kunnen forceren

Vertaalbestanden:

- `lang/nl/hermes.php`
- `lang/en/hermes.php`
- `lang/de/hermes.php`

## Rollen

Er zijn vier rollen:

- `User`
- `user_pro`
- `Beheerder`
- `Admin`

Rolverdeling:

- `User` gebruikt dashboard, questionnaires, Academy, blog en profiel
- `user_pro` gebruikt dezelfde hoofdflows als `User`, maar is bedoeld als aparte abonnementsrol voor Pro-gebruikers
- `Beheerder` heeft admin-toegang binnen eigen organisatie-scope
- `Admin` heeft volledige toegang tot organisaties, users, questionnaires, availability, responses, Academy, blog, assets en vertalingen

Nieuwe registraties krijgen standaard rol `User`.

Admin-aanmaak van gebruikers:

- als een gebruiker via de admin-portal wordt aangemaakt, ontvangt die gebruiker direct een verificatiemail voor het e-mailadres
- een gebruiker die nog als `contactpersoon` aan een organisatie gekoppeld is, kan niet verwijderd worden totdat bij die organisatie eerst een andere contactpersoon is gekozen

## UX En Gedeelde UI

De ingelogde gebruikersomgeving is geharmoniseerd rond één Hermes-stijl.

Actuele UX-status:

- overal hetzelfde hoofdmenu voor ingelogde gebruikers
- consistente heading-, card-, feedback-, CTA- en metadata-patronen
- gedeelde gebruikerscomponenten voor pagina-opbouw, lege staten en actieblokken
- donkere `accent`-kaarten gebruiken witte titels, tekst en metadata voor leesbaarheid

Belangrijke layoutbestanden:

- `resources/views/components/layouts/hermes-dashboard.blade.php`
- `resources/views/components/layouts/hermes-public.blade.php`
- `resources/views/components/layouts/hermes-admin.blade.php`

Belangrijke gedeelde gebruikerscomponenten:

- `resources/views/components/user-page-heading.blade.php`
- `resources/views/components/user-section-heading.blade.php`
- `resources/views/components/user-surface-card.blade.php`
- `resources/views/components/user-stat-tile.blade.php`
- `resources/views/components/user-guidance-card.blade.php`
- `resources/views/components/user-action-row.blade.php`
- `resources/views/components/user-inline-meta.blade.php`
- `resources/views/components/user-feedback.blade.php`

## Dashboard

Het dashboard is een samenvattingsdashboard en niet langer een volledige questionnairelijst.

Het toont:

- Academy samenvatting
- questionnaire samenvatting
- aantal beschikbare questionnaires
- aantal afgeronde questionnaires
- aantal conceptquestionnaires
- duidelijke vervolgstappen richting Academy en vragenlijsten

De oude losse questionnairelijst op dashboard is verwijderd; die flow zit nu op `/vragenlijsten`.

## Vragenlijsten

De questionnairebibliotheek staat op `/vragenlijsten`.

Actuele status van deze pagina:

- de titel `Bibliotheek` staat binnen de buitenste lijstcontainer
- de pagina toont beschikbare questionnaires als kaarten
- per kaart zijn status, conceptbadge en starten- of hervatten-actie zichtbaar
- dashboard en library gebruiken dezelfde cataloguslogica
- de oude regel onder `Bibliotheek` met de actieve taal is verwijderd
- statusmeldingen op deze pagina worden ook gebruikt als veilige fallback na een taalwissel vanaf een questionnaire-detailpagina

Belangrijke functionele afspraken:

- gebruikers zien alleen questionnaires voor hun eigen organisatie
- een questionnaire is alleen zichtbaar als de availability actief is
- de onderliggende questionnaire moet zelf ook actief zijn
- de questionnaire-locale moet passen bij de actieve taal van de gebruiker

Taalfilter voor questionnaires:

- eerst profieltaal van de gebruiker
- als die leeg is: sessietaal

## Questionnaire Flow

De invulflow ondersteunt:

- meerstaps invullen
- autosave
- handmatig concept opslaan
- hervatten van concepten
- conditionele vervolgvragen
- definitief indienen
- questionnaire-specifieke resultaatanalyse na definitieve inzending

Belangrijke statusafspraken:

- een response is `concept` zolang `submitted_at` leeg is
- een response is definitief zodra hij is ingediend
- conceptresponses blijven uitgesloten van admin-exports
- afgeronde responses mogen niet ongemerkt terugvallen naar draft
- na definitieve inzending wordt een `analysis_snapshot` op de response opgeslagen

Belangrijke databasevelden:

- `last_saved_at`
- `resume_token`
- `submitted_at`
- `current_questionnaire_category_id`
- `analysis_snapshot`

UI-afspraken:

- duidelijke stapindicator en voortgangsindicator
- `Vorige`, `Volgende`, `Concept opslaan` en `Indienen`
- feedback bij ontbrekende verplichte velden
- scroll terug naar boven bij stapwissel
- de introkaart op de questionnaire-detailpagina gebruikt nu alleen titel, beschrijving en een gecombineerd feedbackblok
- de instructietekst en autosave-status staan in hetzelfde feedbackblok als één doorlopende paragraaf
- de oude aparte groene kaart `Invulinstructie` is verwijderd
- de informatieve rij met `Organisatie`, `Categorieën`, `Actieve taal` en `Opslaan` is verwijderd uit de detailpagina

Conditionele vragen:

- verborgen vervolgvragen mogen validatie niet blokkeren
- verborgen vervolgvragen mogen niet via oude input blijven hangen
- conditionele logica moet ketenveilig werken

Taalwissel tijdens invullen:

- als een gebruiker via de taalswitch van taal wisselt terwijl hij op een questionnaire-detailpagina zit
- en die questionnaire niet bestaat in de gekozen taal
- dan wordt geen kale `403` meer getoond
- de gebruiker wordt teruggeleid naar `/vragenlijsten` met een statusmelding dat de questionnaire in die taal niet beschikbaar is

## Taalafhankelijke Questionnaires

Questionnaires en questions zijn taalafhankelijk.

Datamodel:

- `questionnaires.locale`
- `questionnaire_questions.locale`

Actuele afspraak:

- per taal kan een eigen questionnaire bestaan

Standaardquestionnaires die nu aanwezig zijn:

- `Adaptability Scan volgens het A.C.E.-model` (`nl`)
- `Adaptability Scan based on the A.C.E. model` (`en`)
- `Quick scan digitale weerbaarheid` (`nl`)
- `Digital resilience quick scan` (`en`)
- `De digitale spiegel` (`nl`)

Deze worden gesynchroniseerd via de bestaande sync-actions en migraties voor gelokaliseerde standaardquestionnaires.

## Questionnaire Availability

Questionnaire-availability is strikt per organisatie gemodelleerd.

Actuele architectuur:

- `organization_questionnaires` bevat alleen concrete koppelingen tussen exact één questionnaire en exact één organisatie
- `org_id = null` wordt niet meer gebruikt als speciale betekenis voor `alle organisaties`
- `alle organisaties` is in de admin geen apart opslagmodel meer, maar een bulkactie die individuele organisatiekoppelingen aanmaakt

Beschikbare availability-velden per koppeling:

- `org_id`
- `available_from`
- `available_until`
- `is_active`

Admin-afspraken:

- admins kunnen één of meerdere organisaties in één keer koppelen
- op de editpagina van een bestaande koppeling wordt alleen die ene koppeling bewerkt
- extra organisaties worden vanuit een apart blok toegevoegd
- op het admin-questionnaire-overzicht wordt availability nu getoond als volledige organisatiematrix, dus gekoppelde en niet-gekoppelde organisaties
- per organisatieregel zijn acties beschikbaar voor wijzigen, verwijderen en activeren/deactiveren

## Questionnaire Analyse

Na een definitieve inzending krijgt een gebruiker een questionnaire-specifieke analyse te zien als daar logica voor is geconfigureerd.

Actuele architectuur:

- `app/Support/Questionnaires/Results/QuestionnaireResultsEngine.php` is de centrale entrypoint
- specifieke analyzers kunnen per questionnaire worden gekoppeld
- responses zonder specifieke analyzer vallen terug op een generieke opgeslagen-bevestiging

Specifieke implementatie die nu aanwezig is:

- `De digitale spiegel` heeft eigen scoringslogica en resultaatduiding
- deze analyse gebruikt omgekeerde items, dimensiescores, totaalscore, profielduiding en aanbevolen vervolgstap

## Questionnaire Import/Export

Er is een lokale JSON import/export-flow voor de questionnaire-bibliotheek.

Beschikbare Artisan-commando's:

- `php artisan questionnaires:export`
- `php artisan questionnaires:import`

De export bevat:

- questionnaire titel
- beschrijving
- taal
- actief/inactief status
- categorieën
- vragen
- antwoordopties
- sortering
- display conditions

De export bevat niet:

- organization-questionnaire availability records
- responses
- rapportages

Belangrijke importafspraken:

- questionnaires worden gematcht op `title + locale`
- categorieën worden gesynchroniseerd op `sort_order`
- vragen worden gesynchroniseerd op `sort_order`
- conditionele logica wordt opnieuw gekoppeld zonder afhankelijkheid van lokale IDs

Zie ook `QUESTIONNAIRE_IMPORT_EXPORT_HANDLEIDING.md`.

## Academy

De Academy is alleen voor ingelogde gebruikers.

Actuele status:

- header en opbouw sluiten aan op de dashboardstijl
- contentblokken gebruiken dezelfde kaart- en metadata-componenten als de rest van de gebruikersomgeving
- donkere highlightblokken volgen dezelfde witte-tekstafspraak als dashboard en blog

## Forum

Het forum is alleen beschikbaar voor ingelogde gebruikers via `/forum`.

Actuele status:

- gebruikers kunnen nieuwe discussies plaatsen
- discussies hebben type `vraag`, `ervaring` of `inzicht`
- gebruikers kunnen tags toevoegen voor vindbaarheid
- andere ingelogde gebruikers kunnen reageren
- overzicht ondersteunt zoeken en filteren op type en tag
- forumcontent ondersteunt eenvoudige Markdown

Belangrijke functionele afspraken:

- het forum gebruikt dezelfde Hermes-dashboardnavigatie als Academy, vragenlijsten en profiel
- discussies en reacties zijn zichtbaar voor alle ingelogde gebruikers
- nieuwe reacties verversen de activiteitssortering van een discussie

## Blog

De blog is publiek toegankelijk.

Actuele functionele status:

- overzichtsroute: `/blog`
- detailroute: `/blog/{blogPost:slug}`
- overzicht bevat filtering en artikelkaarten
- detail toont meertalige content, tags en meta-informatie
- blogcontent wordt in Markdown geschreven
- media-shortcodes worden tijdens renderen omgezet

Belangrijke blogafspraken:

- een blogpost mag opgeslagen worden met alleen Nederlandse inhoud
- Engels en Duits zijn optioneel
- lege of triviale vertalingen vallen publiek terug op Nederlandse content

Renderer-afspraken:

- headings zonder spatie worden genormaliseerd
- `[video ...]` en `[image ...]` shortcodes worden ondersteund
- `align="left|center|right"` werkt voor media

Professionalisering die al is doorgevoerd:

- sterkere meta descriptions
- Open Graph en Twitter cards
- structured data op detailpagina's
- duidelijke SEO-heading op de blog-overzichtspagina in alle ondersteunde talen
- sitemap via aparte controller/view
- gerelateerde artikelen
- admin previewflow

## Homepage

De publieke homepage is SEO-technisch versterkt.

Actuele status:

- mensgerichte positionering voor individuen met focus op weerbaarheid, digitale weerbaarheid, houvast en zelfvertrouwen
- informele Nederlandse toon met consequent `je` in plaats van `u`
- duidelijke CTA naar een aparte publieke organisatiepagina
- locale-afhankelijke `<title>` en meta description
- canonical URL
- Open Graph en Twitter cards
- structured data voor `WebSite` en `Organization`

Zie ook `BLOG_MARKDOWN_HANDLEIDING.md`.

## Profiel

Er is één centrale profielpagina op `/settings/profile`.

De gebruiker kan daar wijzigen:

- naam
- e-mailadres
- voornaam
- geslacht
- geboortedatum
- woonplaats
- land
- voorkeurstaal
- wachtwoord

Actuele profielafspraken:

- `Beveiliging` en `Weergave` zijn verwijderd uit het instellingenmenu
- er is geen aparte security- of appearance-pagina meer
- profiel is de enige overgebleven settings-pagina
- de pagina toont geen extra losse kop boven de hoofdkaart
- de pagina opent vanaf de bovenkant en focust niet automatisch op het naamveld
- e-mailverificatiestatus wordt duidelijk getoond
- de impact van taalvoorkeur wordt expliciet uitgelegd
- profieltaal mag leeg zijn; dan blijft sessietaal leidend

`users`-tabel velden die hierbij relevant zijn:

- `first_name`
- `gender`
- `birth_date`
- `city`
- `country`
- `locale`

Vaste profielwaarden:

- `gender`: `man`, `vrouw`, `anders`
- `country`: `Nederland`, `België`, `Duitsland`, `Frankrijk`, `UK`, `VS`, `Anders`

Gedrag bij profielwijziging:

- als e-mail wijzigt, wordt `email_verified_at` leeggemaakt
- daarna wordt automatisch een nieuwe verificatiemail verstuurd
- de actieve taal wordt ook in de sessie gezet

## Account Verwijderen / Anonimiseren

Accounts worden niet fysiek verwijderd maar geanonimiseerd voor statistiek.

Huidige flow:

- op de profielpagina staat een inline verwijderlink met bevestigings-popup
- na bevestiging wordt de gebruiker uitgelogd
- het account blijft bestaan voor historische response-data

Anonimisatie-afspraak:

- `name` en `first_name` worden vervangen door het `user_id`
- `email` wordt vervangen door `deleted-user+{id}@hermesresults.com`
- `email_verified_at` wordt leeggemaakt
- overige statistisch bruikbare velden blijven behouden

## Admin-Portal

De admin-portal is beschikbaar voor `Admin` en `Beheerder`, met extra onderdelen alleen voor globale admins.

Actuele beheerdelen:

- users
- organisaties
- questionnaires
- availability
- responses
- statistieken
- export
- blogbeheer
- vertaalbeheer
- Academy-beheer
- assetbeheer

Alleen globale `Admin`:

- assetbeheer
- Academy-beheer
- vertaalbeheer
- blogbeheer
- interne strategiepagina's met Nederlandse conceptcopy voor homepage, B2B, pricing en privacy
- interne publieke previewpagina's van die strategiecopy, alleen zichtbaar voor globale admins

Actuele UX-status:

- admin gebruikt een header-menu in Hermes-stijl
- confirm-delete dialogs zijn gecentraliseerd
- lege staten, resultaatmeta, toolbars, filters, feedback, row-actions en statusbadges zijn op meerdere indexpagina's gestandaardiseerd
- het organisatieformulier in admin gebruikt voor `land` een vaste keuzelijst: `Nederland`, `België`, `Duitsland`, `Frankrijk`, `UK`, `VS`, `Anders`
- het organisatieformulier toont bij `contactpersoon` alleen gebruikers met rol `Admin` of `Beheerder`
- het gebruikersoverzicht in admin ondersteunt filters op organisatie, rol en land; deze filters blijven behouden in paginatie en CSV-export

Belangrijke gedeelde admin-componenten:

- `resources/views/components/admin-confirm-delete.blade.php`
- `resources/views/components/admin-empty-state.blade.php`
- `resources/views/components/admin-results-meta.blade.php`
- `resources/views/components/admin-toolbar.blade.php`
- `resources/views/components/admin-toolbar-group.blade.php`
- `resources/views/components/admin-filter-field.blade.php`
- `resources/views/components/admin-filter-actions.blade.php`
- `resources/views/components/admin-feedback.blade.php`
- `resources/views/components/admin-row-actions.blade.php`
- `resources/views/components/admin-status-badge.blade.php`

## Assetbibliotheek

Er is een centrale assetbibliotheek voor globale admins op `/admin-portal/media-assets`.

Huidige functie:

- upload van losse bestanden naar de server
- bedoeld voor afbeeldingen, video's, pdf's en vergelijkbare media
- per asset worden URL, embed-snippet, bestandstype, uploader en bestandsgrootte getoond

Afbakening:

- deze assetbibliotheek is voor losse bestanden
- complete e-learning exports met eigen mapstructuur horen niet in deze flow

## Rapportages En Export

Admin-rapportages ondersteunen explicieter statusinzicht.

Actuele status:

- filters op response-status
- consistente filterdoorvoer tussen index, stats en export
- statusbadges in response-overzichten
- duidelijke lege staten
- detailweergave met fallback als er nog geen opgeslagen antwoorden zijn
- conceptresponses blijven uitgesloten van export
- exports blijven ook bij nul resultaten functioneel

## Bekende Recente Migraties

Belangrijke recente migraties:

- `2026_04_01_143722_add_display_conditions_to_questionnaire_questions_table.php`
- `2026_04_01_143722_add_draft_fields_to_questionnaire_responses_table.php`
- `2026_04_01_174732_add_profile_fields_to_users_table.php`
- `2026_04_02_095134_create_media_assets_table.php`
- `2026_04_02_112910_add_current_questionnaire_category_id_to_questionnaire_responses_table.php`
- `2026_04_02_122232_add_locale_to_questionnaires_table.php`
- `2026_04_02_122232_add_locale_to_questionnaire_questions_table.php`
- `2026_04_02_122232_backfill_questionnaire_locales.php`
- `2026_04_02_123613_make_users_locale_nullable.php`
- `2026_04_02_124220_sync_localized_default_questionnaires.php`
- `2026_04_06_171621_add_locale_index_to_questionnaires_table.php`
- `2026_04_08_182458_add_analysis_snapshot_to_questionnaire_responses_table.php`
- `2026_04_08_185711_make_org_id_nullable_on_organization_questionnaires_table.php`
- `2026_04_09_053803_convert_global_questionnaire_availabilities_to_per_organization_records.php`

Status:

- deze migraties zijn lokaal uitgevoerd
- op live moeten ze expliciet worden meegenomen als dat nog niet is gebeurd

## Belangrijke Routes

Gebruikersroutes:

- `/`
- `/inspiratiebronnen`
- `/dashboard`
- `/vragenlijsten`
- `/academy`
- `/forum`
- `/settings/profile`
- `/blog`

Admin-routes:

- `/admin-portal`
- `/admin-portal/strategie`
- `/admin-portal/strategie/{pagina}/preview`
- `/admin-portal/users`
- `/admin-portal/organizations`
- `/admin-portal/questionnaires`
- `/admin-portal/questionnaire-responses`
- `/admin-portal/academy-courses`
- `/admin-portal/blog-posts`
- `/admin-portal/media-assets`

## Belangrijke Bestanden

Gebruikersomgeving:

- `resources/views/dashboard.blade.php`
- `resources/views/questionnaires/index.blade.php`
- `resources/views/questionnaires/show.blade.php`
- `resources/views/academy/index.blade.php`
- `resources/views/forum/index.blade.php`
- `resources/views/forum/show.blade.php`
- `resources/views/blog/index.blade.php`
- `resources/views/blog/show.blade.php`
- `resources/views/pages/settings/⚡profile.blade.php`

Questionnaire back-end:

- `app/Http/Controllers/DashboardController.php`
- `app/Http/Controllers/LocaleController.php`
- `app/Http/Controllers/QuestionnaireLibraryController.php`
- `app/Http/Controllers/QuestionnaireResponseController.php`
- `app/Http/Controllers/ForumThreadController.php`
- `app/Http/Controllers/ForumReplyController.php`
- `app/Http/Controllers/Admin/OrganizationQuestionnaireController.php`
- `app/Http/Controllers/Admin/QuestionnaireResponseReportController.php`
- `app/Http/Requests/SubmitQuestionnaireResponseRequest.php`
- `app/Http/Requests/Admin/StoreOrganizationQuestionnaireRequest.php`
- `app/Http/Requests/Admin/UpdateOrganizationQuestionnaireRequest.php`
- `app/Support/Questionnaires/AvailableQuestionnaireCatalog.php`
- `app/Support/Questionnaires/QuestionnaireConditionEvaluator.php`
- `app/Support/Questionnaires/Results/QuestionnaireResultsEngine.php`
- `app/Support/Questionnaires/Results/DigitalMirrorQuestionnaireResponseAnalyzer.php`
- `app/Models/Questionnaire.php`
- `app/Models/QuestionnaireQuestion.php`
- `app/Models/QuestionnaireResponse.php`
- `app/Models/OrganizationQuestionnaire.php`
- `app/Models/ForumThread.php`
- `app/Models/ForumReply.php`

Questionnaire import/export:

- `app/Console/Commands/ExportQuestionnairesCommand.php`
- `app/Console/Commands/ImportQuestionnairesCommand.php`
- `app/Support/Questionnaires/QuestionnaireLibraryExporter.php`
- `app/Support/Questionnaires/QuestionnaireLibraryImporter.php`

Admin, blog en assets:

- `resources/views/components/admin-menu.blade.php`
- `resources/views/admin/media-assets/index.blade.php`
- `resources/views/admin/blog-posts/form.blade.php`
- `app/Http/Controllers/BlogSitemapController.php`
- `app/Http/Controllers/Admin/MediaAssetController.php`

Services en autorisatie:

- `app/Services/SpotlightQuestionnaireService.php`
- `app/Policies/QuestionnairePolicy.php`
- `app/Policies/BlogPostPolicy.php`
- `app/Policies/ForumThreadPolicy.php`
- `app/Policies/ForumReplyPolicy.php`

Gedeelde logica:

- `app/Concerns/NormalizesAnswers.php`
- `app/Http/Requests/Admin/BaseUserRequest.php`

## Live Mailstatus

De uitgaande mail op live is werkend bevestigd.

Werkende Hostnet-configuratie:

- `MAIL_MAILER=smtp`
- `MAIL_HOST=mailout.hostnet.nl`
- `MAIL_PORT=587`
- `MAIL_ENCRYPTION=tls`
- `MAIL_USERNAME` moet een echt bij Hostnet gehost e-mailadres zijn
- `MAIL_FROM_ADDRESS` moet ook een echt Hostnet-adres zijn
- `MAIL_FROM_NAME="Hermes Results"`

Praktische afspraken:

- gebruik geen inline comments op dezelfde regel in `.env`
- na wijziging van mailinstellingen op live altijd:
  - `php artisan optimize:clear`
  - `php artisan config:cache`

Bevestigd werkend op live:

- wachtwoord reset
- e-mailverificatie

## Gebruik Van Dit Bestand

Gebruik dit document als startpunt voor vervolgopdrachten, tenzij nieuwe code of nieuwe deploys aantoonbaar actuelere informatie geven.

Bij functionele wijzigingen moet `Project_Context.md` mee worden bijgewerkt, zodat dit bestand bruikbaar blijft als vaste baseline.
