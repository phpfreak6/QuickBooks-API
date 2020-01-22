<?php
include('header.php');
include('connection.php');
// session_start();

if(!isset($_SESSION['login_user'])){
	header("Location: login.php");
}else{
	header("Location: connect-account.php");
}
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