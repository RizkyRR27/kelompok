<?php
session_start();

// Cek sesi dan role
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'dosen') {
    header("Location: login.php");
    exit();
}

include 'db.php';

try {
    // Ambil data laporan dari database
    $query = "SELECT * FROM laporan WHERE id_dosen = :id_dosen";
    $stmt = $conn->prepare($query);
    $stmt->execute(['id_dosen' => $_SESSION['data_user']['id_dosen']]);
    $laporan = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Terjadi kesalahan saat mengambil data laporan: " . htmlspecialchars($e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Output Laporan</title>
    <link rel="stylesheet" href="tampilan.css">
</head>
<body>
    <h1>Output Laporan Pelanggaran</h1>
    <nav>
        <ul>
            <li><a href="dosen_dashboard.php">Dashboard</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>

    <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
    
    <?php if (!empty($laporan)): ?>
        <table border="1">
            <tr>
                <th>NIM</th>
                <th>Nama Mahasiswa</th>
                <th>Kelas</th>
                <th>Pelanggaran</th>
                <th>Tanggal Laporan</th>
            </tr>
            <?php foreach ($laporan as $row): ?>
                <tr>
                    <td><?= htmlspecialchars($row['nim'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($row['nama_mhs'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($row['kelas'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($row['pelanggaran'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($row['tanggal_laporan'], ENT_QUOTES, 'UTF-8') ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p>Tidak ada laporan yang ditemukan.</p>
    <?php endif; ?>
</body>
</html>