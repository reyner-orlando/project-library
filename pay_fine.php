<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['fine_id'])) {
    $fine_id = $_POST['fine_id'];

    try {
        $stmt = $pdo->prepare("UPDATE fine SET is_paid = 1 WHERE fine_id = ?");
        $stmt->execute([$fine_id]);
        header("Location: dashboard_borrow.php"); // ganti dengan nama file utama Anda
        exit;
    } catch (PDOException $e) {
        die("Gagal mengupdate status pembayaran: " . $e->getMessage());
    }
} else {
    header("Location: dashboard_borrow.php");
    exit;
}
