<?php
session_start();

$title = 'Events';
$css = 'css/website/event.css';

include('includes/header.php');
require_once('includes/helper.php');

$con = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

$sql = "SELECT * FROM events";

if (isset($_GET['search'])) {
    $keyword = trim($_GET['search']);

    $type = $con->real_escape_string($keyword);

    $sql .= " WHERE title LIKE '%$keyword%' OR content LIKE '%$keyword%'";
}

$checked = [];
if (isset($_GET['type']) && isset($_GET['seats'])) {
    $checked = $_GET['type'];

    foreach ($checked as $args) {
        $type_checked[] = "type = '" . $args . "'";
    }

    $type_str = implode(' OR ', $type_checked);

    $checked = $_GET['seats'];

    foreach ($checked as $args) {
        $seats_checked[] = "seats " . $SEATS[$args];
    }

    $seats_str = implode(' OR ', $seats_checked);

    $checked = array_merge($_GET['type'], $_GET['seats']);

    $sql .= " WHERE (" . $type_str . ") AND (" . $seats_str . ")";
} else if (isset($_GET['type'])) {
    $checked = $_GET['type'];

    foreach ($checked as $args) {
        $type_checked[] = "type = '" . $args . "'";
    }

    $type_str = implode(' OR ', $type_checked);

    $sql .= " WHERE " . $type_str;
} else if (isset($_GET['seats'])) {
    $checked = $_GET['seats'];

    foreach ($checked as $args) {
        $seats_checked[] = "seats " . $SEATS[$args];
    }

    $seats_str = implode(' OR ', $seats_checked);

    $sql .= " WHERE " . $seats_str;
}

$result = $con->query($sql);

?>
<div class="sidebar">
    <div class="container">
        <div class="search-bar">
            <form action="" method="get">
                <input type="text" name="search" id="search" placeholder="Search">
                <input style="font-family: FontAwesome" value="&#xf002;" type="submit">
            </form>
        </div>
        <div class="filters">
            <form action="" method="get">
                <h3>Category</h3>

                <div class="checkbox meetup">
                    <input type="checkbox" name="type[]" id="meetup" value="Meetup" <?php if (in_array('Meetup', $checked)) echo "checked" ?>>
                    <label for=" meetup">Meetup</label>
                </div>
                <div class="checkbox workshop">
                    <input type="checkbox" name="type[]" id="workshop" value="Workshop" <?php if (in_array('Workshop', $checked)) echo "checked" ?>>
                    <label for=" workshop">Workshop</label>
                </div>
                <div class="checkbox competition">
                    <input type="checkbox" name="type[]" id="competition" value="Competition" <?php if (in_array('Competition', $checked)) echo "checked" ?>>
                    <label for=" competition">Competition</label>
                </div>

                <hr>

                <h3>Availability</h3>

                <div class="checkbox available">
                    <input type="checkbox" name="seats[]" id="available" value="available" <?php if (in_array('available', $checked)) echo "checked" ?>>
                    <label for=" available">Available</label>
                </div>
                <div class="checkbox sold-out">
                    <input type="checkbox" name="seats[]" id="sold-out" value="sold-out" <?php if (in_array('sold-out', $checked)) echo "checked" ?>>
                    <label for=" sold-out">Sold Out</label>
                </div>

                <input type="submit" value="Filter" class="submit-btn">
            </form>
        </div>

        <div class="reset-btn">
            <a href="event.php">Reset</a>
        </div>
    </div>
</div>

<section class="event-section">
    <div class="event-container">
        <h1 class="section-title"><?php echo $title; ?></h1>

        <?php
        if ($result->num_rows > 0) {
        ?>
            <div class='event-items'>
                <?php
                while ($row = $result->fetch_assoc()) {
                    $buttonText = $row["seats"] > 0 ? "Sign Up" : "Sold Out";

                    $user = $_SESSION["user"];
                    $eventID = $row["id"];
                    $checkSql = "SELECT * FROM bookings WHERE event_id = $eventID AND user_id = '$user'";

                    $checkResult = $con->query($checkSql);
                    if ($checkResult->num_rows > 0) {
                        $buttonText = "Signed Up";
                        $disabled = "disabled";
                    } else {
                        $disabled = $buttonText == 'Sold Out' ? 'disabled' : '';
                    }


                    echo "
                    <div class='event-card'>
                        <div class='row title'>
                            <h1>" . htmlspecialchars($row["title"])  . " </h1>
                        </div>
                        <div class='row details'>
                            <strong>
                                <p>" . date("d-M-Y", strtotime($row["date"])) . " &#8226; " . $row['type'] . " &#8226; " . $row['seats'] . " seats remaining</p>
                            </strong>
                        </div>
                        <div class='row content'>
                            <p>" . nl2br($row["content"]) . "</p>
                            <form action='payment.php' method='post'>
                                <input type='hidden' name='event' id='event' value=" . $eventID . ">
                                <input type='submit' value='" . $buttonText . "' " . $disabled . " >
                            </form>
                        </div>
                    </div>";
                }
                ?>
            </div>
        <?php
        } else {
            echo "<div class='no-results'>No results found!</div>";
        }
        ?>

    </div>
</section>
</body>

</html>

<?php
$result->free();
$con->close();
?>