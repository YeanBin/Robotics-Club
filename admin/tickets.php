<?php
session_start();

if (!isset($_SESSION['admin'])) {
    header("location: ../login.php");
    exit();
}

$title = 'User Tickets';
$css = '../css/admin/event.css';

include('../includes/header-admin.php');
require_once('../includes/helper.php');

if (empty($_GET) && empty($_POST) || ($_SERVER["REQUEST_METHOD"] == "GET" && !isset($_GET['user']))) {
    header('location: dashboard.php');
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $user = trim($_GET['user']);

    $con = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    if ($con->connect_error) {
        die("Connection failed: " . $con->connect_error);
    }

    $user = $con->real_escape_string($user);
    $sql = "SELECT * FROM bookings INNER JOIN events ON bookings.event_id = events.id WHERE bookings.user_id = '$user'";

    $result = $con->query($sql);
}

?>

<section class="main-section">
    <div class="main-container">
        <div class="taskbar">
            <h1><?php echo $user ?>'s EVENTS</h1>
        </div>

        <div class="event-container">
            <table border="1">
                <colgroup>
                    <col style="width: 5%;">
                    <col>
                    <col>
                    <col>
                    <col>
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
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
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
                                <td colspan="5">%d record(s) found.</td>
                            </tr>',
                            $result->num_rows
                        );
                        ?>
                    </tfoot>
                <?php
                } else {
                ?>
                    <tr>
                        <td colspan="5">No records found.</td>
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