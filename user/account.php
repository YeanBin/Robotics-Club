<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("location: ../login.php");
    exit();
}

$title = 'User Profile';
$css = '../css/user/account.css';
$user = $_SESSION['user'];

include('../includes/header-user.php');
require_once('../includes/helper.php');

$con = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

$query = "SELECT * FROM user WHERE username = '$user'";
$result = $con->query($query);

if ($row = $result->fetch_object()) {
    $avatar = $row->avatar;
    $username = $row->username;
    $email = $row->email;
    $password = $row->password;
    $phoneNo = $row->phoneNo;
    $gender = $row->gender;
}
$result->free();

if (!empty($_POST)) {
    $username = $_POST['username'];
    $newEmail = trim($_POST['email']);
    $newPass = trim($_POST['password']);
    $confirm = trim($_POST['confirm']);
    $phoneNo = trim($_POST['phone']);
    $gender = trim($_POST['gender']);

    $error['email'] = validateEmail($email, 0, $newEmail);
    $error['confirm'] = validateConfirm($confirm, $password, 0);

    if (!empty($newPass)) {
        $error['password'] = validatePassword($newPass);
    }

    $error = array_filter($error);

    if (empty($error)) {
        $sql = "UPDATE user SET email = ?, phoneNo = ?, gender = ?";

        if (!empty($newPass)) {
            $newPass = password_hash($newPass, PASSWORD_DEFAULT);
            $sql .= ", password = ?";
        }

        $sql .= " WHERE username = ?";

        $stm = $con->prepare($sql);

        if (!empty($newPass)) {
            $stm->bind_param('sssss', $newEmail, $phoneNo, $gender, $newPass, $username);
        } else {
            $stm->bind_param('ssss', $newEmail, $phoneNo, $gender, $username);
        }

        $stm->execute();
    }

    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['avatar'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        if ($file['size'] > 1048576) {
            $error['avatar'] = 'File is more than 1MB in size';
        } else if ($ext != 'jpg' && $ext != 'jpeg' && $ext != 'gif' && $ext != 'png') {
            $error['avatar'] = 'Only JPG, GIF and PNG are allowed.';
        }

        if (empty($error)) {
            if (!empty($row->avatar) && file_exists('../' . $row->avatar)) {
                unlink('../' . $row->avatar);
            }

            $dir = 'uploads/';
            $avatar = $dir . uniqid() . '.' . $ext;

            if (move_uploaded_file($file['tmp_name'], '../' . $avatar)) {
                $sql = "UPDATE user SET avatar = ? WHERE username = ?";

                $stm = $con->prepare($sql);
                $stm->bind_param('ss', $avatar, $username);

                $stm->execute();

                header("Location: {$_SERVER['PHP_SELF']}");
                exit();
            }
        }
    }
}

$con->close();
?>

<section class="main-section">
    <div class="main-container">
        <form method="post" enctype="multipart/form-data">
            <div class="avatar">
                <div class="image">
                    <?php
                    if (!empty($avatar)) {
                        echo '<img src="../' . $avatar . '" alt="Avatar">';
                    } else {
                        echo '<i class="fa-solid fa-user"></i>';
                    }
                    ?>
                </div>

                <div class="avatar-input <?php echo isset($error) && isset($error['avatar']) ? 'error' : '' ?>">
                    <input type="hidden" name="MAX_FILE_SIZE" value="1048576" />
                    <input type="file" name="avatar">

                    <?php if (isset($error) && isset($error['avatar'])) printf('<small>%s</small>', $error['password']); ?>
                </div>

                <p><?php echo $username ?></p>
            </div>

            <div class="details">
                <h1>User Information</h1>
                <hr>
                <div class="details-input">
                    <input type="hidden" name="username" value="<?php echo $username ?>">

                    <div class="input-container email <?php echo isset($error) && isset($error['email']) ? 'error' : '' ?>">
                        <label for="email">Email</label>
                        <input type="text" name="email" value="<?php echo $email ?>" maxlength=35>

                        <?php if (isset($error) && isset($error['email'])) printf('<small>%s</small>', $error['email']); ?>
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

                    <div class="input-container phone">
                        <label for="phone">Phone No.</label>
                        <input type="tel" name="phone" value="<?php echo $phoneNo ?>" maxlength="11">
                    </div>

                    <div class="input-container gender">
                        <label for="gender">Gender</label>
                        <select name="gender">
                            <option value="" <?php echo empty($gender) ? 'selected' : '' ?>>Select</option>
                            <option value="Male" <?php echo $gender === 'Male' ? 'selected' : '' ?>>Male</option>
                            <option value="Female" <?php echo $gender === 'Female' ? 'selected' : '' ?>>Female</option>
                        </select>
                    </div>

                    <div class="form-button">
                        <button type="submit">Save Changes</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>

</body>

</html>