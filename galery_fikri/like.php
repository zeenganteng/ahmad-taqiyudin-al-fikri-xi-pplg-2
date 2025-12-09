<?php
require "config.php";
require "functions.php";

if (!is_logged_in()) {
    echo json_encode([
        "message" => "Login untuk menyukai.",
        "likes" => 0
    ]);
    exit;
}

$pid = intval($_POST['photo_id']);
$uid = user_id();

// cek apakah sudah like
$cek = mysqli_query($koneksi, "
    SELECT * FROM likes WHERE photo_id=$pid AND user_id=$uid
");

if (mysqli_num_rows($cek) > 0) {
    // sudah like → hapus
    mysqli_query($koneksi,"
        DELETE FROM likes WHERE photo_id=$pid AND user_id=$uid
    ");
    $msg = "Like dibatalkan.";
} else {
    // belum → tambahkan like
    mysqli_query($koneksi,"
        INSERT INTO likes(photo_id, user_id) VALUES($pid, $uid)
    ");
    $msg = "Kamu menyukai foto ini.";
}

// hitung total like
$total = mysqli_fetch_assoc(mysqli_query($koneksi,"
    SELECT COUNT(*) AS ttl FROM likes WHERE photo_id=$pid
"))['ttl'];

echo json_encode([
    "message" => $msg,
    "likes" => $total
]);
?>
