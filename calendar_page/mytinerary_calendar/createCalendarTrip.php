<?php
  session_start();

  # ensure that a user is logged in
  if(!isset($_SESSION["email"])) {
    # redirect user to login page
    header("Location: ../../login.php");    //TODO: Update this so that it works
    exit;
  }
  $creator_email = $_SESSION["email"];

  // Create connection
  $conn = new mysqli('localhost', 'csmick', 'allAboutDatBase', 'mytinerary');
  // Check connection
  if ($conn->connect_error) {
      die("Connection failed: " . $conn->connect_error);
  }

  # Query for trip title, dates, and events
  if(isset($_SESSION["calendar_id"]) && $_SESSION["calendar_id"] != "0") {
    $sql = "SELECT * FROM Calendar WHERE calendar_id = ".$_SESSION["calendar_id"].";";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    $title = $row["title"];
    $trip_start = $row["trip_start"];
    $trip_end = $row["trip_end"];
    $sql = "SELECT * FROM Event, Site WHERE calendar_id = ".$_SESSION["calendar_id"]." and Event.site_id = Site.place_id;";
    $result = $conn->query($sql);
    $json_response = array();
    if ($result->num_rows > 0) {
      while ($row = mysqli_fetch_array($result, MYSQL_ASSOC)) {
          $row_array['title'] = $row['name'];
          $row_array['id'] = $row['place_id'];
          $row_array['start'] = str_replace(' ', 'T', $row['start_time']);
          $row_array['end'] = str_replace(' ', 'T', $row['end_time']);
          array_push($json_response,$row_array);
      }
    }
    $events = json_encode($json_response);
  }
  else {
    $title = "";
    $trip_start = "";
    $trip_end = "";
    $events = json_encode(json_decode("{}"));
  }


  # Check if calendar_id is passed in. If not, set to 0.
  if (isset($_POST["calendar_id"])) {
    $_SESSION["calendar_id"] = $_POST["calendar_id"];
    exit;
  }

?>

<!DOCTYPE html>
<html>
<head>
<meta charset='utf-8' />
<title>MyTinerary | Calendar</title>
<link rel="stylesheet" type="text/css" href="../../css/bootstrap.min.css"/>  <!-- Bootstrap -->

<link href='../fullcalendar.css' rel='stylesheet' />
<link href='../fullcalendar.print.css' rel='stylesheet' media='print' />
<script src='../lib/moment.min.js'></script>
<!-- jQuery -->
<script src="../../js/jquery-2.0.0.min.js"></script>
<!-- Latest compiled and minified JavaScript for Bootstrap-->
<script src="../../js/bootstrap.min.js"></script>
<!-- <script src='../lib/jquery.min.js'></script>  -->

<script src='../lib/jquery-ui.min.js'></script>     <!-- For dragging events -->
<script src='../fullcalendar.min.js'></script>
<!-- <script src='../json/calendar.js'></script> --> <!-- TODO not sure what this does and why it's not there -->
<!-- Custom theme -->
<link rel="stylesheet" type="text/css" href="../../css/style.css"/>

<script>// src='load_calendar.js'>
// Load this when page is ready:
  $(document).ready(function() {
    // Get info from input
    var creator_email = "<?php echo $creator_email?>";
    var duration = 4; //Hard-coded because we aren't obligating user to fill out trip info (title and dates) before they drag elements
    var today = new Date();

    // Load sites events as external events
    $.ajax({
      type: "POST",
      url: "php/get-events.php",
      data: {'user_id':creator_email, 'trip_duration':duration},
      success: function(eventData) {
        $.each(eventData, function(index, event) {
          var data = "'place_id':'" + event.site_id + "', 'title':'" + event.site_name + "'";
          $('#external-events').append("<div class='fc-event' data-event= {" + data + "'}'>" + event.site_name + "</div>");
        });

        $('#external-events div.fc-event').each(function() {
            $(this).data('event', {
                 title: $.trim($(this).text()), // use the element's text as the event title
                 id: (($(this).attr('data-event')).split(":")[1]).split(",")[0],  // TODO
                 stick: true // maintain when user navigates (see docs on the renderEvent method)
            });
            // make the event draggable using jQuery UI
            $(this).draggable({
              zIndex: 999,
              revert: true,      // will cause the event to go back to its
              revertDuration: 0  //  original position after the drag
            });
        });
      },
      error: function(e) {
        console.log(e);
      },
      dataType: "json"
    });
   // Update events shown according to search input
    $("#search").keyup(function() {
      var inputSearch = $("#search").val();
      var urlSearch;
      var HTMLAppendSearch;
      var dataSearch;
      var allOrSuggested; // All = 0; Suggested = 1
      if (inputSearch.length > 0) { // Something is being searched; search in all files
        urlSearch = "php/search.php";
        dataSearch = {'searchrequest': inputSearch};
        allOrSuggested = 0;
      }
      else {  // Nothing is being searched; only show suggested events
        urlSearch = "php/get-events.php";
        dataSearch = { 'user_id' : creator_email, 'trip_duration': duration};
        allOrSuggested = 1;
      }
      $.ajax({
        type: "POST",
        url: urlSearch,
        data: dataSearch,
        success: function(eventData) {
          $('#external-events .fc-event').each( function(index, event) {
            event.remove();
          });
          $.each(eventData, function(index, event) {
            if (!allOrSuggested) {
              HTMLAppend = "<div class='fc-event' data-event='{'title':'" + event.name + "', 'place_id':'" + event.place_id + "'}'>" + event.name + "</div>";
            }
            else {
              HTMLAppend = "<div class='fc-event' data-event='{'title':'" + event.site_name + "', 'place_id':'" + event.site_id + "'}'>" + event.site_name + "</div>";
            }
            $('#external-events').append(HTMLAppend);
          });
          $('#external-events div.fc-event').each(function() {
              $(this).data('event', {
                   title: $.trim($(this).text()), // use the element's text as the event title                   stick: true // maintain when user navigates (see docs on the renderEvent method)
              });
              // make the event draggable using jQuery UI
              $(this).draggable({
                zIndex: 999,
                revert: true,      // will cause the event to go back to its
                revertDuration: 0  //  original position after the drag
              });
          });
        },
        error: function(e) {
          console.log(e);
        },
        dataType: "json"
      });
    });

    $('#calendar').fullCalendar({
      events: <?php echo $events; ?>,
      forceEventDuration: true,
      header: {
        left: 'prev,next today',
        center: 'title',
        right: 'month,agendaWeek,agendaDay'
      },
      navLinks: true,   // Can click day/week names to navigate views
      editable: true,
      eventLimit: true, // Allow "more" link when too many events
      droppable: true,  // Can drop items
      defaultDate: today
    });

    var calendar_id = "<?php echo $_SESSION["calendar_id"] ?>";
    var title = "<?php echo $title; ?>";
    var trip_start = "<?php echo $trip_start; ?>";
    var trip_end = "<?php echo $trip_end; ?>";

    if (calendar_id!=0 && title!=null && trip_start!=null && trip_end!=null) {
      var tripEvent = [{
        "title" : title,
        "start" : trip_start,
        "end" : trip_end,
        backgroundColor: '#98D2E6',
        rendering: 'background'
      }];
      $("#calendar").fullCalendar('addEventSource', tripEvent);
    }
   // populate text boxes
    $("#title").val(title);
    $("#trip_start").val(trip_start);
    $("#trip_end").val(trip_end);

    // Save calendar
    $('#save-button').click(function(){
       // Check to see if input text is filled. If any is empty, throw error and return.
       var title = $('#title').val();
       var trip_start = $('#trip_start').val();
       var trip_end = $('#trip_end').val();

       if (!(title && trip_start && trip_end)) {
         alert("Cannot save calendar if title, trip start, and trip end are not set.");
         return;
       }

       if (trip_end < trip_start) {
         alert("Cannot save calendar if trip end date is before the start date.");
         return;
       }

       if (calendar_id == 0) {  // It was not set, create a new calendar entry
         console.log("calendar_id not set");
         // Collect info from fields and call request to save in Calendar table
         $.ajax({
           url: 'php/save-calendar-instance.php',
           data: {'title':title, 'start':trip_start, 'end':trip_end},
           type: "POST",
           dataType: "text",
           success: function(json) {
             console.log("Calendar instance created.");
             var new_calendar_id = json;
             calendar_id = new_calendar_id;
             console.log(new_calendar_id);
             // Collect info about all events. Call request to save in Event table
             var calendarEvents = $("#calendar").fullCalendar('clientEvents');
             $.each(calendarEvents, function(index, event) {
               console.log(event.id);
               console.log("Start: " + event);
               if(event.id != null) {
                 $.ajax({
                     url: 'php/save-events.php',
                     data: {'start':event.start.format(), 'end':event.end.format(), 'site_id':event.id, 'calendar_id': new_calendar_id},   // TODO: save time of end
                     type: "POST",
                     success: function(json) {
                       console.log("Saved events on Events with a new calendar_id");                     },
                     error: function(e) {
                       console.log(e);
                     },
                     dataType: "json"
                   });
                 }
              });
           },
           error: function(e) {
             console.log(e);
           }
         });

       }
       else { // Calendar already exists and is being edited.  calendar_id != 0
         console.log("calendar_id found");
         // Update calendar instance (in case the title or dates changed)
         title = $('#title').val();
         $.ajax({
           url: 'php/update-calendar-instance.php',
           data: {'calendar_id': calendar_id, 'title':title, 'start':trip_start, 'end':trip_end},
           type: "POST",
           success: function(json) {
              console.log("Calendar instance updated");
           },
           error: function(e) {
              console.log(e);
           }
         });
         console.log('CALENDAR_ID: ' + calendar_id);
        // Delete all events saved with that calendar_id
         $.ajax({
           url: 'php/delete-calendar-events.php',
          data: {'calendar_id': calendar_id},
          type: "POST",
           success: function(json) {
             console.log("Deleted all events that had been saved with that calendar_id");
           },
           error: function(e) {
             console.log(e);
           },
           dataType: "text"
         });

         // Collect info about all events. Call request to save in Event table
         var calendarEvents = $("#calendar").fullCalendar('clientEvents');
         $.each(calendarEvents, function(index, event) {
           console.log(event.id);
           console.log(event);
           if(event.id != null) {
             $.ajax({
                 url: 'php/save-events.php',
                 data: {'start':event.start.format(), 'end':event.end.format(), 'site_id':event.id, 'calendar_id': calendar_id},   // TODO: save time of end
                 type: "POST",
                 success: function(json) {
                   console.log("Saved events on Events with a new calendar_id");
                 },
                 error: function(e) {
                   console.log(e);
                 },
                 dataType: "json"
               });
             }
          });
       }
       alert("Your trip was saved successfully.");
    });

    $('#trip_info').change(function() {
      var title = $('#title').val();
      var trip_start = $('#trip_start').val();
      var trip_end = $('#trip_end').val();

      if (trip_start) {
        // Move calendar to date of event
        $('#calendar').fullCalendar('gotoDate', trip_start);
      }

      if (title && trip_start && trip_end) {
        // Add trip event to calendar as background event
        var trip_event = [{
          title: title,
          start: trip_start,
          end: trip_end,
          backgroundColor: '#98D2E6',
          rendering: 'background'
        }];
        $("#calendar").fullCalendar('addEventSource', trip_event);
      }

      if (trip_start && trip_end) {
        if (trip_end < trip_start) {
          alert("Your trip must end before it begins.");
        }
      }
    });

  });

</script>
</head>
<style>
	body {
		text-align: center;
		font-size: 14px;
		font-family: "Lucida Grande",Helvetica,Arial,Verdana,sans-serif;
	}
	#external-events {
		float: left;
		width: 150px;
		padding: 0 10px;
		border: 1px solid #ccc;
		background: #eee;
		text-align: left;
	}

	#external-events h4 {
		font-size: 16px;
		margin-top: 0;
		padding-top: 1em;
	}

	#external-events .fc-event {
		margin: 10px 0;
		cursor: pointer;
	}

	#external-events p {
		margin: 1.5em 0;
		font-size: 11px;
		color: #666;
	}

	#external-events p input {
		margin: 0;
		vertical-align: middle;
	}

	#calendar {
		float: right;
	}

</style>

<body>
  <!-- Navigation bar -->
  <div class="nav" style="margin-bottom: 20px;">
      <ul class="nav nav-tabs">
          <li class="dashboard"><a href="../../dashboard.php" title="Dashboard">Dashboard</a></li>
          <li class="calendar"><a href="../../calendar_list.php" title="Calendar">Calendar</a></li>
          <li class="settings pull-right" style="margin-right: 15px;">
                  <div class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Settings<span class="caret"></span></div>
                  <div class="dropdown-menu">
                          <a class="dropdown-item" href="../../account.php" title="My Account"><div class="full">My Account</div></a>
                          <div class="dropdown-divider"></div>
                          <a class="dropdown-item" href="../../logout.php"><div class="full">Logout</div></a>
                  </div>
          </li>
      </ul>
  </div>

  <div class='container-fluid'>
  <div id='container' class='row'>
      <div class='col-sm-6'>
  <form id="trip_info" align="left">
    Title of the trip: <input class="create-trip" type="text" id="title" name="title" align="left"><br>
    Start date: <input class="create-trip" type="date" id="trip_start" name="trip_start" align="left"><br>
    End date: <input class="create-trip" type="date" id="trip_end" name="trip_end" align="left"><br>
  </form>
    <br>
    <input type="button" value="Save Events" id="save-button" style="float: left" >
    <br>
  </div>
  </div>
  </div>
  <div class='container-fluid'>
    <div class='row'>
      <div class='col-sm-2'>
        <div id='external-events'>
          <h4>Attractions</h4>
          <form>
          <input id="search" type="text" name="search" placeholder="Search for attractions" style="width: 100%;"><br>
          </form>
        </div>
      </div>
      <!-- <div class='col-sm-10'> -->
        <div id='container' class='col-sm-10'>
          <div id='calendar'></div></center>
        </div>
      <!-- </div> -->

      <div style='clear:both'></div>
    </div>
  </div>

</body>
</html>
