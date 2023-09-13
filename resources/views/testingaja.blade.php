<?php

header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header('Access-Control-Allow-Methods: GET, POST, PUT');
header('content-type: application/json; charset=utf-8');

// Set the base path where you want to store the uploaded images
$imageBasePath = '/home/srsssmsc/mobilepro.srs-ssms.com/storage/app/public/qc/inspeksi_gudang/';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if the action parameter is set
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'upload') {
            // Handle image upload
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $uploadedFile = $_FILES['image'];
                $clientFileName = $_POST['filename']; // The filename you want
                $fullPath = $imageBasePath . '/' . $clientFileName; // Use the client-specified filename
              if (move_uploaded_file($uploadedFile['tmp_name'], $fullPath)) {
    $response = [
        'status' => 'success',
        'message' => 'File uploaded successfully.',
        'file_path' => $fullPath, // Optionally, you can include the file path in the response
    ];
    echo json_encode($response);
} else {
    $response = [
        'status' => 'error',
        'message' => 'Failed to move uploaded file.',
    ];
    echo json_encode($response);
}
        } elseif ($_POST['action'] === 'delete') {
            // Handle image deletion
            if (isset($_POST['imageFileName'])) {
                $imageFileName = $_POST['imageFileName'];
                $fullPath = $imageBasePath . '/' . $imageFileName;
                
                if (file_exists($fullPath)) {
                    if (unlink($fullPath)) {
                        echo 'File deleted successfully.';
                    } else {
                        echo 'Failed to delete the file.';
                    }
                } else {
                    echo 'File not found.';
                }
            } else {
                echo 'Missing imageFileName parameter.';
            }
        } else {
            echo 'Invalid action parameter.';
        }
    } else {
        echo 'Missing action parameter.';
    }
} else {
    echo 'Invalid request method.';
}
?>
