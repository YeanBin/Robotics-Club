<?php
session_start();

$title = 'Payment';
$css = 'css/website/payment.css';

include('includes/header.php');
require_once('includes/helper.php');

$hasResult = true;

if (isset($_POST['event'])) {
    if (!isset($_SESSION['user'])) {
        header("location: login.php?redirect=event.php");
        exit();
    }

    $con = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $eventID = $_POST['event'];

    $sql = "SELECT * FROM events WHERE id = $eventID";
    $result = $con->query($sql);

    if ($row = $result->fetch_object()) {
        $id = $row->id;
        $title = $row->title;
        $date = $row->date;
        $type = $row->type;
        $seats = $row->seats;
        $price = $row->price;
        $content = $row->content;
    } else {
        $hasResult = false;
    }
    $result->free();
    $con->close();
}
?>

<section class="main-section">
    <?php
    if ($hasResult) {
    ?>
        <div class="form-container">
            <h2><?php echo htmlspecialchars($title); ?></h2>
            <p>Date: <?php echo date("d-M-Y", strtotime($date)); ?></p>
            <p>Type: <?php echo $type; ?></p>
            <p>Remaining Seats: <?php echo $seats; ?></p>
            <p><?php echo nl2br($content) ?></p>

            <p>Card Details:</p>
            <form method="post" action="payment.php">
                <input type="hidden" name="event_id" value="<?php echo $event_id; ?>">

                <input type="text" id="cardNo" name="cardNo" placeholder="Card number">
                <input type="text" id="expiration" name="expiration" placeholder="Expiration date (MM/YY)">
                <input type="text" id="cvv" name="cvv" placeholder="CVV">
                <input type="text" id="name" name="name" placeholder="Name">

                <button type="submit" name="confirm_payment">Confirm Payment</button>
            </form>
        </div>
    <?php
    } else {
        echo
        '<div class="error-container" style="width: 20vw">
            <h1>Oops, Record not found.</h1>

            <input type="button" value="Back to list" onclick="location=\'' . $table . '.php\'">
        </div>';
    }
    ?>
</section>

</body>

</html>