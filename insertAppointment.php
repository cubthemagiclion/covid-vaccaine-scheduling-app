<!DOCTYPE html>
<!-- Example Blog written by Raymond Mui -->
<html>
<title>Insert more appointments</title>

<?php
date_default_timezone_set('EST');
include "config.php";
include "function.php";
session_start();

//check if the user is logged in.
if(isset($_SESSION["user_id"])) {

  //if the user have entered _all_ entries in the form, insert into database
  if(isset($_POST["Date"]) && isset($_POST["startTime"]) ) {
    $date_correct_format = $_POST["Date"];
    $time_correct_format = $_POST["startTime"];
    echo $date_correct_format;

		    if ($stmt = $link->prepare("insert into Appointment (Date, startTime, providerId) values (?,?,(select providerId from providers where user_id = ?));")) {
              $stmt->bind_param("ssi", $date_correct_format, $time_correct_format,$_SESSION["user_id"]);
              $stmt->execute();
              $stmt->close();
              echo "Insertion complete, click <a href=\"index.php\">here</a> to return to homepage."; 
          }		  
        }	 
	
  //}
  //if not then display registration form
  else {
    echo "Enter New Available Appointment Information Below: <br /><br />\n";
    echo '<form action="InsertAppointment.php" method="POST">';
    echo "\n";	
  
    echo 'Date : <input type="text" name="Date" /><br />';
    echo "\n";
    echo 'Start Time: <input type="text" name="startTime" /><br />';
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