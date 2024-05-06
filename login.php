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
?>

<!-- Account Section -->
<section class="account-section">
	<div class="forms-container">
		<!-- Login Form -->
		<div class="login-form">
			<header>Login</header>

			<?php
			if (!empty($_POST)) {
				$username = trim($_POST['username']);
				$password = trim($_POST['password']);

				$con = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

				$username  = $con->real_escape_string($username);
				$sql = "SELECT username, password FROM user WHERE username = ? AND admin = 0";

				$stm = $con->prepare($sql);

				$stm->bind_param('s', $username);

				$stm->execute();
				$stm->store_result();

				if ($stm->num_rows > 0) {
					$stm->bind_result($username, $hashed_password);

					$stm->fetch();

					if (password_verify($password, $hashed_password)) {
						$_SESSION['user'] = $username;

						$username = $password = null;

						if (isset($_GET['redirect'])) {
							header("location: " . $_GET['redirect']);
						} else {
							header("location: user/dashboard.php");
						}
						exit();
					} else {
						$error['username'] = $error['password'] = 'Invalid username or password';
					}
				} else {
					$error['username'] = $error['password'] = 'Invalid username or password';
				}

				$stm->close();
				$con->close();

				$error = array_filter($error);
			} else {
				$username = '';
				$password = '';
			}
			?>

			<form method="post" class="login">
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

				<div class="form-link">
					<a href="#" class="forgot-link">Forgot password?</a>
				</div>

				<div class="form-button">
					<input type="submit" name="submit" value="Login" />
					<input type="button" class="button" value="Reset" onclick="location='<?php echo $_SERVER["PHP_SELF"] ?>'">
				</div>
			</form>

			<div class="form-link">
				<span>Don't have an account? <a href="register.php">Signup</a></span>
			</div>
		</div>
	</div>
</section>
</body>

</html>