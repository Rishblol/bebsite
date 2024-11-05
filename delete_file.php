<?php
session_start();


$username = $_COOKIE['username'] ?? null;


if (!$username) {
    echo "User not logged in";
    exit();
}


if (!isset($_POST['file'])) {
    echo "No file specified";
    exit();
}

$file_path = $_POST['file'];



$user_dir = "uploads/" . $username . "/";
$real_path = realpath($file_path);
$real_user_dir = realpath($user_dir);

if (!$real_path || !$real_user_dir || strpos($real_path, $real_user_dir) !== 0) {
    echo "Invalid file path";
    exit();
}


if (!file_exists($file_path)) {
    echo "File does not exist";
    exit();
}


if (unlink($file_path)) {
    echo "success";
} else {
    echo "Failed to delete file";
}
