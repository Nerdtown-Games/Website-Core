<?php
// Enable detailed error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Get the image name and overlay image name from the URL
$image = isset($_GET['image']) ? $_GET['image'] : '';
$overlayImage = isset($_GET['ov_image']) ? $_GET['ov_image'] . '.png' : 'new.png'; // Default to 'new.png' if not provided

// Define paths
$imagePath = "icons/" . $image;
$overlayPath = 'overlay/' . $overlayImage; // Update to include the 'overlay/' directory

// Path to the default user image (assuming your domain is example.com)
$defaultImageURL = '/files/images/default.png';

// Function to create a red square with text
function createErrorImage($text) {
    $width = 200;  // Set width to 200px
    $height = 200; // Set height to 200px
    $image = imagecreatetruecolor($width, $height);

    // Colors
    $backgroundColor = imagecolorallocate($image, 255, 0, 0); // Red background
    $textColor = imagecolorallocate($image, 255, 255, 255); // White text

    // Fill background
    imagefill($image, 0, 0, $backgroundColor);

    // Load font
    $fontPath = '../Coconutz.ttf'; // Replace with a valid path to a .ttf font file
    $fontSize = 20;

    if (!file_exists($fontPath)) {
        die('Font file not found: ' . $fontPath);
    }

    // Add text
    imagettftext($image, $fontSize, 0, 10, 100, $textColor, $fontPath, $text);

    return $image;
}

// If bypass is set in the URL
if (isset($_GET['bypass'])) {
    // If bypass is set, show the default user image
    header('Content-Type: image/png');

    // Check if the default image exists
    if (file_exists($_SERVER['DOCUMENT_ROOT'] . $defaultImageURL)) {
        // Output the default user image without manipulation
        readfile($_SERVER['DOCUMENT_ROOT'] . $defaultImageURL);
        exit();
    } else {
        // If the default image file doesn't exist
        die('Default image not found: ' . $defaultImageURL);
    }
}

// Function to determine the actual image type
function getImageType($filePath) {
    if (!file_exists($filePath)) {
        return '';
    }

    $imageData = file_get_contents($filePath);
    $imageType = '';

    if (substr($imageData, 0, 8) === "\x89PNG\x0D\x0A\x1A\x0A") {
        $imageType = 'png';
    } elseif (substr($imageData, 0, 2) === "\xFF\xD8") {
        $imageType = 'jpeg';
    } elseif (substr($imageData, 0, 6) === "GIF87a" || substr($imageData, 0, 6) === "GIF89a") {
        $imageType = 'gif';
    }

    return $imageType;
}

// Determine the image type based on the file contents
$imageType = getImageType($imagePath);

// If the image type is not found or unsupported, show the error image
if (!$imageType) {
    header('Content-Type: image/png');
    $errorImage = createErrorImage('404 Not Found');
    imagepng($errorImage);
    imagedestroy($errorImage);
    exit();
}

// Create the image resource based on the determined type
switch ($imageType) {
    case 'jpeg':
        $imageResource = imagecreatefromjpeg($imagePath);
        break;
    case 'png':
        $imageResource = imagecreatefrompng($imagePath);
        break;
    case 'gif':
        $imageResource = imagecreatefromgif($imagePath);
        break;
    default:
        // Unsupported image format; show the error image
        header('Content-Type: image/png');
        $errorImage = createErrorImage('404 Not Found');
        imagepng($errorImage);
        imagedestroy($errorImage);
        exit();
}

// Check if the base image was loaded successfully
if (!$imageResource) {
    // If there's an error loading the base image, show the error image
    header('Content-Type: image/png');
    $errorImage = createErrorImage('404 Not Found');
    imagepng($errorImage);
    imagedestroy($errorImage);
    exit();
}

// Load the overlay image if it exists
if (file_exists($overlayPath)) {
    $overlayResource = imagecreatefrompng($overlayPath);

    if (!$overlayResource) {
        // If there's an error loading the overlay image, show the error image
        header('Content-Type: image/png');
        $errorImage = createErrorImage('404 Not Found');
        imagepng($errorImage);
        imagedestroy($errorImage);
        exit();
    }

    // Get the dimensions of the main image and overlay
    $imageWidth = imagesx($imageResource);
    $imageHeight = imagesy($imageResource);
    $overlayWidth = imagesx($overlayResource);
    $overlayHeight = imagesy($overlayResource);

    // Calculate the position to place the overlay (centered)
    $overlayX = ($imageWidth - $overlayWidth) / 2;
    $overlayY = ($imageHeight - $overlayHeight) / 2;

    // Overlay the images
    imagecopy($imageResource, $overlayResource, $overlayX, $overlayY, 0, 0, $overlayWidth, $overlayHeight);
    imagedestroy($overlayResource);
}

// Output the final image based on the original image format
if ($imageType == 'png') {
    header('Content-Type: image/png');
    imagepng($imageResource);
} elseif ($imageType == 'gif') {
    header('Content-Type: image/gif');
    imagegif($imageResource);
} else {
    header('Content-Type: image/jpeg');
    imagejpeg($imageResource);
}

// Clean up
imagedestroy($imageResource);
exit();
?>
