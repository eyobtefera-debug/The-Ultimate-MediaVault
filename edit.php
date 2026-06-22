<?php
// 1. Datenbankverbindung laden
require_once 'db.php';

// 2. Prüfen, ob eine ID übergeben wurde
if (!isset($_GET['id'])) {
  header('Location: index.php'); // Wenn keine ID da ist, zurück zur Startseite
  exit;
}

$id = $_GET['id'];
$error = '';

// 3. Aktuelle Daten des Mediums aus der Datenbank holen
$stmt = $pdo->prepare("SELECT * FROM media WHERE id = ?");
$stmt->execute([$id]);
$item = $stmt->fetch(PDO::FETCH_ASSOC);

// Falls jemand eine ID eingibt, die es nicht gibt
if (!$item) {
  header('Location: index.php');
  exit;
}

// 4. Wenn das Formular abgeschickt wurde (Update)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $title = trim($_POST['title']);
  $genre = trim($_POST['genre']);
  $description = trim($_POST['description']);
  $rating = $_POST['rating'];
  $status = $_POST['status'];

  if (empty($title)) {
    $error = 'Bitte geben Sie einen Titel ein.';
  } else {
    try {
      // WICHTIG: Hier nutzen wir UPDATE statt INSERT!
      $sql = "UPDATE media SET title = ?, genre = ?, description = ?, rating = ?, status = ? WHERE id = ?";
      $stmt = $pdo->prepare($sql);
      $stmt->execute([$title, $genre, $description, $rating, $status, $id]);

      // Zurück zur Übersicht
      header('Location: index.php');
      exit;
    } catch (PDOException $e) {
      $error = 'Fehler beim Aktualisieren: ' . $e->getMessage();
    }
  }
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Medium bearbeiten</title>
</head>
<body>

<header>
  <h1>✏️ Medium bearbeiten</h1>
  <nav>
    <a href="index.php">⬅️ Zurück zur Übersicht</a>
  </nav>
</header>

<main>
  <?php if ($error): ?>
    <p style="color: red; font-weight: bold;"><?php echo $error; ?></p>
  <?php endif; ?>

  <form action="edit.php?id=<?php echo $item['id']; ?>" method="POST" style="margin-top: 20px;">

    <div style="margin-bottom: 15px;">
      <label for="title"><strong>Titel:</strong> (Pflichtfeld)</label><br>
      <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($item['title']); ?>" required style="width: 100%; max-width: 400px;">
    </div>

    <div style="margin-bottom: 15px;">
      <label for="genre"><strong>Kategorie / Genre:</strong></label><br>
      <input type="text" id="genre" name="genre" value="<?php echo htmlspecialchars($item['genre']); ?>" style="width: 100%; max-width: 400px;">
    </div>

    <div style="margin-bottom: 15px;">
      <label for="description"><strong>Beschreibung:</strong></label><br>
      <textarea id="description" name="description" rows="4" style="width: 100%; max-width: 400px;"><?php echo htmlspecialchars($item['description']); ?></textarea>
    </div>

    <div style="margin-bottom: 15px;">
      <label for="rating"><strong>Bewertung (1-5 Sterne):</strong></label><br>
      <input type="number" id="rating" name="rating" min="1" max="5" value="<?php echo htmlspecialchars($item['rating']); ?>" style="width: 50px;">
    </div>

    <div style="margin-bottom: 15px;">
      <label for="status"><strong>Status:</strong></label><br>
      <select id="status" name="status">
        <option value="nicht gesehen" <?php echo $item['status'] == 'nicht gesehen' ? 'selected' : ''; ?>>Nicht gesehen</option>
        <option value="gesehen" <?php echo $item['status'] == 'gesehen' ? 'selected' : ''; ?>>Gesehen</option>
      </select>
    </div>

    <button type="submit" style="padding: 10px 20px; font-weight: bold; cursor: pointer;">💾 Änderungen speichern</button>

  </form>
</main>

</body>
</html>
