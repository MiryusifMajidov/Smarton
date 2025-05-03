<?php
require 'vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

header('Content-Type: application/json');


ini_set('log_errors', 1);
ini_set('error_log', 'php_error.log');

$host    = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'smarton';


function downloadAndResizeImage($url, $uploadDir) {
    $imageContent = @file_get_contents($url);
    if ($imageContent === false) {
        return false;
    }

    $tempPath = tempnam(sys_get_temp_dir(), 'img_');
    file_put_contents($tempPath, $imageContent);

    list($width, $height, $type) = getimagesize($tempPath);

    switch ($type) {
        case IMAGETYPE_JPEG:
            $sourceImage = imagecreatefromjpeg($tempPath);
            $extension = 'jpg';
            break;
        case IMAGETYPE_PNG:
            $sourceImage = imagecreatefrompng($tempPath);
            $extension = 'png';
            break;
        case IMAGETYPE_GIF:
            $sourceImage = imagecreatefromgif($tempPath);
            $extension = 'gif';
            break;
        default:
            return false;
    }

    $newWidth = 100;
    $newHeight = 100;
    $resizedImage = imagecreatetruecolor($newWidth, $newHeight);

    imagecopyresampled($resizedImage, $sourceImage, 0, 0, 0, 0,
        $newWidth, $newHeight, $width, $height);

    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $fileName = uniqid('img_', true) . '.' . $extension;
    $savePath = rtrim($uploadDir, '/') . '/' . $fileName;

    switch ($type) {
        case IMAGETYPE_JPEG:
            imagejpeg($resizedImage, $savePath);
            break;
        case IMAGETYPE_PNG:
            imagepng($resizedImage, $savePath);
            break;
        case IMAGETYPE_GIF:
            imagegif($resizedImage, $savePath);
            break;
    }

    imagedestroy($sourceImage);
    imagedestroy($resizedImage);
    unlink($tempPath);

    return $fileName;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['excel_file'])) {
    $file = $_FILES['excel_file']['tmp_name'];

    try {
        $spreadsheet = IOFactory::load($file);
        $sheet       = $spreadsheet->getActiveSheet();
        $rows        = $sheet->toArray();

        $conn = new mysqli($host, $db_user, $db_pass, $db_name);
        if ($conn->connect_error) {
            die("DB bağlantı xətası: " . $conn->connect_error);
        }

        for ($i = 1; $i < count($rows); $i++) {
            $row = $rows[$i];

            $name       = $conn->real_escape_string((string) $row[0]);
            $price      = (float) $row[1];
            $category   = $conn->real_escape_string((string) $row[2]);
            $dateInput  = trim((string) $row[3]);
            $imageUrl   = trim((string) $row[4]);

            if (empty($name) || empty($price) || empty($category) || empty($dateInput) || empty($imageUrl)) {
                continue;
            }

            $dt = DateTime::createFromFormat('d.m.Y', $dateInput);
            if ($dt === false) {
                continue;
            }
            $formattedDate = $dt->format('Y-m-d H:i:s');

            if (!filter_var($imageUrl, FILTER_VALIDATE_URL)) {
                continue;
            }

            $imageFileName = downloadAndResizeImage($imageUrl, __DIR__ . '/upload');
            if (!$imageFileName) {
                continue;
            }

            $imagePath = 'upload/' . $conn->real_escape_string($imageFileName);

            $sql = "
                INSERT INTO products (name, price, category, image, date)
                VALUES (
                    '{$name}',
                    '{$price}',
                    '{$category}',
                    '{$imagePath}',
                    '{$formattedDate}'
                )
            ";

            if (!$conn->query($sql)) {
                throw new Exception("Məhsul əlavə olunarkən xəta baş verdi: " . $conn->error);
            }
        }

        echo json_encode(['success' => true, 'message' => 'Məhsullar uğurla əlavə olundu (boş və ya səhv sətirlər atlanıb)']);
    }
    catch (Exception $e) {
        error_log($e->getMessage());
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}
else {
    echo json_encode(['success' => false, 'message' => 'Zəhmət olmasa .xlsx faylı göndərin']);
}
?>
