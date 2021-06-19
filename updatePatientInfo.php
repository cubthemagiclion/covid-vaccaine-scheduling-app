<?php
// Initialize the session
require_once("config.php");
session_start();

if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}
$patient_name_err= $patient_DOB_err=$ssn_err=$patient_address_err=$patient_phone_err=$patient_email_err=$max_travel_distance_err="";


 $patient_name= $patient_DOB=$snn= $patient_address=$patient_phone=$patient_email=$max_travel_distance = "";

  $param_id= "";




?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Update Patient Info</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body{ font: 14px sans-serif; text-align: center; }
        .wrapper{ width: 350px; padding: 20px; }
    </style>
</head>
<body>
	<p>Go Back to <a href="patientView.php">Profile</a>.</p>
	<?php

	$sql = "SELECT patient_name, patient_DOB, ssn, patient_address, patient_phone, patient_email, max_travel_distance FROM Patients WHERE user_id= ? ";

  
 	
  if($stmt= mysqli_prepare($link, $sql)){
  	mysqli_stmt_bind_param($stmt, "s", $param_username);
  	
  	$param_username = $_SESSION["id"];
  	if(mysqli_stmt_execute($stmt)){


  		mysqli_stmt_store_result($stmt);
  		if(mysqli_stmt_num_rows($stmt) == 1){  
  			mysqli_stmt_bind_result($stmt, $patient_name, $patient_DOB, $ssn, $patient_address, $patient_phone, $patient_email, $max_travel_distance);

  			mysqli_stmt_fetch($stmt);


  		 }
  	}

  }

  if($_SERVER["REQUEST_METHOD"] == "POST"){
  		echo "hello";
  	if(empty(trim($_POST["patient_name"]))){
        $patient_name_err = "Please enter your name.";     
    }else{
        $patient_name=trim($_POST["patient_name"]);
    }
     if(empty(trim($_POST["patient_DOB"]))){
        $patient_DOB_err = "Please enter your DOB.";     
    }else{
        $patient_DOB=trim($_POST["patient_DOB"]);
    }
     if(empty(trim($_POST["ssn"]))){
        $ssn_err = "Please enter your SSN.";     
    }else{
        $ssn=trim($_POST["ssn"]);
    }
     if(empty(trim($_POST["patient_address"]))){
        $patient_address_err = "Please enter your address.";     
    }else{
        $patient_address=trim($_POST["patient_address"]);
    }
     if(empty(trim($_POST["patient_phone"]))){
        $patient_phone_err = "Please enter your phone number.";     
    }else{
        $patient_phone=trim($_POST["patient_phone"]);
    }
     if(empty(trim($_POST["patient_email"]))){
        $patient_email_err = "Please enter your email.";     
    }else{
        $patient_email=trim($_POST["patient_email"]);
    }
     if(empty(trim($_POST["max_travel_distance"]))){
        $max_travel_distance_err = "Please enter your Max Distance.";     
    }else{
        $max_travel_distance=trim($_POST["max_travel_distance"]);
    }

    if(empty($patient_name_err) && empty($patient_address_err) && empty($patient_email_err)&& empty($patient_phone_err)&& empty($patient_DOB_err)&& empty($ssn_err)&& empty($max_travel_distance_err)){
    	$newDOB= new DateTime($patient_DOB);
        $now= new DateTime();
        $difference = $now->diff($newDOB);
        $age = $difference->y;
        if($age>74){
            $groupId=2;
         }else if($age<75 and $age>64){
            $groupId=3;
        }else if($age>15 and $age<65){
            $groupId=4;
        }
    	$sql2 = "UPDATE Patients SET patient_name = '$patient_name',patient_DOB='$patient_DOB', ssn='$ssn', patient_address='$patient_address',patient_phone='$patient_phone', max_travel_distance= '$max_travel_distance', groupId='$groupId' WHERE user_id = ?";
    	//echo $sql2;

    	//echo $sql;
        if($stmt = mysqli_prepare($link, $sql2)){

        	mysqli_stmt_bind_param($stmt, "s", $param_id);

        	$param_id = $_SESSION['id'];
        	 if(mysqli_stmt_execute($stmt)){

        	 	header("location: patientView.php");

        	 }

        }


    }
  }


  


	?>
	<div class="wrapper">
    <h1> Update Your Info</h1>
    <br> <br>
    <p>Make your changes and click the Update button.</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                <label>Your Name</label>
                <input type="text" name="patient_name" class="form-control <?php echo (!empty($patient_name_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $patient_name; ?>">
                <span class="invalid-feedback"><?php echo $patient_name_err; ?></span>
            </div>
            <div class="form-group">
                <label>Date of Birth(YYYY-MM-DD)</label>
                <input type="text" name="patient_DOB" class="form-control <?php echo (!empty($patient_DOB_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $patient_DOB; ?>">
                <span class="invalid-feedback"><?php echo $patient_DOB_err; ?></span>
            </div>
            <div class="form-group">
                <label>SSN (9 numbers, no dashes)</label>
                <input type="text" name="ssn" class="form-control <?php echo (!empty($ssn_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $ssn; ?>">
                <span class="invalid-feedback"><?php echo $ssn_err; ?></span>
            </div>
            <div class="form-group">
                <label>Address</label>
                <input type="text" name="patient_address" class="form-control <?php echo (!empty($patient_address_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $patient_address; ?>">
                <span class="invalid-feedback"><?php echo $patient_address_err; ?></span>
            </div>
            <div class="form-group">
                <label>Phone Number</label>
                <input type="text" name="patient_phone" class="form-control <?php echo (!empty($patient_phone_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $patient_phone; ?>">
                <span class="invalid-feedback"><?php echo $patient_phone_err; ?></span>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="text" name="patient_email" class="form-control <?php echo (!empty($patient_email_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $patient_email; ?>">
                <span class="invalid-feedback"><?php echo $patient_email_err; ?></span>
            </div>
            <div class="form-group">
                <label>Max Travel Distance</label>
                <input type="text" name="max_travel_distance" class="form-control <?php echo (!empty($max_travel_distance_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $max_travel_distance; ?>">
                <span class="invalid-feedback"><?php echo $max_travel_distance_err; ?></span>
            </div>
            <input type="submit" class="btn btn-primary" value="Update">
        </form>
    </div>
</body>
</html>