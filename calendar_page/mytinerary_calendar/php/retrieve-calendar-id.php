DASDASDASDASDASDASDASDASDASDASDAS

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
  $sql = "SELECT calendar_id FROM WHERE Calendar (title, trip_start, trip_end, price_min, price_max, creator_email) VALUES ('".$title."', '".$trip_start."', '".$trip_end."', '".$price_min."', '".$price_max."', '".$creator_email."');";
  if (mysqli_query($db_conx, $sql)) {
    echo "New calendar trip created successfully";
  } else {
    echo "Error: " . $sql . "<br>" . mysqli_error($db_conx);
  }

}
mysqli_close($db_conx);
?>
