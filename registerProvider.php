<!DOCTYPE html>
<!-- Example Blog written by Raymond Mui -->
<html>
<title>Register</title>

<?php

include "config.php";
session_start();
//if the user is already logged in, redirect them back to homepage
if(isset($_SESSION["username"])) {
  echo "You are already logged in. ";
  echo "You will be redirected in 3 seconds or click <a href=\"index.php\">here</a>.";
  header("refresh: 3; index.php");
}
else {
  //if the user have entered _all_ entries in the form, insert into database
  if(isset($_POST["username"]) && isset($_POST["password"])) {

    //check if username already exists in database
    if ($stmt = $link->prepare("select username from users where username = ?")) {
      $stmt->bind_param("s", $_POST["username"]);
      $stmt->execute();
      $stmt->bind_result($username);
        if ($stmt->fetch()) {
          echo "That username already exists. ";
          echo "You will be redirected in 3 seconds or click <a href=\"registerProvider.php\">here</a>.";
          header("refresh: 3; registerProvider.php");
		  $stmt->close();
        }
		//if not then insert the entry into database, note that user_id is set by auto_increment
		else {
		    $stmt->close();
		    if ($stmt = $link->prepare("insert into users (username,password,account_type) values (?,?,'provider')")) {
              $stmt->bind_param("ss", $_POST["username"], ($_POST["password"]));
              //$stmt->bind_param("ss", $_POST["username"], md5($_POST["password"]));
              $stmt->execute();
              $stmt->close();
              echo "Registration complete, click <a href=\"index.php\">here</a> to return to homepage."; 
          }		  
        }	 
	}
  }
  //if not then display registration form
  else {
    echo "Enter your information below: <br /><br />\n";
    echo '<form action="registerProvider.php" method="POST">';
    echo "\n";	
    echo 'Username: <input type="text" name="username" /><br />';
    echo "\n";
	  echo 'Password: <input type="password" name="password" /><br />';
    echo "\n";

	echo '<input type="submit" value="Submit" />';
    echo "\n";
	echo '</form>';
	echo "\n";
	echo '<br /><a href="index.php">Go back</a>';

  }
}
$link->close();


?>


</html>