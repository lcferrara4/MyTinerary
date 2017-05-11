$("#change_password").click(function(e) {

	// prevent page from refreshing
	e.preventDefault();

	// dusplay the change password form
	$("#change_password_form").show();
});

$("#cancel_change_password").click(function(e) {

	// prevent page from reloading
	e.preventDefault();

	// hide form error messages
	$(".error-list").empty();
	$(".error-list").hide();
	$("#old_password, #new_password, #confirm_password").removeClass("error")	

	// hide change password form
	$("#change_password_form").hide();
});

$("#change_password_btn").click(function(e) {

	// prevent default form submission
	e.preventDefault();

	// remove error messages and markers
	$("#old_password, #new_password, #confirm_password").removeClass("error");
	$(".error-list").hide();
	$(".error-list").empty();

	// initialize error flag
	var error = false;

	// retrieve values from form
	var old_password = $("#old_password").val();
	var new_password = $("#new_password").val();
	var confirm_password = $("#confirm_password").val();

	if(new_password.length < 8) {
		$("#new_password, #confirm_password").addClass("error");
		$(".error-list").append("<li>Password must be at least 8 characters</li>");
		error = true;
	}

	if(new_password != confirm_password) {
		$("#new_password, #confirm_password").addClass("error");
		$(".error-list").append("<li>Passwords do not match</li>");
	}

	if(!error) {
		// POST request
		$.ajax({
			type: "POST",
			url: "account.php",
			data: {"old_password": old_password, "new_password": new_password, "confirm_password": confirm_password},
			success: function() {

				console.log("Successfully updated password!");
			
				// remove error messages and markers
				$("#old_password, #new_password, #confirm_password").removeClass("error");
				$(".error-list").hide();
				$(".error-list").empty();
	
				// hide change password form
				$("#change_password_form").hide();
			},
			error: function() {

				console.log("An error occurred. Please ensure that your current password is correct and try again.");

				// display error message
				$(".error-list").append("<li>An error occurred. Please ensure that your current password is correct and try again.</li>");
				$(".error-list").show();
			},
			dataType: "text"
		});
	}
	else {
		$(".error-list").show();
	}
});

$("#delete_account").click(function(e) {

	// prevent page refresh
	e.preventDefault();

	// ask user to confirm that they would like to delete their account
	if(confirm("Are you sure you want to delete your account?")) {

		// run code on server to delete account
		$.ajax({
			type: "POST",
			url: "delete_account.php",
			success: function() {
				console.log("Success!");

				// redirect user to create_account page
				window.location = "create_account.php";
			},
			error: function(e) {
				console.log(e);
			},
			dataType: "text"
		});
	}
});
