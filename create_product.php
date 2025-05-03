<?php
session_start();
if (!isset($_SESSION['email'])) {
    header('Location: login.php');
    exit;
}

$host    = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'smarton';

$mysqli = new mysqli($host, $db_user, $db_pass, $db_name);
if ($mysqli->connect_error) {
    die('DB bağlantı xətası: ' . $mysqli->connect_error);
}


$categories = [];
$res = $mysqli->query("SELECT id, categoryName FROM categories ORDER BY categoryName ASC");
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $categories[] = $row;
    }
}

if (isset($_POST['create'])) {

    $product_name = trim(strip_tags(htmlspecialchars(
        $mysqli->real_escape_string($_POST['product_name'])
    )));
    $price = trim(strip_tags(htmlspecialchars(
        $mysqli->real_escape_string($_POST['price'])
    )));
    $category_id = (int) $_POST['category'];


    $thumbnail   = trim(strip_tags(htmlspecialchars(
        $mysqli->real_escape_string($_FILES['image']['name'])
    )));


    if ($product_name === '' || $price === '' || $category_id === 0 || $thumbnail === '') {
        $error = 'Zəhmət olmasa, bütün sahələri doldurun.';
    } else {

        $resCheck = $mysqli->query(
            "SELECT id FROM products 
             WHERE name = '$product_name' 
               AND category = $category_id"
        );
        if ($resCheck && $resCheck->num_rows > 0) {
            $error = 'Bu məhsul artıq mövcuddur.';
        } else {

            $uploadDir  = 'upload/';
            $targetPath = $uploadDir . basename($_FILES['image']['name']);
            move_uploaded_file($_FILES['image']['tmp_name'], $targetPath);

            $date = date('Y-m-d H:i:s');


            if ($mysqli->query(
                "INSERT INTO products (name, price, category, image, date) 
                 VALUES (
                    '" . $product_name . "',
                    '" . $price . "',
                    "  . $category_id . ",
                    '" . $targetPath . "',
                    '" . $date . "'
                 )"
            )) {
                header('Location: products.php');
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
            <h4 class="mb-4">Məhsul Əlavə Et</h4>

            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>

            <form action="" method="POST" enctype="multipart/form-data">

                <div class="mb-3">
                    <label for="productName" class="form-label">Məhsul adı</label>
                    <input type="text" class="form-control" id="productName" name="product_name" required>
                </div>


                <div class="mb-3">
                    <label for="price" class="form-label">Qiymət (AZN)</label>
                    <input type="number" class="form-control" id="price" name="price" step="0.01" required>
                </div>


                <div class="mb-3">
                    <label for="category" class="form-label">Kateqoriya</label>
                    <select class="form-select" id="category" name="category" required>
                        <option value="">Kateqoriya seçin</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo $cat['id']; ?>">
                                <?php echo htmlspecialchars($cat['categoryName']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>


                <div class="mb-3">
                    <label for="image" class="form-label">Şəkil (miniatura)</label>
                    <input class="form-control" type="file" id="image" name="image" accept="image/*" required>
                </div>


                <button type="submit" name="create" class="btn btn-primary">Əlavə et</button>
            </form>
        </div>
    </div>
</section>

<?php include 'footer.php'; ?>
