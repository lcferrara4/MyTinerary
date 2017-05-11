<?php

	session_start();

	# ensure that a user is logged in
	if(!isset($_SESSION["email"])) {
		# redirect user to login page
		header("Location: login.php");
		exit;
	}

?>
<?php
                # update sites in database if columns not filled
                $email = $_SESSION["email"];


                # connect to database
                $db_conx = mysqli_connect("localhost", "csmick", "allAboutDatBase", "mytinerary");

                # ensure that the connection succeeded
                if(mysqli_connect_errno()) {
                        http_response_code(503);
                        exit("Failed to connect to database. Please try again.");
                }

                #       if(isset($_SESSION["email"])) {
                $sql = "select place_id, name, category, rating, food, bar, sightseeing, shopping, art, museum, theater, sports from Site
                        WHERE food IS NULL;";
                $result = mysqli_query($db_conx, $sql) or die("Could not run Naive Bayes on new Sites");
		$filename = "dev_" . $_SESSION["email"] . ".txt";
                $myfile = fopen($filename, "w+")  or die("Unable to open file!");
		chmod("dev_" . $_SESSION["email"] . ".txt", 0777);

                if($result->num_rows > 0){
                        while ($row = $result->fetch_assoc()){
                                fwrite($myfile, $row['place_id'] . "|");
                                fwrite($myfile, $row['name'] . "|");
                                fwrite($myfile, $row['category'] . "|");
                                fwrite($myfile, $row['rating'] . "|");
                                fwrite($myfile, "0|0|0|0|0|0|0|0\n");
                        }

                        fclose($myfile);
                        $command = "/usr/bin/python /var/www/html/cse30246/dat_base/nb.py " . $filename;
			$data = shell_exec($command) or die();
                        $lines = explode( "\n", $data );
                        foreach ($lines as $line){
                                $answer = explode("|", $line);
                                $new_sql = "UPDATE Site
                                                SET food = ".$answer[1].", 
                                                bar = ".$answer[2].", 
                                                sightseeing = ".$answer[3].", 
                                                shopping = ".$answer[4].", 
                                                art = ".$answer[5].", 
                                                museum = ".$answer[6].", 
                                                theater = ".$answer[7].",
                                                sports = ".$answer[8]." 
                                        WHERE place_id = '".$answer[0]."';";
                                echo $new_sql;
				mysqli_query($db_conx, $new_sql) or die("Could not update site categories");
                        }
                        unlink("dev_" . $_SESSION["email"] . ".txt");
                }


#       }

?>

<!doctype html>
<html>
	<head>
		<title>Dashboard</title>
		<!-- Favicon -->
		<link rel="shortcut icon" type="image/x-icon" href="favicon.ico" />
		<!-- Latest compiled and minified CSS -->
        	<link rel="stylesheet" type="text/css" href="css/bootstrap.min.css"/>
        	<!-- Optional theme -->
        	<link rel="stylesheet" type="text/css" href="css/bootstrap-theme.min.css"/>
        	<!-- Custom theme -->
		<link rel="stylesheet" type="text/css" href="css/style.css"/>

	</head>

	<body>
		<div class="nav">	
		    <ul class="nav nav-tabs">
			<li class="index current active"><a href="dashboard.php" title="Dashboard">Dashboard</a></li>
			<li class="calendar"><a href="calendar_list.php" title="Calendar">Calendar</a></li>
			<li class="settings pull-right" style="margin-right: 15px;">
				<div class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Settings<span class="caret"></span></div>
				<div class="dropdown-menu">
					<a class="dropdown-item" href="account.php" title="My Account"><div class="full">My Account</div></a>
					<div class="dropdown-divider"></div>
					<a class="dropdown-item" href="logout.php"><div class="full">Logout</div></a>
				</div>
			</li>
		    </ul>	
		</div>		

		<h1 style="margin-left: 15px;">Dashboard - <?php echo $_SESSION["first_name"]." ".$_SESSION["last_name"]; ?></h1>

		<div class="container-fluid">
			<div class="row">
				<div class="col-xs-12">
					<div class="panel panel-primary">
						<div class="panel-heading">
							<h2 class="panel-title" style="display: inline-block; line-height: 30px;">Upcoming Trips</h2>
							<button class="btn btn-success new_trip_btn" style="float: right; position: relative; bottom: 2px;">Create a new schedule</button>
						</div>
						<div class="panel-body">
							<div class="container-fluid">
								<div class="row">
									<?php
										$date = date("Y-m-d");
										$sql = "select * from Calendar where creator_email = '".$_SESSION["email"]."' and trip_start > date '".$date."' order by trip_start asc limit 3;";
										$result = mysqli_query($db_conx, $sql) or die("Upcoming trips query failed.");
										if($result->num_rows > 0) {
											while($row = $result->fetch_assoc()) {
												$trip_start = date('d M Y', strtotime($row['trip_start']));
												$trip_end = date('d M Y', strtotime($row['trip_end']));
												echo "<div class=\"trip_desc col-xs-4\" data-id='".$row['calendar_id']."'><h3>".$row["title"]."</h3><p>".$trip_start." - ".$trip_end."</p></div>";
											}
										}
										else {
											echo "You have no upcoming trips.";
										}
									?>
								</div>
							</div>	
						</div>
					</div>
				</div>
			</div>
		</div>
        <div class="container-fluid">
            <div class="row">
                <div class="col-xs-12">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <h2 class="panel-title">Shared With You</h2>
                        </div>
                        <div class="panel-body">
                            <div class="container-fluid">
                                <div class="row">
                                    <?php
                                        $date = date("Y-m-d");
                                        $sql = "select * from Calendar where Calendar.calendar_id in (select calendar_id from Permissions where user_email = '".$_SESSION["email"]."') and trip_start > date '".$date."' and not creator_email = '".$_SESSION["email"]."' order by trip_start asc limit 3;";
                                        $result = mysqli_query($db_conx, $sql) or die("Shared trips query failed.");
                                        if($result->num_rows > 0) {
                                            while($row = $result->fetch_assoc()) {
                                                $trip_start = date('d M Y', strtotime($row['trip_start']));
                                                $trip_end = date('d M Y', strtotime($row['trip_end']));
                                                echo "<div class=\"trip_desc col-xs-4\" data-id='".$row['calendar_id']."'><h3>".$row["title"]."</h3><p>".$trip_start." - ".$trip_end."</p></div>";
                                            }
                                        }
                                        else {
                                            echo "There are no trips shared with you.";
                                        }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
		<div class="container-fluid">
			<div class="row">
				<div class="col-xs-12">
					<div class="panel panel-primary">
						<div class="panel-heading">
							<h2 class="panel-title">Past Trips</h2>
						</div>
						<div class="panel-body">
							<div class="container-fluid">
								<div class="row">
									<?php
										$date = date("Y-m-d");
										$sql = "select * from Calendar where (creator_email = '".$_SESSION["email"]."' or Calendar.calendar_id in (select calendar_id from Permissions where user_email = '".$_SESSION["email"]."')) and trip_start <= date '".$date."' order by trip_start desc limit 3;";
										$result = mysqli_query($db_conx, $sql) or die("Past trips query failed.");
										if($result->num_rows > 0) {
											while($row = $result->fetch_assoc()) {
												$trip_start = date('d M Y', strtotime($row['trip_start']));
												$trip_end = date('d M Y', strtotime($row['trip_end']));

												echo "<div class=\"trip_desc col-xs-4\"><h3>".$row["title"]."</h3><p>".$trip_start." - ".$trip_end."</p></div>";
											}
										}
										else {
											echo "You have no past trips.";
										}
									?>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<!-- jQuery -->
		<script src="js/jquery-2.0.0.min.js"></script>
        	<!-- Latest compiled and minified JavaScript for Bootstrap-->
        	<script src="js/bootstrap.min.js"></script>
		<!-- Custom script -->
		<script src='js/edit_trip.js'></script>
		<script src='js/new_trip.js'></script>
	</body>
</html>
