<?php
    $conn = mysqli_connect("localhost", "cbadart", "Puppies!1", "mytinerary");
    if($conn === false) {
        die("ERROR: Could not connect. " . mysqli_connect_error());
    }

    //get relevant data from form
    $user_email = mysqli_real_escape_string($conn, $_POST['User']);
    $calendar_id = mysqli_real_escape_string($conn, $_POST['CalendarID']);

    //attempt to insert
    $sql = "INSERT INTO Permissions (calendar_id, user_email) VALUES ('$calendar_id', '$user_email')";

    if(mysqli_query($conn, $sql)) {
        //echo "Records added successfully. " . $sql;
    } else {
        //echo "ERROR: Could not execute " . mysqli_error($conn);
    }

    //close connection
    mysqli_close($conn);

    //redirect to calendar_list
    echo '<script type="text/javascript">
             window.location = "https://dsg1.crc.nd.edu/cse30246/dat_base/calendar_list.php"
        </script>';
?> 

