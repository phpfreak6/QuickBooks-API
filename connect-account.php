<?php
//session_start();
include('header.php');
if(!isset($_SESSION['login_user']) && !isset($_SESSION['login_users'])){
	header("Location: login.php");
}
require_once(__DIR__ . '/vendor/autoload.php');
//use QuickBooksOnline\API\Core\ServiceContext;
use QuickBooksOnline\API\DataService\DataService;
// use QuickBooksOnline\API\PlatformService\PlatformService;
// use QuickBooksOnline\API\Core\Http\Serialization\XmlObjectSerializer;
use QuickBooksOnline\API\Facades\Customer;


include('connection.php');

// if(isset($_POST['connect'])){
	
	// $client_id = $_POST['client_id'];
	// $client_sec = $_POST['client_sec'];
	// $sqls = "UPDATE users SET client_id='$client_id',client_secret='$client_sec' WHERE id='".$_SESSION['login_user']."'";
	// mysqli_query($con, $sqls);
	
	
	$dataService = DataService::Configure(array(
    'auth_mode' => 'oauth2',
    'ClientID' => $config['client_id'],
    'ClientSecret' =>  $config['client_secret'],
    'RedirectURI' => $config['redirect_url'],
    'scope' => $config['scope'],
    'baseUrl' => $config['base_url']
));


$OAuth2LoginHelper = $dataService->getOAuth2LoginHelper();
$authUrl = $OAuth2LoginHelper->getAuthorizationCodeURL();


// Store the url in PHP Session Object;
$_SESSION['authUrl'] = $authUrl;
//set the access token using the auth object
if (isset($_SESSION['sessionAccessToken'])) {
	$accessToken = $_SESSION['sessionAccessToken'];
    $accessToken = $_SESSION['sessionAccessToken'];
    $accessTokenJson = array('token_type' => 'bearer',
        'access_token' => $accessToken->getAccessToken(),
        'refresh_token' => $accessToken->getRefreshToken(),
        'x_refresh_token_expires_in' => $accessToken->getRefreshTokenExpiresAt(),
        'expires_in' => $accessToken->getAccessTokenExpiresAt()
    );
    $dataService->updateOAuth2Token($accessToken);
    $oauthLoginHelper = $dataService -> getOAuth2LoginHelper();
    $CompanyInfo = $dataService->getCompanyInfo();
	// echo json_encode($accessTokenJson, JSON_PRETTY_PRINT); die;
}
// header("Location:".$_SESSION['authUrl']);


// }
$sqlqq    = "SELECT * FROM users WHERE id='".$_SESSION['login_user']."'";
$resultss = mysqli_query($con,$sqlqq);
$rowss    = mysqli_fetch_array($resultss,MYSQLI_ASSOC);

?>

<div class="container">

<div class="wktops">
<?php
// echo $message;

?>

	<form action="<?php echo $authUrl; ?>" method="post" id="key_formss">
      <div class="col-xs-4" style="margin-top:10px;">
       <h4 style="color:#000;">Connect Your Quickbook Account</h4>
		<div style="margin-top:10px;">
		<input type="submit" id="key_form" class="btn btn-primary" name="connect" value="Connect">
		</div>
      </div>
	  </form>
    </div>
    </div>
  
<?php
/* if (isset($_SESSION['sessionAccessToken'])) {
	
	echo "<pre>"; print_r($_SESSION); die;
	 $accessToken = unserialize($_SESSION['sessionAccessToken']);
	//echo  $accessToken->getAccessToken(); die;
    $accessTokenJson = array('token_type' => 'bearer',
        'access_token' => $accessToken->getAccessToken(),
        'refresh_token' => $accessToken->getRefreshToken(),
        'x_refresh_token_expires_in' => $accessToken->getRefreshTokenExpiresAt(),
        'expires_in' => $accessToken->getAccessTokenExpiresAt()
    );
	
    $dataService->updateOAuth2Token($accessToken);
    $oauthLoginHelper = $dataService -> getOAuth2LoginHelper();
    $CompanyInfo = $dataService->getCompanyInfo();
	echo json_encode($accessTokenJson, JSON_PRETTY_PRINT);  
} */
?>
</body>

</html>
