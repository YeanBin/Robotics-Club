<?php
session_start();

if (!isset($_SESSION['admin'])) {
    header("location: ../login.php");
    exit();
}

$title = 'Edit';
$css = '../css/admin/account.css';

include('../includes/header-admin.php');
require_once('../includes/helper.php');

$con = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

$result = $con->query("SELECT * FROM user WHERE id = 0");

if ($row = $result->fetch_object()) {
    $username = $row->username;
    $password = $row->password;
}
$result->free();

if (!empty($_POST)) {
    $newUser = trim($_POST['username']);
    $newPass = trim($_POST['password']);
    $confirm = trim($_POST['confirm']);

    $error['username'] = validateUsername($newUser, 0, $username);
    $error['confirm'] = validateConfirm($confirm, $password, 0);

    if (!empty($newPass)) {
        $error['password'] = validatePassword($newPass);
    }

    $error = array_filter($error);

    if (empty($error)) {
        $sql = "UPDATE user SET username = ?";

        if (!empty($newPass)) {
            $newPass = password_hash($newPass, PASSWORD_DEFAULT);
            $sql .= ", password = ?";
        }

        $sql .= " WHERE id = 0";

        $stm = $con->prepare($sql);

        if (!empty($newPass)) {
            $stm->bind_param('ss', $newUser, $newPass);
        } else {
            $stm->bind_param('s', $newUser);
        }

        if ($stm->execute()) {
            header("location: dashboard.php");
            exit();
        }
    }
} else {
    $newUser = $username;
}
$con->close();
?>

<section class="main-section">
    <div class="form-container">
        <h1>Edit Details</h1>
        <form method="post">
            <div class="input-container username <?php echo isset($error) && isset($error['username']) ? 'error' : '' ?>">
                <label for="username">Username</label>
                <input type="text" name="username" value="<?php echo $newUser ?>">

                <?php if (isset($error) && isset($error['username'])) printf('<small>%s</small>', $error['username']); ?>
            </div>

            <div class="input-container password <?php echo isset($error) && isset($error['password']) ? 'error' : '' ?>">
                <label for="password">New Password</label>
                <input type="password" name="password">

                <?php if (isset($error) && isset($error['password'])) printf('<small>%s</small>', $error['password']); ?>
            </div>

            <div class="input-container confirm <?php echo isset($error) && isset($error['confirm']) ? 'error' : '' ?>">
                <label for="confirm">Current Password</label>
                <input type="password" name="confirm">

                <?php if (isset($error) && isset($error['confirm'])) printf('<small>%s</small>', $error['confirm']); ?>
            </div>

            <div class="input-container submit">
                <input type="submit" value="Save Changes">
            </div>
        </form>
    </div>
</section>