<?php
if (!isset($_COOKIE['username'])) {
    header('Location: login.html');
    exit();
}

$username = $_COOKIE['username'];
$uploads_dir = "uploads/";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            
            document.querySelectorAll('.delete-button').forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();

                    if (confirm('Are you sure you want to delete this file?')) {
                        const filePath = this.getAttribute('href');

                        
                        fetch('delete_file.php', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/x-www-form-urlencoded',
                                },
                                body: 'file=' + encodeURIComponent(filePath)
                            })
                            .then(response => response.text())
                            .then(data => {
                                if (data === 'success') {
                                    
                                    this.closest('tr').remove();

                                    
                                    const tbody = document.querySelector('tbody');
                                    if (tbody.children.length === 0) {
                                        const table = document.querySelector('table');
                                        table.remove();
                                        document.body.innerHTML += "<div class='no-files'>No files uploaded yet.</div>";
                                    }
                                } else {
                                    alert('Error deleting file: ' + data);
                                }
                            })
                            .catch(error => {
                                alert('Error deleting file: ' + error);
                            });
                    }
                });
            });
        });
    </script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Uploaded Files</title>
    <style>
        :root {
            --bg-color: black;
            --text-color: #f4f4f4;
            --navbar-bg: black;
            --navbar-text: #fff;
            --button-bg: black;
            --button-text: #fff;
            --table-border: #555;
            --table-header-bg: #3a3a3a;
            --table-row-hover: #2a2a2a;
        }

        body {
            font-family: consolas;
            margin: 0;
            padding: 0;
            background-color: var(--bg-color);
            color: var(--text-color);
        }

        .navbar {
            background-color: var(--navbar-bg);
            overflow: hidden;
            padding: 22px 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .navbar a {
            color: var(--navbar-text);
            text-align: center;
            padding: 14px 20px;
            text-decoration: none;
            transition: background-color 0.3s;
        }

        .navbar a:hover {
            background-color: #4CAF50;
        }

        .content {
            padding: 20px;
            max-width: 1000px;
            margin: 0 auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: rgba(255, 255, 255, 0.05);
        }

        th,
        td {
            border: 1px solid var(--table-border);
            padding: 12px;
            text-align: left;
        }

        th {
            background-color: var(--table-header-bg);
            color: #fff;
        }

        tr:hover {
            background-color: var(--table-row-hover);
        }

        .file-link {
            color: #4CAF50;
            text-decoration: none;
        }

        .file-link:hover {
            text-decoration: underline;
        }

        .no-files {
            text-align: center;
            padding: 20px;
            background-color: rgba(255, 255, 255, 0.05);
            border-radius: 5px;
        }

        .action-buttons {
            display: flex;
            gap: 10px;
        }

        .action-button {
            padding: 5px 10px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            text-decoration: none;
            color: white;
            background-color: #4CAF50;
        }

        .delete-button {
            padding: 5px 10px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            text-decoration: none;
            color: white;
            background-color: #f44336;
        }
    </style>
</head>

<body>
    <div class="navbar">
        <div class="navbar-left">
            <a href="index.php">Home</a>
            <a class="active" href="#">My Files</a>
        </div>
        <div class="navbar-right">
            <a href="logout.php">Log out</a>
        </div>
    </div>

    <div class="content">
        <h1>My Uploaded Files</h1>

        <?php

        $username = $_COOKIE['username'] ?? null;

        if (!$username) {
            echo "<div class='no-files'>User not logged in.</div>";
            exit();
        }

        $user_dir = "uploads/" . $username . "/";

        if (!is_dir($user_dir)) {
            echo "<div class='no-files'>No files uploaded yet.</div>";
            exit();
        }

        $files = scandir($user_dir);

        if (count($files) <= 2) {
            echo "<div class='no-files'>No files uploaded yet.</div>";
        } else {
            echo "<table>
                    <thead>
                        <tr>
                            <th>File Name</th>
                            <th>Size</th>
                            <th>Upload Date</th>
                            <th>Type</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>";

            foreach ($files as $file) {
                if ($file != "." && $file != "..") {
                    $file_path = $user_dir . $file;
                    $file_size = filesize($file_path);
                    $file_time = filemtime($file_path);
                    $file_type = mime_content_type($file_path);

                    if ($file_size < 1024) {
                        $size_str = $file_size . " B";
                    } elseif ($file_size < 1048576) {
                        $size_str = round($file_size / 1024, 2) . " KB";
                    } else {
                        $size_str = round($file_size / 1048576, 2) . " MB";
                    }

                    echo "<tr>
                            <td><a href='" . htmlspecialchars($file_path) . "' class='file-link'>" . htmlspecialchars($file) . "</a></td>
                            <td>" . $size_str . "</td>
                            <td>" . date("Y-m-d H:i:s", $file_time) . "</td>
                            <td>" . $file_type . "</td>
                            <td class='action-buttons'>
                                <a href='" . htmlspecialchars($file_path) . "' download class='action-button'>Download</a>
                                <a href='" . htmlspecialchars($file_path) . "' delete class='delete-button'>Delete</a>
                            </td>
                          </tr>";
                }
            }

            echo "</tbody></table>";
        }
        ?>

    </div>
</body>

</html>