<?php
    include ("validateLoggedIn.php");
    include ("IDtoLetter.php");
    $_SESSION['user'] = $userID;

    $userIDtoLetters = num2alpha($userID);

    $comment = $_POST["comment-editor"];
    $file = "{$userIDtoLetters}Polymorphism.java";
    file_put_contents($file,$comment);
    header('Location: polymorphism.php');
?>