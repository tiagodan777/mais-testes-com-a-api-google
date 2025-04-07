<!-- index.php -->
<form action="upload_resumable.php" method="POST" enctype="multipart/form-data">
  <label>Título:</label><br>
  <input type="text" name="title"><br><br>

  <label>Descrição:</label><br>
  <textarea name="description"></textarea><br><br>

  <label>Tags (separadas por vírgula):</label><br>
  <input type="text" name="tags"><br><br>

  <label>Vídeo:</label><br>
  <input type="file" name="video"><br><br>

  <button type="submit">Enviar para YouTube</button>
</form>