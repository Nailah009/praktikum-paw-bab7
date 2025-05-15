<?php
require_once 'config.php';
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$id = $_SESSION['user_id'];
$message = "";

try {
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = :id");
    $stmt->bindParam(":id", $id);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        session_destroy();
        header("Location: login.php");
        exit();
    }
} catch (PDOException $e) {
    die("Koneksi gagal: " . $e->getMessage());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $newUsername = trim($_POST["username"]);
    $newEmail = trim($_POST["email"]);

    try {
        $checkStmt = $conn->prepare("SELECT id FROM users WHERE username = :username AND id != :id");
        $checkStmt->bindParam(":username", $newUsername);
        $checkStmt->bindParam(":id", $id);
        $checkStmt->execute();

        if ($checkStmt->rowCount() > 0) {
            $message = "Username sudah dipakai, coba yang lain.";
        } else {
            $stmt = $conn->prepare("UPDATE users SET username = :username, email = :email WHERE id = :id");
            $stmt->bindParam(":username", $newUsername);
            $stmt->bindParam(":email", $newEmail);
            $stmt->bindParam(":id", $id);
            $stmt->execute();

            $message = "Profil berhasil diperbarui.";

            // Update data user buat ditampilkan ulang
            $user['username'] = $newUsername;
            $user['email'] = $newEmail;
        }
    } catch (PDOException $e) {
        $message = "Terjadi kesalahan: " . $e->getMessage();
    }
}
?>


<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Profil</title>
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
        .form-container {
            background: #fff;
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }
        h2 {
            margin-bottom: 20px;
            color: #2c3e50;
        }
        input {
            width: 90%;
            padding: 12px 16px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 10px;
            font-size: 14px;
        }
        button {
            padding: 12px;
            background: #3498db;
            color: white;
            border: none;
            width: 100%;
            border-radius: 10px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        button:hover {
            background: #2980b9;
        }
        .message {
            margin-top: 15px;
            font-weight: 600;
        }
        .message.success {
            color: #2ecc71;
        }
        .message.error {
            color: #e74c3c;
        }
        a {
            display: block;
            margin-top: 20px;
            color: #3498db;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Edit Profil</h2>
        <form method="POST">
            <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" required>
            <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
            <button type="submit">Simpan Perubahan</button>
        </form>

        <?php if ($message): ?>
            <p class="message <?= strpos($message, 'berhasil') !== false ? 'success' : 'error' ?>">
                <?= $message ?>
            </p>
        <?php endif; ?>

        <a href="dashboard.php">← Kembali ke Dashboard</a>
    </div>
</body>
</html>