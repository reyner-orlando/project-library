<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Login</title>
  <!-- Bootstrap 5 CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <style>
    body {
        background:  #f4f7fb;;
        /* background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%); */
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .card {
      border: none;
      border-radius: 15px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    }
    .form-control:focus {
      border-color: #6a11cb;
      box-shadow: 0 0 0 0.2rem rgba(106, 17, 203, 0.25);
    }
    .btn-primary {
      background-color: #6a11cb;
      border: none;
    }
    .btn-primary:hover {
      background-color: #5311b6;
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-6 col-lg-4">
        <div class="card p-4">
          <div class="card-body">
            <h3 class="text-center mb-4 text-primary fw-bold">Login to Your Account</h3>
            <form method="post" id="loginform">
              <div class="mb-3">
                <label for="email" class="form-label">Email Address</label>
                <input type="email" class="form-control" id="email" name="email" placeholder="your@email.com" required />
              </div>
              <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" placeholder="••••••••" required />
              </div>
              <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="remember" name="remember"/>
                <label class="form-check-label" for="remember">Remember me</label>
              </div>
              <div class="d-grid">
                <button type="submit" class="btn btn-primary">Login</button>
              </div>
            </form>
            <p class="text-center mt-3 mb-0 text-muted">
              Don't have an account? <a href="register.php" class="text-decoration-none text-primary">Register</a>
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>
  <script>
    $(document).ready(function() {
      $('#loginform').submit(function(e) {
        e.preventDefault(); // Mencegah form reload

        // Ambil data form
        var formData = $(this).serialize();

        // AJAX request
        $.ajax({
          url: 'login_process.php',
          type: 'POST',
          data: formData,
          success: function(response) {
            if (response.status === 'success') {
              Swal.fire({
                icon: 'success',
                title: 'Success!!',
                text: response.message
              }).then(() => {
                window.location.href = 'index.php';
              });
            } else {
              Swal.fire({
                icon: 'error',
                title: 'Failed!!',
                text: response.message
              });
            }
          },
          error: function() {
            Swal.fire({
              icon: 'error',
              title: 'Oops...',
              text: 'Terjadi kesalahan koneksi.'
            });
          }
        });
      });
    });
</script>
</body>
</html>
