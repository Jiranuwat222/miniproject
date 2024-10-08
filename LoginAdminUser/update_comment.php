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
    $query = "UPDATE comments SET content = '$new_content' WHERE id = '$comment_id' AND user_id = '{$_SESSION['userid']}'"; // ตรวจสอบว่าเป็นของผู้ใช้ที่ล็อกอินอยู่
    if (mysqli_query($conn, $query)) {
        // ถ้าอัปเดตสำเร็จ
        echo "Comment updated successfully"; // อาจเปลี่ยนเป็น JSON response ถ้าต้องการ
    } else {
        // ถ้ามีข้อผิดพลาด
        echo "Error updating comment: " . mysqli_error($conn);
    }
}
?>
