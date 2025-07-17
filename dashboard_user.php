<?php
// Start session if not already started

    session_start();
    echo "Session status: " . session_status() . "<br>";
echo "Session user_id: " . ($_SESSION['user_id'] ?? 'Not set') . "<br>";
 ?>
<?php include 'header.php'; ?>
<?php include 'db.php'; ?>
<?php include 'nav2.php'; ?>


<?php
// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // For testing/development - set a default user_id if not logged in
    // In production, you would redirect to login page
    // $_SESSION['user_id'] = 1; // Using a default user ID for testing
    // Uncomment the following lines in production
     header('Location: login.php');
     exit;
} ?>
<?php
// Fetch user information
$userId = $_SESSION['user_id'];
try {
    $stmt = $pdo->prepare("SELECT * FROM user WHERE user_id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // If user not found, create a placeholder for display purposes
    if (!$user) {
        $user = [
            'name' => 'Demo User',
            'email' => 'demo@example.com',
            'phone' => '123-456-7890',
            'address' => 'Demo Address',
            'created_at' => date('Y-m-d')
        ];
    }
} catch (PDOException $e) {
    // Handle database error
    $user = [
        'name' => 'Demo User',
        'email' => 'demo@example.com',
        'phone' => '123-456-7890',
        'address' => 'Demo Address',
        'created_at' => date('Y-m-d')
    ];
}

// Fetch current borrowings
try {
    $stmtCurrent = $pdo->prepare("
        SELECT br.*, b.*
        FROM borrowing br 
        JOIN book b ON br.book_id = b.book_id 
        WHERE br.user_id = ? AND br.brw_status = 'ongoing' AND (br.return_date IS NULL OR br.return_date = '')
        ORDER BY br.due_date ASC
    ");
    $stmtCurrent->execute([$userId]);
    $currentBorrowings = $stmtCurrent->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Handle database error
    $currentBorrowings = [];
}

// Fetch borrowing history - now includes rejected borrowings
try {
    $stmtHistory = $pdo->prepare("
        SELECT br.*, b.*
        FROM borrowing br 
        JOIN book b ON br.book_id = b.book_id 
        WHERE br.user_id = ? AND (br.return_date IS NOT NULL OR br.brw_status = 'rejected')
        ORDER BY 
            CASE 
                WHEN br.brw_status = 'rejected' THEN br.brw_date 
                ELSE br.return_date 
            END DESC
        LIMIT 10
    ");
    $stmtHistory->execute([$userId]);
    $borrowingHistory = $stmtHistory->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Handle database error
    $borrowingHistory = [];
}

// Fetch fines
try {
    $stmtFines = $pdo->prepare("
        SELECT f.*, br.brw_date, br.due_date, br.return_date, b.book_title
        FROM fine f
        JOIN borrowing br ON f.brw_id = br.brw_id
        JOIN book b ON br.book_id = b.book_id
        WHERE br.user_id = ?
        ORDER BY br.due_date DESC
    ");
    $stmtFines->execute([$userId]);
    $fines = $stmtFines->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Handle database error
    $fines = [];
}

// Calculate total unpaid fines
$totalUnpaidFines = 0;
foreach ($fines as $fine) {
    if ($fine['is_paid'] == 0) {
        $totalUnpaidFines += $fine['fine_amount'];
    }
}

// Handle logout
if (isset($_POST['logout'])) {
    // Destroy the session
    session_destroy();
    
    // Redirect to login page
    header('Location: login.php');
    exit;
}

// Handle tab switching
$activeTab = isset($_GET['tab']) ? $_GET['tab'] : 'profile';
?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
<div class="main-content">
    <div class="container py-4">
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <h1 class="mb-0">My Dashboard</h1>
                    <a href="logout.php" class="btn btn-outline-danger">
                        <i class="bi bi-box-arrow-right"></i> Logout
                    </a>
                </div>
                <hr>
            </div>
        </div>

        <!-- User Summary Cards -->
        <div class="row mb-4">
            <!-- Current Borrowings Card -->
            <div class="col-md-4 mb-3">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body d-flex flex-column">
                        <div class="d-flex align-items-center mb-3">
                            <i class="bi bi-book text-primary fs-1 me-3"></i>
                            <div>
                                <h5 class="card-title mb-0">Current Borrowings</h5>
                                <p class="text-muted small mb-0">Books you currently have</p>
                            </div>
                        </div>
                        <h2 class="display-4 fw-bold text-center my-3"><?php echo count($currentBorrowings); ?></h2>
                        <a href="?tab=current" class="btn btn-sm btn-outline-primary mt-auto">View Details</a>
                    </div>
                </div>
            </div>

            <!-- Borrowing History Card -->
            <div class="col-md-4 mb-3">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body d-flex flex-column">
                        <div class="d-flex align-items-center mb-3">
                            <i class="bi bi-clock-history text-success fs-1 me-3"></i>
                            <div>
                                <h5 class="card-title mb-0">Borrowing History</h5>
                                <p class="text-muted small mb-0">Your past borrowings</p>
                            </div>
                        </div>
                        <h2 class="display-4 fw-bold text-center my-3"><?php echo count($borrowingHistory); ?></h2>
                        <a href="?tab=history" class="btn btn-sm btn-outline-success mt-auto">View History</a>
                    </div>
                </div>
            </div>

            <!-- Fines Card -->
            <div class="col-md-4 mb-3">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body d-flex flex-column">
                        <div class="d-flex align-items-center mb-3">
                            <i class="bi bi-cash-coin text-danger fs-1 me-3"></i>
                            <div>
                                <h5 class="card-title mb-0">Unpaid Fines</h5>
                                <p class="text-muted small mb-0">Outstanding payments</p>
                            </div>
                        </div>
                        <h2 class="display-4 fw-bold text-center my-3">Rp<?php echo number_format($totalUnpaidFines, 3); ?></h2>
                        <a href="?tab=fines" class="btn btn-sm btn-outline-danger mt-auto">View Fines</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabs Navigation -->
        <ul class="nav nav-tabs mb-4">
            <li class="nav-item">
                <a class="nav-link <?php echo $activeTab == 'profile' ? 'active' : ''; ?>" href="?tab=profile">
                    <i class="bi bi-person"></i> Profile
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo $activeTab == 'current' ? 'active' : ''; ?>" href="?tab=current">
                    <i class="bi bi-book"></i> Current Borrowings
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo $activeTab == 'history' ? 'active' : ''; ?>" href="?tab=history">
                    <i class="bi bi-clock-history"></i> Borrowing History
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo $activeTab == 'fines' ? 'active' : ''; ?>" href="?tab=fines">
                    <i class="bi bi-cash-coin"></i> Fines
                </a>
            </li>
        </ul>

        <!-- Tab Content -->
        <div class="tab-content">
            <!-- Profile Tab -->
            <div class="tab-pane fade <?php echo $activeTab == 'profile' ? 'show active' : ''; ?>" id="profile">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 text-center mb-4 mb-md-0">
                                <div class="mb-3">
                                    <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($user['name'] ?? 'User'); ?>&background=random&color=fff&size=200" 
                                         class="rounded-circle img-thumbnail" alt="Profile Picture" style="width: 180px; height: 180px;">
                                </div>
                                <h4><?php echo htmlspecialchars($user['name'] ?? 'User Name'); ?></h4>
                                <p class="text-muted">
                                    <i class="bi bi-person-badge"></i> 
                                    Member since: <?php echo isset($user['created_at']) ? date('M d, Y', strtotime($user['created_at'])) : 'N/A'; ?>
                                </p>
                            </div>
                            <div class="col-md-8">
                                <h5 class="border-bottom pb-2 mb-3">Personal Information</h5>
                                <div class="row mb-3">
                                    <div class="col-md-4 fw-bold">Full Name:</div>
                                    <div class="col-md-8"><?php echo htmlspecialchars($user['user_fullname'] ?? 'N/A'); ?></div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-4 fw-bold">Email:</div>
                                    <div class="col-md-8"><?php echo htmlspecialchars($user['user_email'] ?? 'N/A'); ?></div>
                                </div>                                
                                <div class="mt-4">
                                    <a href="edit_profile.php" class="btn btn-primary">
                                        <i class="bi bi-pencil-square"></i> Edit Profile
                                    </a>
                                    <a href="change_password.php" class="btn btn-outline-secondary ms-2">
                                        <i class="bi bi-lock"></i> Change Password
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Current Borrowings Tab -->
            <div class="tab-pane fade <?php echo $activeTab == 'current' ? 'show active' : ''; ?>" id="current">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title mb-4">Current Borrowings</h5>
                        
                        <?php if (empty($currentBorrowings)): ?>
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle"></i> You don't have any books currently borrowed.
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Book</th>
                                            <th>Borrowed Date</th>
                                            <th>Due Date</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($currentBorrowings as $book): ?>
                                            <?php 
                                                $dueDate = new DateTime($book['due_date']);
                                                $today = new DateTime();
                                                $isOverdue = $today > $dueDate;
                                                $daysLeft = $today->diff($dueDate)->days;
                                                $daysLeftText = $isOverdue ? $daysLeft . ' days overdue' : $daysLeft . ' days left';
                                            ?>
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <img src="https://covers.openlibrary.org/b/isbn/<?=$book['book_isbn']?>-L.jpg"
                                                             alt="Book Cover" class="me-2" style="width: 40px; height: 60px; object-fit: cover;">
                                                        <div>
                                                            <div class="fw-bold"><?php echo htmlspecialchars($book['book_title']); ?></div>
                                                            <small class="text-muted"><?php echo htmlspecialchars($book['book_author']); ?></small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td><?php echo date('M d, Y', strtotime($book['brw_date'])); ?></td>
                                                <td><?php echo date('M d, Y', strtotime($book['due_date'])); ?></td>
                                                <td>
                                                    <?php if ($isOverdue): ?>
                                                        <span class="badge bg-danger">Overdue</span>
                                                    <?php elseif ($daysLeft <= 3): ?>
                                                        <span class="badge bg-warning text-dark">Due Soon</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-success">On Time</span>
                                                    <?php endif; ?>
                                                    <div class="small mt-1"><?php echo $daysLeftText; ?></div>
                                                </td>
                                                <td>
                                                    <a href="extend_borrowing.php?id=<?php echo $book['brw_id']; ?>" class="btn btn-sm btn-outline-primary">
                                                        <i class="bi bi-arrow-clockwise"></i> Extend
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Borrowing History Tab -->
            <div class="tab-pane fade <?php echo $activeTab == 'history' ? 'show active' : ''; ?>" id="history">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title mb-4">Borrowing History</h5>
                        
                        <?php if (empty($borrowingHistory)): ?>
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle"></i> You don't have any borrowing history yet.
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th width="35%">Book</th>
                                            <th width="15%">Borrowed Date</th>
                                            <th width="15%">Return Date</th>
                                            <th width="15%">Status</th>
                                            <th width="20%">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($borrowingHistory as $book): ?>
                                            <?php 
                                            // Handle different statuses
                                            if ($book['brw_status'] == 'rejected') {
                                                $statusClass = 'bg-secondary';
                                                $statusText = 'Request Rejected';
                                            } else {
                                                $dueDate = new DateTime($book['due_date']);
                                                $returnDate = new DateTime($book['return_date']);
                                                $isLate = $returnDate > $dueDate;
                                                
                                                $statusClass = $isLate ? 'bg-danger' : 'bg-success';
                                                $statusText = $isLate ? 'Returned Late' : 'Returned On Time';
                                            }
                                            ?>
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <img src="https://covers.openlibrary.org/b/isbn/<?=$book['book_isbn']?>-L.jpg"
                                                             alt="Book Cover" class="me-2" style="width: 40px; height: 60px; object-fit: cover;">
                                                        <div>
                                                            <div class="fw-bold"><?php echo htmlspecialchars($book['book_title']); ?></div>
                                                            <small class="text-muted"><?php echo htmlspecialchars($book['book_author']); ?></small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td><?php echo date('M d, Y', strtotime($book['brw_date'])); ?></td>
                                                <td>
                                                    <?php 
                                                    if ($book['brw_status'] == 'rejected') {
                                                        echo '<span class="text-muted">N/A</span>';
                                                    } elseif (!empty($book['return_date']) && $book['return_date'] != '0000-00-00' && strtotime($book['return_date']) > 0) {
                                                        echo date('M d, Y', strtotime($book['return_date']));
                                                    } else {
                                                        echo 'Not returned yet';
                                                    }
                                                    ?>
                                                </td>
                                                <td>
                                                    <span class="badge <?php echo $statusClass; ?>"><?php echo $statusText; ?></span>
                                                    <?php if ($book['brw_status'] == 'rejected'): ?>
                                                        <div class="small mt-1">Request was declined</div>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if ($book['brw_status'] == 'done' && $book['return_date'] != NULL && $book['return_date'] != '0000-00-00'): ?>
                                                        <?php 
                                                        // Check if user has already reviewed this book
                                                        $reviewStmt = $pdo->prepare("SELECT * FROM bookreview WHERE book_id = ? AND user_id = ?");
                                                        $reviewStmt->execute([$book['book_id'], $userId]);
                                                        $hasReviewed = $reviewStmt->rowCount() > 0;
                                                        ?>
                                                        
                                                        <?php if ($hasReviewed): ?>
                                                            <div class="d-flex flex-column">
                                                                <span class="badge badge-success mb-2"style="border-radius: 20px;"><i class="bi bi-check-circle-fill" ></i> Reviewed</span>
                                                                <a href="book_review.php?book_id=<?php echo $book['book_id']; ?>&title=<?php echo urlencode($book['book_title']); ?>" 
                                                                   class="btn btn-sm btn-outline-primary" style="border-radius: 20px;">
                                                                    <i class="bi bi-pencil-square"></i> Update Review
                                                                </a>
                                                            </div>
                                                        <?php else: ?>
                                                            
                                                            <!-- Direct link as fallback -->
                                                            <a href="book_review.php?book_id=<?php echo $book['book_id']; ?>&title=<?php echo urlencode($book['book_title']); ?>" 
                                                               class="btn btn-sm btn-outline-primary mt-1" style="border-radius: 20px;">
                                                                <i class="bi bi-pencil-fill"></i> Write Review
                                                            </a>
                                                        <?php endif; ?>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Fines Tab -->
            <div class="tab-pane fade <?php echo $activeTab == 'fines' ? 'show active' : ''; ?>" id="fines">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title mb-4">Fines</h5>
                        
                        <?php if (empty($fines)): ?>
                            <div class="alert alert-success">
                                <i class="bi bi-check-circle"></i> You don't have any fines. Keep returning books on time!
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Book</th>
                                            <th>Fine Date</th>
                                            <th>Amount</th>
                                            <th>Reason</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($fines as $fine): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($fine['book_title']); ?></td>
                                                <td><?php echo date('M d, Y', strtotime($fine['due_date'])); ?></td>
                                                <td>Rp<?php echo number_format($fine['fine_amount'], 3); ?>,00</td>
                                                <td>yes</td>
                                                <td>
                                                    <?php if ($fine['is_paid'] == '1'): ?>
                                                        <span class="badge bg-success">Paid</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-danger">Unpaid</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if ($fine['is_paid'] == '0'): ?>
                                                        <a href="pay_fine.php?id=<?php echo $fine['fine_id']; ?>" class="btn btn-sm btn-primary">
                                                            <i class="bi bi-credit-card"></i> Pay Now
                                                        </a>
                                                    <?php else: ?>
                                                        <button class="btn btn-sm btn-outline-secondary" disabled>
                                                            <i class="bi bi-check-circle"></i> Paid
                                                        </button>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            
                            <?php if ($totalUnpaidFines > 0): ?>
                                <div class="alert alert-warning mt-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <i class="bi bi-exclamation-triangle"></i> 
                                            You have <strong>$<?php echo number_format($totalUnpaidFines, 2); ?></strong> in unpaid fines.
                                        </div>
                                        <a href="pay_all_fines.php" class="btn btn-warning">
                                            <i class="bi bi-credit-card"></i> Pay All Fines
                                        </a>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



<?php include 'footer.php'; ?>