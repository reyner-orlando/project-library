<?php include 'header.php'; ?>
<?php if (isset($_SESSION['message'])): ?>
<script>
    Swal.fire({
        icon: 'info',
        title: 'Notifikasi',
        text: '<?= addslashes($_SESSION['message']) ?>',
        confirmButtonText: 'OK'
    });
</script>
<?php unset($_SESSION['message']); endif; ?>
<?php include 'booklistdb.php'; ?>
<?php include 'nav2.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LibRA - Book Collection</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <style>
    
    
    .page-header {
      background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
      color: white;
      padding: 2rem 0;
      border-radius: 0 0 1rem 1rem;
      margin-bottom: 2rem;
    }
    
    .page-title {
      font-size: 2rem;
      font-weight: 600;
    }
    
    .search-container {
      background-color: white;
      border-radius: 50px;
      padding: 0.5rem;
      display: flex;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
      margin-bottom: 1.5rem;
      width: 100%;
    }
    
    .search-input {
      border: none;
      outline: none;
      padding: 0.5rem 1rem;
      flex: 1;
      border-radius: 50px;
    }
    
    .search-btn {
      background-color: var(--primary-color);
      color: white;
      border: none;
      border-radius: 50px;
      padding: 0.5rem 1.5rem;
      cursor: pointer;
      transition: all 0.3s ease;
    }
    
    .search-btn:hover {
      background-color: var(--secondary-color);
    }
    
    .filter-container {
      background-color: white;
      border-radius: 1rem;
      padding: 1.5rem;
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
      margin-bottom: 1.5rem;
    }
    
    .filter-title {
      font-size: 1.1rem;
      font-weight: 600;
      margin-bottom: 1rem;
      color: var(--secondary-color);
    }
    
    .filter-group {
      margin-bottom: 1rem;
    }
    
    .filter-label {
      font-weight: 500;
      color: var(--secondary-color);
      margin-bottom: 0.5rem;
    }
    
    .filter-select {
      border: 1px solid #ddd;
      border-radius: 0.5rem;
      padding: 0.5rem;
      width: 100%;
      outline: none;
      transition: all 0.3s ease;
    }
    
    .filter-select:focus {
      border-color: var(--primary-color);
      box-shadow: 0 0 0 0.2rem rgba(63, 114, 175, 0.25);
    }
    
    .filter-btn {
      background-color: var(--primary-color);
      color: white;
      border: none;
      border-radius: 0.5rem;
      padding: 0.5rem 1rem;
      width: 100%;
      cursor: pointer;
      transition: all 0.3s ease;
    }
    
    .filter-btn:hover {
      background-color: var(--secondary-color);
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
      background-color: #f0f0f0;
      display: flex;
      align-items: center;
      justify-content: center;
      overflow: hidden;
    }
    
    .book-img {
      max-height: 180px;
      max-width: 80%;
      transition: all 0.3s ease;
    }
    
    .book-card:hover .book-img {
      transform: scale(1.05);
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
      margin-bottom: 0.5rem;
    }
    
    .book-rating {
      margin-bottom: 0.75rem;
      display: flex;
      align-items: center;
    }
    
    .rating-stars {
      color: #f8ce0b;
      font-size: 0.9rem;
      margin-right: 0.5rem;
    }
    
    .rating-value {
      font-size: 0.9rem;
      font-weight: 600;
      color: #555;
    }
    
    .book-id {
      font-size: 0.8rem;
      color: #999;
      margin-bottom: 1rem;
    }
    
    .book-action {
      display: flex;
      justify-content: space-between;
      align-items: center;
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
    }
    
    .delete-btn {
      background-color: #f8d7da;
      color: #dc3545;
      border: none;
      border-radius: 50px;
      padding: 0.4rem 1rem;
      font-size: 0.85rem;
      font-weight: 500;
      cursor: pointer;
      transition: all 0.3s ease;
    }
    
    .delete-btn:hover {
      background-color: #dc3545;
      color: white;
    }
    
    .pagination-container {
      display: flex;
      justify-content: center;
      margin-top: 2rem;
    }
    
    .pagination .page-link {
      color: var(--secondary-color);
      border-radius: 0.5rem;
      margin: 0 0.25rem;
      transition: all 0.3s ease;
    }
    
    .pagination .page-link:hover {
      background-color: var(--accent-color);
      color: var(--secondary-color);
    }
    
    .pagination .active .page-link {
      background-color: var(--primary-color);
      border-color: var(--primary-color);
    }
    </style>
</head>
<body>
  <div class="main-content">
    <!-- Page Header -->
    <div class="page-header">
      <div class="container">
        <h1 class="page-title mb-3">Explore Our Book Collection</h1>
        <div class="search-container">
          <form action="booklist.php" method="GET" id="searchForm" class="d-flex w-100">
            <input type="text" class="search-input" name="search" placeholder="Search by title, author, or genre..." value="<?= htmlspecialchars($search ?? '') ?>">
            <button type="submit" class="search-btn">Search</button>
            <!-- Hidden fields to preserve filters when searching -->
            <input type="hidden" name="category" value="<?= htmlspecialchars($category ?? '') ?>">
            <input type="hidden" name="author" value="<?= htmlspecialchars($author ?? '') ?>">
            <input type="hidden" name="availability" value="<?= htmlspecialchars($availability ?? '') ?>">
            <input type="hidden" name="rating" value="<?= htmlspecialchars($rating ?? '') ?>">
            <input type="hidden" name="sort" value="<?= htmlspecialchars($sort ?? 'title_asc') ?>">
          </form>
        </div>
      </div>
    </div>
    
    <div class="container">
      <div class="row">
        <!-- Filters Sidebar -->
        <div class="col-lg-3 mb-4">
          <div class="filter-container">
            <h3 class="filter-title">Filter Books</h3>
            <form action="booklist.php" method="GET" id="filterForm">
              <div class="filter-group">
                <label class="filter-label">Category</label>
                <select class="filter-select" name="category" id="categoryFilter">
                  <option value="">All Categories</option>
                  <?php foreach ($categories as $cat): ?>
                  <option value="<?= $cat['cat_id'] ?>" <?= ($category == $cat['cat_id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($cat['cat_name']) ?>
                  </option>
                  <?php endforeach; ?>
                </select>
              </div>
              
              <div class="filter-group">
                <label class="filter-label">Author</label>
                <select class="filter-select" name="author" id="authorFilter">
                  <option value="">All Authors</option>
                  <?php foreach ($authors as $auth): ?>
                  <option value="<?= htmlspecialchars($auth['book_author']) ?>" <?= ($author == $auth['book_author']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($auth['book_author']) ?>
                  </option>
                  <?php endforeach; ?>
                </select>
              </div>
              
              <div class="filter-group">
                <label class="filter-label">Availability</label>
                <select class="filter-select" name="availability" id="availabilityFilter">
                  <option value="" <?= empty($availability) ? 'selected' : '' ?>>All Books</option>
                  <option value="available" <?= ($availability == 'available') ? 'selected' : '' ?>>Available Now</option>
                  <option value="borrowed" <?= ($availability == 'borrowed') ? 'selected' : '' ?>>Currently Borrowed</option>
                </select>
              </div>
              
              <div class="filter-group">
                <label class="filter-label">Rating</label>
                <select class="filter-select" name="rating" id="ratingFilter">
                  <option value="0" <?= ($rating == 0) ? 'selected' : '' ?>>All Ratings</option>
                  <?php for ($i = 5; $i >= 1; $i--): ?>
                    <option value="<?= $i ?>" <?= ($rating == $i) ? 'selected' : '' ?>>
                      <?= $i ?> Star<?= ($i > 1) ? 's' : '' ?>
                    </option>
                  <?php endfor; ?>
                </select>
              </div>
              
              <!-- Hidden field to preserve search term when filtering -->
              <input type="hidden" name="search" value="<?= htmlspecialchars($search ?? '') ?>">
              <input type="hidden" name="sort" value="<?= htmlspecialchars($sort ?? 'title_asc') ?>">
              
              <button type="submit" class="filter-btn mt-3">
                <i class="bi bi-funnel me-2"></i> Apply Filters
              </button>
            </form>
          </div>
        </div>
        
        <!-- Book Grid -->
        <div class="col-lg-9">
          <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="m-0">Book Collection</h2>
            <div class="d-flex align-items-center">
              <label class="me-2 mb-0">Sort by:</label>
              <select class="form-select" id="sortSelect">
                <option value="title_asc" <?= ($sort == 'title_asc') ? 'selected' : '' ?>>Title (A-Z)</option>
                <option value="title_desc" <?= ($sort == 'title_desc') ? 'selected' : '' ?>>Title (Z-A)</option>
                <option value="author_asc" <?= ($sort == 'author_asc') ? 'selected' : '' ?>>Author</option>
                <option value="newest" <?= ($sort == 'newest') ? 'selected' : '' ?>>Newest First</option>
                <option value="rating_high" <?= ($sort == 'rating_high') ? 'selected' : '' ?>>Highest Rated</option>
                <option value="rating_low" <?= ($sort == 'rating_low') ? 'selected' : '' ?>>Lowest Rated</option>
              </select>
            </div>
          </div>
          
          <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-4">
            <?php foreach ($resulthasilbuku as $book): ?>
            <div class="col">
              <div class="book-card">
                <div class="book-img-container">
                  <img src="https://covers.openlibrary.org/b/isbn/<?= htmlspecialchars($book['book_isbn']) ?>-L.jpg" class="book-img" alt="<?= htmlspecialchars($book['book_title']) ?>">
                </div>
                <div class="book-info">
                  <h3 class="book-title"><?= htmlspecialchars($book['book_title']) ?></h3>
                  <p class="book-author"><?= htmlspecialchars($book['book_author']) ?></p>
                  
                  <!-- Book Rating -->
                  <div class="book-rating">
                    <div class="rating-stars">
                      <?php 
                      $bookRating = round($book['avg_rating'], 1);
                      for($i = 1; $i <= 5; $i++): 
                        if($i <= $bookRating): ?>
                          <i class="bi bi-star-fill"></i>
                        <?php elseif($i - 0.5 <= $bookRating): ?>
                          <i class="bi bi-star-half"></i>
                        <?php else: ?>
                          <i class="bi bi-star"></i>
                        <?php endif;
                      endfor; ?>
                    </div>
                    <div class="rating-value">
                      <?= number_format($bookRating, 1) ?> 
                      <span class="text-muted">(<?= $book['review_count'] ?> <?= $book['review_count'] == 1 ? 'review' : 'reviews' ?>)</span>
                    </div>
                  </div>
                  
                  <p class="book-id">ISBN: <?= htmlspecialchars($book['book_isbn']) ?></p>
                  <div class="book-action">
                    <a href="book_template.php?id=<?= htmlspecialchars($book['book_id']) ?>" class="view-btn">View Details</a>
                    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                    <button class="delete-btn" onclick="konfirmasiHapus(<?= htmlspecialchars($book['book_id']) ?>)">
                      <i class="bi bi-trash"></i> Delete
                    </button>
                    <?php endif; ?>
                  </div>
                </div>
              </div>
            </div>
            <?php endforeach; ?>
          </div>
          
          <!-- Pagination -->
          <?php if ($totalPages > 1): ?>
          <div class="pagination-container mt-5">
            <nav aria-label="Page navigation">
              <ul class="pagination">
                <!-- Previous page link -->
                <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                  <a class="page-link" href="<?= ($page <= 1) ? '#' : '?page='.($page-1).
                    (empty($search) ? '' : '&search='.$search).
                    (empty($category) ? '' : '&category='.$category).
                    (empty($author) ? '' : '&author='.$author).
                    (empty($availability) ? '' : '&availability='.$availability).
                    ($rating > 0 ? '&rating='.$rating : '').
                    (empty($sort) ? '' : '&sort='.$sort) ?>" 
                    aria-label="Previous" <?= ($page <= 1) ? 'tabindex="-1" aria-disabled="true"' : '' ?>>
                    <span aria-hidden="true">&laquo;</span>
                  </a>
                </li>
                
                <!-- Page number links -->
                <?php 
                // Determine range of page numbers to display
                $startPage = max(1, $page - 2);
                $endPage = min($totalPages, $page + 2);
                
                // Ensure we always show 5 page links if possible
                if ($endPage - $startPage + 1 < 5 && $totalPages >= 5) {
                    if ($startPage == 1) {
                        $endPage = min(5, $totalPages);
                    } elseif ($endPage == $totalPages) {
                        $startPage = max(1, $totalPages - 4);
                    }
                }
                
                // Generate page links
                for ($i = $startPage; $i <= $endPage; $i++): 
                ?>
                <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                  <a class="page-link" href="?page=<?= $i.
                    (empty($search) ? '' : '&search='.$search).
                    (empty($category) ? '' : '&category='.$category).
                    (empty($author) ? '' : '&author='.$author).
                    (empty($availability) ? '' : '&availability='.$availability).
                    ($rating > 0 ? '&rating='.$rating : '').
                    (empty($sort) ? '' : '&sort='.$sort) ?>">
                    <?= $i ?>
                  </a>
                </li>
                <?php endfor; ?>
                
                <!-- Next page link -->
                <li class="page-item <?= ($page >= $totalPages) ? 'disabled' : '' ?>">
                  <a class="page-link" href="<?= ($page >= $totalPages) ? '#' : '?page='.($page+1).
                    (empty($search) ? '' : '&search='.$search).
                    (empty($category) ? '' : '&category='.$category).
                    (empty($author) ? '' : '&author='.$author).
                    (empty($availability) ? '' : '&availability='.$availability).
                    ($rating > 0 ? '&rating='.$rating : '').
                    (empty($sort) ? '' : '&sort='.$sort) ?>" 
                    aria-label="Next" <?= ($page >= $totalPages) ? 'tabindex="-1" aria-disabled="true"' : '' ?>>
                    <span aria-hidden="true">&raquo;</span>
                  </a>
                </li>
              </ul>
            </nav>
          </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<!-- SweetAlert2 CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// Handle sorting change
document.getElementById('sortSelect').addEventListener('change', function() {
  const sortValue = this.value;
  const currentUrl = new URL(window.location.href);
  currentUrl.searchParams.set('sort', sortValue);
  window.location.href = currentUrl.toString();
});

// Auto-submit filters on change
document.getElementById('categoryFilter').addEventListener('change', function() {
  document.getElementById('filterForm').submit();
});

document.getElementById('authorFilter').addEventListener('change', function() {
  document.getElementById('filterForm').submit();
});

document.getElementById('availabilityFilter').addEventListener('change', function() {
  document.getElementById('filterForm').submit();
});

document.getElementById('ratingFilter').addEventListener('change', function() {
  document.getElementById('filterForm').submit();
});

function konfirmasiHapus(bookId) {
    Swal.fire({
        title: 'Delete Book?',
        text: "This book will permanently deleted!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('delete_book.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ book_id: bookId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: data.message,
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: data.message
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Kesalahan Server',
                    text: 'Tidak dapat menghapus buku.'
                });
            });
        }
    });
}
</script>

<?php include 'footer.php'; ?>