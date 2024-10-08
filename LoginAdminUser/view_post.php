
<?php
session_start();
include('connection.php');

if (!isset($_SESSION['userid'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['userid'];
$post_id = $_GET['post_id'];

// ตรวจสอบ userlevel ของผู้ใช้
$user_query = "SELECT userlevel FROM user WHERE id = '$user_id'";
$user_result = mysqli_query($conn, $user_query);
$user = mysqli_fetch_assoc($user_result);
$is_admin = $user['userlevel'] === 'a';

// ดึงข้อมูลโพสต์
$post_query = "
    SELECT posts.*, user.firstname, user.lastname 
    FROM posts 
    JOIN user ON posts.user_id = user.id 
    WHERE posts.id = '$post_id'
";
$post_result = mysqli_query($conn, $post_query);
$post = mysqli_fetch_assoc($post_result);
$is_post_owner = $post['user_id'] == $user_id;

// ดึงข้อมูลความคิดเห็น
$comment_query = "
    SELECT comments.*, user.firstname, user.lastname 
    FROM comments 
    JOIN user ON comments.user_id = user.id 
    WHERE comments.post_id = '$post_id'
";
$comment_result = mysqli_query($conn, $comment_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($post['title']); ?></title>
    
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
            background-color: white;
            padding: 20px;
            margin: 20px 0;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
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
        .comment {
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 10px;
            margin-top: 10px;
            background-color: #f1f1f1;
        }
        .comment-actions {
            margin-top: 5px;
            text-align: right;
        }
        .button {
            background-color: #007BFF;
            color: white;
            padding: 8px 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-align: center;
            margin-top: 10px;
        }
        .button:hover {
            background-color: #0056b3;
        }
        .edit-post, .delete-post, .edit-comment {
    display: inline-block; /* ให้แสดงเป็นปุ่ม */
    background-color: #9370DB; /* สีม่วงกลาง */
    color: white; /* สีข้อความ */
    padding: 10px 15px; /* ขนาดของปุ่ม */
    border: none; /* ไม่มีกรอบ */
    border-radius: 5px; /* มุมโค้ง */
    cursor: pointer; /* เปลี่ยนเป็น pointer เมื่อ hover */
    margin-top: 10px; /* ระยะห่างด้านบน */
    transition: background-color 0.3s; /* เอฟเฟกต์เมื่อ hover */
    }
    
    .edit-post:hover, .delete-post:hover, .edit-comment:hover {
    background-color: #8A2BE2; /* สีม่วงเข้มขึ้นเมื่อ hover */
    }
        .add-comment-form button {
    background-color: #D5006D; /* สีชมพูเข้ม */
    color: white; /* สีข้อความ */
    padding: 10px 15px; /* ขนาดของปุ่ม */
    border: none; /* ไม่มีกรอบ */
    border-radius: 5px; /* มุมโค้ง */
    cursor: pointer; /* เปลี่ยนเป็น pointer เมื่อ hover */
    transition: background-color 0.3s; /* เอฟเฟกต์เมื่อ hover */
    }

    .add-comment-form button:hover {
    background-color: #FF6F91; /* สีชมพูอ่อนเมื่อ hover */
    }

        .add-comment-form textarea {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ddd;
            border-radius: 5px;
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

    </style>
</head>
<body>

</body>
</html>


    <script>
        // ฟังก์ชันสำหรับแก้ไขโพสต์
        function toggleEditPost() {
            var editSection = document.getElementById('edit-post');
            editSection.style.display = editSection.style.display === 'none' ? 'block' : 'none';
            document.getElementById('edit-title').value = "<?php echo htmlspecialchars($post['title']); ?>";
            document.getElementById('edit-content').value = "<?php echo htmlspecialchars($post['content']); ?>";
        }

        function updatePost() {
            var title = document.getElementById('edit-title').value;
            var content = document.getElementById('edit-content').value;
            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'update_post.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onload = function() {
                if (xhr.status === 200) {
                    alert('กระทู้ถูกแก้ไขเรียบร้อยแล้ว');
                    location.reload();
                }
            };
            xhr.send('post_id=<?php echo $post_id; ?>&title=' + encodeURIComponent(title) + '&content=' + encodeURIComponent(content));
        }

// ฟังก์ชันลบโพสต์
function deletePost() {
    if (confirm("คุณแน่ใจหรือไม่ว่าต้องการลบกระทู้นี้?")) {
        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'delete_post.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onload = function() {
            if (xhr.status === 200) {
                alert('กระทู้ถูกลบเรียบร้อยแล้ว');
                <?php if ($is_admin): ?>
                    window.location.href = 'admin_page.php';
                <?php else: ?>
                    window.location.href = 'user_page.php';
                <?php endif; ?>
            }
        };
        xhr.send('post_id=<?php echo $post_id; ?>');
    }
}
        // ฟังก์ชันเพิ่มความคิดเห็น
        function addComment() {
            var content = document.getElementById('new-comment').value;
            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'add_comment.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onload = function() {
                if (xhr.status === 200) {
                    location.reload();
                }
            };
            xhr.send('post_id=<?php echo $post_id; ?>&content=' + encodeURIComponent(content));
        }

        // ฟังก์ชันแก้ไขความคิดเห็น
        function toggleEdit(comment_id) {
            var editSection = document.getElementById('edit-comment-' + comment_id);
            editSection.style.display = editSection.style.display === 'none' ? 'block' : 'none';
        }

        function updateComment(comment_id) {
            var content = document.getElementById('edit-content-' + comment_id).value;
            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'update_comment.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onload = function() {
                if (xhr.status === 200) {
                    location.reload();
                }
            };
            xhr.send('comment_id=' + comment_id + '&content=' + encodeURIComponent(content));
        }

        // ฟังก์ชันลบความคิดเห็น
        function deleteComment(comment_id) {
            if (confirm("คุณแน่ใจหรือไม่ว่าต้องการลบความคิดเห็นนี้?")) {
                var xhr = new XMLHttpRequest();
                xhr.open('POST', 'delete_comment.php', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.onload = function() {
                    if (xhr.status === 200) {
                        location.reload();
                    }
                };
                xhr.send('comment_id=' + comment_id);
            }
        }
    </script>
</head>
<body>

<div class="container">
    <h1><?php echo htmlspecialchars($post['title']); ?></h1>
    <div class="post">
        <div class="post-title"><?php echo htmlspecialchars($post['title']); ?></div>
        <div class="post-content"><?php echo nl2br(htmlspecialchars($post['content'])); ?></div>
        <div class="post-meta">
            <p>ตั้งโดย: <?php echo htmlspecialchars($post['firstname'] . ' ' . $post['lastname']); ?></p>
            <p>วันที่สร้าง: <?php echo date('d/m/Y H:i:s', strtotime($post['created_at'])); ?></p>
            <p>แก้ไขล่าสุด: <?php echo date('d/m/Y H:i:s', strtotime($post['updated_at'])); ?></p>
        </div>

        <?php if ($is_post_owner): ?>
    <div class="post-actions" style="text-align: right;">
    <a class="button" href="edit_post.php?post_id=<?php echo $post_id; ?>">แก้ไขกระทู้</a>
    <button class="button" style="background-color: #ff4d4d; color: white;" onclick="deletePost()">ลบกระทู้</button>

    </div>
<?php elseif ($is_admin): ?>
    <div class="post-actions" style="text-align: right;">
    <button class="button" style="background-color: #ff4d4d; color: white;" onclick="deletePost()">ลบกระทู้</button>

    </div>
<?php endif; ?>
    </div>

        <div class="add-comment-form">
            <h3>เพิ่มความคิดเห็น</h3>
            <textarea id="new-comment" rows="4"></textarea>
        <button class="button" onclick="addComment()">เพิ่มความคิดเห็น</button>
    </div>


    <!-- ปุ่มกลับไปยังหน้าหลัก -->
    <?php if ($is_admin): ?>
        <a href="admin_page.php" class="back-button">กลับไปยังหน้าหลัก (Admin)</a>
    <?php else: ?>
        <a href="user_page.php" class="back-button">กลับไปยังหน้าหลัก (User)</a>
    <?php endif; ?>


    <h2>ความคิดเห็น</h2>
    <?php while ($comment = mysqli_fetch_assoc($comment_result)): ?>
        <div class="comment">
            <p><strong><?php echo htmlspecialchars($comment['firstname'] . ' ' . $comment['lastname']); ?></strong> (วันที่: <?php echo date('d/m/Y H:i:s', strtotime($comment['created_at'])); ?>)</p>
            <p><?php echo nl2br(htmlspecialchars($comment['content'])); ?></p>
            <div class="comment-actions">
                <?php if ($comment['user_id'] == $user_id): // แสดงปุ่มแก้ไขและลบเฉพาะเจ้าของความคิดเห็น ?>
                    <button class="edit-post" onclick="toggleEdit(<?php echo $comment['id']; ?>)">แก้ไขความคิดเห็น</button>
                    <div id="edit-comment-<?php echo $comment['id']; ?>" class="edit-comment" style="display:none;">
                        <textarea id="edit-content-<?php echo $comment['id']; ?>" rows="2"><?php echo htmlspecialchars($comment['content']); ?></textarea>
                        <button class="button" onclick="updateComment(<?php echo $comment['id']; ?>)">บันทึกการเปลี่ยนแปลง</button>
                    </div>
                    <button class="button" style="background-color: #ff4d4d; color: white;" onclick="deleteComment(<?php echo $comment['id']; ?>)">ลบความคิดเห็น</button>
                <?php elseif ($is_admin): // แสดงปุ่มลบเฉพาะแอดมิน ?>
                    <button class="button" style="background-color: #ff4d4d; color: white;" onclick="deleteComment(<?php echo $comment['id']; ?>)">ลบความคิดเห็น</button>
                <?php endif; ?>
            </div>
        </div>
    <?php endwhile; ?>

    
    
    
</div>

</body>
</html>
