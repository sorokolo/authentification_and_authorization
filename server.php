<?php
session_start();

// initializing variables
$username = "";
$email    = "";
$errors = array(); 

$db = mysqli_connect('localhost', 'root', '', 'registration');

if (isset($_POST['reg_user'])) {
  $username = mysqli_real_escape_string($db, $_POST['username']);
  $email = mysqli_real_escape_string($db, $_POST['email']);
  $password_1 = mysqli_real_escape_string($db, $_POST['password_1']);
  $user_type = mysqli_real_escape_string($db, $_POST['user_type']);

  if (empty($username)) { array_push($errors, "Username is required"); }
  if (empty($email)) { array_push($errors, "Email is required"); }
  if (empty($password_1)) { array_push($errors, "Password is required"); }

  $user_check_query = "SELECT * FROM users WHERE username='$username' OR email='$email' LIMIT 1";
  $result = mysqli_query($db, $user_check_query);
  $user = mysqli_fetch_assoc($result);
  
  if ($user) { 
    if ($user['username'] === $username) {
      array_push($errors, "Username already exists");
    }

    if ($user['email'] === $email) {
      array_push($errors, "email already exists");
    }
  }

  if (count($errors) == 0) {
	  $password = md5($password_1);
	  

  	$query = "INSERT INTO users (username, email, password, user_type) 
  			  VALUES('$username', '$email', '$password', '$user_type')";
  	mysqli_query($db, $query);
  	$_SESSION['username'] = $username;
	$_SESSION['success'] = "You are now logged in";
	  
	if($user_type === 'doctor'){
		header('location: doctors.php');
	}
	elseif($user_type === 'super_User'){
		header('location: super.php');
	}
	else{
		header('location: Client.php');

	}
  }
}

// LOGIN USER
if (isset($_POST['login_user'])) {
	$username = mysqli_real_escape_string($db, $_POST['username']);
	$password = mysqli_real_escape_string($db, $_POST['password']);
	$query_status = "SELECT user_type FROM users WHERE username='$username'";

	$query_result = mysqli_query($db, $query_status);
	$status = mysqli_fetch_assoc($query_result);
	$user_type_here = $status['user_type'];

	if (empty($username)) {
		array_push($errors, "Username is required");
	}
	if (empty($password)) {
		array_push($errors, "Password is required");
	}
  
	if (count($errors) == 0) {
		$password = md5($password);
		$query = "SELECT * FROM users WHERE username='$username' AND password='$password'";
		$results = mysqli_query($db, $query);
		if (mysqli_num_rows($results) == 1) {
		  $_SESSION['username'] = $username;
		  $_SESSION['success'] = "You are now logged in";
		
		if($user_type_here === 'doctor'){
			header('location: doctors.php');
		}
		elseif($user_type_here === 'super_User'){
			header('location: super.php');
		}
		else{
			header('location: Client.php');
	
		}
		}else {
			array_push($errors, "Wrong username/password combination");
		}
	}
  }
  
  ?>