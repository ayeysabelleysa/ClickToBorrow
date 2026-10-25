<?php
session_start();
include("connect.php");

if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit;
}

$user_email = $_SESSION['email'];

$sql = "SELECT email FROM users WHERE email = ?";
$stmt = $con->prepare($sql);
$stmt->bind_param("s", $user_email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="logo.png">
    <title>Profile</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            text-align: center;
            background: linear-gradient(white, #DAA520);
            margin: 0;
            padding: 0;
            min-height: 100vh;
        }
        .container {
            background: white;
            width: 40%;
            margin: 100px auto;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        .profile-pic {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
        }
        .home-btn {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 15px;
            background-color: #DAA520;
            color: white;
            text-decoration: none;
            border-radius: 8px;
        }
        .logout-btn {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 15px;
            background-color: #3655b3;
            color: white;
            text-decoration: none;
            border-radius: 10px;
        }
    </style>
</head>
<body>

    <div class="container">
        <img src="profile.png" class="profile-pic" alt="Profile Picture">
        <p>💗</p>
        <p><strong>Email:</strong> <?= htmlspecialchars($user_email); ?></p>
        <a href="homepage.php" class="home-btn">Back to home</a>
        <br>
        <a href="login.php" class="logout-btn">Log out</a>

    </div>

</body>
</html>
