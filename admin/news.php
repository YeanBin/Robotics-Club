<?php
session_start();

if (!isset($_SESSION['admin'])) {
    header("location: ../login.php");
    exit();
}

$title = 'News Management';
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
    $stm = $con->prepare("DELETE FROM news WHERE id = ?");

    $stm->bind_param('i', $id);

    $stm->execute();

    if ($stm->affected_rows > 0) {
        header("location: news.php");
        exit();
    }

    $stm->close();
}

if (!empty($_GET) && isset($_GET['delete'])) {
    $id = isset($_GET['delete']) ? trim(($_GET['delete'])) : null;

    $id  = $con->real_escape_string($id);
    $sql = "SELECT * FROM news WHERE id = $id";

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
                        <td>Title: </td>
                        <td>%s</td>
                    </tr>
                    <tr>
                        <td>Content: </td>
                        <td>%s</td>
                    </tr>
                    </table>

                    <form action="" method="post">
                        <input type="hidden" name="id" value="%s" />
                        <input type="submit" name="yes" value="Yes" class="button"/>
                        <input type="button" value="Cancel" onclick="location=\'news.php\'" class="button"/>
                    </form>
                </div>
            </div>',
            $row->id,
            $row->title,
            $row->content,
            $row->id
        );
    }

    $result->free();
}
$con->close();

$con = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

$result = $con->query("SELECT id, title, date, content FROM news");
?>

<section class="main-section">
    <div class="main-container">
        <div class="taskbar">
            <h1>NEWS LIST</h1>

            <div class="buttons">
                <a href="add.php?table=news" class="button">Add</a>
            </div>
        </div>

        <div class="news-container">
            <table border="1">
                <colgroup>
                    <col style="width: 3%;">
                    <col>
                    <col style="width: 12%;">
                    <col style="width: 10%;">
                </colgroup>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Date</th>
                        <th>Options</th>
                    </tr>
                </thead>

                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_object()) {
                        printf(
                            '<tr>
                                <td>%d</td>
                                <td>
                                    <a href="news.php?view=%d">%s</a>
                                </td>
                                <td>%s</td>
                                <td>
                                    <a href="edit.php?table=news&id=%d">Edit</a> | 
                                    <a href="news.php?delete=%d">Delete</a>
                                </td>
                            </tr>',
                            $row->id,
                            $row->id,
                            $row->title,
                            date("d-M-Y", strtotime($row->date)),
                            $row->id,
                            $row->id
                        );
                    }
                ?>
                    <tfoot>
                        <?php
                        printf(
                            '<tr>
                                <td colspan="5">%d record(s) found.</td>
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

                ?>
            </table>
        </div>
    </div>
</section>

<?php
if (isset($_GET['view'])) {
    $id = trim($_GET['view']);

    $id = $con->real_escape_string($id);
    $sql = "SELECT * FROM news WHERE id = $id";

    $result = $con->query($sql);

    if ($row = $result->fetch_object()) {
        printf(
            '<div class="popup active" style="display: none">
                <div class="big-card">
                    <i class="fa-solid fa-xmark" onclick=popupToggle()></i>

                    <div class="big-content">
                        <h1>%s</h1>
                        <span>%s</span>
                        <p>%s</p>
                    </div>
                </div>
            </div>',
            $row->title,
            date("d-M-Y", strtotime($row->date)),
            $row->content
        );
    }
}

$result->free();
$con->close();
?>

<script src="../js/script.js"></script>
</body>

</html>