<?php
require 'koneksi.php';
if (session_status() === PHP_SESSION_NONE) session_start();
if (empty($_SESSION['user'])) { header("Location: login.php"); exit; }

function ambil($db, $sql) {
  try {
    $r = $db->query($sql);
    return $r ? $r->fetch_all(MYSQLI_ASSOC) : [];
  } catch (Throwable $e) {
    return null;
  }
}
function h($v) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }

// Nama tabel detail bisa detail_checklist atau details_checklist
$cek = ambil($db, "SHOW TABLES LIKE 'detail_checklist'");
$tDetail = $cek ? 'detail_checklist' : 'details_checklist';

// Query utama: sama seperti tampilan di index.php (nis, nama, kelas, tanggal, jam, jumlah item)
$rows = ambil($db, "SELECT s.nis, s.nama, s.kelas, c.tanggal, c.jam, COUNT(d.id_checklist) AS item
  FROM checklist c
  JOIN siswa s ON s.nis = c.nis
  LEFT JOIN `$tDetail` d ON d.id_checklist = c.id_checklist
  GROUP BY c.id_checklist
  ORDER BY c.tanggal DESC, c.jam DESC");

// Kalau query di atas tidak cocok dengan tabelmu, pakai semua kolom tabel checklist
if ($rows === null) {
  $rows = ambil($db, "SELECT * FROM checklist") ?: [];
}

header('Content-Type: application/vnd.ms-excel; charset=utf-8');
header('Content-Disposition: attachment; filename="checklist_' . date('Ymd_His') . '.xls"');
header('Pragma: no-cache');
header('Expires: 0');
?>
<html xmlns:x="urn:schemas-microsoft-com:office:excel">
<head>
<meta charset="utf-8">
<style>
  table { border-collapse: collapse; }
  th, td { border: 1px solid #000; padding: 4px 8px; }
  th { background: #2f8cff; color: #fff; font-weight: bold; }
  .teks { mso-number-format: "\@"; }
</style>
</head>
<body>
<table>
  <tr><td colspan="7" style="border:0;font-size:16px;font-weight:bold">Konservasi Energi - Data Checklist</td></tr>
  <tr><td colspan="7" style="border:0">Diunduh: <?= date('d-m-Y H:i') ?></td></tr>
  <tr><td colspan="7" style="border:0"></td></tr>
<?php if ($rows): $kolom = array_keys($rows[0]); ?>
  <tr>
    <th>No</th>
    <?php foreach ($kolom as $k): ?><th><?= h(ucwords(str_replace('_', ' ', $k))) ?></th><?php endforeach; ?>
  </tr>
  <?php foreach ($rows as $i => $r): ?>
  <tr>
    <td><?= $i + 1 ?></td>
    <?php foreach ($kolom as $k): ?><td class="teks"><?= h($r[$k]) ?></td><?php endforeach; ?>
  </tr>
  <?php endforeach; ?>
<?php else: ?>
  <tr><td colspan="7">Belum ada data checklist.</td></tr>
<?php endif; ?>
</table>
</body>
</html>