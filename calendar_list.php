<?php

	session_start();

	if(!isset($_SESSION["email"])) {
		header("Location: login.php");
		exit;
	}
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
		<link rel="stylesheet" type="text/css" href="css/calendar_list.css"/>

	</head>

	<body>
		<div class="nav">	
		    <ul class="nav nav-tabs">
			<li class="dashboard"><a href="dashboard.php" title="Dashboard">Dashboard</a></li>
			<li class="calendar index current active"><a href="calendar_list.php" title="Calendar">Calendar</a></li>
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

		<div class="jumbotron">
			<h1>Calendars</h1>
		</div>
		<div class="container">
			<?php

				$db_conx = mysqli_connect("localhost", "csmick", "allAboutDatBase", "mytinerary");

				$sql = "select * from Calendar where creator_email = '".$_SESSION["email"]."' or Calendar.calendar_id in (select calendar_id from Permissions where user_email = '".$_SESSION["email"]."') order by trip_start desc;";
				$result = mysqli_query($db_conx, $sql) or die("Trips query failed.");
				if($result->num_rows > 0) {
					echo "<div class=\"row\">
                            <div class=\"col-xs-12\">
                                <a class=\"new_trip_btn btn btn-primary\">Create New Calendar</a>
                            </div>
                        </div>";
                    echo "<div class='calendar_list'>";
					while($row = $result->fetch_assoc()) {
                        $cal_id = $row['calendar_id'];
						$trip_start = date('M d Y', strtotime($row['trip_start']));
						$trip_end = date('M d Y', strtotime($row['trip_end']));
                        $creator = $row['creator_email'];
						echo "<div class='row trip_row'>";
                        if($creator === $_SESSION["email"]) {
                            echo " 
                               <div class=\"calendar_option_btn_grp\">
                                    <button type=\"button\" class=\"openMyModal btn btn-primary\" id=\"shareButton\" data-id=\"".$cal_id."\" data-toggle=\"modal\" data-target=\"#myModal\">Share</button>
                                    <button type=\"button\" class=\"openDeleteModal btn btn-primary\" id=\"deleteButton\" data-id=\"".$cal_id."\" data-toggle=\"modal\" data-target=\"#deleteModal\">Delete</button>
                                </div>";
                        }
                        echo "<div style=\"height: 112px;\" class=\"trip_desc col-xs-12\" data-id='".$row['calendar_id']."'><h3>".$row["title"]."</h3>";
                                    if($creator !== $_SESSION["email"]) {
                                        echo "<h5>Shared by: ";
                                        $user_query = "select first_name, last_name, email from Users where email = '".$creator."';";
                                        $res = mysqli_query($db_conx, $user_query) or die("User query failed.");
                                        while($user = $res->fetch_assoc()) {
                                            echo $user['first_name']." ".$user['last_name'] ;
                                        }
                                    }
                                    
                        echo "
                                    </h5>
                                    <p>".$trip_start." - ".$trip_end."</p>
                                </div>
                            </div>";
					}
					echo "</div>";
				}
				else {
					echo "<div class='center_text'><h1 style=\"text-align: center; font-size: 36px;\">You have no trips planned.</h1><a href='calendar_page/mytinerary_calendar/createCalendarTrip.php' class=\"btn btn-primary\">Create a new calendar!</a></div>";
				}
			?>
		</div>
        <!-- Modal -->
        <div id="myModal" class="modal fade" role="dialog">
            <div class="modal-dialog">
                <!-- Modal content -->
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" style="color: white;">&times;</button>
                        <h4 class="modal-title">Share</h4>
                    </div>
                    <div class="modal-body">
                        <form action="share_calendar.php" method="post" id="ShareForm">
                            <div class="row">
                                <div class="col-xs-3">
                                    <label for="users">User Email:</label>
                                </div>
                                <div class="col-xs-9">
                                    <input type="text" name="User" id="users" style="width: 100%; border: 1px solid #e5e5e5;">
                                </div>
                            </div>
                            <input type="text" name="CalendarID" id="CalendarID" value="" class="hidden">
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button form="ShareForm" type="submit" value="Save" class="btn btn-primary">Save</button>
                    </div>
                </div>
            </div>
        </div>
        <!--Delete Calendar Modal-->
        <div id="deleteModal" class="modal fade" role="dialog" style="width: 50%;">
            <div class="modal-dialog" style="width: inherit;">
                <div class="modal-content" style="min-width: 192px;">
                    <div class="modal-body">
                        <h4 style="text-align: center;">Delete Calendar?</h4>
                    </div>
                    <div class="modal-footer" style="text-align: center">
                        <form action="delete_calendar.php" method="post" id="DeleteForm">
                            <input type="text" name="DCalendarID" id="DCalendarID" value="" class="hidden">
                        </form>
                        <button type="buttoin" class="btn btn-danger" data-dismiss="modal">No</button>
                        <button form="DeleteForm" type="submit" value="Delete" class="btn btn-success">Yes</button>
                    </div>
                </div>
            </div>
        </div>
	<!-- jQuery -->
	<script src="js/jquery-2.0.0.min.js"></script>
        <!-- Latest compiled and minified JavaScript for Bootstrap-->
        <script src="js/bootstrap.min.js"></script>
	<!-- Custom script -->
	<script src="js/edit_trip.js"></script>
	<script src="js/new_trip.js"></script>
        <script src="js/calendar_list.js"></script>
	</body>
</html>
