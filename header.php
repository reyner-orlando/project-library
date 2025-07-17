<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>LibRA - Library Resource Application</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <style>
    :root {
      --primary-color: #3f72af;
      --secondary-color: #112d4e;
      --accent-color: #dbe2ef;
      --light-color: #f9f7f7;
      --dark-color: #112d4e;
      --success-color: #4caf50;
      --warning-color: #ff9800;
      --danger-color: #f44336;
    }
    
    body {
      font-family: 'Poppins', sans-serif;
      background-color: var(--light-color);
      margin: 0;
      padding: 0;
    }
    
    /* Navbar Styles */
    .navbar {
      background-color: var(--primary-color);
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
      padding: 0.75rem 1.5rem;
    }
    
    .navbar-brand {
      color: white;
      font-size: 1.8rem;
      font-weight: 700;
      letter-spacing: 0.5px;
    }
    
    .navbar-brand:hover {
      color: var(--accent-color);
    }
    
    /* Sidebar Styles */
    .sidebar {
      width: 260px;
      height: 100vh;
      position: fixed;
      top: 0;
      left: 0;
      background-color: white;
      box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
      padding-top: 70px;
      z-index: 99;
      transition: all 0.3s ease;
    }
    
    .sidebar .nav-link {
      color: var(--secondary-color);
      padding: 0.75rem 1.5rem;
      border-radius: 0.5rem;
      margin: 0.25rem 1rem;
      display: flex;
      align-items: center;
      transition: all 0.3s ease;
    }
    
    .sidebar .nav-link:hover, 
    .sidebar .nav-link.active {
      background-color: var(--accent-color);
      color: var(--secondary-color);
      font-weight: 500;
    }
    
    .sidebar .nav-link i {
      margin-right: 0.75rem;
      font-size: 1.1rem;
    }
    
    /* Mobile Sidebar */
    .offcanvas {
      width: 280px;
    }
    
    .offcanvas-header {
      background-color: var(--primary-color);
      color: white;
    }
    
    .offcanvas-title {
      font-weight: 600;
    }
    
    .offcanvas .nav-link {
      padding: 0.75rem 1rem;
      color: var(--secondary-color);
      border-radius: 0.5rem;
      margin: 0.25rem 0.5rem;
      transition: all 0.3s ease;
    }
    
    .offcanvas .nav-link:hover {
      background-color: var(--accent-color);
    }
    
    /* Main Content */
    .main-content {
      padding: 2rem;
      margin-top: 60px;
    }
    
    @media (min-width: 992px) {
      .main-content {
        margin-left: 260px;
      }
    }
    
    /* Profile Button */
    .profile-button {
      display: flex;
      align-items: center;
      background-color: rgba(255, 255, 255, 0.15);
      text-decoration: none;
      color: white;
      border: none;
      border-radius: 50px;
      padding: 0.5rem 1rem;
      transition: all 0.3s ease;
    }
    
    .profile-button:hover {
      background-color: rgba(255, 255, 255, 0.25);
      text-decoration: none;
    }
    
    .profile-button img {
      width: 32px;
      height: 32px;
      border-radius: 50%;
      margin-right: 0.5rem;
    }
    
    .profile-button i {
      font-size: 1.5rem;
      margin-right: 0.5rem;
    }
  </style>
</head>
<body>

