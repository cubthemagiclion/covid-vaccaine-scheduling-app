<?php
// Include config file
require_once "config.php";
 
// Define variables and initialize with empty values
$username = $password = $confirm_password = $patient_name=$patient_DOB=$ssn=$patient_address=$patient_phone=$patient_email=$max_travel_distance="";
$username_err = $password_err = $confirm_password_err =$patient_name_err= $patient_DOB_err=$ssn_err=$patient_address_err=$patient_phone_err=$patient_email_err=$max_travel_distance_err="";
 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
 
    // Validate username
    if(empty(trim($_POST["username"]))){
        $username_err = "Please enter a username.";
    } 
    else{
        // Prepare a select statement
        $sql = "SELECT user_id FROM users WHERE username = ?";
        
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_username);
            
            // Set parameters
            $param_username = trim($_POST["username"]);
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                /* store result */
                mysqli_stmt_store_result($stmt);
                
                if(mysqli_stmt_num_rows($stmt) == 1){
                    $username_err = "This username is already taken.";
                } else{
                    $username = trim($_POST["username"]);
                }
            } else{

                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }

    
    
    // Validate password
    if(empty(trim($_POST["password"]))){
        $password_err = "Please enter a password.";     
    } elseif(strlen(trim($_POST["password"])) < 6){
        $password_err = "Password must have atleast 6 characters.";
    } else{
        $password = trim($_POST["password"]);
    }
    
    // Validate confirm password
    if(empty(trim($_POST["confirm_password"]))){
        $confirm_password_err = "Please confirm password.";     
    } else{
        $confirm_password = trim($_POST["confirm_password"]);
        if(empty($password_err) && ($password != $confirm_password)){
            $confirm_password_err = "Password did not match.";
        }
    }
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
    // Check input errors before inserting in database
    if(empty($username_err) && empty($password_err) && empty($confirm_password_err)&& empty($patient_name_err)&& empty($patient_address_err) && empty($patient_email_err)&& empty($patient_phone_err)&& empty($patient_DOB_err)&& empty($ssn_err)&& empty($max_travel_distance_err)){
        
        // Prepare an insert statement
        $sql = "INSERT INTO users (username, password, account_type) VALUES (?, ?, 'patient')";
         
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "ss", $param_username, $param_password);
            
            // Set parameters
            $param_username = $username;
            $param_password = $password;
            //$param_account_type= "patient";
             // Creates a password hash
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Redirect to login page
               //echo "hi";
               $sql2="SELECT user_id from users where username = ? ";
               //echo "hello";
               //echo $sql2;
               
                if($stmt2= mysqli_prepare($link,$sql2)){
                    //echo "test1";
                    mysqli_stmt_bind_param($stmt2, "s", $param_username);
                    $param_username = $username;
                }
                if(mysqli_stmt_execute($stmt2)){
                //store result 
                    //echo "test2";

                    mysqli_stmt_store_result($stmt2);
                    if(mysqli_stmt_num_rows($stmt2) == 1){
                        //echo "test 3";
                        mysqli_stmt_bind_result($stmt2,$user_id);
                        mysqli_stmt_fetch($stmt2);
                    }

                }else{
                
                echo "Oops! Something went wrong. Please try again later.";
                }

                //echo "test 4";
                $newDOB= new DateTime($patient_DOB);
                $now= new DateTime();
                $difference = $now->diff($newDOB);
                $age = $difference->y;
                //echo $age;
                $sql3="INSERT INTO Patients (patient_name, patient_DOB, ssn, patient_address, patient_phone, patient_email, max_travel_distance, user_id, groupId) VALUES (?,?,?,?,?,?,?,?,?)";
                //echo "test 5";
                if($stmt3=mysqli_prepare($link,$sql3)){
                    //echo "test 6";
                    mysqli_stmt_bind_param($stmt3, "ssisssiii", $param_patient_name, $param_patient_DOB, $param_ssn, $param_patient_address, $param_patient_phone, $param_patient_email, $param_max_travel_distance, $param_id, $param_groupId);
                    $param_patient_name= $patient_name;
                    $param_patient_DOB= $patient_DOB;
                    $param_ssn= $ssn;
                    $param_patient_address= $patient_address;
                    $param_patient_phone= $patient_phone;
                    $param_patient_email= $patient_email;
                    $param_max_travel_distance= $max_travel_distance;
                    $param_id= $user_id;
                    if($age>74){
                        $param_groupId=2;
                    }else if($age<75 and $age>64){
                        $param_groupId=3;
                    }else if($age>15 and $age<65){
                        $param_groupId=4;
                    }
                }
                if(mysqli_stmt_execute($stmt3)){
                    header("location: login.php");
                }
            } else{
                echo "hi";
                echo "Oops! Something went wrong. Please try again later.";
                
            } 

            // Close statement
            mysqli_stmt_close($stmt);
            mysqli_stmt_close($stmt2);
            mysqli_stmt_close($stmt3);
            

        }
    }
  }
    // Close connection
    mysqli_close($link);
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sign Up</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body{ font: 14px sans-serif; }
        .wrapper{ width: 350px; padding: 20px; }
    </style>
</head>
<body>
    <div class="wrapper">
        <h2>Sign Up</h2>
        <p>Please fill this form to create an account.</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" class="form-control <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $username; ?>">
                <span class="invalid-feedback"><?php echo $username_err; ?></span>
            </div>    
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $password; ?>">
                <span class="invalid-feedback"><?php echo $password_err; ?></span>
            </div>
            <div class="form-group">
                <label>Confirm Password</label>
                <input type="password" name="confirm_password" class="form-control <?php echo (!empty($confirm_password_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $confirm_password; ?>">
                <span class="invalid-feedback"><?php echo $confirm_password_err; ?></span>
            </div>
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
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Submit">
                <input type="reset" class="btn btn-secondary ml-2" value="Reset">
            </div>
            
            <p>Already have an account? <a href="login.php">Login here</a>.</p>
             <p>Want to go home? <a href="home.php">Press here</a>.</p>

        </form>
    </div>    
</body>
</html>