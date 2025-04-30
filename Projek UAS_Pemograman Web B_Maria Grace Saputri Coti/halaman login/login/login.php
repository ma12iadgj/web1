<?php
// Konfigurasi database
$host = "localhost";
$user = "root"; 
$pass = ""; 
$db   = "pemograman"; 

// Membuat koneksi ke database
$conn = new mysqli($host, $user, $pass, $db);

// Periksa koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Buat tabel jika belum ada
$sqlCreateTable = "CREATE TABLE IF NOT EXISTS user (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
)";
$conn->query($sqlCreateTable);

// Fungsi untuk proses registrasi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    $username = $_POST['uname'];
    $password = password_hash($_POST['pass'], PASSWORD_BCRYPT); // Hash password

    $sql = "INSERT INTO user (user, password) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $username, $password);

    if ($stmt->execute()) {
        echo "Registrasi berhasil! Silakan login.";
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}

// Fungsi untuk proses login
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['login'])) {
    $username = $_GET['uname'];
    $password = $_GET['pass'];

    $sql = "SELECT password FROM user WHERE user = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($hashedPassword);
        $stmt->fetch();
        if (password_verify($password, $hashedPassword)) {
            echo "Login berhasil! Selamat datang, " . htmlspecialchars($username) . ".";
        } else {
            echo "Password salah!";
        }
    } else {
        echo "Username tidak ditemukan!";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Registrasi dan Login</title>
</head>
<body>
    <h2>Registrasi</h2>
    <form action="" method="post">
    <p>Nama: <input type="text" name="nama"></p>
        <p>Jenis Kelamin: <input type="radio" name="kel" value="laki">Laki-laki
            <input type="radio" name="kel" value="perempuan">Perempuan
        </p>
        <p>Fakultas: <input type="text" name="Fakultas"></p>
        <p>Prodi: <input type="text" name="Prodi"></p>
        <p>Username: <input type="text" name="uname" required></p>
        <p>Password: <input type="password" name="pass" required></p>
        <input type="hidden" name="register" value="1">
        <p><input type="submit" value="Daftar"></p>
    </form>

    <h2>Login Ulang</h2>
    <form action="" method="get">
        <p>Username: <input type="text" name="uname" required></p>
        <p>Password: <input type="password" name="pass" required></p>
        <input type="hidden" name="login" value="1">
        <p><input type="submit" value="Masuk"></p>
    </form>
</body>
</html>
