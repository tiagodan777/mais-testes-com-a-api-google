<?php
require 'config.php';

$tokenSessionKey = 'token-' . $client->prepareScopes();

if (isset($_GET['code'])) {
    $client->fetchAccessTokenWithAuthCode($_GET['code']);
    $token = $client->getAccessToken();
    header('Loation: ' . REDIRECT_URL);
}