document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('add-book').addEventListener('click', function(e) {
        window.location.href = "dashboard_book.php";

    });
    document.getElementById('borrow-book').addEventListener('click', function(e) {
        window.location.href = "dashboard_borrow.php";

    })
    document.getElementById('user-list').addEventListener('click', function(e) {
        window.location.href = "dashboard_admin_user.php";

    })
    
});