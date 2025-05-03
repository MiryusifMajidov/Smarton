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
$mysqli  = new mysqli($host, $db_user, $db_pass, $db_name);
if ($mysqli->connect_error) {
    die('DB bağlantı xətası: ' . $mysqli->connect_error);
}


$result = $mysqli->query("SELECT * FROM categories ORDER BY id ASC");
?>
<?php include "header.php"; ?>

<section class="section">
    <div class="container-fluid">
        <div class="title-wrapper pt-30">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="title d-flex align-items-center flex-wrap">
                        <h2 class="mr-40">Categories</h2>
                        <a href="create_category.php" class="main-btn primary-btn btn-hover btn-sm">
                            <i class="lni lni-plus mr-5"></i> New Category
                        </a>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="breadcrumb-wrapper">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Category</li>
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
                                <h6 class="mb-10">Category Table</h6>
                                <div class="table-wrapper table-responsive">
                                    <table class="table">
                                        <thead>
                                        <tr>
                                            <th><h6>#</h6></th>
                                            <th><h6>Name</h6></th>
                                            <th><h6>Action</h6></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php if ($result && $result->num_rows > 0):
                                            $i = 1;
                                            while ($row = $result->fetch_assoc()): ?>
                                                <tr>
                                                    <td><?php echo $i++; ?></td>
                                                    <td class="min-width">
                                                        <p><?php echo htmlspecialchars($row['categoryName']); ?></p>
                                                    </td>
                                                    <td>
                                                        <div class="action">
                                                            <button
                                                                    class="btn btn-link text-danger delete-category-btn"
                                                                    data-id="<?php echo $row['id']; ?>"
                                                                    type="button"
                                                            >
                                                                <i class="lni lni-trash-can"></i>
                                                            </button>
                                                        </div>
                                                    </td>

                                                </tr>
                                            <?php endwhile;
                                        else: ?>
                                            <tr>
                                                <td colspan="3" class="text-center">Heç bir kateqoriya tapılmadı.</td>
                                            </tr>
                                        <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div><!-- end row -->
                </div>
            </div>
        </section>
    </div>
</section>

<script>
    document.querySelectorAll('.delete-category-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const categoryId = this.getAttribute('data-id');
            Swal.fire({
                title: 'Silmək istədiyinizə əminsiniz?',
                text: "Bu əməliyyat geri alınmaz!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Bəli, sil!',
                cancelButtonText: 'Imtina et'
            }).then((result) => {
                if (result.isConfirmed) {

                    window.location.href = 'delete_category.php?id=' + categoryId;
                }
            });
        });
    });
</script>

<?php include "footer.php"; ?>
