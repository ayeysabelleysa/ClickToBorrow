<?php
session_start();
include("connect.php");

if (!isset($_SESSION['email'])) {
    header('Location: login.php');
    exit;
}

$user_email = $_SESSION['email'];

$sql = "SELECT * FROM borrow_requests WHERE user_email = ? ORDER BY created_at DESC";
$stmt = $con->prepare($sql);
$stmt->bind_param("s", $user_email);
$stmt->execute();
$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>History | Click to Borrow</title>
    <link rel="stylesheet" href="history.css" type="text/css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap">
</head>
<body>

    <div class="container">
        <h2>My Borrowing History</h2>
        <table>
            <tr>
                <th>Item</th>
                <th>Date</th>
                <th>Time</th>
                <th>Status</th>
                <th>Receipt</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['item']); ?></td>
                    <td><?= date("F j, Y", strtotime($row['created_at'])); ?></td>
                    <td><?= date("h:i A", strtotime($row['created_at'])); ?></td>
                    <td>
                        <span class="status <?= strtolower($row['status']); ?>">
                            <?= htmlspecialchars($row['status']); ?>
                        </span>
                    </td>
                    <td>
                        <?php if ($row['status'] != 'pending'): ?>
                            <a href="receipt.php?id=<?= $row['id']; ?>" class="receipt-btn">View Receipt</a>
                            <?php else: ?>
                                <span style="color: red; font-weight: bold;">...</span>
                                <?php endif; ?>
                            </td>

                </tr>
            <?php endwhile; ?>
        </table>
        <a href="homepage.php" class="home-btn">Back to Home</a>
    </div>

</body>
</html>

<?php $stmt->close(); ?>
