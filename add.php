<?php
// 1. DATENBANKVERBINDUNG LADEN
// Hier holen wir uns die $pdo-Variable aus der db.php, damit wir mit der Datenbank sprechen können.
require_once 'db.php';

// Variablen für Fehlermeldungen oder Erfolgsmeldungen initialisieren
$error = '';

// 2. PRÜFEN, OB DAS FORMULAR ABGESCHICKT WURDE
// $_SERVER['REQUEST_METHOD'] === 'POST' bedeutet: Hat der User auf den "Speichern"-Button geklickt?
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  // 3. DATEN AUS DEM FORMULAR AUSLESEN
  // Wir holen die eingetippten Werte aus dem $_POST-Array.
  // trim() entfernt dabei versehentliche Leerzeichen am Anfang und Ende.
  $title = trim($_POST['title']);
  $genre = trim($_POST['genre']);
  $description = trim($_POST['description']);
  $rating = $_POST['rating'];
  $status = $_POST['status'];

  // 4. PFLICHTFELDER PRÜFEN
  // Der Titel darf nicht leer sein, da er in der Datenbank als "NOT NULL" definiert ist.
  if (empty($title)) {
    $error = 'Bitte gib mindestens einen Titel ein!';
  } else {
    try {
      // 5. DATEN IN DIE DATENBANK SPEICHERN (SQL INSERT)
      // Wir bereiten den SQL-Befehl vor. Die Fragezeichen (?) sind Platzhalter.
      // Das ist extrem wichtig, um sich vor Hackerangriffen (SQL-Injection) zu schützen!
      $sql = "INSERT INTO media (title, genre, description, rating, status)
                    VALUES (?, ?, ?, ?, ?)";

      $stmt = $pdo->prepare($sql);

      // Hier ersetzen wir die Fragezeichen mit den echten Werten aus dem Formular
      $stmt->execute([$title, $genre, $description, $rating, $status]);

      // 6. ZURÜCK ZUR ÜBERSICHT WEITERLEITEN
      // Wenn alles geklappt hat, schicken wir den User automatisch zurück zur index.php
      header('Location: index.php');
      exit; // Das Skript wird hier sofort beendet

    } catch (PDOException $e) {
      // Falls beim Speichern ein Datenbank-Fehler auftritt, wird er hier abgefangen
      $error = 'Fehler beim Speichern: ' . $e->getMessage();
    }
  }
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Neues Medium hinzufügen</title>
</head>
<body>

<header>
  <h1>➕ Neues Medium hinzufügen</h1>
  <nav>
    <a href="index.php">⬅️ Zurück zur Übersicht</a>
  </nav>
</header>

<main>
  <?php if ($error): ?>
    <p style="color: red; font-weight: bold;"><?php echo $error; ?></p>
  <?php endif; ?>

  <form action="add.php" method="POST" style="margin-top: 20px;">

    <div style="margin-bottom: 15px;">
      <label for="title"><strong>Titel:</strong> (Pflichtfeld)</label><br>
      <input type="text" id="title" name="title" required style="width: 100%; max-width: 400px;">
    </div>

    <div style="margin-bottom: 15px;">
      <label for="genre"><strong>Kategorie / Genre:</strong></label><br>
      <input type="text" id="genre" name="genre" placeholder="z.B. Sci-Fi, Thriller, Roman" style="width: 100%; max-width: 400px;">
    </div>

    <div style="margin-bottom: 15px;">
      <label for="description"><strong>Beschreibung:</strong></label><br>
      <textarea id="description" name="description" rows="4" style="width: 100%; max-width: 400px;"></textarea>
    </div>

    <div style="margin-bottom: 15px;">
      <label for="rating"><strong>Bewertung (1-5 Sterne):</strong></label><br>
      <input type="number" id="rating" name="rating" min="1" max="5" value="3" style="width: 50px;">
    </div>

    <div style="margin-bottom: 15px;">
      <label for="status"><strong>Status:</strong></label><br>
      <select id="status" name="status">
        <option value="nicht gesehen">Nicht gesehen</option>
        <option value="gesehen">Gesehen</option>
      </select>
    </div>

    <button type="submit" style="padding: 10px 20px; font-weight: bold; cursor: pointer;">💾 Speichern</button>

  </form>
</main>

</body>
