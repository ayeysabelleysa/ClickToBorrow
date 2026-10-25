<?php
session_start();
include("connect.php");

if (!isset($_SESSION['email'])) {
    header('Location: login.php');
    exit;
}

$user_email = $_SESSION['email'];

if (isset($_GET['id'])) {
    $borrow_id = $_GET['id'];
    $sql = "SELECT * FROM borrow_requests WHERE id = ? AND user_email = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("is", $borrow_id, $user_email);
    $stmt->execute();
    $result = $stmt->get_result();
    $borrow = $result->fetch_assoc();
    $stmt->close();

    
} else {
    header("Location: history.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt | Click to Borrow</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap">
    <style>
    body {
        font-family: 'Poppins', sans-serif;
        text-align: center;
        min-height: 100vh;
        margin: 0;
        padding: 0;
        background: linear-gradient(white, #3655b3);
    }
    .container {
        background: white;
        width: 60%;
        margin: auto;
        margin-top: 50px;
        padding: 20px;
        border-radius: 12px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }
    h2 {
        color: #3655b3;
    }
    .success {
        font-size: 1.2rem;
        font-weight: bold;
        color: green;
    }
    .home-btn {
        display: inline-block;
        margin-top: 20px;
        padding: 12px 20px;
        background-color: #3655b3;
        color: white;
        font-weight: 600;
        text-decoration: none;
        border-radius: 8px;
    }
    .home-btn:hover {
        background-color: black;
    }
    </style>
</head>
<body>

    <div class="container">
        <h2>Borrow Receipt</h2>
        <p><strong>Item:</strong> <?= htmlspecialchars($borrow['item']); ?></p>
        <p><strong>Date: </strong><?= date("F j, Y", strtotime($borrow['created_at'])); ?></p>
        <p><strong>Time: </strong><?= date("h:i A", strtotime($borrow['created_at'])); ?></p>

        <p class="success">Borrow request approved.</p>
        <a href="history.php" class="home-btn">Back to History</a>
    </div>

</body>
</html>
