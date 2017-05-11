<?php

session_start();

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
  $calendar_id = $_SESSION["calendar_id"];

  # Save info to database
  $sql = "UPDATE Calendar SET title='$title', trip_start='$trip_start', trip_end='$trip_end' WHERE calendar_id=$calendar_id;";
  if (mysqli_query($db_conx, $sql)) {
    echo "Existing calendar trip updated successfully";
  } else {
    echo "Error: " . $sql . "<br>" . mysqli_error($db_conx);
  }

}
mysqli_close($db_conx);
?>
