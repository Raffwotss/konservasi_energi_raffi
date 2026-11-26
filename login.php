<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require 'koneksi.php';
if (session_status() === PHP_SESSION_NONE) session_start();

$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $user = trim($_POST['username']);
  $pass = $_POST['password'];
  $st = $db->prepare("SELECT * FROM users WHERE username=?");
  $st->bind_param("s", $user);
  $st->execute();
  $u = $st->get_result()->fetch_assoc();
  if ($u && (password_verify($pass, $u['password']) || hash_equals($u['password'], $pass))) {
    $_SESSION['user'] = $u;
    header("Location: index.php");
    exit;
  }
  $err = "Username atau password salah";
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Masuk | WEB KONSERVASI ENERGI</title>
<link rel="stylesheet" href="style.css">

</head>
<body class="login-page">
<div class="login-shell">
  <section class="login-art">
    <div>
      <div class="login-kicker">WELCOME</div>
      <h1>Small actions.<br>Brighter future.</h1>
      <p>Platform pemantauan konservasi energi.</p>
    </div>
    <div class="login-stamp">DAREKA NO TAMENI · DEMI SESEORANG · ENERGY AWARENESS</div>
  </section>
  <section class="login-form-wrap">
    <div class="login-kicker">Konservasi Energi</div>
    <h2>Selamat datang</h2>
    <p class="muted">Masuk untuk melanjutkan ke dashboard konservasi energi.</p>
<h2>Konservasi Energi</h2>
<?php if ($err) echo '<p class="error-msg">' . htmlspecialchars($err) . '</p>'; ?>
<form method="post" autocomplete="off">
  <label for="username">USERNAME</label>
  <input id="username" name="username" placeholder="Masukkan username" required>
  <label for="password">PASSWORD</label>
  <input id="password" type="password" name="password" placeholder="Masukkan password" required>
  <button type="submit">Masuk ke dashboard →</button>
</form>
</section>
</div>
</body>
</html>