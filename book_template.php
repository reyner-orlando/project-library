<?php session_start(); // Pastikan session dimulai
include 'header.php'; ?>
<?php include 'db.php'; ?>
<?php include 'nav2.php'; ?>
<?php

$is_admin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
// Get book ID from URL parameter
$book_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Initialize book data
$book = null;
$book_categories = [];
$available_copies = 0;
$total_copies = 0;
$related_books = [];
$book_rating = 0;
$rating_count = 0;
$reviews_by_rating = [1 => [], 2 => [], 3 => [], 4 => [], 5 => []];

// Fetch book data from database
if ($book_id > 0) {
    try {
        // Use PDO to fetch book data
        $query = "SELECT * FROM book WHERE book_id = ?";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$book_id]);
        
        if ($stmt->rowCount() > 0) {
            $book = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Get book categories
            $cat_query = "SELECT c.* FROM category c 
                          JOIN assigncat ac ON c.cat_id = ac.cat_id 
                          WHERE ac.book_id = ?";
            $cat_stmt = $pdo->prepare($cat_query);
            $cat_stmt->execute([$book_id]);
            $book_categories = $cat_stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Get book rating
            $rating_query = "SELECT AVG(br_rating) as avg_rating, COUNT(*) as review_count 
                            FROM bookreview 
                            WHERE book_id = ?";
            $rating_stmt = $pdo->prepare($rating_query);
            $rating_stmt->execute([$book_id]);
            $rating_result = $rating_stmt->fetch(PDO::FETCH_ASSOC);
            $book_rating = $rating_result['avg_rating'] ? round($rating_result['avg_rating'], 1) : 0;
            $rating_count = $rating_result['review_count'] ?? 0;
            
            // Get reviews grouped by rating (1-5)
            $reviews_query = "SELECT br.*, u.user_fullname 
                              FROM bookreview br 
                              JOIN user u ON br.user_id = u.user_id 
                              WHERE br.book_id = ? 
                              ORDER BY br.br_date DESC";
            $reviews_stmt = $pdo->prepare($reviews_query);
            $reviews_stmt->execute([$book_id]);
            $all_reviews = $reviews_stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Group reviews by rating
            foreach ($all_reviews as $review) {
                $rating = intval($review['br_rating']);
                if ($rating >= 1 && $rating <= 5) {
                    $reviews_by_rating[$rating][] = $review;
                }
            }
            
            // Check book availability (count copies)
            $copy_query = "SELECT COUNT(*) as total, 
                           SUM(CASE WHEN copy_status = 'available' THEN 1 ELSE 0 END) as available 
                           FROM bookcopy WHERE book_id = ?";
            $copy_stmt = $pdo->prepare($copy_query);
            $copy_stmt->execute([$book_id]);
            $copy_result = $copy_stmt->fetch(PDO::FETCH_ASSOC);
            
            $total_copies = $copy_result['total'] ?? 0;
            $available_copies = $copy_result['available'] ?? 0;
            
            // Get related books (same author or same categories)
            $category_ids = [];
            foreach ($book_categories as $cat) {
                $category_ids[] = $cat['cat_id'];
            }
            
            // If we have categories, find books in the same categories
            if (!empty($category_ids)) {
                $placeholders = implode(',', array_fill(0, count($category_ids), '?'));
                $related_query = "SELECT DISTINCT b.* FROM book b 
                                 JOIN assigncat ac ON b.book_id = ac.book_id 
                                 WHERE ac.cat_id IN ($placeholders) 
                                 AND b.book_id != ? 
                                 ORDER BY RAND() LIMIT 4";
                
                $params = $category_ids;
                $params[] = $book_id;
                
                $related_stmt = $pdo->prepare($related_query);
                $related_stmt->execute($params);
                $related_books = $related_stmt->fetchAll(PDO::FETCH_ASSOC);
            }
            
            // If we don't have enough related books by category, add some by the same author
            if (count($related_books) < 4) {
                $author_query = "SELECT * FROM book 
                                WHERE book_author = ? 
                                AND book_id != ? 
                                ORDER BY RAND() 
                                LIMIT ?";
                $author_stmt = $pdo->prepare($author_query);
                $author_stmt->execute([
                    $book['book_author'],
                    $book_id,
                    4 - count($related_books)
                ]);
                $author_related = $author_stmt->fetchAll(PDO::FETCH_ASSOC);
                $related_books = array_merge($related_books, $author_related);
            }
            
            // If we still don't have enough related books, get random books
            if (count($related_books) < 4) {
                $random_query = "SELECT * FROM book 
                               WHERE book_id != ? 
                               ORDER BY RAND() 
                               LIMIT ?";
                $random_stmt = $pdo->prepare($random_query);
                $random_stmt->execute([
                    $book_id,
                    4 - count($related_books)
                ]);
                $random_related = $random_stmt->fetchAll(PDO::FETCH_ASSOC);
                $related_books = array_merge($related_books, $random_related);
            }
        }
    } catch (PDOException $e) {
        // Handle database error
        echo "Error: " . $e->getMessage();
    }
}

// If book not found in database, use sample data for demonstration
if (!$book && $book_id > 0) {
    // Sample books data based on your actual database structure and ID
    $sample_books = [
        1 => [
            'book_id' => 1,
            'book_title' => 'Bumi',
            'book_author' => 'Tere Liye',
            'book_desc' => 'Sebuah buku yang menceritakan tentang Raib, seorang gadis remaja yang memiliki kekuatan supernatural.',
            'book_year' => '2016',
            'book_timeadded' => '2025-04-23 22:42:41'
        ],
        2 => [
            'book_id' => 2,
            'book_title' => 'Bulan',
            'book_author' => 'Tere Liye',
            'book_desc' => 'Edisi ke-2 dari serial Bumi, melanjutkan petualangan Raib dan teman-temannya.',
            'book_year' => '2015',
            'book_timeadded' => '2025-04-23 23:07:32'
        ],
        3 => [
            'book_id' => 3,
            'book_title' => 'Matahari',
            'book_author' => 'Tere Liye',
            'book_desc' => 'Edisi ke-3 dari serial Bumi, mengungkap lebih banyak rahasia dunia paralel.',
            'book_year' => '2017',
            'book_timeadded' => '2025-04-24 01:45:53'
        ],
        4 => [
            'book_id' => 4,
            'book_title' => 'Komet Minor',
            'book_author' => 'Tere Liye',
            'book_desc' => 'Edisi ke-4 dari serial Bumi, petualangan Raib semakin menegangkan.',
            'book_year' => '2020',
            'book_timeadded' => '2025-04-24 02:44:47'
        ],
        6 => [
            'book_id' => 6,
            'book_title' => 'Harry Potter',
            'book_author' => 'J. K. Rowling',
            'book_desc' => 'ALAKAZAM! Kisah seorang penyihir muda yang belajar di sekolah sihir Hogwarts.',
            'book_year' => '2025',
            'book_timeadded' => '2025-04-24 10:38:41'
        ],
        7 => [
            'book_id' => 7,
            'book_title' => 'SI JUKI',
            'book_author' => 'Faza Meonk',
            'book_desc' => 'Sebuah komik keren tentang petualangan si Juki.',
            'book_year' => '2011',
            'book_timeadded' => '2025-04-29 06:57:53'
        ]
    ];
    
    // Use the sample book data that matches the ID, or create a generic one if not found
    if (isset($sample_books[$book_id])) {
        $book = $sample_books[$book_id];
    } else {
        // Generic fallback if ID doesn't match any sample
        $book = [
            'book_id' => $book_id,
            'book_title' => 'Book #' . $book_id,
            'book_author' => 'Unknown Author',
            'book_desc' => 'This is a placeholder for book ID ' . $book_id . ' which is not in our sample data.',
            'book_year' => date('Y'),
            'book_timeadded' => date('Y-m-d H:i:s')
        ];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LibRA - <?= $book ? htmlspecialchars($book['book_title']) : 'Book Details' ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        .book-detail-section {
            padding: 3rem 0;
        }
        
        .book-cover-container {
            position: relative;
            margin-bottom: 1.5rem;
        }
        
        .book-cover {
            width: 100%;
            border-radius: 0.5rem;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }
        
        .book-category-badge {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background-color: var(--accent-color);
            color: var(--secondary-color);
            padding: 0.5rem 1rem;
            border-radius: 2rem;
            font-size: 0.8rem;
            font-weight: 600;
        }
        
        .book-title {
            font-size: 2rem;
            font-weight: 700;
            color: var(--secondary-color);
            margin-bottom: 0.5rem;
        }
        
        .book-author {
            font-size: 1.2rem;
            color: #666;
            margin-bottom: 1.5rem;
        }
        
        .book-description {
            font-size: 1rem;
            line-height: 1.7;
            color: #444;
            margin-bottom: 2rem;
        }
        
        .book-meta {
            background-color: white;
            border-radius: 0.5rem;
            padding: 1.5rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            margin-bottom: 2rem;
        }
        
        .meta-item {
            display: flex;
            margin-bottom: 1rem;
            align-items: center;
        }
        
        .meta-item:last-child {
            margin-bottom: 0;
        }
        
        .meta-icon {
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
        }
        
        .meta-content {
            flex: 1;
        }
        
        .meta-title {
            font-size: 0.8rem;
            color: #666;
            margin-bottom: 0.25rem;
        }
        
        .meta-text {
            font-size: 1rem;
            font-weight: 600;
            color: var(--secondary-color);
        }
        
        .action-buttons {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
        }
        
        .borrow-btn {
            background-color: var(--primary-color);
            color: white;
            border: none;
            border-radius: 0.5rem;
            padding: 0.75rem 1.5rem;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
        }
        
        .borrow-btn:hover {
            background-color: var(--secondary-color);
            color: white;
            text-decoration: none;
        }
        
        .borrow-btn i {
            margin-right: 0.5rem;
        }
        
        .wishlist-btn {
            background-color: white;
            color: var(--secondary-color);
            border: 1px solid var(--secondary-color);
            border-radius: 0.5rem;
            padding: 0.75rem 1.5rem;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .wishlist-btn:hover {
            background-color: var(--accent-color);
        }
        
        .wishlist-btn i {
            margin-right: 0.5rem;
        }
        
        .similar-books-section {
            padding: 3rem 0;
            background-color: #f8f9fa;
        }
        
        .section-title {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
            color: var(--secondary-color);
        }
        
        .book-card {
            background-color: white;
            border-radius: 0.5rem;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            height: 100%;
            transition: all 0.3s ease;
        }
        
        .book-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
        }
        
        .book-img-container {
            position: relative;
            height: 200px;
            overflow: hidden;
        }
        
        .book-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .book-info {
            padding: 1.5rem;
        }
        
        .card-book-title {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: var(--secondary-color);
        }
        
        .card-book-author {
            font-size: 0.9rem;
            color: #666;
            margin-bottom: 1rem;
        }
        
        .view-btn {
            background-color: var(--accent-color);
            color: var(--secondary-color);
            border: none;
            border-radius: 50px;
            padding: 0.4rem 1rem;
            font-size: 0.85rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }
        
        .view-btn:hover {
            background-color: var(--primary-color);
            color: white;
            text-decoration: none;
        }
        
        .book-rating {
            margin-bottom: 1.5rem;
        }
        
        .rating-stars {
            font-size: 1.25rem;
        }
        
        .rating-value {
            font-size: 1.25rem;
            font-weight: 600;
            margin-left: 0.5rem;
        }
        
        /* Review section styles */
        .reviews-section {
            background-color: #f8f9fa;
        }
        
        .review-card {
            border: none;
            border-radius: 0.5rem;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.05);
            transition: transform 0.2s;
        }
        
        .review-card:hover {
            transform: translateY(-5px);
        }
        
        .review-stars {
            color: #f8ce0b;
        }
        
        .review-text {
            font-style: italic;
            color: #495057;
        }
        
        .nav-tabs .nav-link {
            border-radius: 0.5rem 0.5rem 0 0;
            font-weight: 500;
        }
        
        .nav-tabs .nav-link.active {
            background-color: #fff;
            border-color: #dee2e6 #dee2e6 #fff;
            color: #495057;
        }
    </style>
</head>
<body>
    <div class="main-content">
        <?php if ($book): ?>
        <!-- Book Detail Section -->
        <div class="book-detail-section">
            <div class="container">
                <div class="row">
                    <!-- Book Cover and Details -->
                    <div class="col-lg-4">
                        <div class="book-cover-container">
                            <img src="https://covers.openlibrary.org/b/isbn/<?= urlencode($book['book_isbn']) ?>-L.jpg" class="book-cover" alt="<?= htmlspecialchars($book['book_title']) ?>">
                            <div class="book-category-badge"><?= htmlspecialchars($book['book_year']) ?></div>
                        </div>
                        <!-- If user is admin, show edit button -->
                        <?php if ($is_admin): ?>
                            <a href="edit_book.php?id=<?= $book['book_id'] ?>" class="btn btn-warning">Edit Book</a>
                        <?php endif; ?>
                        
                        <div class="book-meta">
                            <div class="meta-item">
                                <div class="meta-icon">
                                    <i class="bi bi-hash"></i>
                                </div>
                                <div class="meta-content">
                                    <h4 class="meta-title">Book ID</h4>
                                    <p class="meta-text"><?= htmlspecialchars($book['book_id']) ?></p>
                                </div>
                            </div>
                            <div class="meta-item">
                                <div class="meta-icon">
                                    <i class="bi bi-calendar-event"></i>
                                </div>
                                <div class="meta-content">
                                    <h4 class="meta-title">Publication Year</h4>
                                    <p class="meta-text"><?= htmlspecialchars($book['book_year']) ?></p>
                                </div>
                            </div>
                            <div class="meta-item">
                                <div class="meta-icon">
                                    <i class="bi bi-clock-history"></i>
                                </div>
                                <div class="meta-content">
                                    <h4 class="meta-title">Added to Library</h4>
                                    <p class="meta-text"><?= date('F j, Y', strtotime($book['book_timeadded'])) ?></p>
                                </div>
                            </div>
                            <div class="meta-item">
                                <div class="meta-icon">
                                    <i class="bi bi-bookmark"></i>
                                </div>
                                <div class="meta-content">
                                    <h4 class="meta-title">Categories</h4>
                                    <p class="meta-text">
                                        <?php if (!empty($book_categories)): ?>
                                            <?php foreach ($book_categories as $index => $category): ?>
                                                <?= htmlspecialchars($category['cat_name']) ?><?= ($index < count($book_categories) - 1) ? ', ' : '' ?>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            Uncategorized
                                        <?php endif; ?>
                                    </p>
                                </div>
                            </div>
                            <div class="meta-item">
                                <div class="meta-icon">
                                    <i class="bi bi-book"></i>
                                </div>
                                <div class="meta-content">
                                    <h4 class="meta-title">Availability</h4>
                                    <p class="meta-text">
                                        <?php if ($total_copies > 0): ?>
                                            <span class="badge <?= ($available_copies > 0) ? 'bg-success' : 'bg-danger' ?>" style="padding: 5px 10px; color: white;">
                                                <?= $available_copies ?> of <?= $total_copies ?> copies available
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-warning" style="padding: 5px 10px; color: white;">No physical copies</span>
                                        <?php endif; ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Book Information -->
                    <div class="col-lg-8">
                        <h1 class="book-title"><?= htmlspecialchars($book['book_title']) ?></h1>
                        <p class="book-author">by <?= htmlspecialchars($book['book_author']) ?></p>
                        
                        
                        
                        <div class="action-buttons">
                            <?php if ($available_copies > 0): ?>
                            <a href="book_request.php?book_id=<?= $book['book_id'] ?>&book_title=<?= urlencode($book['book_title']) ?>&book_author=<?= urlencode($book['book_author']) ?>" class="borrow-btn">
                                <i class="bi bi-journal-arrow-down"></i> Borrow This Book
                            </a>
                            <?php else: ?>
                            <button class="borrow-btn" disabled style="opacity: 0.6; cursor: not-allowed;">
                                <i class="bi bi-journal-arrow-down"></i> Currently Unavailable
                            </button>
                            <?php endif; ?>
                        </div>
                        
                        <h3 class="section-title">About This Book</h3>
                        <p class="book-description"><?= nl2br(htmlspecialchars($book['book_desc'])) ?></p>
                        
                        <!-- Book Availability Status -->
                        <div class="book-availability mt-4 p-3 bg-light rounded">
                            <h4><i class="bi bi-info-circle me-2"></i> Availability</h4>
                            <p class="mb-0">This book is currently <span class="badge <?= ($available_copies > 0) ? 'bg-success' : 'bg-danger' ?>"><?= ($available_copies > 0) ? 'Available' : 'Not Available' ?></span> for borrowing.</p>
                            <p class="small text-muted mt-2">Borrow period: 3 days, with option to extend if no waiting list.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Reviews by Rating Section -->
        <?php if ($book && $rating_count > 0): ?>
        <div class="reviews-section py-5 bg-light">
            <div class="container">
                <h2 class="section-title mb-4">Reviews by Rating</h2>
                
                <ul class="nav nav-tabs mb-4" id="reviewTabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" 
                           id="rating-all-tab" 
                           data-toggle="tab" 
                           href="#rating-all" 
                           role="tab">
                            All Reviews 
                            <span class="badge badge-pill badge-secondary"><?= $rating_count ?></span>
                        </a>
                    </li>
                    <?php for ($i = 5; $i >= 1; $i--): ?>
                        <li class="nav-item">
                            <a class="nav-link" 
                               id="rating-<?= $i ?>-tab" 
                               data-toggle="tab" 
                               href="#rating-<?= $i ?>" 
                               role="tab">
                                <?= $i ?> Star<?= ($i > 1) ? 's' : '' ?> 
                                <span class="badge badge-pill badge-secondary"><?= count($reviews_by_rating[$i]) ?></span>
                            </a>
                        </li>
                    <?php endfor; ?>
                </ul>
                
                <div class="tab-content" id="reviewTabsContent">
                    <!-- All Reviews Tab -->
                    <div class="tab-pane fade show active" 
                         id="rating-all" 
                         role="tabpanel">
                        
                        <?php if ($rating_count == 0): ?>
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle me-2"></i> No reviews yet.
                            </div>
                        <?php else: ?>
                            <div class="row">
                                <?php 
                                // Combine all reviews from all ratings
                                $all_reviews = [];
                                for ($r = 5; $r >= 1; $r--) {
                                    $all_reviews = array_merge($all_reviews, $reviews_by_rating[$r]);
                                }
                                
                                // Sort by date (newest first)
                                usort($all_reviews, function($a, $b) {
                                    return strtotime($b['br_date']) - strtotime($a['br_date']);
                                });
                                
                                foreach ($all_reviews as $review): 
                                ?>
                                    <div class="col-md-6 mb-4">
                                        <div class="card review-card">
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between align-items-center mb-3">
                                                    <h5 class="card-title mb-0"><?= htmlspecialchars($review['user_fullname']) ?></h5>
                                                    <div class="review-date text-muted small">
                                                        <?= date('M d, Y', strtotime($review['br_date'])) ?>
                                                    </div>
                                                </div>
                                                <div class="review-stars mb-2">
                                                    <?php for ($star = 1; $star <= 5; $star++): ?>
                                                        <i class="bi bi-star<?= ($star <= $review['br_rating']) ? '-fill' : '' ?>"></i>
                                                    <?php endfor; ?>
                                                </div>
                                                <p class="card-text review-text">
                                                    "<?= nl2br(htmlspecialchars($review['br_review'])) ?>"
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <?php for ($i = 5; $i >= 1; $i--): ?>
                        <div class="tab-pane fade" 
                             id="rating-<?= $i ?>" 
                             role="tabpanel">
                            
                            <?php if (empty($reviews_by_rating[$i])): ?>
                                <div class="alert alert-info">
                                    <i class="bi bi-info-circle me-2"></i> No <?= $i ?>-star reviews yet.
                                </div>
                            <?php else: ?>
                                <div class="row">
                                    <?php foreach ($reviews_by_rating[$i] as $review): ?>
                                        <div class="col-md-6 mb-4">
                                            <div class="card review-card">
                                                <div class="card-body">
                                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                                        <h5 class="card-title mb-0"><?= htmlspecialchars($review['user_fullname']) ?></h5>
                                                        <div class="review-date text-muted small">
                                                            <?= date('M d, Y', strtotime($review['br_date'])) ?>
                                                        </div>
                                                    </div>
                                                    <div class="review-stars mb-2">
                                                        <?php for ($star = 1; $star <= 5; $star++): ?>
                                                            <i class="bi bi-star<?= ($star <= $review['br_rating']) ? '-fill' : '' ?>"></i>
                                                        <?php endfor; ?>
                                                    </div>
                                                    <p class="card-text review-text">
                                                        "<?= nl2br(htmlspecialchars($review['br_review'])) ?>"
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endfor; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Similar Books Section -->
        <?php if (!empty($related_books)): ?>
        <div class="similar-books-section">
            <div class="container">
                <h2 class="section-title">You May Also Like</h2>
                <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4">
                    <?php foreach ($related_books as $related_book): ?>
                    <div class="col">
                        <div class="book-card">
                            <div class="book-img-container">
                                <?php 
                                $cover_url = "https://covers.openlibrary.org/b/isbn/". htmlspecialchars($related_book['book_isbn']) . "-L.jpg";
                                ?>
                                <img src="<?= $cover_url ?>" class="book-img" alt="<?= htmlspecialchars($related_book['book_title']) ?>">
                            </div>
                            <div class="book-info">
                                <h3 class="card-book-title"><?= htmlspecialchars($related_book['book_title']) ?></h3>
                                <p class="card-book-author"><?= htmlspecialchars($related_book['book_author']) ?></p>
                                <a href="book_template.php?id=<?= $related_book['book_id'] ?>" class="view-btn">View Book</a>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
        <?php else: ?>
        <!-- Book Not Found -->
        <div class="container py-5">
            <div class="text-center">
                <i class="bi bi-exclamation-triangle" style="font-size: 5rem; color: #dc3545;"></i>
                <h2 class="mt-4">Book Not Found</h2>
                <p class="lead">Sorry, the book you're looking for doesn't exist or has been removed.</p>
                <a href="booklist.php" class="btn btn-primary mt-3">Browse Our Collection</a>
            </div>
        </div>
        <?php endif; ?>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
</body>
</html>