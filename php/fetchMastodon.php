<?php
require 'config.php';

$keyword = urlencode($_POST['keyword']);

$apiUrl = "https://mastodon.social/api/v1/timelines/tag/$keyword?limit=250";


$response = file_get_contents($apiUrl);
$data = json_decode($response, true);

try {
    $pdo = new PDO($dsn, $user, $pass, $options);

    foreach ($data as $post) {
        // Überprüfung für jedes Feld
        $postId = isset($post['id']) && $post['id'] ? $post['id'] : '';
        $createdAt = isset($post['created_at']) && $post['created_at'] ? $post['created_at'] : '';
        $content = isset($post['content']) && $post['content'] ? $post['content'] : '';
        $username = isset($post['account']['username']) && $post['account']['username'] ? $post['account']['username'] : '';
        $displayName = isset($post['account']['display_name']) && $post['account']['display_name'] ? $post['account']['display_name'] : '';

        $cardUrl = isset($post['card']['url']) && $post['card']['url'] ? $post['card']['url'] : '';
        $cardTitle = isset($post['card']['title']) && $post['card']['title'] ? $post['card']['title'] : '';
        $cardDescription = isset($post['card']['description']) && $post['card']['description'] ? $post['card']['description'] : '';
        $cardImage = isset($post['card']['image']) && $post['card']['image'] ? $post['card']['image'] : '';

        // SQL-Statement vorbereiten
        $stmt = $pdo->prepare("INSERT INTO mastodon_posts (post_id, created_at, content, username, display_name, card_url, card_title, card_description, card_image) 
                               VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
                               ON DUPLICATE KEY UPDATE post_id=post_id"); 

        // Daten einfügen
        $stmt->execute([$postId, $createdAt, $content, $username, $displayName, $cardUrl, $cardTitle, $cardDescription, $cardImage]);
    }

    echo json_encode(["message" => count($data) . " Einträge gespeichert"]);

} catch (\PDOException $e) {
    if ($e->errorInfo[1] == 1062) { // Erkennung von doppelten Einträgen
        echo json_encode(["error" => "Doppelter Eintrag gefunden - Speichern abgebrochen."]);
    } else {
        echo json_encode(["error" => "Datenbankfehler: " . $e->getMessage()]);
    }
}
?>
