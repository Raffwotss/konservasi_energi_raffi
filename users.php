<?php
require 'koneksi.php'; cek_login();
if (!in_array(level(), ['Admin','Guru'])) die("Akses ditolak. <a href='index.php'>Kembali</a>");
$admin = (level() === 'Admin');
$e = null;
if ($admin && isset($_GET['hapus'])) {
  if ((int)$_GET['hapus'] !== (int)$_SESSION['user']['id_user']) {
    $st = $db->prepare("DELETE FROM users WHERE id_user=?"); $st->bind_param("i", $_GET['hapus']); $st->execute();
  }
  header("Location: users.php"); exit;
}
if ($admin && $_SERVER['REQUEST_METHOD'] === 'POST') {
  try {
    if (!empty($_POST['id'])) {
      if ($_POST['password'] !== '') {
        $h = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $st = $db->prepare("UPDATE users SET username=?, nama=?, level=?, password=? WHERE id_user=?");
        $st->bind_param("ssssi", $_POST['username'], $_POST['nama'], $_POST['level'], $h, $_POST['id']);
      } else {
        $st = $db->prepare("UPDATE users SET username=?, nama=?, level=? WHERE id_user=?");
        $st->bind_param("sssi", $_POST['username'], $_POST['nama'], $_POST['level'], $_POST['id']);
      }
    } else {
      $h = password_hash($_POST['password'], PASSWORD_DEFAULT);
      $st = $db->prepare("INSERT INTO users (username,password,nama,level) VALUES (?,?,?,?)");
      $st->bind_param("ssss", $_POST['username'], $h, $_POST['nama'], $_POST['level']);
    }
    $st->execute();
  } catch (mysqli_sql_exception $x) { die("Gagal: username sudah dipakai. <a href='users.php'>Kembali</a>"); }
  header("Location: users.php"); exit;
}
if ($admin && isset($_GET['edit'])) {
  $st = $db->prepare("SELECT * FROM users WHERE id_user=?"); $st->bind_param("i", $_GET['edit']); $st->execute();
  $e = $st->get_result()->fetch_assoc();
}
$rs = $db->query("SELECT * FROM users ORDER BY nama");
?>
<!DOCTYPE html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Users | Neon Idol Energy Lab</title></head><body>
<?php include "menu.php"; ?>
<h2>Data Users</h2>
<?php if ($admin): ?><form method="post" class="frm"><input type="hidden" name="id" value="<?= $e['id_user'] ?? '' ?>">
<input name="username" placeholder="Username" maxlength="50" value="<?= htmlspecialchars($e['username'] ?? '') ?>" required><br>
<input name="nama" placeholder="Nama" maxlength="100" value="<?= htmlspecialchars($e['nama'] ?? '') ?>" required><br>
<input type="password" name="password" placeholder="<?= $e ? 'Password baru (kosongkan jika tidak diubah)' : 'Password' ?>" <?= $e ? '' : 'required' ?>><br>
<select name="level"><?php foreach (['Admin','Guru','Siswa'] as $l) echo "<option".(($e['level'] ?? '') === $l ? ' selected' : '').">$l</option>"; ?></select><br>
<button><?= $e ? 'Update' : 'Tambah' ?></button> <?php if ($e) echo '<a href="users.php">Batal</a>'; ?></form><br><?php endif; ?>
<table class="tbl"><tr><th>ID</th><th>Username</th><th>Nama</th><th>Level</th><?php if ($admin) echo '<th>Aksi</th>'; ?></tr>
<?php while ($r = $rs->fetch_assoc()): ?>
<tr><td><?= $r['id_user'] ?></td><td><?= htmlspecialchars($r['username']) ?></td><td><?= htmlspecialchars($r['nama']) ?></td><td><?= $r['level'] ?></td>
<?php if ($admin): ?><td><a href="?edit=<?= $r['id_user'] ?>">Edit</a><?php if ($r['id_user'] != $_SESSION['user']['id_user']): ?> |
<a href="?hapus=<?= $r['id_user'] ?>" onclick="return confirm('Hapus user?')">Hapus</a><?php endif; ?></td><?php endif; ?></tr>
<?php endwhile; ?></table></body></html>