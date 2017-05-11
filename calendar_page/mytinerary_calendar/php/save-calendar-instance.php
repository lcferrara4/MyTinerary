<?php
session_start();

 # ensure that a user is logged in
 if(!isset($_SESSION["email"])) {
         # redirect user to login page
         header("Location: login.php");
         exit;
 }

# Connect to database
$db_conx = mysqli_connect("localhost", "marangu1", "mysql2016data", "mytinerary") or die("Unable to connect to database");
if ($db_conx->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
else {
  # Create local variables from the posted data
  $title = $_POST["title"];
  $trip_start = $_POST["start"];
  $trip_end = $_POST["end"];
  $creator_email = $_SESSION["email"];

  # Save info to database
  $sql = "INSERT INTO Calendar (title, trip_start, trip_end, creator_email) VALUES ('".$title."', '".$trip_start."', '".$trip_end."', '".$creator_email."');";

  if (!mysqli_query($db_conx, $sql)) {
    echo "Error: " . $sql . "<br>" . mysqli_error($db_conx);
    exit;
  }

  # TODO TESTING this
  $sql = "SELECT calendar_id FROM Calendar WHERE title = '$title' AND trip_start = '$trip_start' AND trip_end = '$trip_end' AND creator_email = '$creator_email' ORDER BY calendar_id DESC LIMIT 1;";
  $result = mysqli_query($db_conx, $sql);

  if (!$result) {
    echo "DB Error, could not find calendar_id of calendar just created.\n";
    echo 'MySQL Error: ' . mysql_error();
    exit;
  }

  $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
  $_SESSION["calendar_id"] = $row['calendar_id']; // Update calendar_id so user can edit w/o reloading
  echo $row['calendar_id'];

}
mysqli_close($db_conx);
?>
