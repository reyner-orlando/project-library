<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $brw_id = $_POST['borrowid'];

    try {
        // Ambil copy_id dari peminjaman yang dimaksud
        $stmt = $pdo->prepare("SELECT copy_id FROM borrowing WHERE brw_id = ?");
        $stmt->execute([$brw_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result && $result['copy_id']) {
            $copy_id = $result['copy_id'];

            // Update status peminjaman
            $stmt = $pdo->prepare("UPDATE borrowing 
                                   SET brw_status = 'done', return_date = CURDATE() 
                                   WHERE brw_id = ?");
            $stmt->execute([$brw_id]);

            // Tandai copy sebagai available lagi
            $stmt = $pdo->prepare("UPDATE bookcopy 
                                   SET copy_status = 'Available'
                                   WHERE copy_id = ?");
            $stmt->execute([$copy_id]);
        }

        header("Location: dashboard_borrow.php");
        exit();
    } catch (PDOException $e) {
        die("Gagal mengembalikan buku: " . $e->getMessage());
    }
}
