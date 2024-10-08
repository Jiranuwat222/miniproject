<?php 
session_start();
if (!isset($_SESSION['userlevel']) || $_SESSION['userlevel'] != 'a') { // เฉพาะแอดมินเท่านั้น
    header("Location: index.php");
    exit();
}

include('connection.php');

if (isset($_POST['user_id'])) {
    $user_id = $_POST['user_id'];

    // ลบความคิดเห็นที่เชื่อมโยงกับผู้ใช้ก่อน
    $delete_comments = $conn->prepare("DELETE FROM comments WHERE user_id = ?");
    $delete_comments->bind_param("i", $user_id);
    $delete_comments->execute();
    $delete_comments->close();

    // ใช้ Prepared Statements เพื่อลบผู้ใช้จากฐานข้อมูล
    $stmt = $conn->prepare("DELETE FROM user WHERE id = ?");
    $stmt->bind_param("i", $user_id); // "i" แสดงถึง integer
    $stmt->execute();

    // ตรวจสอบผลลัพธ์
    if ($stmt->affected_rows > 0) {
        // ลบสำเร็จ
        header("Location: manage_users.php");
        exit();
    } else {
        // ถ้ามีปัญหาในการลบ
        echo "<script>
            alert('ไม่สามารถลบบัญชีผู้ใช้ได้');
            window.location.href = 'manage_users.php';
            </script>";
        exit();
    }

    $stmt->close(); // ปิด statement
}
?>
