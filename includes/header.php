<?php
session_start();

if (isset($_SESSION['user'])) {
	$user = $_SESSION['user'];

	require_once('includes/helper.php');

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
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<link rel="icon" href="https://www.tarc.edu.my/images/tarIco.ico" />
	<title><?php echo $title ?></title>

	<!-- CSS Files -->
	<link rel="stylesheet" href="css/style.css" />
	<link rel="stylesheet" href="css/website/navbar.css" />
	<link rel="stylesheet" href="<?php echo $css ?>" />
	<link rel="stylesheet" href="<?php echo $css1 ?>" />

	<!-- Font Awesome -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body>

	<div class="navbar">
		<div class="container">
			<div class="logo">
				<a href="index.php">
					<img src="assets/tarc.png" alt="image" />
					<h1>
						TAR UMT <br />
						ROBOTICS CLUB
					</h1>
				</a>
			</div>
			<div class="links">
				<a href="event.php">Events</a>
				<a href="news.php">News</a>
				<a href="about-us.php">About Us</a>
				<a href="login.php">
					<?php
					if (!empty($avatar)) {
						echo '<img src="' . $avatar . '" alt="Avatar">';
					} else {
						echo '<i class="fa-solid fa-user"></i>';
					}
					?>
				</a>
			</div>
		</div>
	</div>