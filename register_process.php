<?php
include 'db.php';
header('Content-Type: application/json');

try {
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $birthdate = $_POST['birthdate'];
    $rawpassword = $_POST['password'];
    $hashedPassword = password_hash($rawpassword, PASSWORD_DEFAULT);
    $check = $pdo->prepare("SELECT COUNT(*) FROM user WHERE user_email = :email");
    $check->bindParam(':email', $email, PDO::PARAM_STR);
    $check->execute();
    if ($check->fetchColumn() > 0) {
        echo json_encode([
            'status' => 'error',
            'message' => '❌ Email sudah digunakan. Silakan gunakan email lain.'
        ]);
        exit;
    }
    // Email belum digunakan, lanjutkan insert
    $sql = "INSERT INTO user (user_fullname, user_dob, user_email, user_password)
            VALUES (:fullname, :dob, :email, :password)";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':fullname', $fullname, PDO::PARAM_STR);
    $stmt->bindParam(':dob', $birthdate, PDO::PARAM_STR);
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->bindParam(':password', $hashedPassword, PDO::PARAM_STR);
    $stmt->execute();
    echo json_encode([
        'status' => 'success',
        'message' => '✅ Pendaftaran berhasil!'
    ]);
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => '❌ Gagal mendaftar: ' . $e->getMessage()
    ]);
}
?>
