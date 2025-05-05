<?php
session_start();
if (!isset($_SESSION['email'])) {
    echo '<meta http-equiv="refresh" content="0; URL=login.php">';
    exit;
}
include "header.php";


$host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'smarton';

$mysqli = new mysqli($host, $db_user, $db_pass, $db_name);
if ($mysqli->connect_error) {
    die("DB bağlantı xətası: " . $mysqli->connect_error);
}


$sql = "SELECT * FROM products ORDER BY id DESC";

$result = $mysqli->query($sql);
?>
<section class="section">
    <div class="container-fluid">
        <div class="title-wrapper pt-30">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="title d-flex align-items-center flex-wrap">
                        <h2 class="mr-40">Products</h2>
                        <a href="create_product.php" class="main-btn primary-btn btn-hover btn-sm" style="margin-right: 50px;">New Product
                        </a>
                        <a href="#" onclick="uploadExcel()" class="main-btn primary-btn btn-hover btn-sm">
                            <i class="lni lni-plus mr-5"></i> New Product from Excel
                        </a>
                    </div>
                </div>



                <div class="col-md-6">
                    <div class="breadcrumb-wrapper">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item">
                                    <a href="#0">Dashboard</a>
                                </li>
                                <li class="breadcrumb-item active" aria-current="page">
                                    Product
                                </li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>

        <section class="table-components">
            <div class="container-fluid">
                <div class="tables-wrapper">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card-style mb-30">
                                <h6 class="mb-10">Product Table</h6>
                                <div class="table-wrapper table-responsive">
                                    <table class="table">
                                        <thead>
                                        <tr>
                                            <th><h6>#</h6></th>
                                            <th><h6>İmage</h6></th>
                                            <th><h6>Name</h6></th>
                                            <th><h6>Category</h6></th>
                                            <th><h6>Qiymət</h6></th>
                                            <th><h6>Status</h6></th>
                                            <th><h6>Action</h6></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php if ($result && $result->num_rows > 0): ?>
                                            <?php $count = 1; while($row = $result->fetch_assoc()): ?>
                                                <tr>
                                                    <td><?php echo $count++; ?></td>
                                                    <td>
                                                        <div class="employee-image">
                                                            <img src="<?php echo $row['image']; ?>" alt="" width="60" height="60" style="object-fit: cover;">
                                                        </div>
                                                    </td>
                                                    <td class="min-width"><?php echo htmlspecialchars($row['name']); ?></td>
                                                    <td class="min-width"><?php echo htmlspecialchars($row['category']); ?></td>
                                                    <td class="min-width"><?php echo number_format($row['price'], 2); ?> ₼</td>
                                                    <td class="min-width">
                                                        <span class="status-btn active-btn">Active</span>
                                                    </td>
                                                    <td>
                                                        <div class="action">
                                                            <button class="text-danger delete-product-btn" data-id="<?php echo $row['id']; ?>">
                                                                <i class="lni lni-trash-can"></i>
                                                            </button>
                                                        </div>
                                                    </td>


                                                </tr>
                                            <?php endwhile; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="7">Heç bir məhsul tapılmadı.</td>
                                            </tr>
                                        <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</section>

<script>
    function uploadExcel() {
        Swal.fire({
            title: 'Excel faylını seçin',
            input: 'file',
            inputAttributes: {
                'accept': '.xlsx',
                'aria-label': 'Excel faylını yüklə (.xlsx)'
            },
            showCancelButton: true,
            confirmButtonText: 'Yüklə',
            cancelButtonText: 'İmtina et',
            preConfirm: (file) => {
                return new Promise((resolve, reject) => {
                    if (file && file.name.endsWith('.xlsx')) {
                        const formData = new FormData();
                        formData.append('excel_file', file);

                        fetch('uploadExcel.php', {
                            method: 'POST',
                            body: formData
                        })
                            .then(res => res.json())
                            .then(response => {
                                Swal.fire({
                                    icon: response.success ? 'success' : 'error',
                                    title: response.success ? 'Uğurlu!' : 'Xəta!',
                                    text: response.message,
                                }).then(() => {
                                    if (response.success) {
                                        window.location.href = 'product.php';
                                    }
                                });
                            })
                            .catch(() => {
                                Swal.fire('Xəta!', 'Serverə qoşulmaq olmadı', 'error');
                            });
                    } else {
                        Swal.showValidationMessage('Zəhmət olmasa .xlsx faylı seçin!');
                    }
                });
            }
        });
    }
</script>

<script>
    document.querySelectorAll('.delete-product-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const productId = this.getAttribute('data-id');
            Swal.fire({
                title: 'Silmək istədiyinizə əminsiniz',
                text: "Bu əməliyyat geri alınmaz",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Bəli, sil',
                cancelButtonText: 'İmtina et'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'delete_product.php?id=' + productId;
                }
            });
        });
    });
</script>

<?php include "footer.php"; ?>
