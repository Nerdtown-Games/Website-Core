<?php
// Get the image name and size from query parameters
$imageName = isset($_GET['image']) ? $_GET['image'] : '';
$size = isset($_GET['size']) ? intval($_GET['size']) : 25;

// Validate input
if (!$imageName || !$size || $size <= 0) {
    die('Invalid parameters. Provide ?image=imagename&size=size');
}

// Path to the image folder (modify as needed)
$imagePath = __DIR__ . '/' . $imageName;

// Check if the image exists
if (!file_exists($imagePath)) {
    die('Image not found: ' . $imagePath);
}

// Get the file's last modified time for caching headers
$lastModified = filemtime($imagePath);

// Generate an ETag based on the image path, modification time, and size
$etag = md5($imagePath . $lastModified . $size);

// Set caching headers


// Check if the client has a cached version
if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) || isset($_SERVER['HTTP_IF_NONE_MATCH'])) {
    $ifModifiedSince = isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) ? strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) : false;
    $ifNoneMatch = isset($_SERVER['HTTP_IF_NONE_MATCH']) ? trim($_SERVER['HTTP_IF_NONE_MATCH'], '"') : false;

    if (($ifModifiedSince && $ifModifiedSince >= $lastModified) || ($ifNoneMatch && $ifNoneMatch === $etag)) {
        header('HTTP/1.1 304 Not Modified');
        exit;
    }
}

// Get the original image dimensions
list($originalWidth, $originalHeight, $imageType) = getimagesize($imagePath);

// Calculate new dimensions while maintaining aspect ratio
$newHeight = $size; // Use the provided size for the height
$newWidth = intval($originalWidth * ($newHeight / $originalHeight)); // Adjust width proportionally

// Create a new true colour image with the new dimensions
$resizedImage = imagecreatetruecolor($newWidth, $newHeight);

// Check if the image has transparency (PNG or GIF)
if ($imageType == IMAGETYPE_PNG || $imageType == IMAGETYPE_GIF) {
    // Enable transparency
    imagealphablending($resizedImage, false);
    imagesavealpha($resizedImage, true);
    // Fill the background with transparent color
    $transparent = imagecolorallocatealpha($resizedImage, 0, 0, 0, 127);
    imagefill($resizedImage, 0, 0, $transparent);
}

// Load the original image based on its type
switch ($imageType) {
    case IMAGETYPE_JPEG:
        $originalImage = imagecreatefromjpeg($imagePath);
        break;
    case IMAGETYPE_PNG:
        $originalImage = imagecreatefrompng($imagePath);
        break;
    case IMAGETYPE_GIF:
        $originalImage = imagecreatefromgif($imagePath);
        break;
    default:
        die('Unsupported image type.');
}

// Resize the image
imagecopyresampled(
    $resizedImage,
    $originalImage,
    0, 0, 0, 0,
    $newWidth, $newHeight,
    $originalWidth, $originalHeight
);

// Set the appropriate header to force PNG output
header('Cache-Control: max-age=31536000, public');
header('Expires: ' . gmdate('D, d M Y H:i:s', strtotime('+1 year')) . ' GMT');
header('Last-Modified: ' . gmdate('D, d M Y H:i:s', $lastModified) . ' GMT');
header('ETag: "' . $etag . '"');
header('Content-Type: image/png');

// Output the image as PNG (force PNG format)
imagepng($resizedImage);

// Free up memory
imagedestroy($resizedImage);
imagedestroy($originalImage);
?>