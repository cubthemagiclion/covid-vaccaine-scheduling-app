<!DOCTYPE html>
<!-- Example Blog written by Raymond Mui -->
<html>
<title>Update Provider Information</title>

<?php

include "include.php";
session_start();
echo "current session's user_id is ";
echo $_SESSION["user_id"];
echo "current get id is ";

//check if the user is logged in.
if(isset($_SESSION["user_id"])) {
    echo 'Please complete your provider information: ';

  //if the user have entered _all_ entries in the form, insert into database
  if(isset($_POST["providername"]) && isset($_POST["providerphone"]) 
  && isset($_POST["provideraddress"])  && isset($_POST["providertype"]) ) {
    //check if providername already exists in database
    /*
    if ($stmt = $mysqli->prepare("select provider_name from providers where provider_name = ?")) {
      $stmt->bind_param("s", $_POST["providername"]);
      $stmt->execute();
      $stmt->bind_result($providername);
        if ($stmt->fetch()) {
          echo "That username already exists. ";
          echo "You will be redirected in 3 seconds or click <a href=\"register.php\">here</a>.";
          header("refresh: 3; register.php");
		  $stmt->close();
        }
		//if not then insert the entry into database, note that user_id is set by auto_increment
		else {
		    $stmt->close();*/
		    if ($stmt = $link->prepare("insert into providers 
            (provider_name,provider_phone,provider_address,providerType,user_id) values (?,?,?,?,?);")) {
              $stmt->bind_param("ssssi", $_POST["providername"], $_POST["providerphone"],$_POST["provideraddress"], $_POST["providertype"],$_SESSION["user_id"]);
              //$stmt->bind_param("ss", $_POST["username"], md5($_POST["password"]));
              $stmt->execute();
              $stmt->close();
              echo "Information update complete, click <a href=\"index.php\">here</a> to return to homepage."; 
          }		  
        }	 
	
  //}
  //if not then display registration form
  else {
    echo "Enter your information below: <br /><br />\n";
    echo '<form action="completeProviderInfo.php" method="POST">';
    echo "\n";	
  
    echo 'Provider Name: <input type="text" name="providername" /><br />';
    echo "\n";
    echo 'Provider Phone Number: <input type="text" name="providerphone" /><br />';
    echo "\n";
    echo 'Provider Phone Address: <input type="text" name="provideraddress" /><br />';
    echo "\n";
    echo 'Provider Type: <input type="text" name="providertype" /><br />';
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