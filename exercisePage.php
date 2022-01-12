<!DOCTYPE html>
<html>
   <head>
      <title>
         Embedding an online compiler 
         into a website
      </title>
      </link>
      <script src="https://cdn.jsdelivr.net/npm/animejs@3.0.1/lib/anime.min.js"></script>
   </head>
   <link rel="stylesheet" type="text/css" href="css/excercisePage.css?v=<?php echo time(); ?>">
   <body>

<script>

   function AddPoints() {
            window.location.href= 'addpoints.php';
        }

</script>

      <?php
      include ("header.html");
      include ("validateLoggedIn.php");
      include ("serverConfig.php");
      include ("IDtoLetter.php");
      include ("unlinkFile.php");
      $_SESSION['user'] = $userID;
      
      $filename_without_ext=$_SESSION['varname'];
      
      $userIDtoLetters = num2alpha($userID);

      foreach( glob('excerciseAnswers' . '/*.*') as $fileAnswer){
         if($fileAnswer == "excerciseAnswers/{$filename_without_ext}Answer.java"){
        
            $getAnswerContents = file_get_contents("{$fileAnswer}", "w");
         }
     }
     

      ?>
      <br>
      <div class = "excution" >
         <div class = excutionOutput >

            <?php
               if (file_exists("{$userIDtoLetters}Random.java")){
                   $file = "{$userIDtoLetters}Random.java";
                   $current = file_get_contents($file);
                   echo shell_exec("javac $file && java {$userIDtoLetters}Random");
                   echo shell_exec("javac {$userIDtoLetters}Random.java > log.txt 2>&1");
                   echo nl2br(file_get_contents( "log.txt" ));
               } 
               ?>
         </div>
      </div>
      <div class = "compiler">
         <form action="processCodeExercises.php" method="post" >
            <textarea data-editor = "java" rows="20" cols="55" name="comment-editor"  data-gutter="1" >
            <?php
               echo $current;
               ?>
            </textarea>
            <input type="submit" value="Compile">
         </form>
      </div>


      <?php
         //$getAnswerContents = file_get_contents("{$fileAnswer}", "w");
         $lines = explode("\n", $getAnswerContents);
         $skipped_contentAnswer = implode("\n", array_slice($lines, 2));
         $skipped_contentAnswer = preg_replace('/\s+/', '',   $skipped_contentAnswer);

         $getUserContents = file_get_contents("{$userIDtoLetters}Random.java", "w");
         $lines = explode("\n", $getUserContents);
         $skipped_contentUser = implode("\n", array_slice($lines, 3));
         $skipped_contentUser = preg_replace('/\s+/', '',  $skipped_contentUser);

         similar_text("{$skipped_contentAnswer}","{$skipped_contentUser}",$percent);

         if($percent > 90){
            //fopen("Random.java", "w+");
            //fopen("Random.class", "w");
            $RandomDelete = "Random.java";
            $RandomClassDelete = "Random.class";
            print "<H3 id = 'animation-info1' class = 'Answer-info'>Looks like your close to the answer your file was %'{$percent}' accurate to the answer ✔️</H3>";
            print "<H3 id = 'animation-info2'class = 'Answer-info'>Sit tight and we will check your output 😄</H3>";

            file_put_contents("Random.java", $getAnswerContents);

            $answer = shell_exec("javac {$userIDtoLetters}Random.java && java {$userIDtoLetters}Random");
            shell_exec("javac {$userIDtoLetters}Random.java > log2.txt 2>&1");
            $log1 = nl2br(file_get_contents( "log.txt" ));

            $answer2 = shell_exec("javac Random.java && java Random");
            shell_exec("javac Random.java > log2.txt 2>&1");
            $log2 = nl2br(file_get_contents( "log2.txt" ));

            if($answer == $answer2){
               print "<H3 id = 'animation-info3' class = 'Answer-info'>Your answer was correct ✔️</H3>";

               $ButtonSQL = "SELECT pointtracker FROM users WHERE userID = {$userID} AND pointtracker = 0;";
                    $ButtonSQLResult = $conn -> query($ButtonSQL);
                    $ButtonSQL = $ButtonSQLResult->fetch_assoc();

               if($ButtonSQL) {
                  print "<button type ='button' id = 'animation-info6' class='submitpoints' disabled '>Already submitted points</button> ";
               } else {
                  print "<button type ='button' id = 'animation-info6' class='submitpoints' onClick='AddPoints()'>Collect Points</button>";
               }
               unlinkFiles($RandomDelete, $RandomClassDelete);
            }
            else if($answer != $answer2){
               print "<H3 id = 'animation-info4' class = 'Answer-info'>Output is not the same as Answer try again ❌</H3>";
               
            }
            
         }
         else{
            print "<H1 id = 'animation-info5' class = 'Answer-info'>Your answer was wrong ❌</H1>";
         }
      ?>
      
  
      <script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script>
      <script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.2.6/ace.js" type="text/javascript" charset="utf-8"></script>
      <script src="js/syntax.js" type="text/javascript"></script>
      <script src="js/mode-java" type="text/javascript"></script>
      <script src="js/animatePolyLesson" type="text/javascript"></script>
   </body>
</html>
