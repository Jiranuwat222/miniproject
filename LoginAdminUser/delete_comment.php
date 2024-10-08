<?php
session_start();
include('connection.php');

if (isset($_POST['comment_id'])) {
    $comment_id = intval($_POST['comment_id']);
    
    // ตรวจสอบว่าผู้ใช้เป็น admin หรือเจ้าของความคิดเห็น
    $comment_query = "SELECT user_id FROM comments WHERE id = '$comment_id'";
    $comment_result = mysqli_fetch_assoc(mysqli_query($conn, $comment_query));

    if ($_SESSION['userid'] == $comment_result['user_id'] || $_SESSION['userid'] == 1) {
        $query = "DELETE FROM comments WHERE id = '$comment_id'";
        mysqli_query($conn, $query);
    }
}

header("Location: admin_page.php"); // เปลี่ยนไปยังหน้า admin page
exit();
?>
