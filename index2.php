<?php
session_start(); //Session should always be active

$app_id				= '476810305817532';  //localhost
$app_secret 		= '34d260e575387b18426633125c010620';
$required_scope 	= 'public_profile, publish_actions'; //Permissions required
$redirect_url 		= 'http://localhost/dhtpfashion/public/plugin/facebook-php-sdk-v4-4.0-dev/'; //FB redirects to this page with a code

require_once "autoload.php"; //include autoload from SDK folder

//import required class to the current scope
use Facebook\FacebookSession;
use Facebook\FacebookRequest;
use Facebook\GraphUser;
use Facebook\FacebookRedirectLoginHelper;

FacebookSession::setDefaultApplication($app_id , $app_secret);
$helper = new FacebookRedirectLoginHelper($redirect_url);

try {
  $session = $helper->getSessionFromRedirect();
} catch(FacebookRequestException $ex) {
	die(" Error : " . $ex->getMessage());
} catch(\Exception $ex) {
	die(" Error : " . $ex->getMessage());
}


//if user wants to log out
if(isset($_GET["log-out"]) && $_GET["log-out"]==1){
	unset($_SESSION["fb_user_details"]);
	//session ver is set, redirect user 
	header("location: ". $redirect_url);
}

//Test normal login / logout with session

if ($session){ //if we have the FB session
	//echo 'co session';die;
	//get user data
	$user_profile = (new FacebookRequest($session, 'GET', '/me'))->execute()->getGraphObject(GraphUser::className());
	
	//save session var as array
	$_SESSION["fb_user_details"] = $user_profile->asArray(); 
	
	//session ver is set, redirect user 
	header("location: ". $redirect_url);
	
}else{ 
	
	//session var is still there
	if(isset($_SESSION["fb_user_details"]))
	{
		print 'Hi '.$_SESSION["fb_user_details"]["name"].' you are logged in! [ <a href="?log-out=1">log-out</a> ] ';
		print '<pre>';
		print_r($_SESSION["fb_user_details"]);
		print '</pre>';
	}
	else
	{
		//display login url 
		$login_url = $helper->getLoginUrl( array( 'scope' => $required_scope ) );
		echo '<a href="'.$login_url.'">Login with Facebook</a>'; 
	}
}


/* Demo review
if ($session){ //if we have the FB session
	
	######## Fetch user Info ###########
	$user_profile = (new FacebookRequest($session, 'GET', '/me'))->execute()->getGraphObject(GraphUser::className());
    $user_id =  $user_profile->getId(); 
    $user_name = $user_profile->getName(); 
	$user_email =  $user_profile->getEmail();
	$location =  $user_profile->getLocation();


    ######## Check User Permission called 'publish_actions' ##########
    $user_permissions = (new FacebookRequest($session, 'GET', '/me/permissions'))->execute()->getGraphObject(GraphUser::className())->asArray();
    $found_permission = false;
    foreach($user_permissions as $key => $val){         
        if($val->permission == 'publish_actions'){
            $found_permission = true;
        }
    }
    
	###### post image if 'publish_actions' permission available ########
    if($found_permission){
		$image = "/home/images/image_name.jpg"; //server path to image
		$post_data = array('source' => '@'.$image, 'message' => 'This is test Message');
		$response = (new FacebookRequest( $session, 'POST', '/me/photos', $post_data ))->execute()->getGraphObject();
    }


	###### Save info in database ########
	$mysqli = new mysqli($mysql_host, $mysql_username, $mysql_password, $mysql_db_name);
	if ($mysqli->connect_error) {
		die('Error : ('. $mysqli->connect_errno .') '. $mysqli->connect_error);
	}
	$results = $mysqli->query("SELECT COUNT(*) FROM usertable WHERE id=".$user_id);
	$get_total_rows = $results->fetch_row();
	
	if(!$get_total_rows[0]){
		$insert_row = $mysqli->query("INSERT INTO usertable (fbid, fullname, email) VALUES(".$user_id.", '".$user_name."', '".$user_email."')");
		if($insert_row){
			print 'Success! ID of last inserted record is : ' .$mysqli->insert_id .'<br />'; 
		}
	}
}else{ 

	//display login url 
	$login_url = $helper->getLoginUrl( array( 'scope' => $required_scope ) );
	echo '<a href="'.$login_url.'">Login with Facebook</a>'; 
}
*/