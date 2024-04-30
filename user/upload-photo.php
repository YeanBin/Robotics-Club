<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("location: ../login.php");
    exit();
}

$title = 'User Management';
$css = '../css/user/user_panel.css';
$user = $_SESSION['user'];

include('../includes/header-user.php');
require_once('../includes/helper.php');

$con = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

$query = "SELECT * FROM user WHERE username = '$user'";
$result = $con->query($query);

?>
<section class="main-section">
    <div class="main-container">
        <div class="upload">
            <header>Upload Photo</header>
            <?php if ($result && $result->num_rows > 0 && isset($row['photo_path']) && !empty($row['photo_path'])) { ?>
                <img src="<?php echo $row['photo_path']; ?>" alt="User Photo">
            <?php } ?>
        <br>
        <!-- Button to upload photo -->
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
            <label for="file">Select a photo:</label>
            <input type="file" id="file" name="file"></br>
            <input type="submit" value="Submit" name="submit"></br>
            <input type="button" value="Back" onclick="location='account.php'">
        </div>
    </div>
</section>