<?php
session_start();
$host     = 'localhost';
$db_user  = 'root';
$db_pass  = '';
$db_name  = 'smarton';

$mysqli = new mysqli($host, $db_user, $db_pass, $db_name);
if ($mysqli->connect_error) {
    die('Verilənlər bazasına qoşulma xətası: ' . $mysqli->connect_error);
}

if (isset($_POST['login'])) {

    $email    = trim(strip_tags(htmlspecialchars(mysqli_real_escape_string($mysqli, $_POST['email']))));
    $password = trim(strip_tags(htmlspecialchars(mysqli_real_escape_string($mysqli, $_POST['password']))));


    if (empty($email) || empty($password)) {
        $message = 'Melumatlar tam deyil';
    } else {

        $res = mysqli_query($mysqli,
            "SELECT id, username, email, password 
             FROM users 
             WHERE email='" . $email . "'"
        );
        if ($res && mysqli_num_rows($res) > 0) {
            $info = mysqli_fetch_assoc($res);


            if (password_verify($password, $info['password'])) {

                $_SESSION['user_id']  = $info['id'];
                $_SESSION['username'] = $info['username'];
                $_SESSION['email']    = $info['email'];


                header('Location: index.php');
                exit;
            } else {
                $message = 'Email və ya şifrə yalnışdır';
            }
        } else {
            $message = 'Bu email ilə istifadəçi tapılmadı';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="az">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daxil ol</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container d-flex justify-content-center align-items-center min-vh-100">
    <div class="card shadow p-4" style="width:100%; max-width:400px;">
        <h4 class="mb-4 text-center">Daxil ol</h4>

        <?php if (!empty($message)): ?>
            <div class="alert alert-danger text-center"><?php echo $message; ?></div>
        <?php endif; ?>

        <form action="login.php" method="POST">
            <div class="mb-3">
                <label for="email" class="form-label">Email ünvanı</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Şifrə</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>

            <button type="submit" name="login" class="btn btn-primary w-100">Daxil ol</button>
        </form>
        <div class="text-center mt-3">
            <a href="register.php">Əgər hesabınız yoxdursa, qeydiyyatdan keçin</a>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
