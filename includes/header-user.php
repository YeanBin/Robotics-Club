<?php
require_once('../includes/helper.php');

$con = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

$query = "SELECT * FROM user WHERE username = '$user'";
$result = $con->query($query);

if ($row = $result->fetch_object()) {
    $avatar = $row->avatar;
}
$result->free();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="https://www.tarc.edu.my/images/tarIco.ico" />
    <title><?php echo $title ?></title>

    <!-- CSS Files -->
    <link rel="stylesheet" href="../css/style.css" />
    <link rel="stylesheet" href="../css/admin/admin.css">
    <link rel="stylesheet" href="<?php echo $css ?>" />

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body>
    <div class="sidenav">
        <div class="container">
            <div class="logo">
                <a href="dashboard.php">
                    <?php
                    if (!empty($avatar)) {
                        echo '<img src="../' . $avatar . '" alt="Avatar">';
                    } else {
                        echo '<i class="fa-solid fa-user"></i>';
                    }

                    echo $user
                    ?>
                </a>
            </div>
            <div class="links">
                <a href="../index.php">Home</a>
                <a href="notifications.php">Notifications</a>
                <a href="account.php">Account</a>
                <a href="tickets.php">Tickets</a>
            </div>
            <div class="logout">
                <a href="../logout.php" class="logout"><i class="fa-solid fa-right-from-bracket"></i>Logout</a>
            </div>
        </div>
    </div>