<?php
session_start();
header('Content-Type: application/json');

require_once 'db.php';

try {
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
        throw new Exception('Akses ditolak. Hanya admin yang dapat menghapus buku.');
    }

    $input = json_decode(file_get_contents('php://input'), true);

    if (!isset($input['book_id'])) {
        throw new Exception('ID buku tidak ditemukan.');
    }

    $book_id = intval($input['book_id']);

    $stmt = $pdo->prepare("DELETE FROM book WHERE book_id = :book_id");
    $stmt->bindParam(':book_id', $book_id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Buku berhasil dihapus.'
        ]);
    } else {
        throw new Exception('Gagal menghapus buku dari database.');
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
exit;
?>
