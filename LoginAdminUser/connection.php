<?php
    $conn = mysqli_connect("localhost", "root", "", "loginadminuser");
    if (!$conn) {
        die("Failed to connect to database" . mysqli_error($conn));
    }
?>