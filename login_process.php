<?php
include 'db.php';
header('Content-Type: application/json');


try{

    $email = $_POST['email'];
    $rawpassword = $_POST['password'];
    $sql = "SELECT * FROM user WHERE user_email = :email;";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->execute();
    $checklogin = $stmt->fetch(PDO::FETCH_ASSOC);
    if(password_verify($rawpassword, $checklogin['user_password'])){
        session_start();
        $_SESSION['user_id'] = $checklogin['user_id'];
        $_SESSION['username'] = $checklogin['user_fullname'];
        if(isset($_POST['remember'])){
            setcookie("remember_email", $email, time() + (86400)); //1 day
        }else{
            setcookie("remember_email", "", time() - 3600); // delete cookies
        }
        if($checklogin['role_id'] == 99){
            $_SESSION['role'] = "admin";
        }else{
            $_SESSION['role'] = "member";
        }
        echo json_encode([
            'status' => 'success',
            'message' => '✅ Login Success!'
        ]);
        exit;
    }else{
        echo json_encode([
            'status' => 'error',
            'message' => '❌ Login Failed!'
        ]);
    }
}catch(Exception $e){
    echo json_encode([
        'status' => 'error',
        'message' => '❌ Gagal Login: ' . $e->getMessage()
    ]);
}


?>