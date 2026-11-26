<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require 'koneksi.php';
if (session_status() === PHP_SESSION_NONE) session_start();
if (empty($_SESSION['user'])) { header("Location: login.php"); exit; }

function ambil($db, $sql) {
  $r = $db->query($sql);
  return $r ? $r->fetch_all(MYSQLI_ASSOC) : [];
}
function h($v) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
function judul($k) { return ucwords(str_replace('_', ' ', $k)); }

$checklist = ambil($db, "SELECT * FROM checklist");
$tabelDetail = ambil($db, "SHOW TABLES LIKE 'detail_checklist'") ? 'detail_checklist' : 'details_checklist';
$details   = ambil($db, "SELECT * FROM `$tabelDetail`");

// Deteksi kolom penghubung: kolom pertama checklist (primary key) dicari di details_checklist
$pk = $checklist ? array_key_first($checklist[0]) : null;
$fk = null;
if ($pk && $details) {
  if (array_key_exists($pk, $details[0])) {
    $fk = $pk;
  } else {
    foreach (array_keys($details[0]) as $k) {
      if (stripos($k, 'checklist') !== false) { $fk = $k; break; }
    }
  }
}

$grup = [];
if ($fk) {
  foreach ($details as $d) $grup[$d[$fk]][] = $d;
}

function tabel($rows, $sembunyi = null) {
  if (!$rows) { echo "<p><i>Tidak ada data.</i></p>"; return; }
  $kolom = array_keys($rows[0]);
  echo "<table><thead><tr><th>No</th>";
  foreach ($kolom as $k) if ($k !== $sembunyi) echo "<th>" . h(judul($k)) . "</th>";
  echo "</tr></thead><tbody>";
  foreach ($rows as $i => $r) {
    echo "<tr><td>" . ($i + 1) . "</td>";
    foreach ($kolom as $k) if ($k !== $sembunyi) echo "<td>" . h($r[$k]) . "</td>";
    echo "</tr>";
  }
  echo "</tbody></table>";
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<title>Export Checklist</title>
<style>
  body { font-family: Arial, sans-serif; font-size: 12px; margin: 24px; color: #000; }
  h1 { font-size: 18px; margin: 0 0 4px; }
  .tgl { color: #555; margin-bottom: 16px; }
  h2 { font-size: 14px; margin: 18px 0 6px; }
  table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
  th, td { border: 1px solid #000; padding: 5px 7px; text-align: left; }
  th { background: #eee; }
  .blok { page-break-inside: avoid; }
  .aksi { margin-bottom: 16px; }
  .aksi button { padding: 8px 14px; margin-right: 6px; cursor: pointer; }
  @media print { .aksi { display: none; } body { margin: 0; } }
</style>
</head>
<body>

<div class="aksi">
  <button onclick="window.print()">Simpan sebagai PDF</button>
  <button onclick="history.back()">Kembali</button>
</div>

<h1>Konservasi Energi - Rekap Checklist</h1>
<div class="tgl">Dicetak: <?= date('d-m-Y H:i') ?></div>

<?php if (!$checklist): ?>
  <p><i>Tabel checklist masih kosong.</i></p>
<?php elseif ($fk): ?>
  <?php foreach ($checklist as $i => $c): ?>
    <div class="blok">
      <h2>Checklist <?= $i + 1 ?></h2>
      <table>
        <?php foreach ($c as $k => $v): ?>
          <tr><th style="width:30%"><?= h(judul($k)) ?></th><td><?= h($v) ?></td></tr>
        <?php endforeach; ?>
      </table>
      <?php tabel($grup[$c[$pk]] ?? [], $fk); ?>
    </div>
  <?php endforeach; ?>
<?php else: ?>
  <h2>Data Checklist</h2>
  <?php tabel($checklist); ?>
  <h2>Detail Checklist</h2>
  <?php tabel($details); ?>
<?php endif; ?>

</body>
</html>