<!DOCTYPE html>

<html>
<?php

include ("config.php");
include "function.php";
session_start();
/*
Providers might first
land on a page that shows some summary information such as how many appointments have
been accepted, cancelled, etc. Then they should be able to input additional appointments, or
list all appointments by certain fields and tags -- e.g., list all accepted upcoming appointments
sorted by appointment or acceptance time and date, or list all cancelled appointments by time
and date of the cancellation. You can decide what is the best interface for such as system.
*/
//check if the user exists and prints out username, if not redirects back to homepage
if ($stmt = $link->prepare("select username from users where user_id = ? AND account_type = 'provider';")) {
  $stmt->bind_param("s", $_SESSION["user_id"]);
  $stmt->execute();
  $stmt->bind_result($username);
  if($stmt->fetch()) {
	$username = htmlspecialchars($username);
	echo "<title>$username's Appointments Details</title>\n";
	echo "$username's Appointments Details: <br><br><br>";
  }
  else {
    echo "user not found. \n";
    echo "You will be redirected in 3 seconds or click <a href=\"index.php\">here</a>.\n";
    header("refresh: 3; index.php");
  }
  $stmt->close();
}




if(!isset($_SESSION["username"])) {
  echo "You are not logged in. ";
  echo "You will be returned to the homepage in 3 seconds or click <a href=\"index.php\">here</a>.\n";
  header("refresh: 3; index.php");
}
else {

  //if the user have entered a message, insert it into database

  //if not then display the form for posting message
  
    echo '<form action="view.php" method="POST">';
/*
    echo 'Select Status:    Cancelled<input type="checkbox" name="status_selection" value = "Cancelled"/>';

    echo '   Accepted: <input type="checkbox" name="status_selection" value = "Accepted"/>';

    echo '   All: <input type="checkbox" name="status_selection" value = "all"/>';*/
echo '<select name="status_selection" id="status_selection">
<option value="">--Please choose the status you want to see--</option>
<option value="all">All</option>
<option value="Accepted">Accepted</option>
<option value="Cancelled">Cancelled</option>
<option value="No Show">No Show</option>
<option value="Completed">Completed</option>
<option value="Offer Sent">Offer Sent, No Patient Accepted Yet</option>
</select>';
	echo '   <input type="submit" value="Submit" />';
    echo "\n";
	echo '</form>';
	echo "<br>";



  if(isset($_POST["status_selection"])) {
    $selected = $_POST["status_selection"];
    appointment_of_status($link,$selected);

    /*
    $address = nl2br(htmlspecialchars($_POST["message"]));
    //insert into database, note that message_id is auto_increment and time is set to current_timestamp by default
    if ($stmt = $mysqli->prepare("update patients set patient_address = ? WHERE user_id = ?")) {
      $stmt->bind_param("is", $address,$_SESSION["user_id"] );
      $stmt->execute();
      $stmt->close();
	  $user_id = htmlspecialchars($_SESSION["user_id"]);
	  echo "Your address is updated. \n";
      echo "You will be returned to your blog in 3 seconds or click <a href=\"view.php?user_id=$user_id\">here</a>.";
      header("refresh: 3; view.php?user_id=$user_id");
    }  */
  }

}
echo '<br /><a href="index.php">Go back</a>';
$link->close();
?>

</html>