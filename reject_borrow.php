<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $brw_id = $_POST['brw_id'];

    try {
        $stmt = $pdo->prepare("UPDATE borrowing SET brw_status = 'rejected' WHERE brw_id = ?");
        $stmt->execute([$brw_id]);

        header("Location: dashboard_borrow.php"); // Kembali ke dashboard
        exit();
    } catch (PDOException $e) {
        die("Gagal menolak peminjaman: " . $e->getMessage());
    }
}
?>
