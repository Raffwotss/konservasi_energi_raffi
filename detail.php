<?php
require 'koneksi.php'; cek_login();
$st = $db->prepare("SELECT d.*, p.nama_perangkat FROM detail_checklist d
  JOIN perangkat p ON p.id_perangkat=d.id_perangkat WHERE d.id_checklist=?");
$st->bind_param("i", $_GET['id']); $st->execute();
$rs = $st->get_result();
?>
<!DOCTYPE html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Detail Checklist | Neon Idol Energy Lab</title>
</head>
<body><?php include "menu.php"; ?><h2>Detail Checklist #<?= (int)$_GET['id'] ?></h2><a href="index.php">&laquo; Kembali</a><br><br>
<table><tr><th>Perangkat</th><th>Tahapan</th><th>Kondisi</th><th>Status</th><th>Keterangan</th></tr>
<?php while($r=$rs->fetch_assoc()): ?>
<tr><td><?= htmlspecialchars($r['nama_perangkat']) ?></td><td><?= $r['tahapan'] ?></td>
<td><?= htmlspecialchars($r['kondisi']) ?></td><td><?= $r['status'] ?></td><td><?= htmlspecialchars($r['keterangan']) ?></td></tr>
<?php endwhile; ?></table></body></html>