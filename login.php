<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="https://fonts.googleapis.com/css?family=Roboto:400,700" rel="stylesheet">

<title>Login Form</title>
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
$message = '';

if (isset($_POST["log_in"])) {
    
    $myusername = $_POST['email'];
    $mypassword = md5($_POST['password']);

    $sql    = "SELECT id FROM users WHERE email='$myusername' and password='$mypassword'";
    $result = mysqli_query($con,$sql);
    $row    = mysqli_fetch_array($result,MYSQLI_ASSOC);
    $count  = mysqli_num_rows($result);

    if ($count == 1) {
        
        $_SESSION['login_user'] = $row['id'];
		header("location: connect-account.php");
		
    } else {
		
        $message = '<div class="alert alert-danger alert-dismissible">
					<strong>Error!</strong> Invalid Email or Password.
				</div>';
    }
}
?>
<div class="signup-form wklog_in">
<?php
echo $message;
?>
    <form action="" method="post">
		<h2>Login Your Account</h2>
		
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
            <input type="submit" value="Log In" name="log_in" class="btn btn-primary btn-block btn-lg">
        </div>
		
    </form>
	<div class="text-center">Don't have an account? <a href="register.php">Sign up here</a>.</div>
</div>
</body>
</html>                            