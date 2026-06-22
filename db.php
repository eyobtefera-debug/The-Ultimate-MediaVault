<?php
//Verbindungsdaten
$host = "127.0.0.1";
$port = '3307';
$dbname = 'media_vault';
$username = 'root';
$password = 'Eyob2005_';

$dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";

try {
  $pdo = new PDO($dsn, $username, $password);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
  die("Verbindung zur Datenbank fehlgeschlagen: " . $e->getMessage());
}
?>
