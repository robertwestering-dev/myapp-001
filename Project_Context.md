# Project Context

## Doel Van Dit Bestand

Gebruik dit document als actuele baseline van Hermes Results voor vervolgwerk, onboarding, deploys, bugfixes en context-herstel na een pauze.

Deze samenvatting beschrijft de actuele functionele en technische status van de codebase per `2026-04-19` (sessie 3), aangevuld met sessie-updates t/m `2026-04-23`.

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

Beveiliging:

- `app/Http/Middleware/AddSecurityHeaders.php` voegt op elke response een per-request-nonce toe en stuurt: `X-Frame-Options`, `X-Content-Type-Options`, `Referrer-Policy`, `Permissions-Policy` en een strikte `Content-Security-Policy` — geregistreerd als globale web-middleware in `bootstrap/app.php`
- CSP-policy: `default-src 'self'`, `script-src 'nonce-{nonce}' 'strict-dynamic' 'unsafe-eval'`, `style-src 'self' 'unsafe-inline' https://fonts.bunny.net`, `font-src 'self' https://fonts.bunny.net`, `img-src 'self' data: https:`, `connect-src 'self'`, `base-uri 'self'`, `form-action 'self'`, `frame-ancestors 'self'`
- `'unsafe-eval'` is vereist omdat Livewire v4 intern `new Function()` gebruikt voor `wire:click`/`wire:model` expressie-evaluatie — dit is een bekende beperking; de nonce-bescherming (tegen externe scriptinjectie en inline scripts) blijft volledig intact
- `Vite::useCspNonce($nonce)` wordt vóór `$next($request)` aangeroepen — Livewire v4 leest de nonce automatisch via `Vite::cspNonce()`
- `config/livewire.php` heeft `csp_safe => true` — Livewire gebruikt de CSP-veilige Alpine-bundle zonder inline scripts
- routes `/forum`, `/vragenlijsten`, `/academy` en alle questionnaire-routes vereisen `verified` middleware naast `auth` — onverifieerde gebruikers kunnen deze flows niet bereiken
- route `/pro-upgrade` vereist `auth`; bezoekers worden naar login gestuurd en kunnen de tijdelijke Pro-upgradepagina niet publiek bekijken
- `/contact` is gelimiteerd op `throttle:5,1`, `/locale` op `throttle:30,1`
- SVG-uploads zijn geblokkeerd in `StoreMediaAssetRequest` — toegestane types: jpg, jpeg, png, webp, gif, mp4, mov, webm, ogg, pdf
- `User::anonymizeForStatistics()` verwijdert actieve database-sessies van de gebruiker direct na anonimisering
- sessieversleuteling staat aan (`SESSION_ENCRYPT=true`) en cookies worden alleen via HTTPS verstuurd (`SESSION_SECURE_COOKIE=true` op productie)
- `BlogPostRenderer::normalizeCssDimension()` accepteert alleen veilige CSS-eenheden (px, em, rem, %, vw, vh, e.d.) — onbekende waarden vallen terug op `100%`

2FA-handhaving:

- `app/Http/Middleware/EnsureTwoFactorEnabled.php` blokkeert toegang tot het admin-portal als `two_factor_confirmed_at` leeg is — admins en beheerders worden doorgestuurd naar `admin.two-factor.notice`
- de noticepagina staat buiten de 2FA-gate en verwijst door naar `/settings/profile`
- op de profielpagina kunnen `Admin` en `Beheerder` 2FA in- en uitschakelen via Fortify-actions (`EnableTwoFactorAuthentication`, `ConfirmTwoFactorAuthentication`, `DisableTwoFactorAuthentication`)
- `UserFactory::admin()` en `UserFactory::manager()` bevatten standaard `withTwoFactor()` zodat tests niet worden geblokkeerd door de middleware
- admins die 2FA hebben ingesteld, worden na login omgeleid naar `/two-factor-challenge` (Fortify-flow) in plaats van direct naar het portal
- na het succesvol doorlopen van de 2FA-challenge worden admins en beheerders doorgestuurd naar `admin.portal` via een custom `TwoFactorLoginResponse` (`app/Actions/Fortify/TwoFactorLoginResponse.php`) — gewone gebruikers gaan naar `dashboard`
- het 2FA-challenge-invoerveld is één enkel tekstveld (niet het split-digit `flux:otp` component) voor maximale bruikbaarheid

Auditlog:

- `app/Models/AdminActivityLog.php` + tabel `admin_activity_logs` — slaat op: `user_id`, `action`, `subject_type`, `subject_id`, `description`, `ip_address`, `timestamps`
- `app/Services/AuditLogger.php` — injecteerbare service die automatisch de ingelogde gebruiker en het IP-adres registreert
- `UserController` logt `user.created`, `user.updated`, `user.deleted`
- `OrganizationController` logt `organization.created`, `organization.updated`, `organization.deleted`
- auditlogoverzicht beschikbaar op `/admin-portal/audit-logs` — alleen voor globale `Admin`, ondersteunt filteren op actie en omschrijving

Forummeldingen:

- `app/Notifications/ForumReplyPosted.php` — ShouldQueue-notificatie die de thread-auteur een e-mail stuurt bij een nieuwe reactie van een andere gebruiker
- `ForumReplyController::store()` verstuurt de melding na het aanmaken van de reactie, mits de reageerder ≠ de thread-auteur
- trilinguale mailteksten in `lang/nl/hermes.php`, `lang/en/hermes.php` en `lang/de/hermes.php` onder `notifications.forum_reply`

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
- `AvailableQuestionnaireCatalog::forUser()` zet twee virtuele relaties op elk `OrganizationQuestionnaire`-object: `currentResponse` (huidig concept of anders de laatste definitieve inzending) en `completedResponses` (Collection van definitieve inzendingen gesorteerd op `submitted_at` desc) — deze worden via `setRelation()` ingesteld en zijn geen echte Eloquent-relaties
- `app/Services/BlogPostRenderer` verzorgt alle rendering van blogcontent (Markdown, media-shortcodes) — `BlogPost::renderedContentForLocale()` delegeert aan deze service; voeg rendering-logica nooit direct toe aan het model
- `app/Services/SuccessfulLoginSummary` registreert na een succesvolle login de vorige loginmoment en de meest recente definitieve questionnaire-inzending in de sessie (sleutel: `SuccessfulLoginSummary::SESSION_KEY`) en werkt `users.last_login_at` bij — wordt aangeroepen vanuit zowel `LoginResponse` als `TwoFactorLoginResponse` zodat het dashboard de eenmalige loginpopup kan tonen

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
- productie-assets die via `/images/...` worden geladen, staan bij Hostnet in de map `images` naast `hermesresults-app`, niet in `hermesresults-app/public/images`; de homepage gebruikt bijvoorbeeld `/images/6lagen-model.png`

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
- na elke deploy `composer install --no-dev --optimize-autoloader` draaien zodat dev-packages (zoals `laravel/boost`) uit de autoload-map worden verwijderd — anders geeft de server een `BoostServiceProvider not found` error
- als de server een `Class "translator" does not exist` error gooit, is de oorzaak altijd een corrupte of verouderde bootstrap-cache; fix: `rm -f bootstrap/cache/*.php && php artisan optimize:clear && php artisan config:cache && php artisan route:cache && php artisan view:cache`

Gebruik ook `DEPLOY_HANDLEIDING.md` als checklist per wijzigingstype.

## Domeinen En Talen

Beoogde domeinen:

- `hermesresults.com`
- `hermesresults.nl`
- `hermesresults.eu`

Actuele locale-opzet:

- `.com` -> `nl`
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
- `lang/fr/hermes.php` — voorbereidend bestand voor een toekomstige Franse locale; `fr` is nog **niet** opgenomen in `config/locales.supported` en dus nog niet actief in de taalswitch

Belangrijke vertaalafspraak:

- bij elke tekstwijziging in een van de vertaalbestanden moeten de corresponderende keys in **alle drie de actieve taalbestanden** (nl, en, de) direct worden bijgewerkt; werk ook `fr` bij als de key daar aanwezig is of als de hele sectie daar voorbereid wordt
- de Nederlandse tekst is altijd leidend; Engels, Duits en waar aanwezig het voorbereidende Frans worden daarop afgestemd
- dit geldt ook voor volgorde-wijzigingen in lijsten (bijv. `features`-arrays) en het toevoegen of verwijderen van keys

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
- het gedeelde hamburgermenu blijft op smartphone naast het logo staan; het mobiele menu heeft een eigen scrollgebied zodat de pagina achter het menu niet meescrollt
- de headerknop `Maak een afspraak` wordt alleen op de contactpagina getoond; op alle andere publieke en ingelogde pagina's staat deze knop niet in de header
- de logoutknop voor ingelogde gebruikers gebruikt in alle headers de neutrale grijze `pill--neutral` variant
- publieke navigatielinks naar Contact openen `/contact` zonder `#contact`, zodat de pagina bovenaan start

Belangrijke layoutbestanden:

- `resources/views/components/hermes-header.blade.php`
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

- questionnaire samenvatting
- Academy samenvatting
- aantal beschikbare questionnaires
- aantal lopende questionnaires
- aantal afgeronde questionnaires
- aantal beschikbare Academy-items
- aantal lopende Academy-items
- aantal afgeronde Academy-items

Actuele dashboardstatus:

- het eerste hoofdblok is `Questionnaires`, daarna volgt `Academy`
- het blok `Questionnaires` toont alleen titel, korte introductietekst, drie statustegels (`Beschikbaar`, `Lopend`, `Afgerond`) en een actieknop
- de extra dashboardtekst over concepten en de regel met de actieve questionnairetaal zijn verwijderd uit het blok `Questionnaires`
- het blok `Academy` toont titel, korte introductietekst, drie statustegels (`Beschikbaar`, `Lopend`, `Afgerond`) en een actieknop
- de Academy-tegels hebben exact dezelfde hoogte als de Questionnaires-tegels
- de oude sectie `Volgende stap` met guidance-kaarten is volledig verwijderd
- na elke succesvolle login van een gewone gebruiker wordt op het dashboard een eenmalige popup getoond met naam, vorige login en de laatst ingevulde zelftest
- `users.last_login_at` bewaart de meest recente succesvolle login; de popup gebruikt de vorige waarde voordat deze wordt bijgewerkt
- de laatste zelftest in de popup is de nieuwste definitieve `QuestionnaireResponse` van de gebruiker
- als de laatste zelftest op of vóór de datum van vandaag min drie maanden is afgerond, toont de popup een oproep om die zelftest opnieuw te doen en voortgang te volgen

De oude losse questionnairelijst op dashboard is verwijderd; die flow zit nu op `/vragenlijsten`.

## Vragenlijsten

De questionnairebibliotheek staat op `/vragenlijsten`.

Actuele status van deze pagina:

- de titel `Bibliotheek` staat binnen de buitenste lijstcontainer
- de pagina toont beschikbare questionnaires als kaarten
- per kaart zijn status, conceptbadge en starten- of hervatten-actie zichtbaar
- per kaart worden eerdere definitieve inzendingen getoond met datum/tijd en een link naar de resultaten van die specifieke inzending
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
- meerdere definitieve inzendingen per gebruiker per questionnaire
- resultaten en analyse bekijken per afzonderlijke inzending
- conditionele vervolgvragen
- definitief indienen
- questionnaire-specifieke resultaatanalyse na definitieve inzending

Belangrijke statusafspraken:

- een response is `concept` zolang `submitted_at` leeg is
- een response is definitief zodra hij is ingediend
- per gebruiker en organization-questionnaire mag meer dan één definitieve response bestaan
- gewone gebruikers met rol `User` mogen een questionnaire één keer definitief invullen
- gebruikers met rol `user_pro` mogen dezelfde questionnaire meerdere keren definitief invullen
- als een gewone `User` een al afgeronde questionnaire opnieuw wil openen, wordt hij teruggestuurd naar `/vragenlijsten` met een sluitbare PRO-popup
- de invulpagina gebruikt alleen de nieuwste conceptresponse; afgeronde responses openen via de aparte resultatenroute
- conceptresponses blijven uitgesloten van admin-exports
- afgeronde responses mogen niet ongemerkt terugvallen naar draft
- na definitieve inzending wordt een `analysis_snapshot` op de response opgeslagen
- definitieve inzendingen worden niet meer heropend als invulformulier; de gebruiker wordt doorgestuurd naar de resultaatpagina van die specifieke response

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
- `Positief fundament` (`nl`)

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
- `Positief fundament` heeft eigen PERMA-scoringslogica en resultaatduiding
- deze analyse berekent vijf pijlerscores (`P`, `E`, `R`, `M`, `A`), een totaalscore (`20-100`), een overall profiel en een prioritaire vervolgstap op basis van de laagste pijlerscore
- bij gelijke laagste pijlerscores geldt prioriteit `P > E > M > R > A`
- gratis `User` ziet alleen overall score, overall profiel en een globale CTA
- `user_pro` ziet daarnaast alle vijf pijlerscores, pijlerbadges, adviesteksten en de aanbevolen startpijler

## Questionnaire Import/Export

Er is een lokale JSON import/export-flow voor de questionnaire-bibliotheek.

Beschikbare Artisan-commando's:

- `php artisan questionnaires:export`
- `php artisan questionnaires:import` — standaard additive import; questionnaires die niet in het importbestand staan blijven behouden
- `php artisan questionnaires:import --prune` — synchroniseert de volledige bibliotheek: questionnaires die niet in het importbestand staan worden verwijderd; vereist minimaal één questionnaire in het importbestand

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
- de thread-auteur ontvangt een e-mailmelding (`ForumReplyPosted`) als iemand anders reageert — notificatie wordt asynchroon verstuurd via de queue

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
- blog tagcounts worden als simpele array gecachet en bij een ongeldige cachewaarde automatisch opnieuw opgebouwd

## Homepage

De publieke homepage is SEO-technisch versterkt.

Actuele status:

- mensgerichte positionering voor individuen met focus op weerbaarheid, digitale weerbaarheid, houvast en zelfvertrouwen
- informele Nederlandse toon met consequent `je` in plaats van `u`
- de Nederlandse homepagecopy is de bron voor vertalingen in `lang/en/hermes.php`, `lang/de/hermes.php` en het voorbereidende `lang/fr/hermes.php`
- de homepage gebruikt de `home_people`-vertalingen voor de hoofdpagina en toont het Robert-verhaal via `about_page.story_*` en `about_page.mission_*`
- blokvolgorde homepage: hero, `Herkenbaar`, `Ik help je op weg`, `Aanpak`, diensten/stappenplan, resultaat/vertrouwen, inspiratie en CTA
- het blok `Ik help je op weg` bevat de kaarten `Ik ben Robert van Westering` en `Dit wil ik bijdragen`
- het blok `Aanpak` toont links `/images/6lagen-model.png` en rechts de tekst `Zo werkt het` met de uitleg van het zeslagenmodel
- duidelijke CTA naar een aparte publieke organisatiepagina
- locale-afhankelijke `<title>` en meta description
- canonical URL
- Open Graph en Twitter cards
- structured data voor `WebSite` en `Organization`

Zie ook `BLOG_MARKDOWN_HANDLEIDING.md`.

## Voor Organisaties

De publieke organisatiepagina is bereikbaar via `/voor-organisaties`.

Actuele positionering:

- kernboodschap: het succes van digitale oplossingen staat of valt met je mensen
- de pagina richt zich op organisaties die willen begrijpen waar medewerkers afhaken bij digitale tools, werkwijzen en transformaties
- de Nederlandse copy is leidend; Engels, Duits en Frans zijn afgestemd op deze Nederlandse versie
- de view gebruikt vooral keys uit `hermes.home.*` voor de zichtbare organisatiepagina-opbouw en `hermes.organizations_page.*` voor SEO, structured data en legacy/ondersteunende paginadata

Actuele pagina-opbouw:

- hero met drie proof-blokjes: `Quick scans`, `Stappenplan`, `Inzicht & overzicht`
- zijblok `Over mij` met Robert-positionering, drie bullets en een korte technologie/mens-boodschap
- probleemblok met externe, interne en filosofische context
- waarschuwingsblok `Als je niets doet`
- aanbodblok met twee stappen: eerst meten, dan begeleiden
- stappenplan van scan naar resultaat
- resultaatblok met concrete opbrengsten
- CTA/contactblok met hetzelfde contactformulier als `/contact`

Belangrijke copy-afspraken:

- gebruik niet meer de oude insteek `Digital transformation; measurable and human` / `Digitale Transformation; messbar und menschlich`
- vermijd leeftijdslabeling zoals `vijftigers` in nieuwe organisatiecopy; gebruik neutraler `medewerkers` of `ervaren medewerkers`
- Robert wordt op deze pagina gepositioneerd rond 35 jaar ervaring, Benefits Realization en mensgerichte digitale verandering

## Contact

De contactpagina is publiek toegankelijk via `/contact`.

Actuele status:

- de pagina gebruikt de publieke Hermes-homepagestijl
- de pagina staat in de publieke navigatie voor bezoekers
- de pagina toont het contactformulier uit het `Call to action`-blok van `/voor-organisaties`; de linker CTA-tekst en Calendly-knop zijn op de contactpagina verwijderd
- het contactformulier gebruikt dezelfde `POST /contact` flow als het bestaande organisatieformulier
- na versturen keert de gebruiker terug naar de pagina waar het formulier stond, met fragment `#contact`

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
- twee-factor-authenticatie (alleen zichtbaar voor `Admin` en `Beheerder`)

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

Verificatiemail opnieuw sturen:

- de knop "Klik hier om de verificatiemail opnieuw te sturen" is een `wire:click="resendVerificationEmail"` knop, geen losse form POST
- gebruik altijd `wire:click` voor acties binnen Livewire-components — een `<form method="POST">` binnen een Livewire-component wordt door Livewire onderschept en bereikt de server niet als HTTP-request

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
- auditlog

Alleen globale `Admin`:

- assetbeheer
- Academy-beheer
- vertaalbeheer
- blogbeheer
- auditlog
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
- `2026_04_11_065853_create_admin_activity_logs_table.php`
- `2026_04_09_111411_add_positive_foundation_questionnaire.php`
- `2026_04_11_065853_create_admin_activity_logs_table.php`
- `2026_04_19_141151_allow_multiple_questionnaire_responses_per_user.php`
- `2026_04_19_150511_add_last_login_at_to_users_table.php`

Status:

- deze migraties zijn lokaal uitgevoerd
- op live moeten ze expliciet worden meegenomen als dat nog niet is gebeurd

## Belangrijke Routes

Gebruikersroutes:

- `/`
- `/inspiratiebronnen`
- `/contact`
- `/dashboard`
- `/vragenlijsten`
- `/questionnaires/results/{response}`
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
- `/admin-portal/audit-logs`
- `/admin-portal/two-factor-notice`

## Belangrijke Bestanden

Gebruikersomgeving:

- `resources/views/contact.blade.php`
- `resources/views/dashboard.blade.php`
- `resources/views/questionnaires/index.blade.php`
- `resources/views/questionnaires/show.blade.php`
- `resources/views/questionnaires/results.blade.php`
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
- `app/Support/Questionnaires/Results/PositiveFoundationQuestionnaireResponseAnalyzer.php`
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
- `app/Services/AuditLogger.php`
- `app/Services/SuccessfulLoginSummary.php`
- `app/Actions/Fortify/LoginResponse.php`
- `app/Actions/Fortify/TwoFactorLoginResponse.php`
- `app/Policies/QuestionnairePolicy.php`
- `app/Policies/BlogPostPolicy.php`
- `app/Policies/ForumThreadPolicy.php`
- `app/Policies/ForumReplyPolicy.php`

Notificaties:

- `app/Notifications/ForumReplyPosted.php`

Middleware:

- `app/Http/Middleware/AddSecurityHeaders.php`
- `app/Http/Middleware/EnsureTwoFactorEnabled.php`
- `app/Http/Middleware/EnsureUserIsAdmin.php`
- `app/Http/Middleware/EnsureUserIsGlobalAdmin.php`

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

## Sessie-update 2026-04-21

Deze update vult de baseline van `2026-04-19` aan met de belangrijkste wijzigingen uit de huidige sessie.

Questionnaire-lokalisatie:

- questionnaires moeten conceptueel één canoniek record blijven, ook als ze in meerdere talen ingevuld kunnen worden
- taalvarianten worden niet meer opgelost door aparte questionnaire-records per taal aan te maken
- `app/Support/Questionnaires/LocalizedQuestionnaireContent.php` past titel, beschrijving, categorienamen en zichtbare vragen toe op basis van de actieve locale
- `AvailableQuestionnaireCatalog`, `QuestionnaireResponseController` en `SubmitQuestionnaireResponseRequest` gebruiken de locale-context zodat overzicht, invulpagina, validatie en opslag dezelfde taalset gebruiken
- vragen kunnen een eigen `locale` hebben; per categorie en `sort_order` wordt de vraag voor de actieve locale gekozen
- import/export neemt vraag-locale en locale-aware display conditions mee

Canonieke meertalige questionnaires:

- `Digitale spiegel` bestaat als één canonieke questionnaire met Nederlandse, Engelse en Duitse vragen en antwoordopties
- `Positief fundament` bestaat als één canonieke questionnaire met Nederlandse, Engelse en Duitse titel, beschrijving, vragen en antwoordopties
- `Positief fundament` gebruikt in de questionnaire-beschrijving niet langer de prefix `PERMA-`; alleen de beschrijving is aangepast, resultaat- en moduleteksten met `PERMA` zijn ongemoeid gelaten
- `Adaptability Scan` en `Quick Scan digitale weerbaarheid` zijn ook naar het canonieke meertalige patroon gebracht met locale-specifieke vragen
- `database/migrations/2026_04_21_060214_resync_canonical_multilingual_questionnaires.php` is een forward-only data repair migration die de canonieke meertalige questionnaires opnieuw synchroniseert
- bij deploy moet `php artisan migrate --force` deze datasynchronisatie uitvoeren; lokaal kunnen de specifieke seeders worden gedraaid om dezelfde data te herstellen

PRO-flow en profielpagina:

- nieuwe afgeschermde pagina `/pro-upgrade` met named route `pro-upgrade.show`
- `GET /pro-upgrade` en `POST /pro-upgrade` zijn beschermd met `auth`
- `POST /pro-upgrade` gebruikt `App\Http\Controllers\ProUpgradeController`
- als een ingelogde gebruiker met rol `User` op `Start met Pro` klikt, wordt de rol gewijzigd naar `user_pro`
- rollen `user_pro`, `Beheerder` en `Admin` worden door de upgrade-actie niet gewijzigd
- op `/settings/profile` is het taalvoorkeur-infoblok vervangen door een PRO-blok
- op `/settings/profile` ziet rol `User` de titel `Upgrade jezelf naar PRO` met CTA `Ik wil Pro worden`
- op `/settings/profile` zien andere rollen de titel `Je bent een Pro` zonder CTA
- de profielpagina toont niet langer de hoofdtitel `Persoonlijke gegevens`, de bijbehorende intro, de badge `Wordt een pro` of de verificatiebadge `E-mail geverifieerd`
- het taalkeuzeveld in het profielformulier is blijven bestaan

Admin en gebruikersoverzicht:

- op `/admin-portal/users` is de kolom met e-mailadres vervangen door de naam van de organisatie
- de users-query eager-loadt de organisatie zodat de lijst geen extra queries per rij nodig heeft

Belangrijke nieuwe/gewijzigde bestanden:

- `app/Support/Questionnaires/LocalizedQuestionnaireContent.php`
- `app/Actions/Questionnaires/SyncDigitalMirrorQuestionnaire.php`
- `app/Actions/Questionnaires/SyncPositiveFoundationQuestionnaire.php`
- `app/Actions/Questionnaires/SyncAdaptabilityAceQuestionnaire.php`
- `app/Actions/Questionnaires/SyncDigitalResilienceQuickScanQuestionnaire.php`
- `database/migrations/2026_04_21_060214_resync_canonical_multilingual_questionnaires.php`
- `app/Http/Controllers/ProUpgradeController.php`
- `resources/views/pro-upgrade.blade.php`
- `resources/views/pages/settings/⚡profile.blade.php`
- `resources/views/admin/users/index.blade.php`
- `tests/Feature/DigitalMirrorQuestionnaireTest.php`
- `tests/Feature/PositiveFoundationQuestionnaireTest.php`
- `tests/Feature/PricingPageTest.php`
- `tests/Feature/Settings/ProfileUpdateTest.php`

Teststatus uit deze sessie:

- gerichte questionnaire-tests voor `Digitale spiegel`, `Positief fundament`, questionnaire library en response flow zijn groen
- profiel- en PRO-upgrade tests zijn groen
- volledige testsuite is niet opnieuw volledig groen bevestigd in deze sessie; eerder waren er twee unrelated full-suite failures rond contactlinkverwachtingen in publieke pagina-tests

## Sessie-update 2026-04-22

Profiel-volledigheidscheck bij inloggen:

- na een succesvolle login controleert `LoginResponse` (en `TwoFactorLoginResponse`) of het profiel volledig is ingevuld
- als dat niet het geval is én de gebruiker geen admin/beheerder is, wordt de gebruiker direct doorgestuurd naar `/settings/profile` (in plaats van het dashboard)
- er wordt een session flash `profile_incomplete_prompt` gezet zodat de profielpagina de melding kan tonen
- `User::isProfileComplete()` controleert of `first_name`, `gender`, `birth_date`, `city` en `country` allemaal niet leeg zijn — gebruikt `empty()` zodat ook lege strings (`""`) als onvolledig worden beschouwd
- de `x-user-info-card` component heeft een optionele `:prompt` prop gekregen die onderaan de kaart een oranje tekst toont (`color: #d96a2b`, de `--accent` kleur van de CTAs)
- op de profielpagina: `showProfilePrompt` public property, gezet in `mount()` via `session()->has('profile_incomplete_prompt')`
- het bericht "Vul hieronder je profiel volledig in. Alvast dank!" verschijnt onderaan het Verificatiestatus-blok, vetgedrukt in de CTA-accentkleur
- `UserFactory::incompleteProfile()` state toegevoegd voor gebruik in tests
- 9 nieuwe tests in `tests/Feature/Auth/LoginRedirectTest.php`

Gewijzigde bestanden:

- `app/Models/User.php` — `isProfileComplete()` methode toegevoegd
- `app/Actions/Fortify/LoginResponse.php` — redirect naar profiel als onvolledig
- `app/Actions/Fortify/TwoFactorLoginResponse.php` — idem
- `resources/views/components/user-info-card.blade.php` — optionele `$prompt` prop
- `resources/views/components/layouts/hermes-dashboard.blade.php` — CSS klasse `.user-info-card__prompt`
- `resources/views/pages/settings/⚡profile.blade.php` — `showProfilePrompt` property + prompt doorgeven
- `database/factories/UserFactory.php` — `incompleteProfile()` state
- `tests/Feature/Auth/LoginRedirectTest.php` — nieuw testbestand

Publieke pagina-copy en tijdelijke Pro-aanbieding:

- `/prijzen` toont bij het Pro-pakket de doorgestreepte prijs `€98 per jaar` met label `Tijdelijk GRATIS`
- de Pro-tagline op `/prijzen` is `Voor wie echt wil groeien`
- de hero-intro op `/prijzen` bevat de gecorrigeerde tekst `helpen je op jouw persoonlijke weg`
- `/pro-upgrade` gebruikt de tekst `Activeer je Pro-toegang...` zonder `straks`
- `/over-ons` gebruikt `ruim 35 jaar` in plaats van `bijna 40 jaar`
- relevante vertalingen in `lang/nl/hermes.php`, `lang/en/hermes.php` en `lang/de/hermes.php` zijn waar van toepassing bijgewerkt
- `tests/Feature/PricingPageTest.php`, `tests/Feature/AboutPageTest.php` en `tests/Feature/ExampleTest.php` zijn bijgewerkt voor de nieuwe toegankelijkheid en copy

Gerichte teststatus:

- `php artisan test --compact tests/Feature/PricingPageTest.php tests/Feature/ExampleTest.php` groen: 33 tests, 148 assertions
- `php artisan test --compact tests/Feature/AboutPageTest.php` groen: 2 tests, 13 assertions

## Gebruik Van Dit Bestand

Gebruik dit document als startpunt voor vervolgopdrachten, tenzij nieuwe code of nieuwe deploys aantoonbaar actuelere informatie geven.

Bij functionele wijzigingen moet `Project_Context.md` mee worden bijgewerkt, zodat dit bestand bruikbaar blijft als vaste baseline.
