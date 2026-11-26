<?php
require 'koneksi.php'; cek_login();
if (!in_array(level(), ['Admin','Guru'])) die("Akses ditolak. <a href='index.php'>Kembali</a>");
$admin = (level() === 'Admin');
$e = null;
if ($admin && isset($_GET['hapus'])) {
  $st = $db->prepare("DELETE FROM perangkat WHERE id_perangkat=?"); $st->bind_param("i", $_GET['hapus']); $st->execute();
  header("Location: perangkat.php"); exit;
}
if ($admin && $_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!empty($_POST['id'])) {
    $st = $db->prepare("UPDATE perangkat SET nama_perangkat=?, jenis=?, keterangan=? WHERE id_perangkat=?");
    $st->bind_param("sssi", $_POST['nama'], $_POST['jenis'], $_POST['ket'], $_POST['id']);
  } else {
    $st = $db->prepare("INSERT INTO perangkat (nama_perangkat,jenis,keterangan) VALUES (?,?,?)");
    $st->bind_param("sss", $_POST['nama'], $_POST['jenis'], $_POST['ket']);
  }
  $st->execute(); header("Location: perangkat.php"); exit;
}
if ($admin && isset($_GET['edit'])) {
  $st = $db->prepare("SELECT * FROM perangkat WHERE id_perangkat=?"); $st->bind_param("i", $_GET['edit']); $st->execute();
  $e = $st->get_result()->fetch_assoc();
}
$rs = $db->query("SELECT * FROM perangkat ORDER BY nama_perangkat");
?>
<!DOCTYPE html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Perangkat | KONSERVASI ENERGI</title></head><body>
<?php include "menu.php"; ?>
<h2>Data Perangkat</h2>
<?php if ($admin): ?><form method="post" class="frm"><input type="hidden" name="id" value="<?= $e['id_perangkat'] ?? '' ?>">
<input name="nama" placeholder="Nama perangkat" maxlength="100" value="<?= htmlspecialchars($e['nama_perangkat'] ?? '') ?>" required><br>
<input name="jenis" placeholder="Jenis" maxlength="50" value="<?= htmlspecialchars($e['jenis'] ?? '') ?>"><br>
<textarea name="ket" placeholder="Keterangan"><?= htmlspecialchars($e['keterangan'] ?? '') ?></textarea><br>
<button><?= $e ? 'Update' : 'Tambah' ?></button> <?php if ($e) echo '<a href="perangkat.php">Batal</a>'; ?></form><br><?php endif; ?>
<table class="tbl"><tr><th>ID</th><th>Nama</th><th>Jenis</th><th>Keterangan</th><?php if ($admin) echo '<th>Aksi</th>'; ?></tr>
<?php while ($r = $rs->fetch_assoc()): ?>
<tr><td><?= $r['id_perangkat'] ?></td><td><?= htmlspecialchars($r['nama_perangkat']) ?></td>
<td><?= htmlspecialchars($r['jenis']) ?></td><td><?= htmlspecialchars($r['keterangan']) ?></td>
<?php if ($admin): ?><td><a href="?edit=<?= $r['id_perangkat'] ?>">Edit</a> | <a href="?hapus=<?= $r['id_perangkat'] ?>" onclick="return confirm('Hapus perangkat?')">Hapus</a></td><?php endif; ?></tr>
<?php endwhile; ?></table></body></html>