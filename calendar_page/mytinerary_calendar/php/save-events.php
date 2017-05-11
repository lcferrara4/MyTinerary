<?php

  $start = $_POST["start"];
  $end = $_POST["end"];
  $site_id = str_replace("'", "", $_POST["site_id"]);
  $calendar_id = $_POST["calendar_id"];

  require dirname(__FILE__) . '/utils.php';
    # Connect to database
    $db_conx = mysqli_connect("localhost", "marangu1", "mysql2016data", "mytinerary") or die("Unable to connect to database");
    if ($db_conx->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    else {
      # Request info from database
      $sql = "INSERT INTO Event (start_time, end_time, site_id, calendar_id) VALUES ('$start', '$end', '$site_id', $calendar_id);";

      if ($db_conx->query($sql) === TRUE) {
        echo "New record created successfully";
        exit;
      }
      else {
        echo "Error: " . $sql . "<br>" . $db_conx->error;
        exit(404);
      }
    }

    // echo $json;
    #mysql_close($db_conx);
?>
