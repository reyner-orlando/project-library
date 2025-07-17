<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
  <!-- Top Navbar -->
  <nav class="navbar fixed-top">
    <div class="container-fluid">
      <!-- Mobile Toggle Button -->
      <button class="btn d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarMenu" aria-controls="sidebarMenu" style="color: white;">
        <i class="bi bi-list fs-4"></i>
      </button>
      
      <!-- Brand -->
      <a class="navbar-brand" href="index.php">LibRA</a>
      
      <!-- Profile Button -->
      <a href="<?php echo isset($_SESSION['user_id']) ? 'dashboard_user.php' : 'login.php'; ?>" class="profile-button">
        <i class="bi bi-person-circle"></i>
        <span class="d-none d-md-inline">
          <?php echo isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'My Profile'; ?>
        </span>
      </a>
    </div>
  </nav>
  
  <!-- Sidebar for Desktop -->
  <div class="sidebar d-none d-lg-block">
    <ul class="nav flex-column">
      <li class="nav-item">
        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>" href="index.php">
          <i class="bi bi-house-door"></i> Home
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'booklist.php' ? 'active' : ''; ?>" href="booklist.php">
          <i class="bi bi-book"></i> Books
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'book_request.php' ? 'active' : ''; ?>" href="book_request.php">
          <i class="bi bi-file-earmark-text"></i> Book Request
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'contact_us.php' ? 'active' : ''; ?>" href="contact_us.php">
          <i class="bi bi-envelope"></i> Contact Us
        </a>
      </li>
      <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
      <li class="nav-item">
        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard_admin.php' ? 'active' : ''; ?>" href="dashboard_admin.php">
          <i class="bi bi-gear"></i> Book Management
        </a>
      </li>
      <?php endif; ?>
    </ul>
  </div>
  
  <!-- Sidebar for Mobile (Offcanvas) -->
  <div class="offcanvas offcanvas-start" tabindex="-1" id="sidebarMenu" aria-labelledby="sidebarMenuLabel">
    <div class="offcanvas-header">
      <h5 class="offcanvas-title" id="sidebarMenuLabel">LibRA Menu</h5>
      <button type="button" class="btn-close text-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
      <ul class="nav flex-column">
        <li class="nav-item">
          <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>" href="index.php">
            <i class="bi bi-house-door"></i> Home
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'booklist.php' ? 'active' : ''; ?>" href="booklist.php">
            <i class="bi bi-book"></i> Books
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'book_request.php' ? 'active' : ''; ?>" href="book_request.php">
            <i class="bi bi-file-earmark-text"></i> Book Request
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'contact_us.php' ? 'active' : ''; ?>" href="contact_us.php">
            <i class="bi bi-envelope"></i> Contact Us
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard_user.php' ? 'active' : ''; ?>" href="<?php echo isset($_SESSION['user_id']) ? 'dashboard_user.php' : 'login.php'; ?>">
            <i class="bi bi-person"></i> My Profile
          </a>
        </li>
        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
        <li class="nav-item">
          <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'book_management.php' ? 'active' : ''; ?>" href="book_management.php">
            <i class="bi bi-gear"></i> Book Management
          </a>
        </li>
        <?php endif; ?>
      </ul>
    </div>
  </div>

