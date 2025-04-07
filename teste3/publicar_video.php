<?php

require __DIR__ . '/vendor/autoload.php';

// Configurações
$CLIENT_ID = '34321114946-1asfsueoamnacg7ee90nq96165l010ij.apps.googleusercontent.com'; // Substitua pelo seu ID do cliente
$CLIENT_SECRET = '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'; // Substitua pelo seu Secret do cliente
$REDIRECT_URI = 'http://localhost:8888/mais-testes-com-a-api-google/teste3/oauth2callback.php';

// Caminho para o arquivo de token
$TOKEN_PATH = 'token.json';

$VIDEO_PATH = 'video-2.mp4';
$VIDEO_TITLE = 'Vídeo de Teste';
$VIDEO_DESCRIPTION = 'Esta é uma descrição de teste do vídeo.';
$VIDEO_CATEGORY_ID = '22'; // Pessoas e Blogs
$VIDEO_TAGS = ['teste', 'vídeo de teste', 'youtube api'];
$VIDEO_PRIVACY_STATUS = 'unlisted';

$client = new Google\Client();
$client->setAuthConfig([
    'client_id' => $CLIENT_ID,
    'client_secret' => $CLIENT_SECRET,
]);
$client->setRedirectUri($REDIRECT_URI);
$client->addScope(Google\Service\YouTube::YOUTUBE_UPLOAD);
$client->addScope(Google\Service\YouTube::YOUTUBE);

// Carrega o token de acesso do arquivo, se existir
if (file_exists($TOKEN_PATH)) {
    $accessToken = json_decode(file_get_contents($TOKEN_PATH), true);
    $client->setAccessToken($accessToken);
}

// Se não houver token ou se ele estiver expirado, solicita um novo
if ($client->isAccessTokenExpired()) {
    if ($client->getRefreshToken()) {
        $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
    } else {
        $authUrl = $client->createAuthUrl();
        printf("Abra este link em seu navegador:\n%s\n", $authUrl);
        print 'Digite o código de autorização: ';
        $authCode = trim(fgets(STDIN));

        $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);
        $client->setAccessToken($accessToken);

        if (array_key_exists('error', $accessToken)) {
            throw new Exception(join(', ', $accessToken));
        }
    }

    if (!file_exists(dirname($TOKEN_PATH))) {
        mkdir(dirname($TOKEN_PATH), 0777, true);
    }
    file_put_contents($TOKEN_PATH, json_encode($client->getAccessToken()));
}

$youtube = new Google\Service\YouTube($client);

try {
    // Define as informações do vídeo
    $video = new Google\Service\YouTube\Video();
    $videoSnippet = new Google\Service\YouTube\VideoSnippet();
    $videoSnippet->setTitle($VIDEO_TITLE);
    $videoSnippet->setDescription($VIDEO_DESCRIPTION);
    $videoSnippet->setCategoryId($VIDEO_CATEGORY_ID);
    $videoSnippet->setTags($VIDEO_TAGS);
    $videoStatus = new Google\Service\YouTube\VideoStatus();
    $videoStatus->setPrivacyStatus($VIDEO_PRIVACY_STATUS);
    $video->setSnippet($videoSnippet);
    $video->setStatus($videoStatus);

    // Crie a requisição para inserir o vídeo
    $request = $youtube->videos->insert('snippet,status', $video, ['uploadType' => 'resumable']);

    // Define o conteúdo do vídeo para o upload
    $media = new Google\Http\MediaFileUpload(
        $client,
        $request,
        'video/*',
        file_get_contents($VIDEO_PATH),
        false
    );
    $media->setFileSize(filesize($VIDEO_PATH));
    $chunkSizeBytes = 1 * 1024 * 1024; // 1MB
    $media->setChunkSize($chunkSizeBytes);
    $media->setResumable(true);
    $media->setMimeType('video/*'); // Adicionando explicitamente o tipo MIME

    // Execute o upload
    $insert = $youtube->videos->insert(
        'snippet,status',
        $video,
        ['mediaUpload' => $media, 'uploadType' => 'resumable'] // Reafirmando o tipo de upload
    );

    printf("Vídeo enviado com sucesso! ID do vídeo: %s\n", $insert['id']);

} catch (Google\Service\Exception $e) {
    printf("Ocorreu um erro ao enviar o vídeo: %s\n", $e->getMessage());
} catch (Exception $e) {
    printf("Ocorreu um erro: %s\n", $e->getMessage());
}

?>