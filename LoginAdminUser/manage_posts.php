<?php
session_start();

// ตรวจสอบว่า session มีการ login อยู่หรือไม่
if (!isset($_SESSION['userid']) || !isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}

include('connection.php');

// การดึงโพสต์ทั้งหมด
$post_query = "SELECT posts.*, user.firstname, user.lastname FROM posts JOIN user ON posts.user_id = user.id";
$post_result = mysqli_query($conn, $post_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จัดการกระทู้</title>
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
        .delete-button {
            background-color: #dc3545; /* สีแดง */
            color: white;
            padding: 8px 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .delete-button:hover {
            background-color: #c82333; /* สีแดงเข้มเมื่อ hover */
        }
        .back-button {
    display: inline-block; /* เปลี่ยนจาก block เป็น inline-block */
    text-align: center;
    background-color: #4b0082; /* สีม่วงเข้มน้ำเงิน */
    color: white;
    padding: 10px 20px; /* ลดความยาว padding ด้านข้างลง */
    border-radius: 5px;
    text-decoration: none;
    font-weight: bold;
    margin-top: 20px; /* เพิ่มระยะห่างด้านบน */
    transition: background-color 0.3s;
    }
    .back-button:hover {
    background-color: #3e0073; /* สีม่วงเข้มน้ำเงินเข้มเมื่อ hover */
    }
        /* โมดัล */
        .modal {
            display: none; /* ซ่อนโมดัลเริ่มต้น */
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.4); /* พื้นหลังมืด */
            justify-content: center;
            align-items: center;
        }
        .modal-content {
            background-color: #fefefe;
            margin: auto;
            padding: 20px;
            border: 1px solid #888;
            width: 300px;
            border-radius: 10px;
            text-align: center;
        }
        .modal-button {
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin: 5px;
            font-size: 16px;
        }
        .modal-button.confirm-button {
            background-color: #007BFF; /* สีน้ำเงิน */
            color: white;
        }
        .modal-button.confirm-button:hover {
            background-color: #0056b3; /* สีน้ำเงินเข้มเมื่อ hover */
        }
    </style>
    <script>
        function showModal(postId) {
            document.getElementById("deleteModal").style.display = "flex";
            document.getElementById("postIdToDelete").value = postId; // เก็บ post_id ใน hidden input
        }

        function hideModal() {
            document.getElementById("deleteModal").style.display = "none";
        }

        function confirmDelete() {
            const postId = document.getElementById("postIdToDelete").value; // ดึง post_id
            const formData = new FormData();
            formData.append('post_id', postId); // เพิ่ม post_id ไปยัง FormData

            // ทำการส่งคำขอ AJAX ไปยัง delete_post_v2.php
            fetch('delete_post_v2.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                const resultModal = document.getElementById("resultModal");
                const resultMessage = document.getElementById("resultMessage");

                if (data.success) {
                    resultMessage.innerText = data.message; // แสดงข้อความสำเร็จ
                } else {
                    resultMessage.innerText = data.message; // แสดงข้อความผิดพลาด
                }
                resultModal.style.display = "flex"; // แสดงผลลัพธ์
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }

        function hideResultModal() {
            document.getElementById("resultModal").style.display = "none";
            location.reload(); // รีเฟรชหน้าเมื่อปิดโมดัล
        }
    </script>
</head>
<body>

<div class="container">
    <h1>จัดการกระทู้</h1>
    
    <a href="admin_page.php" class="back-button">กลับไปยังหน้า Admin </a> <!-- เพิ่มปุ่มกลับไปยังหน้า Admin Page -->

    <?php while ($post = mysqli_fetch_assoc($post_result)): ?>
        <div class="post">
            <p class="post-meta">ตั้งโดย: <?php echo htmlspecialchars($post['firstname'] . ' ' . $post['lastname']); ?></p>
            <h2 class="post-title">
                <a href="view_post.php?post_id=<?php echo $post['id']; ?>"><?php echo htmlspecialchars($post['title']); ?></a>
            </h2>
            <p class="post-content"><?php echo nl2br(htmlspecialchars($post['content'])); ?></p>
            <button class="delete-button" onclick="showModal(<?php echo $post['id']; ?>)">ลบกระทู้</button>
        </div>
    <?php endwhile; ?>
</div>

<!-- โมดัลสำหรับยืนยันการลบ -->
<div id="deleteModal" class="modal">
    <div class="modal-content">
        <h2>ยืนยัน</h2>
        <p>คุณแน่ใจหรือไม่ว่าต้องการลบโพสต์นี้?</p>
        <input type="hidden" name="post_id" id="postIdToDelete" value="">
        <button class="modal-button confirm-button" onclick="confirmDelete()">ยืนยัน</button>
        <button class="modal-button cancel-button" onclick="hideModal()">ยกเลิก</button>
    </div>
</div>

<!-- โมดัลสำหรับแสดงผลลัพธ์การลบ -->
<div id="resultModal" class="modal">
    <div class="modal-content" style="text-align: center;">
        <h2>สำเร็จ</h2>
        <p id="resultMessage"></p>
        <button class="modal-button confirm-button" onclick="hideResultModal()">ปิด</button>
    </div>
</div>

</body>
</html>
