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


    $stmt = $conn->prepare("SELECT username, password FROM login WHERE username = ?");
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }


    $stmt->bind_param("s", $username);


    $stmt->execute();


    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();


        if (password_verify($password, $user['password'])) {

            $_SESSION['username'] = $username;
            $_SESSION['logged_in'] = true;


            setcookie(
                "username",
                $username,
                [
                    'expires' => time() + (86400 * 30),
                    'path' => '/',
                    'secure' => true,
                    'httponly' => true,
                    'samesite' => 'Strict'
                ]
            );

            $stmt->close();
            $conn->close();

            header("Location: index.php");
            exit();
        } else {
            header("Location: login.html?error=invalid_credentials");
            exit();
        }
    } else {
        header("Location: login.html?error=user_not_found");
        exit();
    }

    $stmt->close();
    $conn->close();
} else {
    header("Location: login.html?error=invalid_method");
    exit();
}
