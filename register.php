<?php
include("connect.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['psw']);
    $confirm_password = trim($_POST['psw-repeat']);
    $school = trim($_POST['school']);

    // NU Fairview student email
    if (!preg_match('/^[a-zA-Z0-9._%+-]+@students\.nu-fairview\.edu\.ph$/', $email)) {
        echo "<script>alert('Only NU Fairview student emails are allowed!'); window.history.back();</script>";
        exit();
    }

    if ($password !== $confirm_password) {
        echo "<script>alert('Passwords do not match!'); window.history.back();</script>";
        exit();
    }

    // check if email is already registered
    $sqlCheck = "SELECT * FROM users WHERE email = ?";
    $stmtCheck = $con->prepare($sqlCheck);
    $stmtCheck->bind_param("s", $email);
    $stmtCheck->execute();
    $result = $stmtCheck->get_result();

    if ($result->num_rows > 0) {
        echo "<script>alert('Email is already registered! Please login instead.'); window.location.href='login.php';</script>";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // insert user data into database
        $sqlInsert = "INSERT INTO users (email, password, school) VALUES (?, ?, ?)";
        $stmtInsert = $con->prepare($sqlInsert);
        $stmtInsert->bind_param("sss", $email, $hashed_password, $school);

        if ($stmtInsert->execute()) {
            echo "<script>alert('Account registered successfully!'); window.location.href='login.php';</script>";
        } else {
            echo "<script>alert('Registration failed. Please try again.'); window.history.back();</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | Click to Borrow</title>
    <link rel="stylesheet" href="register.css" type="text/css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap">
</head>
<body>
    <div class="container">
        <br><br>
        <div class="register-card">
            <form action="register.php" method="POST">
                <br>
            <img src="logo.png" class="logo" style="height: 130px;" alt="logo">
                <p style="font-size: 13px; font-style: italic; color: #777;">Create your account.</p>

                <div class="input-group">

                <label>What School are you on:*</label>
                <br>
                    <select name="school" id="school" onchange="checkItemBorrowed()" required>
                    <option value="" disabled selected></option>
                        <option value="set">School of Engineering and Technology (SET)</option>
                        <option value="sas">School of Arts and Sciences (SAS)</option>
                        <option value="sthm">School of Tourism and Hospitality Management (STHM)</option>
                        <option value="sba">School of Business and Accountancy (SBA)</option>
                        <option value="soa">School of Architecture (SOA)</option>
                    </select>
                
                </div>

                <div class="input-group">
                    
                    <label for="email"><b>NU Email*</b></label>
                    <input type="email" placeholder="Enter Your NU email" name="email" required>
                </div>

                <div class="input-group">
                    <label for="password"><b>Password*</b></label>
                    <input type="password" id="password" placeholder="Enter Password" name="psw" required>
                </div>

                <div class="show-password-container">
                    <input type="checkbox" id="show-password" class="show-password" onclick="togglePasswordVisibility()">
                    <label for="show-password">Show Password</label>
                </div>
                <br>

                <div class="input-group">
                    <label for="confirm-password"><b>Confirm Password*</b></label>
                    <input type="password" id="confirm-password" placeholder="Repeat Password" name="psw-repeat" required>
                    <button type="submit" class="btn">Register</button>

                </div>
                <p class="login-text">
                    Already have an account? <a href="login.php" class="backlogin"><b style="text-decoration: underline;">Login</b></a>
                </p>
            </form>
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
