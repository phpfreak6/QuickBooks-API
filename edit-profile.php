<?php

include('header.php');
if(!isset($_SESSION['login_user']) && !isset($_SESSION['login_users'])){
	header("Location: login.php");
}

include('connection.php');
$sql    = "SELECT * FROM users WHERE id='".$_SESSION['login_user']."'";
$result = mysqli_query($con,$sql);
$row    = mysqli_fetch_array($result,MYSQLI_ASSOC);

$message = '';
if(isset($_POST['update'])){
	

	if($_POST['pswd'] != ''){
		$pswd 		= md5($_POST['pswd']);
	}else{
		$pswd 		= $row['password'];
	}
	
	$sqls = "UPDATE users SET password='$pswd' WHERE id='".$_SESSION['login_user']."'";
	
	if (mysqli_query($con, $sqls)) {
		$message = '<div class="alert alert-success alert-dismissible">
					<strong>Password Updated Successfully.</strong>
					</div>';
	}else{
		$message = '<div class="alert alert-danger alert-dismissible">
					<strong>Error!</strong> "'.mysqli_error($con).'"
					</div>';
	}
	
}

?>

<div class="container">

<div class="wktops">
<?php
echo $message;
?>
<form action="" method="post">
      <div class="col-xs-4" style="margin-top:10px;">
      
        <label for="ex3">Change Password</label>
        <input class="form-control"  type="password" value="" name="pswd" placeholder="Change password" required>
		
		<div style="margin-top:10px;">
		<input type="submit" class="btn btn-primary" name="update" value="Update">
		</div>
      </div>
</form>
</div>
</div>
</body>
</html>