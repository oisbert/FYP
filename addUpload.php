<?php   
        include ("validateLoggedIn.php");
        include ("serverConfig.php");
        $conn = new mysqli($DB_SERVER, $DB_USERNAME, $DB_PASSWORD, $DB_DATABASE);
        if ($conn -> connect_error) {
            die("Connection failed:" .$conn -> connect_error);
        }
        $currentUser = $_SESSION['user'];
        $taskID = $_GET['taskID'];
        $taskTitle= $_GET['taskTitle'];
        $status= $_GET['status'];

        $targetDir = "taskUploads/{$taskTitle}/{$currentUser}";

        // Checking whether a file is directory or not
        if (is_dir($targetDir)){
            echo ("Given $gfg_directory exists");
        }else{
            mkdir("taskUploads/{$taskTitle}/{$currentUser}", 0700);
        }

        $userDirectory = "taskUploads/{$taskTitle}/{$currentUser}/";
        $fileName = NULL;
        $fileName = basename($_FILES["file"]["name"]);
        $targetFilePath = $userDirectory.$fileName;
        $fileType = pathinfo($targetFilePath,PATHINFO_EXTENSION);
        $allowTypes = array('jpg','png','jpeg','gif','pdf','java');

        move_uploaded_file($_FILES["file"]["tmp_name"],$targetFilePath);

      
        if(in_array($fileType, $allowTypes) OR $fileName == NULL){
        $sql = "INSERT INTO taskstatus ( userID, taskID, status, filePathUser)
                VALUES ('{$currentUser}', '{$taskID}', '{$status}', '{$fileName}')";

        if ($conn->query($sql) === TRUE) {
            echo "The file ".$fileName. " has been uploaded successfully.".$status;
        } 
        else {
            echo "Error: " . $sql . "<br>" . $conn->error;
         }

        $conn->close();
        }
        else{
            echo "Sorry, only JPG, JPEG, PNG, GIF, & PDF files are allowed to upload.";
        }
?>