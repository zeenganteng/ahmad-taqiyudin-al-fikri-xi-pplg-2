<?php
session_start();
require "config.php";
require "functions.php";

// cek id foto
$photo_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($photo_id <= 0) {
    die("ID foto tidak valid.");
}

// ambil data foto
$photo_query = mysqli_query($koneksi, "
    SELECT photos.*, users.username 
    FROM photos 
    JOIN users ON users.id = photos.user_id 
    WHERE photos.id = $photo_id
");

if (!$photo_query || mysqli_num_rows($photo_query) == 0) {
    die("Foto tidak ditemukan!");
}

$data = mysqli_fetch_assoc($photo_query);

// ambil komentar
$kom_query = mysqli_query($koneksi, "
    SELECT comments.*, users.username
    FROM comments
    JOIN users ON users.id = comments.user_id
    WHERE photo_id = $photo_id
    ORDER BY id DESC
");

// tambah komentar
if (isset($_POST['comment']) && is_logged_in()) {
    $c = mysqli_real_escape_string($koneksi, $_POST['comment']);
    $uid = user_id();

    mysqli_query($koneksi, "
        INSERT INTO comments(photo_id, user_id, comment)
        VALUES($photo_id, $uid, '$c')
    ");

    header("Location: photo.php?id=" . $photo_id);
    exit;
}

// jumlah like
$countLike = mysqli_fetch_assoc(mysqli_query($koneksi, "
    SELECT COUNT(*) AS total FROM likes WHERE photo_id=$photo_id
"))['total'];

// apakah user sudah like
$already = 0;
if (is_logged_in()) {
    $uid = user_id();
    $cek = mysqli_query($koneksi, "
        SELECT * FROM likes WHERE photo_id=$photo_id AND user_id=$uid
    ");
    $already = mysqli_num_rows($cek);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($data['title']) ?></title>
    <link rel="stylesheet" href="surya.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        .btn-back, .btn-like, .btn-download, .btn-share, .btn-copy {
            display: inline-block;
            margin: 5px 5px 5px 0;
            padding: 8px 12px;
            border-radius: 5px;
            text-decoration: none;
            color: #fff;
            cursor: pointer;
        }
        .btn-back { background-color: #6c757d; }
        .btn-back:hover { background-color: #5a6268; }
        .btn-like { background-color: #e74c3c; border: none; }
        .btn-like:hover { background-color: #c0392b; }
        .btn-download { background-color: #28a745; }
        .btn-download:hover { background-color: #218838; }
        .btn-share { background-color: #007bff; }
        .btn-share:hover { background-color: #0056b3; }
        .btn-copy { background-color: #17a2b8; border: none; }
        .btn-copy:hover { background-color: #138496; }
        .photo-detail { max-width: 700px; margin: 20px auto; font-family: Arial, sans-serif; }
        .detail-img { width: 100%; max-height: 400px; object-fit: cover; border-radius: 8px; }
        textarea { width: 100%; height: 60px; margin-bottom: 5px; padding: 5px; }
        .comment-box { border: 1px solid #ddd; padding: 8px; margin-bottom: 5px; border-radius: 5px; background: #f9f9f9; }
    </style>
</head>
<body>

<div class="photo-detail">

    <!-- Tombol Back -->
    <a href="index.php" class="btn-back">← Kembali ke Galeri</a>

    <h2><?= htmlspecialchars($data['title']) ?></h2>

    <img src="uploads/<?= htmlspecialchars($data['filename']) ?>" class="detail-img" alt="<?= htmlspecialchars($data['title']) ?>">

    <p><?= htmlspecialchars($data['description']) ?></p>
    <small>Upload oleh: <?= htmlspecialchars($data['username']) ?></small><br><br>

    <!-- tombol like -->
    <?php if (is_logged_in()): ?>
        <button id="likeBtn" class="btn-like" data-id="<?= $photo_id ?>">
            ❤️ Like (<?= $countLike ?>)
        </button>
        <span id="likeText"><?= ($already ? "Kamu sudah menyukai foto ini." : "") ?></span>
    <?php else: ?>
        <a href="login.php">Login untuk like</a>
    <?php endif; ?>

    <br><br>

    <!-- tombol download -->
    <a href="download.php?file=<?= htmlspecialchars($data['filename']) ?>" class="btn-download">
        ⬇ Download Foto
    </a>

    <br><br>

    <!-- share & salin link -->
    <a href="https://wa.me/?text=<?= urlencode("Lihat foto keren ini: http://localhost/galery_fikri/photo.php?id=$photo_id") ?>" class="btn-share" target="_blank">Share ke WhatsApp</a>

    <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode("http://localhost/galery_fikri/photo.php?id=$photo_id") ?>" class="btn-share" target="_blank">Share ke Facebook</a>

    <a href="https://twitter.com/intent/tweet?text=Foto keren ini: <?= urlencode("http://localhost/galery_fikri/photo.php?id=$photo_id") ?>" class="btn-share" target="_blank">Share ke Twitter</a>

    <button id="copyLink" class="btn-copy">📋 Salin Link</button>

    <hr><br>

    <!-- komentar -->
    <h3>Komentar</h3>

    <?php if (is_logged_in()): ?>
    <form method="post">
        <textarea name="comment" required></textarea><br>
        <button type="submit">Kirim</button>
    </form>
    <?php else: ?>
        <a href="login.php">Login untuk komentar.</a>
    <?php endif; ?>

    <br>

    <?php while ($k = mysqli_fetch_assoc($kom_query)): ?>
        <div class="comment-box">
            <b><?= htmlspecialchars($k['username']) ?>:</b><br>
            <?= htmlspecialchars($k['comment']) ?>
        </div>
    <?php endwhile; ?>

</div>

<script>
$("#likeBtn").click(function(){
    let id = $(this).data("id");
    $.post("like.php", { photo_id:id }, function(res){
        let data = JSON.parse(res);
        $("#likeBtn").html("❤️ Like (" + data.likes + ")");
        $("#likeText").html(data.message);
    });
});

// tombol salin link
$("#copyLink").click(function(){
    let link = "<?= "http://localhost/galery_fikri/photo.php?id=$photo_id" ?>";
    navigator.clipboard.writeText(link).then(function(){
        alert("Link foto telah disalin!");
    }, function(err){
        alert("Gagal menyalin link: " + err);
    });
});
</script>

</body>
</html>
