<?php include 'nav2.php'; ?>
<?php include 'header.php'; ?>
<?php include 'db.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>LibRA - Dashboard</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  <style>
    body {
      margin: 0;
      font-family: 'Poppins', sans-serif;
      background-color: #f4f7fb;
    }
    .main {
      margin-top: 60px;
      padding: 20px;
    }
    @media (min-width: 992px) {
      .main {
        margin-left: 250px;
      }
    }
    h1 {
      font-size: 32px;
      font-weight: 600;
      color: #333;
    }
    .card {
      border-radius: 12px;
      color: #fff;
      transition: transform 0.3s ease, box-shadow 0.3s ease;
      min-height: 150px;
    }
    .card:hover {
      box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
      cursor: pointer;
      transform: translateY(-4px);
    }
    .card-body {
      padding: 30px;
      position: relative;
    }
    .card-title {
      font-size: 20px;
      font-weight: 600;
      margin-bottom: 1rem;
    }
    .card-text {
      font-size: 16px;
    }
    .card-icon {
      font-size: 40px;
      position: absolute;
      top: 20px;
      right: 20px;
      opacity: 0.3;
    }
    .bg-blue { background-color: #007bff; }
    .bg-red { background-color: #dc3545; }
    .bg-orange { background-color: #fd7e14; }
    .bg-green { background-color: #28a745; }
  </style>
</head>
<body>
  <div class="main">
    <div class="container mt-4">
      <h1 class="text-center mb-4">Welcome to LibRA</h1>
      <div class="row g-4">

        <!-- Card 1 -->
        <div class="col-12 col-md-6 col-lg-3 mb-4">
          <div class="card bg-blue" id="add-book">
            <div class="card-body">
              <p class="card-title">New Books Added</p>
              <i class="bi bi-journal-plus card-icon"></i>
              <p class="card-text"><?php echo($rowterbaru["jumlah_buku_baru"]) ?> new books added recently.</p>
            </div>
          </div>
        </div>

        <!-- Card 2 -->
        <div class="col-12 col-md-6 col-lg-3 mb-4">
          <div class="card bg-red">
            <div class="card-body">
              <p class="card-title">Fines</p>
              <i class="bi bi-cash-coin card-icon"></i>
              <p class="card-text"><?php echo($rowfine["jumlahfine"]) ?> fines have not been paid.</p>
            </div>
          </div>
        </div>

        <!-- Card 3 -->
        <div class="col-12 col-md-6 col-lg-3 mb-4">
          <div class="card bg-orange" id="borrow-book">
            <div class="card-body">
              <p class="card-title">Borrowed Books</p>
              <i class="bi bi-book-half card-icon"></i>
              <p class="card-text"><?php echo($rowborrow["jumlahpinjam"]) ?> books are borrowed.</p>
            </div>
          </div>
        </div>

        <!-- Card 4 -->
        <div class="col-12 col-md-6 col-lg-3 mb-4">
          <div class="card bg-green">
            <div class="card-body">
              <p class="card-title">Available Books</p>
              <i class="bi bi-book card-icon"></i>
              <p class="card-text"><?php echo($rowbuku["jumlahbuku"]) ?> books are available.</p>
            </div>
          </div>
        </div>

        <div class="col-12 col-md-6 col-lg-3 mb-4">
          <div class="card bg-info" id="user-list">
            <div class="card-body">
              <p class="card-title">Users</p>
              <i class="bi bi-person card-icon"></i>
              <p class="card-text"><?php echo($rowuser["jumlahuser"]) ?> users are active.</p>
            </div>
          </div>
        </div>

      </  >
    </div>
  </div>
  <script src="js/script.js?v=<?= time() ?>"></script>
</body>
</html>
