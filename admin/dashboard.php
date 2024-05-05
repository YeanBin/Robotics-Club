<?php
session_start();

if (!isset($_SESSION['admin'])) {
    header("location: ../login.php");
    exit();
}

$title = 'Admin Dashboard';

include('../includes/header-admin.php');
?>

<!-- content -->
<section class="main-section">
    <div class="main-container dashboard">
        <h2>Welcome <b><?php echo $_SESSION['admin'] ?></b>, what would you like to do?</h2>

        <a href="events.php"><i class="fa-regular fa-calendar-days"></i>Manage Events</a>
        <a href="news.php"><i class="fa-solid fa-newspaper"></i> Manage News</a>
        <a href="users.php"><i class="fa-solid fa-users"></i>Manage Users</a>
        <a href="notification.php"><i class="fa-solid fa-bell"></i>Manage Notification</a>
    </div>
</section>

</body>

</html>