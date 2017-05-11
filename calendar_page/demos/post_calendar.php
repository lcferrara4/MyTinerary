<!DOCTYPE html>
<html>
<head>
<meta charset='utf-8' />
<link href='../fullcalendar.css' rel='stylesheet' />
<link href='../fullcalendar.print.css' rel='stylesheet' media='print' />
<script src='../lib/moment.min.js'></script>
<script src='../lib/jquery.min.js'></script>
<script src='../fullcalendar.min.js'></script>
<script>

	$(document).ready(function() {

		$('#calendar').fullCalendar({
			header: {
				left: 'prev,next today',
				center: 'title',
				right: 'month,agendaWeek,agendaDay'
			},
			defaultDate: '2016-09-12',
			navLinks: true, // can click day/week names to navigate views
			selectable: true,
			selectHelper: true,
			select: function(start, end) {
				var title = prompt('Enter Trip Title:');
        var budgetmax = prompt('Enter max price:');
				var eventData;
				if (title) {
					eventData = {
						title: title,
						start: start,
						end: end
					};
          eventData = {
						title: title,
						start: trip_start,
						end: trip_end
					};
					$('#calendar').fullCalendar('renderEvent', eventData, true); // stick? = true
				}
				$('#calendar').fullCalendar('unselect');
			},
			editable: true,
			eventLimit: true, // allow "more" link when too many events
			events: [
        {
          title: title,
          start: trip_start,
          end: trip_end
        };
				// {
				// 	title: 'Click for Google',
				// 	url: 'http://google.com/',
				// 	start: '2016-09-28'
        //   end: '2016-09-29'
				// }
			]
		});

	});

</script>
<style>

	body {
		margin: 40px 10px;
		padding: 0;
		font-family: "Lucida Grande",Helvetica,Arial,Verdana,sans-serif;
		font-size: 14px;
	}

	#calendar {
		max-width: 900px;
		margin: 0 auto;
	}

</style>
</head>

<body>

Title: <?php echo $_POST["title"]; ?><br>
Start Date: <?php echo $_POST["trip_start"]; ?>
End Date: <?php echo $_POST["trip_end"]; ?>
Min Price: <?php echo $_POST["price_min"]; ?>
Max Price: <?php echo $_POST["price_max"]; ?>


<div id='calendar'></div>

</body>
</html>
