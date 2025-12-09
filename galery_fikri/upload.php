<?php
require "config.php";
require "functions.php";
if (!is_logged_in()) { header("Location: login.php"); exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $desc  = $_POST['description'];

    $file = $_FILES['photo']['name'];
    $tmp  = $_FILES['photo']['tmp_name'];

    $ext = pathinfo($file, PATHINFO_EXTENSION);
    $new = time() . "_" . rand(1000,9999) . "." . $ext;

    move_uploaded_file($tmp, "uploads/" . $new);

    $uid = $_SESSION['user_id'];

    mysqli_query($koneksi, "
        INSERT INTO photos(user_id, title, filename, description)
        VALUES($uid, '$title', '$new', '$desc')
    ");

    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head><title>Upload Foto</title></head>
<link rel="stylesheet" href="surya.css">
<body>

<h2>Upload Foto</h2>

<form method="post" enctype="multipart/form-data">
    <label>Judul</label><br>
    <input name="title"><br><br>

    <label>Deskripsi</label><br>
    <textarea name="description"></textarea><br><br>

    <label>Foto</label><br>
    <input type="file" name="photo"><br><br>

    <button>Upload</button>
</form>

</body>
</html>
