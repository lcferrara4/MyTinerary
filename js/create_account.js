$("#submit").click(function(e) {

	// prevent form submission on button click
	e.preventDefault();

	// post request will not be sent if error = true
	var error = false;

	// remove error class and messages from all elements
	$("#firstName").removeClass("error");
	$("label[for='firstName'] small").attr("hidden", true);
	$("#lastName").removeClass("error");
	$("label[for='lastName'] small").attr("hidden", true);
	$("#email").removeClass("error");
	$("label[for='email'] small").attr("hidden", true);
	$("#password").removeClass("error");
	$("label[for='password'] small").attr("hidden", true);
	$("#confirmPassword").removeClass("error");
	$("label[for='confirmPassword'] small").attr("hidden", true);

	// retrieve values from form
	var firstName = $("#firstName").val();
	var lastName = $("#lastName").val();
	var email = $("#email").val();
	var password = $("#password").val();
	var confirmPassword = $("#confirmPassword").val();

	// check validity of first name
	if(firstName.length < 2) {
		$("#firstName").addClass("error");
		$("label[for='firstName'] small").removeAttr("hidden");
		error = true;
	}

	// check validity of last name
	if(lastName.length < 2) {
		$("#lastName").addClass("error");
		$("label[for='lastName'] small").removeAttr("hidden");
		error = true;
	}

	// check validity of email
	// NOTE: this does not guarantee validity of email address
	var emailPattern = /^([a-z\d!#$%&'*+\-\/=?^_`{|}~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]+(\.[a-z\d!#$%&'*+\-\/=?^_`{|}~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]+)*|"((([ \t]*\r\n)?[ \t]+)?([\x01-\x08\x0b\x0c\x0e-\x1f\x7f\x21\x23-\x5b\x5d-\x7e\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|\\[\x01-\x09\x0b\x0c\x0d-\x7f\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))*(([ \t]*\r\n)?[ \t]+)?")@(([a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|[a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF][a-z\d\-._~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]*[a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])\.)+([a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|[a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF][a-z\d\-._~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]*[a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])\.?$/i;
	
	if(!emailPattern.test(email)) {
		$("#email").addClass("error");
		$("label[for='email'] small").removeAttr("hidden");
		error = true;
	}

	// check validity of password
	if(password.length < 8) {
		$("#password").addClass("error");
		$("label[for='password'] small").removeAttr("hidden");
		$("#confirmPassword").addClass("error");
		error = true;
	}

	if(password != confirmPassword) {
		$("#password").addClass("error");
		$("#confirmPassword").addClass("error");
		$("label[for='confirmPassword'] small").removeAttr("hidden");
		error = true;
	}

	// submit for if there are no errors
	if(!error) {
		// POST request
		$.ajax({
			type: "POST",
			url: "create_account.php",
			data: {"firstName": firstName, "lastName": lastName, "email": email, "password": password, "confirmPassword": confirmPassword},
			success: function(result) {

				console.log('Account created successfully!');

				// redirect to login page
				window.location = "login.php";
			},
			error: function(e) {

				// print error message
				console.log("Account could not be created.");
				console.log(e);
			},
			dataType: "text"
		});
	}
});
