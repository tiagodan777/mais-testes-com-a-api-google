<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require __DIR__ . '/vendor/autoload.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$client = new Google\Client();
$client->setAuthConfig([
    'client_id' => '34321114946-1asfsueoamnacg7ee90nq96165l010ij.apps.googleusercontent.com', // Substitua pelo seu ID do cliente
    'client_secret' => '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', // Substitua pelo seu Secret do cliente
]);
$client->setRedirectUri('http://localhost:8888/mais-testes-com-a-api-google/teste3/oauth2callback.php');

if (! isset($_GET['code'])) {
    $auth_url = $client->createAuthUrl();
    header('Location: ' . filter_var($auth_url, FILTER_SANITIZE_URL));
} else {
    $client->fetchAccessTokenWithAuthCode($_GET['code']);
    $access_token = $client->getAccessToken();

    if (!file_exists(dirname('token.json'))) {
        mkdir(dirname('token.json'), 0777, true);
    }
    file_put_contents('token.json', json_encode($access_token));

    echo 'Token de acesso salvo. Você pode fechar esta janela.';
}
?>