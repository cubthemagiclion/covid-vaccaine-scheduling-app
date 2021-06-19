<?php
// Initialize the session
include "config.php";
session_start();
 $days = [
  0=> 'Monday',
  1=> 'Tuesday',
  2=> 'Wednesday',
  3=> 'Thursday',
  4 => 'Friday',
  5=> 'Saturday',
  6=>'Sunday'
];

if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}



?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Update Availability</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body{ font: 14px sans-serif; text-align: center; }
    </style>
    
</head>
<body>


    <h1> Update Availability</h1>
    <p>
    	<p>Go Back to <a href="patientView.php">Profile</a>.</p>
    	<?php 
    		$patientId = $_SESSION["patientId"];
    		$query= "SELECT timeSlotId FROM PatientAvailability WHERE patientId = $patientId ";
    		$result= mysqli_query($link, $query);
    		while($row = mysqli_fetch_assoc($result)){
    			$new_array[]=$row['timeSlotId'];
    		}
    		//echo $new_array;
  
            $query2= "SELECT timeSlotId,timeSlot, day FROM Calendar";
            $result2= mysqli_query($link, $query2);
            //$query2->execute();
                    ?>
        <form method= "post" action ="#">
        	
        		<?php
        		while ( $row2= mysqli_fetch_assoc($result2)) {
        			if(in_array($row2['timeSlotId'], $new_array)){

        			?>

        			<div>

        			<input type="checkbox" id="slot" name="slot[]" value="<?php echo $row2['timeSlotId']?>" checked>
        			<label for="slot"><?php echo $row2['timeSlot'].$days[$row2['day']]?></label>
        		</div>
        		<?php
        			}
        			else{

        				?>
        				<div>

        			<input type="checkbox" id="slot" name="slot[]" value="<?php echo $row2['timeSlotId']?>">
        			<label for="slot"><?php echo $row2['timeSlot'].$days[$row2['day']]?></label>
        		</div>

        				<?php 
        			}
        		}
        		?>
        	
        		<input type="submit" name = 'update' class="btn btn-primary" value="Update">
        	

        </form>


    </p>
    <?php
if(isset($_POST['update'])){

	
	if(!empty($_POST['slot'])){
		//$values= $_POST['slot'];

		$sql = "DELETE FROM PatientAvailability WHERE patientId= $patientId";
		
		if(mysqli_query($link, $sql)){
  			
  			

    			foreach($_POST['slot'] as $value){
    				//echo "hi";
 					$sql2="INSERT INTO PatientAvailability (patientId, timeSlotId) VALUES (?,?)";
 					//echo "test1";
  					if($stmt= mysqli_prepare($link, $sql2)){
  						//echo "test 3";
  						mysqli_stmt_bind_param($stmt, "ii", $param_patientId, $param_timeSlotId);
  						$param_patientId=$patientId;
  						$param_timeSlotId=$value;

  						mysqli_stmt_execute($stmt);
    					mysqli_stmt_close($stmt);

 				
 				}
 			}

 			header("location: patientView.php");
 		}
 	}
	
	 else {echo "check at least one time Slot";}
	
}
 

?>
</body>
</html>
