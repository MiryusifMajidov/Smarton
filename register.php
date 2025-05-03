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


if (isset($_POST['register'])) {
    $username = trim(strip_tags(htmlspecialchars(mysqli_real_escape_string($mysqli, $_POST['username']))));
    $email    = trim(strip_tags(htmlspecialchars(mysqli_real_escape_string($mysqli, $_POST['email']))));
    $password = trim(strip_tags(htmlspecialchars(mysqli_real_escape_string($mysqli, $_POST['password']))));


    if (!empty($username) && !empty($email) && !empty($password)) {


        $usernameCheck = mysqli_query($mysqli, "SELECT id FROM users WHERE username='" . $username . "'");
        if (mysqli_num_rows($usernameCheck) > 0) {
            $message = 'Bu istifadəçi adı artıq qeydiyyatdan keçib';
        } else {

            $emailCheck = mysqli_query($mysqli, "SELECT id FROM users WHERE email='" . $email . "'");

            if (mysqli_num_rows($emailCheck) == 0) {


                $passwordHash = password_hash($password, PASSWORD_DEFAULT);
                $date = date('Y-m-d H:i:s');


                $insertUser = mysqli_query($mysqli, "INSERT INTO users(username, email, password, date) VALUES('" . $username . "', '" . $email . "', '" . $passwordHash . "', '" . $date . "')");


                if ($insertUser) {

                    $checkUser = mysqli_query($mysqli, "SELECT * FROM users WHERE email='" . $email . "' AND password='" . $passwordHash . "'");

                    if ($checkUser !== false) {
                        if (mysqli_num_rows($checkUser) > 0) {
                            $info = mysqli_fetch_array($checkUser);

                            $_SESSION['user_id'] = $info['id'];
                            $_SESSION['username'] = $info['username'];
                            $_SESSION['email'] = $info['email'];

                            echo '<meta http-equiv="refresh" content="0; URL=index.php">';
                            exit;
                        }
                    } else {
                        $message =  "SQL sorğularında səhv baş verdi: " . mysqli_error($mysqli);
                    }
                } else {
                    $message ='Qeydiyyat zamanı səhv baş verdi: ' . mysqli_error($mysqli);
                }
            } else {
                $message = 'Bu email artıq qeydiyyatdan keçib';
            }
        }
    } else {
        $message = 'Melumatlar tam deyil';
    }
}


?>


<!DOCTYPE html>
<html lang="az">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Qeydiyyat Formu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container d-flex justify-content-center align-items-center min-vh-100">
    <div class="card shadow p-4" style="width: 100%; max-width: 400px;">
        <h4 class="mb-4 text-center">Qeydiyyat</h4>

        <?php if (!empty($message)) : ?>
            <div class="alert alert-info text-center"><?php echo $message; ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label for="username" class="form-label">İstifadəçi adı</label>
                <input type="text" class="form-control" id="username" name="username">
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email ünvanı</label>
                <input type="email" class="form-control" id="email" name="email">
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Şifrə</label>
                <input type="password" class="form-control" id="password" name="password">
            </div>

            <button type="submit" name="register" class="btn btn-primary w-100">Qeydiyyatdan keç</button>
        </form>

        <div class="text-center mt-3">
            <a href="login.php">Əgər hesabınız varsa, daxil olun</a>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
