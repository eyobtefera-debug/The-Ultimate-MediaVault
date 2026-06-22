<?php
require_once 'db.php';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $title = trim($_POST['title']);
  $genre = trim($_POST['genre']);
  $description = trim($_POST['description']);
  $cover_image = trim($_POST['cover_image']);
  $rating = $_POST['rating'];
  $status = $_POST['status'];
  $type = $_POST['type']; // NEU: Typ auslesen

  if (empty($title)) {
    $error = 'Bitte gib mindestens einen Titel ein!';
  } else {
    try {
      // SQL angepasst um "type"
      $sql = "INSERT INTO media (title, genre, description, cover_image, rating, status, type) VALUES (?, ?, ?, ?, ?, ?, ?)";
      $stmt = $pdo->prepare($sql);
      $stmt->execute([$title, $genre, $description, $cover_image, $rating, $status, $type]);
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
  <title>Neues Medium hinzufügen</title>
  <link rel="stylesheet" href="style.css">
  <style>
    .autocomplete-wrapper { position: relative; width: 100%; max-width: 400px; }
    .suggestions-box { position: absolute; top: 100%; left: 0; width: 100%; background: var(--white, #fff); border: 1px solid #ddd; border-radius: 0 0 6px 6px; z-index: 1000; box-shadow: 0 4px 10px rgba(0,0,0,0.1); display: none; max-height: 200px; overflow-y: auto; }
    .suggestion-item { padding: 10px; cursor: pointer; }
    .suggestion-item:hover { background-color: var(--primary-color, #4A90E2); color: white; }
  </style>
</head>
<body>
<header><h1>➕ Neues Medium hinzufügen</h1><a href="index.php" class="btn">⬅️ Zurück</a></header>

<main>
  <form action="add.php" method="POST">
    <label><strong>Titel:</strong></label><br>
    <div class="autocomplete-wrapper">
      <input type="text" id="title" name="title" required style="width:100%" autocomplete="off">
      <div id="suggestions" class="suggestions-box"></div>
    </div>

    <label><strong>Typ:</strong></label><br>
    <select id="type" name="type" style="width:100%; max-width:400px;">
      <option value="Film">Film</option>
      <option value="Serie">Serie</option>
    </select><br><br>

    <label><strong>Genre:</strong></label><br>
    <input type="text" id="genre" name="genre" style="width:100%; max-width:400px;"><br><br>
    <label><strong>Beschreibung:</strong></label><br>
    <textarea id="description" name="description" style="width:100%; max-width:400px;"></textarea><br><br>
    <label><strong>Cover-URL:</strong></label><br>
    <input type="url" id="cover_image" name="cover_image" style="width:100%; max-width:400px;"><br><br>

    <button type="submit" class="btn">💾 Speichern</button>
  </form>
</main>

<script>
  const TMDB_API_KEY = '7b35a93a38ed7b4f841e08e020d29110';
  const titleInput = document.getElementById('title');
  const typeInput = document.getElementById('type');
  const suggestionsBox = document.getElementById('suggestions');

  titleInput.addEventListener('input', function() {
    const value = titleInput.value.trim();
    if (value.length < 2) return;

    // Multi-Suche (findet Filme UND Serien)
    fetch(`https://api.themoviedb.org/3/search/multi?api_key=${TMDB_API_KEY}&language=de-DE&query=${encodeURIComponent(value)}`)
      .then(res => res.json())
      .then(data => {
        suggestionsBox.innerHTML = '';
        data.results.slice(0, 5).forEach(item => {
          const div = document.createElement('div');
          div.className = 'suggestion-item';
          div.innerText = (item.title || item.name);
          div.onclick = () => {
            titleInput.value = (item.title || item.name);
            typeInput.value = (item.media_type === 'tv' ? 'Serie' : 'Film');
            document.getElementById('description').value = item.overview || '';
            if(item.poster_path) document.getElementById('cover_image').value = 'https://image.tmdb.org/t/p/w500' + item.poster_path;
            suggestionsBox.style.display = 'none';
          };
          suggestionsBox.appendChild(div);
        });
        suggestionsBox.style.display = 'block';
      });
  });
</script>
</body>
</html>
