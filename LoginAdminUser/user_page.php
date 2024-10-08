<?php
session_start();

// ตรวจสอบว่า session มีการ login อยู่หรือไม่
if (!isset($_SESSION['userid']) || !isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}

include('connection.php');

// การดึงโพสต์ทั้งหมด (เรียงจากใหม่ไปเก่า)
$post_query = "SELECT posts.*, user.firstname, user.lastname FROM posts JOIN user ON posts.user_id = user.id ORDER BY posts.created_at DESC";
$post_result = mysqli_query($conn, $post_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>หน้าหลัก</title>
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
        .post {
            margin-bottom: 20px;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #fafafa;
        }
        .post-title {
            font-weight: bold;
            font-size: 24px;
            color: #007BFF;
            margin-bottom: 10px;
        }
        .post-content {
            font-size: 16px;
            margin-top: 5px;
            color: #333;
        }
        .post-meta {
            font-size: 14px;
            color: #777;
            margin-top: 10px;
        }
        .user-info {
            text-align: right;
            margin-bottom: 20px;
        }
        .logout-button {
            background-color: #dc3545; /* สีแดง */
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .logout-button:hover {
            background-color: #c82333; /* สีแดงเข้มเมื่อ hover */
        }
        .manage-button {
    display: inline-block;
    background-color: #8A2BE2; /* สีม่วงฟ้า */
    color: white; /* สีข้อความ */
    padding: 10px 15px;
    border: none;
    border-radius: 5px;
    text-decoration: none;
    margin-right: 10px;
    transition: background-color 0.3s; /* เพิ่มการเปลี่ยนแปลงสี */
}

.manage-button:hover {
    background-color: #7B1FA2; /* สีม่วงเข้มขึ้นเมื่อ hover */
}

    </style>
</head>
<body>

<div class="container">
    <div class="user-info">
    <img src="logo.png" alt="" width="150" style="margin-bottom: -100px; margin-left: -20px; margin-right: auto; display: block;">   <span>ยินดีต้อนรับ: <?php echo htmlspecialchars($_SESSION['user']); ?></span>
        <form action="logout.php" method="POST" style="display: inline;">
            <button type="submit" class="logout-button">ล็อกเอาท์</button>
        </form>
    </div>
    
    <h1>User </h1>

    <!-- ปุ่มจัดการสำหรับผู้ดูแลระบบ -->
    <div class="manage-buttons">
        <a href="new_post.php" class="manage-button">ตั้งกระทู้ใหม่</a>
    </div>

    <?php while ($post = mysqli_fetch_assoc($post_result)): ?>
        <div class="post">
            <p class="post-meta">ตั้งโดย: <?php echo htmlspecialchars($post['firstname'] . ' ' . $post['lastname']); ?></p>
            <h2 class="post-title">
                <a href="view_post.php?post_id=<?php echo $post['id']; ?>"><?php echo htmlspecialchars($post['title']); ?></a>
            </h2>
            <p class="post-content"><?php echo nl2br(htmlspecialchars($post['content'])); ?></p>
        </div>
    <?php endwhile; ?>
</div>

</body>
</html>
