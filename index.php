<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Index Page</title>
    <style>
        :root {
            --bg-color: black;
            --text-color: #f4f4f4;
            --navbar-bg: black;
            --navbar-text: #fff;
            --button-bg: black;
            --button-text: #fff;
            --input-bg: #444;
            --input-text: #fff;
            --table-border: #555;
            --table-header-bg: #3a3a3a;
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
            padding: 10px 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .navbar-left,
        .navbar-right {
            display: flex;
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
            transition-duration: 0.2s;
        }

        .content {
            padding: 20px;
            max-width: 800px;
            margin: 0 auto;
        }

        h1 {
            text-align: center;
            color: var(--text-color);
            font-family: consolas;
        }

        .sql-console {
            margin-top: 20px;
        }

        .sql-input {
            width: 100%;
            height: 100px;
            margin-bottom: 10px;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid var(--table-border);
            background-color: var(--input-bg);
            color: var(--input-text);
        }

        .sql-output {
            border: 1px solid var(--table-border);
            padding: 10px;
            background-color: var(--input-bg);
            color: var(--input-text);
            border-radius: 5px;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        th,
        td {
            border: 1px solid var(--table-border);
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: var(--table-header-bg);
        }

        input[type="submit"],
        input[type="file"] {
            background-color: var(--button-bg);
            color: var(--button-text);
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        input[type="submit"]:hover,
        input[type="file"]:hover {
            opacity: 0.8;
        }

        .error-message {
            color: #ff4444;
            text-align: center;
            margin: 10px 0;
        }

        @media (max-width: 600px) {
            .navbar {
                flex-direction: column;
                align-items: flex-start;
            }

            .navbar-right {
                margin-top: 10px;
            }
        }
    </style>
</head>

<body>
    <div class="navbar">
        <div class="navbar-left">
            <a class="active" href="#home">Home</a>  
            <a href="/website/list_file.php" onclick="showFiles()">My Files</a>
            <?php
            if (!isset($_COOKIE['username'])) {
                echo '<a href="login.html">Login</a>';
                echo '<a href="signup.html">Signup</a>';
            }
            ?>
        </div>
        <div class="navbar-right">
            <?php
            if (isset($_COOKIE['username'])) {
                echo '<a href="logout.php">Log out</a>';
            }
            ?>
        </div>
    </div>

    <div class="content">
        <?php
        if (isset($_COOKIE['username'])) {
            $username = htmlspecialchars($_COOKIE['username']);
            
            if ($username === 'admin') {
                
                echo "<h1>Welcome, Admin!</h1>";
                echo "<p>I thank you for my existence- enter your command daddy</p>";

                echo '<div class="sql-console">
                    <form method="post">
                        <textarea name="sql_query" class="sql-input" placeholder="Enter your SQL query here..."></textarea>
                        <br>
                        <input type="submit" value="Execute Query">
                    </form>
                </div>';

                if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['sql_query'])) {
                    $sql_query = $_POST['sql_query'];

                    
                    if (stripos($sql_query, 'DROP') !== false) {
                        echo '<div class="error-message">DROP command is not allowed!</div>';
                    } else {
                        $servername = "localhost";
                        $db_username = "root";
                        $password = "";
                        $dbname = "auth";

                        try {
                            $conn = new mysqli($servername, $db_username, $password, $dbname);

                            if ($conn->connect_error) {
                                throw new Exception("Connection failed: " . $conn->connect_error);
                            }

                            $result = $conn->query($sql_query);

                            echo '<div class="sql-output">';
                            if ($result === TRUE) {
                                echo "Query executed successfully";
                            } elseif ($result === FALSE) {
                                echo "Error executing query: " . $conn->error;
                            } else {
                                if ($result->num_rows > 0) {
                                    echo "<table>";
                                    $first_row = true;
                                    while ($row = $result->fetch_assoc()) {
                                        if ($first_row) {
                                            echo "<tr>";
                                            foreach ($row as $key => $value) {
                                                echo "<th>" . htmlspecialchars($key) . "</th>";
                                            }
                                            echo "</tr>";
                                            $first_row = false;
                                        }
                                        echo "<tr>";
                                        foreach ($row as $value) {
                                            echo "<td>" . htmlspecialchars($value) . "</td>";
                                        }
                                        echo "</tr>";
                                    }
                                    echo "</table>";
                                } else {
                                    echo "0 results";
                                }
                            }
                            echo '</div>';

                            $conn->close();
                        } catch (Exception $e) {
                            echo '<div class="sql-output">';
                            echo "Error: " . $e->getMessage();
                            echo '</div>';
                        }
                    }
                }
            } else {
                
                echo '<h1>Welcome, ' . $username . '</h1>';
                echo "<p style='text-align: center;'>What the fuck did you just fucking say about me, you little bitch? I'll have you know I graduated top of my class in the Navy Seals, and I've been involved in numerous secret raids on Al-Quaeda, and I have over 300 confirmed kills. I am trained in gorilla warfare and I'm the top sniper in the entire US armed forces. You are nothing to me but just another target. I will wipe you the fuck out with precision the likes of which has never been seen before on this Earth, mark my fucking words. You think you can get away with saying that shit to me over the Internet? Think again, fucker. As we speak I am contacting my secret network of spies across the USA and your IP is being traced right now so you better prepare for the storm, maggot. The storm that wipes out the pathetic little thing you call your life. You're fucking dead, kid. I can be anywhere, anytime, and I can kill you in over seven hundred ways, and that's just with my bare hands. Not only am I extensively trained in unarmed combat, but I have access to the entire arsenal of the United States Marine Corps and I will use it to its full extent to wipe your miserable ass off the face of the continent, you little shit. If only you could have known what unholy retribution your little 'clever' comment was about to bring down upon you, maybe you would have held your fucking tongue. But you couldn't, you didn't, and now you're paying the price, you goddamn idiot. I will shit fury all over you and you will drown in it. You're fucking dead, kiddo.</p>";
                echo '<form action="upload.php" method="post" enctype="multipart/form-data" style="text-align: center;">
                    Select file to upload:
                    <input type="file" name="fileToUpload" id="fileToUpload" style="font-family:consolas;">
                    <br><br>
                    <input class="button" type="submit" style="font-family:consolas;" value="Upload file" name="submit">
                </form>';
            }
        } else {
            
            echo '<h1>Welcome, Guest</h1>';
            echo '<p style="text-align: center;">Please login or signup to access features.</p>';
        }
        ?>
    </div>

    <script>
        document.querySelector('form[enctype="multipart/form-data"]')?.addEventListener('submit', function(e) {
            e.preventDefault();

            var formData = new FormData(this);

            fetch('upload.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                alert(data.message);
                if (data.success) {
                    location.reload();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while uploading the file.');
            });
        });
    </script>
</body>

</html>