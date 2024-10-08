<?php
session_start();
if (!isset($_SESSION['userid']) || !isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}

include('connection.php');

$post_id = $_GET['post_id']; // รับ post_id จาก URL

// ตรวจสอบว่า post_id มีค่า
if (!$post_id) {
    echo "ไม่พบโพสต์";
    exit();
}

// ดึงข้อมูลโพสต์
$post_query = "SELECT * FROM posts WHERE id = '$post_id'";
$post_result = mysqli_query($conn, $post_query);
$post = mysqli_fetch_assoc($post_result);

// ตรวจสอบว่าโพสต์มีอยู่ในฐานข้อมูล
if (!$post) {
    echo "โพสต์ไม่พบ";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แก้ไขกระทู้</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #d59ede, #b577d1, #9966cc); /* ไล่ระดับสีม่วงเข้ม */
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: auto;
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            color: #444;
        }
        input[type="text"],
        textarea {
            width: 100%;
            padding: 10px;
            margin-top: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            resize: vertical;
            min-height: 150px;
        }
        button {
            display: block;
            width: 100%;
            background-color: #FF4D94; /* สีชมพูเข้ม */
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 10px;
        }
        button:hover {
            background-color: #FF007A; /* สีชมพูเข้มขึ้นเมื่อ hover */
        }
        .back-button {
            display: block;
            text-align: center;
            margin-top: 20px;
            text-decoration: none;
            color: #FF4D94; /* สีชมพูเข้ม */
        }
        .back-button:hover {
            color: #FF007A; /* สีชมพูเข้มขึ้นเมื่อ hover */
        }
        /* Modal styles */
        #confirmationModal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.4);
            padding-top: 60px;
        }
        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 400px;
            border-radius: 10px;
            text-align: center;
        }
        .modal-content button {
            background-color: #FF4D94;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .modal-content button:hover {
            background-color: #FF007A;
        }
    </style>
    <script>
        function updatePost(event) {
            event.preventDefault();

            var postId = document.getElementsByName('post_id')[0].value;
            var title = document.getElementsByName('title')[0].value;
            var content = document.getElementsByName('content')[0].value;

            var modal = document.getElementById("confirmationModal");
            modal.style.display = "block";

            document.getElementById("confirmBtn").onclick = function() {
                var xhr = new XMLHttpRequest();
                xhr.open('POST', 'update_post.php', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.onload = function() {
                    if (xhr.status === 200) {
                        var response = JSON.parse(xhr.responseText);
                        if (response.status === 'success') {
                            window.location.href = "view_post.php?post_id=" + response.post_id;
                        } else {
                            alert('เกิดข้อผิดพลาดในการอัปเดตกระทู้: ' + response.message);
                        }
                    } else {
                        alert('เกิดข้อผิดพลาดในการอัปเดตกระทู้');
                    }
                };
                xhr.send('post_id=' + postId + '&title=' + encodeURIComponent(title) + '&content=' + encodeURIComponent(content));
                modal.style.display = "none";
            };

            document.getElementById("cancelBtn").onclick = function() {
                modal.style.display = "none";
            };
        }
    </script>
</head>
<body>

<div class="container">
    <h1>แก้ไขกระทู้</h1>
    <form onsubmit="updatePost(event)">
        <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
        <input type="text" name="title" value="<?php echo htmlspecialchars($post['title']); ?>" required placeholder="หัวข้อโพสต์">
        <textarea name="content" required><?php echo htmlspecialchars($post['content']); ?></textarea>
        <button type="submit">บันทึกการแก้ไข</button>
    </form>
    <a href="view_post.php?post_id=<?php echo $post['id']; ?>" class="back-button">กลับไปยังกระทู้</a>
</div>

<div id="confirmationModal">
    <div class="modal-content">
        <h2>ยืนยันการแก้ไข</h2>
        <p>คุณแน่ใจว่าต้องการบันทึกการแก้ไขกระทู้นี้หรือไม่?</p>
        <button id="confirmBtn">ยืนยัน</button>
        <button id="cancelBtn">ยกเลิก</button>
    </div>
</div>

</body>
</html>
