<?php 
    session_start();

    if (isset($_POST['email'])) {
        include('connection.php');

        $email = $_POST['email'];
        $password = $_POST['password'];
        $passwordenc = md5($password);

        // ตรวจสอบว่ามีผู้ใช้ที่มีอีเมลนี้หรือไม่
        $query = "SELECT * FROM user WHERE email = '$email'";
        $result = mysqli_query($conn, $query);

        if (mysqli_num_rows($result) == 1) {
            // หากพบอีเมล ตรวจสอบรหัสผ่าน
            $row = mysqli_fetch_array($result);

            if ($row['password'] === $passwordenc) {
                // รหัสผ่านถูกต้อง
                $_SESSION['userid'] = $row['id'];
                $_SESSION['user'] = $row['firstname'] . " " . $row['lastname'];
                $_SESSION['userlevel'] = $row['userlevel'];

                if ($_SESSION['userlevel'] == 'a') {
                    header("Location: admin_page.php");
                    exit();
                } elseif ($_SESSION['userlevel'] == 'm') {
                    header("Location: user_page.php");
                    exit();
                }
            } else {
                // รหัสผ่านไม่ถูกต้อง
                $_SESSION['error'] = 'รหัสผ่านไม่ถูกต้อง';
                header("Location: index.php");
                exit();
            }
        } else {
            // ไม่พบอีเมลในระบบ
            $_SESSION['error'] = 'ไม่พบบัญชีผู้ใช้ที่มีอีเมลนี้';
            header("Location: index.php");
            exit();
        }
    } else {
        header("Location: index.php");
        exit();
    }
?>
