$('.trip_desc').click(function(e) {
	var calendar_id = $(this).data('id');

	$.ajax({
		type: "POST",
		url: "calendar_page/mytinerary_calendar/createCalendarTrip.php",
		data: {'calendar_id': calendar_id},
		success: function() {
			console.log("Success!");

			// redirect to calendar
			window.location = 'calendar_page/mytinerary_calendar/createCalendarTrip.php';
		},
		error: function(e) {
			console.log(e);
		},
		dataType: "text"
	});
});
