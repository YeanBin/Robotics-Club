<?php
session_start();

if (!isset($_SESSION['admin'])) {
    header("location: ../login.php");
    exit();
}

$title = 'User Management';
$css = '../css/admin/event.css';

include('../includes/header-admin.php');
require_once('../includes/helper.php');

$con = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

if (!empty($_POST)) {
    $id = trim($_POST['id']);

    $id  = $con->real_escape_string($id);
    $stm = $con->prepare("DELETE FROM user WHERE id = ?");

    $stm->bind_param('i', $id);

    $stm->execute();

    if ($stm->affected_rows > 0) {
        header("location: users.php");
        exit();
    }

    $stm->close();
}

if (!empty($_GET)) {
    $id = isset($_GET['delete']) ? trim(($_GET['delete'])) : null;

    $id  = $con->real_escape_string($id);
    $sql = "SELECT * FROM user WHERE id = $id AND admin = 0";

    $result = $con->query($sql);

    if ($row = $result->fetch_object()) {
        printf(
            '<div class="popup active">
                <div class="card">
                    <h3>Confirmation</h3>
                    <p>Are you sure you want to delete the following?</p>
                    
                    <table border="1">
                    <tr>
                        <td>ID: </td>
                        <td>%d</td>
                    </tr>
                    <tr>
                        <td>Username: </td>
                        <td>%s</td>
                    </tr>
                    <tr>
                        <td>Email: </td>
                        <td>%s</td>
                    </tr>
                    <tr>
                        <td>Phone No.: </td>
                        <td>%s</td>
                    </tr>
                    <tr>
                        <td>Gender: </td>
                        <td>%s</td>
                    </tr>
                    </table>

                    <form action="" method="post">
                        <input type="hidden" name="username" value="%s" />
                        <input type="submit" name="yes" value="Yes" class="button"/>
                        <input type="button" value="Cancel" onclick="location=\'users.php\'" class="button"/>
                    </form>
                </div>
            </div>',
            $row->id,
            $row->username,
            $row->email,
            $row->phoneNo == null ? "-" : $row->phoneNo,
            $row->gender == null ? "-" : $row->gender,
            $row->username
        );
    }

    $result->free();
}
$con->close();

$con = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

$result = $con->query("SELECT * FROM user WHERE admin = 0");
?>

<section class="main-section">
    <div class="main-container">
        <div class="taskbar">
            <h1>USERS LIST</h1>
        </div>

        <div class="user-container">
            <table border="1">
                <colgroup>
                    <col style="width: 3%;">
                    <col>
                    <col>
                    <col>
                    <col style="width: 7%;">
                    <col style="width: 13%;">
                </colgroup>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Phone No.</th>
                        <th>Gender</th>
                        <th>Options</th>
                    </tr>
                </thead>

                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_object()) {
                        printf(
                            '<tr>
                                <td>%d</td>
                                <td>%s</td>
                                <td>%s</td>
                                <td>%s</td>
                                <td>%s</td>
                                <td>
                                    <a href="tickets.php?user=%s">Bookings</a> |
                                    <a href="users.php?delete=%d">Delete</a>
                                </td>
                            </tr>',
                            $row->id,
                            $row->username,
                            $row->email,
                            $row->phoneNo == null ? "-" : $row->phoneNo,
                            $row->gender == null ? "-" : $row->gender,
                            $row->username,
                            $row->id
                        );
                    }
                ?>
                    <tfoot>
                        <?php
                        printf(
                            '<tr>
                                <td colspan="6">%d record(s) found.</td>
                            </tr>',
                            $result->num_rows
                        );
                        ?>
                    </tfoot>
                <?php
                } else {
                ?>
                    <tr>
                        <td colspan="5">No records found.</td>
                    </tr>
                <?php
                }

                $result->free();
                $con->close();
                ?>
            </table>
        </div>
    </div>
</section>