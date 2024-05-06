<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("location: ../login.php");
    exit();
}

$title = 'My Tickets';
$user = $_SESSION['user'];

include('../includes/header-user.php');

$con = new mysqli("localhost", "root", "", "robotic-club");
if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

$user_id = $_SESSION['user']['username'];
$sql_get_bookings = "SELECT events.title, events.date, events.type, events.seats, events.content 
                     FROM bookings 
                     INNER JOIN events ON bookings.event_id = events.id 
                     WHERE bookings.user_id = ?";
$stmt_get_bookings = $con->prepare($sql_get_bookings);
$stmt_get_bookings->bind_param('s', $user_id);
$stmt_get_bookings->execute();
$result_get_bookings = $stmt_get_bookings->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?></title>
    <link rel="stylesheet" href="css/website/payment.css">
    <script src="js/script.js"></script>
</head>
<body>

<section class="ticket-section">
    <div class="container-ticket">
        <?php
        if ($result_get_bookings->num_rows > 0) {
            while ($row = $result_get_bookings->fetch_assoc()) {
                ?>
                <h2><?php echo htmlspecialchars($row["title"]); ?></h2>
                <p>Date: <?php echo date("d-M-Y", strtotime($row["date"])); ?></p>
                <p>Type: <?php echo $row["type"]; ?></p>
                <p>Remaining Seats: <?php echo $row["seats"]; ?></p>
                <p>Description: <?php echo nl2br($row["content"]); ?></p>
                <br>
                <?php
            }
        } else {
            echo "You have not booked any events.";
        }
        $stmt_get_bookings->close();
        ?>
    </div>
</section>

</body>
</html>
