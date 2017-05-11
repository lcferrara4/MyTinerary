$("#submit").click(function(e) {

	// prevent form submission on button click
	e.preventDefault();

	// hide and remove php errors
	$("#error_list").attr("hidden", true);
	$("#error_list ul").replaceWith("");
	
	// retrieve values from form
	var food = $("#food").is(":checked");
	var bar = $("#bar").is(":checked");
	var shopping = $("#shopping").is(":checked");
	var sports = $("#sports").is(":checked");
	var museums = $("#museums").is(":checked");
	var sightseeing = $("#sightseeing").is(":checked");
	var art = $("#art").is(":checked");
	var theater = $("#theater").is(":checked");
        var one_dollar = $("#1dollar").is(":checked");
        var two_dollar = $("#2dollar").is(":checked");
        var three_dollar = $("#3dollar").is(":checked");

	// POST request
	$.ajax({
		type: "POST",
		url: "account.php",
		data: {"food": food, "bar": bar, "shopping": shopping, "sports": sports, "museums": museums, "sightseeing": sightseeing, "art": art, "theater": theater, "one_dollar": one_dollar, "two_dollar": two_dollar, "three_dollar": three_dollar},
		success: function(result) {

			console.log('Preferences updated successfully!');

			// redirect to login page
			window.location = "dashboard.php";
		},
		error: function(e) {

			// print error message
			console.log("Preferences could not be updated.");
			console.log(e);

			// render error in browser
			$("#error_list ul").replaceWith("<ul>" + e.responseText + "</ul>");
			$("#error_list").removeAttr("hidden");
		},
		dataType: "json"
	});
});
