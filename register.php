<?php
session_start();

// Konfigurasi database
$host = 'localhost';
$dbname = 'user_system';
$dbuser = 'root';
$dbpass = '';

$message = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $dbuser, $dbpass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Jika form disubmit
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $username = $_POST['username'];
        $email = $_POST['email'];
        $password = $_POST['password'];

        if (empty($username) || empty($email) || empty($password)) {
            $message = '<span style="color: red;">Semua kolom harus diisi.</span>';
        } else {
            // Cek apakah username atau email sudah digunakan
            $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username OR email = :email");
            $stmt->execute(['username' => $username, 'email' => $email]);

            if ($stmt->rowCount() > 0) {
                $message = '<span style="color: red;">Username atau Email sudah digunakan.</span>';
            } else {
                // Simpan ke database
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (:username, :email, :password)");
                $stmt->execute([
                    'username' => $username,
                    'email' => $email,
                    'password' => $hashedPassword
                ]);

                $message = '<span style="color: green;">Registrasi berhasil! </span>';
            }
        }
    }
} catch (PDOException $e) {
    $message = 'Koneksi gagal: ' . $e->getMessage();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Daftar Akun</title>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: #f8f9fa;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .form-container {
            background: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }
        h2 {
            text-align: center;
            margin-bottom: 25px;
            color: #343a40;
        }
        input[type="text"], input[type="email"], input[type="password"] {
            width: 100%;
            padding: 12px 16px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 16px;
            box-sizing: border-box;
        }
        .btn {
            width: 100%;
            padding: 12px;
            margin-top: 16px;
            border: none;
            border-radius: 8px;
            background-color: #007BFF;
            color: white;
            font-weight: bold;
            cursor: pointer;
            font-size: 16px;
        }
        .btn:hover {
            background-color: #0056b3;
        }
        .link {
            margin-top: 15px;
            text-align: center;
            font-size: 14px;
        }
        .link a {
            color: #007BFF;
            text-decoration: none;
        }
        .message {
            text-align: center;
            margin-top: 10px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Daftar Akun</h2>
        <?php if (!empty($message)): ?>
            <div class="message"><?= $message ?></div>
        <?php endif; ?>
        <form action="" method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button class="btn" type="submit">Register</button>
        </form>
        <div class="link">
            <p>Sudah punya akun? <a href="login.php">Login di sini</a></p>
        </div>
    </div>
</body>
</html>
