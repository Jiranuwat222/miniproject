<?php
include('connection.php');

$post_id = $_GET['post_id'];
$comment_query = "
    SELECT comments.*, user.firstname, user.lastname 
    FROM comments 
    JOIN user ON comments.user_id = user.id 
    WHERE comments.post_id = '$post_id'
";
$comment_result = mysqli_query($conn, $comment_query);

while ($comment_row = mysqli_fetch_assoc($comment_result)) {
    ?>
    <div class="comment">
        <p>ความคิดเห็นโดย: <?php echo htmlspecialchars($comment_row['firstname'] . ' ' . $comment_row['lastname']); ?></p>
        <p><?php echo nl2br(htmlspecialchars($comment_row['content'])); ?></p>
        <div class="comment-actions">
            <p class="post-meta">วันที่: <?php echo date('d/m/Y H:i:s', strtotime($comment_row['created_at'])); ?></p>
            <button class="button" onclick="toggleEdit(<?php echo $comment_row['id']; ?>)">แก้ไขความคิดเห็น</button>
            <button class="button" style="background-color: red;" onclick="deleteComment(<?php echo $comment_row['id']; ?>)">ลบความคิดเห็น</button>
        </div>

        <!-- ฟอร์มแก้ไขความคิดเห็น -->
        <div class="edit-comment" id="edit-comment-<?php echo $comment_row['id']; ?>">
            <textarea id="edit-content-<?php echo $comment_row['id']; ?>" rows="3" required><?php echo htmlspecialchars($comment_row['content']); ?></textarea>
            <button class="button" onclick="updateComment(<?php echo $comment_row['id']; ?>)">อัปเดตความคิดเห็น</button>
        </div>
    </div>
    <?php
}
?>
