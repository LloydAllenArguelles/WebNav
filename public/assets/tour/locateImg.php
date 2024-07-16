<?php
header('Content-Type: application/json'); // Ensure the response is JSON
$directory = 'panoramas/';
$images = glob($directory . '*.{jpg,jpeg,png,gif}', GLOB_BRACE);

if ($images === false) {
    echo json_encode(['error' => 'Failed to read directory']);
} else {
    echo json_encode($images);
}
?>