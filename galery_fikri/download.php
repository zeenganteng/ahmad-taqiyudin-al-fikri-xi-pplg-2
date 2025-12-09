<?php
$file = basename($_GET['file']); // mencegah path traversal
$path = "uploads/" . $file;

if (!file_exists($path)) {
    die("File tidak ditemukan.");
}

header("Content-Type: application/octet-stream");
header("Content-Disposition: attachment; filename=\"$file\"");
header("Content-Length: " . filesize($path));

readfile($path);
exit;
?>
