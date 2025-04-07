<!DOCTYPE html>
<html lang="pt-pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teste de Upload</title>
</head>
<body>
    <main>
        <div>
            <form action="upload.php" method="post" enctype="multipart/form-data">
                <label for="file">Ficheiro</label> <input type="file" name="file" id="file"><br>
                <label for="titulo">Título</label> <input type="text" name="titulo" id="titulo"><br>
                <label for="descricao">Descrição</label> <input type="text" name="descricao" id="descricao"><br>
                <label for="tags">Tags</label> <input type="text" name="tags" id="tags"><br>
                <label for="visibilidade">Visibilidade</label> 
                Pública <input type="radio" name="visibilidade" id="visibilidade" value="publica">
                Privada <input type="radio" name="visibilidade" id="visibilidade" value="privada">
                <input type="submit" value="Enviar">
            </form>
        </div>
    </main>
</body>
</html>