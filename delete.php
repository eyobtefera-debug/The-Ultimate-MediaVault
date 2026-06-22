<?php
// 1. Datenbankverbindung laden
require_once 'db.php';

// 2. Prüfen, ob eine ID in der URL übergeben wurde (z.B. delete.php?id=2)
if (isset($_GET['id'])) {
  $id = $_GET['id'];

  try {
    // 3. Den Datensatz sicher löschen
    $stmt = $pdo->prepare("DELETE FROM media WHERE id = ?");

    $stmt->execute([$id]);

  } catch (PDOException $e) {
    // Fehler anzeigen, falls was schiefgeht
    die("Fehler beim Löschen: " . $e->getMessage());
  }
}

// 4. Den User sofort wieder zurück zur Übersicht schicken
header('Location: index.php');
exit; // Skript sauber beenden
?>
