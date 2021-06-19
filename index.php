<!DOCTYPE html>
<!-- Example Blog written by Raymond Mui -->
<html>
<title>Main Page</title>
 <p>Want to go home? <a href="home.php">Press here</a>.</p>

<?php

include ("config.php");
include "function.php";
session_start();

if(!isset($_SESSION["username"])) {
  echo "Welcome to the providers portal, you are not logged in. <br /><br >\n";
  echo 'You can <a href="loginProvider.php">login</a> here or <a href="registerProvider.php">register</a> if you don\'t have a provider account yet.';
  echo "\n";

}
else {
  $username = htmlspecialchars($_SESSION["username"]);
  echo "Welcome $username. You are logged in.<br /><br />\n";
  echo 'You may check <a href="view.php?user_id=';
  echo htmlspecialchars($_SESSION["user_id"]);
  echo '">appointments details</a>, or <a href="insertAppointment.php">insert more appointments</a>, or <a href="logoutProvider.php">logout</a>.';
  echo "\n";
  echo 'Or you can <a href = "completeProviderInfo.php">complete your provider information </a> if you have not done so.';
}
echo "<br /><br />\n";


if(isset($_SESSION["user_id"])) {
  $current_user_id = $_SESSION["user_id"];
if ($stmt = $link->prepare("WITH pid AS
(select providerId from providers where user_id = ?)
select count(*),status from pid natural join Appointment natural join PatientReceivedAppointment group by status;")) {
  $stmt->bind_param("i", $current_user_id);
  $stmt->execute();
  $stmt->bind_result($appointmentCount, $status);
    echo "Summary of your appointments:<br>";
    while ($stmt->fetch()) {
      //echo '<a href="view.php?user_id=';
    $status = htmlspecialchars($status);
    echo "Number of $status Appointments: ";
    echo htmlspecialchars($appointmentCount);
    echo "<br />";
    }
  

  $stmt->close();
  $link->close();
}
}

?>

</html>