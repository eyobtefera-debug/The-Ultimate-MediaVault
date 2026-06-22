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
      $orderBy = "rating ASC";  // Schlechteste Bewertung zuerst
    } elseif ($_GET['sort'] == 'title_asc') {
      $orderBy = "title ASC";   // Alphabetisch
    }
  }

// 3. SQL zusammenbauen und ausführen
  $sql = "SELECT * FROM media $whereClause ORDER BY $orderBy";
  $stmt = $pdo->prepare($sql);
  $stmt->execute($params);
  $mediaItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

// --- NEU: STATISTIKEN AUS DER DATENBANK HOLEN ---
  // Wir holen Gesamtanzahl, gesehene Medien und den Bewertungsdurchschnitt in nur EINER einzigen, hocheffizienten Abfrage
  $statsQuery = "SELECT
      COUNT(*) as total,
      SUM(CASE WHEN status = 'gesehen' THEN 1 ELSE 0 END) as watched,
      AVG(rating) as avg_rating
  FROM media";
  $statsStmt = $pdo->query($statsQuery);
  $stats = $statsStmt->fetch(PDO::FETCH_ASSOC);

  // Werte für die Anzeige vorbereiten (round() rundet die Sterne-Zahl auf 1 Nachkommastelle)
  $totalMedia = $stats['total'] ?? 0;
  $watchedMedia = $stats['watched'] ?? 0;
  $avgRating = $stats['avg_rating'] ? round($stats['avg_rating'], 1) : 0;
// ------------------------------------------------

} catch (PDOException $e) {
  die("Fehler beim Laden der Medien: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="referrer" content="no-referrer">
  <title>Media Vault - Übersicht</title>
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
  <h1>THE ULTIMATE MediaVault</h1>
  <nav>
  </nav>
</header>

<main>
  <h2>Deine Mediensammlung</h2>

  <div class="stat-container">
    <div class="stat-card">
      <div style="font-size: 24px; margin-bottom: 5px;">🎬</div>
      <div style="font-size: 11px; color: gray; text-transform: uppercase; font-weight: bold; letter-spacing: 0.5px;">Gesamt im Vault</div>
      <div style="font-size: 26px; font-weight: bold; margin-top: 5px; color: var(--secondary-color);"><?php echo $totalMedia; ?></div>
    </div>

    <div class="stat-card">
      <div style="font-size: 24px; margin-bottom: 5px;">✅</div>
      <div style="font-size: 11px; color: gray; text-transform: uppercase; font-weight: bold; letter-spacing: 0.5px;">Gesehen</div>
      <div style="font-size: 26px; font-weight: bold; margin-top: 5px; color: #2ecc71;"><?php echo $watchedMedia; ?></div>
    </div>

    <div class="stat-card">
      <div style="font-size: 24px; margin-bottom: 5px;">⭐</div>
      <div style="font-size: 11px; color: gray; text-transform: uppercase; font-weight: bold; letter-spacing: 0.5px;">Ø Bewertung</div>
      <div style="font-size: 26px; font-weight: bold; margin-top: 5px; color: #FFD700;"><?php echo $avgRating; ?> <span style="font-size: 14px; color: gray; font-weight: normal;">/ 5</span></div>
    </div>
  </div>

  <a href="add.php" class="btn">➕ Neues Medium hinzufügen</a>

  <form method="GET" action="index.php" style="background: var(--bg-color); padding: 15px; border-radius: 8px; margin: 20px 0; border: 1px solid #ddd;">
    <div style="margin-bottom: 15px; position: relative;">
      <label for="search-input" style="font-weight: bold; margin-right: 5px;">🔍 Echtzeit-Suche & Vorschläge:</label>
      <input type="text" id="search-input" list="titelliste" placeholder="Titel eingeben..." style="width: 100%; max-width: 400px; margin-top: 5px; margin-bottom: 0; padding: 8px 12px;" autocomplete="off">
      <datalist id="titelliste">
        <?php foreach ($mediaItems as $suggestItem): ?>
        <option value="<?php echo htmlspecialchars($suggestItem['title']); ?>">
          <?php endforeach; ?>
      </datalist>
    </div>

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
    <table>
      <thead>
      <tr>
        <th>Cover</th>
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
            <?php if (!empty($item['cover_image'])): ?>
              <img src="<?php echo htmlspecialchars($item['cover_image']); ?>" alt="Cover" style="width: 60px; height: 90px; object-fit: cover; border-radius: 6px; box-shadow: 0 2px 5px rgba(0,0,0,0.2);">
            <?php else: ?>
              <div class="cover-placeholder">Kein Bild</div>
            <?php endif; ?>
          </td>

          <td>
            <a href="detail.php?id=<?php echo $item['id']; ?>">
              <?php echo htmlspecialchars($item['title']); ?>
            </a>
          </td>

          <td><?php echo htmlspecialchars($item['genre']); ?></td>
          <td><?php echo htmlspecialchars($item['description']); ?></td>
          <td style="color: #FFD700; font-size: 18px; letter-spacing: 2px;">
            <?php echo str_repeat('★', $item['rating']) . str_repeat('☆', 5 - $item['rating']); ?>
          </td>
          <td><?php echo htmlspecialchars($item['status']); ?></td>

          <td>
            <a href="edit.php?id=<?php echo $item['id']; ?>">✏️ Bearbeiten</a> |
            <a href="delete.php?id=<?php echo $item['id']; ?>">🗑️ ...</a>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</main>

<script>
  const searchInput = document.getElementById('search-input');
  const tableRows = document.querySelectorAll('table tbody tr');

  searchInput.addEventListener('input', function() {
    const filterValue = searchInput.value.toLowerCase().trim();
    tableRows.forEach(row => {
      const titleCell = row.getElementsByTagName('td')[1];
      if (titleCell) {
        const titleText = titleCell.textContent || titleCell.innerText;
        if (titleText.toLowerCase().indexOf(filterValue) > -1) {
          row.style.display = "";
        } else {
          row.style.display = "none";
        }
      }
    });
  });
</script>
</body>
</html>
