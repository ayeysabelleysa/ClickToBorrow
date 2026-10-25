<?php
session_start();
include("connect.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['nu_account']) && isset($_POST['password'])) {
        $email = trim($_POST['nu_account']);
        $password = trim($_POST['password']);

        // NU email verify 
        if (!preg_match('/^[a-zA-Z0-9._%+-]+@(students\.)?nu-fairview\.edu\.ph$/', $email)) {
            echo "<script>alert('Invalid NU Fairview email.'); window.history.back();</script>";
            exit();
        }

        // ITSO admin login requires a password configured outside source control.
        if (strpos($email, "@nu-fairview.edu.ph") !== false) {
            $admin_password = getenv("CLICKTOBORROW_ADMIN_PASSWORD");
            if ($admin_password !== false && $admin_password !== "" && hash_equals($admin_password, $password)) {
                $_SESSION['email'] = $email;
                $_SESSION['role'] = "admin";

            //  Admin nu email into database
            $sql_admin = "INSERT IGNORE INTO admin (email) VALUES (?)";
            $stmt_admin = $con->prepare($sql_admin);
            $stmt_admin->bind_param("s", $email);
            $stmt_admin->execute();
            $stmt_admin->close();

                echo "<script>alert('Login successful!');</script>";
                echo "<script>window.location.href='admin.php';</script>";
                exit();
            } else {
                echo "<script>alert('Login failed!'); window.history.back();</script>";
                exit();
            }
        }

        // Student login (database)
        $sql = "SELECT * FROM users WHERE email = ?";
        $stmt = $con->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $hashed_password = $row['password'];

            if (password_verify($password, $hashed_password)) {
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['email'] = $row['email'];
                $_SESSION['role'] = "student";

                echo "<script>alert('Login successful!');</script>";
                echo "<script>window.location.href='homepage.php';</script>";
                exit();
            } else {
                echo "<script>alert('Incorrect password!'); window.history.back();</script>";
            }
        } else {
            echo "<script>alert('Email not found! Please register first.'); window.history.back();</script>";
        }
        $stmt->close();
    } else {
        echo "<script>alert('Please enter both email and password!'); window.history.back();</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Click to Borrow - Login</title>
    <link rel="stylesheet" href="login.css" type="text/css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap">
</head>
<body>
    <div class="container">
        <div class="card">
            <h2>Welcome Back to</h2>
            <img src="clicktoborrow2.png" class="logo" style="height: 130px;" alt="Click to Borrow system logo">
            <p class="subtitle">"Fast, Easy, Reliable – Borrow IT Equipment with a Click!"</p>
            
            <form action="login.php" method="POST">
                <div class="input-group">
                    <label for="nu_account"><b>NU Account*</b></label>
                    <input type="email" id="nu_account" name="nu_account" placeholder="Enter your NU email" required>
                </div>
                <div class="input-group">
                    <label for="password"><b>Password*</b></label>
                    <input type="password" id="password" name="password" placeholder="Enter password" required>
                </div>

                <div class="show-password-container">
                    <input type="checkbox" id="show-password" class="show-password" onclick="togglePasswordVisibility()">
                    <label for="show-password">Show Password</label>
                </div>
                <br>
                
                <button type="submit" class="btn">Login</button>
            </form>

            <p class="register-text">Don't have an account? 
                <a href="register.php" class="register-link" style="text-decoration: underline;">
                    <b>Sign up</b>
                </a>
            </p>
        </div>
    </div>

    <script>
        function togglePasswordVisibility() {
            var password = document.getElementById("password");
            var showPasswordCheckbox = document.getElementById("show-password");

            if (showPasswordCheckbox.checked) {
                password.type = "text";
            } else {
                password.type = "password";
            }
        }
    </script>
</body>
</html>
