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


function downloadAndResizeImage($url, $uploadDir)
{



    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    $imageContent = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);

    if ($imageContent === false || $httpCode !== 200) {

        return false;
    }


    $finfo = finfo_open();
    $mimeType = finfo_buffer($finfo, $imageContent, FILEINFO_MIME_TYPE);
    finfo_close($finfo);



    switch ($mimeType) {
        case 'image/jpeg':
            $sourceImage = imagecreatefromstring($imageContent);
            $extension = 'jpg';
            break;
        case 'image/png':
            $sourceImage = imagecreatefromstring($imageContent);
            $extension = 'png';
            break;
        case 'image/gif':
            $sourceImage = imagecreatefromstring($imageContent);
            $extension = 'gif';
            break;
        case 'image/webp':
            $sourceImage = imagecreatefromstring($imageContent);
            $extension = 'webp';
            break;

        default:

            return false;
    }

    if (!$sourceImage) {

        return false;
    }

    $width = imagesx($sourceImage);
    $height = imagesy($sourceImage);




    $newWidth = 100;
    $newHeight = 100;

    $resizedImage = imagecreatetruecolor($newWidth, $newHeight);


    if ($mimeType === 'image/png' || $mimeType === 'image/gif' || $mimeType === 'image/webp') {
        imagecolortransparent($resizedImage, imagecolorallocatealpha($resizedImage, 0, 0, 0, 127));
        imagealphablending($resizedImage, false);
        imagesavealpha($resizedImage, true);
    }

    imagecopyresampled(
        $resizedImage, $sourceImage,
        0, 0, 0, 0,
        $newWidth, $newHeight,
        $width, $height
    );



    if (!file_exists($uploadDir)) {
        if (mkdir($uploadDir, 0755, true)) {

        } else {

            return false;
        }
    }

    $fileName = uniqid('img_', true) . '.' . $extension;
    $savePath = rtrim($uploadDir, '/') . '/' . $fileName;

    switch ($mimeType) {
        case 'image/jpeg':
            $success = imagejpeg($resizedImage, $savePath, 90);
            break;
        case 'image/png':
            $success = imagepng($resizedImage, $savePath);
            break;
        case 'image/gif':
            $success = imagegif($resizedImage, $savePath);
            break;
        case 'image/webp':
            $success = imagewebp($resizedImage, $savePath, 90);
            break;
    }

    if ($success) {

    } else {

        return false;
    }


    imagedestroy($sourceImage);
    imagedestroy($resizedImage);



    return $fileName;
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['excel_file'])) {
    $file = $_FILES['excel_file']['tmp_name'];

    try {
        $spreadsheet = IOFactory::load($file);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();

        $conn = new mysqli($host, $db_user, $db_pass, $db_name);
        if ($conn->connect_error) {
            throw new Exception("DB bağlantı xətası: " . $conn->connect_error);
        }

        for ($i = 1; $i < count($rows); $i++) {
            $row = $rows[$i];

            $name      = $conn->real_escape_string(trim((string)$row[0]));
            $price     = (float)$row[1];
            $category  = $conn->real_escape_string(trim((string)$row[2]));
            $dateInput = trim((string)$row[3]);
            $imageUrl  = trim((string)$row[4]);

            if (!$name || !$price || !$category || !$dateInput || !$imageUrl) {
                continue;
            }

            $dt = DateTime::createFromFormat('d.m.Y', $dateInput);
            if (!$dt) {
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
    } catch (Exception $e) {
        error_log($e->getMessage());
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Zəhmət olmasa .xlsx faylı göndərin']);
}
?>
