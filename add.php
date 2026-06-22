<?php
// 1. DATENBANKVERBINDUNG LADEN
require_once 'db.php';

$error = '';

// 2. PRÜFEN, OB DAS FORMULAR ABGESCHICKT WURDE
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  // 3. DATEN AUS DEM FORMULAR AUSLESEN
  $title = trim($_POST['title']);
  $cover_image = trim($_POST['cover_image']);
  $genre = trim($_POST['genre']);
  $description = trim($_POST['description']);
  $rating = $_POST['rating'];
  $status = $_POST['status'];

  // 4. PFLICHTFELDER PRÜFEN
  if (empty($title)) {
    $error = 'Bitte gib mindestens einen Titel ein!';
  } else {
    try {
      // 5. DATEN IN DIE DATENBANK SPEICHERN (SQL INSERT)
      $sql = "INSERT INTO media (title, genre, description, cover_image, rating, status) VALUES (?, ?, ?, ?, ?, ?)";
      $stmt = $pdo->prepare($sql);
      $stmt->execute([$title, $genre, $description, $cover_image, $rating, $status]);

      // 6. ZURÜCK ZUR ÜBERSICHT WEITERLEITEN
      header('Location: index.php');
      exit;

    } catch (PDOException $e) {
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
  <link rel="stylesheet" href="style.css">
  <style>
    /* Styling für die Autocomplete-Vorschläge */
    .autocomplete-wrapper {
      position: relative;
      width: 100%;
      max-width: 400px;
    }
    .suggestions-box {
      position: absolute;
      top: 100%;
      left: 0;
      width: 100%;
      background: var(--white, #fff);
      border: 1px solid #ddd;
      border-top: none;
      border-radius: 0 0 6px 6px;
      z-index: 1000;
      box-shadow: 0 4px 10px rgba(0,0,0,0.1);
      display: none;
      max-height: 200px;
      overflow-y: auto;
    }
    .suggestion-item {
      padding: 10px 12px;
      cursor: pointer;
      color: var(--text-color, #333);
    }
    .suggestion-item:hover {
      background-color: var(--primary-color, #4A90E2);
      color: white;
    }
    body.dark-mode .suggestions-box {
      border-color: #444;
    }
  </style>
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
  <h1>➕ Neues Medium hinzufügen</h1>
  <nav>
    <a href="index.php" class="btn">⬅️ Zurück zur Übersicht</a>
  </nav>
</header>

<main>
  <?php if ($error): ?>
    <p style="color: red; font-weight: bold;"><?php echo $error; ?></p>
  <?php endif; ?>

  <form action="add.php" method="POST" style="margin-top: 20px;">

    <div style="margin-bottom: 15px;">
      <label for="title"><strong>Titel:</strong> (Pflichtfeld)</label><br>
      <div class="autocomplete-wrapper">
        <input type="text" id="title" name="title" required style="width: 100%; margin-top: 5px; margin-bottom: 0;" autocomplete="off" placeholder="Tippe einen Titel...">
        <div id="suggestions" class="suggestions-box"></div>
      </div>
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
      <label for="cover_image"><strong>Cover-Bild (Bild-URL von TMDB einfügen):</strong></label><br>
      <input type="url" id="cover_image" name="cover_image" placeholder="https://image.tmdb.org/t/p/w500/..." style="width: 100%; max-width: 400px;">
    </div>

    <div style="margin-bottom: 15px;">
      <label><strong>Bewertung:</strong></label><br>
      <div class="star-rating" id="star-container" style="font-size: 28px; cursor: pointer; color: #CCC; user-select: none;">
        <span class="star active" data-value="1">★</span>
        <span class="star active" data-value="2">★</span>
        <span class="star active" data-value="3">★</span>
        <span class="star" data-value="4">★</span>
        <span class="star" data-value="5">★</span>
      </div>
      <input type="hidden" id="rating" name="rating" value="3" required>
    </div>

    <div style="margin-bottom: 15px;">
      <label for="status"><strong>Status:</strong></label><br>
      <select id="status" name="status" style="width: 100%; max-width: 400px;">
        <option value="nicht gesehen">Nicht gesehen</option>
        <option value="gesehen">Gesehen</option>
      </select>
    </div>

    <button type="submit" class="btn" style="padding: 10px 20px; font-weight: bold; cursor: pointer;">💾 Speichern</button>

  </form>
</main>

<script>
  // 1. STERN-LOGIK
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

  // 2. AUTOMATISCHE VORSCHLÄGE (AJAX / Fetch)
  const titleInput = document.getElementById('title');
  const suggestionsBox = document.getElementById('suggestions');

  titleInput.addEventListener('input', function() {
    const value = titleInput.value.trim();

    if (value.length < 1) {
      suggestionsBox.style.display = 'none';
      return;
    }

    // Wir fragen im Hintergrund heimlich unsere search_suggest.php an
    fetch('search_suggest.php?term=' + encodeURIComponent(value))
      .then(response => response.json())
      .then(data => {
        suggestionsBox.innerHTML = ''; // Box leeren

        if (data.length > 0) {
          data.forEach(titleText => {
            const div = document.createElement('div');
            div.classList.add('suggestion-item');
            div.innerText = titleText;

            // Wenn man auf einen Vorschlag klickt, Text ins Feld setzen
            div.addEventListener('click', function() {
              titleInput.value = titleText;
              suggestionsBox.style.display = 'none';
            });

            suggestionsBox.appendChild(div);
          });
          suggestionsBox.style.display = 'block';
        } else {
          suggestionsBox.style.display = 'none';
        }
      });
  });

  // Schließen der Box, wenn man irgendwo anders hinklickt
  document.addEventListener('click', function(e) {
    if (e.target !== titleInput) {
      suggestionsBox.style.display = 'none';
    }
  });
</script>

</body>
</html>
