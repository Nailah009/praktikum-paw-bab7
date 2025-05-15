<?php
session_start();

// Koneksi ke database
$host = 'localhost';
$dbname = 'user_system';
$dbuser = 'root';
$dbpass = '';

$message = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $dbuser, $dbpass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Kalau tombol login ditekan
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $username = $_POST['username'];
        $password = $_POST['password'];

        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
        $stmt->execute(['username' => $username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            header("Location: dashboard.php");
            exit();
        } else {
            $message = '<span style="color: red;">Username atau password salah.</span>';
        }
    }
} catch (PDOException $e) {
    $message = 'Koneksi gagal: ' . $e->getMessage();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: #f2f4f6;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .login-container {
            background: white;
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }
        h2 {
            text-align: center;
            margin-bottom: 25px;
            color: #343a40;
        }
        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 14px 16px;
            margin: 12px 0;
            border: 1px solid #ccc;
            border-radius: 10px;
            font-size: 16px;
            box-sizing: border-box;
        }
        .btn {
            width: 100%;
            padding: 14px;
            margin-top: 16px;
            border: none;
            border-radius: 12px;
            background-color: #28a745;
            color: white;
            font-weight: bold;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.2s ease;
        }
        .btn:hover {
            background-color: #218838;
        }
        .link {
            text-align: center;
            margin-top: 16px;
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
    <div class="login-container">
        <h2>Login</h2>
        <?php if (!empty($message)): ?>
            <div class="message"><?= $message ?></div>
        <?php endif; ?>
        <form action="" method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button class="btn" type="submit">Login</button>
        </form>
        <div class="link">
            Belum punya akun? <a href="register.php">Daftar di sini</a>
        </div>
    </div>
</body>
</html>
