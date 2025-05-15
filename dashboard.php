<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Koneksi database
$host = 'localhost';
$dbname = 'user_system';
$dbuser = 'root';
$dbpass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $dbuser, $dbpass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
    $stmt->execute(['id' => $_SESSION['user_id']]);
    $user = $stmt->fetch();

    if (!$user) {
        session_destroy();
        header("Location: login.php");
        exit();
    }
} catch (PDOException $e) {
    die("Koneksi gagal: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(to right, #eef2f3, #cfd9df);
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .dashboard-container {
            background: #fff;
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 500px;
            text-align: center;
        }
        h2 {
            margin-bottom: 8px;
            color: #2c3e50;
        }
        p {
            color: #666;
            margin-bottom: 30px;
        }
        .btn-group {
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
        }
        .btn {
            padding: 12px 24px;
            font-weight: 600;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }
        .edit {
            background: #3498db;
            color: white;
        }
        .edit:hover {
            background: #2980b9;
        }
        .delete {
            background: #e74c3c;
            color: white;
        }
        .delete:hover {
            background: #c0392b;
        }
        .logout {
            background: #2ecc71;
            color: white;
            width: 46%;
            margin-top: 10px;
        }
        .logout:hover {
            background: #27ae60;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <h2>Selamat datang, <?= htmlspecialchars($user['username']) ?>!</h2>
        <p>Email: <?= htmlspecialchars($user['email']) ?></p>

        <div class="btn-group">
            <a href="edit_profile.php" class="btn edit">Edit Profil</a>
            <a href="delete_account.php" class="btn delete" onclick="return confirm('Yakin ingin menghapus akun?')">Hapus Akun</a>
            <a href="logout.php" class="btn logout"  onclick="return confirm('Apakah yakin mau keluar?')">Logout</a>
        </div>
    </div>
</body>
</html>
