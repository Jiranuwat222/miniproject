<?php
session_start();

// ตรวจสอบว่า session มีการ login อยู่หรือไม่
if (!isset($_SESSION['userid']) || !isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}

include('connection.php');

// ตรวจสอบ userlevel ของผู้ใช้
$user_id = $_SESSION['userid'];
$user_query = "SELECT userlevel FROM user WHERE id = '$user_id'";
$user_result = mysqli_query($conn, $user_query);
$user = mysqli_fetch_assoc($user_result);
$is_admin = $user['userlevel'] === 'a'; // ถ้า userlevel เป็น 'a' หมายถึงแอดมิน

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // รับข้อมูลจากฟอร์ม
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $content = mysqli_real_escape_string($conn, $_POST['content']);

    // คำสั่ง SQL สำหรับเพิ่มโพสต์ใหม่
    $query = "INSERT INTO posts (user_id, title, content, created_at) VALUES ('$user_id', '$title', '$content', NOW())";

    if (mysqli_query($conn, $query)) {
        // ตรวจสอบว่าเป็น Admin หรือ User
        if ($is_admin) {
            header("Location: admin_page.php"); // Redirect ไปยังหน้า Admin
        } else {
            header("Location: user_page.php"); // Redirect ไปยังหน้า User
        }
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ตั้งกระทู้ใหม่</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #d59ede, #b577d1, #9966cc); /* ไล่ระดับสีม่วงเข้ม */
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
        }
        .container {
            width: 80%;
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            color: #444;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            color: #333;
        }
        input[type="text"],
        textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-sizing: border-box;
        }
        button {
            background-color: #FF4D94; /* สีฟ้าเข้มชมพู */
            color: white; /* สีข้อความ */
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: #FF007A; /* สีฟ้าเข้มชมพูเข้มเมื่อ hover */
        }
        .back-btn {
            background-color: #FF4D94; /* สีเดียวกับปุ่มตั้งกระทู้ */
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-align: center;
            display: block;
            width: 80%; /* ขยายปุ่มเต็มความกว้าง */
            
        }

        .back-btn:hover {
            background-color: #FF007A; /* สีเดียวกับปุ่มตั้งกระทู้เมื่อ hover */
        }

        /* เพิ่มการจัดวางปุ่มให้มีช่องว่างหรือจัดตำแหน่งได้ */
        .button-container {
            margin-top: 30px; /* ขยับตำแหน่งปุ่มทั้งกล่อง */
            display: flex;
            justify-content: center; /* จัดตำแหน่งปุ่มให้อยู่ตรงกลาง */
        }
    </style>
</head>
<body>

<div class="container">
    <h1>ตั้งกระทู้ใหม่</h1>
    <form action="new_post.php" method="POST">
        <div class="form-group">
            <label for="title">หัวข้อ:</label>
            <input type="text" id="title" name="title" required>
        </div>
        <div class="form-group">
            <label for="content">เนื้อหา:</label>
            <textarea id="content" name="content" rows="5" required></textarea>
        </div>
        <button type="submit">ตั้งกระทู้</button>
    </form>

    <!-- ปุ่มกลับหน้า admin หรือ user ขึ้นอยู่กับสิทธิ์ -->
    <div class="button-container">
        <?php if ($is_admin): ?>
            <a href="admin_page.php" class="back-btn">กลับหน้า Admin</a>
        <?php else: ?>
            <a href="user_page.php" class="back-btn">กลับหน้า User</a>
        <?php endif; ?>
    </div>
</div>

</body>
</html>