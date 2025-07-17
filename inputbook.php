<?php

include 'db.php'; // Pastikan koneksi database sudah benar

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $bookType = $_POST['bookType'];

    if ($bookType == 'book') {
        // Input data untuk Buku Biasa
        $bookid = $_POST['bookid'];
        $title = $_POST['title'];
        $author = $_POST['author'];
        $isbn = isset($_POST['isbn']) ? $_POST['isbn'] : null; // Ambil nilai ISBN (opsional)
        $desc = $_POST['desc'];
        $year = $_POST['year'];
        $categories = isset($_POST['book_category']) ? $_POST['book_category'] : [];

        try {
            // Mulai transaksi
            $pdo->beginTransaction();
            
            // Cek apakah book_id sudah ada
            $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM book WHERE book_id = ?");
            $checkStmt->execute([$bookid]);
            if ($checkStmt->fetchColumn() > 0) {
                throw new PDOException("Book ID already exists. Please use a different ID.");
            }

            // Masukkan buku ke tabel book dengan kolom ISBN baru
            $stmt = $pdo->prepare("INSERT INTO book (book_id, book_title, book_author, book_isbn, book_desc, book_year, book_timeadded) 
                                  VALUES (:bookid, :title, :author, :isbn, :desc, :year, NOW())");
            $stmt->execute([
                ':bookid' => $bookid,
                ':title' => $title,
                ':author' => $author,
                ':isbn' => $isbn,
                ':desc' => $desc,
                ':year' => $year
            ]);

            // Masukkan ke tabel assigncat jika kategori dipilih
            foreach ($categories as $cat_id) {
                // Cek jika pasangan book_id dan cat_id sudah ada
                $checkPairStmt = $pdo->prepare("SELECT COUNT(*) FROM assigncat WHERE book_id = ? AND cat_id = ?");
                $checkPairStmt->execute([$bookid, $cat_id]);
                $pairExists = $checkPairStmt->fetchColumn() > 0;
            
                if (!$pairExists) {
                    // Jika belum ada, lakukan insert
                    try {
                        $assignStmt = $pdo->prepare("INSERT INTO assigncat (book_id, cat_id) VALUES (?, ?)");
                        $assignStmt->execute([$bookid, $cat_id]);
                    } catch (PDOException $e) {
                        throw new PDOException("Error assigning category: " . $e->getMessage());
                    }
                }
            }

            // Commit transaksi jika semua berhasil
            $pdo->commit();
            echo "Book added successfully with ISBN and categories!";

        } catch (PDOException $e) {
            $pdo->rollBack(); // Rollback jika ada error
            echo "Error: " . $e->getMessage();
        }

    } elseif ($bookType == 'physicalBook') {
        // Input data untuk Buku Fisik
        $bookSelection = $_POST['bookSelection'];
        $copyId = $_POST['copyId'];

        try {
            $stmt = $pdo->prepare("INSERT INTO bookcopy (book_id, copy_id) VALUES (:bookSelection, :copyId)");
            $stmt->execute([
                ':bookSelection' => $bookSelection,
                ':copyId' => $copyId
            ]);
            echo "Physical book copy added successfully!";
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }
}

?>