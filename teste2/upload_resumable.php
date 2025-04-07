<?php
require_once 'vendor/autoload.php';

// === 1. Carregar o vídeo e dados enviados pelo formulário ===
$title = $_POST['title'] ?? 'Sem título';
$description = $_POST['description'] ?? '';
$tags = isset($_POST['tags']) ? explode(',', $_POST['tags']) : [];
$videoPath = $_FILES['video']['tmp_name'];

if (!file_exists($videoPath)) {
    die('Erro: vídeo não enviado.');
}

// === 2. Autenticar o cliente Google ===
$client = new Google_Client();
$client->setAuthConfig('client_secret.json');
$client->addScope(Google_Service_YouTube::YOUTUBE_UPLOAD);
$client->setAccessType('offline');

$tokenPath = 'token.json';
if (!file_exists($tokenPath)) {
    header('Location: oauth2callback.php');
    exit;
}

$accessToken = json_decode(file_get_contents($tokenPath), true);
$client->setAccessToken($accessToken);

// Renovar token se expirado
if ($client->isAccessTokenExpired()) {
    if ($client->getRefreshToken()) {
        $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
        file_put_contents($tokenPath, json_encode($client->getAccessToken()));
    } else {
        die('Token expirado e sem refresh token.');
    }
}

// === 3. Preparar o serviço YouTube ===
$youtube = new Google_Service_YouTube($client);

// === 4. Criar metadados do vídeo ===
$snippet = new Google_Service_YouTube_VideoSnippet();
$snippet->setTitle($title);
$snippet->setDescription($description);
$snippet->setTags($tags);
$snippet->setCategoryId("22"); // "People & Blogs"

$status = new Google_Service_YouTube_VideoStatus();
$status->setPrivacyStatus('public');

$video = new Google_Service_YouTube_Video();
$video->setSnippet($snippet);
$video->setStatus($status);

// === 5. Preparar o upload resumable ===
$request = $youtube->videos->insert(
    "status,snippet",
    $video,
    ["uploadType" => "resumable"]
);

$chunkSize = 1024 * 1024 * 2; // 2MB
$media = new Google_Http_MediaFileUpload(
    $client,
    $request,
    'video/*',
    null,
    true,
    $chunkSize
);
$media->setFileSize(filesize($videoPath));

// === 6. Upload em partes ===
$handle = fopen($videoPath, 'rb');
$status = false;
while (!$status && !feof($handle)) {
    $chunk = fread($handle, $chunkSize);
    $status = $media->nextChunk($chunk);
}
fclose($handle);

echo "Vídeo enviado com sucesso! ID: " . $status['id'];