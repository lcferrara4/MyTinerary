<!DOCTYPE html>
<?php
//database info
$servername = "localhost";
$username = "cbadart";
$password = "Puppies!1";
$dbname = "mytinerary";

//create connection
$conn = new mysqli($servername, $username, $password, $dbname);
//check connection
if($conn->connect_error) {
	die("Connection failed: " . $conn->connect_error);
}
//get all the place_ids in the database
$sql_query = "SELECT place_id FROM Site WHERE addr is NULL";
$result = $conn->query($sql_query);
if($result->num_rows > 0) {
	//handle data of each row
	$res = [];
	while($row = $result->fetch_assoc()) {
		// add each place_id to res array
		array_push($res, $row["place_id"]);
	}
	//encode res array as json
	echo json_encode($res);
} else {
	echo "0 results";
}
//close connection
$conn->close();
?>
