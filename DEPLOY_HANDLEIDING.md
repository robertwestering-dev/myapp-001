# Deploy Handleiding Voor Beginners

Dit document is bedoeld voor jou als beginner. Deze versie is aangepast op basis van wat we in de praktijk op jouw live server hebben gezien.

De belangrijkste conclusie is:

- de live map is **geen git-repository**
- op de live server staat **geen npm**
- deployen doe je dus vooral door **bestanden te uploaden met Cyberduck**
- daarna run je op de server een paar eenvoudige Laravel-commando's

## De Juiste Live Map

De actieve live Laravel-app staat hier:

```bash
/webroots/sites/hermesresults.com/hermesresults-app
```

Gebruik altijd deze map.

Gebruik **niet**:

```bash
/home/cl1myceal_u/hermesresults-app
```

## Belangrijkste Les

De volgende keer hoef je niet moeilijk te denken in git-deploys op de server.

Voor jouw situatie werkt dit het beste:

1. lokaal wijzigingen maken
2. lokaal `npm run build` draaien als je frontend hebt aangepast
3. met Cyberduck de gewijzigde mappen uploaden
4. op de server een paar commando's runnen
5. live testen

## De Snelle Versie

Als je weinig tijd hebt, volg dan deze checklist:

1. Maak lokaal je wijzigingen af.
2. Run lokaal `npm run build` als je frontend hebt aangepast.
3. Upload met Cyberduck de gewijzigde mappen naar:
   `/webroots/sites/hermesresults.com/hermesresults-app`
4. Log in op de server via SSH.
5. Controleer op de server of kritieke bestanden echt zijn overschreven.
6. Run op de server:

```bash
cd /webroots/sites/hermesresults.com/hermesresults-app
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

7. Als je Academy seeddata hebt aangepast, run dan ook:

```bash
php artisan db:seed --class=Database\\Seeders\\AcademyCourseSeeder --force
```

8. Test daarna live de site.

## Wat Je Bijna Altijd Moet Uploaden

Na de problemen die we hebben opgelost, is dit de belangrijkste regel:

Als je twijfelt, upload dan in elk geval deze mappen als ze lokaal gewijzigd zijn:

- `app/`
- `bootstrap/`
- `config/`
- `lang/`
- `resources/views/`
- `routes/`
- `database/migrations/`
- `database/seeders/`
- `public/build/` als je frontend hebt aangepast

Waarom dit belangrijk is:

- `lang/` ontbrak een keer, waardoor je vertaalkeys zag zoals `hermes.home.hero_title`
- `bootstrap/app.php` ontbrak een keer, waardoor de locale-middleware niet werkte
- `public/build/` moet apart mee, omdat de server geen npm heeft

## Taalredirects Voor `.nl` En `.eu`

Er is nu een fallback-oplossing gebouwd voor forwards naar `.com`.

De middleware ondersteunt nu:

```bash
?lang=nl
?lang=de
```

Daardoor kun je deze 301-redirects gebruiken:

- `hermesresults.nl` -> `https://hermesresults.com?lang=nl`
- `hermesresults.eu` -> `https://hermesresults.com?lang=de`

Belangrijk:

- hiervoor moet de juiste versie van `app/Http/Middleware/SetApplicationLocale.php` live staan
- en ook de juiste versie van `bootstrap/app.php`
- daarna moet je caches vernieuwen

## Wanneer Je `npm run build` Moet Draaien

Run lokaal op je Mac:

```bash
npm run build
```

als je iets hebt aangepast in:

- `resources/views/`
- `resources/css/`
- `resources/js/`
- Vite/Tailwind-gerelateerde frontend
- header / layout / styling

Na `npm run build` moet je deze map uploaden:

```bash
public/build
```

naar:

```bash
/webroots/sites/hermesresults.com/hermesresults-app/public/build
```

## Jouw Standaard Deploy-Volgorde

Gebruik voortaan deze volgorde.

### Stap 1. Lokaal wijzigingen afronden

Controleer op je Mac of alles klaar is.

### Stap 2. Frontend lokaal builden

Als je frontend hebt aangepast:

```bash
npm run build
```

### Stap 3. Uploaden met Cyberduck

Open Cyberduck en maak verbinding met je server.

Ga naar:

```bash
/webroots/sites/hermesresults.com/hermesresults-app
```

Upload daarna de gewijzigde mappen en bestanden.

Belangrijk:

- zorg dat Cyberduck bestaande bestanden echt overschrijft
- let extra goed op losse bestanden in `database/seeders/`, `database/factories/`, `app/`, `routes/` en `lang/`
- ga er niet automatisch van uit dat een upload gelukt is alleen omdat Cyberduck geen foutmelding gaf

### Stap 4. Inloggen op de server via SSH

Open Terminal op je Mac en log in op de server.

Als je ingelogd bent, ga dan naar:

```bash
cd /webroots/sites/hermesresults.com/hermesresults-app
```

### Stap 5. Controleer of kritieke bestanden echt live staan

Dit is een nieuwe verplichte stap als je net een bugfix, seeder, factory, controller, middleware of route hebt geüpload.

Controleer met `sed`, `grep` of `ls` of de server echt de nieuwe inhoud heeft.

Voorbeelden:

```bash
sed -n '1,220p' database/seeders/BlogPostSeeder.php
sed -n '1,160p' database/factories/BlogPostFactory.php
grep -n "SetApplicationLocale" bootstrap/app.php
ls -l app/Http/Middleware/SetApplicationLocale.php
```

Waarom dit belangrijk is:

- een upload kan stil misgaan of een oud bestand laten staan
- juist bij seeders en factories kan dat later verwarrende fouten geven
- door eerst te controleren voorkom je dat je fout zoekt in Laravel terwijl het echte probleem een niet-overschreven file is

### Stap 6. Composer en Laravel-commando's runnen

Run daarna:

```bash
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Als je locale-, middleware- of bootstrap-wijzigingen hebt gedaan, is dit blok extra belangrijk.

### Stap 7. Alleen als nodig: seeders

Als je Academy-data of Academy seeders hebt aangepast:

```bash
php artisan db:seed --class=Database\\Seeders\\AcademyCourseSeeder --force
```

Als je blog seeddata hebt aangepast:

```bash
php artisan db:seed --class=Database\\Seeders\\BlogPostSeeder --force
```

### Stap 8. Live controleren

Open daarna:

1. `https://hermesresults.com`
2. homepage
3. taalswitch
4. contactformulier
5. login
6. `/academy`
7. `/admin-portal`
8. `/blog`
9. `/admin-portal/blog-posts`

## Wat Je Op De Server Moet Typen

Dit is je standaardblok:

```bash
cd /webroots/sites/hermesresults.com/hermesresults-app
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

En eventueel:

```bash
php artisan db:seed --class=Database\\Seeders\\AcademyCourseSeeder --force
```

Of:

```bash
php artisan db:seed --class=Database\\Seeders\\BlogPostSeeder --force
```

## Belangrijke Uitzonderingen

### 1. Gebruik geen `git pull` op live

We hebben vastgesteld dat live geen git-repository is.

Dus dit werkt niet op live:

```bash
git pull
```

Upload bestanden met Cyberduck in plaats daarvan.

### 2. Gebruik geen `npm run build` op live

We hebben vastgesteld dat `npm` niet beschikbaar is op de live server.

Dus build altijd lokaal op je Mac.

### 3. Vergeet `bootstrap/app.php` niet

Als je middleware, bootstrapping of app-registratie hebt aangepast, moet ook dit bestand mee:

- `bootstrap/app.php`

Bij jou was dat essentieel voor de taalswitch.

### 4. Vergeet `lang/` niet

Als je taalbestanden verandert, upload dan de hele `lang/` map of de relevante taalmappen.

Bij jou zorgde een ontbrekende `lang/` map ervoor dat de site vertaalkeys toonde.

## Praktische Uploadlijst Per Soort Wijziging

### A. Alleen teksten / vertalingen aangepast

Upload:

- `lang/`

Daarna op de server:

```bash
php artisan optimize:clear
php artisan config:cache
php artisan view:cache
```

### B. Alleen views / homepage / header aangepast

Upload:

- `resources/views/`
- `lang/` als er ook teksten zijn veranderd
- `public/build/`

Daarna op de server:

```bash
php artisan optimize:clear
php artisan view:cache
```

### C. Controllers / middleware / routes / config aangepast

Upload:

- `app/`
- `bootstrap/`
- `config/`
- `routes/`
- eventueel `lang/`

Daarna op de server:

```bash
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### D. Database-wijzigingen

Upload:

- `database/migrations/`
- eventueel `database/seeders/`
- eventueel `database/factories/`
- eventueel `app/Models/`

Daarna op de server:

```bash
php artisan migrate --force
```

En eventueel:

```bash
php artisan db:seed --class=Database\\Seeders\\AcademyCourseSeeder --force
```

of:

```bash
php artisan db:seed --class=Database\\Seeders\\BlogPostSeeder --force
```

### E. Nieuwe blog of contentmodule

Upload:

- `app/Http/Controllers/`
- `app/Http/Requests/`
- `app/Models/`
- `database/migrations/`
- `database/seeders/`
- `database/factories/` als seeders of tests factories gebruiken
- `lang/`
- `resources/views/`
- `routes/`

Controleer daarna expliciet op de server:

```bash
sed -n '1,260p' database/seeders/BlogPostSeeder.php
```

Wat je niet meer wilt zien als de nieuwe blogseeder live staat:

```bash
BlogPost::factory()
```

Daarna run:

```bash
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan db:seed --class=Database\\Seeders\\BlogPostSeeder --force
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## Als Er Iets Mis Gaat

### Probleem: vertaalkeys zichtbaar op de site

Voorbeeld:

- `hermes.home.hero_title`
- `hermes.nav.services`

Dan is meestal `lang/` niet goed geüpload.

Oplossing:

1. upload `lang/`
2. run:

```bash
php artisan optimize:clear
php artisan config:cache
php artisan view:cache
```

### Probleem: taalswitch doet niets

Dan moet je controleren of `bootstrap/app.php` goed is meegekomen.

Run op de server:

```bash
grep -n "SetApplicationLocale" bootstrap/app.php
```

Als je niets terugkrijgt, dan staat de juiste versie van `bootstrap/app.php` niet live.

Controleer daarna ook of de middleware-file zelf aanwezig is:

```bash
ls -l app/Http/Middleware/SetApplicationLocale.php
```

Als de taalswitch nog steeds niet werkt:

1. controleer `.env`
2. zorg dat dit op live staat:

```bash
APP_LOCALE=nl
APP_FALLBACK_LOCALE=nl
SESSION_DRIVER=file
```

3. vernieuw daarna de caches opnieuw:

```bash
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Probleem: `.com` blijft Engels of Nederlands, ondanks taalkeuze

Dan is meestal een van deze dingen mis:

- `bootstrap/app.php` niet goed geüpload
- `SetApplicationLocale.php` niet goed geüpload
- caches nog oud
- `.env` locale-instellingen niet goed

Controleer dan:

```bash
php artisan config:show app.locale
php artisan config:show app.fallback_locale
php artisan config:show session.driver
php artisan route:list --name=locale.update
grep -n "SetApplicationLocale" bootstrap/app.php
```

Wat je wilt zien:

- `app.locale = nl`
- `app.fallback_locale = nl`
- `session.driver = file`
- route `POST /locale`
- en een match voor `SetApplicationLocale` in `bootstrap/app.php`

### Probleem: redirects vanaf `.nl` of `.eu` komen niet in de juiste taal uit

Controleer dan of de forward-link echt goed staat ingesteld:

- `hermesresults.nl` -> `https://hermesresults.com?lang=nl`
- `hermesresults.eu` -> `https://hermesresults.com?lang=de`

En controleer of deze file live staat:

- `app/Http/Middleware/SetApplicationLocale.php`

### Probleem: Academy seeder faalt

Als je deze fout krijgt:

```bash
Target class [Database\Seeders\AcademyCourseSeeder] does not exist
```

dan ontbreekt de seeder op live.

Upload dan:

- `database/seeders/`

Als je deze fout krijgt:

```bash
Table '...academy_courses' doesn't exist
```

dan moet eerst de migratie mee en uitgevoerd worden.

Upload dan:

- `database/migrations/`

en run:

```bash
php artisan migrate --force
```

### Probleem: blog seeder of factory faalt

Als je een fout krijgt zoals:

```bash
Class "Database\Factories\BlogPostFactory" not found
```

dan ontbreekt waarschijnlijk deze file op live:

- `database/factories/BlogPostFactory.php`

Als je een fout krijgt zoals:

```bash
Call to undefined function Database\Factories\fake()
```

of:

```bash
Call to a member function unique() on null
```

dan is meestal een van deze dingen mis:

- de verkeerde versie van `database/factories/BlogPostFactory.php` staat live
- de verkeerde versie van `database/seeders/BlogPostSeeder.php` staat live
- Cyberduck heeft de file niet echt overschreven

Controleer dan eerst de serverinhoud:

```bash
sed -n '1,260p' database/seeders/BlogPostSeeder.php
sed -n '1,180p' database/factories/BlogPostFactory.php
```

Als de seeder nog `BlogPost::factory()` bevat terwijl dat lokaal niet meer zo is, dan is de upload niet goed gegaan.

Oplossing:

1. upload de juiste file opnieuw
2. controleer direct opnieuw met `sed`
3. run pas daarna de seeder opnieuw

## Nuttige Commando's

Naar de live app-map:

```bash
cd /webroots/sites/hermesresults.com/hermesresults-app
```

Check waar je bent:

```bash
pwd
```

Bestanden tonen:

```bash
ls -la
```

Caches vernieuwen:

```bash
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Migraties draaien:

```bash
php artisan migrate --force
```

Seeder draaien:

```bash
php artisan db:seed --class=Database\\Seeders\\AcademyCourseSeeder --force
```

Blog seeder draaien:

```bash
php artisan db:seed --class=Database\\Seeders\\BlogPostSeeder --force
```

## Mijn Advies Voor De Volgende Keer

Denk de volgende keer niet in "deploy-script op de server", maar in:

1. wat heb ik lokaal aangepast?
2. welke mappen horen daarbij?
3. moet ik lokaal `npm run build` doen?
4. uploaden met Cyberduck
5. artisan/composer commando's op de server
6. live testen

Dat is voor jouw situatie de snelste en betrouwbaarste werkwijze.
