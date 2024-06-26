<?php
session_start();

if (isset($_SESSION['user'])) {
    header("location: user/dashboard.php");
    exit();
} else if (isset($_SESSION['admin'])) {
    header("location: admin/dashboard.php");
    exit();
}

$title = 'Admin Login';
$css = 'css/website/login.css';

include('includes/header.php');
require_once('includes/helper.php');
?>

<!-- Account Section -->
<section class="account-section">
    <div class="forms-container">
        <!-- Login Form -->
        <div class="login-form">
            <header>Admin Login</header>

            <?php
            if (!empty($_POST)) {
                $username = trim($_POST['username']);
                $password = trim($_POST['password']);

                if ($username == ADMIN_USER && $password == ADMIN_PASS) {
                    $_SESSION['admin'] = $username;

                    $username = $password = null;

                    header("location: admin/dashboard.php");
                    exit();
                } else {
                    $error['username'] = $error['password'] = 'Invalid username or password';
                }
            } else {
                $username = '';
                $password = '';
            }
            ?>

            <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post" class="login">
                <div class="form-input <?php echo isset($error) && isset($error['username']) ? 'error' : (!empty($_POST) && !isset($error['username']) ? 'success' : '') ?> ">
                    <label for="username">Username</label>
                    <span class="fa-solid fa-user"></span>
                    <input type="text" name="username" id="username" value="<?php echo $username ?>" placeholder="Enter username" />

                    <i class="fa-solid fa-circle-check"></i>
                    <i class="fa-solid fa-circle-exclamation"></i>
                    <?php if (isset($error) && isset($error['username'])) printf('<small>%s</small>', $error['username']); ?>
                </div>

                <div class="form-input <?php echo isset($error) && isset($error['password']) ? 'error' : '' ?> ">
                    <label for="password">Password</label>
                    <span class="fa-solid fa-key"></span>
                    <input type="password" name="password" id="password" value="<?php echo $password ?>" placeholder="Enter password" />

                    <i class="fa-solid fa-circle-exclamation"></i>
                    <?php if (isset($error) && isset($error['password'])) printf('<small>%s</small>', $error['password']); ?>
                </div>

                <div class="form-button">
                    <input type="submit" name="submit" value="Login" />
                    <input type="button" class="button" value="Reset" onclick="location='<?php echo $_SERVER["PHP_SELF"] ?>'">
                </div>
            </form>
        </div>
    </div>
</section>
</body>

</html>