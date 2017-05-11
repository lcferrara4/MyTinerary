<!DOCTYPE html>
<!--
	stuff.php receives json data from Place Search API call and inserts the place_id and name into our data so that we can use the place_id to later make a getDetails call
-->
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
$data = json_decode(file_get_contents('php://input'), true);
for($i = 0; $i < count($data); $i = $i + 1){
	$object = $data[$i];
	$place_id = $object['place_id'];
	$name = $object['name'];
	$name = str_replace('\'', '\'\'', $name);
		
	//insert values
	$sql_query = "INSERT INTO Site(place_id, name) VALUES ('$place_id', '$name')";

	if($conn->query($sql_query) === TRUE) {
		echo "New record created successfully";
	} else {
		echo "Error: " . $sql_query . "<br>" . $conn->error;
	}
}
//close connection
$conn->close();
?>
