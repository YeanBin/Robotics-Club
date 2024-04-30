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
        <div class="user-info-table">
            <header>My details</header></br>
            <table class="info">
                <h3>Username :</h3>
                <?php
                if ($result && $result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    echo "<tr>";
                    echo "<td>" . $row['username'] . "</td>";
                    echo "</tr>";
                } else {
                    echo "<tr><td colspan='3'>No user information available.</td></tr>";
                }
                ?>
            </table>
            </br>
            </br>
            <header>E-mail address</header>
            </br>
            <table class="info">
                <h3>Email :</h3>
                <?php
                if ($result && $result->num_rows > 0) {
                    echo "<tr>";
                    echo "<td>" . $row['email'] . "</td>";
                    echo "</tr>";
                } else {
                    echo "<tr><td colspan='3'>No user information available.</td></tr>";
                }
                ?>
            </table>
                        <!-- Display the uploaded photo -->
                        <?php if ($result && $result->num_rows > 0 && isset($row['photo']) && !empty($row['photo'])) { ?>
                <img src="<?php echo $row['photo']; ?>" alt="User Photo">
            <?php } ?>
        </div>
        </br>
        <input type="button" value="Edit" onclick="location='edit.php'">
        <input type="button" value="Upload" onclick="location='upload-photo.php'">
    </div>
    <div style="float: left;">
        <?php
            if (isset($_GET['image']))
            {
                $image = $_GET['image'];
                printf('<img src="uploads/%s" width="150" alt="" style="border: 1px solid gray;"/>', $image);
            
                // Form for deletion.
                printf('
                <form action="%s" method="post">
                    <input type="hidden" name="image" value="%s" />
                    <input type="submit" name="delete" value="Delete" />
                </form>', $_SERVER['PHP_SELF'] ,$image);
                
            }
        ?>
    
</section>