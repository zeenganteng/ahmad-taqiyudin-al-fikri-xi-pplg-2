<?php
require_once "config.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/* ============================
   AUTH FUNCTIONS
============================ */

function is_logged_in() {
    return isset($_SESSION['user_id']);
}

function user_id() {
    return $_SESSION['user_id'] ?? 0;
}

function current_user() {
    global $koneksi;

    if (!is_logged_in()) return null;

    $id = intval($_SESSION['user_id']);
    $res = mysqli_query($koneksi, "SELECT * FROM users WHERE id=$id");

    return mysqli_fetch_assoc($res);
}

/* ============================
   HELPER
============================ */

function esc($txt) {
    return htmlspecialchars($txt, ENT_QUOTES, 'UTF-8');
}

/* ============================
   LIKE SYSTEM
============================ */

function count_likes($photo_id) {
    global $koneksi;

    $photo_id = intval($photo_id);

    $q = mysqli_query($koneksi, "
        SELECT COUNT(*) AS c 
        FROM likes 
        WHERE photo_id = $photo_id
    ");

    $d = mysqli_fetch_assoc($q);
    return $d['c'] ?? 0;
}

function user_liked($photo_id) {
    global $koneksi;

    if (!is_logged_in()) return false;

    $uid = $_SESSION['user_id'];
    $pid = intval($photo_id);

    $q = mysqli_query($koneksi, "
        SELECT id FROM likes 
        WHERE user_id = $uid AND photo_id = $pid
    ");

    return mysqli_num_rows($q) > 0;
}
?>
