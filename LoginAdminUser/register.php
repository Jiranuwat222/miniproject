<?php
    session_start();
    require_once "connection.php";

    if (isset($_POST['submit'])) {

        $email = $_POST['email'];
        $password = $_POST['password'];
        $firstname = $_POST['firstname'];
        $lastname = $_POST['lastname'];

        // ตรวจสอบรูปแบบอีเมล
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    showAlert('รูปแบบอีเมลไม่ถูกต้อง');
                });
            </script>";
        } else {

            $user_check = "SELECT * FROM user WHERE email = '$email' LIMIT 1";
            $result = mysqli_query($conn, $user_check);
            $user = mysqli_fetch_assoc($result);

            if ($user && $user['email'] === $email) {
                echo "<script>
                    document.addEventListener('DOMContentLoaded', function() {
                        showAlert('อีเมลนี้มีผู้ใช้งานแล้ว');
                    });
                </script>";
            } else {
                $passwordenc = md5($password);

                $query = "INSERT INTO user (email, password, firstname, lastname, userlevel)
                            VALUE ('$email', '$passwordenc', '$firstname', '$lastname', 'm')";
                $result = mysqli_query($conn, $query);

                if ($result) {
                    $_SESSION['success'] = "เพิ่มผู้ใช้สำเร็จ";
                    header("Location: index.php");
                    exit();
                } else {
                    $_SESSION['error'] = "เกิดข้อผิดพลาดบางอย่าง";
                    header("Location: index.php");
                    exit();
                }
            }
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Page</title>

    <link rel="stylesheet" href="style.css">

    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #d59ede, #b577d1, #9966cc); /* ไล่ระดับสีม่วง */
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        form {
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            width: 400px;
            text-align: center;
        }

        label {
            display: block;
            text-align: left;
            margin-bottom: 5px;
            color: #444;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        input[type="submit"] {
            width: 100%;
            background-color: #FF4D94; /* สีชมพู */
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #FF007A; /* สีชมพูเข้ม */
        }

        /* ปรับสไตล์สำหรับลิงก์ Go back to Login */
        .back-to-login {
            display: inline-block;
            padding: 10px 20px;
            margin-top: 10px;
            border: 2px solid #FF4D94; /* กรอบสีชมพู */
            color: #FF4D94;
            text-decoration: none;
            border-radius: 5px;
            transition: 0.3s ease; /* เพิ่มการเปลี่ยนแปลงแบบนุ่มนวล */
        }

        .back-to-login:hover {
            background-color: #FF4D94; /* พื้นหลังสีชมพูเมื่อ hover */
            color: white;
        }

        a:hover {
            color: #FF007A;
        }

        .alert-box {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: #f44336; /* สีแดง */
            color: white;
            padding: 20px;
            border-radius: 10px;
            font-size: 18px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            text-align: center;
            z-index: 9999;
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

    <script>
        function showAlert(message) {
            var alertBox = document.createElement('div');
            alertBox.className = 'alert-box';
            alertBox.innerHTML = message + '<br><button onclick="closeAlert()">ตกลง</button>';
            document.body.appendChild(alertBox);
        }

        function closeAlert() {
            var alertBox = document.querySelector('.alert-box');
            if (alertBox) {
                alertBox.remove();
            }
        }

        // เรียกใช้ showAlert สำหรับแจ้งเตือนหลังจากโหลดเพจเสร็จสิ้น
        document.addEventListener('DOMContentLoaded', function() {
            <?php if (isset($_SESSION['error'])) : ?>
                showAlert('<?php echo $_SESSION['error']; ?>');
            <?php endif; ?>

            <?php if (isset($_SESSION['success'])) : ?>
                showAlert('<?php echo $_SESSION['success']; ?>');
            <?php endif; ?>
        });
    </script>

</head>
<body>

    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
        <img src="logo.png" alt="" width="150" style="margin-bottom: -60px;" > 
        <label for="firstname">Firstname:</label>
        <input type="text" name="firstname" placeholder="Enter your firstname" required>
        <br>
        <label for="lastname">Lastname:</label>
        <input type="text" name="lastname" placeholder="Enter your lastname" required>
        <br>        
        <label for="email">Email:</label>
        <input type="text" name="email" placeholder="Enter your email" required>
        <br>
        <label for="password">Password:</label>
        <input type="password" name="password" placeholder="Enter your password" required>
        <br>

        <input type="submit" name="submit" value="Submit">

        <!-- ปุ่มกลับไปหน้า Login อยู่ใต้ปุ่ม Submit -->
        <a href="index.php" class="back-to-login">Go back to Login</a>

    </form>

</body>
</html>

<?php 
    if (isset($_SESSION['success']) || isset($_SESSION['error'])) {
        session_destroy();
    }
?>
