
<?php
include('config.php');

$apiKey = $google_api_key;
$content = json_decode(file_get_contents('php://input'), true);

$apiUrl = "https://language.googleapis.com/v1/documents:analyzeSentiment?key=$apiKey";
$jsonData = json_encode([
    'document' => [
        'content' => $content['text'],
        'type' => 'PLAIN_TEXT'
    ],
    'encodingType' => 'UTF8'
]);

// Initialize cURL session
$ch = curl_init($apiUrl);

// Set cURL options
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);

// Execute cURL session and fetch response
$response = curl_exec($ch);

// Check for cURL errors
if (curl_errno($ch)) {
    echo 'cURL error: ' . curl_error($ch);
}

// Close cURL session
curl_close($ch);

echo $response;
?>
