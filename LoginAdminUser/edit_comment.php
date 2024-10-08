<?php
session_start();
include('connection.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $comment_id = $_POST['comment_id'];
    $new_content = $_POST['content'];

    // ป้องกัน SQL Injection
    $comment_id = mysqli_real_escape_string($conn, $comment_id);
    $new_content = mysqli_real_escape_string($conn, $new_content);

    // ตรวจสอบว่า comment_id และ new_content มีค่าหรือไม่
    if (empty($comment_id) || empty($new_content)) {
        echo "Comment ID or content cannot be empty.";
        exit();
    }

    // อัปเดตความคิดเห็นในฐานข้อมูล
    $query = "UPDATE comments SET content = '$new_content' WHERE id = '$comment_id'";
    if (mysqli_query($conn, $query)) {
        // ถ้าอัปเดตสำเร็จ
        header("Location: admin_page.php"); // เปลี่ยนเป็นเส้นทางที่ต้องการไปหลังจากแก้ไขเสร็จ
        exit();
    } else {
        // ถ้ามีข้อผิดพลาด
        echo "Error updating comment: " . mysqli_error($conn);
    }
}
?>
