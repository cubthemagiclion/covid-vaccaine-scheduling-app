<!DOCTYPE html>

<html>
<?php

function appointment_of_status($conn, $st) {
  if(isset($_SESSION["user_id"])) {
    if($st == "all"){
        $sql = "WITH pid AS
        (select providerId from providers where user_id = ?)
        select A.appointmentId, A.Date, A.startTime, P.status, P.offerSentTime, P.responseReceivedTime 
        from pid natural join Appointment AS A natural join PatientReceivedAppointment AS P;";
        $current_user_id = $_SESSION["user_id"];
        if ($stmt = $conn->prepare($sql)) {
          $stmt->bind_param("i", $current_user_id);}
    }else{
        $sql = "WITH pid AS
        (select providerId from providers where user_id = ?)
        select A.appointmentId, A.Date, A.startTime, P.status, P.offerSentTime, P.responseReceivedTime 
        from pid natural join Appointment AS A natural join PatientReceivedAppointment AS P
        Where P.status = ?;";
        $current_user_id = $_SESSION["user_id"];
        if ($stmt = $conn->prepare($sql)) {
          $stmt->bind_param("is", $current_user_id,$st);}
    }
    $stmt->execute();
    $stmt->bind_result($appid,$appDate,$startTime,$status,$offerSentTime,$responseReceivedTime);
 
    echo '<table style="border-collapse: collapse;
    margin: 25px 0;
    font-size: 0.9em;
    font-family: sans-serif;
    min-width: 500px;
    box-shadow: 0 0 20px rgba(0, 0, 0, 0.15);" border="2" width="80%">';
    echo '<tr style = "color:#009879;font-weight:bold;text-align:center;"><td style ="padding: 0px 10px;">Appointment ID:</td> <td style="padding: 12px 15px;">Date</td><td style ="padding: 0px 10px;">Start Time</td><td style="padding: 12px 15px;">Status</td><td style="padding: 12px 15px;">Offer Sent Time</td><td style="padding: 12px 15px;">Patient Response Received Time</td></tr>';
    while ($stmt->fetch()) {
      echo "<tr style = 'border-bottom: 1px solid #dddddd;background-color: #009879;
      color: #ffffff;
      text-align: center;'><td style='padding: 0px 50px;'>$appid</td> <td style='padding: 12px 15px;'>$appDate</td> <td style='padding: 12px 15px;'>$startTime</td> <td style='padding: 12px 15px;'>$status</td> <td style='padding: 12px 15px;'>$offerSentTime</td> <td style='padding: 12px 15px;'>$responseReceivedTime</td></tr>";
    }
    echo "</table>";

    $stmt->close();
    $conn->close();
  }
  }


/*
if ($stmt = $mysqli->prepare("select patient_name, ssn, patient_address from patients where user_id = ?")) {
    $stmt->bind_param("i", $_GET["user_id"]);
    $stmt->execute();
    $stmt->bind_result($patient_name,$ssn,$address);
    while($stmt->fetch()) {
      $patient_name = nl2br(htmlspecialchars($patient_name)); //nl2br function replaces \n and \r with <br />
      $ssn = htmlspecialchars($ssn);
    $address = nl2br(htmlspecialchars($address));
      echo '<table border="2" width="30%"><tr><td>';
      echo "\n";
      echo "SSN of $ssn, $username full name is: </td></tr>
    <tr><td><br />$patient_name<br /><br /></td></tr>
    <tr><td><br />$address<br /><br /></td></tr>
    </table><br />\n";
    }
    $stmt->close();
  }
  $mysqli->close();*/
?>
</html>