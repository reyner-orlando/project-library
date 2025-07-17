<?php
// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Include database connection and header files
include 'header.php';
include 'db.php';
include 'nav2.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Get user ID and book information from URL
$userId = $_SESSION['user_id'];
$bookId = isset($_GET['book_id']) ? intval($_GET['book_id']) : 0;
$bookTitle = isset($_GET['title']) ? $_GET['title'] : 'Unknown Book';

// Check if book exists and if user has already reviewed it
$bookExists = false;
$hasReviewed = false;
$existingReview = null;

try {
    // Check if book exists and get average rating
    $bookStmt = $pdo->prepare("SELECT b.*, IFNULL(AVG(br.br_rating), 0) as avg_rating 
                            FROM book b 
                            LEFT JOIN bookreview br ON b.book_id = br.book_id 
                            WHERE b.book_id = ? 
                            GROUP BY b.book_id");
    $bookStmt->execute([$bookId]);
    $book = $bookStmt->fetch(PDO::FETCH_ASSOC);
    $bookExists = ($book !== false);
    
    // Check if user has already reviewed this book
    $reviewStmt = $pdo->prepare("SELECT * FROM bookreview WHERE book_id = ? AND user_id = ?");
    $reviewStmt->execute([$bookId, $userId]);
    $existingReview = $reviewStmt->fetch(PDO::FETCH_ASSOC);
    $hasReviewed = ($existingReview !== false);
    
} catch (PDOException $e) {
    $error = "Database error: " . $e->getMessage();
}

// Handle form submission
$success = false;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rating = isset($_POST['rating']) ? intval($_POST['rating']) : 5;
    $review = isset($_POST['review']) ? trim($_POST['review']) : '';
    
    // Validate input
    if (empty($review)) {
        $error = "Please enter your review.";
    } elseif ($rating < 1 || $rating > 5) {
        $error = "Please select a valid rating (1-5).";
    } else {
        try {
            // Begin transaction
            $pdo->beginTransaction();
            
            if ($hasReviewed) {
                // Update existing review
                $updateStmt = $pdo->prepare("
                    UPDATE bookreview 
                    SET br_review = ?, br_rating = ?, br_date = NOW() 
                    WHERE book_id = ? AND user_id = ?
                ");
                $updateStmt->execute([$review, $rating, $bookId, $userId]);
            } else {
                // Insert new review
                $insertStmt = $pdo->prepare("
                    INSERT INTO bookreview (book_id, user_id, br_review, br_rating, br_date) 
                    VALUES (?, ?, ?, ?, NOW())
                ");
                $insertStmt->execute([$bookId, $userId, $review, $rating]);
            }
            
            // Update average rating in book table
            $avgRatingStmt = $pdo->prepare("
                SELECT AVG(br_rating) as avg_rating 
                FROM bookreview 
                WHERE book_id = ?
            ");
            $avgRatingStmt->execute([$bookId]);
            $avgRating = $avgRatingStmt->fetch(PDO::FETCH_ASSOC)['avg_rating'];
            
            // No need to update the book table with the average rating
            // The average is calculated on-the-fly in the query
            
            // Commit transaction
            $pdo->commit();
            
            $success = true;
            
            // Refresh the page to show the updated review
            if ($success) {
                // Use a relative URL to avoid issues with base URL
                echo "<script>window.location.href = 'book_review.php?book_id=$bookId&title=" . urlencode($bookTitle) . "&success=1';</script>";
                exit;
            }
            
        } catch (PDOException $e) {
            // Rollback transaction on error
            $pdo->rollBack();
            $error = "Database error: " . $e->getMessage();
        }
    }
}

// Check for success message in URL
$showSuccessMessage = isset($_GET['success']) && $_GET['success'] == 1;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Review Book - LibRA</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <style>
        .review-container {
            max-width: 800px;
            margin: 0 auto;
        }
        
        .book-info {
            display: flex;
            align-items: center;
            margin-bottom: 2rem;
            padding: 1.5rem;
            background-color: #f8f9fa;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .book-cover {
            width: 120px;
            height: 180px;
            object-fit: cover;
            margin-right: 1.5rem;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        
        .book-details h1 {
            font-size: 1.8rem;
            margin-bottom: 0.5rem;
            color: #2c3e50;
        }
        
        .book-details p {
            color: #7f8c8d;
            margin-bottom: 0.5rem;
        }
        
        .star-rating {
            display: inline-flex;
            flex-direction: row-reverse;
            font-size: 2em;
            justify-content: center;
            padding: 0 0.2em;
            text-align: center;
            width: 5em;
        }
        
        .star-rating input {
            display: none;
        }
        
        .star-rating label {
            color: #ccc;
            cursor: pointer;
            padding: 0 0.1em;
            transition: color 0.3s ease;
        }
        
        .star-rating label:hover,
        .star-rating label:hover ~ label,
        .star-rating input:checked ~ label {
            color: #f8ce0b;
        }
        
        .rating-value {
            font-size: 1.2em;
            color: #f39c12;
            font-weight: bold;
        }
        
        .review-form {
            background-color: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.1);
        }
        
        .form-group label {
            font-weight: 600;
            color: #34495e;
        }
        
        .btn-submit {
            background-color: #3498db;
            border: none;
            padding: 0.6rem 2rem;
            font-weight: 600;
            border-radius: 30px;
            transition: all 0.3s ease;
        }
        
        .btn-submit:hover {
            background-color: #2980b9;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        
        .existing-review {
            background-color: #e8f4f8;
            padding: 1.5rem;
            border-radius: 10px;
            margin-bottom: 2rem;
            border-left: 4px solid #3498db;
        }
        
        .review-date {
            font-size: 0.85rem;
            color: #7f8c8d;
        }
        
        .review-stars {
            color: #f39c12;
            font-size: 1.2rem;
            margin-bottom: 0.5rem;
        }
        
        .review-text {
            font-style: italic;
            color: #34495e;
        }
        
        .alert {
            border-radius: 10px;
        }
    </style>
</head>
<body>
    <div class="main-content py-5">
        <div class="container review-container">
            <?php if (!$bookExists): ?>
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle-fill"></i> Book not found. Please go back and try again.
                </div>
                <a href="dashboard_user.php?tab=history" class="btn btn-primary">
                    <i class="bi bi-arrow-left"></i> Back to Dashboard
                </a>
            <?php else: ?>
                <!-- Success Message -->
                <?php if ($showSuccessMessage): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle-fill"></i> Your review has been submitted successfully!
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                <?php endif; ?>
                
                <!-- Error Message -->
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-triangle-fill"></i> <?php echo $error; ?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                <?php endif; ?>
                
                <!-- Book Information -->
                <div class="book-info">
                    <img src="https://covers.openlibrary.org/b/isbn/<?php echo htmlspecialchars($book['book_isbn']); ?>-L.jpg" 
                         alt="Book Cover" class="book-cover">
                    <div class="book-details">
                        <h1><?php echo htmlspecialchars($book['book_title']); ?></h1>
                        <p><strong>Author:</strong> <?php echo htmlspecialchars($book['book_author']); ?></p>
                        <p><strong>Published:</strong> <?php echo htmlspecialchars($book['book_year']); ?></p>
                        <?php if (isset($book['avg_rating']) && $book['avg_rating'] > 0): ?>
                            <p>
                                <strong>Current Rating:</strong> 
                                <span class="text-warning">
                                    <?php 
                                    $rating = round($book['avg_rating'], 1);
                                    for ($i = 1; $i <= 5; $i++) {
                                        if ($i <= $rating) {
                                            echo '<i class="bi bi-star-fill"></i> ';
                                        } elseif ($i - 0.5 <= $rating) {
                                            echo '<i class="bi bi-star-half"></i> ';
                                        } else {
                                            echo '<i class="bi bi-star"></i> ';
                                        }
                                    }
                                    echo " ($rating)";
                                    ?>
                                </span>
                            </p>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Existing Review (if any) -->
                <?php if ($hasReviewed): ?>
                    <div class="existing-review">
                        <h4>Your Previous Review</h4>
                        <div class="review-date">
                            <i class="bi bi-calendar"></i> 
                            Reviewed on <?php echo date('F j, Y', strtotime($existingReview['br_date'])); ?>
                        </div>
                        <div class="review-stars">
                            <?php 
                            for ($i = 1; $i <= 5; $i++) {
                                if ($i <= $existingReview['br_rating']) {
                                    echo '<i class="bi bi-star-fill"></i>';
                                } else {
                                    echo '<i class="bi bi-star"></i>';
                                }
                            }
                            ?>
                        </div>
                        <div class="review-text">
                            "<?php echo htmlspecialchars($existingReview['br_review']); ?>"
                        </div>
                    </div>
                <?php endif; ?>
                
                <!-- Review Form -->
                <div class="review-form">
                    <h3 class="mb-4"><?php echo $hasReviewed ? 'Update Your Review' : 'Write a Review'; ?></h3>
                    
                    <form action="" method="post">
                        <div class="form-group mb-4">
                            <label for="rating">Your Rating</label>
                            <div class="text-center my-3">
                                <div class="star-rating">
                                    <?php for($i = 5; $i >= 1; $i--): ?>
                                        <input type="radio" id="star<?php echo $i; ?>" name="rating" value="<?php echo $i; ?>" 
                                            <?php echo ($hasReviewed && $existingReview['br_rating'] == $i) || (!$hasReviewed && $i == 5) ? 'checked' : ''; ?>>
                                        <label for="star<?php echo $i; ?>"><i class="bi bi-star-fill"></i></label>
                                    <?php endfor; ?>
                                </div>
                                <div class="rating-value mt-2">
                                    <?php echo $hasReviewed ? $existingReview['br_rating'] : '5'; ?>.0
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group mb-4">
                            <label for="review">Your Review</label>
                            <textarea class="form-control" id="review" name="review" rows="6" required 
                                placeholder="Share your thoughts about this book..."><?php echo $hasReviewed ? htmlspecialchars($existingReview['br_review']) : ''; ?></textarea>
                            <small class="form-text text-muted">
                                Your honest review helps other readers make informed decisions.
                            </small>
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center">
                            <a href="dashboard_user.php?tab=history" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left"></i> Back to Dashboard
                            </a>
                            <button type="submit" class="btn btn-primary btn-submit">
                                <i class="bi bi-send-fill"></i> <?php echo $hasReviewed ? 'Update Review' : 'Submit Review'; ?>
                            </button>
                        </div>
                    </form>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            // Update rating display when stars are clicked
            $('input[name="rating"]').on('change', function() {
                var ratingValue = $(this).val();
                $('.rating-value').text(ratingValue + '.0');
            });
            
            // Auto-dismiss alerts after 5 seconds
            setTimeout(function() {
                $('.alert').alert('close');
            }, 5000);
        });
    </script>
</body>
</html>

<?php include 'footer.php'; ?>
