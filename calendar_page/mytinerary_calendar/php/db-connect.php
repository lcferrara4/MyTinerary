<?php
  require dirname(__FILE__) . '/utils.php';
  
    #echo "DB_CONNECT DASDADAS";
    # Connect to database
    $db_conx = mysqli_connect("localhost", "marangu1", "mysql2016data", "mytinerary") or die("Unable to connect to database");
    if ($db_conx->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    else {

      # Request info from database
      // $sql = "SELECT place_id, name, category FROM Site;";
      $sql = "SELECT place_id, name FROM Site;";
      $result = mysqli_query($db_conx, $sql);

      $json_response = array();

      while ($row = mysqli_fetch_array($result, MYSQL_ASSOC)) {
          $row_array['place_id'] = $row['place_id'];
          $row_array['name'] = $row['name'];
          // $row_array['category'] = $row['category'];
          array_push($json_response,$row_array);
      }
      $json = json_encode($json_response);
    }

    echo $json;
    #mysql_close($db_conx);
?>
