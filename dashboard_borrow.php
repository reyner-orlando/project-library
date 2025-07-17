<?php
include 'nav2.php';
include 'header.php';
include 'db.php';

// Ambil nilai filter dari form (jika ada)
$bookTitleFilter = isset($_POST['book_title']) ? $_POST['book_title'] : '';
$usernameFilter = isset($_POST['username']) ? $_POST['username'] : '';
$dueDateFilter = isset($_POST['due_date']) ? $_POST['due_date'] : '';
$borrowStatusFilter = isset($_POST['brw_status']) ? $_POST['brw_status'] : '';

// Menambahkan filter ke query SQL
$sql = "SELECT DISTINCT b.*, b.brw_id as borrowid, bk.book_title as BookName, u.user_fullname as Username, f.* 
        FROM borrowing b 
        LEFT JOIN user u ON u.user_id = b.user_id 
        LEFT JOIN bookcopy bc ON bc.book_id = b.book_id 
        LEFT JOIN book bk ON bc.book_id = bk.book_id 
        LEFT JOIN fine f ON f.brw_id = b.brw_id
        WHERE 1=1";

if ($bookTitleFilter) {
    $sql .= " AND bk.book_title LIKE :book_title";
}
if ($usernameFilter) {
    $sql .= " AND u.user_fullname LIKE :username";
}
if ($dueDateFilter) {
    $sql .= " AND b.due_date = :due_date";
}
if ($borrowStatusFilter) {
    $sql .= " AND b.brw_status = :borrow_status";
}

$sql .= " ORDER BY brw_date ASC";

// Menyiapkan dan mengeksekusi query
try {
    $stmt = $pdo->prepare($sql);

    // Bind parameter filter
    if ($bookTitleFilter) {
        $stmt->bindValue(':book_title', "%$bookTitleFilter%");
    }
    if ($usernameFilter) {
        $stmt->bindValue(':username', "%$usernameFilter%");
    }
    if ($dueDateFilter) {
        $stmt->bindValue(':due_date', $dueDateFilter);
    }
    if ($borrowStatusFilter) {
        $stmt->bindValue(':borrow_status', $borrowStatusFilter);
    }

    $stmt->execute();
    $borrowings = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Koneksi atau query gagal: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LibRA - Status Peminjaman Buku</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            background-color: #f4f7fb;
        }
        
        .page-header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 3rem 0;
            border-radius: 0 0 1rem 1rem;
            margin-bottom: 2rem;
        }

        .page-title {
            font-size: 2rem;
            font-weight: 600;
            margin-bottom: 1rem;
        }
        
        .page-description {
            font-size: 1.1rem;
            font-weight: 300;
            max-width: 700px;
            margin-bottom: 0;
        }
        
        .content-container {
            background-color: white;
            border-radius: 1rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            padding: 2rem;
            margin-bottom: 2rem;
        }
        
        .content-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--secondary-color);
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
        }
        
        .content-title i {
            margin-right: 0.75rem;
            font-size: 1.75rem;
            color: var(--primary-color);
        }
        
        .form-label {
            font-weight: 500;
            color: var(--secondary-color);
            margin-bottom: 0.5rem;
        }
        
        .form-control, .form-select {
            border: 1px solid #ddd;
            border-radius: 0.5rem;
            padding: 0.75rem 1rem;
            transition: all 0.3s ease;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(63, 114, 175, 0.25);
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            color: white;
            border: none;
            border-radius: 0.5rem;
            padding: 0.75rem 1.5rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            background-color: var(--secondary-color);
        }
        
        .btn-success {
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 0.5rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .btn-success:hover {
            background-color: #218838;
        }
        
        .btn-danger {
            background-color: #dc3545;
            color: white;
            border: none;
            border-radius: 0.5rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .btn-danger:hover {
            background-color: #c82333;
        }
        
        .btn-warning {
            background-color: #ffc107;
            color: #212529;
            border: none;
            border-radius: 0.5rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .btn-warning:hover {
            background-color: #e0a800;
        }
        
        .btn-outline-success {
            color: #28a745;
            border-color: #28a745;
            border-radius: 0.5rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .btn-outline-success:hover {
            background-color: #28a745;
            color: white;
        }
        
        .table {
            border-radius: 0.5rem;
            overflow: hidden;
        }
        
        .table thead {
            background-color: var(--primary-color);
            color: white;
        }
        
        .table th {
            font-weight: 500;
            padding: 1rem;
        }
        
        .table td {
            padding: 1rem;
            vertical-align: middle;
        }
        
        .status-returned {
            color: #28a745;
            font-weight: 600;
        }
        
        .status-borrowed {
            color: #dc3545;
            font-weight: 600;
        }
        
        .badge {
            padding: 0.5rem 0.75rem;
            border-radius: 0.5rem;
            font-weight: 500;
        }
        
        .info-card {
            background-color: white;
            border-radius: 1rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            padding: 1.5rem;
            height: 100%;
        }
        
        .info-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--secondary-color);
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
        }
        
        .info-title i {
            margin-right: 0.75rem;
            font-size: 1.5rem;
            color: var(--primary-color);
        }
        
        .info-item {
            display: flex;
            margin-bottom: 1rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #eee;
        }
        
        .info-item:last-child {
            margin-bottom: 0;
            padding-bottom: 0;
            border-bottom: none;
        }
        
        .info-icon {
            width: 40px;
            height: 40px;
            background-color: var(--accent-color);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
            color: var(--primary-color);
            font-size: 1.25rem;
            flex-shrink: 0;
        }
        
        .info-text h4 {
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 0.25rem;
            color: var(--secondary-color);
        }
        
        .info-text p {
            font-size: 0.9rem;
            color: #666;
            margin: 0;
        }
        
        .alert {
            border-radius: 0.5rem;
            padding: 1rem 1.5rem;
        }
        
        @media (min-width: 992px) {
            .main-content {
                margin-left: 250px;
            }
        }
    </style>
</head>
<body>
    <div class="main-content">
        <!-- Page Header -->
        <div class="page-header">
            <div class="container">
                <h1 class="page-title">Status Peminjaman Buku</h1>
                <p class="page-description">Kelola dan pantau semua status peminjaman buku di perpustakaan. Gunakan filter untuk menemukan peminjaman tertentu dengan cepat.</p>
            </div>
        </div>

        <div class="container">
            <div class="row">
                <!-- Main Content -->
                <div class="col-lg-8">
                    <div class="content-container">
                        <h2 class="content-title"><i class="bi bi-list-check"></i> Daftar Peminjaman</h2>
                        
                        <!-- Filter Form -->
                        <form method="POST" class="mb-4">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="book_title" class="form-label">Judul Buku</label>
                                        <input type="text" name="book_title" id="book_title" class="form-control" placeholder="Cari judul buku..." value="<?= htmlspecialchars($bookTitleFilter) ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="username" class="form-label">Nama Peminjam</label>
                                        <input type="text" name="username" id="username" class="form-control" placeholder="Cari nama peminjam..." value="<?= htmlspecialchars($usernameFilter) ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="due_date" class="form-label">Tanggal Tenggat</label>
                                        <input type="date" name="due_date" id="due_date" class="form-control" value="<?= htmlspecialchars($dueDateFilter) ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="brw_status" class="form-label">Status Peminjaman</label>
                                        <select name="brw_status" id="brw_status" class="form-select">
                                            <option value="">Semua Status</option>
                                            <option value="ongoing" <?= $borrowStatusFilter === 'ongoing' ? 'selected' : '' ?>>Sedang Dipinjam</option>
                                            <option value="done" <?= $borrowStatusFilter === 'done' ? 'selected' : '' ?>>Dikembalikan</option>
                                            <option value="rejected" <?= $borrowStatusFilter === 'rejected' ? 'selected' : '' ?>>Ditolak</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary"><i class="bi bi-search me-2"></i> Filter</button>
                                </div>
                            </div>
                        </form>
                        
                        <!-- Table -->
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped align-middle">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Judul Buku</th>
                                        <th>Nama Peminjam</th>
                                        <th>Tanggal Pinjam</th>
                                        <th>Tenggat</th>
                                        <th>Status</th>
                                        <th>Copy ID</th>
                                        <th>Denda</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (count($borrowings) > 0): ?>
                                        <?php foreach($borrowings as $row): ?>
                                            <tr>
                                                <td><?= $row["borrowid"] ?></td>
                                                <td><?= htmlspecialchars($row["BookName"]) ?></td>
                                                <td><?= htmlspecialchars($row["Username"]) ?></td>
                                                <td><?= $row["brw_date"] ?></td>
                                                <td><?= $row["due_date"] ?></td>
                                                <td class="<?= $row["brw_status"] === 'done' ? 'status-returned' : 'status-borrowed' ?>">
                                                    <?= $row["brw_status"] ?>
                                                    <?php if ($row["brw_status"] === 'ongoing'): ?>
                                                        <form method="POST" action="return_book.php" style="margin-top: 5px;">
                                                            <input type="hidden" name="borrowid" value="<?= $row["borrowid"] ?>">
                                                            <button type="submit" class="btn btn-success btn-sm"><i class="bi bi-check-circle me-1"></i> Kembalikan</button>
                                                        </form>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if (!$row['copy_id'] && $row['brw_status'] !== 'rejected'): ?>
                                                        <form method="POST" action="assign_copy.php">
                                                            <input type="hidden" name="brw_id" value="<?= $row['borrowid'] ?>">
                                                            <select name="copy_id" class="form-select form-select-sm" required>
                                                                <?php
                                                                $bookId = $row['book_id'];
                                                                $stmt2 = $pdo->prepare("SELECT copy_id 
                                                                                FROM bookcopy 
                                                                                WHERE book_id = ? 
                                                                                AND copy_id NOT IN (
                                                                                    SELECT copy_id 
                                                                                    FROM borrowing 
                                                                                    WHERE brw_status = 'ongoing' 
                                                                                    AND copy_id IS NOT NULL
                                                                                )");
                                                                $stmt2->execute([$bookId]);
                                                                $availableCopies = $stmt2->fetchAll(PDO::FETCH_ASSOC);
                                                                foreach ($availableCopies as $copy) {
                                                                    echo "<option value=\"{$copy['copy_id']}\">{$copy['copy_id']}</option>";
                                                                }
                                                                ?>
                                                            </select>
                                                            <button type="submit" class="btn btn-primary btn-sm mt-1"><i class="bi bi-link me-1"></i> Assign</button>
                                                        </form>
                                                        <form method="POST" action="reject_borrow.php" onsubmit="return confirm('Tolak permintaan ini?')" style="margin-top: 5px;">
                                                            <input type="hidden" name="brw_id" value="<?= $row['borrowid'] ?>">
                                                            <button type="submit" class="btn btn-danger btn-sm"><i class="bi bi-x-circle me-1"></i> Tolak</button>
                                                        </form>
                                                    <?php elseif ($row['brw_status'] === 'rejected'): ?>
                                                        <span class="text-muted">Ditolak</span>
                                                    <?php else: ?>
                                                        <?= $row['copy_id'] ?>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if ($row["fine_amount"]): ?>
                                                        <div>
                                                            Rp<?= number_format($row["fine_amount"] * 1000, 0, ',', '.') ?>
                                                            <?php if ($row["is_paid"] == 1): ?>
                                                                <span class="badge bg-success">Lunas</span>
                                                            <?php else: ?>
                                                                <form method="POST" action="pay_fine.php" onsubmit="return confirm('Konfirmasi pembayaran denda?')">
                                                                    <input type="hidden" name="fine_id" value="<?= $row['fine_id'] ?>">
                                                                    <button type="submit" class="btn btn-sm btn-outline-success mt-1"><i class="bi bi-cash-coin me-1"></i> Pay</button>
                                                                </form>
                                                            <?php endif; ?>
                                                        </div>
                                                    <?php elseif ($row["brw_status"] === 'ongoing' && date('Y-m-d') > $row["due_date"]): ?>
                                                        <button 
                                                            class="btn btn-warning btn-sm" 
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#fineModal"
                                                            onclick="setFineBorrowId(<?= $row['borrowid'] ?>)"
                                                        ><i class="bi bi-exclamation-triangle me-1"></i> Add Fine</button>
                                                    <?php else: ?>
                                                        <span class="text-muted">-</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr><td colspan="8" class="text-center">Tidak ada data peminjaman.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
                <!-- Sidebar -->
                <div class="col-lg-4">
                    <div class="info-card mb-4">
                        <h3 class="info-title"><i class="bi bi-info-circle"></i> Panduan Pengelolaan</h3>
                        <div class="info-item">
                            <div class="info-icon">
                                <i class="bi bi-book"></i>
                            </div>
                            <div class="info-text">
                                <h4>Status Peminjaman</h4>
                                <p>Pantau status peminjaman buku dengan mudah dan kelola pengembalian tepat waktu.</p>
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-icon">
                                <i class="bi bi-cash"></i>
                            </div>
                            <div class="info-text">
                                <h4>Pengelolaan Denda</h4>
                                <p>Tambahkan denda untuk keterlambatan dan catat pembayaran secara sistematis.</p>
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-icon">
                                <i class="bi bi-search"></i>
                            </div>
                            <div class="info-text">
                                <h4>Filter Data</h4>
                                <p>Gunakan filter untuk menemukan data peminjaman tertentu dengan cepat dan mudah.</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="info-card">
                        <h3 class="info-title"><i class="bi bi-graph-up"></i> Ringkasan</h3>
                        <?php
                        // Count statistics
                        $ongoingCount = 0;
                        $returnedCount = 0;
                        $lateCount = 0;
                        $today = date('Y-m-d');
                        
                        foreach($borrowings as $row) {
                            if ($row["brw_status"] === 'ongoing') {
                                $ongoingCount++;
                                if ($today > $row["due_date"]) {
                                    $lateCount++;
                                }
                            } elseif ($row["brw_status"] === 'done') {
                                $returnedCount++;
                            }
                        }
                        ?>
                        <div class="row g-3 mb-3">
                            <div class="col-6">
                                <div class="p-3 bg-primary bg-opacity-10 rounded text-center">
                                    <h4 class="m-0 text-primary"><?= $ongoingCount ?></h4>
                                    <p class="small mb-0">Sedang Dipinjam</p>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-3 bg-success bg-opacity-10 rounded text-center">
                                    <h4 class="m-0 text-success"><?= $returnedCount ?></h4>
                                    <p class="small mb-0">Dikembalikan</p>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="p-3 bg-danger bg-opacity-10 rounded text-center">
                                    <h4 class="m-0 text-danger"><?= $lateCount ?></h4>
                                    <p class="small mb-0">Terlambat</p>
                                </div>
                            </div>
                        </div>
                        <div class="d-grid gap-2 mt-4">
                            <a href="booklist.php" class="btn btn-outline-primary"><i class="bi bi-book me-2"></i> Lihat Katalog Buku</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Input Fine -->
    <div class="modal fade" id="fineModal" tabindex="-1" aria-labelledby="fineModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="fineModalLabel">Tambah Denda</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="add_fine.php">
                        <input type="hidden" name="brw_id" id="fine_brw_id">
                        <div class="mb-3">
                            <label for="fine_amount" class="form-label">Jumlah Denda (dalam ribuan Rupiah)</label>
                            <input type="number" class="form-control" id="fine_amount" name="fine_amount" min="1" required>
                        </div>
                        <div class="mb-3">
                            <label for="fine_reason" class="form-label">Alasan Denda</label>
                            <textarea class="form-control" id="fine_reason" name="fine_reason" rows="3" required></textarea>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">Simpan Denda</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function setFineBorrowId(brw_id) {
            document.getElementById('fine_brw_id').value = brw_id;
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>