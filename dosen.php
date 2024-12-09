<?php
session_start();

// Cek sesi dan role
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'dosen') {
    header("Location: login.php");
    exit();
}

// Menu untuk dashboard, laporkan mahasiswa, dan logout
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Dosen</title>
    <link rel="stylesheet" href="tampilan.css">
</head>
<body>
    <h1>Selamat Datang, <?= htmlspecialchars($_SESSION['username']) ?></h1>
    <nav>
        <ul>
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="laporkan_mahasiswa.php">Laporkan Mahasiswa</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>
</body>
</html>