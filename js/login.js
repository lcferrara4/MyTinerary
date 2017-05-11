$("#submit").click(function(e) {

	// prevent form submission on button click
	e.preventDefault()

	// retrieve values from form
	var email = $("#email").val();
	var password = $("#password").val();

	// remove error messages
	$("small").remove();

	// submit ajax POST request
	$.ajax({
		type: "POST",
		url: "login.php",
		data: {"email": email, "password": password},
		success: function() {

			console.log("Login successful!");

			// redirect user to dashboard
			window.location = "dashboard.php";
		},
		error: function(e) {

			console.log(e);

			// empty password form field
			$("#password").val("");

			// display errors
			$("label[for='email']").append("<small>*" + e.responseText + "</small>");
		},
		dataType: "text"
	});
});
