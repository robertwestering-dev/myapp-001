#!/usr/bin/env bash
set -euo pipefail

APP_DIR="/webroots/sites/hermesresults.com/hermesresults-app"
PHP_BIN="php"
COMPOSER_BIN="composer"
NPM_BIN="npm"
SEED_CLASS="AcademyCourseSeeder"

say() {
    printf '\n%s\n' "$1"
}

confirm() {
    local prompt="$1"
    local answer

    read -r -p "$prompt [ja/nee]: " answer

    case "${answer,,}" in
        ja|j|yes|y)
            return 0
            ;;
        *)
            return 1
            ;;
    esac
}

cleanup() {
    if [[ "${APP_IS_DOWN:-0}" == "1" ]]; then
        say "De applicatie wordt weer online gezet."
        "$PHP_BIN" artisan up || true
    fi
}

trap cleanup EXIT

say "Hermes Results deploy-script"
say "Dit script is bedoeld voor de LIVE server van Hostnet."
say "Actieve live map: $APP_DIR"

if [[ ! -d "$APP_DIR" ]]; then
    say "FOUT: de app-map bestaat niet."
    say "Controleer of je dit script op de juiste server uitvoert."
    exit 1
fi

for command in "$PHP_BIN" "$COMPOSER_BIN" "$NPM_BIN" git; do
    if ! command -v "$command" >/dev/null 2>&1; then
        say "FOUT: commando '$command' is niet beschikbaar op deze server."
        exit 1
    fi
done

if ! confirm "Heb je jouw laatste wijzigingen al naar git gepusht?"; then
    say "Stopgezet. Push eerst je wijzigingen en run daarna dit script opnieuw."
    exit 1
fi

if ! confirm "Weet je zeker dat je nu LIVE wilt updaten?"; then
    say "Stopgezet. Er is niets aangepast."
    exit 0
fi

cd "$APP_DIR"

say "1/9 - Controleren of dit een git-repository is"
git rev-parse --is-inside-work-tree >/dev/null

say "2/9 - Applicatie in onderhoudsmodus zetten"
"$PHP_BIN" artisan down || true
APP_IS_DOWN=1

say "3/9 - Nieuwe code ophalen met git pull"
git pull

say "4/9 - PHP packages installeren"
"$COMPOSER_BIN" install --no-dev --optimize-autoloader

say "5/9 - Migraties uitvoeren"
"$PHP_BIN" artisan migrate --force

if confirm "Wil je ook de Academy seeder uitvoeren?"; then
    say "Academy seeder wordt uitgevoerd"
    "$PHP_BIN" artisan db:seed --class="$SEED_CLASS" --force
else
    say "Academy seeder wordt overgeslagen"
fi

say "6/9 - Frontend assets bouwen"
"$NPM_BIN" run build

say "7/9 - Laravel caches opschonen"
"$PHP_BIN" artisan optimize:clear

say "8/9 - Laravel caches opnieuw opbouwen"
"$PHP_BIN" artisan config:cache
"$PHP_BIN" artisan route:cache
"$PHP_BIN" artisan view:cache

say "9/9 - Applicatie weer online zetten"
"$PHP_BIN" artisan up
APP_IS_DOWN=0

say "Deploy voltooid."
say "Controleer nu handmatig:"
say "- https://hermesresults.com"
say "- taalwissel in de header"
say "- inloggen"
say "- /academy"
say "- /admin-portal"
