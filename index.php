<?php require_once("WebApp/header.php"); ?>

		<form id="loginForm">
			<div id="login">
				<div id="login_title">
					<h5>Please login to continue</h5>
				</div>
				
				<div id="login_username">
					<ul>
						<li><label for="input_username">Username</label></li>
						<li><input id="input_username" type="text" />
						<li><label for="input_password">Password</label></li>
						<li><input id="input_password" type="password" /></li>
					</ul>

				<div id="login_submit">
					<input id="login_btn" type="submit" value="Login" />
				</div>
			</div>
		</form>

<?php require_once("WebApp/footer.php"); ?>


<script>
	//Put functions inside a document.ready...When the document has fully loaded these functions will perform. Outside of it you can put functions etc.
	$("document").ready(function(){
		
		//Calls a onclick when a specified ID is labeled
		$("#loginForm").submit(function (event){
			//Get the username and password entered
			var username = $("#input_username").val();
			var password = $("#input_password").val();
			
			//Start the AJAX call to request page, send in correct values
			$.ajax({
			  type: "POST",
			  url: "request.php",
			  data: { r : "user", q : "login", username : username, password : password},
			  success: function(result){
				//If successful....Do something with the result. Turn it into a JSON value!
				var json = jQuery.parseJSON(result);
				
				console.log(result); 
				alert(json.User.Username + " - " + json.User.Admin);
				
				//We'll have to save these variables for later use....I gotta brush up on Session variables but atm lets just get you up to stat on this
				
			  },
			  error: function (result){
				//If an error occurs...Do something (Display error messages)
				var error = result.status;
				switch(error){
					case 400 : alert("Bad Request - Missing username or password");break;
					case 401 : alert("Unauthorized - Invalid Password");break;
					case 404 : alert("User not found");break;
					default : alert("Unknown error has occured");
				}
			  }
			});
			
			//Return false to stop page from refreshing
			return false;
		});
	});
</script>