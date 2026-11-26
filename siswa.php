<?php
require 'koneksi.php'; cek_login();
if (!in_array(level(), ['Admin','Guru'])) die("Akses ditolak. <a href='index.php'>Kembali</a>");
$admin = (level() === 'Admin');
$e = null;
if ($admin && isset($_GET['hapus'])) {
  $st = $db->prepare("DELETE FROM siswa WHERE nis=?"); $st->bind_param("i", $_GET['hapus']); $st->execute();
  header("Location: siswa.php"); exit;
}
if ($admin && $_SERVER['REQUEST_METHOD'] === 'POST') {
  if ($_POST['mode'] === 'edit') {
    $st = $db->prepare("UPDATE siswa SET nama=?, kelas=? WHERE nis=?");
    $st->bind_param("ssi", $_POST['nama'], $_POST['kelas'], $_POST['nis']);
  } else {
    $st = $db->prepare("INSERT INTO siswa (nis,nama,kelas) VALUES (?,?,?)");
    $st->bind_param("iss", $_POST['nis'], $_POST['nama'], $_POST['kelas']);
  }
  try { $st->execute(); } catch (mysqli_sql_exception $x) { die("Gagal: NIS sudah ada. <a href='siswa.php'>Kembali</a>"); }
  header("Location: siswa.php"); exit;
}
if ($admin && isset($_GET['edit'])) {
  $st = $db->prepare("SELECT * FROM siswa WHERE nis=?"); $st->bind_param("i", $_GET['edit']); $st->execute();
  $e = $st->get_result()->fetch_assoc();
}
$rs = $db->query("SELECT * FROM siswa ORDER BY nama");
?>
<!DOCTYPE html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Siswa | KONSERVASI ENERGI</title></head><body>
<?php include "menu.php"; ?>
<h2>Data Siswa</h2>
<?php if ($admin): ?><form method="post" class="frm"><input type="hidden" name="mode" value="<?= $e ? 'edit' : 'tambah' ?>">
<input name="nis" type="number" placeholder="NIS" value="<?= $e['nis'] ?? '' ?>" <?= $e ? 'readonly' : '' ?> required><br>
<input name="nama" placeholder="Nama" maxlength="100" value="<?= htmlspecialchars($e['nama'] ?? '') ?>" required><br>
<input name="kelas" placeholder="Kelas" maxlength="10" value="<?= htmlspecialchars($e['kelas'] ?? '') ?>" required><br>
<button><?= $e ? 'Update' : 'Tambah' ?></button> <?php if ($e) echo '<a href="siswa.php">Batal</a>'; ?></form><br><?php endif; ?>
<table class="tbl"><tr><th>NIS</th><th>Nama</th><th>Kelas</th><?php if ($admin) echo '<th>Aksi</th>'; ?></tr>
<?php while ($r = $rs->fetch_assoc()): ?>
<tr><td><?= $r['nis'] ?></td><td><?= htmlspecialchars($r['nama']) ?></td><td><?= htmlspecialchars($r['kelas']) ?></td>
<?php if ($admin): ?><td><a href="?edit=<?= $r['nis'] ?>">Edit</a> | <a href="?hapus=<?= $r['nis'] ?>" onclick="return confirm('Hapus siswa beserta checklist-nya?')">Hapus</a></td><?php endif; ?></tr>
<?php endwhile; ?></table></body></html>