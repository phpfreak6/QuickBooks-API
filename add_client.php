<?php
include('header.php');
include('connection.php');

if(!isset($_SESSION['login_user']) && !isset($_SESSION['login_users'])){
	header("Location: login.php");
}
require_once(__DIR__ . '/vendor/autoload.php');
use QuickBooksOnline\API\Core\ServiceContext;
use QuickBooksOnline\API\DataService\DataService;
use QuickBooksOnline\API\PlatformService\PlatformService;
use QuickBooksOnline\API\Core\Http\Serialization\XmlObjectSerializer;
use QuickBooksOnline\API\Facades\Customer;

$sqlq    = "SELECT * FROM users WHERE id='".$_SESSION['login_user']."'";
$result = mysqli_query($con,$sqlq);
$row    = mysqli_fetch_array($result,MYSQLI_ASSOC);
/* if($row['access_token'] == ''){
	header("Location: connect-account.php");
} */


if(isset($_GET['code'])){
	$dataService = DataService::Configure(array(
        'auth_mode' => 'oauth2',
        'ClientID' => $config['client_id'],
        'ClientSecret' =>  $config['client_secret'],
        'RedirectURI' => $config['redirect_url'],
        'scope' => $config['scope'],
        'baseUrl' => $config['base_url']
    ));
	
    $OAuth2LoginHelper = $dataService->getOAuth2LoginHelper();
	// echo '<pre>'; print_r($_SESSION).'</br>';print_r($OAuth2LoginHelper); die('thereww');
    $parseUrl = parseAuthRedirectUrl($_SERVER['QUERY_STRING']);

    /*
     * Update the OAuth2Token
     */
    $accessToken = $OAuth2LoginHelper->exchangeAuthorizationCodeForToken($parseUrl['code'], $parseUrl['realmId']);
    $dataService->updateOAuth2Token($accessToken);
	
	  $accessTokenJson = array('token_type' => 'bearer',
        'access_token' => $accessToken->getAccessToken(),
        'refresh_token' => $accessToken->getRefreshToken(),
        'x_refresh_token_expires_in' => $accessToken->getRefreshTokenExpiresAt(),
        'expires_in' => $accessToken->getAccessTokenExpiresAt()
    );
	
    $dataService->updateOAuth2Token($accessToken);
    $oauthLoginHelper = $dataService -> getOAuth2LoginHelper();
    $CompanyInfo = $dataService->getCompanyInfo();
	$access_token = $accessTokenJson['access_token'];
	$refresh_token = $accessTokenJson['refresh_token'];
	$access_expire = strtotime($accessTokenJson['expires_in']);
	$refresh_expire = strtotime($accessTokenJson['x_refresh_token_expires_in']);
	$realmId = $parseUrl['realmId'];
	
	// insert data into database.
	$sqls = "UPDATE users SET access_token='$access_token',acess_token_expiry='$access_expire',refresh_token='$refresh_token',refresh_token_expiry='$refresh_expire',realmId='$realmId' WHERE id='".$_SESSION['login_user']."'";
	mysqli_query($con, $sqls);
	
	// echo '<pre>'; print_r($accessToken); die;
	
	header("Location:".$config['redirect_url']);
}
$message = '';


if(isset($_POST['add_customer'])){
	
	$sqlqq    = "SELECT * FROM users WHERE id='".$_SESSION['login_user']."'";
	$resultss = mysqli_query($con,$sqlqq);
	$rowss    = mysqli_fetch_array($resultss,MYSQLI_ASSOC);

	$acess_token_expiry = $rowss['acess_token_expiry'];
	$refresh_token_expiry = $rowss['refresh_token_expiry'];
	$current_date = strtotime(date("Y-m-d h:i:s"));
	
	// check if access token and refresh token date is expire.
	
	
	if($refresh_token_expiry < $current_date){
		$message = '<div class="alert alert-danger alert-dismissible">
					<strong>Your Refresh Token is expired. Please connect again (Connect account page)</strong>
		</div>'; 
		
	}else{
	
	if($acess_token_expiry < $current_date){
		
		
		$refresh_data = create_reafresh_token($rowss); // call a refresh token function.
		$getRefreshTokenExpiresAt = strtotime($refresh_data->getRefreshTokenExpiresAt());
		$getAccessTokenExpiresAt = strtotime($refresh_data->getAccessTokenExpiresAt());
		$newaccess_token = $refresh_data->getAccessToken();
		$newrefresh_token = $refresh_data->getRefreshToken();
		
		// update access token, refresh token, access token expiry and refresh token expiry date.
		$sqls1 = "UPDATE users SET access_token='$newaccess_token',acess_token_expiry='$getAccessTokenExpiresAt',refresh_token='$newrefresh_token',refresh_token_expiry='$getRefreshTokenExpiresAt' WHERE id='".$_SESSION['login_user']."'";
		mysqli_query($con, $sqls1);
		
		// get keys and tokens form database.
		$sqlqq1    = "SELECT * FROM users WHERE id='".$_SESSION['login_user']."'";
		$resultss1 = mysqli_query($con,$sqlqq1);
		$rowss    = mysqli_fetch_array($resultss1,MYSQLI_ASSOC);

		
	}
	
	$realmIdss = $rowss['realmId'];
	$access_tokens = $rowss['access_token'];
	$refresh_tokens = $rowss['refresh_token'];
	$acess_token_expiry = $rowss['acess_token_expiry'];
	$refresh_token_expiry = $rowss['refresh_token_expiry'];
	
	
	// form post values.
	// echo '<pre>'; print_r($_POST); die;
	$fname 					= $_POST['fname'];
	$lname 					= $_POST['lname'];
	$cemail 				= $_POST['cemail'];
	$phone 				= $_POST['phone'];
	$company_name 			= $_POST['company_name'];
	$billing_address 		= $_POST['billing_address'];
	$billing_city	 		= $_POST['billing_city'];
	$billing_country 		= $_POST['billing_country'];
	$billing_country_code 	= $_POST['billing_country_code'];
	$billing_post_code 		= $_POST['billing_post_code'];
	
	$dataService = DataService::Configure(array(
		   'auth_mode' => 'oauth2',
			 'ClientID' => $config['client_id'],
			 'ClientSecret' =>  $config['client_secret'],
			 'accessTokenKey' =>  $access_tokens,
			 'refreshTokenKey' => $refresh_tokens,
			 'QBORealmID' => $realmIdss,
			 'baseUrl' => $config['base_url']
	));
	
	$dataService->setLogLocation("/Users/hlu2/Desktop/newFolderForLog");


	// Add a customer
	$customerObj = Customer::create([
	"BillAddr" => [
     "Line1"=>  $billing_address,
     "City"=>  	$billing_city,
     "Country"=>  $billing_country,
     "CountrySubDivisionCode"=>  $billing_country_code,
     "PostalCode"=>  $billing_post_code
	],
	 "Title"=>  $fname,
	 "GivenName"=>  $fname,
	 "MiddleName"=>  $lname,
	 "FamilyName"=>  $fname,
	 "Suffix"=>  "Jr",
	 "FullyQualifiedName"=>  $fname.'&nbsp;'.$lname,
	 "CompanyName"=>  $company_name,
	 "DisplayName"=>  $fname,
	 "PrimaryPhone"=>  [
     "FreeFormNumber"=>  $phone
	 ],
	 "PrimaryEmailAddr"=>  [
		 "Address" => $cemail
	 ]
	]);
	$resultingCustomerObj = $dataService->Add($customerObj);

	$error = $dataService->getLastError();
	if ($error) {
		echo "The Status code is: " . $error->getHttpStatusCode() . "\n";
		echo "The Helper message is: " . $error->getOAuthHelperError() . "\n";
		echo "The Response message is: " . $error->getResponseBody() . "\n";
	} else {
		$message = '<div class="alert alert-success alert-dismissible">
					<strong>Client Add Successfully.</strong>
		</div>';
		$clientss_id = $resultingCustomerObj->Id;
		
		$sql = "INSERT INTO client (user_id,client_id,first_name,last_name,email,phone,company_name,billing_address,billing_city,billing_country,billing_country_code,billing_post_code)VALUES ('".$_SESSION['login_user']."','$clientss_id','$fname','$lname','$cemail','$phone','$company_name','$billing_address','$billing_city','$billing_country','$billing_country_code','$billing_post_code')";
		mysqli_query($con, $sql);
	}

}
}

function parseAuthRedirectUrl($url)
{
    parse_str($url,$qsArray);
    return array(
        'code' => $qsArray['code'],
        'realmId' => $qsArray['realmId']
    );
}

// function for refresh token.
function create_reafresh_token($rowss)
{

    $dataService = DataService::Configure(array(
        'auth_mode' => 'oauth2',
        'ClientID' => $config['client_id'],
		'ClientSecret' =>  $config['client_secret'],
        'RedirectURI' => $config['redirect_url'],
        'baseUrl' => $config['base_url'],
        'refreshTokenKey' => $rowss['refresh_token'],
        'QBORealmID' => $rowss['realmId'],
    ));

    /*
     * Update the OAuth2Token of the dataService object
     */
    $OAuth2LoginHelper = $dataService->getOAuth2LoginHelper();
    $refreshedAccessTokenObj = $OAuth2LoginHelper->refreshToken();
    $dataService->updateOAuth2Token($refreshedAccessTokenObj);
	
	return $refreshedAccessTokenObj;
}
?>


<div class="container">

<div class="wktops">
<?php
echo $message;

?>
<form action="" method="post">
      <div class="col-xs-4">
        <label for="ex3">First Name</label>
        <input class="form-control" type="text" name="fname" placeholder="First Name">
      
        <label for="ex3">Last Name</label>
        <input class="form-control"  type="text" name="lname" placeholder="Last Name">
      
		<label for="ex3">Email</label>
        <input class="form-control" type="email" name="cemail" placeholder="Email">
		
        <label for="ex3">Company Name</label>
        <input class="form-control" type="text" name="company_name" placeholder="Company Name">
		
		<label for="ex3">Phone number</label>
        <input class="form-control" type="number" name="phone" placeholder="Phone Number">
		
		<label for="ex3">Billing Address</label>
        <input class="form-control" type="text" name="billing_address" placeholder="Billing Address">
		
		<label for="ex3">Billing City</label>
        <input class="form-control" type="text" name="billing_city" placeholder="Billing City">
		
		<label for="ex3">Billing Country</label>
        <input class="form-control" type="text" name="billing_country" placeholder="Billing Country">
		
		<label for="ex3">Billing Country Code</label>
        <input class="form-control" type="text" name="billing_country_code" placeholder="Ex. CA">
		
		<label for="ex3">Billing Post Code</label>
        <input class="form-control" type="text" name="billing_post_code" placeholder="Billing Post Code">
		
		<div style="margin-top:10px;">
		<input type="submit" class="btn btn-primary" name="add_customer" value="Add Client">
		</div>
      </div>
	  </form>
	 
</div>
</div>
</body>
</html>