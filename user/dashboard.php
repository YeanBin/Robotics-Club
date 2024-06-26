<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("location: ../login.php");
    exit();
}

$title = 'User Dashboard';
$user = $_SESSION['user'];

include('../includes/header-user.php');
?>

<!-- content -->
<section class="main-section">
    <div class="main-container dashboard">
        <h2>Welcome <b><?php echo $user ?></b>, what would you like to do?</h2>

        <a href="notifications.php"><i class="fa-regular fa-envelope"></i>View Notifications</a>
        <a href="account.php"><i class="fa-solid fa-user"></i>My Account</a>
        <a href="tickets.php"><i class="fa-solid fa-ticket"></i>View Tickets</a>
    </div>
</section>

</body>

</html>