<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $brw_id = $_POST['brw_id'];
    $copy_id = $_POST['copy_id'];

    try {
        $stmt = $pdo->prepare("UPDATE borrowing SET copy_id = ?, brw_status = 'ongoing' WHERE brw_id = ?");
        $stmt->execute([$copy_id, $brw_id]);
        $stmt = $pdo->prepare("UPDATE bookcopy SET copy_status = 'Borrowed' WHERE copy_id = ?");
        $stmt->execute([$copy_id]);
        header("Location: dashboard_borrow.php"); // Ganti ke nama file tampilan utama jika berbeda
        exit();
    } catch (PDOException $e) {
        die("Gagal update: " . $e->getMessage());
    }
}
?>
