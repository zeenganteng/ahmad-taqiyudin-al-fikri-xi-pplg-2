<?php
require "config.php";
require "functions.php";

// Jika sudah login, redirect ke index.php
if (is_logged_in()) {
    header("Location: index.php");
    exit;
}

$err = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = mysqli_real_escape_string($koneksi, $_POST['email']);
    $password = $_POST['password'];

    $q = mysqli_query($koneksi, "SELECT * FROM users WHERE email='$email'");
    $user = mysqli_fetch_assoc($q);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        header("Location: index.php");
        exit;
    } else {
        $err = "Email atau password salah!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>

<style>
body {
    font-family: Arial, sans-serif;
    background: linear-gradient(135deg, #ece9ff, #ffffff);
    margin: 0;
    padding: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    height: 100vh;
}

.login-box {
    background: white;
    padding: 28px;
    width: 330px;
    border-radius: 14px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.12);
    text-align: center;
}

h2 {
    margin: 0 0 20px 0;
    color: #4b4b4b;
}

label {
    font-weight: bold;
    display: block;
    text-align: left;
    margin: 10px 0 5px;
    color: #555;
}

input {
    width: 100%;
    padding: 10px;
    border: 2px solid #dad6ff;
    border-radius: 10px;
    outline: none;
    font-size: 15px;
    transition: 0.2s;
}

input:focus {
    border-color: #8e74ff;
}

button {
    margin-top: 15px;
    width: 100%;
    padding: 10px;
    background: #6A5AE0;
    border: none;
    color: white;
    font-size: 16px;
    border-radius: 10px;
    cursor: pointer;
    transition: 0.25s;
}

button:hover {
    background: #7e6aff;
    transform: translateY(-2px);
}

.error {
    background: #ffdadb;
    padding: 10px;
    color: #c43636;
    border-radius: 8px;
    margin-bottom: 15px;
}

a {
    text-decoration: none;
    color: #6A5AE0;
    font-weight: bold;
}
</style>

</head>
<body>

<div class="login-box">

    <h2>Login</h2>

    <?php if (!empty($err)): ?>
        <div class="error"><?= $err ?></div>
    <?php endif; ?>

    <form method="post">

        <label>Email</label>
        <input type="email" name="email" required>

        <label>Password</label>
        <input type="password" name="password" required>

        <button type="submit">Login</button>

    </form>

    <p style="margin-top: 15px;">
        Belum punya akun? <a href="register.php">Register</a>
    </p>

</div>

</body>
</html>
