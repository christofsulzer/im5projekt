<?php
// Set the content type to JSON for the response
header('Content-Type: application/json');

// Include external database configuration
require 'config.php';

try {
    // Establish a connection to the database
    $pdo = new PDO($dsn, $user, $pass, $options);

    // SQL query to fetch all posts in descending order by 'created_at'
    $sql = "SELECT * FROM mastodon_posts ORDER BY created_at DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    // Fetch the data as an associative array
    $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Check if 'card_image' has a value and adjust if necessary
    foreach ($posts as &$post) {
        if (empty($post['card_image'])) {
            $post['card_image'] = null;
        }
    }

    // Return the data in JSON format
    echo json_encode($posts);

} catch (\PDOException $e) {
    // Return an error message if there are problems retrieving the data
    echo json_encode(['error' => 'Daten konnten nicht abgerufen werden: ' . $e->getMessage()]);
}
?>
