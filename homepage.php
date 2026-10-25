<?php
session_start();
include("connect.php");

if (!isset($_SESSION['email'])) {
    header('Location: login.php');
    exit;
}

$user_email = $_SESSION['email'];

$check_user_sql = "SELECT email FROM users WHERE email = ?";
$stmt = $con->prepare($check_user_sql);
$stmt->bind_param("s", $user_email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 0) {
    header('Location: login.php');
    exit();
}

$borrowed_items = [];
$sql = "SELECT item FROM borrow_requests WHERE user_email = ?";
$stmt = $con->prepare($sql);
$stmt->bind_param("s", $user_email);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $borrowed_items[] = $row['item'];
}
$stmt->close();

$overdue_items = [];
$sql = "SELECT item, created_at FROM borrow_requests 
        WHERE user_email = ? AND status = 'overdue'";
$stmt = $con->prepare($sql);
$stmt->bind_param("s", $user_email);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $overdue_items[] = $row;
}
$stmt->close();

$message = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $item = htmlspecialchars(trim($_POST['item']));
    $date = htmlspecialchars(trim($_POST['date']));

    if (in_array($item, $borrowed_items)) {
        $message = "You have already borrowed this item.";
    } else {
        $sql = "INSERT INTO borrow_requests (item, date, user_email, status) VALUES (?, ?, ?, 'pending')";
        $stmt = $con->prepare($sql);
        $stmt->bind_param("sss", $item, $date, $user_email);

        if ($stmt->execute()) {
            header('Location: receipt.php');
            exit;
        } else {
            $message = "Error: " . $stmt->error;
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Click to Borrow | Home</title>
    <link rel="stylesheet" href="homepage.css" type="text/css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap">
</head>
<body>

    <nav class="navbar">
            <img src="logo.png" class="logo" style="height: 130px;">
        <div class="nav-links">
            <a href="login.php">Log in</a>
            <a href="register.php" class="register">Register</a>
        </div>
    </nav>

    <nav class="navbar">
        <div class="second-nav-links">
            <a href="homepage.php" class="homee">Home</a>
            <a href="history.php">My Borrowings</a>
        </div>

        <div class="icon-container">
            <div class="icon" onclick="toggleNotification()">
                <img src="notif.png" alt="Notification">
                <span id="notif-badge" class="notification-badge" style="display: <?= count($overdue_items) > 0 ? 'inline' : 'none' ?>;">
                    <?= count($overdue_items) ?>
                </span>
            </div>

            <div id="notification-popup" class="notification-popup">
                <?php if (count($overdue_items) > 0): ?>
                    <?php foreach ($overdue_items as $item): ?>
            <div class="notif-card">
                <div class="notif-content">
                    <h3><?= htmlspecialchars($item['item']); ?></h3>
                    <p style="color: red;">Please return the item immediately!</p>
                    <p class="notif-date"><strong>Date:</strong> <?= date("m/d/Y", strtotime($item['created_at'])); ?></p>
                    <p class="notif-time"><strong>Time:</strong> <?= date("h:i A", strtotime($item['created_at'])); ?></p>
                </div>
                <span class="close-btn" onclick="closeNotification()">&times;</span>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p style="text-align: center;">No overdue items.</p>
    <?php endif; ?>
</div>


            <a href="profile.php"><img src="profile.png" alt="Profile" class="profile-pic"></a>
        </div>
    </nav>

    <br>
    <h2 style="text-align: center; color: #3655b3;">Select the Item you need to borrow.</h2>

    <div class="cr">
        <form class="borrow-form" method="POST">
            <label for="item">Select Item:</label>
            <select name="item" id="item" onchange="checkItemBorrowed()" required>
                <option disabled selected>👆</option>
                <option value="hdmi">HDMI Cable</option>
                <option value="tv">TV</option>
                <option value="projector">Projector</option>
                <option value="remote">Remote</option>
            </select>

            <label>Date:</label>
            <input type="date" name="date" required>

            <p id="message" class="message"><?= $message; ?></p>
            <button type="submit" id="borrow-btn" class="borrow-btn">Click to Borrow</button>
        </form>
    </div>
    <br><br><br><br>

    <script>
        function checkItemBorrowed() {
            var borrowedItems = <?= json_encode($borrowed_items); ?>;
            var selectedItem = document.getElementById("item").value;
            var messageDiv = document.getElementById("message");
            var borrowBtn = document.getElementById("borrow-btn");

            if (borrowedItems.includes(selectedItem)) {
                messageDiv.textContent = "You have already borrowed this item.";
                borrowBtn.classList.add("disabled");
                borrowBtn.disabled = true;
            } else {
                messageDiv.textContent = "";
                borrowBtn.classList.remove("disabled");
                borrowBtn.disabled = false;
            }
        }

        function toggleNotification() {
            var notifPopup = document.getElementById("notification-popup");
            var badge = document.getElementById("notif-badge");

            if (notifPopup.style.display === "none" || notifPopup.style.display === "") {
                notifPopup.style.display = "block";
                badge.style.display = "none"; 
            } else {
                notifPopup.style.display = "none";
            }
        }

        function closeNotification() {
            document.getElementById("notification-popup").style.display = "none";
        }

        document.addEventListener("click", function(event) {
            var notifPopup = document.getElementById("notification-popup");
            var notifIcon = document.querySelector(".icon");

            if (!notifPopup.contains(event.target) && !notifIcon.contains(event.target)) {
                notifPopup.style.display = "none";
            }
        });
    </script>

</body>
</html>
