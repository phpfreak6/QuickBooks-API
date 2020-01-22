<?php
// $con = mysqli_connect("localhost","db user","db password","db name");
$con = mysqli_connect("localhost","quickbook_user","Aa147147147#","quick_book_db"); // set database details.

// Check connection
if (mysqli_connect_errno()){
	echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

// Set Quick book credentials.
$config =  array(
    'redirect_url' => 'http://iamwhiz.com/quick-book/add_client.php', 	// redirect url.
    'client_id' => 'L0PidXQRN6rWUOcj9DLtcdhM13pTfdsOkC2yRXzrPyCaYQQyuL',// Quick book account client ID.
    'client_secret' => 'nSo6n7cPawetT9tIxjhat9YwS7i3RUUgk5t49Vmd',		// Quick book account client secret.
    'scope' => 'com.intuit.quickbooks.accounting',
    'base_url' => "development" //If you want to set it live then use change "development" to "Production".
);
?>