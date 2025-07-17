<?php
include 'db.php';
session_start(); // Pastikan session dimulai
$is_admin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';

if (!$is_admin) {
    // Jika bukan admin, arahkan kembali ke halaman sebelumnya atau halaman login
    header('Location: booklist.php');
    exit();
}

// Cek apakah ada ID buku yang diterima dari URL
$book_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($book_id > 0) {
    // Ambil data buku berdasarkan ID
    try {
        $query = "SELECT * FROM book WHERE book_id = ?";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$book_id]);

        if ($stmt->rowCount() > 0) {
            $book = $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            // Jika buku tidak ditemukan, redirect
            header('Location: booklist.php');
            exit();
        }
    } catch (PDOException $e) {
        // Handle error
        echo "Error: " . $e->getMessage();
        exit();
    }
} else {
    // Jika ID tidak valid, redirect
    header('Location: booklist.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Proses form submit untuk menyimpan perubahan
    $book_title = $_POST['book_title'] ?? '';
    $book_author = $_POST['book_author'] ?? '';
    $book_desc = $_POST['book_desc'] ?? '';
    $book_year = $_POST['book_year'] ?? '';

    try {
        $update_query = "UPDATE book SET book_title = ?, book_author = ?, book_desc = ?, book_year = ? WHERE book_id = ?";
        $stmt = $pdo->prepare($update_query);
        $stmt->execute([$book_title, $book_author, $book_desc, $book_year, $book_id]);

        // Redirect setelah berhasil
        header('Location: book_template.php?id=' . $book_id);
        exit();
    } catch (PDOException $e) {
        echo "Error updating book: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Book - <?= htmlspecialchars($book['book_title']) ?></title>
    <!-- Include Bootstrap or other styles -->
</head>
<body>
    <div class="container">
        <h1>Edit Book - <?= htmlspecialchars($book['book_title']) ?></h1>
        <form method="POST">
            <div class="form-group">
                <label for="book_title">Title</label>
                <input type="text" name="book_title" id="book_title" class="form-control" value="<?= htmlspecialchars($book['book_title']) ?>" required>
            </div>
            <div class="form-group">
                <label for="book_author">Author</label>
                <input type="text" name="book_author" id="book_author" class="form-control" value="<?= htmlspecialchars($book['book_author']) ?>" required>
            </div>
            <div class="form-group">
                <label for="book_desc">Description</label>
                <textarea name="book_desc" id="book_desc" class="form-control" required><?= htmlspecialchars($book['book_desc']) ?></textarea>
            </div>
            <div class="form-group">
                <label for="book_year">Publication Year</label>
                <input type="text" name="book_year" id="book_year" class="form-control" value="<?= htmlspecialchars($book['book_year']) ?>" required>
            </div>
            <button type="submit" class="btn btn-success">Save Changes</button>
        </form>
    </div>
</body>
</html>
