<?php

include_once '../functions.php';

$fileTypes = ['image/png', 'image/gif', 'image/jpg', 'image/jpeg'];

$folder = empty($_POST['folder']) ? 'Images/Accounts' : $_POST['folder'];
$fileName = empty($_POST['filename']) ? $_FILES['file']['name'] : $_POST['filename'];

if (!(in_array($_FILES['file']['type'], $fileTypes))) {
    return;
} elseif (is_uploaded_file($_FILES['file']['tmp_name'])) {
    //in case you want to move  the file in uploads directory
    $response = move_uploaded_file($_FILES['file']['tmp_name'], getDirectory() . $folder . $fileName);

    return $response;
}

return;