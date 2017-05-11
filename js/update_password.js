$("#submit").click(function(e) {

        // post request will not be sent if error = true
        var error = false;

        // prevent form submission on button click
        e.preventDefault();

        // clear errors
        $("#error_list").attr("hidden");
        $("#error_list ul li").replaceWith("")

        // remove error class and messages from all elements
        $("#old_password").removeClass("error");
        $("label[for='old_password'] small").attr("hidden", true);
        $("#new_password").removeClass("error");
        $("label[for='new_password'] small").attr("hidden", true);
        $("#confirm_password").removeClass("error");
        $("label[for='confirm_password'] small").attr("hidden", true);

	// retrieve values from form
	var old_password = $("#old_password").val();
	var new_password = $("#new_password").val();
	var confirm_password = $("#confirm_password").val();

	// check validity of password
	if(new_password.length < 8) {
		$("#new_password").addClass("error");
		$("label[for='new_password'] small").removeAttr("hidden");
		$("#confirm_password").addClass("error");
		error = true;
	}

	if(new_password != confirm_password) {
		$("#new_password").addClass("error");
		$("#confirm_password").addClass("error");
		$("label[for='confirm_password'] small").removeAttr("hidden");
		error = true;
	}

	// submit for if there are no errors
	if(!error) {
		// POST request
		$.ajax({
			type: "POST",
			url: "update_password.php",
			data: {"old_password": old_password, "new_password": new_password, "confirm_password": confirm_password},
			success: function(result) {

				console.log('Password updated successfully!');

				// redirect to login page
				window.location = "dashboard.php";
			},
			error: function(e) {

				// print error message
				console.log("Password could not be updated.");
				console.log(e);

				// render error in browser
				$("#error_list ul").replaceWith("<ul>" + e.responseText + "</ul>");
				$("#error_list").removeAttr("hidden");
			},
			dataType: "json"
		});
	}
});
