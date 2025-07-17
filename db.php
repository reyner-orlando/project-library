<?php
$servername = "sql112.infinityfree.com";
$username = "if0_38248451";
$password = "winata20060714";
$database = "if0_38248451_LibRA";

// Create connection
try {
    // Membuat koneksi ke database menggunakan PDO
    $pdo = new PDO("mysql:host=$servername;dbname=$database", $username, $password);

    // Set error mode PDO ke exception untuk penanganan error yang lebih baik
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // echo "Koneksi berhasil!";
} catch (PDOException $e) {
    // Menangani error jika koneksi gagal
    // echo "Koneksi gagal: " . $e->getMessage();
}

// echo "Connected successfully";

$sql = "SELECT count(book_id) as jumlahbuku FROM book";
$stmt = $pdo->query($sql);

$rowbuku = $stmt->fetch(PDO::FETCH_ASSOC);

$sql = "SELECT count(fine_id) as jumlahfine FROM fine WHERE is_paid = 0";
$stmt = $pdo->query($sql);

$rowfine = $stmt->fetch(PDO::FETCH_ASSOC);

$sql = "SELECT count(brw_id) as jumlahpinjam FROM borrowing WHERE brw_status = 'ongoing'";
$stmt = $pdo->query($sql);

$rowborrow = $stmt->fetch(PDO::FETCH_ASSOC);

$sql = "SELECT COUNT(*) AS jumlah_buku_baru
FROM book
WHERE book_timeadded >= DATE_SUB(CURDATE(), INTERVAL 7 DAY);
";
$stmt = $pdo->query($sql);
$rowterbaru = $stmt->fetch(PDO::FETCH_ASSOC);

$sql = "SELECT COUNT(*) AS jumlahuser
FROM user
WHERE status_id = 1;
";
$stmt = $pdo->query($sql);
$rowuser = $stmt->fetch(PDO::FETCH_ASSOC);



?>