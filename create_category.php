<?php
session_start();
if (!isset($_SESSION['email'])) {
    header('Location: login.php');
    exit;
}


$host     = 'localhost';
$db_user  = 'root';
$db_pass  = '';
$db_name  = 'smarton';
$mysqli   = new mysqli($host, $db_user, $db_pass, $db_name);
if ($mysqli->connect_error) {
    die('DB bağlantı xətası: ' . $mysqli->connect_error);
}


if (isset($_POST['create'])) {
    $category_name = trim(strip_tags(htmlspecialchars(
        $mysqli->real_escape_string($_POST['category_name'])
    )));

    if ($category_name === '') {
        $error = 'Zəhmət olmasa, kateqoriya adını daxil edin.';
    } else {

        $res = $mysqli->query(
            "SELECT id FROM categories WHERE categoryName = '$category_name'"
        );
        if ($res && $res->num_rows > 0) {
            $error = 'Bu kateqoriya artıq mövcuddur.';
        } else {
            $date = date('Y-m-d H:i:s');

            if ($mysqli->query(
                "INSERT INTO categories (categoryName, date) VALUES ('" . $category_name . "', '" . $date . "')"
            )) {
                header('Location: category.php');
                exit;
            } else {
                $error = 'Əlavə edərkən xəta: ' . $mysqli->error;
            }
        }
    }
}

include 'header.php';
?>

<section class="section">
    <div class="container-fluid">
        <div class="container mt-5">
            <h4 class="mb-4">Kateqoriya Əlavə Et</h4>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>

            <form action="" method="POST">
                <div class="mb-3">
                    <label for="categoryName" class="form-label">Kateqoriya adı</label>
                    <input
                            type="text"
                            class="form-control"
                            id="categoryName"
                            name="category_name"

                            required
                    >
                </div>
                <button type="submit" name="create" class="btn btn-primary">Əlavə et</button>
            </form>
        </div>
    </div>
</section>

<?php include 'footer.php'; ?>
