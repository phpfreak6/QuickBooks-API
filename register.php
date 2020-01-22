<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="https://fonts.googleapis.com/css?family=Roboto:400,700" rel="stylesheet">

<title>Register Form</title>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script> 
<link rel="stylesheet" href="style.css">
</head>
<body>
<?php
include('connection.php');
session_start();
// $configs = include('Oauth2/config.php');
// include("Oauth2/Client.php");
//Client ID:-Q0I9oKQFhJna8OkF4VTz5OwzLUE0jU9vaU8QDmJnar58oLIrYt
//Secret ID:-D95gKblMKX2KnMnfNKWDf21NAkWy22mZQr18PYmr

// $redirect_uri = $configs['oauth_redirect_uri'];
// $openID_redirect_uri = $configs['openID_redirect_uri'];
// $refreshTokenPage = $configs['refreshTokenPage'];
$message = '';
if(isset($_POST['sign_up'])){
	
	// $username 		= $_POST['username'];
	$email 			= $_POST['email'];
	$password 		= md5($_POST['password']);
	
		$check = 'SELECT * FROM users where email = "'.$email.'"';
        $result = mysqli_query($con, $check);

         if (mysqli_num_rows($result) ==0) {
		
				$sql = "INSERT INTO users (email,password)VALUES ('$email','$password')";
				
				if (mysqli_query($con, $sql)) {
					$insert_id = mysqli_insert_id($con);
					$_SESSION['login_user'] = $insert_id;
					$message = '<div class="alert alert-success alert-dismissible">
							<strong>Register Successfully.</strong>
						</div>';
						header("location: connect-account.php");
				} else {
					$message = '<div class="alert alert-danger alert-dismissible">
							<strong>Error!</strong> "'.mysqli_error($con).'"
						</div>';
				}
		 }else{
			 $message = '<div class="alert alert-danger alert-dismissible">
							<strong>Error!</strong>Email already exist!
						</div>';
			 
		 }
	
?>


<?php

}

?>

<div class="signup-form">
<?php
echo $message;
?>
    <form action="" method="post">
		<h2>Create Account</h2>
        <!--div class="form-group">
			<div class="input-group">
				<span class="input-group-addon"><i class="fa fa-user"></i></span>
				<input type="text" class="form-control" name="username" placeholder="Username" required="required">
			</div>
        </div-->
        <div class="form-group">
			<div class="input-group">
				<span class="input-group-addon"><i class="fa fa-paper-plane"></i></span>
				<input type="email" class="form-control" name="email" placeholder="Email Address" required="required">
			</div>
        </div>
		<div class="form-group">
			<div class="input-group">
				<span class="input-group-addon"><i class="fa fa-lock"></i></span>
				<input type="password" class="form-control" name="password" placeholder="Password" required="required">
			</div>
        </div>
		
		
		<div class="form-group">
            <input type="submit" value="Sign Up" name="sign_up" class="btn btn-primary btn-block btn-lg">
        </div>
    </form>
	<div class="text-center">Already have an account? <a href="login.php">Login here</a>.</div>
	<div class="text-center">Read documentation <a target="_blank" href="quick-book.pdf">Click here</a>.</div>
</div>
</body>
</html>                            