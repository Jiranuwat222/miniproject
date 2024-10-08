<?php
session_start();
include('connection.php');

if (!isset($_SESSION['userid'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

if (isset($_POST['post_id'])) {
    $post_id = $_POST['post_id'];
    $post_id = mysqli_real_escape_string($conn, $post_id); // ป้องกัน SQL Injection

    // ลบคอมเมนต์ที่เกี่ยวข้องก่อน (ถ้าจำเป็น)
    $delete_comments_query = "DELETE FROM comments WHERE post_id = '$post_id'";
    $delete_comments_result = mysqli_query($conn, $delete_comments_query);

    if (!$delete_comments_result) {
        echo json_encode(['success' => false, 'message' => 'Error deleting comments: ' . mysqli_error($conn)]);
        exit();
    }

    // ลบโพสต์
    // เช็คว่าเป็นแอดมินหรือผู้สร้างโพสต์
    $user_id = $_SESSION['userid']; // กำหนด $user_id จาก session
    $query = "DELETE FROM posts WHERE id = '$post_id' AND (user_id = '$user_id' OR '" . $_SESSION['userlevel'] . "' = 'a')";
    $result = mysqli_query($conn, $query);

    // ตรวจสอบผลลัพธ์และเปลี่ยนเส้นทาง
    if ($result) {
        if ($_SESSION['userlevel'] === 'a') {
            header("Location: admin_page.php"); // เปลี่ยนเส้นทางกลับไปยังหน้า admin
        } else {
            header("Location: user_page.php"); // เปลี่ยนเส้นทางกลับไปยังหน้า user
        }
        exit();
    } else {
        echo json_encode(['success' => false, 'message' => 'Error deleting post: ' . mysqli_error($conn)]);
    }
}

echo json_encode(['success' => false, 'message' => 'Invalid request']);
exit();
