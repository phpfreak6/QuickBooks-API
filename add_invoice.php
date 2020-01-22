<?php
include('header.php');
include('connection.php');

if(!isset($_SESSION['login_user'])){
	header("Location: login.php");
}
require_once(__DIR__ . '/vendor/autoload.php');
use QuickBooksOnline\API\Core\ServiceContext;
use QuickBooksOnline\API\DataService\DataService;
use QuickBooksOnline\API\PlatformService\PlatformService;
use QuickBooksOnline\API\Core\Http\Serialization\XmlObjectSerializer;
use QuickBooksOnline\API\Facades\Invoice;
use QuickBooksOnline\API\QueryFilter\QueryMessage;
use QuickBooksOnline\API\Facades\Customer;

$sqlq    = "SELECT * FROM users WHERE id='".$_SESSION['login_user']."'";
$result = mysqli_query($con,$sqlq);
$row    = mysqli_fetch_array($result,MYSQLI_ASSOC);
if($row['access_token'] == ''){
	header("Location: connect-account.php");
}



$sql = "SELECT * FROM client WHERE user_id='".$_SESSION['login_user']."'";
$result = mysqli_query($con,$sql);
while($row = mysqli_fetch_array($result,MYSQLI_ASSOC)) $rows[] = $row;


$message = '';
$sqlqq    = "SELECT * FROM users WHERE id='".$_SESSION['login_user']."'";
$resultss = mysqli_query($con,$sqlqq);
$rowss    = mysqli_fetch_array($resultss,MYSQLI_ASSOC);

$acess_token_expiry = $rowss['acess_token_expiry'];
$refresh_token_expiry = $rowss['refresh_token_expiry'];
$current_date = strtotime(date("Y-m-d h:i:s"));

$realmIdss = $rowss['realmId'];
$access_tokens = $rowss['access_token'];
$refresh_tokens = $rowss['refresh_token'];



if($refresh_token_expiry < $current_date){
		$message = '<div class="alert alert-danger alert-dismissible">
					<strong>Your Refresh Token is expired. Please connect again (Connect account page)</strong>
		</div>'; 
		
	}else{

	if($acess_token_expiry < $current_date){
		
		$refresh_data = create_reafresh_token($rowss,$config); // call a refresh token function.
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
	$dataService = DataService::Configure(array(
       'auth_mode' => 'oauth2',
         'ClientID' => $config['client_id'],
		 'ClientSecret' =>  $config['client_secret'],
		 'RedirectURI' => $config['redirect_url'],
         'accessTokenKey' => $access_tokens,
         'refreshTokenKey' => $refresh_tokens,
         'QBORealmID' => $realmIdss,
         'baseUrl' => $config['base_url']
	));

	
	
}


if(isset($_POST['add_invoice'])){


	$amount 		= $_POST['amount'];
	$item_val 		= $_POST['item_val'];
	$bill_email 	= $_POST['bill_email'];
	$description 	= $_POST['description'];
	$sel_option 	= $_POST['sel_option'];
	$sel_items 	= $_POST['sel_items'];
	
	$realmIdss = $rowss['realmId'];
	$access_tokens = $rowss['access_token'];
	$refresh_tokens = $rowss['refresh_token'];
	$acess_token_expiry = $rowss['acess_token_expiry'];
	$refresh_token_expiry = $rowss['refresh_token_expiry'];
	
	$dataService = DataService::Configure(array(
       'auth_mode' => 'oauth2',
         'ClientID' => $config['client_id'],
		 'ClientSecret' =>  $config['client_secret'],
         'accessTokenKey' => $access_tokens,
         'refreshTokenKey' => $refresh_tokens,
         'QBORealmID' => $realmIdss,
         'baseUrl' => $config['base_url']
	));




	
$dataService->setLogLocation("/Users/hlu2/Desktop/newFolderForLog");

$total_amount = $amount*$item_val;

// $dataService->throwExceptionOnError(true);
//Add a new Invoice
$theResourceObj = Invoice::create([
     "Line" => [
   [
	 "Description" => $description,
     "Amount" => $total_amount,
     "DetailType" => "SalesItemLineDetail",
     "SalesItemLineDetail" => [
       "ItemRef" => [
         "value" => $sel_items
        ],
		"Qty"=> $item_val,
		"UnitPrice"=> $amount,
      ]
      ]
    ],
"CustomerRef"=> [
  "value"=> $sel_option
],
      "BillEmail" => [
            "Address" => $bill_email
      ]
]);

// echo '<pre>'; print_r($theResourceObj); die('thereww');
$resultingObj = $dataService->Add($theResourceObj);


$error = $dataService->getLastError();
if ($error) {
    echo "The Status code is: " . $error->getHttpStatusCode() . "\n";
    echo "The Helper message is: " . $error->getOAuthHelperError() . "\n";
    echo "The Response message is: " . $error->getResponseBody() . "\n";
}
else {
    $message = '<div class="alert alert-success alert-dismissible">
				<strong>Invoice Add Successfully.</strong>
			</div>';
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
function create_reafresh_token($rowss,$config)
{
	// echo '<pre>'; print_r($config); die('ok');
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


$oneQuery = new QueryMessage();
$oneQuery->sql = "SELECT";
$oneQuery->entity = "Item";
$queryString = $oneQuery->getString();
$entities = $dataService->Query($queryString);


$cstmrs = $dataService->Query("Select * from Customer");

?>


<div class="container">

<div class="wktops">
<?php
echo $message;
?>
<form action="" method="post">
      <div class="col-xs-4">
        <label for="ex3">Select Client</label>
        <select class="form-control selectpicker" name="sel_option" data-live-search="true">
		<?php
		foreach($cstmrs as $_cstmrs){
		?>
        <option value="<?php echo $_cstmrs->Id; ?>"><?php echo $_cstmrs->GivenName;?></option>
		<?php
		}
		?>
		</select>
	
	
		<label for="ex3">Select Items</label>
        <select class="form-control selectpicker" name="sel_items" data-live-search="true">
		<?php
		
		
		foreach($entities as $_entities){
		?>
        <option value="<?php echo $_entities->Id; ?>"><?php echo $_entities->Name;?></option>
		<?php
		}
		?>
		</select>
		
      
        <label for="ex3">Unit Price</label>
        <input class="form-control"  type="number" name="amount" placeholder="Unit Price">
      
		<label for="ex3">Quantity</label>
        <input class="form-control" type="number" name="item_val" placeholder="Quantity">
		
        <label for="ex3">Description</label>
        <textarea class="form-control" name="description" placeholder="Billing Description"></textarea>
		
		<label for="ex3">Billing Email</label>
        <input class="form-control" type="email" name="bill_email" placeholder="Billing email">
		
		<div style="margin-top:10px;">
		<input type="submit" class="btn btn-primary" name="add_invoice" value="Add Invoice">
		</div>
      </div>
	  </form>
</div>
</div>
</body>
</html>