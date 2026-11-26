<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

include 'koneksi.php';
$koneksi = $db;
cek_login();

$siswa = mysqli_query($koneksi, "SELECT nis, nama FROM siswa ORDER BY nama");
$guru = mysqli_query($koneksi, "SELECT id_guru, nama FROM guru ORDER BY nama");
$perangkat = mysqli_query($koneksi, "SELECT id_perangkat, nama_perangkat FROM perangkat");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tipe = ($_POST['tipe'] ?? 'siswa') === 'guru' ? 'guru' : 'siswa';
    $tanggal = $_POST['tanggal'];
    $jam = $_POST['jam'];

    if ($tipe === 'guru') {
        $nis = null;
        $id_guru = (int) $_POST['id_guru'];
    } else {
        $nis = (int) $_POST['nis'];
        $id_guru = null;
    }

    $stmt = mysqli_prepare($koneksi,
        "INSERT INTO checklist (tipe, nis, id_guru, tanggal, jam) VALUES (?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "siiss", $tipe, $nis, $id_guru, $tanggal, $jam);
    mysqli_stmt_execute($stmt);
    $id_checklist = mysqli_insert_id($koneksi);

    // ASUMSI kolom details_checklist: id_checklist, id_perangkat, tahapan, kondisi, status, keterangan
    $stmt2 = mysqli_prepare($koneksi,
        "INSERT INTO details_checklist (id_checklist, id_perangkat, tahapan, kondisi, status, keterangan)
         VALUES (?, ?, ?, ?, ?, ?)");
    foreach (($_POST['pilih'] ?? []) as $id_perangkat => $v) {
        $id_perangkat = (int) $id_perangkat;
        $tahapan = $_POST['tahapan'][$id_perangkat] ?? 'Sebelum';
        $kondisi = $_POST['kondisi'][$id_perangkat] ?? '';
        $status = $_POST['status'][$id_perangkat] ?? 'Ya';
        $keterangan = $_POST['keterangan'][$id_perangkat] ?? '';
        mysqli_stmt_bind_param($stmt2, "iissss", $id_checklist, $id_perangkat, $tahapan, $kondisi, $status, $keterangan);
        mysqli_stmt_execute($stmt2);
    }

    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Tambah Checklist | Konservasi Energi</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;800&display=swap" rel="stylesheet">
<style>
:root {
    --bg-top: #0b0a1f;
    --bg-bottom: #2f6df6;
    --card: rgba(255,255,255,.045);
    --line: rgba(96,165,250,.38);
    --text: #f4f7ff;
    --muted: #9fb4ff;
    --cyan: #5eead4;
    --sky: #67d3ff;
    --pink: #f0abfc;
    --field: rgba(10,14,50,.72);
}
* { box-sizing: border-box; }
body {
    margin: 0;
    min-height: 100vh;
    font-family: 'Poppins', system-ui, sans-serif;
    color: var(--text);
    background: linear-gradient(180deg, var(--bg-top) 0%, #12175a 45%, var(--bg-bottom) 100%) fixed;
}
.wrap { max-width: 1100px; margin: 0 auto; padding: 24px 20px 60px; }

.brand { display: flex; align-items: center; gap: 14px; margin-bottom: 34px; }
.logo {
    width: 54px; height: 54px; border-radius: 16px; display: grid; place-items: center;
    font-weight: 800; color: #0b1240; font-size: 18px;
    background: linear-gradient(135deg, var(--sky), #7c8cff 55%, var(--pink));
    box-shadow: 0 0 22px rgba(103,211,255,.45);
}
.brand b { display: block; font-weight: 800; letter-spacing: .08em; font-size: 17px; }
.brand span { font-size: 11px; letter-spacing: .14em; color: var(--muted); }

h1 {
    font-size: 30px; font-weight: 800; margin: 0 0 22px;
    display: flex; align-items: center; gap: 12px;
}
h1::before {
    content: ""; width: 8px; height: 34px; border-radius: 6px;
    background: linear-gradient(180deg, var(--sky), var(--pink));
}

.panel {
    background: var(--card);
    border: 1px solid var(--line);
    border-radius: 26px;
    padding: 28px;
    backdrop-filter: blur(6px);
}

.row { display: flex; flex-wrap: wrap; align-items: center; gap: 14px 22px; margin-bottom: 18px; }
label.f { display: flex; align-items: center; gap: 12px; font-weight: 500; }

input[type=date], input[type=time], input[type=text], select {
    font-family: inherit; font-size: 14px; color: var(--text);
    background: var(--field);
    border: 1px solid var(--line);
    border-radius: 14px;
    padding: 10px 14px;
    min-height: 44px;
    color-scheme: dark;
}
input[type=text] { width: 100%; }
select { cursor: pointer; font-weight: 600; }
select:disabled { opacity: .5; }
:is(input, select, button, a):focus-visible { outline: 2px solid var(--cyan); outline-offset: 2px; }

.seg { display: inline-flex; padding: 4px; gap: 4px; background: var(--field); border: 1px solid var(--line); border-radius: 16px; }
.seg input { position: absolute; opacity: 0; pointer-events: none; }
.seg label { padding: 8px 22px; border-radius: 12px; cursor: pointer; font-weight: 600; color: var(--muted); }
.seg input:checked + label { color: #08113a; background: linear-gradient(90deg, var(--cyan), #b9c6ff); }
.seg input:focus-visible + label { outline: 2px solid var(--cyan); }

.tablewrap { overflow-x: auto; border: 1px solid var(--line); border-radius: 20px; margin-top: 6px; }
table { width: 100%; border-collapse: collapse; min-width: 860px; }
th {
    text-align: left; padding: 14px 16px; font-size: 11px; font-weight: 800; letter-spacing: .1em;
    color: var(--cyan); background: rgba(20,40,140,.55); text-transform: uppercase;
}
td { padding: 12px 16px; border-top: 1px solid rgba(96,165,250,.18); font-weight: 500; }
tr:nth-child(even) td { background: rgba(255,255,255,.025); }
tr:hover td { background: rgba(103,211,255,.09); }
td input[type=checkbox] { width: 22px; height: 22px; accent-color: #5eead4; cursor: pointer; }

.actions { display: flex; align-items: center; gap: 18px; margin-top: 26px; }
.btn {
    font-family: inherit; font-weight: 800; font-size: 15px; cursor: pointer;
    padding: 13px 30px; border: 0; border-radius: 16px; color: #07123a;
    background: linear-gradient(90deg, #7ff3e4, #67d3ff);
    box-shadow: 0 0 20px rgba(103,211,255,.4);
    transition: transform .15s, box-shadow .15s;
}
.btn:hover { transform: translateY(-1px); box-shadow: 0 0 28px rgba(103,211,255,.65); }
.batal { color: var(--sky); font-weight: 800; text-decoration: none; }
.batal:hover { text-decoration: underline; }

@media (max-width: 640px) {
    .panel { padding: 18px; border-radius: 20px; }
    h1 { font-size: 24px; }
    label.f { width: 100%; }
    label.f select, label.f input { flex: 1; }
}
@media (prefers-reduced-motion: reduce) { .btn { transition: none; } }
</style>
</head>
<body>
<div class="wrap">

    <header class="brand">
        <div class="logo">NE</div>
        <div>
            <b>KONSERVASI ENERGI</b>
            <span>KONSERVASI ENERGI RAFFI</span>
        </div>
    </header>

    <h1>Tambah Checklist</h1>

    <form method="post" class="panel">

        <div class="row">
            <div class="seg" role="radiogroup" aria-label="Tipe pengisi">
                <input type="radio" name="tipe" id="t_siswa" value="siswa" checked>
                <label for="t_siswa">Siswa</label>
                <input type="radio" name="tipe" id="t_guru" value="guru">
                <label for="t_guru">Guru</label>
            </div>

            <label class="f" id="box_siswa">Siswa:
                <select name="nis" id="sel_siswa">
                    <?php while ($s = mysqli_fetch_assoc($siswa)): ?>
                        <option value="<?= $s['nis'] ?>"><?= $s['nis'] ?> - <?= htmlspecialchars($s['nama']) ?></option>
                    <?php endwhile; ?>
                </select>
            </label>

            <label class="f" id="box_guru" style="display:none">Guru:
                <select name="id_guru" id="sel_guru" disabled>
                    <?php while ($g = mysqli_fetch_assoc($guru)): ?>
                        <option value="<?= $g['id_guru'] ?>"><?= $g['id_guru'] ?> - <?= htmlspecialchars($g['nama']) ?></option>
                    <?php endwhile; ?>
                </select>
            </label>
        </div>

        <div class="row">
            <label class="f">Tanggal:
                <input type="date" name="tanggal" value="<?= date('Y-m-d') ?>" required>
            </label>
            <label class="f">Jam:
                <input type="time" name="jam" value="<?= date('H:i') ?>" required>
            </label>
        </div>

        <div class="tablewrap">
            <table>
                <thead>
                    <tr>
                        <th>Pilih</th>
                        <th>Perangkat</th>
                        <th>Tahapan</th>
                        <th>Kondisi</th>
                        <th>Status</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                <?php while ($p = mysqli_fetch_assoc($perangkat)): $id = $p['id_perangkat']; ?>
                    <tr>
                        <td><input type="checkbox" name="pilih[<?= $id ?>]" value="1" aria-label="Pilih <?= htmlspecialchars($p['nama_perangkat']) ?>"></td>
                        <td><?= htmlspecialchars($p['nama_perangkat']) ?></td>
                        <td>
                            <select name="tahapan[<?= $id ?>]">
                                <option value="Sebelum">SEBELUM</option>
                                <option value="Sesudah">SESUDAH</option>
                            </select>
                        </td>
                        <td><input type="text" name="kondisi[<?= $id ?>]"></td>
                        <td>
                            <select name="status[<?= $id ?>]">
                                <option value="Ya">Ya</option>
                                <option value="Tidak">Tidak</option>
                            </select>
                        </td>
                        <td><input type="text" name="keterangan[<?= $id ?>]"></td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <div class="actions">
            <button type="submit" class="btn">Simpan</button>
            <a href="index.php" class="batal">Batal</a>
        </div>
    </form>
</div>

<script>
function ganti() {
    var guru = document.getElementById('t_guru').checked;
    document.getElementById('box_siswa').style.display = guru ? 'none' : '';
    document.getElementById('box_guru').style.display = guru ? '' : 'none';
    document.getElementById('sel_siswa').disabled = guru;
    document.getElementById('sel_guru').disabled = !guru;
}
document.getElementById('t_siswa').addEventListener('change', ganti);
document.getElementById('t_guru').addEventListener('change', ganti);
</script>
</body>
</html>