<link rel="stylesheet" href="style.css">
<header class="nav">
  <a class="brand" href="index.php">
    <span class="brand-mark">NE</span>
    <span><span class="brand-name">KONSERVASI ENERGI</span><span class="brand-sub">KONSERVASI ENERGI RAFFI</span></span>
  </a>
  <nav class="nav-links">
    <a href="index.php">◈ Checklist</a>
    <?php $lv = $_SESSION['user']['level'] ?? '';
    if ($lv === 'Admin' || $lv === 'Guru') echo '<a href="siswa.php">Siswa</a><a href="perangkat.php">Perangkat</a><a href="users.php">Users</a>'; ?>
    <a href="logout.php">Keluar ↗</a>
  </nav>
</header>
