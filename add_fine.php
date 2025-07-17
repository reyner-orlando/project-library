<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $brw_id = $_POST['brw_id'];
    $fine_amount = $_POST['fine_amount'];

    try {
        $stmt = $pdo->prepare("INSERT INTO fine (brw_id, fine_amount) VALUES (:id, :fine)");
        $stmt->execute([
            ':fine' => $fine_amount,
            ':id' => $brw_id
        ]);
        header("Location: dashboard_borrow.php"); // ganti dengan nama file utama jika beda
        exit;
    } catch (PDOException $e) {
        die("Error updating fine: " . $e->getMessage());
    }
}
?>
