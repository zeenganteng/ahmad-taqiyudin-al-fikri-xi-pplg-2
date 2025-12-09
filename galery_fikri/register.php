<?php
require "config.php";
require "functions.php";

$err = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email    = trim($_POST['email']);
    $password = $_POST['password'];

    // Cek username / email sudah ada
    $cek = mysqli_query($koneksi, "
        SELECT * FROM users 
        WHERE username='$username' OR email='$email'
    ");

    if (mysqli_num_rows($cek) > 0) {
        $err = "Username atau Email sudah digunakan!";
    } else {
        $passHash = password_hash($password, PASSWORD_DEFAULT);

        $q = mysqli_query($koneksi, "
            INSERT INTO users(username, email, password)
            VALUES('$username', '$email', '$passHash')
        ");

        if ($q) {
            header("Location: login.php");
            exit;
        } else {
            $err = "Gagal mendaftar!";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register</title>

<style>
body {
    font-family: Arial, sans-serif;
    background: linear-gradient(135deg, #ece9ff, #ffffff);
    margin: 0;
    padding: 0;
    display: flex;
    height: 100vh;
    align-items: center;
    justify-content: center;
}

.register-box {
    width: 330px;
    background: white;
    padding: 28px;
    border-radius: 14px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.12);
    text-align: center;
}

h2 {
    margin-bottom: 20px;
    color: #4b4b4b;
}

label {
    font-weight: bold;
    text-align: left;
    display: block;
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
    transition: 0.25s;
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
    border-radius: 10px;
    font-size: 16px;
    cursor: pointer;
    transition: 0.25s;
}

button:hover {
    background: #7e6aff;
    transform: translateY(-2px);
}

.error {
    background: #ffd4d6;
    padding: 10px;
    color: #b83232;
    border-radius: 10px;
    margin-bottom: 15px;
}

p {
    margin-top: 10px;
}

a {
    color: #6A5AE0;
    font-weight: bold;
    text-decoration: none;
}
</style>

</head>
<body>

<div class="register-box">

    <h2>Register</h2>

    <?php if (!empty($err)): ?>
        <div class="error"><?= $err ?></div>
    <?php endif; ?>

    <form method="post">

        <label>Username</label>
        <input name="username" required>

        <label>Email</label>
        <input name="email" type="email" required>

        <label>Password</label>
        <input name="password" type="password" required>

        <button type="submit">Daftar</button>
    </form>

    <p>Sudah punya akun? <a href="login.php">Login</a></p>

</div>

</body>
</html>
