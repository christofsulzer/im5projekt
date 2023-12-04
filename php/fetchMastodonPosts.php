<?php
// Include the configuration file
require 'config.php';

// Encode the keyword received from the POST request for URL usage
$keyword = urlencode($_POST['keyword']);

// Define the API URL with the encoded keyword and a limit
$apiUrl = "https://mastodon.social/api/v1/timelines/tag/$keyword?limit=250";

// Fetch the response from the API and decode the JSON data
$response = file_get_contents($apiUrl);
$data = json_decode($response, true);

try {
    // Establish a new database connection using PDO
    $pdo = new PDO($dsn, $user, $pass, $options);

    // Loop through each post in the fetched data
    foreach ($data as $post) {
        // Check and set each field, using ternary operators for validation
        $postId = isset($post['id']) && $post['id'] ? $post['id'] : '';
        $createdAt = isset($post['created_at']) && $post['created_at'] ? $post['created_at'] : '';
        $content = isset($post['content']) && $post['content'] ? $post['content'] : '';
        $username = isset($post['account']['username']) && $post['account']['username'] ? $post['account']['username'] : '';
        $displayName = isset($post['account']['display_name']) && $post['account']['display_name'] ? $post['account']['display_name'] : '';

        $cardUrl = isset($post['card']['url']) && $post['card']['url'] ? $post['card']['url'] : '';
        $cardTitle = isset($post['card']['title']) && $post['card']['title'] ? $post['card']['title'] : '';
        $cardDescription = isset($post['card']['description']) && $post['card']['description'] ? $post['card']['description'] : '';
        $cardImage = isset($post['card']['image']) && $post['card']['image'] ? $post['card']['image'] : '';

        // Prepare the SQL statement for inserting data into the database
        $stmt = $pdo->prepare("INSERT INTO mastodon_posts (post_id, created_at, content, username, display_name, card_url, card_title, card_description, card_image) 
                               VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
                               ON DUPLICATE KEY UPDATE post_id=post_id");

        // Execute the SQL statement with the gathered data
        $stmt->execute([$postId, $createdAt, $content, $username, $displayName, $cardUrl, $cardTitle, $cardDescription, $cardImage]);
    }

    // Return a JSON response indicating the number of entries saved
    echo json_encode(["message" => count($data) . " Einträge gespeichert"]);

} catch (\PDOException $e) {
    // Catch block to handle database connection errors
    if ($e->errorInfo[1] == 1062) { // Check for duplicate entry error
        echo json_encode(["error" => "Doppelter Eintrag gefunden - Speichern abgebrochen."]);
    } else {
        // General database error handling
        echo json_encode(["error" => "Datenbankfehler: " . $e->getMessage()]);
    }
}
?>
