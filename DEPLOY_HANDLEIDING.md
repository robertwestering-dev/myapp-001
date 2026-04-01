# Deploy Handleiding Voor Beginners

Dit document is bedoeld voor jou als beginner. Je hoeft geen serverexpert te zijn om dit te volgen.

De live versie van Hermes Results draait volgens de huidige projectcontext in:

- publieke webroot: `/webroots/sites/hermesresults.com`
- Laravel app-map: `/webroots/sites/hermesresults.com/hermesresults-app`

Gebruik dus altijd die app-map.

Let op:
- gebruik **niet** de oude map `/home/cl1myceal_u/hermesresults-app`
- deze handleiding gaat uit van deployen via `git pull` op de server

## Wat je hebt gekregen

In de projectroot staat nu een bestand:

- [deploy.sh](/Users/robert/Desktop/MyApp-001/deploy.sh)

Dat script helpt je op de server met:

- onderhoudsmodus aanzetten
- `git pull`
- `composer install`
- `php artisan migrate --force`
- optioneel Academy seeder draaien
- `npm run build`
- caches legen en opnieuw opbouwen
- site weer online zetten

## Heel Korte Samenvatting

Als je ervaring hebt, is dit de korte versie:

1. zorg dat je code naar git is gepusht
2. log in op de server
3. ga naar `/webroots/sites/hermesresults.com/hermesresults-app`
4. run `./deploy.sh`
5. volg de vragen op het scherm
6. test de live site

## Deel 1: Voorbereiding Op Je Eigen Computer

Doe dit eerst op je eigen computer.

### Stap 1. Controleer of je wijzigingen lokaal goed zijn

Controleer of je bestanden echt zijn aangepast.

### Stap 2. Zet je wijzigingen in git

Als je met git werkt, moet de live server jouw laatste code kunnen ophalen met `git pull`.

In gewone mensentaal betekent dat:

1. je slaat je wijzigingen op
2. je commit ze
3. je pusht ze naar de git-server

Als je hierbij hulp nodig hebt, gebruik dan deze simpele volgorde in je terminal in de projectmap:

```bash
git status
git add .
git commit -m "Update Hermes Results"
git push
```

Belangrijk:
- doe dit alleen als je weet dat je echt jouw wijzigingen wilt bewaren
- als `git status` rare of onverwachte bestanden laat zien, stop dan eerst

## Deel 2: Inloggen Op De Server

Je moet daarna inloggen op je Hostnet-server via SSH.

Als je Hostnet of je beheerder je een SSH-login heeft gegeven, ziet dat meestal ongeveer zo uit:

```bash
ssh jouw-gebruikersnaam@jouw-server
```

Als je dit nog nooit hebt gedaan:

1. open Terminal op je Mac
2. plak het SSH-commando dat je van Hostnet of je beheerder hebt gekregen
3. druk op Enter
4. vul eventueel je wachtwoord in

## Deel 3: Naar De Juiste Live Map Gaan

Na het inloggen op de server:

```bash
cd /webroots/sites/hermesresults.com/hermesresults-app
```

Controleer waar je bent:

```bash
pwd
```

Je moet dan dit pad zien:

```bash
/webroots/sites/hermesresults.com/hermesresults-app
```

## Deel 4: Het Deploy Script Klaarzetten

Als `deploy.sh` nog niet op de server staat, upload het bestand dan eerst naar:

```bash
/webroots/sites/hermesresults.com/hermesresults-app
```

Zet het daarna uitvoerbaar:

```bash
chmod +x deploy.sh
```

Je hoeft dit meestal maar één keer te doen.

## Deel 5: De Deploy Uitvoeren

Run daarna op de server:

```bash
./deploy.sh
```

Het script stelt je een paar simpele vragen.

### Vraag 1

Het script vraagt of je je code al naar git hebt gepusht.

Als dat nog niet zo is:

- typ `nee`
- druk op Enter
- stop hier
- push eerst je code vanaf je eigen computer

### Vraag 2

Het script vraagt of je echt live wilt updaten.

Als je klaar bent:

- typ `ja`
- druk op Enter

### Vraag 3

Het script vraagt of je de Academy seeder wilt draaien.

Kies:

- `ja` als je Academy-content of Academy seeddata hebt aangepast
- `nee` als je dat niet hebt gedaan

## Deel 6: Wat Het Script Voor Je Doet

Het script doet daarna automatisch:

1. de site tijdelijk in onderhoudsmodus zetten
2. nieuwe code ophalen met `git pull`
3. PHP packages bijwerken met Composer
4. database-migraties uitvoeren
5. optioneel de Academy seeder draaien
6. frontend opnieuw bouwen met `npm run build`
7. Laravel caches opschonen
8. caches opnieuw opbouwen
9. de site weer online zetten

## Deel 7: Wat Je Na De Deploy Altijd Moet Controleren

Open daarna in je browser:

1. `https://hermesresults.com`
2. klik door de homepage
3. test de taalswitch
4. test het contactformulier
5. test inloggen
6. test `/academy`
7. test `/admin-portal`

## Als Er Iets Fout Gaat

### Situatie 1: Het script stopt met een foutmelding

Lees de laatste regels goed.

Kopieer die foutmelding en bewaar hem.

### Situatie 2: De site blijft in onderhoudsmodus

Run dan handmatig:

```bash
php artisan up
```

### Situatie 3: Wijzigingen zijn niet zichtbaar

Meestal komt dat door:

- `git pull` haalde niet de nieuwste code op
- `npm run build` is niet goed gegaan
- caches zijn niet goed vernieuwd

Je kunt dan nog een keer deze commando's runnen:

```bash
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## Mijn Advies Voor Jou Als Beginner

Gebruik voor live deploys steeds precies deze volgorde:

1. lokaal controleren
2. committen en pushen
3. inloggen op server
4. naar de juiste map gaan
5. `./deploy.sh` runnen
6. live controleren

Ga niet handmatig losse commando's proberen als je niet zeker weet wat ze doen.

## Handige Commando's

Check waar je bent:

```bash
pwd
```

Bestanden in de map tonen:

```bash
ls -la
```

Controleren of `deploy.sh` aanwezig is:

```bash
ls -la deploy.sh
```

Script uitvoerbaar maken:

```bash
chmod +x deploy.sh
```

Deploy starten:

```bash
./deploy.sh
```
