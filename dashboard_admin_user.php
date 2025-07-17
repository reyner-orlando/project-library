<?php
include 'nav2.php'; 
include 'header.php'; 
include 'db.php'; 
// Ambil data user
$stmt = $pdo->query("SELECT u.user_id, u.user_fullname, ur.role_name, us.status_info FROM user u JOIN userrole ur ON u.role_id = ur.role_id JOIN userstatus us ON us.status_id = u.status_id;");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>LibRA - User Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        @media (min-width: 992px) {
            .main-content {
                margin-left: 250px;
            }
        }
    </style>
</head>
<body class="bg-light">
<div class="main-content">
    <div class="container py-5">
        <h2 class="mb-4 text-center">User Management</h2>
        <div class="table-responsive shadow rounded bg-white p-4">
            <table class="table table-bordered table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?= htmlspecialchars($user['user_id']) ?></td>
                        <td><?= htmlspecialchars($user['user_fullname']) ?></td>
                        <td>
                            <select name="role" class="form-select form-select-sm" data-user-id="<?= $user['user_id'] ?>">
                                <option value="99" <?= $user['role_name'] == 'Admin' ? 'selected' : '' ?>>Admin</option>
                                <option value="1" <?= $user['role_name'] == 'Member' ? 'selected' : '' ?>>User</option>
                            </select>
                        </td>
                        <td>
                            <select name="status" class="form-select form-select-sm" data-user-id="<?= $user['user_id'] ?>">
                                <option value="1" <?= $user['status_info'] == 'Active' ? 'selected' : '' ?>>Active</option>
                                <option value="0" <?= $user['status_info'] == 'Inactive' ? 'selected' : '' ?>>Inactive</option>
                                <option value="2" <?= $user['status_info'] == 'Suspended' ? 'selected' : '' ?>>Suspended</option>
                            </select>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-primary save-btn" data-user-id="<?= $user['user_id'] ?>">
                                Save Data
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
$(document).ready(function(){
    $('.save-btn').click(function(){
        let userId = $(this).data('user-id');
        let role = $('select[name="role"][data-user-id="' + userId + '"]').val();
        let status = $('select[name="status"][data-user-id="' + userId + '"]').val();

        $.ajax({
    method: 'POST',
    url: 'update_user.php', // sekarang terpisah
    data: {
        ajax_update: true,
        user_id: userId,
        role: role,
        status: status
    },
    dataType: 'json',
    success: function(response) {
        Swal.fire({
            icon: response.success ? 'success' : 'error',
            title: response.success ? 'Success!' : 'Failed!',
            text: response.message
        });
    },
    error: function(xhr, status, error) {
        console.log(xhr.responseText); // debug
        Swal.fire({
            icon: 'error',
            title: 'Failed!',
            text: 'An error ocured while sending data.'
        });
    }
});

    });
});
</script>
</body>
</html>
