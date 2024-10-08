<?php
session_start();
if (!isset($_SESSION['userid']) || !isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}

include('connection.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $post_id = $_POST['post_id'];
    $title = $_POST['title']; // เพิ่มการจัดการหัวข้อโพสต์
    $content = $_POST['content'];

    // ป้องกัน SQL Injection
    $post_id = mysqli_real_escape_string($conn, $post_id);
    $title = mysqli_real_escape_string($conn, $title); // ป้องกัน SQL Injection
    $content = mysqli_real_escape_string($conn, $content);

    // อัปเดตโพสต์ในฐานข้อมูล
    $update_query = "UPDATE posts SET title = '$title', content = '$content', updated_at = NOW() WHERE id = '$post_id'"; // เพิ่มการอัปเดตหัวข้อ
    if (mysqli_query($conn, $update_query)) {
        // ส่งค่าผลลัพธ์กลับไปยังหน้าแก้ไขโพสต์
        echo json_encode(['status' => 'success', 'post_id' => $post_id]);
    } else {
        echo json_encode(['status' => 'error', 'message' => mysqli_error($conn)]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'วิธีการร้องขอไม่ถูกต้อง']);
}
?>