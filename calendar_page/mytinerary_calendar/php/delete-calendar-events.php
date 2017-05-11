<?php

  // $creator_email = $_POST["creator_email"];
  $calendar_id = $_POST["calendar_id"];
  echo $_POST['calendar_id'];
  require dirname(__FILE__) . '/utils.php';
    # Connect to database
    $db_conx = mysqli_connect("localhost", "marangu1", "mysql2016data", "mytinerary") or die("Unable to connect to database");
    if ($db_conx->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    else {
      # Request info from database
      $sql = "DELETE FROM Event WHERE calendar_id=$calendar_id;";
      // $sql = "DELETE FROM Event WHERE creator_email='$creator_email' AND calendar_id=$calendar_id;";
      echo $sql;

      if ($db_conx->query($sql) === TRUE) {
        echo "DELETE FROM Event WHERE calendar_id=$calendar_id;";
        echo "Deleted calendar events successfully";
      }
      else {
        echo "Error: " . $sql . "<br>" . $db_conx->error;
      }
    }

    // echo $json;
    #mysql_close($db_conx);
?>
