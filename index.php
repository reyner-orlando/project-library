<?php include 'header.php'; ?>
<?php include 'db.php'; ?>
<?php include 'nav2.php'; ?>
<?php
// Get current month's borrowing statistics
$current_month = date('Y-m');
$current_month_start = $current_month . '-01';
$current_month_end = date('Y-m-t'); // Last day of current month

// Get borrowing count for current month
$borrowing_query = "SELECT COUNT(*) as borrow_count FROM borrowing 
                   WHERE brw_date BETWEEN ? AND ? 
                   AND brw_status != 'rejected'";
$borrowing_stmt = $pdo->prepare($borrowing_query);
$borrowing_stmt->execute([$current_month_start, $current_month_end]);
$borrowing_result = $borrowing_stmt->fetch(PDO::FETCH_ASSOC);
$borrowing_count = $borrowing_result['borrow_count'] ?? 0;

// Get user count
$user_query = "SELECT COUNT(*) as user_count FROM user
                WHERE status_id = 1";
$user_stmt = $pdo->prepare($user_query);
$user_stmt->execute();
$user_result = $user_stmt->fetch(PDO::FETCH_ASSOC);
$user_count = $user_result['user_count'] ?? 0;

// Get average rating
$rating_query = "SELECT AVG(br_rating) as avg_rating FROM bookreview";
$rating_stmt = $pdo->prepare($rating_query);
$rating_stmt->execute();
$rating_result = $rating_stmt->fetch(PDO::FETCH_ASSOC);
$avg_rating = $rating_result['avg_rating'] ? round($rating_result['avg_rating'], 1) : 0;

// Get most popular book this month (most borrowed)
$popular_query = "SELECT b.book_id, b.book_title, b.book_author, COUNT(*) as borrow_count 
                FROM borrowing br 
                JOIN book b ON br.book_id = b.book_id 
                WHERE br.brw_date BETWEEN ? AND ? 
                AND br.brw_status != 'rejected' 
                GROUP BY b.book_id 
                ORDER BY borrow_count DESC 
                LIMIT 1";
$popular_stmt = $pdo->prepare($popular_query);
$popular_stmt->execute([$current_month_start, $current_month_end]);
$popular_book = $popular_stmt->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LibRA - Online Library</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <style>
        :root {
            --primary-color-rgb: 63, 114, 175; /* This should match your primary color */
        }
        
        .hero-section {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 3rem 0;
            border-radius: 1rem;
            margin-bottom: 2rem;
            position: relative;
            overflow: hidden;
        }
        
        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 300px;
            height: 100%;
            background-image: url('img/book-pattern.png');
            background-size: cover;
            opacity: 0.1;
        }
        
        .hero-title {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }
        
        .hero-subtitle {
            font-size: 1.2rem;
            font-weight: 300;
            margin-bottom: 1.5rem;
            max-width: 600px;
        }
        
        
        
        .section-title {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
            color: var(--secondary-color);
            display: flex;
            align-items: center;
        }
        
        .section-title::after {
            content: '';
            flex: 1;
            height: 1px;
            background-color: #ddd;
            margin-left: 1rem;
        }
        
        .book-card {
            border-radius: 1rem;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
            height: 100%;
            background-color: white;
        }
        
        .book-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }
        
        .book-img-container {
            height: 200px;
            overflow: hidden;
            position: relative;
        }
        
        .book-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: all 0.5s ease;
        }
        
        .book-card:hover .book-img {
            transform: scale(1.05);
        }
        
        .book-category {
            position: absolute;
            top: 10px;
            right: 10px;
            background-color: var(--primary-color);
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 500;
        }
        
        .book-info {
            padding: 1.25rem;
        }
        
        .book-title {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: var(--secondary-color);
        }
        
        .book-author {
            font-size: 0.9rem;
            color: #666;
            margin-bottom: 0.75rem;
        }
        
        .book-description {
            font-size: 0.85rem;
            color: #777;
            margin-bottom: 1rem;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        .book-action {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .book-rating {
            display: flex;
            align-items: center;
            color: #f39c12;
            font-size: 0.9rem;
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
        }
        
        .view-btn:hover {
            background-color: var(--primary-color);
            color: white;
        }
        
        .swiper {
            padding: 1rem 0.5rem 2rem;
        }
        
        .stats-container {
            background-color: white;
            border-radius: 1rem;
            padding: 1.5rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            margin-bottom: 2rem;
        }
        
        .stat-item {
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
        }
        
        .stat-icon {
            width: 45px;
            height: 45px;
            background-color: var(--accent-color);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
            color: var(--primary-color);
            font-size: 1.25rem;
        }
        
        .stat-info h4 {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 0.25rem;
            color: var(--secondary-color);
        }
        
        .stat-info p {
            font-size: 0.9rem;
            color: #666;
            margin: 0;
        }
        
        .stats-container {
            background-color: white;
            border-radius: 1rem;
            padding: 2rem;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
            margin-bottom: 2rem;
        }
        
        .stats-row {
            margin-top: 1rem;
        }
        
        .stat-card {
            background-color: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
        }
        
        .stat-card-body {
            padding: 1.5rem;
            display: flex;
            align-items: center;
            flex-grow: 1;
        }
        
        .stat-icon-wrapper {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
            color: white;
            font-size: 1.75rem;
        }
        
        .bg-primary {
            background-color: var(--primary-color);
        }
        
        .bg-success {
            background-color: #28a745;
        }
        
        .bg-info {
            background-color: #17a2b8;
        }
        
        .bg-warning {
            background-color: #ffc107;
        }
        
        .stat-content {
            flex: 1;
        }
        
        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.25rem;
            color: var(--secondary-color);
            line-height: 1;
        }
        
        .stat-value .small {
            font-size: 1rem;
            font-weight: 400;
            color: #777;
        }
        
        .stat-label {
            font-size: 0.9rem;
            color: #666;
            margin: 0;
            font-weight: 500;
        }
        
        .stat-footer {
            padding: 0.75rem 1.5rem;
            font-size: 0.8rem;
            color: #555;
            text-align: center;
            font-weight: 500;
        }
        
        .bg-primary-light {
            background-color: rgba(var(--primary-color-rgb), 0.1);
        }
        
        .bg-success-light {
            background-color: rgba(40, 167, 69, 0.1);
        }
        
        .bg-info-light {
            background-color: rgba(23, 162, 184, 0.1);
        }
        
        .bg-warning-light {
            background-color: rgba(255, 193, 7, 0.1);
        }
    </style>
</head>
<body>
    <div class="main-content">
        <!-- Hero Section -->
        <div class="container">
            <div class="hero-section px-4">
                <div class="row align-items-center">
                    <div class="col-lg-8">
                        <h1 class="hero-title">Welcome to LibRA</h1>
                        <p class="hero-subtitle">Your gateway to knowledge. Discover, borrow, and explore thousands of books from our collection.</p>
                        
                        
                        
                        
                    </div>
                </div>
            </div>
            
            <!-- Stats Section -->
            <div class="stats-container mb-5">
                <h2 class="section-title">Library Statistics</h2>
                <div class="row stats-row">
                    <div class="col-md-3 mb-4">
                        <div class="stat-card">
                            <div class="stat-card-body">
                                <div class="stat-icon-wrapper bg-primary">
                                    <i class="bi bi-book"></i>
                                </div>
                                <div class="stat-content">
                                    <h3 class="stat-value"><?php echo $rowbuku['jumlahbuku'] ?? '0'; ?></h3>
                                    <p class="stat-label">Total Books</p>
                                </div>
                            </div>
                            <div class="stat-footer bg-primary-light">
                                <span>In our collection</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3 mb-4">
                        <div class="stat-card">
                            <div class="stat-card-body">
                                <div class="stat-icon-wrapper bg-success">
                                    <i class="bi bi-people"></i>
                                </div>
                                <div class="stat-content">
                                    <h3 class="stat-value"><?php echo $user_count; ?></h3>
                                    <p class="stat-label">Active Members</p>
                                </div>
                            </div>
                            <div class="stat-footer bg-success-light">
                                <span>Registered users</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3 mb-4">
                        <div class="stat-card">
                            <div class="stat-card-body">
                                <div class="stat-icon-wrapper bg-info">
                                    <i class="bi bi-calendar-check"></i>
                                </div>
                                <div class="stat-content">
                                    <h3 class="stat-value"><?php echo $borrowing_count; ?></h3>
                                    <p class="stat-label">Borrowings</p>
                                </div>
                            </div>
                            <div class="stat-footer bg-info-light">
                                <span>This month</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3 mb-4">
                        <div class="stat-card">
                            <div class="stat-card-body">
                                <div class="stat-icon-wrapper bg-warning">
                                    <i class="bi bi-star-fill"></i>
                                </div>
                                <div class="stat-content">
                                    <h3 class="stat-value"><?php echo $avg_rating; ?><span class="small">/5</span></h3>
                                    <p class="stat-label">Average Rating</p>
                                </div>
                            </div>
                            <div class="stat-footer bg-warning-light">
                                <span>Based on user reviews</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Featured Books Section -->
            <div class="featured-books-section my-5">
                <h2 class="section-title">Featured Books</h2>
                <div class="featured-books-container">
                    <?php
                    // Get featured books from database using PDO
                    $featuredBooks = [];
                    try {
                        $featuredQuery = "SELECT * FROM book ORDER BY book_year DESC LIMIT 4";
                        $stmt = $pdo->query($featuredQuery);
                        
                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            // Map database columns to our expected format
                            $featuredBooks[] = [
                                'book_id' => $row['book_id'],
                                'book_title' => $row['book_title'],
                                'book_author' => $row['book_author'],
                                'book_desc' => $row['book_desc'],
                                'book_isbn' => $row['book_isbn'],
                                'book_category' => !empty($row['book_category']) ? $row['book_category'] : 'Featured',
                                'book_year' => $row['book_year']
                            ];
                        }
                    } catch (PDOException $e) {
                        // Silently fail and use empty array
                        // echo "Error: " . $e->getMessage();
                    }
                    ?>
                    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4">
                        <?php foreach ($featuredBooks as $book): ?>
                        <div class="col">
                            <div class="featured-book-card">
                                <div class="book-cover-wrapper">
                                    <div class="book-category-badge"><?= htmlspecialchars($book['book_category'] ?? 'Featured') ?></div>
                                    <img src="https://covers.openlibrary.org/b/isbn/<?=$book['book_isbn']?>-L.jpg"
                                         class="book-cover-img" alt="<?= htmlspecialchars($book['book_title']) ?>">
                                    <div class="book-hover-overlay">
                                        <a href="book_template.php?id=<?= htmlspecialchars($book['book_id']) ?>" class="btn-view-details">View Details</a>
                                    </div>
                                </div>
                                <div class="book-details">
                                    <h3 class="book-title"><?= htmlspecialchars($book['book_title']) ?></h3>
                                    <p class="book-author">by <span><?= htmlspecialchars($book['book_author']) ?></span></p>
                                    <div class="book-meta">
                                        <div class="book-year">
                                            <i class="bi bi-calendar-event"></i>
                                            <span><?= htmlspecialchars($book['book_year']) ?></span>
                                        </div>
                                        <div class="book-status available">Available</div>
                                    </div>
                                    <p class="book-description"><?= htmlspecialchars(substr($book['book_desc'], 0, 100)) . (strlen($book['book_desc']) > 100 ? '...' : '') ?></p>
                                    <div class="book-actions">
                                        <a href="book_template.php?id=<?= htmlspecialchars($book['book_id']) ?>" class="btn-borrow">Borrow</a>
                                        <button class="btn-bookmark"><i class="bi bi-bookmark-plus"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            
            <style>
                /* Enhanced Featured Books Styles */
                .featured-books-section {
                    padding: 2rem 0;
                }
                
                .featured-book-card {
                    background: white;
                    border-radius: 12px;
                    overflow: hidden;
                    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.07);
                    transition: all 0.3s ease;
                    height: 100%;
                    display: flex;
                    flex-direction: column;
                }
                
                .featured-book-card:hover {
                    transform: translateY(-8px);
                    box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
                }
                
                .book-cover-wrapper {
                    position: relative;
                    padding-top: 140%;
                    overflow: hidden;
                }
                
                .book-cover-img {
                    position: absolute;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    object-fit: cover;
                    transition: transform 0.5s ease;
                }
                
                .featured-book-card:hover .book-cover-img {
                    transform: scale(1.08);
                }
                
                .book-category-badge {
                    position: absolute;
                    top: 15px;
                    right: 15px;
                    background: var(--primary-color);
                    color: white;
                    padding: 5px 12px;
                    border-radius: 30px;
                    font-size: 0.75rem;
                    font-weight: 600;
                    z-index: 2;
                    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
                }
                
                .book-hover-overlay {
                    position: absolute;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    background: rgba(0, 0, 0, 0.5);
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    opacity: 0;
                    transition: opacity 0.3s ease;
                }
                
                .featured-book-card:hover .book-hover-overlay {
                    opacity: 1;
                }
                
                .btn-view-details {
                    background: white;
                    color: var(--secondary-color);
                    padding: 10px 20px;
                    border-radius: 30px;
                    font-weight: 600;
                    text-decoration: none;
                    transform: translateY(20px);
                    transition: all 0.3s ease;
                }
                
                .featured-book-card:hover .btn-view-details {
                    transform: translateY(0);
                }
                
                .btn-view-details:hover {
                    background: var(--primary-color);
                    color: white;
                }
                
                .book-details {
                    padding: 1.5rem;
                    display: flex;
                    flex-direction: column;
                    flex-grow: 1;
                }
                
                .book-title {
                    font-size: 1.2rem;
                    font-weight: 700;
                    color: var(--secondary-color);
                    margin-bottom: 0.5rem;
                    line-height: 1.3;
                }
                
                .book-author {
                    font-size: 0.9rem;
                    color: #666;
                    margin-bottom: 1rem;
                }
                
                .book-author span {
                    color: var(--primary-color);
                    font-weight: 600;
                }
                
                .book-meta {
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    margin-bottom: 1rem;
                }
                
                .book-year {
                    font-size: 0.85rem;
                    display: flex;
                    align-items: center;
                    color: #666;
                }
                
                .book-year i {
                    margin-right: 5px;
                    color: var(--primary-color);
                }
                
                .book-year span {
                    font-weight: 600;
                }
                
                .book-status {
                    font-size: 0.8rem;
                    font-weight: 600;
                    padding: 4px 10px;
                    border-radius: 20px;
                }
                
                .book-status.available {
                    background: rgba(40, 167, 69, 0.1);
                    color: #28a745;
                }
                
                .book-status.borrowed {
                    background: rgba(255, 193, 7, 0.1);
                    color: #ffc107;
                }
                
                .book-description {
                    font-size: 0.9rem;
                    color: #666;
                    margin-bottom: 1.5rem;
                    line-height: 1.5;
                    flex-grow: 1;
                }
                
                .book-actions {
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    margin-top: auto;
                }
                
                .btn-borrow {
                    background: var(--primary-color);
                    color: white;
                    padding: 8px 20px;
                    border-radius: 30px;
                    font-weight: 600;
                    font-size: 0.9rem;
                    text-decoration: none;
                    transition: all 0.3s ease;
                    flex-grow: 1;
                    text-align: center;
                    margin-right: 10px;
                }
                
                .btn-borrow:hover {
                    background: var(--secondary-color);
                    text-decoration: none;
                    color: white;
                }
                
                .btn-bookmark {
                    width: 40px;
                    height: 40px;
                    border-radius: 50%;
                    background: var(--accent-color);
                    border: none;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    color: var(--primary-color);
                    font-size: 1.1rem;
                    transition: all 0.3s ease;
                    cursor: pointer;
                }
                
                .btn-bookmark:hover {
                    background: var(--primary-color);
                    color: white;
                }
            </style>
            
            <!-- Recently Added Books Section -->
            <div class="recently-added-section my-5">
                <h2 class="section-title">Recently Added</h2>
                <div class="recently-added-container">
                    <?php
                    // Get recently added books from database using PDO
                    $recentlyAddedBooks = [];
                    try {
                        $recentQuery = "SELECT * FROM book ORDER BY book_timeadded DESC LIMIT 4";
                        $recentStmt = $pdo->query($recentQuery);
                        
                        while ($row = $recentStmt->fetch(PDO::FETCH_ASSOC)) {
                            // Map database columns to our expected format
                            $recentlyAddedBooks[] = [
                                'book_id' => $row['book_id'],
                                'book_title' => $row['book_title'],
                                'book_author' => $row['book_author'],
                                'book_desc' => $row['book_desc'],
                                'book_isbn' => $row['book_isbn'],
                                'book_category' => !empty($row['book_category']) ? $row['book_category'] : 'New',
                                'book_year' => $row['book_year']
                            ];
                        }
                    } catch (PDOException $e) {
                        // Silently fail and use empty array
                        // echo "Error: " . $e->getMessage();
                    }
                    ?>
                    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4">
                        <?php foreach ($recentlyAddedBooks as $book): ?>
                        <div class="col">
                            <div class="featured-book-card">
                                <div class="book-cover-wrapper">
                                    <div class="book-category-badge"><?= htmlspecialchars($book['book_category'] ?? 'New') ?></div>
                                    <img src="https://covers.openlibrary.org/b/isbn/<?=$book['book_isbn']?>-L.jpg"
                                         class="book-cover-img" alt="<?= htmlspecialchars($book['book_title']) ?>">
                                    <div class="book-hover-overlay">
                                        <a href="book_template.php?id=<?= htmlspecialchars($book['book_id']) ?>" class="btn-view-details">View Details</a>
                                    </div>
                                </div>
                                <div class="book-details">
                                    <h3 class="book-title"><?= htmlspecialchars($book['book_title']) ?></h3>
                                    <p class="book-author">by <span><?= htmlspecialchars($book['book_author']) ?></span></p>
                                    <div class="book-meta">
                                        <div class="book-year">
                                            <i class="bi bi-calendar-event"></i>
                                            <span><?= htmlspecialchars($book['book_year']) ?></span>
                                        </div>
                                        <div class="book-status available">Available</div>
                                    </div>
                                    <p class="book-description"><?= htmlspecialchars(substr($book['book_desc'], 0, 100)) . (strlen($book['book_desc']) > 100 ? '...' : '') ?></p>
                                    <div class="book-actions">
                                        <a href="book_template.php?id=<?= htmlspecialchars($book['book_id']) ?>" class="btn-borrow">Borrow</a>
                                        <button class="btn-bookmark"><i class="bi bi-bookmark-plus"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Swiper JS -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>
    <script>
        // Initialize Swiper
        const recommendedSwiper = new Swiper('.recommended-swiper', {
            slidesPerView: 1,
            spaceBetween: 20,
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
            },
            breakpoints: {
                640: {
                    slidesPerView: 2,
                },
                992: {
                    slidesPerView: 3,
                },
                1200: {
                    slidesPerView: 4,
                },
            },
        });
        
        const recentSwiper = new Swiper('.recent-swiper', {
            slidesPerView: 1,
            spaceBetween: 20,
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
            },
            breakpoints: {
                640: {
                    slidesPerView: 2,
                },
                992: {
                    slidesPerView: 3,
                },
                1200: {
                    slidesPerView: 4,
                },
            },
        });
    </script>
<?php include 'footer.php'; ?>