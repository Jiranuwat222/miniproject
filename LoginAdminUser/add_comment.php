<?php
session_start();
include('connection.php');

if (isset($_POST['content']) && isset($_POST['post_id'])) {
    $content = mysqli_real_escape_string($conn, $_POST['content']);
    $post_id = intval($_POST['post_id']);
    $user_id = $_SESSION['userid'];

    // ตรวจสอบว่ามีข้อมูลในช่องความคิดเห็นหรือไม่
    if (!empty(trim($content))) { // ใช้ trim() เพื่อตรวจสอบช่องว่าง
        $query = "INSERT INTO comments (post_id, user_id, content) VALUES ('$post_id', '$user_id', '$content')";
        mysqli_query($conn, $query);
    } else {
        // ถ้าช่องความคิดเห็นว่าง แสดงข้อความเตือน
        $_SESSION['comment_error'] = "คุณยังไม่ได้กรอกความเห็นใดๆ"; // เก็บข้อความในเซสชัน
    }
}

exit();
?>
