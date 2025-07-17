<?php
session_start();
include 'db.php';

header('Content-Type: text/plain');

// Ambil dan validasi data dari POST
$book_id = intval($_POST['book_id'] ?? 0);
$book_title = trim($_POST['book_title'] ?? '');
$book_author = trim($_POST['book_author'] ?? '');
$borrow_date = trim($_POST['borrow_date'] ?? '');
$return_date = trim($_POST['return_date'] ?? '');
$user_id = $_SESSION['user_id'];

// Validasi umum   
if ($book_id <= 0 || empty($book_title) || empty($borrow_date) || empty($return_date)) {
    http_response_code(400);
    echo "Please fill all required fields.";
    exit;
}

// Validasi format tanggal
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $borrow_date) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $return_date)) {
    http_response_code(400);
    echo "Invalid date format. Use YYYY-MM-DD.";
    exit;
}

// Validasi logika tanggal
if (strtotime($return_date) <= strtotime($borrow_date)) {
    http_response_code(400);
    echo "Return date must be after borrow date.";
    exit;
}

try {
    // Simpan data ke database
    $stmt = $pdo->prepare("INSERT INTO borrowing (book_id, user_id, brw_date, due_date) VALUES (?, ?, ?, ?)");
    $stmt->execute([$book_id, $user_id, $borrow_date, $return_date]);

    echo "Book borrowing request submitted successfully!";
} catch (PDOException $e) {
    http_response_code(500);
    echo "Database error: " . $e->getMessage();
}
