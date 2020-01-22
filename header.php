<!DOCTYPE html>
<html lang="en">
<head>
<?php
session_start();
$filname = basename($_SERVER['REQUEST_URI'], '?' . $_SERVER['QUERY_STRING']);
if($filname == 'connect-account.php'){
	$title = 'Connect Account';
}else if($filname == 'edit-profile.php'){
	$title = 'Change Password';
}else if($filname == 'add_client.php'){
	$title = 'Add Client';
}else if($filname == 'add_invoice.php'){
	$title = 'Add Invoice';
}
include('connection.php');
$sql    = "SELECT * FROM users WHERE id='".$_SESSION['login_user']."'";
$result = mysqli_query($con,$sql);
$row    = mysqli_fetch_array($result,MYSQLI_ASSOC);
?>
<title><?php echo $title; ?></title>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.3/css/bootstrap-select.min.css" />
<link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>
<link rel="stylesheet" href="style.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.3/js/bootstrap-select.min.js"></script>
<script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
</head>
<body>
 <nav class="navbar navbar-default navbar-static-top">
        <div class="container">


            <div class="navbar-header">

                <!-- Collapsed Hamburger -->
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#app-navbar-collapse">
                        <span class="sr-only">Toggle Navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>

                </a>
            </div>

            <div class="collapse navbar-collapse" id="app-navbar-collapse">
                <!-- Left Side Of Navbar -->
                <ul class="nav navbar-nav">
                    <li><a href="connect-account.php">Connect Account</a></li>
                    <li><a href="edit-profile.php">Change Password</a></li>
                    <?php
					if($row['access_token'] != ''){
					?>
                    <li><a href="add_client.php">Add Client</a></li>
                    <li><a href="add_invoice.php">Create Invoice</a></li>
					<li><a href="logout.php">Logout</a></li>
					<?php
					}
					?>
					
					
					
                </ul>

            </div>
        </div>
    </nav>
