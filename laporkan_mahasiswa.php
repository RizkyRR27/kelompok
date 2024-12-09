<?php
session_start();

// Cek sesi dan role
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'dosen') {
    header("Location: login.php");
    exit();
}

include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nim = $_POST['nim'];
    $kelas = $_POST['kelas'];
    $pelanggaran = $_POST['pelanggaran'];
    $tingkat = $_POST['tingkat'];
    $kejadian = $_POST['kejadian'];
    $id_dosen = $_SESSION['data_user']['id_dosen'];

    $dosen_yang_lapor = $_SESSION['data_user']['nama_dosen'];

    // Validasi input
    if (empty($nim) || empty($kelas) || empty($pelanggaran) || empty($tingkat) || empty($kejadian)) {
        $error = "Semua field wajib diisi.";
    } else {
        try {
            // Ambil nama mahasiswa berdasarkan NIM
            $query_nama_mhs = "SELECT * FROM mahasiswa WHERE nim = :nim";
            $stmt_nama_mhs = $conn->prepare($query_nama_mhs);
            $stmt_nama_mhs->bindParam(':nim', $nim, PDO::PARAM_STR);
            $stmt_nama_mhs->execute();
            
            // Ambil data mahasiswa
            $mahasiswa = $stmt_nama_mhs->fetch(PDO::FETCH_ASSOC);
        
            if (!$mahasiswa) {
                throw new Exception("Mahasiswa dengan NIM $nim tidak ditemukan.");
            }
        
            $nama_mhs = $mahasiswa['nama_mhs'];
            $id_mhs = $mahasiswa['id_mhs'];
        
            // Simpan laporan pelanggaran ke database
            $query = "INSERT INTO laporan (nim, nama_mhs, id_mhs, kelas, pelanggaran, tingkat, kejadian, id_dosen, dosen_yang_lapor, tanggal_laporan) 
            VALUES (:nim, :nama_mhs, :id_mhs, :kelas, :pelanggaran, :tingkat, :kejadian, :id_dosen, :dosen_yang_lapor, :tanggal_laporan)";
            
            $stmt = $conn->prepare($query);
            $stmt->execute([
                'nim' => htmlspecialchars($nim, ENT_QUOTES, 'UTF-8'),
                'nama_mhs' => htmlspecialchars($nama_mhs, ENT_QUOTES, 'UTF-8'),
                'id_mhs' => $id_mhs,
                'kelas' => htmlspecialchars($kelas, ENT_QUOTES, 'UTF-8'),
                'pelanggaran' => htmlspecialchars($pelanggaran, ENT_QUOTES, 'UTF-8'),
                'tingkat' => htmlspecialchars($tingkat, ENT_QUOTES, 'UTF-8'),
                'kejadian' => htmlspecialchars($kejadian, ENT_QUOTES, 'UTF-8'),
                'id_dosen' => $id_dosen,
                'dosen_yang_lapor' => $dosen_yang_lapor,
                'tanggal_laporan' => date('Y-m-d H:i:s'),
            ]);
            $success = "Laporan pelanggaran berhasil dikirim.";
            header("Location: output.php");
            exit();
        } catch (PDOException $e) {
            $error = "Terjadi kesalahan database: " . htmlspecialchars($e->getMessage());
        } catch (Exception $e) {
            $error = $e->getMessage();
        }
        
    }
}

// Ambil data mahasiswa dari database
$query_mahasiswa = "SELECT * FROM mahasiswa"; // Ganti 'mahasiswa' dengan nama tabel yang sesuai
$stmt_mahasiswa = $conn->prepare($query_mahasiswa);
$stmt_mahasiswa->execute();
$mahasiswa_options = $stmt_mahasiswa->fetchAll(PDO::FETCH_ASSOC);

// Ambil data kelas dari database
$query_kelas = "SELECT * FROM kelas"; // Ganti 'kelas' dengan nama tabel yang sesuai
$stmt_kelas = $conn->prepare($query_kelas);
$stmt_kelas->execute();
$kelas_options = $stmt_kelas->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporkan Mahasiswa</title>
    <link rel="stylesheet" href="tampilan.css">
</head>
<body>
    <h1>Laporkan Mahasiswa</h1>
   
    <nav>
        <ul>
            <li><a href="dosen_dashboard.php">Dashboard</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>
    <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
    <?php if (isset($success)) echo "<p style='color:green;'>$success</p>"; ?>
    
    <form method="POST">
        <select name="nim" required>
            <option value="">Pilih NIM Mahasiswa</option>
            <?php foreach ($mahasiswa_options as $mahasiswa) : ?>
                <option value="<?= htmlspecialchars($mahasiswa['nim'], ENT_QUOTES, 'UTF-8'); ?>">
                    <?= htmlspecialchars($mahasiswa['nama_mhs'], ENT_QUOTES, 'UTF-8'); ?> (<?= htmlspecialchars($mahasiswa['nim'], ENT_QUOTES, 'UTF-8'); ?>)
                </option>
            <?php endforeach; ?>
        </select>
        
        <select name="kelas" required>
            <option value="">Pilih Kelas</option>
            <?php foreach ($kelas_options as $kelas) : ?>
                <option value="<?= htmlspecialchars($kelas['id_kls'], ENT_QUOTES, 'UTF-8'); ?>">
                    <?= htmlspecialchars($kelas['nama_kls'], ENT_QUOTES, 'UTF-8'); ?>
                </option>
            <?php endforeach; ?>
        </select>
        
        <select name="tingkat" required>
            <option value="">Pilih Tingkat Pelanggaran</option>
            <option value="I">Tingkat I</option>
            <option value="II">Tingkat II</option>
            <option value="III">Tingkat III</option>
        </select>
        
        <textarea name="kejadian" placeholder="Deskripsi Kejadian" required></textarea>
        <textarea name="pelanggaran" placeholder="Deskripsi Pelanggaran" required></textarea>
        
        <button type="submit">Kirim Laporan</button>
    </form>
</body>
</html>
