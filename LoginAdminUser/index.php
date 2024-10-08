<?php 
    session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Login Page</title>
    
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

        .container {
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            width: 400px;
            text-align: center;
            position: relative;
        }

        h1 {
            color: #444;
            margin-bottom: 20px;
        }

        label {
            display: block;
            text-align: left;
            color: #444;
            margin-bottom: 5px;
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

        a {
            display: block;
            margin-top: 20px;
            color: #FF4D94;
            text-decoration: none;
        }

        a:hover {
            color: #FF007A;
        }

        .success, .error {
            margin-bottom: 20px;
            padding: 10px;
            border-radius: 5px;
            position: absolute;
            top: 10px;
            left: 50%;
            transform: translateX(-50%);
            width: 80%;
            text-align: center;
        }

        .success {
            background-color: #d4edda;
            color: #155724;
        }

        .error {
            background-color: #f8d7da;
            color: #721c24;
        }
        
    </style>
</head>
<body>

    <div class="container">
        <!-- เพิ่มโลโก้ที่นี่ -->
        <img src="logo.png" alt="" width="150" style="margin-bottom: -70px;" > 
        <h1>Login</h1>

        <?php if (isset($_SESSION['success'])) : ?>
            <div class="success">
                <?php echo $_SESSION['success']; ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])) : ?>
            <div class="error">
                <?php echo $_SESSION['error']; ?>
            </div>
        <?php endif; ?>

        <form action="login.php" method="post">
            <label for="email">Email</label>
            <input type="text" name="email" placeholder="Email" required>
            
            <label for="password">Password</label>
            <input type="password" name="password" placeholder="Password" required>
            
            <input type="submit" name="submit" value="Login">
        </form>

        <a href="register.php">Go to register</a>
    </div>

</body>
</html>

<?php 
    if (isset($_SESSION['success']) || isset($_SESSION['error'])) {
        session_destroy();
    }
?>
