<?php
session_start();
date_default_timezone_set('Asia/Jakarta');
$db = new mysqli("localhost", "root", "", "konservasi_energi_raffi");
if ($db->connect_error) die("Koneksi gagal: " . $db->connect_error);
function cek_login() { if (!isset($_SESSION['user'])) { header("Location: login.php"); exit; } }
function level() { return $_SESSION['user']['level'] ?? ''; }
function wajib_admin() { cek_login(); if (level() !== 'Admin') die("Akses ditolak. <a href='index.php'>Kembali</a>"); }