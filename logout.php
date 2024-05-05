<?php
session_start();

if (isset($_SESSION['user']) || isset($_SESSION['admin'])) {
    $_SESSION = array();
    session_destroy();
    header('location: login.php');
    exit();
} else {
    header('location: index.php');
}
