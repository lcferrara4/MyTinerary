<?php

        session_start();

	# ensure user is logged in
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
        if(isset($_POST["suggest"])) {

                #connect to database
                $db_conx = mysqli_connect("localhost", "csmick", "allAboutDatBase", "mytinerary") or die("Unable to connect to database");

                # create local variables from the posted data
                $email = $_SESSION["email"];
	
		# get preferences from database
		# calculate score

		# remove old items and recalculate if user has updated preferences

		# ignore those already on calendar?

		# score new items in Site table
		if($preferences_have_not_been_updated){
			$sql_sugg = "
				INSERT INTO Suggestions
					(user_id, general_category, site_id, site_name, score)
					VALUES
					('".$user_id."', , )
				SELECT 
				(
				SELECT place_id, name, rating, food, bar, sightseeing, shopping, art, museum, theater, sports from Site
				WHERE place_id NOT IN (SELECT sg.site_id FROM Suggestions sg)
				) A
				"
		}
		# trigger javascript success callback
		$response = "Preferences successfully updated!";
		exit(json_encode($response));
	}
?>

<?php
	
	#connect to database
	$db_conx = mysqli_connect("localhost", "csmick", "allAboutDatBase", "mytinerary") or die("Unable to connect to database");

	# create local variables from the posted data
	$email = $_SESSION["email"];
	
	# query database for Preferences account with matching email
	$sql = "SELECT * FROM Preferences WHERE email like '".$email."' LIMIT 1;";
	$query = mysqli_query($db_conx, $sql);
	$user = mysqli_fetch_array($query, MYSQLI_ASSOC);
?>

<!doctype html>
<html>
        <head>
                <meta charset="UTF-8">
                <title>MyTinerary | User Preferences</title>
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

	<div class="nav">
	    <ul class="nav nav-tabs">
		<li class="dashboard"><a href="dashboard.php" title="Dashboard">Dashboard</a></li>
		<li class="calendar"><a href="calendar_page/mytinerary_calendar/cal-onefile.php" title="Calendar">Calendar</a></li>
		<li class="index current active"><a href="preferences.php" title="Preferences">Preferences</a></li>
		<li class="password"><a href="update_password.php" title="Update Password">Update Password</a></li>
		<li class="logout"><a href="logout.php">Logout</a></li>
		<li class="delete"><a href="delete_account.php">Delete Account</a></li>
	    </ul>
	</div>



                <div class="header">
                        <strong>MyTinerary | User Preferences</strong>
                        <hr>
                </div>
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
                                                <form id="preferenceForm" name="preferenceForm" method="post">
                                                        <div class="container"style="width: inherit">
                                                                <div class="row">
                                                                        <div class="col-sm-12">
                                                                                <div class="form-group">
                                                                                        <label for="food">Importance of Food</label>
                                                                                        <input type="range" id="food" name="food" min="0" max="5", value=<?php echo $user['food_importance']?>>
                                                                                </div>
                                                                        </div>
                                                                </div>
                                                                <div class="row">
                                                                        <div class="col-sm-12">
                                                                                <div class="form-group">
                                                                                        <label for="outside">Time Spent Outside</label>
                                                                                        <input type="range" id="outside" name="outside" min="0" max="5", value=<?php echo $user['time_outside']?>>
                                                                                </div>
                                                                        </div>
                                                                </div>
                                                                <div class="row">
                                                                        <div class="col-sm-12">
                                                                                <div class="form-group">
                                                                                        <label for="doing">Seeing vs Doing</label>
                                                                                        <input type="range" id="doing" name="doing" min="0" max="5", value=<?php echo $user['seeing_vs_doing']?>>
                                                                                </div>
                                                                        </div>
                                                                </div>
                                                                <div class="row">
                                                                        <div class="col-sm-12">
                                                                                <div class="form-group">
                                                                                        <label for="shopping">Shopping</label>
                                                                                        <input type="checkbox" id="shopping" name="shopping" <?php if($user['shopping']):?> checked <?php endif ?>>
                                                                                </div>
                                                                        </div>
                                                                </div>
                                                                <div class="row">
                                                                        <div class="col-sm-12">
                                                                                <div class="form-group">
                                                                                        <label for="museums">Museums</label>
                                                                                        <input type="checkbox" id="museums" name="museums" <?php if($user['museums']):?> checked <?php endif ?>>
                                                                                </div>
                                                                        </div>
                                                                </div>
                                                                <div class="row">
                                                                        <div class="col-sm-12">
                                                                                <div class="form-group">
                                                                                        <label for="sports">Sports</label>
                                                                                        <input type="checkbox" id="sports" name="sports" <?php if($user['sports']):?> checked <?php endif ?>>
                                                                                </div>
                                                                        </div>
                                                                </div>
                                                                <div class="row">
                                                                        <div class="col-sm-12">
                                                                                <div class="form-group">
                                                                                        <label for="sightseeing">Sightseeing</label>
                                                                                        <input type="checkbox" id="sightseeing" name="sightseeing" <?php if($user['sightseeing']):?> checked <?php endif ?>>
                                                                                </div>
                                                                        </div>
                                                                </div>
                                                                <div class="row">
                                                                        <div class="col-sm-12">
                                                                                <div class="form-group">
                                                                                        <label for="art">Art</label>
                                                                                        <input type="checkbox" id="art" name="art" <?php if($user['art']):?> checked <?php endif ?>>
                                                                                </div>
                                                                        </div>
                                                                </div>
                                                                <div class="row">
                                                                        <div class="col-sm-12">
                                                                                <div class="form-group">
                                                                                        <label for="nature">Nature</label>
                                                                                        <input type="checkbox" id="nature" name="nature" <?php if($user['nature']):?> checked <?php endif ?>>
                                                                                </div>
                                                                        </div>
                                                                </div>
                                                                <div class="row">
                                                                        <div class="col-sm-12">
                                                                                <div class="form-group">
                                                                                        <label for="theater">Theater</label>
                                                                                        <input type="checkbox" id="theater" name="theater" <?php if($user['theater']):?> checked <?php endif ?>>
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
                <script src="js/preferences.js"></script>
        </body>
</html>      

