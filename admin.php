<?php
session_start();
include("connect.php");

if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php'); 
    exit;
}

$sql = "SELECT * FROM borrow_requests ORDER BY created_at DESC";
$result = $con->query($sql);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $request_id = $_POST['request_id']; 

    if (isset($_POST['approve'])) {
        $new_status = 'borrowed'; 
    } elseif (isset($_POST['mark_returned'])) {
        $new_status = 'returned'; 
    } elseif (isset($_POST['mark_overdue'])) {
        $new_status = 'overdue'; 
    }

    if (isset($new_status)) {
        $update_sql = "UPDATE borrow_requests SET status = ? WHERE id = ?";
        $stmt = $con->prepare($update_sql);
        $stmt->bind_param("si", $new_status, $request_id);
        $stmt->execute();
        $stmt->close();
    }

    header("Location: admin.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="logo.png">
    <title>Admin | Click to Borrow</title>
    <link rel="stylesheet" href="admin.css" type="text/css">

    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap">
</head>
<body>

    <div class="container">
        <h2>Borrow Requests</h2>
        <table>
            <tr>
                <th>Item</th>
                <th>User</th>
                <th>Date</th>
                <th>Time</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['item']); ?></td> 
                    <td><?= htmlspecialchars($row['user_email']); ?></td> 
                    <td><?= date("m/d/Y", strtotime($row['created_at'])); ?></td> 
                    <td><?= date("h:i A", strtotime($row['created_at'])); ?></td> 
                    <td><?= htmlspecialchars($row['status']); ?></td> 
                    <td>
                        <form method="post">
                            <input type="hidden" name="request_id" value="<?= $row['id']; ?>"> 
                            
                            <button type="submit" name="approve" class="action-btn approve">Approve</button>
                            <button type="submit" name="mark_returned" class="action-btn return">Mark as Returned</button>
                            <button type="submit" name="mark_overdue" class="action-btn overdue">Mark as Overdue</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
        <a href="login.php" class="logout-btn">Logout</a> 
    </div>

</body>
</html>
