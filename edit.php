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
  $cover_image = trim($_POST['cover_image']); // <-- 1. Bild-URL aus dem Formular holen!
  $rating = $_POST['rating'];
  $status = $_POST['status'];

  if (empty($title)) {
    $error = 'Bitte geben Sie einen Titel ein.';
  } else {
    try {
      // 2. SQL UPDATE-Befehl erweitern um "cover_image = ?"
      $sql = "UPDATE media SET title = ?, genre = ?, description = ?, cover_image = ?, rating = ?, status = ? WHERE id = ?";
      $stmt = $pdo->prepare($sql);

      // 3. WICHTIG: Die Reihenfolge im Array MUSS exakt dem SQL-Befehl oben entsprechen. $id bleibt ganz hinten!
      $stmt->execute([$title, $genre, $description, $cover_image, $rating, $status, $id]);

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
  <link rel="stylesheet" href="style.css">
</head>
<body>
<button onclick="toggleDarkMode()" class="theme-toggle" id="theme-btn">🌙 Night Mode</button>

<script>
  if (localStorage.getItem('theme') === 'dark') {
    document.body.classList.add('dark-mode');
    document.getElementById('theme-btn').innerText = '☀️ Light Mode';
  }

  function toggleDarkMode() {
    const body = document.body;
    const btn = document.getElementById('theme-btn');

    body.classList.toggle('dark-mode');

    if (body.classList.contains('dark-mode')) {
      localStorage.setItem('theme', 'dark');
      btn.innerText = '☀️ Light Mode';
    } else {
      localStorage.setItem('theme', 'light');
      btn.innerText = '🌙 Night Mode';
    }
  }
</script>

<header>
  <h1>✏️ Medium bearbeiten</h1>
  <nav>
    <a href="index.php" class="btn">⬅️ Zurück zur Übersicht</a>
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
      <label for="cover_image"><strong>Cover-Bild (Bild-URL von TMDB einfügen):</strong></label><br>
      <input type="url" id="cover_image" name="cover_image" value="<?php echo htmlspecialchars($item['cover_image'] ?? ''); ?>" placeholder="https://image.tmdb.org/t/p/w500/..." style="width: 100%; max-width: 400px;">
    </div>

    <div style="margin-bottom: 15px;">
      <label><strong>Bewertung:</strong></label><br>

      <div class="star-rating" id="star-container">
        <?php
        $currentRating = $item['rating'];
        for($i = 1; $i <= 5; $i++) {
          $activeClass = ($i <= $currentRating) ? 'active' : '';
          echo "<span class='star $activeClass' data-value='$i'>★</span>";
        }
        ?>
      </div>

      <input type="hidden" id="rating" name="rating" value="<?php echo htmlspecialchars($currentRating); ?>" required>
    </div>

    <script>
      const stars = document.querySelectorAll('.star');
      const ratingInput = document.getElementById('rating');

      function updateStars(value) {
        stars.forEach(star => {
          if (star.getAttribute('data-value') <= value) {
            star.classList.add('active');
          } else {
            star.classList.remove('active');
          }
        });
      }

      stars.forEach(star => {
        star.addEventListener('click', function() {
          const value = this.getAttribute('data-value');
          ratingInput.value = value;
          updateStars(value);
        });
      });
    </script>

    <div style="margin-bottom: 15px;">
      <label for="status"><strong>Status:</strong></label><br>
      <select id="status" name="status" style="width: 100%; max-width: 400px;">
        <option value="nicht gesehen" <?php echo $item['status'] == 'nicht gesehen' ? 'selected' : ''; ?>>Nicht gesehen</option>
        <option value="gesehen" <?php echo $item['status'] == 'gesehen' ? 'selected' : ''; ?>>Gesehen</option>
      </select>
    </div>

    <button type="submit" class="btn" style="padding: 10px 20px; font-weight: bold; cursor: pointer;">💾
