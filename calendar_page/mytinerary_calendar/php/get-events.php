<?php

  $user_id = $_POST["user_id"];
  $trip_duration = $_POST["trip_duration"];

  // echo $trip_duration;
  $trip_duration = (int)$trip_duration*3;

  require dirname(__FILE__) . '/utils.php';
    # Connect to database
    $db_conx = mysqli_connect("localhost", "marangu1", "mysql2016data", "mytinerary") or die("Unable to connect to database");
    if ($db_conx->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    else {
      # Request info from database
      $sql = "SELECT email, site_id, site_name FROM Suggestions WHERE email LIKE '" . $user_id . "' ORDER BY score DESC LIMIT ". $trip_duration . ";"; // LIMIT
      // echo $sql;
      $result = mysqli_query($db_conx, $sql);

      $json_response = array();
      while ($row = mysqli_fetch_array($result, MYSQL_ASSOC)) {
          // echo $row['site_name'];
          $row_array['email'] = $row['email'];
          $row_array['site_id'] = $row['site_id'];
          $row_array['site_name'] = $row['site_name'];
          array_push($json_response,$row_array);
      }
      $json = json_encode($json_response);
    }

    echo $json;
    #mysql_close($db_conx);
?>
