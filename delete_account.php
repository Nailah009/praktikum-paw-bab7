<?php
require_once 'config.php';
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}
$id = $_SESSION['user']['id'];

try {
    $stmt = $conn->prepare("DELETE FROM users WHERE id = :id");
    $stmt->bindParam(":id", $id);
    $stmt->execute();

    session_destroy();
    header("Location: register.php");
    exit;
} catch (PDOException $e) {
    echo "Gagal menghapus akun: " . $e->getMessage();
}
