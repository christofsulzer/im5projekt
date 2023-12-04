<?php
// Include the configuration file
require 'config.php';

try {
    // SQL query to select all articles from the database, ordered by publication date (newest first)
    $stmt = $pdo->prepare("SELECT * FROM newsdata_articles order by pubDate desc");
    $stmt->execute();

    // Fetch all the articles
    $articles = $stmt->fetchAll();

    // Check if any articles were found in the database
    if ($articles) {
        // If articles are found, encode and return them as JSON
        echo json_encode(['results' => $articles]);
    } else {
        // If no articles are found, return a message indicating this
        echo json_encode(['message' => 'Keine Artikel gefunden.']);
    }
} catch (\PDOException $e) {
    // If a database error occurs, return an error message
    echo json_encode(["error" => "Datenbankfehler: " . $e->getMessage()]);
}
?>
