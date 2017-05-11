<?php

        session_start();

	if(isset($_SESSION["email"])) {	
		$email = $_SESSION["email"];
	}
	else {
		
		# redirect user to login page
		header("Location: login.php");
		exit;
	}

?>

<?php
        # executes when a post request is sent to this page
        if(isset($_POST["old_password"])) {

                # initialize error flag
                $error = false;

                #connect to database
                $db_conx = mysqli_connect("localhost", "csmick", "allAboutDatBase", "mytinerary") or die("Unable to connect to database");

                # create local variables from the posted data
                $email = $_SESSION["email"];
                $oldPassword = $_POST["old_password"];

                # query database for User account with matching email
                $sql = "SELECT * FROM Users WHERE email like '".$email."' LIMIT 1;";
                $query = mysqli_query($db_conx, $sql);
                $user = mysqli_fetch_array($query, MYSQLI_ASSOC);

                # verify password
                if(password_verify($oldPassword, $user["password"])) {

                        # create local variables from the posted data
                        $new_password = $_POST["new_password"];
                        $confirm_password = $_POST["confirm_password"];

                        # ensure new password is at least 8 characters long
                        if(strlen($new_password) < 8) {
                                echo "<li>Password must be at least 8 characters long</li>";
                                $error = true;
                        }

                        # ensure passwords match
                        if($new_password != $confirm_password) {
                                echo "<li>Passwords do not match</li>";
                                $error = true;
                        }

                        # if any of the above produced an error, exit without creating account
                        if($error) {

                                # trigger javascript error callback
                                http_response_code(400);
                                exit();
                        } else {

                                # hash password
                                $hash = password_hash($new_password, PASSWORD_BCRYPT);

                                # update password in database
                                $sql = "UPDATE Users SET password = '".$hash."' WHERE email = '".$email."';";
                                mysqli_query($db_conx, $sql) or die("Could not update password");

                       }

                        # trigger ajax success callback
                        $result = "Password update successful!";
                        exit(json_encode($result));
                }
                else {

                        # return error message
                        echo "<li>Password is incorrect</li>";

                        # trigger ajax error callback
                        http_response_code(400);
                        exit();
                }
        }
?>

<!doctype html>
<html>
        <head>
                <meta charset="UTF-8">
                <title>MyTinerary | Update Password</title>
                <!-- Favicon -->
                <link rel="shortcut icon" type="image/x-icon" href="favicon.ico" />
                <!-- Latest compiled and minified CSS -->
                <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css"/>
                <!-- Optional theme -->
                <link rel="stylesheet" type="text/css" href="css/bootstrap-theme.min.css"/>
                <!-- Custom stylesheet -->
                <link rel="stylesheet" type="text/css" href="css/login.css"/>
        </head>
        <body>
 
       		<div class="container">
                	<div class="row">
                                <div id="error_list" hidden>
                                        <h3>Error:</h3>
                                        <ul>
                                                <!-- display php errors here -->
                                        </ul>
                                </div>

                                <div class="col-sm-4 col-sm-offset-4">
                                        <div class="well">
                                                <form id="passwordForm" name="passwordForm" method="post">
                                                        <div class="container"style="width: inherit">
                                                                <div class="row">
                                                                        <div class="col-sm-12">
                                                                                <div class="form-group">
                                                                                        <label for="old_password">Old Password<small hidden> *must match account password</small></label>
                                                                                        <input type="password" id="old_password" class="form-control" name="old_password">
                                                                                </div>
                                                                        </div>
                                                                </div>
                                                                <div class="row">
                                                                        <div class="col-sm-12">
                                                                                <div class="form-group">
                                                                                        <label for="new_password">New Password<small hidden> *must be at least 8 characters</small></label>
                                                                                        <input type="password" id="new_password" class="form-control" name="new_password">
                                                                                </div>
                                                                        </div>
                                                                </div>
                                                                <div class="row">
                                                                        <div class="col-sm-12">
                                                                                <div class="form-group">
                                                                                        <label for="confirm_password">Confirm New Password<small hidden> *must match new password</small></label>
                                                                                        <input type="password" id="confirm_password" class="form-control" name="confirm_password">
                                                                                </div>
                                                                        </div>
                                                                </div>
                                                                <div class="row">
                                                                        <div class="col-sm-1">
                                                                                <button id="submit" type="submit" class="btn btn-default">Submit</button>
                                                                        </div>
                                                                </div>
                                                        </div>
                                                </form>
                                        </div>
                                </div>
                        </div>
                </div>

                <!-- jQuery -->
                <script src="js/jquery-2.0.0.min.js"></script>
                <!-- Latest compiled and minified JavaScript for Bootstrap-->
                <script src="js/bootstrap.min.js"></script>
                <!-- Custom Script -->
                <script src="js/update_password.js"></script>
        </body>
</html>
