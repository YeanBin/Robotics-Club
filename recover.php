<?php
$title = 'Login';
$css = 'css/website/login.css';

include('includes/header.php');
require_once('includes/helper.php');

if (isset($_SESSION['user'])) {
    header("location: user/dashboard.php");
    exit();
} else if (isset($_SESSION['admin'])) {
    header("location: admin/dashboard.php");
    exit();
}

$isSuccess = false;

$con = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

if (!empty($_POST)) {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm = trim($_POST['confirm']);

    $error['password'] = validatePassword($password);
    $error['confirm'] = validateConfirm($password, $confirm, 1);

    $error = array_filter($error);

    if (empty($error)) {
        $username  = $con->real_escape_string($username);
        $email = $con->real_escape_string($email);

        $sql = "SELECT * FROM user WHERE username = '$username' AND email = '$email' AND admin = 0";

        if ($result = $con->query($sql)) {
            if ($result->num_rows > 0) {
                $password = password_hash($password, PASSWORD_DEFAULT);

                $updateSql = "UPDATE user SET password = ? WHERE username = ? AND email = ?";

                $stm = $con->prepare($updateSql);

                $stm->bind_param('sss', $password, $username, $email);

                if ($stm->execute()) {
                    $_POST = array();
                    $username = $email = $password = $confirm = null;

                    $isSuccess = true;
                    echo "<script> setTimeout(function() {
                            popupToggle();
                        }, 300) </script> ";
                }
            } else {
                $error['username'] = $error['email'] = 'Invalid username or email';
            }
            $stm->close();
        }
    }
    $con->close();
} else {
    $username = '';
    $email = '';
}
?>

<section class="account-section">
    <div class="forms-container">
        <div class="recover-form">
            <header>Account Recovery</header>

            <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post" class="signup">
                <div class="form-input <?php echo isset($error) && isset($error['username']) ? 'error' : (!empty($_POST) && !isset($error['username']) ? 'success' : '') ?>">
                    <label for="username">Username</label>
                    <span class="fa-solid fa-user"></span>
                    <input type="text" name="username" id="username" value="<?php echo $username ?>" placeholder="Enter username" />

                    <i class="fa-solid fa-circle-check"></i>
                    <i class="fa-solid fa-circle-exclamation"></i>
                    <?php if (isset($error) && isset($error['username'])) printf('<small>%s</small>', $error['username']); ?>
                </div>

                <div class="form-input <?php echo isset($error) && isset($error['email']) ? 'error' : (!empty($_POST) && !isset($error['email']) ? 'success' : '') ?>">
                    <label for="email">Email</label>
                    <span class="fa-solid fa-at"></span>
                    <input type="text" name="email" id="email" value="<?php echo $email ?>" placeholder="Enter e-mail" />

                    <i class="fa-solid fa-circle-check"></i>
                    <i class="fa-solid fa-circle-exclamation"></i>
                    <?php if (isset($error) && isset($error['email'])) printf('<small>%s</small>', $error['email']); ?>
                </div>

                <div class="form-input <?php echo isset($error) && isset($error['password']) ? 'error' : (!empty($_POST) && !isset($error['password']) ? 'success' : '') ?>">
                    <label for="password">New Password</label>
                    <span class="fa-solid fa-key"></span>
                    <input type="password" name="password" id="password" placeholder="Enter new password" />

                    <i class="fa-solid fa-circle-check"></i>
                    <i class="fa-solid fa-circle-exclamation"></i>
                    <?php if (isset($error) && isset($error['password'])) printf('<small>%s</small>', $error['password']); ?>
                </div>

                <div class="form-input <?php echo isset($error) && isset($error['confirm']) ? 'error' : (!empty($_POST) && !isset($error['confirm']) ? 'success' : '') ?>">
                    <label for="confirm">Confirm Password</label>
                    <span class="fa-solid fa-key"></span>
                    <input type="password" name="confirm" id="confirm" placeholder="Confirm new password" />

                    <i class="fa-solid fa-circle-check"></i>
                    <i class="fa-solid fa-circle-exclamation"></i>
                    <?php if (isset($error) && isset($error['confirm'])) printf('<small>%s</small>', $error['confirm']); ?>
                </div>

                <div class="form-link">
                    <a href="login.php" class="forgot-link">Login</a>
                </div>

                <div class="form-button">
                    <input type="submit" class="button" value="Update" />
                    <input type="button" class="button" value="Reset" onclick="location='<?php echo $_SERVER["PHP_SELF"] ?>'">
                </div>
            </form>
        </div>
    </div>
</section>

<div class="popup" style="display: none;">
    <div class="card <?php echo $isSuccess ? "success" : "error" ?>">
        <i class="fa-solid fa-circle-check" style="display: none;"></i>
        <i class="fa-solid fa-circle-xmark" style="display: none;"></i>

        <h2><?php echo $isSuccess ? "Success!" : "Error!" ?></h2>
        <p><?php echo $isSuccess ? "Your password have been successfully updated!" : "Something went wrong, please try again!" ?></p>

        <a href=<?php echo $isSuccess ? "login.php" : "recover.php" ?> class=" popup-button"><?php echo $isSuccess ? "Login" : "Try Again" ?></a>
    </div>
</div>
<script src="js/script.js"></script>
</body>

</html>