<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'robotic-club');

// register.php
function validateUsername($username, $isRegister, $currUsername = null)
{
    if ($username == null) {
        return 'Field cannot be blank';
    } else if (strlen($username) < 3 || strlen($username) > 30) {
        return 'Username must be between 3 to 30 characters long.';
    } else if (!preg_match('/^[a-zA-Z0-9_-]+$/', $username)) {
        return 'Username must contain only letters, numbers, dashes and underscore.';
    } else if (!$isRegister && $currUsername != null && $username == $currUsername) {
        return;
    } else if (($isRegister && isUserExist($username, 'username')) || (!$isRegister && $username != $currUsername && isUserExist($username, 'username'))) {
        return 'Username has been taken.';
    }
}

function validateEmail($email, $isRegister, $currEmail = null)
{
    if ($email == null) {
        return 'Field cannot be blank.';
    } else if (!preg_match('/^[\w.-]+@[\w.-]+\.[A-Za-z]{2,6}$/', $email)) {
        return 'Invalid e-mail address';
    } else if (!$isRegister && $currEmail != null && $email == $currEmail) {
        return;
    } else if (isUserExist($email, 'email')) {
        return 'An account with this e-mail already exist.';
    }
}

function validatePassword($password)
{
    if ($password == null) {
        return 'Field cannot be blank.';
    } else if (strlen($password) < 8 || strlen($password) > 17) {
        return 'Password must be between 8 to 16 characters long.';
    } else if (!preg_match('/^[a-zA-Z0-9!@#$%^&*]+$/', $password)) {
        return 'Password must contain only letters, numbers and symbols.';
    }
}

function validateConfirm($password, $confirm, $isRegister)
{
    if ($confirm == null) {
        return 'Field cannot be blank.';
    } else if (($isRegister && $confirm != $password) || (!$isRegister && !password_verify($password, $confirm))) {
        return 'Passwords does not match';
    }
}

function isUserExist($value, $column)
{
    $exist = false;

    $con = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    $value  = $con->real_escape_string($value);
    $sql = "SELECT * FROM user WHERE $column = '$value'";

    if ($result = $con->query($sql)) {
        if ($result->num_rows > 0) {
            $exist = true;
        }
    }

    $result->free();
    $con->close();

    return $exist;
}

// event.php
function getSeats()
{
    return array(
        'available' => '> 0',
        'sold-out' => '= 0'
    );
}

$SEATS = getSeats();

//add.php
function isEmpty($value)
{
    if ($value == null) {
        return 'Field cannot be blank';
    }
}

//payment.php
function generateID($char, $length)
{
    $min = pow(10, ($length - 1));
    $max = pow(10, $length) - 1;
    $randNum = rand($min, $max);

    return $char . $randNum;
}
