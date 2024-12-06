<?php
session_start();

// Cek sesi dan role
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'dosen') {
    header("Location: login.php");
    exit();
}

include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $mahasiswa_id = $_POST['mahasiswa_id'];
    $pelanggaran = $_POST['pelanggaran'];

    try {
        // Simpan laporan pelanggaran ke database
        //$query = "INSERT INTO laporan (mahasiswa_id, pelanggaran, dosen_id) VALUES (:mahasiswa_id, :pelanggaran, :dosen_id)";
        $stmt = $conn->prepare($query);
        $stmt->execute(['mahasiswa_id' => $mahasiswa_id, 'pelanggaran' => $pelanggaran, 'dosen_id' => $_SESSION['username']]);

        $success = "Laporan pelanggaran berhasil dikirim.";
    } catch (PDOException $e) {
        $error = "Terjadi kesalahan: " . htmlspecialchars($e->getMessage());
    }
}

// Ambil data mahasiswa untuk laporan
$query = "SELECT * FROM mahasiswa";
$stmt = $conn->query($query);
$mahasiswa = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Pelanggaran Dosen</title>
    <link rel="stylesheet" href="tampilan.css">
</head>
<body>
    <h1>Laporan Pelanggaran</h1>
    <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
    <?php if (isset($success)) echo "<p style='color:green;'>$success</p>"; ?>
    
    <form method="POST">
        <select name="mahasiswa_id" required>
            <option value="">Pilih Mahasiswa</option>
            <?php foreach ($mahasiswa as $row): ?>
                <option value="<?= htmlspecialchars($row['id_mhs'], ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($row['nama_mhs'], ENT_QUOTES, 'UTF-8') ?></option>
            <?php endforeach; ?>
        </select>
        <textarea name="pelanggaran" placeholder="Deskripsi Pelanggaran" required></textarea>
        <button type="submit">Kirim Laporan</button>
    </form>
</body>
</html>