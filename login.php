<html>
    <head>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">
        <link rel="stylesheet" type="text/css" href="css/login.css?v=<?php echo time(); ?>">
        <link rel="shortcut icon" type="image/x-icon" href="favicon.ico"/>

        <meta content="width=device-width, initial-scale=1" name="viewport" />

        <title>Login</title>

        <script> 
            function showLoginError(variable) {
                var alert = document.getElementById("alert");
                var node = document.createTextNode(variable);
                alert.appendChild(node);

            }
        </script>

    </head>
    <body>
    <div class="bg-image"></div>
        <div class="wrapper">
            <div class="banner">
                    <div class="login">
                        <h2 style="text-align: left;">Login</h2>
                        <form method="post" action="login.php">
                            <input class="input" type="email" name="email" placeholder="Email" required
                                value="<?php if(isset($_COOKIE['email'])){echo $_COOKIE['email'];} else{echo "";} ?>">
                            <br>
                            <input class="input" type="password" name="pass" placeholder="Password" required
                                id="password">
                            <br>
                            <h6 id="alert"></h6>
                            <input class="button" name="submit" type="submit" value="Login" style="margin-left: 25%;">
                        </form>
                            <div class="buttons">
                                <div class="register">
                                    <h5>New Here?</h5>
                                    <button class="button" onClick="location.href='register.php'">Register</button>
                                </div>
                            </div>
                        <a href="teacherLogin.php" style="color: rgb(27, 21, 21);">Are you a Teacher? Click here</a>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>

<?php

    if(isset($_POST['submit'])) {

        include ("serverConfig.php");
        include ("randomFileGenerator.php");
        include ("IDtoLetter.php");
        $conn = new mysqli($DB_SERVER, $DB_USERNAME, $DB_PASSWORD, $DB_DATABASE);
        if ($conn -> connect_error) {
            die("Connection failed:" .$conn -> connect_error);
        }

        session_start();
        session_unset();

        $email = $_POST['email'];
        $password = $_POST['pass'];

        setcookie("email", $email, time()+250);
        
        $sql = "select * from users where email=\"{$email}\";";
        $result = $conn -> query($sql);
        $row = $result->fetch_assoc();
        $userID = $row["userID"];
        $sqlEmail = $row["email"];
        $sqlPass = $row["password"];
        $isAdmin = $row["admin"];

        $bannedSql = "SELECT * FROM banneduser 
                      WHERE userID = {$userID};";
        $bResult = $conn -> query($bannedSql);
        $bRow = $bResult->fetch_assoc();

        function emailMatches ($inputEmail, $DBEmail) {
            return strcasecmp($inputEmail, $DBEmail) == 0;
        }
        
        if(mysqli_num_rows($bResult) !== 0 ) {
            echo "<script> showLoginError('This user is banned') </script>";
        }
        else if($isAdmin == 1 && emailMatches($email, $sqlEmail) && password_verify($password, $sqlPass)) {
            $_SESSION['user'] = $userID;
            $_SESSION['username'] = $row['username'];
            $_SESSION['loggedin'] = true;
            $_SESSION['admin'] = true;
            header( "Location: admin.php" );
        }
        else if(emailMatches($email, $sqlEmail)  && password_verify($password, $sqlPass)) {
            $_SESSION['user'] = $userID;
            $_SESSION['username'] = $row['username'];
            $_SESSION['loggedin'] = true;

            $sqlPoints = "UPDATE users SET pointtracker = 1";

            if ($conn->query($sqlPoints) === TRUE) {
                echo "New record created successfully";
              } else {
                echo "Error: " . $sqlPoints . "<br>" . $conn->error;
              }

            $userIDtoLetters = num2alpha($userID);
            $randomFile = randomFileGenerator();
            $userPoly = fopen("{$userIDtoLetters}Random.java", "w+");
            $current = file_get_contents("{$randomFile}", "w");
            $userPolyEdit = fopen("{$userIDtoLetters}Random.java", "w");
            fwrite($userPolyEdit, $current);
            $holder = file_get_contents("{$userIDtoLetters}Random.java");
            $filename_without_ext = basename($randomFile, '.java');
            $fileNameFixed = strstr($filename_without_ext, '_', true);
            echo $filename_without_ext;
            $_SESSION['varname'] = $filename_without_ext;
            $replace = str_replace("{$fileNameFixed}", "{$userIDtoLetters}Random",$holder);
            file_put_contents("{$userIDtoLetters}Random.java", $replace);
            fclose($userPolyEdit);
            $classPoly = fopen("{$userIDtoLetters}Random.class", "w");
            header( "Location: lesson.php" );
        }
        else {
            echo "<script> showLoginError('Incorrect email or password') </script>";
        }

        $conn->close(); 
    }

?>

    <form method="post" action="exercisePage.php">
            <input type="hidden" name="varname" value="<?php echo $filename_without_ext; ?>">
            <input type="submit">
        </form>