<?php
$keyword = $_POST['keyword'];
$apiUrl = "https://mastodon.social/api/v1/timelines/tag/$keyword?limit=2";

$response = file_get_contents($apiUrl);
$data = json_decode($response, true);

require 'config.php';  // Externe Datenbankkonfiguration

try {
    $pdo = new PDO($dsn, $user, $pass, $options);

    foreach ($data as $post) {
        $post_id = $post['id'];

        // Überprüfen, ob der Post bereits in der Datenbank existiert
        $checkSql = "SELECT post_id FROM mastodon_posts WHERE post_id = :post_id";
        $checkStmt = $pdo->prepare($checkSql);
        $checkStmt->execute(['post_id' => $post_id]);

        if ($checkStmt->fetch()) {
            echo "Doppelter Eintrag gefunden - Speichern abgebrochen.";
            exit;  // Beendet das Skript
        }

        $created_at = $post['created_at'];
        $content = $post['content'];
        $username = $post['account']['username'];
        $display_name = $post['account']['display_name'];
        $card_url = $post['card']['url'];
        $card_title = $post['card']['title'];
        $card_description = $post['card']['description'];
        $card_image = $post['card']['image'];

        $sql = "INSERT INTO mastodon_posts (post_id, created_at, content, username, display_name, card_url, card_title, card_description, card_image) 
            VALUES (:post_id, :created_at, :content, :username, :display_name, :card_url, :card_title, :card_description, :card_image)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'post_id' => $post_id,
            'created_at' => $created_at,
            'content' => $content,
            'username' => $username,
            'display_name' => $display_name,
            'card_url' => $card_url,
            'card_title' => $card_title,
            'card_description' => $card_description,
            'card_image' => $card_image
        ]);
    }

    echo "Posts gespeichert!";
    header("Location: ../index.html");
    exit;

} catch (\PDOException $e) {
    echo "Datenbankfehler: " . $e->getMessage();
    exit;
}
?>
