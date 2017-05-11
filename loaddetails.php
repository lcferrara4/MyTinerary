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

$data = json_decode(file_get_contents('php://input'), true);
//get relevant data from json
$place_id = $data['place_id'];
$latitude = $data['geometry']['location']['lat'];
$longitude = $data['geometry']['location']['lng'];
$category = $data['types'][0];
$open_hour = $data['opening_hours']['periods'][0]['open']['hours'];
$open_minute = $data['opening_hours']['periods'][0]['open']['minutes'];
$close_hour = $data['opening_hours']['periods'][0]['close']['hours'];
$close_minute = $data['opening_hours']['periods'][0]['close']['minutes'];
$rating = $data['rating'];
$price = $data['price_level'];
$addr = $data['formatted_address'];
$open_time  = "00:".$open_hour.":".$open_minute;
$close_time = "00:".$close_hour.":".$close_minute;
//update values
//$sql_query = "UPDATE Site SET category = ".'"'.$category.'"'.", rating = ".'"'.$rating.'"'.", price = ".'"'.$price.'"'." WHERE place_id = ".'"'.$place_id.'"'.";";
$sql_query = "UPDATE Site SET open_time = ".'"'.$open_time.'"'.", close_time = ".'"'.$close_time.'"'.", lat = ".'"'.$latitude.'"'.", lon = ".'"'.$longitude.'"'.", addr = ".'"'.$addr.'"'." WHERE place_id = ".'"'.$place_id.'"'.";";
if($conn->query($sql_query) === TRUE) {
	echo $sql_query . " updated successfully";
} else {
	echo "Error: " . $sql_query . "<br>" . $conn->error;
}

//close connection
$conn->close();
?>
