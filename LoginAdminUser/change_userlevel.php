<?php
session_start();
if (!isset($_SESSION['userlevel']) || $_SESSION['userlevel'] != 'a') { // เฉพาะแอดมินเท่านั้น
    header("Location: index.php");
    exit();
}

include('connection.php');

if (isset($_POST['user_id']) && isset($_POST['userlevel'])) {
    $user_id = $_POST['user_id'];
    $userlevel = $_POST['userlevel'];

    // อัปเดตบทบาทผู้ใช้ในฐานข้อมูล
    $query = "UPDATE user SET userlevel = '$userlevel' WHERE id = '$user_id'";
    
    if (mysqli_query($conn, $query)) {
        // ถ้าอัปเดตสำเร็จ, เปลี่ยนเส้นทางไปยังหน้า manage_users.php
        header("Location: manage_users.php");
        exit();
    } else {
        // ถ้ามีข้อผิดพลาดในการอัปเดต
        echo "Error updating user level: " . mysqli_error($conn);
        exit();
    }
} else {
    // ถ้าไม่มีข้อมูลที่จำเป็น
    header("Location: manage_users.php");
    exit();
}
?>
