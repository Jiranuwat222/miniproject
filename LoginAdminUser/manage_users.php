<?php
session_start();
if (!isset($_SESSION['userlevel']) || $_SESSION['userlevel'] != 'a') { // เฉพาะแอดมินเท่านั้น
    header("Location: index.php");
    exit();
}

include('connection.php');

// ดึงข้อมูลผู้ใช้ทั้งหมดจากฐานข้อมูล
$user_query = "SELECT * FROM user";
$user_result = mysqli_query($conn, $user_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จัดการผู้ใช้</title>
    <style>
        /* CSS พื้นฐาน */
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #d59ede, #b577d1, #9966cc); /* ไล่ระดับสีม่วงเข้ม */
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
        }
        .container {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            max-width: 900px;
            margin: 0 auto;
        }
        h1 {
            text-align: center;
            color: #333;
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
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            color: #333;
        }
        .button {
            background-color: #0288d1;
            color: white;
            padding: 8px 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .button:hover {
            background-color: #0277bd;
        }
        .button.red {
            background-color: red;
        }
        .button.red:hover {
            background-color: darkred;
        }
        
        /* CSS สำหรับกล่องแจ้งเตือน */
        .alert-box {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: #f44336;
            color: white;
            padding: 20px;
            border-radius: 10px;
            font-size: 18px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            text-align: center;
            z-index: 9999;
            display: none; /* เริ่มต้นซ่อนกล่องแจ้งเตือน */
        }

        .alert-box button {
            background-color: white;
            color: #f44336;
            border: none;
            padding: 10px 20px;
            margin-top: 15px;
            border-radius: 5px;
            cursor: pointer;
        }

        .alert-box button:hover {
            background-color: #ccc;
        }
    </style>
</head>
<body>
<div class="container">
    <!-- ปุ่มกลับไปยังหน้า Admin Page -->
    <a href="admin_page.php" class="back-button">กลับไปยังหน้า Admin </a>  <img src="logo.png" alt="" width="150" style="margin-bottom: -72px; float: right;">


    <h1>จัดการผู้ใช้</h1>

    <!-- แสดงรายชื่อผู้ใช้ทั้งหมด -->
    <table>
        <tr>
            <th>ID</th>
            <th>ชื่อ-สกุล</th>
            <th>อีเมล</th>
            <th>บทบาท</th>
            <th>การจัดการ</th>
        </tr>
        <?php while ($user_row = mysqli_fetch_assoc($user_result)) { ?>
            <tr>
                <td><?php echo $user_row['id']; ?></td>
                <td><?php echo htmlspecialchars($user_row['firstname'] . " " . $user_row['lastname']); ?></td>
                <td><?php echo htmlspecialchars($user_row['email']); ?></td>
                <td><?php echo htmlspecialchars($user_row['userlevel'] === 'a' ? 'Admin' : 'User'); ?></td>
                <td>
                    <!-- ฟอร์มเปลี่ยนบทบาทผู้ใช้ -->
                    <form action="change_userlevel.php" method="POST" style="display: inline;">
                        <input type="hidden" name="user_id" value="<?php echo $user_row['id']; ?>">
                        <select name="userlevel">
                            <option value="a" <?php if($user_row['userlevel'] == 'a') echo 'selected'; ?>>Admin</option>
                            <option value="m" <?php if($user_row['userlevel'] == 'm') echo 'selected'; ?>>User</option>
                        </select>
                        <button class="button" type="submit">เปลี่ยนบทบาท</button>
                    </form>

                    <!-- ฟอร์มลบผู้ใช้ -->
                    <form action="delete_user.php" method="POST" style="display: inline;">
                        <input type="hidden" name="user_id" value="<?php echo $user_row['id']; ?>">
                        <button class="button red" type="button" onclick="showAlert(<?php echo $user_row['id']; ?>)">ลบ</button>
                    </form>
                </td>
            </tr>
        <?php } ?>
    </table>
</div>

<!-- กล่องแจ้งเตือน -->
<div class="alert-box" id="alertBox">
    <p>คุณต้องการลบบัญชีผู้ใช้นี้จริงหรือไม่?</p>
    <button onclick="confirmDelete()">ตกลง</button>
    <button onclick="closeAlert()">ยกเลิก</button>
</div>

<script>
    let userIdToDelete = null;

    function showAlert(userId) {
        userIdToDelete = userId; // เก็บ ID ผู้ใช้ที่จะลบ
        document.getElementById('alertBox').style.display = 'block'; // แสดงกล่องแจ้งเตือน
    }

    function closeAlert() {
        document.getElementById('alertBox').style.display = 'none'; // ซ่อนกล่องแจ้งเตือน
        userIdToDelete = null; // ล้าง ID ผู้ใช้
    }

    function confirmDelete() {
        if (userIdToDelete) {
            // สร้างฟอร์มและส่ง ID ไปยัง delete_user.php
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = 'delete_user.php';
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'user_id';
            input.value = userIdToDelete;
            form.appendChild(input);
            document.body.appendChild(form);
            form.submit(); // ส่งฟอร์ม
        }
    }
</script>

</body>
</html>

