#  THE ULTIMATE MediaVault

##  Key Features
* **Intelligente API-Suche:** Live-Suche nach Filmen und Serien mit automatischer Übernahme von Titel, Genre, Cover und Beschreibung.
* **Dynamische Kategorisierung:** Unterscheidung zwischen Film und Serie mit automatischer Typ-Erkennung.
* **Interaktives Dashboard:** Sofortiger Überblick über die Sammlung mit Echtzeit-Statistiken.
* **Flexibles Filter- & Sortiersystem:** Anpassbare Ansicht nach Status, Sortierung nach Bewertung oder alphabetisch.
* **Modernes User Interface:** Mit integriertem **Dark Mode**.
* **Optimierte Tabellenansicht:** Übersichtliche Darstellung mit automatischer Textkürzung.

## Architektur
* **Frontend:** HTML5, CSS3, JavaScript (Vanilla)
* **Backend:** PHP
* **Datenbank:** MySQL (Schnittstelle via PDO)
* **API:** The Movie Database (TMDB API)

## ️ Startanleitung & Installation
1. **Repository:** Laden Sie den Projektordner herunter.
2. **Datenbank:** Importieren Sie die SQL-Struktur in Ihre MySQL-Datenbank.
  - *SQL-Befehl für das neue Feld:* `ALTER TABLE media ADD COLUMN type VARCHAR(20) DEFAULT 'Film';`
3. **Konfiguration:** Öffnen Sie die `db.php` und passen Sie die Datenbank-Zugangsdaten (Host, Name, User, Passwort) an Ihre lokale Umgebung an.
4. **Start:** Starten Sie Ihren lokalen Webserver, verschieben Sie den Ordner in das `htdocs`-Verzeichnis und rufe `index.php` im Browser auf.
5. **API-Key:** Der TMDB-API-Key ist bereits in der `add.php` hinterlegt und sofort einsatzbereit.

## 📁 Projektstruktur
* `index.php`: Hauptübersicht mit Dashboard, Filtern und Tabellendarstellung.
* `add.php`: Formular zur automatisierten Anlage von Medien (inkl. Live-Suchanbindung).
* `detail.php`: Detailansicht für ein spezifisches Medium.
* `edit.php`: Formular zum Bearbeiten bestehender Einträge.
* `delete.php`: Skript zum Löschen von Medieneinträgen.
* `db.php`: Zentrale Datenbankverbindung.
* `search_suggest.php`: Hilfsskript für die Live-Suche.
* `style.css`: Zentrales Stylesheet.

##  Bedienung
1. **Hinzufügen:** Klicke auf "Neues Medium hinzufügen".
2. **Suchen:** Gib einen Titel ein. Das System schlägt in Echtzeit Treffer aus der TMDB vor.
3. **Übernehmen:** Klicke auf einen Vorschlag, um die Daten automatisch zu übernehmen.
4. **Verwalten:** Nutze die Filter und die Suchfunktion auf der Startseite zur Verwaltung deiner Sammlung.

##  KI Nutzung & Entwicklungsmethodik

1. **Strukturierung:** Unterstützung bei der Architektur
2. **Debugging:** Analyse von Fehlermeldungen und Identifikation von Lösungsansätzen.
3. **API-Integration**
4. **Code-Optimierung**

