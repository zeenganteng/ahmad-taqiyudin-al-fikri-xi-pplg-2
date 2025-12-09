<?php
require "config.php";
require "functions.php";

$photos = mysqli_query($koneksi, "
    SELECT p.id, p.title, p.filename, u.username 
    FROM photos p
    JOIN users u ON u.id = p.user_id
    ORDER BY p.id DESC
");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Galeri Foto</title>

<style>
/* =======================
   GLOBAL PAGE STYLE
======================= */
body {
    font-family: Arial, sans-serif;
    background: linear-gradient(145deg, #ece9ff, #ffffff);
    margin: 0;
    padding: 30px;
}

h1 {
    text-align: center;
    font-size: 34px;
    margin-bottom: 25px;
    color: #4b4b4b;
    text-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

/* =======================
   TOP NAVIGATION
======================= */
.top-bar {
    text-align: center;
    margin-bottom: 30px;
}

.top-bar a {
    font-size: 15px;
    font-weight: bold;
    text-decoration: none;
    background: #6a5ae0;
    color: white;
    padding: 10px 18px;
    border-radius: 10px;
    margin: 0 5px;
    transition: 0.2s;
}

.top-bar a:hover {
    background: #7d6aff;
    transform: translateY(-2px);
}

/* =======================
   GALLERY GRID
======================= */
.gallery {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
    gap: 26px;
}

/* =======================
   PHOTO CARD
======================= */
.photo-card {
    background: white;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 10px 22px rgba(0,0,0,0.08);
    transition: 0.25s;
}

.photo-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 14px 30px rgba(0,0,0,0.12);
}

.photo-card img {
    width: 100%;
    height: 190px;
    object-fit: cover;
}

.photo-info {
    padding: 16px;
}

.photo-info small {
    color: #666;
}

/* =======================
   LIKE BUTTON
======================= */
.like-section {
    margin-top: 12px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.like-btn {
    cursor: pointer;
    background: none;
    border: 2px solid #ff5a79;
    color: #ff5a79;
    padding: 6px 16px;
    font-size: 20px;
    border-radius: 12px;
    transition: 0.25s;
    position: relative;
}

.like-btn:hover {
    transform: scale(1.15);
    background: rgba(255, 90, 121, 0.15);
}

.like-btn.liked {
    background: #ff5a79;
    color: white;
}

/* HEART ANIMATION */
.heart-burst {
    position: absolute;
    font-size: 22px;
    animation: burst 0.6s ease-out forwards;
}

@keyframes burst {
    0% { opacity: 1; transform: scale(0.3) translateY(0); }
    100% { opacity: 0; transform: scale(2) translateY(-40px); }
}
</style>

</head>
<body>

<h1>Galeri Foto</h1>

<div class="top-bar">
<?php if (is_logged_in()): ?>
    Halo, <?= esc(current_user()['username']); ?> |
    <a href="upload.php">Upload Foto</a>
    <a href="logout.php">Logout</a>
<?php else: ?>
    <a href="login.php">Login</a>
    <a href="register.php">Register</a>
<?php endif; ?>
</div>

<div class="gallery">
<?php while ($p = mysqli_fetch_assoc($photos)): ?>
    <div class="photo-card">
        <a href="photo.php?id=<?= $p['id']; ?>">
            <img src="uploads/<?= esc($p['filename']); ?>" alt="<?= esc($p['title']); ?>">
        </a>

        <div class="photo-info">
            <b><?= esc($p['title']); ?></b><br>
            <small>oleh <?= esc($p['username']); ?></small>

            <div class="like-section">

                <button 
                    class="like-btn <?= user_liked($p['id']) ? 'liked' : '' ?>"
                    onclick="toggleLike(<?= $p['id']; ?>, this)">
                    <?= user_liked($p['id']) ? '❤️' : '🤍' ?>
                </button>

                <span id="likes-<?= $p['id']; ?>">
                    <?= count_likes($p['id']); ?> likes
                </span>

            </div>
        </div>

    </div>
<?php endwhile; ?>
</div>

<script>
function toggleLike(photoId, btn) {

    if (!btn.classList.contains("liked")) {
        const heart = document.createElement("div");
        heart.classList.add("heart-burst");
        heart.innerHTML = "❤️";
        btn.appendChild(heart);
        setTimeout(() => heart.remove(), 600);
    }

    const xhr = new XMLHttpRequest();
    xhr.open("POST", "toggle_like.php", true);
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

    xhr.onload = function() {
        const data = JSON.parse(this.responseText);

        if (data.success) {

            document.getElementById("likes-" + photoId).innerText =
                data.likes + " likes";

            if (data.liked) {
                btn.classList.add("liked");
                btn.innerText = "❤️";
            } else {
                btn.classList.remove("liked");
                btn.innerText = "🤍";
            }
        }
    };

    xhr.send("id=" + photoId);
}
</script>

</body>
</html>
