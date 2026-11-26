<?php
require 'koneksi.php'; cek_login();
$admin = (level() === 'Admin');
if ($admin && isset($_GET['hapus'])) {
  $st = $db->prepare("DELETE FROM checklist WHERE id_checklist=?");
  $st->bind_param("i", $_GET['hapus']); $st->execute();
  header("Location: index.php"); exit;
}
$rs = $db->query("SELECT c.*, s.nama, s.kelas, COUNT(d.id_detail) AS jml
  FROM checklist c JOIN siswa s ON s.nis=c.nis
  LEFT JOIN detail_checklist d ON d.id_checklist=c.id_checklist
  GROUP BY c.id_checklist ORDER BY c.tanggal DESC, c.jam DESC");
?>
<!DOCTYPE html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Checklist | KONSERVASI ENERGI</title>
</head>
<body><?php include "menu.php"; ?>
<p>Halo, <b><?= htmlspecialchars($_SESSION['user']['nama']) ?></b> (<?= $_SESSION['user']['level'] ?>) | <a href="logout.php">Logout</a></p>
<h2>Data Checklist</h2>
<style>
.tb { display:flex; justify-content:space-between; align-items:center; gap:12px; flex-wrap:wrap; margin-bottom:22px; }
.tb-r { display:flex; gap:10px; }
.tb a { display:inline-flex; align-items:center; padding:11px 18px; border-radius:11px; font-size:13px; font-weight:700; color:#fff; text-decoration:none; transition:transform .18s, filter .18s; }
.tb a:hover { transform:translateY(-2px); filter:brightness(1.1); color:#fff; }
.tb .tambah { background:linear-gradient(110deg,#2f8cff,#586dff); box-shadow:0 8px 24px rgba(47,140,255,.22); }
.tb .xls { background:linear-gradient(110deg,#5ee7ff,#62a9ff); color:#041326; box-shadow:0 8px 22px rgba(94,231,255,.2); }
.tb .xls:hover { color:#041326; }
.tb .pdf { background:linear-gradient(110deg,#ff77bd,#ff6d87); box-shadow:0 8px 22px rgba(255,119,189,.22); }
</style>

<div class="tb">
  <a href="tambah.php" class="tambah">+ Tambah Checklist</a>
  <div class="tb-r">
    <a href="export_excel.php" class="xls">Export</a>
    <a href="Export.php" target="_blank" class="pdf">PDF</a>
  </div>
</div>

<table><tr><th>No</th><th>NIS</th><th>Nama</th><th>Kelas</th><th>Tanggal</th><th>Jam</th><th>Item</th><th>Aksi</th></tr>
<?php $no=1; while($r=$rs->fetch_assoc()): ?>
<tr><td><?= $no++ ?></td><td><?= $r['nis'] ?></td><td><?= htmlspecialchars($r['nama']) ?></td>
<td><?= htmlspecialchars($r['kelas']) ?></td><td><?= $r['tanggal'] ?></td><td><?= $r['jam'] ?></td><td><?= $r['jml'] ?></td>
<td><a href="detail.php?id=<?= $r['id_checklist'] ?>">Detail</a><?php if ($admin): ?> |
<a href="?hapus=<?= $r['id_checklist'] ?>" onclick="return confirm('Hapus?')">Hapus</a><?php endif; ?></td></tr>
<?php endwhile; ?></table></body></html>