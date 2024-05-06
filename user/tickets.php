<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("location: ../login.php");
    exit();
}

$title = 'My Tickets';
$css = '../css/admin/event.css';
$user = $_SESSION['user'];

include('..\includes\header-user.php');
require_once('..\includes\helper.php');

$con = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

$user_id = $_SESSION['user'];
$sql_get_bookings = "SELECT bookings.booking_id, events.title, events.date, events.type, events.price
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

<section class="main-section">
    <div class="main-container">
        <div class="taskbar">
            <h1>BOOKED EVENTS</h1>
        </div>

        <div class="event-container">
            <table border="1">
                <colgroup>
                    <col style="width: 3%;">
                    <col>
                    <col style="width: 20%;">
                    <col style="width: 12%;">
                    <col style="width: 10%;">
                </colgroup>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Date</th>
                        <th>Type</th>
                        <th>Price</th>
                    </tr>
                </thead>

                <?php
                if ($result_get_bookings->num_rows > 0) {
                    while ($row = $result_get_bookings->fetch_assoc()) {
                        printf(
                            '<tr>
                                <td>%s</td>
                                <td>%s</td>
                                <td>%s</td>
                                <td>%s</td>
                                <td>%s</td>
                            </tr>',
                            htmlspecialchars($row["booking_id"]),
                            htmlspecialchars($row["title"]),
                            date("d-M-Y", strtotime($row["date"])),
                            $row["type"],
                            $row["price"]
                        );
                    }
                ?>
                    <tfoot>
                        <?php
                        printf(
                            '<tr>
                                <td colspan="6">%d record(s) found.</td>
                            </tr>',
                            $result_get_bookings->num_rows
                        );
                        ?>
                    </tfoot>
                <?php
                } else {
                ?>
                    <tr>
                        <td colspan="6">You have not booked any events.</td>
                    </tr>
                <?php
                }
                ?>
            </table>
        </div>
    </div>
</section>
</body>
</html>
