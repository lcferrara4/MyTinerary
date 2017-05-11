<?php

	# start session
	session_start();

	# trigger ajax success callback if user is already signed in
	if(isset($_SESSION["email"])) {
		header("Location: dashboard.php");
		exit;
	}

	# check if user is attempting to log in
	if(isset($_POST["email"])) {
	
		# connect to database
		$db_conx = mysqli_connect("localhost", "csmick", "allAboutDatBase", "mytinerary");

		# ensure that the connection succeeded
		if(mysqli_connect_errno()) {
			http_response_code(503);
			exit("Failed to connect to database. Please try again.");
		}
	
		# retrieve values from post request
		$email = mysqli_real_escape_string($db_conx, $_POST["email"]);
		$password = $_POST["password"];

		# query database for User account with matching email
		$sql = "SELECT * FROM Users WHERE email like '".$email."' LIMIT 1;";
		$query = mysqli_query($db_conx, $sql);
		$user = mysqli_fetch_array($query, MYSQLI_ASSOC);

		# verify password
		if(password_verify($password, $user["password"])) {

			# assign session variables
			$_SESSION["first_name"] = $user["first_name"];
			$_SESSION["last_name"] = $user["last_name"];
			$_SESSION["email"] = $user["email"];
						# trigger ajax success callback
			http_response_code(200);
			exit("Login successful!");
		}
		else {

			# trigger ajax error callback
			http_response_code(400);
			exit("Invalid email address or password");
		}
	}

?>

<!doctype html>
<html>
	<head>
		<meta charset="UTF-8">
		<title>MyTinerary | Log in</title>
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
		<div class="header">
			<strong><a href="index.html" style="color: black; text-decoration: none;">MyTinerary</a> | Log in</strong>
			<hr>
		</div>
		<div class="container">
			<div class="row">
				<div class="col-sm-4 col-sm-offset-4">
					<div class="well">
						<form id="loginForm" name="loginForm">
							<div class="container" style="width: inherit">		
								<div class="row">
									<div class="col-sm-12">
										<div class="form-group">
											<label for="email">Email</label>
											<input type="email" id="email" class="form-control" name="email">
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-sm-12">
										<div class="form-group">
											<label for="password">Password</label>
											<input type="password" id="password" class="form-control" name="password">
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

					<div style="text-align: center;">
						<p>New to MyTinerary? <a href="create_account.php">Create an account!</a>
					</div>
				</div>
			</div>

		</div>

		<!-- jQuery -->
		<script src="js/jquery-2.0.0.min.js"></script>
        	<!-- Latest compiled and minified JavaScript for Bootstrap-->
        	<script src="js/bootstrap.min.js"></script>
		<!-- Custom Script -->
		<script src="js/login.js"></script>
	</body>
</html>
