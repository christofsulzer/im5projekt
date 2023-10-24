<?php
header('Content-Type: application/json');

// Externe Datenbankkonfiguration einbinden 
require 'config.php';

try {
    // Verbindung zur Datenbank herstellen
    $pdo = new PDO($dsn, $user, $pass, $options);

    // SQL-Abfrage, um alle Posts in absteigender Reihenfolge nach 'created_at' zu holen
    $sql = "SELECT * FROM mastodon_posts ORDER BY created_at DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    // Daten als assoziatives Array abrufen
    $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Überprüfen, ob card_image einen Wert hat, und gegebenenfalls anpassen
    foreach ($posts as &$post) {
        if (empty($post['card_image'])) {
            $post['card_image'] = null;
        }
    }

    // Daten im JSON-Format zurückgeben
    echo json_encode($posts);

} catch (\PDOException $e) {
    // Fehlermeldung zurückgeben, wenn es Probleme beim Abrufen der Daten gibt
    echo json_encode(['error' => 'Daten konnten nicht abgerufen werden: ' . $e->getMessage()]);
}
?>
