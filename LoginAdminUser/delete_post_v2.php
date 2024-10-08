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
    $query = "DELETE FROM posts WHERE id = '$post_id' AND (user_id = '" . $_SESSION['userid'] . "' OR '" . $_SESSION['userlevel'] . "' = 'a')";
    $result = mysqli_query($conn, $query);

    // ตรวจสอบผลลัพธ์
    if ($result) {
        echo json_encode(['success' => true, 'message' => 'กระทู้ถูกลบแล้ว']);
    } else {
        echo json_encode(['success' => false, 'message' => 'ลบกระทู้ไม่สำเร็จ: ' . mysqli_error($conn)]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit();
}
