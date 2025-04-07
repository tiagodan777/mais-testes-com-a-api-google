<?php
require_once 'config.php';

$path = 'videos/';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $arguments['titulo'] = $_POST['titulo'];
    $arguments['descricao'] = $_POST['descricao'];
    $arguments['tags'] = $_POST['tags'];
    $arguments['visibilidade'] = $_POST['visibilidade'];

    if (empty($_FILES['file']['name'])) {
        echo "Seleciona um arquivo apra fazer upload";
    }

    $fileName = $path . basename($_FILES['file']['name']);

    if (move_uploaded_file($_FILES['file']['tmp_name'], $fileName)) {
        $sql = "INSERT INTO video (titulo, descricao, tags, visibilidade, file_name)
                VALUES (:titulo,:descricao, :tags, :visibilidade, :file_name);";
        $statement = $pdo->prepare($sql);
        $statement->execute($arguments);
    }

    $id = $pdo->lastInsertId();

    $state = mt_rand();
    $client->setState($state);
    $googleOathURL = $client->createAuthUrl();

    header("Location:  $googleOathURL");
}

header('Location: index.ph')