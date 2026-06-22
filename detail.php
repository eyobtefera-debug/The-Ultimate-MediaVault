<?php
// 1. Datenbank laden
require_once 'db.php';

// 2. ID aus der URL holen
if (!isset($_GET['id'])) {
  header('Location: index.php');
  exit;
}

$id = $_GET['id'];

// 3. Daten zu dieser ID holen
$stmt = $pdo->prepare("SELECT * FROM media WHERE id = ?");
$stmt->execute([$id]);
$item = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$item) {
  header('Location: index.php');
  exit;
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="referrer" content="no-referrer">
  <title>Details: <?php echo htmlspecialchars($item['title']); ?></title>
  <link rel="stylesheet" href="style.css">
</head>
<body>

<button onclick="toggleDarkMode()" class="theme-toggle" id="theme-btn">🌙 Night Mode</button>

<main>
  <h1>🔍 Medien-Details</h1>
  <p style="margin-bottom: 25px;"><a href="index.php" class="btn">⬅️ Zurück zur Übersicht</a></p>

  <div style="border-top: 2px solid var(--primary-color); padding-top: 20px; display: flex; gap: 30px; flex-wrap: wrap;">

    <div style="flex-shrink: 0;">
      <?php if (!empty($item['cover_image'])): ?>
        <img src="<?php echo htmlspecialchars($item['cover_image']); ?>" alt="Cover" style="width: 300px; height: 450px; object-fit: cover; border-radius: 12px; box-shadow: 0 8px 25px rgba(0,0,0,0.4);">
      <?php else: ?>
        <div style="width: 300px; height: 450px; background: #333; color: white; display: flex; align-items: center; justify-content: center; border-radius: 12px; font-weight: bold;">Kein Bild vorhanden</div>
      <?php endif; ?>
    </div>

    <div style="flex-grow: 1; min-width: 300px;">
      <h2 style="font-size: 28px; margin-bottom: 5px;"><?php echo htmlspecialchars($item['title']); ?></h2>
      <p style="color: gray; font-style: italic; margin-top: 0;">Kategorie: <?php echo htmlspecialchars($item['genre'] ?: 'Keine Angabe'); ?></p>

      <div style="margin: 25px 0;">
        <strong style="font-size: 18px;">Beschreibung:</strong>
        <p style="background: var(--bg-color); padding: 15px; border-radius: 6px; min-height: 60px;">
          <?php echo nl2br(htmlspecialchars($item['description'] ?: 'Keine Beschreibung vorhanden.')); ?>
        </p>
      </div>

      <p style="font-size: 18px;">
        <strong>Bewertung:</strong>
        <span style="color: #FFD700; letter-spacing: 2px;">
                  <?php echo str_repeat('★', $item['rating']) . str_repeat('☆', 5 - $item['rating']); ?>
              </span>
      </p>

      <p style="font-size: 18px;">
        <strong>Status:</strong>
        <?php if ($item['status'] == 'gesehen'): ?>
          <span style="color: #2ecc71; font-weight: bold;">✅ Gesehen</span>
        <?php else: ?>
          <span style="color: #e74c3c; font-weight: bold;">❌ Nicht gesehen</span>
        <?php endif; ?>
      </p>
    </div>
  </div>

  <div style="margin-top: 30px; border-top: 1px solid #eee; padding-top: 20px;">
    <a href="edit.php?id=<?php echo $item['id']; ?>" class="btn" style="background-color: #27ae60;">✏️ Dieses Medium bearbeiten</a>
  </div>
</main>

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

</body>
</html>
