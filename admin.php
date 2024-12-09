<?php
session_start();

// Cek sesi dan role
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("login.php");
    exit();
}

include 'db.php';

try {
    if (!$conn) {
        throw new Exception("Koneksi database tidak ditemukan.");
    }

    // Query data mahasiswa
    $query = "SELECT * FROM mahasiswa";
    $stmt = $conn->query($query);
    $mahasiswa = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Database Error: " . $e->getMessage());
    header("Location: ../error.php");
    exit();
} catch (Exception $e) {
    error_log("Error: " . $e->getMessage());
    header("Location: ../error.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Mahasiswa</title>
    <link rel="stylesheet" href="tampilan.css">
</head>
<body>
    <h1>Data Mahasiswa</h1>
    <?php if (empty($mahasiswa)): ?>
        <p>Data mahasiswa tidak ditemukan.</p>
    <?php else: ?>
        <table border="1">
            <tr>
                <th>ID</th>
                <th>NIM</th>
                <th>Nama</th>
                <th>Kelas</th>
            </tr>
            <?php foreach ($mahasiswa as $row): ?>
                <tr>
                    <td><?= isset($row['id_mhs']) ? htmlspecialchars($row['id_mhs'], ENT_QUOTES, 'UTF-8') : 'Tidak Ada' ?></td>
                    <td><?= isset($row['nim']) ? htmlspecialchars($row['nim'], ENT_QUOTES, 'UTF-8') : 'Tidak Ada' ?></td>
                    <td><?= isset($row['nama_mhs']) ? htmlspecialchars($row['nama_mhs'], ENT_QUOTES, 'UTF-8') : 'Tidak Ada' ?></td>
                    <td><?= isset($row['kelas']) ? htmlspecialchars($row['kelas'], ENT_QUOTES, 'UTF-8') : 'Tidak Ada' ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
</body>
</html>
