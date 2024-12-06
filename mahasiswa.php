<?php
session_start();

// Cek sesi dan role
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'mahasiswa') {
    header("Location: login.php");
    exit();
}

include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $mahasiswa_id = $_SESSION['username']; // ID Mahasiswa yang sedang login
    $mahasiswa_dilaporkan = $_POST['mahasiswa_dilaporkan']; // ID Mahasiswa yang dilaporkan
    $pelanggaran = $_POST['pelanggaran']; // Deskripsi pelanggaran
    $tanggal_pelanggaran = $_POST['tanggal_pelanggaran']; // Tanggal pelanggaran

    // Validasi input
    if (empty($mahasiswa_dilaporkan) || empty($pelanggaran) || empty($tanggal_pelanggaran)) {
        $error = "Semua field wajib diisi.";
    } else {
        try {
            // Simpan laporan pelanggaran ke database
            //$query = "INSERT INTO laporan_mahasiswa (mahasiswa_id, mahasiswa_dilaporkan, pelanggaran, tanggal) VALUES (:mahasiswa_id, :mahasiswa_dilaporkan, :pelanggaran, :tanggal)";
            $stmt = $conn->prepare($query);
            $stmt->execute([
                'mahasiswa_id' => htmlspecialchars($mahasiswa_id, ENT_QUOTES, 'UTF-8'),
                'mahasiswa_dilaporkan' => htmlspecialchars($mahasiswa_dilaporkan, ENT_QUOTES, 'UTF-8'),
                'pelanggaran' => htmlspecialchars($pelanggaran, ENT_QUOTES, 'UTF-8'),
                'tanggal' => htmlspecialchars($tanggal_pelanggaran, ENT_QUOTES, 'UTF-8')
            ]);

            $success = "Laporan pelanggaran berhasil dikirim.";
        } catch (PDOException $e) {
            $error = "Terjadi kesalahan saat menyimpan laporan: " . htmlspecialchars($e->getMessage());
        }
    }
}

// Ambil data mahasiswa untuk dropdown
try {
    $query = "SELECT * FROM mahasiswa";
    $stmt = $conn->query($query);
    $mahasiswa_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
    // var_dump($mahasiswa_list);
} catch (PDOException $e) {
    $mahasiswa_list = [];
    $error = "Terjadi kesalahan saat mengambil data mahasiswa: " . htmlspecialchars($e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Pelanggaran Mahasiswa</title>
    <link rel="stylesheet" href="mhs.css">
</head>
<body>
    <div class="form-container">
        <h1>Laporan Pelanggaran</h1>
        <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
        <?php if (isset($success)) echo "<p style='color:green;'>$success</p>"; ?>
        
        <form method="POST">
            <label for="mahasiswa_dilaporkan">Nama Mahasiswa yang Dilaporkan:</label>
            <select name="mahasiswa_dilaporkan" required>
                <option value="">Pilih Mahasiswa</option>
                <?php if (!empty($mahasiswa_list)): ?>
                    <?php foreach ($mahasiswa_list as $row): ?>
                        <option value="<?= htmlspecialchars($row['id'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                            <?= htmlspecialchars($row['nama_mhs'] ?? 'Nama Tidak Tersedia', ENT_QUOTES, 'UTF-8') ?>
                        </option>
                    <?php endforeach; ?>
                <?php else: ?>
                    <option value="">Tidak ada mahasiswa tersedia</option>
                <?php endif; ?>
            </select>
            <?php
                // var_dump($row);
            ?>

            <label for="pelanggaran">Deskripsi Pelanggaran:</label>
            <textarea name="pelanggaran" placeholder="Deskripsi Pelanggaran" required></textarea>

            <label for="tanggal_pelanggaran">Tanggal Pelanggaran:</label>
            <input type="date" name="tanggal_pelanggaran" required>

            <button type="submit">Kirim Laporan</button>
        </form>
    </div>
</body>
</html>
