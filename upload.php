<?php
session_start();
header('Content-Type: application/json');


$username = $_COOKIE['username'] ?? null;

if (!$username) {
  echo json_encode(['success' => false, 'message' => "User not logged in."]);
  exit();
}


$user_dir = "uploads/" . $username . "/";
if (!is_dir($user_dir)) {
  mkdir($user_dir, 0777, true);
}

$target_file = $user_dir . basename($_FILES["fileToUpload"]["name"]);
$uploadOk = 1;
$imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
$message = "";


if (file_exists($target_file)) {
  $message .= "Sorry, file already exists.\n";
  $uploadOk = 0;
}


if ($_FILES["fileToUpload"]["size"] > 1073741824) {
  $message .= "Sorry, your file is too large.\n";
  $uploadOk = 0;
}

if ($uploadOk == 0) {
  $message .= "Sorry, your file was not uploaded.\n";
} else {
  if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
    $message = "The file " . htmlspecialchars(basename($_FILES["fileToUpload"]["name"])) . " has been uploaded.";

    if (!isset($_SESSION['uploaded_files'][$username])) {
      $_SESSION['uploaded_files'][$username] = array();
    }

    $_SESSION['uploaded_files'][$username][] = array(
      'name' => basename($_FILES["fileToUpload"]["name"]),
      'path' => $target_file,
      'upload_time' => date('Y-m-d H:i:s')
    );
  } else {
    $message = "Sorry, there was an error uploading your file.";
    $uploadOk = 0;
  }
}

echo json_encode(['success' => $uploadOk == 1, 'message' => $message]);
