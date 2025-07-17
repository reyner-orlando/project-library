<?php
session_start();
session_unset();  
// Destroy the session
session_destroy();
    
// Redirect to login page
header('Location: login.php');
exit;
?>