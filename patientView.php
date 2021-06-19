<?php
// Initialize the session
//require_once "config.php";
session_start();
require_once "config.php";
 
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}
$days = [
  0=> 'Monday',
  1=> 'Tuesday',
  2=> 'Wednesday',
  3=> 'Thursday',
  4 => 'Friday',
  5=> 'Saturday',
  6=> 'Sunday'
];

?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Welcome</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body{ font: 14px sans-serif; text-align: center; }
        table.center {
        margin-left: auto; 
        margin-right: auto;
        }
        table, th, td {
         border: 1px solid black;
        border-collapse: collapse;
        }
    </style>
</head>
<body>
    <h1 class="my-5">Hi, <b><?php echo htmlspecialchars($_SESSION["username"]); ?></b>. Welcome to our site.</h1>
    <h2>Your Availability:</h2>
    <p>
        <?php 
        $patientId='';
        $sql= "SELECT patientId FROM Patients WHERE user_Id = ? ";
        //echo $sql;
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            //echo "hi";
            
            mysqli_stmt_bind_param($stmt, "s", $param_username);
           
            // Set parameters
            //echo "hi";
           
            $param_username = $_SESSION["id"];
            //echo "hi";
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Store result
                //echo "hi";
                mysqli_stmt_store_result($stmt);
                //echo "hi";
                // Check if username exists, if yes then verify password
                if(mysqli_stmt_num_rows($stmt) == 1){                    
                    // Bind result variables
                    //echo "hi";
                    mysqli_stmt_bind_result($stmt,$patientId) ;
                    //echo "hi";
                    if(mysqli_stmt_fetch($stmt)){
                    $_SESSION['patientId']= $patientId;
                    //echo $patientId;

                    }
                }
            }
        }
            $str = $_SESSION["patientId"];
            //echo $str;
            $query= "SELECT c.timeSlot, c.day FROM PatientAvailability as Pa JOIN Calendar as c on Pa.timeSlotId = c.timeSlotId WHERE Pa.patientId=$str " ;
            //echo $query;
            $result = mysqli_query($link, $query);
            //echo "Returned rows are: " . mysqli_num_rows($result);
            
        ?>
        <table class="center">
            <tr>
                    <th>time</th>
                    <th>Day of the Week</th>
                    
            </tr>
            <?php 
            
                while($row = mysqli_fetch_assoc($result) ){?>
                    <tr>

                    <td><?php echo $row['timeSlot'] ;?></td>
                    <td><?php echo $days[$row['day']] ;?></td>

                    </tr>
            


<?php
                
            }
            ?>
        </table> <br>
        <a href="updateAvailability.php" class="btn btn-primary"> Update Availability</a> <?php  ?>
    </p>
   <h3> Upcoming Appointments</h3>
   <p>
        <?php 
            $str = $_SESSION["patientId"];
            //echo $str;
            $query= "SELECT a.Date, a.startTime, p.provider_name, p.provider_address, a.appointmentId FROM patientReceivedAppointment as Pa JOIN Appointment as a on Pa.appointmentId = a.appointmentId JOIN Providers as p on a.providerId = p.providerId WHERE Pa.patientId=$str and Pa.status='Accepted' ";
            //echo $query;
            $result = mysqli_query($link, $query);
            //echo "Returned rows are: " . mysqli_num_rows($result);
            
        ?>
        <table class="center">
            <tr>
                    <th>Appointment Date</th>
                    <th>Time</th>
                    <th>Provider Name</th>
                    <th>Provider Address</th>
                    <th></th>
                    
            </tr>
            <form method ="post" action="#">
            <?php 
            
                while($row = mysqli_fetch_assoc($result) ){?>
                    <tr>

                    <td><?php echo $row['Date'] ;?></td>
                    <td><?php echo $row['startTime'] ;?></td>
                    <td><?php echo $row['provider_name'];?></td>
                    <td><?php echo $row['provider_address'];?></td>
                    <td><button type="submit" id="cancel" name ="cancel[]" value="<?php echo $row['appointmentId']?>">Cancel</button>
                    </td>
                    </tr>
            


<?php
                
            }
            ?>
        </table> <br>
    </p>
</form>

    <h3>Offered Appointments</h3>
     <p>
        <?php 
            $str = $_SESSION["id"];
            //echo $str;
            $query= "SELECT a.Date, a.startTime, p.provider_name, p.provider_address, a.appointmentId FROM patientReceivedAppointment as Pa JOIN Appointment as a on Pa.appointmentId = a.appointmentId JOIN Providers as p on a.providerId = p.providerId WHERE Pa.patientId=$str and Pa.status='Offer Sent' ";
            //echo $query;
            $result = mysqli_query($link, $query);
            //echo "Returned rows are: " . mysqli_num_rows($result);
            
        ?>
        <table class="center">
            <tr>
                    <th>Appointment Date</th>
                    <th>Time</th>
                    <th>Provider Name</th>
                    <th>Provider Address</th>
                    <th></th>
                    <th></th>
                    
            </tr>
            <form method ="post" action="#">
            
            <?php 
            
                while($row = mysqli_fetch_assoc($result) ){?>
                    <tr>

                    <td><?php echo $row['Date'] ;?></td>
                    <td><?php echo $row['startTime'] ;?></td>
                    <td><?php echo $row['provider_name'];?></td>
                    <td><?php echo $row['provider_address'];?></td>
                    <td><button type="submit" id="accept" name ="accept[]" value="<?php echo $row['appointmentId']?>">Accept</button>
                    </td>
                    
                    <td><button type="submit" id="decline" name ="decline[]" value="<?php echo $row['appointmentId']?>">Decline</button>
                    </td>
                    </tr>
            


<?php
                
            }
            ?>
        </table> <br>
    </p>
</form>
    <p>
        <a href="updatePatientInfo.php" class="btn btn-primary"> Update Your Info</a>
    </p>
    <p>
        <a href="reset-password.php" class="btn btn-warning">Reset Your Password</a>
        <a href="logout.php" class="btn btn-danger ml-3">Log Out</a>
    </p>

<?php

if(isset($_POST['cancel'])){
    //echo "hello";
    foreach($_POST['cancel'] as $value){
        //echo "test";
        //echo $value;
        $sql = "UPDATE patientReceivedAppointment SET status = 'Cancelled' WHERE patientId=? and AppointmentId=?";
        if($stmt = mysqli_prepare($link, $sql)){
            mysqli_stmt_bind_param($stmt,"ii",$param_patientId, $param_AppointmentId);
            $param_patientId=$patientId;
            $param_AppointmentId=$value;
            if(mysqli_stmt_execute($stmt)){
                header("location:patientView.php");
            }
        }

    }   
}//else{echo "hi";}

if(isset($_POST['accept'])){
    //echo "hello";
    foreach($_POST['accept'] as $value){
        //echo "test";
        //echo $value;
        $sql = "UPDATE patientReceivedAppointment SET status = 'Accepted' WHERE patientId=? and AppointmentId=?";
        if($stmt = mysqli_prepare($link, $sql)){
            mysqli_stmt_bind_param($stmt,"ii",$param_patientId, $param_AppointmentId);
            $param_patientId=$patientId;
            $param_AppointmentId=$value;
            if(mysqli_stmt_execute($stmt)){
                header("location:patientView.php");
            }
        }

    }   
}
if(isset($_POST['decline'])){
    //echo "hello";
    foreach($_POST['decline'] as $value){
        //echo "test";
        //echo $value;
        $sql = "UPDATE patientReceivedAppointment SET status = 'Declined' WHERE patientId=? and AppointmentId=?";
        if($stmt = mysqli_prepare($link, $sql)){
            mysqli_stmt_bind_param($stmt,"ii",$param_patientId, $param_AppointmentId);
            $param_patientId=$patientId;
            $param_AppointmentId=$value;
            if(mysqli_stmt_execute($stmt)){
                header("location:patientView.php");
            }
        }

    }   
}
?>
</body>
</html>