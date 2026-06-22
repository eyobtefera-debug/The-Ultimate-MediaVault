<?php
// 1. Datenbankverbindung einbinden
require_once 'db.php';

try {
  // 2. Medien aus der Datenbank abfragen (alphabetisch nach Titel sortiert)
  $stmt = $pdo->query("SELECT * FROM media ORDER BY title ASC");
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
</head>
<body>

<header>
  <h1> THE ULTIMATE MediaVault</h1>
  <nav>
    <a href="add.php" style="font-weight: bold;">➕ Neues Medium hinzufügen</a>
  </nav>
</header>

<main>
  <h2>Deine Mediensammlung</h2>

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
          <td><strong><?php echo htmlspecialchars($item['title']); ?></strong></td>
          <td><?php echo htmlspecialchars($item['genre']); ?></td>
          <td><?php echo htmlspecialchars($item['description']); ?></td>
          <td>
            <?php echo str_repeat('⭐', $item['rating']); ?>
          </td>
          <td>
            <?php echo $item['status'] === 'gesehen' ? '✅ Gesehen' : '❌ Nicht gesehen'; ?>
          </td>
          <td>
            <a href="edit.php?id=<?php echo $item['id']; ?>">✏️ Bearbeiten</a> |
            <a href="delete.php?id=<?php echo $item['id']; ?>" onclick="return confirm('Möchtest du dieses Medium wirklich löschen?');">🗑️ Löschen</a>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</main>

</body>
</html>
