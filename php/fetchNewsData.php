<?php
// Include the config file
require 'config.php';

// Encode the search term received from the POST request for URL usage
$searchTerm = urlencode($_POST['searchTerm']);

// Define the API URL with the encoded search term
$apiUrl = "https://newsdata.io/api/1/news?apikey=pub_31821417b8aa93685c0073653205b6e91fac4&q=$searchTerm";

// Fetch the response from the API and decode the JSON data
$response = file_get_contents($apiUrl);
$data = json_decode($response, true);

// Check if the API call was successful
if ($data['status'] == 'success') {
    // Loop through each article in the fetched data
    foreach ($data['results'] as $article) {
        // Prepare the SQL statement for inserting data into the database
        $stmt = $pdo->prepare("INSERT INTO newsdata_articles 
        (article_id, title, link, description, content, pubDate, image_url, source_id, source_priority, country, category, language) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?) 
        ON DUPLICATE KEY UPDATE title=VALUES(title), link=VALUES(link), description=VALUES(description), 
        content=VALUES(content), pubDate=VALUES(pubDate), image_url=VALUES(image_url), 
        source_id=VALUES(source_id), source_priority=VALUES(source_priority), country=VALUES(country), 
        category=VALUES(category), language=VALUES(language)");

        // Execute the SQL statement with the gathered data
        $stmt->execute([
            $article['article_id'], $article['title'], $article['link'], $article['description'], 
            $article['content'], $article['pubDate'], $article['image_url'], $article['source_id'], 
            $article['source_priority'], implode(",", $article['country']), implode(",", $article['category']), $article['language']
        ]);
    }

    // Return a JSON response indicating successful article storage
    echo json_encode(["message" => "Artikel erfolgreich gespeichert!"]);
} else {
    // Return a JSON response indicating no articles were found
    echo json_encode(["message" => "Keine Artikel gefunden."]);
}
?>
