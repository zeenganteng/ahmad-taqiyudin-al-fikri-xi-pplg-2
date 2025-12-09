<?php
require "config.php";
require "functions.php";

if (!is_logged_in()) {
    echo json_encode([
        "success" => false,
        "message" => "not_logged_in"
    ]);
    exit;
}

$photo_id = intval($_POST['id']);
$user_id = $_SESSION['user_id'];

// cek apakah user sudah like
$cek = mysqli_query($koneksi, "
    SELECT * FROM likes 
    WHERE user_id = $user_id AND photo_id = $photo_id
");

if (mysqli_num_rows($cek) > 0) {

    // UNLIKE
    mysqli_query($koneksi, "
        DELETE FROM likes 
        WHERE user_id = $user_id AND photo_id = $photo_id
    ");
    $liked = false;

} else {

    // LIKE
    mysqli_query($koneksi, "
        INSERT INTO likes(user_id, photo_id)
        VALUES($user_id, $photo_id)
    ");
    $liked = true;
}

echo json_encode([
    "success" => true,
    "liked" => $liked,
    "likes" => count_likes($photo_id)
]);
