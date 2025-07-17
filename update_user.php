<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['ajax_update'])) {
    $id = $_POST['user_id'];
    $new_role = $_POST['role'];
    $new_status = $_POST['status'];

    $stmt = $pdo->prepare("UPDATE user SET role_id = ?, status_id = ? WHERE user_id = ?");
    $success = $stmt->execute([$new_role, $new_status, $id]);

    header('Content-Type: application/json');
    echo json_encode([
        'success' => $success,
        'message' => $success ? 'Update Succesful!' : 'Update Failedl!'
    ]);
    exit;
}
?>
