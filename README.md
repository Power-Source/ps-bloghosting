# PS-Bloghosting

PS-Bloghosting verwandelt eine WordPress/ClassicPress-Multisite in ein kommerzielles Bloghosting à la WordPress.com. Du erstellst Tarife mit Premium-Themes, Plugins, Speicher, Support, Quoten, Werbung u. v. m. und kassierst über integrierte Gateways.

## Highlights
- **Tarif-Modelle**: Beliebig viele Stufen mit Features bündeln (Themes, Plugins, Speicher, Support, Quoten, Werbung, Premium-Themes/-Plugins, BuddyPress, E-Commerce-Filter usw.).
- **Checkout & Preise**: Eingebaute Preistabellen und Checkout; flexible Perioden, Trials und manuelle Freischaltungen.
- **Gateways**: PayPal Express/Pro, Stripe, 2Checkout, manuell, Trial – erweiterbar über Module.
- **Rechnungen & Steuern**: PDF-Rechnungen via TCPDF, Tax-Helper, Belegerstellung, E-Mail-Benachrichtigungen.
- **GDPR & Admin-Tools**: GDPR-Helfer, Logging, Quoten- und Statistiken, Admin-Links, Badge/Widget.
- **Hooks & Module**: Modularer Aufbau unter `modules/` und `gateways/` für eigene Erweiterungen.

## Voraussetzungen
- Multisite aktiviert (WP/CP ab 3.8, getestet bis 6.8.1)
- PHP 7.0+

## Installation (Netzwerk)
1. Plugin-Ordner oder Release-Zip nach `/wp-content/plugins/` kopieren.
2. Im Netzwerk-Dashboard aktivieren.
3. Tarife, Preise und Gateways im Netzwerk-Admin konfigurieren.

## Build & Entwicklung
- Abhängigkeiten: `npm install`
- Übersetzungen (erfordert `msgfmt`/gettext): `npx grunt translate`
- Release-Zip (schließt bin/docs/tests/node_modules aus): `npx grunt build` → `releases/ps-bloghosting-<version>.zip`

## Release-Checkliste
1. Version in `package.json` und `pro-sites.php` (Header + `$version`) anpassen.
2. Übersetzungen regenerieren: `npx grunt translate`.
3. Build ausführen: `npx grunt build`.
4. Taggen und pushen: `git tag -a v<version> -m "Release v<version>" && git push origin v<version>`.

## Links
- Doku: https://cp-psource.github.io/ps-bloghosting/
- Repo: https://github.com/Power-Source/ps-bloghosting
