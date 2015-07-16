<?php
session_start();
include_once 'autoload.php';

// Make sure to load the Facebook SDK for PHP via composer or manually

use Facebook\FacebookSession;
// add other classes you plan to use, e.g.:
use Facebook\FacebookRequest;
use Facebook\GraphUser;
use Facebook\FacebookRequestException;
use Facebook\FacebookRedirectLoginHelper;

$APP_ID = '476810305817532';
$APP_SECRET = '34d260e575387b18426633125c010620';
$redirect = 'http://localhost/dhtpfashion/public/plugin/facebook-php-sdk-v4-4.0-dev/';
$scope = 'public_profile, email';

FacebookSession::setDefaultApplication($APP_ID, $APP_SECRET);

// Add `use Facebook\FacebookRedirectLoginHelper;` to top of file
$helper = new FacebookRedirectLoginHelper($redirect);

// Use the login url on a link or button to 
// redirect to Facebook for authentication
try {
  $session = $helper->getSessionFromRedirect();
} catch(FacebookRequestException $ex) {
  // When Facebook returns an error
} catch(Exception $ex) {
  // When validation fails or other local issues
}

if(isset($_GET['logout'])){
	unset($_SESSION['fb_user_profile']);
	header("Location:".$redirect);
}

if (isset($session)) {
	$user_profile = (new FacebookRequest($session, 'GET', '/me'))->execute()->getGraphObject(GraphUser::className());
	$_SESSION['fb_user_profile'] = $user_profile->asArray();
	header("Location:".$redirect);
	//echo '<pre>';
	//print_r($_SESSION['fb_user_profile']);
	//echo '</pre>';
	//echo $user_profile->asArray()['first_name'];
}
else{
	if(isset($_SESSION['fb_user_profile'])){
		echo 'Hello '.$_SESSION['fb_user_profile']['name'];
		echo '<pre>';
		print_r($_SESSION['fb_user_profile']);
		echo '</pre>';
		echo '<br/><a href="?logout">Log out FB</a>';
	}
	else{
		$loginUrl = $helper->getLoginUrl(array('scope'=>$scope));
		echo '<a href="'.$loginUrl.'">Login with FB</a>';
	}
	
}
?>