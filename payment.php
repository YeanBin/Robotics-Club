<?php
$title = 'Payment';
$css = 'css/website/payment.css';

include('includes/header.php');
require_once('includes/helper.php');

$isSuccess = false;

$con = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($con->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (empty($_POST)) {
    header("location: event.php");
}

if (!isset($_SESSION['user'])) {
    header("location: login.php?redirect=event.php");
    exit();
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

    $tax = $price * 0.06;
    $subtotal = $price - $tax;
}
$result->free();

if (isset($_POST['confirm'])) {
    $card = trim($_POST['card']);
    $name = trim($_POST['name']);
    $expiry = trim($_POST['expiry']);
    $cvv = trim($_POST['cvv']);
    $bookID = generateID("B", 5);

    $error['name'] = isEmpty($name);
    $error['expiry'] = isEmpty($expiry);
    $error['cvv'] = isEmpty($cvv);

    $error = array_filter($error);

    if (empty($error)) {
        $insertSql = 'INSERT INTO bookings (booking_id, event_id, user_id, price) VALUES (?, ?, ?, ?)';

        $insertStm = $con->prepare($insertSql);

        $insertStm->bind_param('sisd', $bookID, $eventID, $user, $price);

        $insertStm->execute();

        if ($insertStm->affected_rows > 0) {
            $updateSql = "UPDATE events SET seats = seats - 1 WHERE id = ?";

            $updateStm = $con->prepare($updateSql);

            $updateStm->bind_param('i', $eventID);

            $updateStm->execute();

            if ($updateStm->affected_rows > 0) {
                $_POST = array();
                $card = $name = $expiry = $cvv = null;

                $isSuccess = true;
                echo "<script> setTimeout(function() {
                    popupToggle();
                }, 300) </script> ";
            }
            $updateStm->close();
        }
        $insertStm->close();
    }
} else {
    $card = '';
    $name = '';
    $expiry = '';
    $cvv = '';
}

$con->close();
?>

<section class="payment-section">
    <div class="main-container">
        <div class="form-container">
            <h1>Checkout</h1>
            <h3>Card Details:</h3>
            <form method="post" id="payment-form">
                <input type="hidden" name="event" value="<?php echo $eventID; ?>">

                <div class="form-input ccard">
                    <label for="card">Card Number</label>
                    <input type="text" id="card" name="card" maxlength="19" value="<?php echo $card ?>" placeholder="1234 5678 9012 3456">
                </div>

                <div class="form-input name <?php echo isset($error) && isset($error['name']) ? 'error' : '' ?>">
                    <label for="name">Name</label>
                    <input type="text" id="name" name="name" maxlength="30" value="<?php echo $name ?>" placeholder="John Doe">

                    <?php if (isset($error) && isset($error['name'])) printf('<small>%s</small>', $error['name']); ?>
                </div>

                <div class="form-input expiry <?php echo isset($error) && isset($error['expiry']) ? 'error' : '' ?>">
                    <label for="expiry">Exp. Date</label>
                    <input type="text" id="expiry" name="expiry" maxlength="5" value="<?php echo $expiry ?>" placeholder="MM/YY">

                    <?php if (isset($error) && isset($error['expiry'])) printf('<small>%s</small>', $error['expiry']); ?>
                </div>

                <div class="form-input cvv <?php echo isset($error) && isset($error['cvv']) ? 'error' : '' ?>">
                    <label for="cvv">CVV</label>
                    <input type="text" id="cvv" name="cvv" maxlength="3" value="<?php echo $cvv ?>" placeholder="123">

                    <?php if (isset($error) && isset($error['cvv'])) printf('<small>%s</small>', $error['cvv']); ?>
                </div>
            </form>
        </div>

        <div class="details-container">
            <h1>Event Summary</h1>
            <div class="event-card">
                <h3><?php echo htmlspecialchars($title); ?></h3>
                <p><?php echo date("d-M-Y", strtotime($date)); ?> &#8226; <?php echo $type; ?> &#8226; <?php echo $seats; ?> seats left</p>
                <p><?php echo nl2br($content) ?></p>
            </div>

            <div class="price">
                <div class="subtotal">
                    <p>Subtotal</p>
                    <p>RM <?php echo number_format($subtotal, 2) ?></p>
                </div>
                <div class="tax">
                    <p>Tax (6%)</p>
                    <p>RM <?php echo number_format($tax, 2) ?></p>
                </div>
                <hr>
                <div class="total">
                    <p>Total</p>
                    <p>RM <?php echo $price ?></p>
                </div>
            </div>

            <div class="form-button">
                <button type="submit" form="payment-form" name="confirm">Confirm Payment</button>
                <button name="cancel" onclick="location='event.php'">Cancel</button>
            </div>
        </div>
    </div>
</section>

<div class="popup" style="display: none;">
    <div class="card <?php echo $isSuccess ? "success" : "error" ?>">
        <i class="fa-solid fa-circle-check" style="display: none;"></i>
        <i class="fa-solid fa-circle-xmark" style="display: none;"></i>

        <h2><?php echo $isSuccess ? "Success!" : "Error!" ?></h2>
        <p><?php echo $isSuccess ? "Your ticket has been successfully booked!" : "Something went wrong, please try again!" ?></p>

        <a href=<?php echo $isSuccess ? "user/tickets.php" : "event.php" ?> class=" popup-button"><?php echo $isSuccess ? "View Tickets" : "Try Again" ?></a>
    </div>
</div>

<script src="js/script.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var cardInput = document.getElementById('card');
        var expiryInput = document.getElementById('expiry');
        var cvvInput = document.getElementById('cvv');

        allowOnlyNumbers(cardInput);
        allowOnlyNumbers(cvvInput);

        cardInput.addEventListener('input', function() {
            var trimmedValue = this.value.replace(/\s/g, '');
            var formattedValue = trimmedValue.replace(/(\d{4})(?=\d)/g, '$1 ');
            this.value = formattedValue;
        });

        expiryInput.addEventListener('input', function() {
            var sanitizedValue = this.value.replace(/\D/g, '');
            var formattedValue = sanitizedValue.replace(/^(\d{0,2})(\d{0,2})/, function(match, p1, p2) {
                var month = p1;
                var year = p2.length > 0 ? '/' + p2 : '';
                return month + year;
            });
            this.value = formattedValue;
        });
    });
</script>
</body>

</html>