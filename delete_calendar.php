<?php
    $conn = mysqli_connect("localhost", "cbadart", "Puppies!1", "mytinerary");
    if($conn === false) {
        die("ERROR: Could not connect. " . mysqli_connect_error());
    }
    $calendar_id = mysqli_real_escape_string($conn, $_POST['DCalendarID']);
    $sql = "DELETE FROM Calendar WHERE calendar_id = ".$calendar_id.";";
    mysqli_query($conn, $sql);

    mysqli_close($conn);
    header("Location: https://dsg1.crc.nd.edu/cse30246/dat_base/calendar_list.php");
?>
