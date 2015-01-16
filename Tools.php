<?php
	include_once("API/globals.php");
?>
<script src="js/jquery.js"></script>
<script>

$(document).ready(function(){
	
	loadDropDownWithUsers();
	loadDropDownWithSurveys();
	
	$("#getAllUsers").click(function(){
		$.ajax({
		  type: "POST",
		  url: "request.php",
		  data: { r : "user", q : "all", ApiKey : "$2a$10$nZjAVjE8zqA.XhO741k99.1wbD1Z/IxORB76MJpLo4lKAjWK8pJRS" },
		  success: function(result){
			console.log(result);
		  }
		});
	});
	
	$("#getUserUsername").click(function(){
		$.ajax({
		  type: "POST",
		  url: "request.php",
		  data: { r : "user", q : "user", username : $("#getUserUinput").val(), ApiKey : "$2a$10$nZjAVjE8zqA.XhO741k99.1wbD1Z/IxORB76MJpLo4lKAjWK8pJRS" },
		  success: function(result){
			console.log(result);
		  }
		});
	});
	
	$("#getUserId").click(function(){
		$.ajax({
		  type: "POST",
		  url: "request.php",
		  data: { r : "user", q : "user", id : $("#getUserIDinput").val(), ApiKey : "$2a$10$nZjAVjE8zqA.XhO741k99.1wbD1Z/IxORB76MJpLo4lKAjWK8pJRS" },
		  success: function(result){
			console.log(result);
		  }
		});
	});
	
	$("#createUser").click(function(){
		$.ajax({
		  type: "POST",
		  url: "request.php",
		  data: { r : "user", q : "create", username : $("#createUserInputUN").val(), password : $("#createUserInputPS").val(), subject : $("#createUserInputSB").val(), admin : $("#createUserInputAD").val(), ApiKey : "$2a$10$nZjAVjE8zqA.XhO741k99.1wbD1Z/IxORB76MJpLo4lKAjWK8pJRS"},
		  success: function(result){
			console.log(result);
		  }
		});
	});
	
	$("#login").click(function(){
		$.ajax({
		  type: "POST",
		  url: "request.php",
		  data: { r : "user", q : "login", username : $("#loginUser").val(), password : $("#loginPass").val() },
		  success: function(result){
			console.log(result);
		  }
		});
	});
	
	$("#getAllSurveys").click(function(){
		$.ajax({
		  type: "POST",
		  url: "request.php",
		  data: { r : "survey", q : "all", ApiKey : "$2a$10$nZjAVjE8zqA.XhO741k99.1wbD1Z/IxORB76MJpLo4lKAjWK8pJRS" },
		  success: function(result){
			console.log(result);
		  }
		});
	});
	
	$("#getAllSurveysUserName").click(function(){
		$.ajax({
		  type: "POST",
		  url: "request.php",
		  data: { r : "survey", q : "user", username : $("#getAllSurveysUser").val(), ApiKey : "$2a$10$nZjAVjE8zqA.XhO741k99.1wbD1Z/IxORB76MJpLo4lKAjWK8pJRS" },
		  success: function(result){
			console.log(result);
		  }
		});
	});
	
	$("#getAllSurveysUserID").click(function(){
		$.ajax({
		  type: "POST",
		  url: "request.php",
		  data: { r : "survey", q : "user", id : $("#getAllSurveysID").val(), ApiKey : "$2a$10$nZjAVjE8zqA.XhO741k99.1wbD1Z/IxORB76MJpLo4lKAjWK8pJRS" },
		  success: function(result){
			console.log(result);
		  }
		});
	});
	
	$("#createSurvey").click(function(){
		$.ajax({
		  type: "POST",
		  url: "request.php",
		  data: { r : "survey", q : "create", survey_name : $("#createSurveyName").val(), survey_description : $("#createSurveyDescription").val(), survey_delay : $("#createSurveyDelay").val(), ApiKey : "$2a$10$nZjAVjE8zqA.XhO741k99.1wbD1Z/IxORB76MJpLo4lKAjWK8pJRS" },
		  success: function(result){
			console.log(result);
		  }
		});
	});
	
	$("#assignSurvey").click(function(){
		$.ajax({
		  type: "POST",
		  url: "request.php",
		  data: { r : "survey", q : "assign", ApiKey : "$2a$10$nZjAVjE8zqA.XhO741k99.1wbD1Z/IxORB76MJpLo4lKAjWK8pJRS" },
		  success: function(result){
			console.log(result);
		  }
		});
	});
});

	function loadDropDownWithUsers(){
		$.ajax({
		  type: "POST",
		  url: "request.php",
		  data: { r : "user", q : "all", ApiKey : "$2a$10$nZjAVjE8zqA.XhO741k99.1wbD1Z/IxORB76MJpLo4lKAjWK8pJRS" },
		  success: function(result){
				var json = jQuery.parseJSON(result);
				
				for(i = 0; i < json.Users.length; i++){
					$('#assignSurveysUser').append('<option value="'+json.Users[i].Id+'">'+json.Users[i].Username+'</option>');
				}
			}
		});
	}
	
	function loadDropDownWithSurveys(){
		$.ajax({
			  type: "POST",
			  url: "request.php",
			  data: { r : "survey", q : "all", ApiKey : "$2a$10$nZjAVjE8zqA.XhO741k99.1wbD1Z/IxORB76MJpLo4lKAjWK8pJRS" },
			  success: function(result){
					var json = jQuery.parseJSON(result);

					for(i = 0; i < json.Surveys.length; i++){
						$('#assignSurveySurvey').append('<option value="'+json.Surveys[i].Id+'">'+json.Surveys[i].Name+'</option>');
					}
				}
			});
	}
</script>
<h1>User - 1.0.0</h1>

<input id="getAllUsers" type="button" value="Get all users" />

<br /><br />

<input id="getUserUinput" type="text" placeholder="By Username" /> <input id="getUserUsername" type="submit" value="Get User" />

<br /><br />
<input id="getUserIDinput" type="number" placeholder="By Id" /> <input id="getUserId" type="submit" value="Get User" />

<br /><br />
<h5>Create User</h5>
<input id="createUserInputUN"type="text" placeholder="Username" /> <input id="createUserInputPS" type="password" placeholder="Password" /> <input id="createUserInputAD" type="number" placeholder="Admin lvl" /> <input id="createUserInputSB" type="number" placeholder="Subject 0 or 1" /><input id="createUser" type="submit" value="Create User" />

<br /><br />
<h5>Sign in</h5>
<input id="loginUser" type="text" placeholder="Username" /> <input id="loginPass" type="password" placeholder="Password" /> <input id="login" type="submit" value="Login" />

<br /><br /><br />
<h1>Survey - 1.0.0</h1>

<input id="getAllSurveys" type="button" value="Get all surveys" />

<br /><br />

<h5>Create Survey</h5>
<input id="createSurveyName" type="text" placeholder="Survey Name" /> <br />
<textarea id="createSurveyDescription" placeholder="Description -- Can be blank 140 Limit"></textarea> <br />
<input id="createSurveyDelay" type="number" placeholder="Delay - Saves in Minutes" /> <br />
<input id="createSurvey" type="button" value="Create Survey"/>
<br /><br /><br />

<h5>Assign Surveys</h5>
<select id="assignSurveysUser">
	<option value="NA">Select User</option>
</select> 
<select id="assignSurveySurvey">
	<option value="NA">Select Survey</option>
</select>
<input id="assignSurvey" type="button" value="Assign Survey" />

<h1>Developing ~ Dont touch</h1>
<h5>To-Do:</h5>
<ul>
	<li>Create new database table for ASSIGNED SURVEYS, Figure out what you want to do with surveys that are assigned to ALL users...</li>
</ul>
<br /><br />

<input id="getAllSurveysID" type="number" placeholder="All Surveys by ID" /> <input id="getAllSurveysUserID" type="button" value="Get Surveys" />

<br /><br />

<input id="getAllSurveysUser" type="text" placeholder="All Surveys by Username" /> <input id="getAllSurveysUserName" type="button" value="Get Surveys" />
