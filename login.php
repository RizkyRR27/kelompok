<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    try {
        // Parameterized query
        $query = "SELECT * FROM dbo.login WHERE username = :username AND role = :role";
        $stmt = $conn->prepare($query);
        $stmt->execute(['username' => $username, 'role' => $role]);

        if ($stmt) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // Verify password using password_verify
            if ($password == $user['password']) {
               // if ($user && password_verify($password, $user['password'])) {
            // if (password_verify($password, $user['password'])) {

                // Regenerate session ID to prevent session fixation
                session_regenerate_id(true);

               
                if ($user['role'] == 'dosen') {
                    $queryDosen = "SELECT * FROM dosen WHERE id_login = :id_login";
                    $stmtDosen = $conn->prepare($queryDosen);
                    $stmtDosen->bindParam(':id_login', $user['no'], PDO::PARAM_INT);
                    $stmtDosen->execute();
                    $dosen = $stmtDosen->fetch(PDO::FETCH_ASSOC);
    
                    if ($dosen) {
                        $_SESSION['data_user'] = $dosen;
                    } else {
                        $error = "Data dosen tidak ditemukan!";
                        exit();
                    }
                }

                

                $_SESSION['id'] = $user['id_dosen'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['nama_dosen'] = $user['nama_dosen'];


                // Redirect based on role
                switch ($user['role']) {
                    case 'admin':
                        header("Location: admin.php");
                        break;
                    case 'dosen':
                        header("Location: dosen.php");
                        break;
                    case 'mahasiswa':
                        header("Location: mahasiswa.php");
                        break;
                }
                exit();
            } else {
                $error = "Password salah!";
            }
        } else {
            $error = "Username tidak ditemukan!";
        }
    } catch (PDOException $e) {
        $error = "Terjadi kesalahan: " . htmlspecialchars($e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Sistem Tata Tertib</title>
    <link rel="stylesheet" href="tampilan.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    
</head>
<body>
    <div class="login-container">
        <h1>Login</h1>
        <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
        <form method="POST">
            <input type="text" name="username" placeholder="Username" required />
            <input type="password" name="password" placeholder="Password" required/>
            <select name="role" required>
                <option value="">Pilih Role</option>
                <option value="admin">Admin</option>
                <option value="mahasiswa">Mahasiswa</option>
                 <option value="dosen">Dosen</option>
                 <option value="dosen">Dosenn</option>
            </select>
            <button type="submit">Login</button>
        </form>
    </div>
</body>
</html>