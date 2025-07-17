<?php include 'nav2.php'; ?>
<?php include 'header.php'; ?>
<?php include 'db.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>LibRA - Input Book</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <!-- Select2 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
  <!-- Select2 JS -->
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

  <style>
    
    /* Matches the contact page styling */
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
    
    .book-container {
      background-color: white;
      border-radius: 1rem;
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
      padding: 2rem;
      margin-bottom: 2rem;
    }
    
    .book-title {
      font-size: 1.5rem;
      font-weight: 600;
      color: var(--secondary-color);
      margin-bottom: 1.5rem;
      display: flex;
      align-items: center;
    }
    
    .book-title i {
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
    
    .book-type-container {
      background-color: var(--accent-color);
      border-radius: 0.5rem;
      padding: 1.5rem;
      margin-bottom: 2rem;
    }
    
    .book-type-title {
      font-size: 1.25rem;
      font-weight: 600;
      color: var(--secondary-color);
      margin-bottom: 1rem;
    }
    
    .form-check-input {
      margin-top: 0.3rem;
    }
    
    .form-check-label {
      margin-left: 0.5rem;
      font-weight: 500;
    }
    
    .select2-container .select2-selection--single,
    .select2-container .select2-selection--multiple {
      height: calc(1.5em + 1.5rem + 2px);
      padding: 0.75rem 1rem;
      border: 1px solid #ddd;
      border-radius: 0.5rem;
    }
    
    .select2-container--default .select2-selection--single .select2-selection__arrow {
      height: 100%;
    }
    
    .select2-container--default .select2-selection--multiple .select2-selection__choice {
      background-color: var(--primary-color);
      border: none;
      color: white;
      padding: 0.25rem 0.5rem;
      border-radius: 0.25rem;
    }
    
    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
      color: white;
      margin-right: 0.5rem;
    }
    
    /* Fix for the main content area */
    .main-content {
      margin-top: 60px;
      padding: 0;
      min-height: 100vh;
      background-color: #f5f9fc;
    }
    
    /* Improved radio buttons */
    .book-type-radio {
      display: flex;
      gap: 2rem;
    }
    
    .book-type-option {
      display: flex;
      align-items: center;
      padding: 0.5rem 1rem;
      border-radius: 0.5rem;
      background-color: white;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
      cursor: pointer;
      transition: all 0.3s ease;
    }
    
    .book-type-option:hover {
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }
    
    .book-type-icon {
      font-size: 1.5rem;
      color: var(--primary-color);
      margin-right: 0.75rem;
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
        <h1 class="page-title">Manage Books</h1>
        <p class="page-description">Add new books or physical copies to the library database.</p>
      </div>
    </div>

    <div class="container">
      <div class="row">
        <div class="col-lg-8 mx-auto">
          <div class="book-container">
            <h2 class="book-title"><i class="bi bi-book"></i> Input Book</h2>
            
            <div class="book-type-container">
              <h3 class="book-type-title">Select Book Type</h3>
              <div class="book-type-radio">
                <label class="book-type-option">
                  <input type="radio" id="addBook" name="bookType" value="book" checked onclick="toggleForm()" class="form-check-input">
                  <span class="book-type-icon"><i class="bi bi-book"></i></span>
                  <span>Add Book</span>
                </label>
                
                <label class="book-type-option">
                  <input type="radio" id="addPhysicalBook" name="bookType" value="physicalBook" onclick="toggleForm()" class="form-check-input">
                  <span class="book-type-icon"><i class="bi bi-bookshelf"></i></span>
                  <span>Add Physical Copy</span>
                </label>
              </div>
            </div>

            <form id="inputBook">
              <!-- Form untuk Menambahkan Buku (Buku Biasa) -->
              <div id="bookForm">
                <div class="row">
                  <div class="col-md-6 mb-3">
                    <label for="bookid" class="form-label">Book ID</label>
                    <input type="text" class="form-control" id="bookid" name="bookid" required>
                  </div>
                  
                  <div class="col-md-6 mb-3">
                    <label for="isbn" class="form-label">ISBN</label>
                    <input type="text" class="form-control" id="isbn" name="isbn" placeholder="Format: 978-3-16-148410-0">
                    <small class="form-text text-muted">Enter book ISBN number (optional)</small>
                  </div>
                </div>
                
                <div class="mb-3">
                  <label for="title" class="form-label">Book Title</label>
                  <input type="text" class="form-control" id="title" name="title" required>
                </div>

                <div class="mb-3">
                  <label for="author" class="form-label">Author</label>
                  <input type="text" class="form-control" id="author" name="author" required>
                </div>

                <div class="mb-3">
                  <label for="desc" class="form-label">Description</label>
                  <textarea class="form-control" id="desc" name="desc" rows="5" required></textarea>
                </div>

                <div class="row">
                  <div class="col-md-6 mb-3">
                    <label for="year" class="form-label">Publication Year</label>
                    <input type="number" class="form-control" id="year" name="year" min="1000" max="9999" required>
                  </div>
                  
                  <div class="col-md-6 mb-3">
                    <label for="book_category" class="form-label">Category</label>
                    <select class="form-control" id="book_category" name="book_category[]" multiple>
                      <?php
                      try {
                        $categoryQuery = "SELECT * FROM category ORDER BY cat_name ASC";
                        $categoryStmt = $pdo->query($categoryQuery);
                        
                        while ($category = $categoryStmt->fetch(PDO::FETCH_ASSOC)) {
                          echo "<option value=\"" . $category['cat_id'] . "\">" . htmlspecialchars($category['cat_name']) . "</option>";
                        }
                      } catch (PDOException $e) {
                        echo "<option value=\"\">Error loading categories</option>";
                      }
                      ?>
                    </select>
                    <small class="form-text text-muted">Hold Ctrl/Cmd to select multiple categories</small>
                  </div>
                </div>
              </div>

              <!-- Form untuk Menambahkan Buku Fisik -->
              <div id="physicalBookForm" style="display:none;">
                <div class="mb-4">
                  <label for="bookSelection" class="form-label">Select Book Title</label>
                  <select class="form-control" id="bookSelection" name="bookSelection" required disabled>
                    <?php
                    try {
                      $stmt = $pdo->prepare("SELECT book_id, book_title FROM book");
                      $stmt->execute();
                      while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        echo "<option value='{$row['book_id']}'>{$row['book_title']}</option>";
                      }
                    } catch (PDOException $e) {
                      echo "Error: " . $e->getMessage();
                    }
                    ?>
                  </select>
                </div>

                <div class="mb-4">
                  <label for="copyId" class="form-label">Copy ID</label>
                  <input type="text" class="form-control" id="copyId" name="copyId" required disabled>
                </div>
              </div>

              <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                <button type="submit" class="submit-btn"><i class="bi bi-save me-2"></i> Save Book</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script>
    // Initialize Select2 for book selection dropdown
    $('#bookSelection').select2({
      placeholder: "Select or search a book title...",
      allowClear: true,
      width: '100%'
    });
    
    // Initialize Select2 for category dropdown
    $('#book_category').select2({
      placeholder: "Select categories...",
      allowClear: true,
      width: '100%'
    });
    
    // Fungsi toggleForm yang dijalankan setelah DOM dimuat
    function toggleForm() {
      const bookForm = document.getElementById('bookForm');
      const physicalBookForm = document.getElementById('physicalBookForm');
      
      if (document.getElementById('addBook').checked) {
        bookForm.style.display = 'block';
        physicalBookForm.style.display = 'none';
        
        // Nonaktifkan input fisik agar tidak divalidasi
        document.getElementById('bookSelection').disabled = true;
        document.getElementById('copyId').disabled = true;

        // Aktifkan input biasa
        document.getElementById('bookid').disabled = false;
        document.getElementById('title').disabled = false;
        document.getElementById('author').disabled = false;
        document.getElementById('isbn').disabled = false;
        document.getElementById('desc').disabled = false;
        document.getElementById('year').disabled = false;
        document.getElementById('book_category').disabled = false;

      } else {
        bookForm.style.display = 'none';
        physicalBookForm.style.display = 'block';

        // Nonaktifkan input buku biasa
        document.getElementById('bookid').disabled = true;
        document.getElementById('title').disabled = true;
        document.getElementById('author').disabled = true;
        document.getElementById('isbn').disabled = true;
        document.getElementById('desc').disabled = true;
        document.getElementById('year').disabled = true;
        document.getElementById('book_category').disabled = true;

        // Aktifkan input fisik
        document.getElementById('bookSelection').disabled = false;
        document.getElementById('copyId').disabled = false;
      }
    }

    $(document).ready(function() {
      $("#inputBook").submit(function(e) {
        e.preventDefault(); // Prevent the default form submission

        var formData = new FormData(this);

        $.ajax({
          url: "inputbook.php", // File PHP untuk menangani input
          type: "POST",
          data: formData,
          processData: false, // penting untuk FormData
          contentType: false, // penting untuk FormData
          success: function(response) {
            alert(response); // Menampilkan hasil dari proses.php
          },
          error: function(xhr, status, error) {
            alert("Error: " + error);
          }
        });
      });
    });
  </script>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>