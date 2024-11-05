<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    session_start();
    $host = "localhost";
    $dbusername = "root";
    $dbpassword = "";
    $dbname = "auth";

    
    $conn = new mysqli($host, $dbusername, $dbpassword, $dbname);
    
    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['password_confirm']);

    
    if ($password !== $confirm_password) {
        header("Location: signup.html?error=password_mismatch");
        exit();
    }

    
    $check_stmt = $conn->prepare("SELECT username FROM login WHERE username = ?");
    if (!$check_stmt) {
        die("Prepare failed: " . $conn->error);
    }

    $check_stmt->bind_param("s", $username);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        $check_stmt->close();
        header("Location: signup.html?error=username_exists");
        exit();
    }
    $check_stmt->close();

    
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    
    $signup_stmt = $conn->prepare("INSERT INTO signup (username, password, confirm_password) VALUES (?, ?, ?)");
    if (!$signup_stmt) {
        die("Prepare failed: " . $conn->error);
    }

    $signup_stmt->bind_param("sss", $username, $hashed_password, $hashed_password);
    $signup_success = $signup_stmt->execute();
    $signup_stmt->close();

    if ($signup_success) {
        
        $login_stmt = $conn->prepare("INSERT INTO login (username, password) VALUES (?, ?)");
        if (!$login_stmt) {
            die("Prepare failed: " . $conn->error);
        }

        $login_stmt->bind_param("ss", $username, $hashed_password);
        $login_success = $login_stmt->execute();
        $login_stmt->close();

        if ($login_success) {
            
            $_SESSION['username'] = $username;
            setcookie("username", $username, time() + (86400 * 30), "/", "", true, true); 
            
            header("Location: index.php");
            exit();
        } else {
            header("Location: error.html?error=login_insert_failed");
            exit();
        }
    } else {
        header("Location: error.html?error=signup_failed");
        exit();
    }

    $conn->close();
}
?>