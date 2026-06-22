<?php
// 1. Datenbankverbindung einbinden
require_once 'db.php';

try {
// --- SORTIEREN & FILTERN LOGIK ---
  $orderBy = "title ASC"; // Standard-Sortierung (A-Z)
  $whereClause = "";
  $params = [];

// 1. Wurde ein Filter gewählt?
  if (isset($_GET['status']) && $_GET['status'] != '') {
    $whereClause = "WHERE status = ?";
    $params[] = $_GET['status']; // Sicherer Parameter für PDO
  }

// 2. Wurde eine Sortierung gewählt?
  if (isset($_GET['sort'])) {
    if ($_GET['sort'] == 'rating_desc') {
      $orderBy = "rating DESC"; // Beste Bewertung zuerst
    } elseif ($_GET['sort'] == 'rating_asc') {
      $orderBy = "rating ASC";  // Schlechteste Bewertung zuerst (NEU!)
    } elseif ($_GET['sort'] == 'title_asc') {
      $orderBy = "title ASC";   // Alphabetisch
    }
  }

// 3. SQL zusammenbauen und ausführen
  $sql = "SELECT * FROM media $whereClause ORDER BY $orderBy";
  $stmt = $pdo->prepare($sql);
  $stmt->execute($params);
// ----------------------------------

$mediaItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
  die("Fehler beim Laden der Medien: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Media Vault - Übersicht</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
<button onclick="toggleDarkMode()" class="theme-toggle" id="theme-btn">🌙 Night Mode</button>

<script>
  // 1. Beim Laden der Seite prüfen, was im Browser-Speicher steht
  if (localStorage.getItem('theme') === 'dark') {
    document.body.classList.add('dark-mode');
    document.getElementById('theme-btn').innerText = '☀️ Light Mode';
  }

  // 2. Die Funktion, die beim Klicken umschaltet
  function toggleDarkMode() {
    const body = document.body;
    const btn = document.getElementById('theme-btn');

    body.classList.toggle('dark-mode');

    // Zustand im LocalStorage des Browsers speichern
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
  <h1> THE ULTIMATE MediaVault</h1>
  <nav>
  </nav>
</header>

<main>
  <h2>Deine Mediensammlung</h2>
  <a href="add.php" class="btn">➕ Neues Medium hinzufügen</a>
  <form method="GET" action="index.php" style="background: var(--bg-color); padding: 15px; border-radius: 8px; margin: 20px 0; border: 1px solid #ddd;">

    <label for="status" style="font-weight: bold; margin-right: 5px;">Filtern:</label>
    <select name="status" id="status" style="width: auto; display: inline-block; margin-right: 20px; margin-top: 0;">
      <option value="">Alle anzeigen</option>
      <option value="gesehen" <?php echo (isset($_GET['status']) && $_GET['status'] == 'gesehen') ? 'selected' : ''; ?>>Gesehen</option>
      <option value="nicht gesehen" <?php echo (isset($_GET['status']) && $_GET['status'] == 'nicht gesehen') ? 'selected' : ''; ?>>Nicht gesehen</option>
    </select>

    <label for="sort" style="font-weight: bold; margin-right: 5px;">Sortieren nach:</label>
    <select name="sort" id="sort" style="width: auto; display: inline-block; margin-right: 20px; margin-top: 0;">
      <option value="title_asc" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'title_asc') ? 'selected' : ''; ?>>Titel (A-Z)</option>
      <option value="rating_desc" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'rating_desc') ? 'selected' : ''; ?>>Bewertung (Beste zuerst)</option>
      <option value="rating_asc" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'rating_asc') ? 'selected' : ''; ?>>Bewertung (Schlechteste zuerst)</option>
    </select>

    <button type="submit" class="btn" style="padding: 8px 15px;">Anwenden</button>
  </form>
  <?php if (empty($mediaItems)): ?>
    <p>Dein Vault ist noch leer. Klicke oben auf "Neues Medium hinzufügen", um zu starten!</p>
  <?php else: ?>
    <table border="1" cellpadding="8" style="border-collapse: collapse; width: 100%;">
      <thead>
      <tr>
        <th>Titel</th>
        <th>Kategorie / Genre</th>
        <th>Beschreibung</th>
        <th>Bewertung</th>
        <th>Status</th>
        <th>Aktionen</th>
      </tr>
      </thead>
      <tbody>
      <?php foreach ($mediaItems as $item): ?>
        <tr>
          <td>
            <a href="detail.php?id=<?php echo $item['id']; ?>" style="text-decoration: none; color: var(--secondary-color); font-weight: bold;">
              <?php echo htmlspecialchars($item['title']); ?>
            </a>
          </td>

          <td><?php echo htmlspecialchars($item['genre']); ?></td>
          <td><?php echo htmlspecialchars($item['description']); ?></td>
          <td><?php echo htmlspecialchars($item['rating']); ?></td>
          <td><?php echo htmlspecialchars($item['status']); ?></td>

          <td>
            <a href="edit.php?id=<?php echo $item['id']; ?>">✏️ Bearbeiten</a> |
            <a href="delete.php?id=<?php echo $item['id']; ?>">🗑️ Löschen</a>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</main>

</body>
</html>
