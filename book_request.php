<?php include 'header.php'; ?>
<?php include 'db.php'; ?>
<?php include 'nav2.php'; ?>
<?php
// Initialize variables
$success_message = '';
$error_message = '';

// Get book information from URL parameters if available
$book_id = isset($_GET['book_id']) ? intval($_GET['book_id']) : 0;
$book_title = isset($_GET['book_title']) ? $_GET['book_title'] : '';
$book_author = isset($_GET['book_author']) ? $_GET['book_author'] : '';
$book_isbn = isset($_GET['book_isbn']) ? $_GET['book_isbn'] : '';

// For debugging
$debug_info = "";
$debug_info .= "GET Parameters: " . print_r($_GET, true) . "\n";
$debug_info .= "book_id: $book_id\n";
$debug_info .= "book_title: $book_title\n";
$debug_info .= "book_author: $book_author\n";

// If book ID is provided but title is empty, try to fetch from database
if ($book_id > 0 && empty($book_title)) {
    try {
        $query = "SELECT * FROM book WHERE book_id = ?";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$book_id]);
        
        if ($stmt->rowCount() > 0) {
            $book = $stmt->fetch(PDO::FETCH_ASSOC);
            $book_title = $book['book_title'] ?? '';
            $book_author = $book['book_author'] ?? '';
            $debug_info .= "Book fetched from database\n";
        }
    } catch (PDOException $e) {
        $debug_info .= "Database error: " . $e->getMessage() . "\n";
    }
}

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate input
    $book_title = trim($_POST['book_title'] ?? '');
    $book_author = trim($_POST['book_author'] ?? '');
    $book_isbn = trim($_POST['book_isbn'] ?? '');
    $request_reason = trim($_POST['request_reason'] ?? '');
    
    if (empty($book_title)) {
        $error_message = "Book title is required.";
    } else {
        // In a real application, you would insert the request into the database
        // For now, just show a success message
        $success_message = "Your book request has been submitted successfully! We will notify you once it's processed.";
    }
}

// Fetch all books for dropdown
$books = [];
try {
    $stmt = $pdo->query("SELECT 
  b.*,
  b.book_id as bookID,
  bc.*
FROM 
  bookcopy bc
JOIN 
  book b ON bc.book_id = b.book_id
WHERE 
  bc.copy_status = 'available'
  GROUP BY 
  b.book_id;;
");
    $books = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error_message = "Error fetching books: " . $e->getMessage();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LibRA - Request a Book</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
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
            max-width: 600px;
            margin-bottom: 0;
        }
        
        .form-container {
            background-color: white;
            border-radius: 1rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            padding: 2rem;
            margin-bottom: 2rem;
        }
        
        .form-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--secondary-color);
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
        }
        
        .form-title i {
            margin-right: 0.75rem;
            font-size: 1.75rem;
            color: var(--primary-color);
        }
        
        .form-label {
            font-weight: 500;
            color: var(--secondary-color);
            margin-bottom: 0.5rem;
        }
        
        .form-control {
            border: 1px solid #ddd;
            border-radius: 0.5rem;
            padding: 0.75rem 1rem;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(63, 114, 175, 0.25);
        }
        
        .form-text {
            color: #6c757d;
            font-size: 0.85rem;
            margin-top: 0.25rem;
        }
        
        .submit-btn {
            background-color: var(--primary-color);
            color: white;
            border: none;
            border-radius: 0.5rem;
            padding: 0.75rem 1.5rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .submit-btn:hover {
            background-color: var(--secondary-color);
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
    </style>
</head>
<body>
    <div class="main-content">
        <!-- Page Header -->
        <div class="page-header">
            <div class="container">
                <h1 class="page-title">Request a Book</h1>
                <p class="page-description">Can't find the book you're looking for? Request it and we'll try to add it to our collection.</p>
            </div>
        </div>
        
        <div class="container">
            <div class="row">
                <!-- Request Form -->
                <div class="col-lg-8">
                    <?php if (!empty($success_message)): ?>
                    <div class="alert alert-success mb-4" role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i> <?= htmlspecialchars($success_message) ?>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($error_message)): ?>
                    <div class="alert alert-danger mb-4" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i> <?= htmlspecialchars($error_message) ?>
                    </div>
                    <?php endif; ?>
                    
                    
                    
                    <div class="form-container">
                        <h2 class="form-title"><i class="bi bi-journal-plus"></i> Book Request Form</h2>
                        <form id="borrowForm">
                        <div class="mb-3">
    <label for="book_id" class="form-label">Book Title*</label>
    <select class="form-control" id="book_id" name="book_id" required>
        <option value="">-- Select Book --</option>
        <?php foreach ($books as $book): ?>
            <option value="<?= $book['bookID'] ?>"
                data-author="<?= htmlspecialchars($book['book_author']) ?>"
                <?= ($book_id > 0 && $book['bookID'] == $book_id) ? 'selected' : '' ?>>
                <?= htmlspecialchars($book['book_title']) ?>
            </option>
        <?php endforeach; ?>
    </select>
</div>

<div class="mb-3">
    <label for="book_author" class="form-label">Author</label>
    <input type="text" class="form-control" id="book_author" name="book_author" readonly>
</div>

    <div class="mb-3">
        <label for="borrow_date" class="form-label">Borrow Date</label>
        <input type="date" class="form-control" id="borrow_date" name="borrow_date" required>
    </div>

    <div class="mb-3">
        <label for="return_date" class="form-label">Estimated Return Date</label>
        <input type="date" class="form-control" id="return_date" name="return_date" required>
    </div>

    <?php if ($book_id > 0): ?>
        <input type="hidden" name="book_id" id="book_id" value="<?= $book_id ?>">
    <?php endif; ?>

    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
        <button type="submit" class="submit-btn"><i class="bi bi-send me-2"></i> Borrow Book</button>
    </div>
</form>
<div id="responseMessage" class="mt-3"></div>

                    </div>
                </div>
                
                <!-- Information Sidebar -->
                <div class="col-lg-4">
                    <div class="info-card mb-4">
                        <h3 class="info-title"><i class="bi bi-info-circle"></i> Request Guidelines</h3>
                        <div class="info-item">
                            <div class="info-icon">
                                <i class="bi bi-clock"></i>
                            </div>
                            <div class="info-text">
                                <h4>Processing Time</h4>
                                <p>Book requests are typically processed within 3-5 business days.</p>
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-icon">
                                <i class="bi bi-check2-circle"></i>
                            </div>
                            <div class="info-text">
                                <h4>Approval Process</h4>
                                <p>Requests are reviewed by our librarians based on availability and demand.</p>
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-icon">
                                <i class="bi bi-envelope"></i>
                            </div>
                            <div class="info-text">
                                <h4>Notifications</h4>
                                <p>You'll receive an email notification when your request is processed.</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="info-card">
                        <h3 class="info-title"><i class="bi bi-search"></i> Before You Request</h3>
                        <p>Please check our catalog thoroughly before submitting a request. The book you're looking for might already be in our collection but:</p>
                        <ul>
                            <li>Currently checked out</li>
                            <li>Listed under a different title</li>
                            <li>Part of a collection or anthology</li>
                        </ul>
                        <div class="d-grid gap-2 mt-4">
                            <a href="booklist.php" class="btn btn-outline-primary"><i class="bi bi-search me-2"></i> Search Catalog</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$('#borrowForm').on('submit', function(e) {
    e.preventDefault();
    
        // Ambil book_title dari dropdown
        var book_title = $('#book_id option:selected').text();
    
    // Menambahkan book_title ke data form yang akan dikirim
    var formData = $(this).serialize() + '&book_title=' + encodeURIComponent(book_title);

    console.log($(this).serialize());
    $.ajax({
        url: 'borrow_handler.php',
        type: 'POST',
        data: formData,
        success: function(response) {
            $('#responseMessage').html('<div class="alert alert-success">' + response + '</div>');
        },
        error: function(xhr, status, error) {
            $('#responseMessage').html('<div class="alert alert-danger">An error occurred: ' + error + '</div>');
        }
    });
});
</script>
<!-- Select2 -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
$(document).ready(function() {
    // Initialize Select2
    $('#book_id').select2({
        placeholder: "-- Select Book --",
        allowClear: true
    });

    // Auto-fill author when book selected
    $('#book_id').on('change', function() {
        var author = $('#book_id option:selected').data('author');
        $('#book_author').val(author || '');
    });
    
    // Trigger the change event to populate the author field if a book is pre-selected
    if ($('#book_id').val()) {
        $('#book_id').trigger('change');
    }
    
    // Set default dates
    var today = new Date();
    var returnDate = new Date();
    returnDate.setDate(today.getDate() + 3); // 3 days from now
    
    // Format dates as YYYY-MM-DD
    var formatDate = function(date) {
        var d = date,
            month = '' + (d.getMonth() + 1),
            day = '' + d.getDate(),
            year = d.getFullYear();

        if (month.length < 2) month = '0' + month;
        if (day.length < 2) day = '0' + day;

        return [year, month, day].join('-');
    };
    
    $('#borrow_date').val(formatDate(today));
    $('#return_date').val(formatDate(returnDate));
});
</script>


</body>
</html>