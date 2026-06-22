<?php

require_once 'db.php';

// Wir sagen dem Browser, dass wir reine JSON-Daten senden
header('Content-Type: application/json');

$suggestions = [];

// Prüfen, ob ein Suchbegriff übergeben wurde
if (isset($_GET['term']) && trim($_GET['term']) !== '') {
  $searchTerm = trim($_GET['term']) . '%'; // Das % sorgt dafür, dass alle Titel gefunden werden, die so BEGINNEN

  try {
    // SQL-Abfrage mit LIKE für die Vorschläge
    $stmt = $pdo->prepare("SELECT title FROM media WHERE title LIKE ? LIMIT 5");
    $stmt->execute([$searchTerm]);
    $suggestions = $stmt->fetchAll(PDO::FETCH_COLUMN); // Holt nur ein flaches Array der Titel
  } catch (PDOException $e) {
    // Fehler ignorieren oder leeres Array senden
  }
}

// Array in JSON umwandeln und ausgeben
echo json_encode($suggestions);
exit;
