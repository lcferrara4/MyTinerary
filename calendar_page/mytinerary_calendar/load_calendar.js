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
      console.log(inputSearch);
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
    console.log(calendar_id);
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
                       console.log("Saved events on Events with a new calendar_id");
                     },
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

