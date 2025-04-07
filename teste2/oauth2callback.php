<?php
require_once 'vendor/autoload.php';

session_start();

$client = new Google_Client();
$client->setAuthConfig('client_secret.json');
$client->addScope(Google_Service_YouTube::YOUTUBE_UPLOAD);
$client->setAccessType('offline');

// Defina a URI de redirecionamento conforme registrado no Google Cloud Console
$client->setRedirectUri('http://localhost:8888/mais-testes-com-a-api-google/teste2/oauth2callback.php');

if (!isset($_GET['code'])) {
    // Gere o URL de autenticação e redirecione o usuário
    $authUrl = $client->createAuthUrl();
    header('Location: ' . filter_var($authUrl, FILTER_SANITIZE_URL));
    exit;
} else {
    // Troque o código de autorização por um token de acesso
    $authCode = $_GET['code'];
    $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);
    
    if (array_key_exists('error', $accessToken)) {
        throw new Exception(join(', ', $accessToken));
    }

    // Salve o token de acesso em um arquivo para uso posterior
    file_put_contents('token.json', json_encode($accessToken));
    echo "Autenticação concluída com sucesso. Você pode fechar esta aba.";
}