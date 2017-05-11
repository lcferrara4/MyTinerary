<?php

	# start session
	session_start();
	
	# redirect user if they are already logged in
	if(isset($_SESSION["email"])) {

		#remove all session variables
		session_unset();
		exit();
	}

?>
<?php

	# executes when a post request is sent to this page
	if(isset($_POST["email"])) {

		# initialize error flag
		$error = false;

		#connect to database
		$db_conx = mysqli_connect("localhost", "csmick", "allAboutDatBase", "mytinerary") or die("Unable to connect to database");

		# create local variables from the posted data
		$firstName = preg_replace("[^0-9]", "", $_POST["firstName"]);
		$lastName = preg_replace("[^0-9]", "", $_POST["lastName"]);
		$email = mysqli_real_escape_string($db_conx, $_POST["email"]);
		$password = $_POST["password"];
		$confirmPassword = $_POST["confirmPassword"];

		# ensure firstName has length of at least 2
		if(strlen($firstName) < 2) {
			echo "<li>First name must contain at least 2 letters</li>";
			$error = true;
		}

		# ensure lastName ha length of at least 2
		if(strlen($lastName) < 2) {
			echo "<li>Last name must contain at least 2 letters</li>";
			$error = true;
		}

		# ensure email is not already in database
		$sql = "SELECT email FROM Users WHERE email like '".$email."' LIMIT 1;";
		$query = mysqli_query($db_conx, $sql) or die("Could not query database");
		if(mysqli_num_rows($query)) {
			echo "<li>Email address is already connected to an account</li>";
			$error = true;
		}

		# ensure password is at least 8 characters long
		if(strlen($password) < 8) {
			echo "<li>Password must be at least 8 characters long</li>";
			$error = true;
		}

		# ensure passwords match
		if($password != $confirmPassword) {
			echo "<li>Passwords do not match</li>";
			$error = true;
		}

		# if any of the above produced an error, exit without creating account
		if($error) {

			# trigger javascript error callback
			http_response_code(400);
			exit();
		}
		else {

			# hash password
			$hash = password_hash($password, PASSWORD_BCRYPT);

			# save info to database
			$sql = "INSERT INTO Users (first_name, last_name, email, password) values ('".$firstName."', '".$lastName."', '".$email."', '".$hash."');";
			mysqli_query($db_conx, $sql) or die("Could not create user account");

			# add row to preferences table
			$sql = "INSERT INTO Preferences (email) values ('".$email."');";
			mysqli_query($db_conx, $sql) or die("Could not create row in user preferences");

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
			$response = "Account successfully created!";
			exit($response);
		}
	}

?>

<!doctype html>
<html>
	<head>
		<meta charset="UTF-8">
		<title>MyTinerary | Create an Account</title>
		<!-- Favicon -->
		<link rel="shortcut icon" type="image/x-icon" href="favicon.ico" />
		<!-- Latest compiled and minified CSS -->
        	<link rel="stylesheet" type="text/css" href="css/bootstrap.min.css"/>
        	<!-- Optional theme -->
        	<link rel="stylesheet" type="text/css" href="css/bootstrap-theme.min.css"/>
		<!-- Custom stylesheet -->
		<link rel="stylesheet" type="text/css" href="css/create_account.css"/>
	</head>
	<body>
		<div class="header">
			<strong><a href="index.html" style="color: black; text-decoration: none;">MyTinerary</a> | Create an Account</strong>
			<hr>
		</div>
		<div class="container">
			<div class="row">
				<div class="col-sm-4 col-sm-offset-4">
					<div class="well">
						<form id="createAccountForm" name="createAccountForm">
							<div class="container"style="width: inherit">	
								<div class="row">
									<div class="col-sm-6">
										<div class="form-group">
											<label for="firstName">First Name<small hidden>*must be at least 2 characters in length</small></label>
											<input type="text" id="firstName" class="form-control" name="firstName" placeholder="John">
										</div>
									</div>
									<div class="col-sm-6">
										<div class="form-group">
											<label for="lastName">Last Name<small hidden>*must be at least 2 characters in length</small></label>
											<input type="text" id="lastName" class="form-control" name="lastName" placeholder="Smith">
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-sm-12">
										<div class="form-group">
											<label for="email">Email<small hidden>*invalid email address</small></label>
											<input type="email" id="email" class="form-control" name="email" placeholder="abc@example.com">
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-sm-12">
										<div class="form-group">
											<label for="password">Password<small hidden>*password must be at least 8 characters long</small></label>
											<input type="password" id="password" class="form-control" name="password">
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-sm-12">
										<div class="form-group">
											<label for="confirmPassword">Confirm Password<small hidden>*passwords do not match</small></label>
											<input type="password" id="confirmPassword" class="form-control" name="confirmPassword">
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
		<script src="js/create_account.js"></script>
	</body>
</html>


