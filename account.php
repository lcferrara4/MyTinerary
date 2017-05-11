<?php

	session_start();

	if(!isset($_SESSION["email"])) {
		header("Location: login.php");
	}
?>
<?php
	#$result = exec("/usr/bin/python3 nb.py dev.txt") or die("what");
	#echo $result;

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
                $query = mysqli_query($db_conx, $sql) or die("Query failed unexpectedly");
                $user = mysqli_fetch_array($query, MYSQLI_ASSOC);

                # verify password
                if(password_verify($oldPassword, $user["password"])) {

                        # create local variables from the posted data
                        $new_password = $_POST["new_password"];
                        $confirm_password = $_POST["confirm_password"];

                        # ensure new password is at least 8 characters long
                        if(strlen($new_password) < 8) {
                                echo "Password must be at least 8 characters long";
                                $error = true;
                        }

                        # ensure passwords match
                        if($new_password != $confirm_password) {
                                echo "Passwords do not match";
                                $error = true;
                        }

                        # if any of the above produced an error, exit without updating password
                        if($error) {

                                # trigger javascript error callback
                                http_response_code(400);
                                exit();
                        } else {

                                # hash password
                                $hash = password_hash($new_password, PASSWORD_BCRYPT);

                                # update password in database
                                $sql = "UPDATE Users SET password = '".$hash."' WHERE email = '".$email."';";
				mysqli_query($db_conx, $sql) or die("Failed to update password");

                       }

                        # trigger ajax success callback
                        $result = "Password update successful!";
                        exit($result);
                }
                else {

                        # return error message
                        echo "Password is incorrect";

                        # trigger ajax error callback
                        http_response_code(400);
                        exit();
                }
        }
?>
<?php
        # executes when a post request is sent to this page
        if(isset($_POST["food"]) or isset($_POST["bar"]) or isset($_POST["shopping"]) or isset($_POST["sports"]) or isset($_POST["museums"]) or isset($_POST["sightseeing"]) or isset($_POST["theater"]) or isset($_POST["art"]) or isset($_POST["one_dollar"]) or isset($_POST["two_dollar"]) or isset($_POST["three_dollar"])) {

                #connect to database
                $db_conx = mysqli_connect("localhost", "csmick", "allAboutDatBase", "mytinerary") or die("Unable to connect to database");

                # create local variables from the posted data
                $email = $_SESSION["email"];

		# create local variables from the posted data
		if($_POST["food"] == "true"){
			$food = "1";
		} else{
			$food = "0";
		}
		if($_POST["bar"] == "true"){
			$bars = "1";
		} else{
			$bars = "0";
		}
		if($_POST["shopping"] == "true"){
			$shopping = "1";
		} else{
			$shopping = "0";
		}
		if($_POST["sports"] == "true"){
			$sports = "1";
		} else{
			$sports = "0";
		}
		if($_POST["museums"] == "true"){
			$museums = "1";
		} else{
			$museums = "0";
		}
		if($_POST["sightseeing"] == "true"){
			$sightseeing = "1";
		} else{
			$sightseeing = "0";
		}
		if($_POST["art"] == "true"){
			$art = "1";
		} else{
			$art = "0";
		}
		if($_POST["theater"] == "true"){
			$theater = "1";
		} else{
			$theater = "0";
		}
                if($_POST["one_dollar"] == "true"){
                        $one_dollar = "1";
                } else{
                        $one_dollar = "0";
                }
                if($_POST["two_dollar"] == "true"){
                        $two_dollar = "1";
                } else{
                        $two_dollar = "0";
                }
                if($_POST["three_dollar"] == "true"){
                        $three_dollar = "1";
                } else{
                        $three_dollar = "0";
                }

		# make sure user has preferences - should not be an issue since update
                $sql = "SELECT * from Preferences where email = '".$email."';";
                $result = mysqli_query($db_conx, $sql) or die("Could not check preferences");

		if($result->num_rows > 0){

			# update preferences in database
			$sql = "UPDATE Preferences
				SET food = ".$food.", 
					bar = ".$bars.", 
					sightseeing = ".$sightseeing.", 
					shopping = ".$shopping.", 
					sports = ".$sports.", 
					museum = ".$museums.", 
					art = ".$art.", 
					theater = ".$theater.",
					one_dollar = ".$one_dollar.",
					two_dollar = ".$two_dollar.",
					three_dollar = ".$three_dollar."
				WHERE email = '".$email."';";
			
			mysqli_query($db_conx, $sql) or die("Could not update preferences");
		}
		else{

			# insert preferences into database
			$sql = "INSERT INTO Preferences
				(email, food, bar, sightseeing, shopping, sports, museum, art, theater, one_dollar, two_dollar, three_dollar)
				VALUES
				('".$email."', ".$food.", ".$bars.", ".$sightseeing.", ".$shopping.", ".$sports.", ".$museums.", ".$art.", ".$theater.", ".$one_dollar.", ".$two_dollar.", ".$three_dollar.");";
			mysqli_query($db_conx, $sql) or die("Could not insert preferences");

		}
		# delete old suggestions from suggestions table
		$sql = "DELETE from Suggestions where email = '".$email."';";
		mysqli_query($db_conx, $sql) or die("Could not delete old suggestions");

		# recalculate scores and add to suggestions table
		$sql = "INSERT INTO Suggestions
			(email, general_category, site_id, site_name, score)
			(SELECT C.email, C.category, C.place_id, C.name, (sum_of_matches) * (C.rating + 1) 
				FROM
					(SELECT email, category, place_id, B.name as name, rating,
						((A.one_dollar + (B.price = 1) = 2) + (A.two_dollar + (B.price = 2) = 2) + (A.three_dollar + (B.price = 3) = 2) + (B.price = 0) + ((A.food + B.food = 2) + (A.bar + B.bar = 2))/2 + (A.sightseeing + B.sightseeing = 2) + (A.shopping + B.shopping = 2) * (7/8)
						+ (A.art + B.art = 2) + (A.museum + B.museum = 2) + (A.theater + B.theater = 2) + (A.sports + B.sports = 2) + .5) as sum_of_matches 
					FROM 
						(SELECT email, food, bar, sightseeing, shopping, art, museum, theater, sports, one_dollar, two_dollar, three_dollar
 							from Preferences WHERE email = '".$email."') A, 
						(SELECT category, place_id, name, rating, price, food, bar, sightseeing, shopping, art, museum, theater, sports from Site) B
					) C
			);";
		mysqli_query($db_conx, $sql) or die("Could not update suggestions");

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
	
	# query database for preferences for account with matching email
	$sql = "SELECT * FROM Preferences WHERE email like '".$email."' LIMIT 1;";
	$query = mysqli_query($db_conx, $sql);
	$user = mysqli_fetch_assoc($query);
?>

<html>
	<head>
		<meta charset="UTF-8">
		<title>MyTinerary | My Account</title>
		<!-- Favicon -->
                <link rel="shortcut icon" type="image/x-icon" href="favicon.ico" />
                <!-- Latest compiled and minified CSS -->
                <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css"/>
                <!-- Optional theme -->
                <link rel="stylesheet" type="text/css" href="css/bootstrap-theme.min.css"/>
		<!-- Custom stylesheets -->
		<link rel="stylesheet" type="text/css" href="css/style.css"/>
		<link rel="stylesheet" type="text/css" href="css/account.css"/>
	</head>
	<body>
		<div class="nav">	
		    <ul class="nav nav-tabs">
			<li class="index"><a href="dashboard.php" title="Dashboard">Dashboard</a></li>
			<li class="calendar"><a href="calendar_list.php" title="Calendar">Calendar</a></li>
			<li class="settings pull-right">
				<div class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Settings<span class="caret"></span></div>
				<div class="dropdown-menu">
					<a class="dropdown-item" href="account.php" title="My Account"><div class="full">My Account</div></a>
					<div class="dropdown-divider"></div>
					<a class="dropdown-item" href="logout.php"><div class="full">Logout</div></a>
				</div>
			</li>
		    </ul>	
		</div>

		<div class="center">
			<h2>Your personal info</h2>

			<h4>Account Credentials</h4>
			<div class="tab"><?php echo $_SESSION["first_name"].' '.$_SESSION["last_name"] ?></div>
			<div class="tab"><?php echo $_SESSION["email"] ?></div>
			<hr>

			<h4>Security Settings</h4>
			<div class="tab"><a id="change_password">Change password</a></div>
			<div class="container-fluid">
				<div class="row">
					<ul class="error-list" hidden>
						<!-- Form errors will be appended here -->
					</ul>
				</div>
				<div class="row">
					<form id="change_password_form" class="form-inline" hidden>
						<div class="form-group">
							<input type="password" id="old_password" class="form-control" name="old_password" placeholder="Current Password">
						</div>
						<div class="form-group">
							<input type="password" id="new_password" class="form-control" name="new_password" placeholder="New Password">
						</div>
						<div class="form-group">
							<input type="password" id="confirm_password" class="form-control" name="confirm_password" placeholder="Confirm New Password">
						</div>
						<button type="submit" id="change_password_btn" class="btn btn-default">Save</button>
						<a id="cancel_change_password">cancel</a>
					</form>
				</div>
			</div>
			<hr>
			<h4>Preferences</h4>
				<div class="col-sm-12">
					<form id="preferenceForm" name="preferenceForm" method="post">
						<div class="container" style="width: inherit">
							<div class="row">
								<div class="col-sm-6">
									<div class="form-group">
										<!--<p>Restaurants</p>-->
										<p>Restaurants</p>
										<!--label for="food">Restaurants</label>-->
										<input type="checkbox" id="food" name="food" <?php if($user['food']):?> checked <?php endif ?>>
										<label for="food"><img class="img" src="images/restaurants.jpg" height="275" width="300"/></label>
									</div>
								</div>
								<div class="col-sm-6">
									<div class="form-group">
										<p>Bars/Clubs</p>
										<!--<label for="food">Bars and clubs</label>-->
										<input type="checkbox" id="bar" name="bars" <?php if($user['bar']):?> checked <?php endif ?>>
										<label for="bar"><img class="img" src="images/bars.jpg" height="275" width="300"/></label>

									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-sm-6">
									<div class="form-group">
										<p>Shopping</p>
										<!--<label for="shopping">Shopping</label>-->
										<input type="checkbox" id="shopping" name="shopping" <?php if($user['shopping']):?> checked <?php endif ?>>
										<label for="shopping"><img class="img" src="images/shopping.jpg" height="275" width="300"/></label>
									</div>
								</div>
								<div class="col-sm-6">
									<div class="form-group">
										<p>Museums</p>
										<!--<label for="museums">Museums</label>-->
										<input type="checkbox" id="museums" name="museums" <?php if($user['museum']):?> checked <?php endif ?>>
										<label for="museums"><img class="img" src="images/museums.jpg" height="275" width="300"/></label>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-sm-6">
									<div class="form-group">
										<p>Sports</p>
										<input type="checkbox" id="sports" name="sports" <?php if($user['sports']):?> checked <?php endif ?>>
										<label for="sports"><img class="img" src="images/sports.jpg" height="275" width="300"/></label>
									</div>
								</div>
								<div class="col-sm-1">
									<div class="form-group">
										<p>Sightseeing</p>
										<input type="checkbox" id="sightseeing" name="sightseeing" <?php if($user['sightseeing']):?> checked <?php endif ?>>
										<label for="sightseeing"><img class="img" src="images/sightseeing.jpg" height="275" width="300"/></label>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-sm-6">
									<div class="form-group">
										<p>Art</p>
										<input type="checkbox" id="art" name="art" <?php if($user['art']):?> checked <?php endif ?>>
										<label for="art"><img class="img" src="images/art.jpg" height="275" width="300"/></label>
									</div>
								</div>
								<div class="col-sm-1">
									<div class="form-group">
										<p>Theater</p>
										<input type="checkbox" id="theater" name="theater" <?php if($user['theater']):?> checked <?php endif ?>>
										<label for="theater"><img class="img" src="images/theater.jpg" height="275" width="300"/></label>
									</div>
								</div>
							</div>
							<div class="row">
								<p>Pick all pricing classifications you are willing to spend.</p>
							</div>
							<div class="row">
								<div class="col-sm-3">
									<div class="form-group">
										<input type="checkbox" id="1dollar" name="1dollar" <?php if($user['one_dollar']):?> checked <?php endif ?>>
										<label for="1dollar"><img class="img" src="images/1dollar.jpg" height="90" width="35"/></label>
									</div>
								</div>
								<div class="col-sm-3">
									<div class="form-group">
										<input type="checkbox" id="2dollar" name="2dollar" <?php if($user['two_dollar']):?> checked <?php endif ?>>
										<label for="2dollar"><img class="img" src="images/2dollars.jpg" height="90" width="65"/></label>
									</div>
								</div>
<div class="col-sm-4">
								<div class="col-sm-11">
                                                                	<div class="form-group">
                                                                                <input type="checkbox" id="3dollar" name="3dollar" <?php if($user['three_dollar']):?> checked <?php endif ?>>
										<label for="3dollar"><img class="img" src="images/3dollars.jpg" height="90" width="120"/></label>
                                                                        </div>
                                                                </div>
							</div>
						</div>
					</form>
				</div>
                                <div class="col-sm-2">
					<button id="submit" type="submit" form="preferenceForm" class="btn btn-primary pull-right">Save</button>
				</div>

		<!-- jQuery -->
		<script src="js/jquery-2.0.0.min.js"></script>
        	<!-- Latest compiled and minified JavaScript for Bootstrap-->
        	<script src="js/bootstrap.min.js"></script>
		<!-- Custom script -->
		<script src="js/account.js"></script>
		<script src="js/preferences.js"></script>
	</body>
</html>
