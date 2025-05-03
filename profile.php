<?php
session_start();
if(!isset($_SESSION['email']))
{echo'  <meta http-equiv="refresh" content="0; URL=login.php">'; exit;}
include "header.php";
?>



<div class="container py-5">
    <?php if (isset($_SESSION['user_id'])): ?>
        <div class="card mx-auto" style="max-width: 500px;">
            <div class="card-body text-center">
                <h3 class="card-title mb-4">Hoş gəlmisiniz, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h3>
                <p class="card-text"><strong>İstifadəçi adı:</strong> <?php echo htmlspecialchars($_SESSION['username']); ?></p>
                <p class="card-text"><strong>Email:</strong> <?php echo htmlspecialchars($_SESSION['email']); ?></p>
                <a href="logout.php" class="btn btn-danger mt-4">Çıxış</a>
            </div>
        </div>
    <?php else: ?>
        <div class="alert alert-warning text-center">
            Giriş etməmisiniz. Lütfən, <a href="login.php">daxil olun</a> və ya <a href="register.php">qeydiyyatdan keçin</a>.
        </div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<?php

include "footer.php";
?>
</body>
</html>

