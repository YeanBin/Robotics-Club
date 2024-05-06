<?php
$title = 'Register';
$css = 'css/website/login.css';

include('includes/header.php');
require_once('includes/helper.php');

global $isSuccess;
?>

<!-- Account Section -->
<section class="account-section">
    <div class="forms-container">
        <!-- Signup Form -->
        <div class="signup-form">
            <header>Signup</header>

            <?php
            if (!empty($_POST)) {
                $username = trim($_POST['username']);
                $email = trim($_POST['email']);
                $password = trim($_POST['password']);
                $confirm = trim($_POST['confirm']);

                $error['username'] = validateUsername($username, 1);
                $error['email'] = validateEmail($email, 1);
                $error['password'] = validatePassword($password);
                $error['confirm'] = validateConfirm($password, $confirm, 1);

                $error = array_filter($error);

                if (empty($error)) {
                    $con = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

                    $sql = 'INSERT INTO user (username, email, password) VALUES (?, ?, ?)';

                    $stm = $con->prepare($sql);

                    $password = password_hash($password, PASSWORD_DEFAULT);

                    $stm->bind_param('sss', $username, $email, $password);

                    $stm->execute();

                    if ($stm->affected_rows > 0) {
                        $_POST = array();
                        $username = $email = $password = $confirm = null;

                        $isSuccess = true;
                        echo "<script> setTimeout(function() {
                            popupToggle();
                        }, 300) </script> ";
                    } else {
                        $isSuccess = false;
                    }

                    $stm->close();
                    $con->close();
                }
            } else {
                $username = '';
                $email = '';
                $password = '';
                $confirm = '';
            }
            ?>

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
                    <label for="password">Password</label>
                    <span class="fa-solid fa-key"></span>
                    <input type="password" name="password" id="password" value="<?php echo $password ?>" placeholder="Create password" />

                    <i class="fa-solid fa-circle-check"></i>
                    <i class="fa-solid fa-circle-exclamation"></i>
                    <?php if (isset($error) && isset($error['password'])) printf('<small>%s</small>', $error['password']); ?>
                </div>

                <div class="form-input <?php echo isset($error) && isset($error['confirm']) ? 'error' : (!empty($_POST) && !isset($error['confirm']) ? 'success' : '') ?>">
                    <label for="confirm">Confirm Password</label>
                    <span class="fa-solid fa-key"></span>
                    <input type="password" name="confirm" id="confirm" value="<?php echo $confirm ?>" placeholder="Confirm password" />

                    <i class="fa-solid fa-circle-check"></i>
                    <i class="fa-solid fa-circle-exclamation"></i>
                    <?php if (isset($error) && isset($error['confirm'])) printf('<small>%s</small>', $error['confirm']); ?>
                </div>

                <div class="form-button">
                    <input type="submit" class="button" value="Signup" />
                    <input type="button" class="button" value="Reset" onclick="location='<?php echo $_SERVER["PHP_SELF"] ?>'">
                </div>
            </form>

            <div class="form-link">
                <span>Already have an account? <a href="login.php">Login</a></span>
            </div>
        </div>
    </div>
</section>

<div class="popup" style="display: none;">
    <div class="card <?php echo $isSuccess ? "success" : "error" ?>">
        <i class="fa-solid fa-circle-check" style="display: none;"></i>
        <i class="fa-solid fa-circle-xmark" style="display: none;"></i>

        <h2><?php echo $isSuccess ? "Success!" : "Error!" ?></h2>
        <p><?php echo $isSuccess ? "You have been successfully registered!" : "Something went wrong, please try again!" ?></p>

        <a href=<?php echo $isSuccess ? "login.php" : "register.php" ?> class=" popup-button"><?php echo $isSuccess ? "Login" : "Try Again" ?></a>
    </div>
</div>
<script src="js/script.js"></script>
</body>

</html>