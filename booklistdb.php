<?php 
    include 'db.php'; 
    
    // Initialize variables
    $search = isset($_GET['search']) ? $_GET['search'] : '';
    $category = isset($_GET['category']) ? $_GET['category'] : '';
    $author = isset($_GET['author']) ? $_GET['author'] : '';
    $availability = isset($_GET['availability']) ? $_GET['availability'] : '';
    $rating = isset($_GET['rating']) ? (int)$_GET['rating'] : 0; // Add rating filter
    $sort = isset($_GET['sort']) ? $_GET['sort'] : 'title_asc';
    
    // Pagination variables
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $booksPerPage = 9; // 3x3 grid
    $offset = ($page - 1) * $booksPerPage;
    
    // Base query with subquery for ratings
    $sql = "SELECT b.*, IFNULL(ratings.avg_rating, 0) as avg_rating, IFNULL(ratings.review_count, 0) as review_count 
           FROM book b
           LEFT JOIN (
               SELECT book_id, AVG(br_rating) as avg_rating, COUNT(br_id) as review_count 
               FROM bookreview 
               GROUP BY book_id
           ) ratings ON b.book_id = ratings.book_id";
    
    // Join with assigncat if category filter is applied
    if (!empty($category)) {
        $sql .= " LEFT JOIN assigncat ac ON b.book_id = ac.book_id";
    }
    
    // No need to join with bookreview here as we're using a subquery
    
    // Join with bookcopy if availability filter is applied
    if ($availability == 'available' || $availability == 'borrowed') {
        $sql .= " LEFT JOIN bookcopy bc ON b.book_id = bc.book_id";
    }
    
    // Where conditions
    $conditions = [];
    $params = [];
    
    // Search condition
    if (!empty($search)) {
        $conditions[] = "(b.book_title LIKE ? OR b.book_author LIKE ? OR b.book_desc LIKE ?)";
        $params[] = "%$search%";
        $params[] = "%$search%";
        $params[] = "%$search%";
    }
    
    // Category filter
    if (!empty($category)) {
        $conditions[] = "ac.cat_id = ?";
        $params[] = $category;
    }
    
    // Author filter
    if (!empty($author)) {
        $conditions[] = "b.book_author = ?";
        $params[] = $author;
    }
    
    // Availability filter
    if ($availability == 'available') {
        $conditions[] = "(bc.copy_status IS NULL OR bc.copy_status = 'available')";
    } elseif ($availability == 'borrowed') {
        $conditions[] = "bc.copy_status = 'borrowed'";
    }
    
    // Rating filter
    if ($rating > 0 && $rating <= 5) {
        $conditions[] = "ratings.avg_rating >= ? AND ratings.avg_rating < ?";
        $params[] = $rating - 0.5; // Lower bound (e.g., 3.5 for 4-star filter)
        $params[] = $rating + 0.5; // Upper bound (e.g., 4.5 for 4-star filter)
    }
    
    // Add WHERE clause if conditions exist
    if (!empty($conditions)) {
        $sql .= " WHERE " . implode(" AND ", $conditions);
    }
    
    // No need for GROUP BY since we're using a subquery for aggregation
    
    // Add ORDER BY clause based on sort parameter
    switch ($sort) {
        case 'title_asc':
            $sql .= " ORDER BY b.book_title ASC";
            break;
        case 'title_desc':
            $sql .= " ORDER BY b.book_title DESC";
            break;
        case 'author_asc':
            $sql .= " ORDER BY b.book_author ASC";
            break;
        case 'newest':
            $sql .= " ORDER BY b.book_timeadded DESC";
            break;
        case 'rating_high':
            $sql .= " ORDER BY ratings.avg_rating DESC";
            break;
        case 'rating_low':
            $sql .= " ORDER BY ratings.avg_rating ASC";
            break;
        default:
            $sql .= " ORDER BY b.book_title ASC";
    }
    
    // Count total books for pagination
    $countSql = "SELECT COUNT(*) as total FROM (" . $sql . ") as counted_books";
    $countStmt = $pdo->prepare($countSql);
    $countStmt->execute($params);
    $result = $countStmt->fetch(PDO::FETCH_ASSOC);
    $totalBooks = $result['total'] ?? 0;
    $totalPages = ceil($totalBooks / $booksPerPage);
    
    // If page is out of range, adjust it
    if ($page < 1) $page = 1;
    if ($page > $totalPages && $totalPages > 0) $page = $totalPages;
    
    // Add LIMIT clause for pagination
    $sql .= " LIMIT $offset, $booksPerPage";
    
    // Prepare and execute the query
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $resulthasilbuku = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get all categories for the filter dropdown
    $categoryQuery = "SELECT * FROM category ORDER BY cat_name ASC";
    $categoryStmt = $pdo->query($categoryQuery);
    $categories = $categoryStmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get all authors for the filter dropdown
    $authorQuery = "SELECT DISTINCT book_author FROM book ORDER BY book_author ASC";
    $authorStmt = $pdo->query($authorQuery);
    $authors = $authorStmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get rating distribution for the filter dropdown
    $ratingQuery = "SELECT FLOOR(br_rating) as rating, COUNT(*) as count 
                   FROM bookreview 
                   GROUP BY FLOOR(br_rating) 
                   ORDER BY rating DESC";
    $ratingStmt = $pdo->query($ratingQuery);
    $ratingDistribution = $ratingStmt->fetchAll(PDO::FETCH_ASSOC);
?>