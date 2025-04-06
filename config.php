<?php

$type = 'mysql';
$server = 'localhost';
$db = 'mais-testes-google-api';
$port = '8889';
$charset = 'utf8mb4';
$username = 'reisupremo';
$password = 'Tiago1234';
$dsn = "$type:host=$server;dbname=$db;port=$port;charset=$charset";

$pdo = new PDO($dsn, $username, $password);

use Google\Service\YouTube;

require 'vendor/autoload.php';

define('OAUTH_CLIENT_ID', '');
define('OAUTH_CLIENT_SECRET', '');
define('REDIRECT_URL', 'http://localhost:8888/mais-testes-com-a-api-google/youtube.php');

$client = new Google_Client();
$client->setClientId(OAUTH_CLIENT_ID);
$client->setClientSecret(OAUTH_CLIENT_SECRET);
$client->setScopes('https://www.googleapis.com/auth/youtube');
$client->setRedirectUri(REDIRECT_URL);

$youtube = new YouTube($client);


