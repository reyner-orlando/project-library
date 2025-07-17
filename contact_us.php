<?php include 'header.php'; ?>
<?php include 'db.php'; ?>
<?php include 'nav2.php'; ?>

<?php
// Initialize variables
$success_message = '';
$error_message = '';

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate input
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');
    
    if (empty($name) || empty($email) || empty($message)) {
        $error_message = "Please fill in all required fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Please enter a valid email address.";
    } else {
        // In a real application, you would send the email or store the message in the database
        // For now, just show a success message
        $success_message = "Thank you for your message! We will get back to you soon.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LibRA - Contact Us</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
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
        
        .contact-container {
            background-color: white;
            border-radius: 1rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            padding: 2rem;
            margin-bottom: 2rem;
        }
        
        .contact-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--secondary-color);
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
        }
        
        .contact-title i {
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
        
        .contact-info-container {
            background-color: white;
            border-radius: 1rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            padding: 2rem;
            height: 100%;
        }
        
        .contact-info-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--secondary-color);
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
        }
        
        .contact-info-title i {
            margin-right: 0.75rem;
            font-size: 1.5rem;
            color: var(--primary-color);
        }
        
        .contact-info-item {
            display: flex;
            margin-bottom: 1.5rem;
            align-items: center;
        }
        
        .contact-info-icon {
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
            flex-shrink: 0;
        }
        
        .contact-info-text {
            font-size: 1rem;
            color: #666;
        }
        
        .contact-info-text strong {
            display: block;
            color: var(--secondary-color);
            margin-bottom: 0.25rem;
        }
        
        .social-links {
            display: flex;
            margin-top: 2rem;
        }
        
        .social-link {
            width: 40px;
            height: 40px;
            background-color: var(--accent-color);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 0.75rem;
            color: var(--primary-color);
            font-size: 1.25rem;
            transition: all 0.3s ease;
        }
        
        .social-link:hover {
            background-color: var(--primary-color);
            color: white;
        }
        
        .library-hours {
            margin-top: 2rem;
        }
        
        .library-hours h4 {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--secondary-color);
            margin-bottom: 1rem;
        }
        
        .hours-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
            color: #666;
        }
        
        .hours-day {
            font-weight: 500;
        }
        
        .alert {
            border-radius: 0.5rem;
            padding: 1rem 1.5rem;
        }
        
        .map-container {
            border-radius: 1rem;
            overflow: hidden;
            margin-top: 2rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }
        
        .map-container iframe {
            width: 100%;
            height: 250px;
            border: 0;
        }
    </style>
</head>
<body>
    <div class="main-content">
        <!-- Page Header -->
        <div class="page-header">
            <div class="container">
                <h1 class="page-title">Contact Us</h1>
                <p class="page-description">Have questions or feedback? We'd love to hear from you. Get in touch with our team.</p>
            </div>
        </div>
        
        <div class="container">
            <div class="row">
                <!-- Contact Form -->
                <div class="col-lg-7">
                    <?php if (!empty($success_message)): ?>
                    <div class="alert alert-success mb-4" role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i> <?= htmlspecialchars($success_message) ?>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($error_message)): ?>
                    <div class="alert alert-danger mb-4" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i> <?= htmlspecialchars($error_message) ?>
                    </div>
                    <?php endif; ?>
                    
                    <div class="contact-container">
                        <h2 class="contact-title"><i class="bi bi-envelope"></i> Send Us a Message</h2>
                        <form method="post" action="">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="name" class="form-label">Your Name*</label>
                                    <input type="text" class="form-control" id="name" name="name" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">Email Address*</label>
                                    <input type="email" class="form-control" id="email" name="email" required>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="subject" class="form-label">Subject</label>
                                <input type="text" class="form-control" id="subject" name="subject">
                            </div>
                            
                            <div class="mb-4">
                                <label for="message" class="form-label">Message*</label>
                                <textarea class="form-control" id="message" name="message" rows="5" required></textarea>
                            </div>
                            
                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <button type="submit" class="submit-btn"><i class="bi bi-send me-2"></i> Send Message</button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Contact Information -->
                <div class="col-lg-5">
                    <div class="contact-info-container">
                        <h3 class="contact-info-title"><i class="bi bi-info-circle"></i> Contact Information</h3>
                        
                        <div class="contact-info-item">
                            <div class="contact-info-icon">
                                <i class="bi bi-geo-alt"></i>
                            </div>
                            <div class="contact-info-text">
                                <strong>Address</strong>
                                Jababeka Education Park, Jl. Ki Hajar Dewantara, RT.2/RW.4, Mekarmukti, Cikarang Utara, Bekasi Regency, West Java 17530
                            </div>
                        </div>
                        
                        <div class="contact-info-item">
                            <div class="contact-info-icon">
                                <i class="bi bi-telephone"></i>
                            </div>
                            <div class="contact-info-text">
                                <strong>Phone</strong>
                                0812-9713-4500
                            </div>
                        </div>
                        
                        <div class="contact-info-item">
                            <div class="contact-info-icon">
                                <i class="bi bi-envelope"></i>
                            </div>
                            <div class="contact-info-text">
                                <strong>Email</strong>
                                libra@president.universiyac.id
                            </div>
                        </div>
                        
                        <div class="library-hours">
                            <h4>Library Hours</h4>
                            <div class="hours-item">
                                <span class="hours-day">Monday - Friday</span>
                                <span>9:00 AM - 8:00 PM</span>
                            </div>
                            <div class="hours-item">
                                <span class="hours-day">Saturday</span>
                                <span>10:00 AM - 6:00 PM</span>
                            </div>
                            <div class="hours-item">
                                <span class="hours-day">Sunday</span>
                                <span>12:00 PM - 5:00 PM</span>
                            </div>
                        </div>
                        
                        <div class="social-links">
                            <a href="#" class="social-link"><i class="bi bi-facebook"></i></a>
                            <a href="#" class="social-link"><i class="bi bi-twitter"></i></a>
                            <a href="#" class="social-link"><i class="bi bi-instagram"></i></a>
                            <a href="#" class="social-link"><i class="bi bi-linkedin"></i></a>
                        </div>
                        
                        <div class="map-container">
                            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3588.8768324437146!2d107.1679713749909!3d-6.2850002937039315!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e6984caf54df305%3A0xb7156354ad963e4d!2sPresident%20University%20-%20Kampus%2C%20Kuliah%20di%20Cikarang!5e1!3m2!1sid!2sid!4v1746350760837!5m2!1sid!2sid" allowfullscreen></iframe>      
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
<?php include 'footer.php'; ?>