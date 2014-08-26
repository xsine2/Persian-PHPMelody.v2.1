<?php
// +------------------------------------------------------------------------+
// | PHP Melody ( www.96down.com )
// +------------------------------------------------------------------------+
// | PHP Melody IS NOT FREE SOFTWARE
// | If you have downloaded this software from a website other
// | than www.96down.com or if you have received
// | this software from someone who is not a representative of
// | PHPSUGAR, you are involved in an illegal activity.
// | ---
// | In such case, please contact: support@96down.com.
// +------------------------------------------------------------------------+
// | Developed by: PHPSUGAR (www.96down.com) / support@96down.com
// | Copyright: (c) 2004-2013 PHPSUGAR. All rights reserved.
// +------------------------------------------------------------------------+
/*
	this functions checks if there are any existing cookies set at last login 
	returns 0 if no cookies were found and 1 otherwise
*/
function lookup_cookies(){
	
		if( empty($_COOKIE[COOKIE_NAME]) || empty($_COOKIE[COOKIE_KEY]) )
			return 0;
	return 1;
}

/*
	this functions checks if there are any existing session variables set at last login
	returns 0 if no sessions were found and 1 otherwise
*/
function lookup_sessions(){

		if( empty($_SESSION[COOKIE_NAME]) || empty($_SESSION[COOKIE_KEY]))
			return 0;
	return 1;
}

/*
	this function checks if the current user is logged in.
*/
function is_user_logged_in(){

	if( lookup_cookies() ){
		
		if( !check_user_login_info($_COOKIE[COOKIE_NAME], $_COOKIE[COOKIE_KEY]) )
		{	
			logout();
			return 0;
		}
		else {
			if( !lookup_sessions()){

				$_SESSION[COOKIE_NAME] = $_COOKIE[COOKIE_NAME];
				$_SESSION[COOKIE_KEY] = $_COOKIE[COOKIE_KEY];
			}
			elseif( strcmp($_COOKIE[COOKIE_NAME], $_SESSION[COOKIE_NAME])  || strcmp($_COOKIE[COOKIE_KEY], $_SESSION[COOKIE_KEY]) ) 
				return 0;
		  return 1;
		}
	}		
	if( lookup_sessions() ){
		if( !check_user_login_info($_SESSION[COOKIE_NAME], $_SESSION[COOKIE_KEY]) )
			return 0;
	return 1;
	}
return 0;
}

/*
	this function verifies if the email and double-hashed password(key) exist in DB and match
*/
function check_user_login_info($username, $user_key){

	global $conn_id;
	
	if( strlen($user_key) != 32 )
		return 0; 
	if( empty($conn_id) ) {
		$conn_id = db_connect();
	}
	$username = str_replace(" ", "", $username);
	$username = stripslashes($username);
	
	$sql = "SELECT username, password FROM pm_users WHERE username= '". secure_sql($username) ."'";
	$result = @mysql_query($sql);
	if( !$result ) 
		return 0;
	$rows = @mysql_num_rows($result) ;
	if( $rows == 0 )
		return 0;
	$row = @mysql_fetch_assoc($result);
	@mysql_free_result($result);
	
	// check if passwords match
	if( strcmp($user_key, md5($row['password'])) )
		return 0;
return 1;
}

/*
	this function verifies if the email and single-hashed password exist in DB and match; similar to check_user_login_info();
*/
function confirm_login( $username, $pass, $hashed = false ){

	global $conn_id, $config;

	if( empty($conn_id) ) {
		$conn_id = db_connect();
	}
	$sql = "SELECT id, username, password, power FROM pm_users WHERE username= '". secure_sql($username) ."'";
	$result = @mysql_query($sql);
	if( !$result ) 
		return 0;
	$rows = @mysql_num_rows($result) ;
	if( $rows == 0 )
		return 0;
	$row = @mysql_fetch_assoc($result);
	@mysql_free_result($result);
	$password = ($hashed) ? $pass : md5($pass);	
	
	// check if passwords match
	if( strcmp($password, $row['password']) ) 
 		return 0;
	
return 1;
}

function is_user_account_active($user_id, $username)
{
	if($user_id != '')
	{
		$sql = "SELECT power FROM pm_users WHERE id = '".$user_id."'";
	}
	elseif($username != '')
	{
		$sql = "SELECT power FROM pm_users WHERE username = '".secure_sql($username)."'";
	}
	$result = @mysql_query($sql);
	$row = @mysql_fetch_assoc($result);
	@mysql_free_result($result);
	
	if( $row['power'] == U_INACTIVE )
		return 0;
	return 1;
}
/*
	this function sets the $_SESSION variables and $_COOKIES, if required
*/
function log_user_in($username, $pass, $remember = false, $hashed = false){
	global $conn_id;


	if( empty($conn_id) ) {
		$conn_id = db_connect();
	}

	if(!confirm_login($username, $pass, false))
		return 0;
	$key = ($hashed) ? md5($pass) : md5(md5($pass));
	
	session_regenerate_id(true);
	$_SESSION[COOKIE_NAME] = $username;
	$_SESSION[COOKIE_KEY] = $key;	
	if($remember == 1){
		
		if (COOKIE_HTTPONLY)
		{
			setcookie(COOKIE_NAME, $username, time()+COOKIE_TIME, COOKIE_PATH, COOKIE_DOMAIN, COOKIE_SECURE, COOKIE_HTTPONLY);
			setcookie(COOKIE_KEY, $key, time()+COOKIE_TIME, COOKIE_PATH, COOKIE_DOMAIN, COOKIE_SECURE, COOKIE_HTTPONLY);
		}
		else
		{
			setcookie(COOKIE_NAME, $username, time()+COOKIE_TIME, COOKIE_PATH, COOKIE_DOMAIN, COOKIE_SECURE);
			setcookie(COOKIE_KEY, $key, time()+COOKIE_TIME, COOKIE_PATH, COOKIE_DOMAIN, COOKIE_SECURE);
		}
	}

return 1;
}

/*
	this function logs the current user out
*/
function logout(){

	if (COOKIE_HTTPONLY)
	{
		setcookie(COOKIE_NAME, ' ', time()-COOKIE_TIME, COOKIE_PATH, COOKIE_DOMAIN, COOKIE_SECURE, COOKIE_HTTPONLY);
		setcookie(COOKIE_KEY, ' ',  time()-COOKIE_TIME, COOKIE_PATH, COOKIE_DOMAIN, COOKIE_SECURE, COOKIE_HTTPONLY);
	}
	else
	{
		setcookie(COOKIE_NAME, ' ', time()-COOKIE_TIME, COOKIE_PATH, COOKIE_DOMAIN, COOKIE_SECURE);
		setcookie(COOKIE_KEY, ' ',  time()-COOKIE_TIME, COOKIE_PATH, COOKIE_DOMAIN, COOKIE_SECURE);
	}
	
	$keep['previous_page'] = $_SESSION['previous_page'];

	$_SESSION = array();
	@session_destroy();
	
	foreach ($keep as $k => $v)
	{
		$_SESSION[$k] = $v;
	}
	
	return 1;
}


function get_last_referer() 
{
	$page = ($_SESSION['previous_page'] != '') ? $_SESSION['previous_page'] : 'index.'. _FEXT;
	// do some cleanup
	$page = str_replace(_URL, '', $page);
	$page = preg_replace('|https?://[^/]+|i', '', $page);
	
	return '/'. $page;
	
	// backup
	/*if ( ! empty($_SERVER['HTTP_REFERER']))
	{		
		$referer = strip_tags($_SERVER['HTTP_REFERER']);
		$referer = str_replace( array("<",">", "'", '"'), "", $referer);
		$referer = str_replace(_URL, '', $referer);		
		$referer = preg_replace('|https?://[^/]+|i', '', $referer );
		
		return $referer;
	}*/
}
/*
	this function checks if the current user is logged in.
*/
function reset_password($email_address = ''){
	global $conn_id;
	if( empty($conn_id) ) {
		$conn_id = db_connect();
	}
	if(empty($email_address) || $email_address == '')
		return 0;
	$new_pass = generate_unique_id();
	$activation_key = generate_activation_key();
	
	$new_md5 = md5($new_pass);
	$sql = "UPDATE pm_users SET new_password = '".$new_md5."', activation_key = '".$activation_key."' WHERE email= '".$email_address."'";
	$result = @mysql_query($sql, $conn_id);
	if( !$result ) 
		return 0;
		
	return array("pass" => $new_pass, "key" => $activation_key);
}
/*
	this function returns an array with the user's information FROM pm_users Table
*/
function fetch_user_info($username){

	global $conn_id;

	$username = str_replace(" ", "", $username);
	$username = stripslashes($username);
	
	if( empty($conn_id) ) {
		$conn_id = db_connect();
	}
		
	$user = array();
	$sql = "SELECT * FROM pm_users WHERE username= '". secure_sql($username) ."'";
	$result = @mysql_query($sql, $conn_id);
	if( !$result ) 
		return false;
	$count = @mysql_num_rows($result);
	if( !$count )
		return false;
	
	$row = @mysql_fetch_assoc($result);
	@mysql_free_result($result);
	foreach($row as $k => $v){
		$user[$k] = stripslashes($v);
	}
	
	$user['id'] = (int) $user['id'];
	
	$links = array('website', 'facebook', 'twitter', 'lastfm');
	foreach($links as $k => $field)
	{
		if ('' != $user[$field] && strpos($user[$field], 'http') === false)
		{
			$user[$field] = 'http://'. $user[$field];
		}	
	}
	
	$user['country'] = (int) $user['country'];
	$user['reg_date'] = (int) $user['reg_date'];
	$user['last_signin'] = (int) $user['last_signin'];
	
	$user['followers_count'] = (int) $user['followers_count'];
	$user['following_count'] = (int) $user['following_count'];
	$user['unread_notifications_count'] = (int) $user['unread_notifications_count'];
	
	$user['avatar_url'] = get_avatar_url($user['avatar'], $user['id']);
	$user['followers_compact'] = pm_compact_number_format($user['followers_count']);
	$user['following_compact'] = pm_compact_number_format($user['following_count']);
	$user['statuses_compact'] = pm_compact_number_format($user['statuses_count']);
	
	return $user;
}


function fetch_user_advanced($unique_id = '') {

	global $conn_id;
	if( empty($conn_id) ) {
		$conn_id = db_connect();
	}
	
	$user = array();
	if(empty($unique_id))
		return false;

	$sql = "SELECT * FROM pm_users WHERE  id= '".$unique_id."'";
	$result = @mysql_query($sql, $conn_id);
	if( !$result )
		return false;
	$count = @mysql_num_rows($result);
	if( !$count )
		return false;
	
	$row = @mysql_fetch_assoc($result);
	@mysql_free_result($result);
	
	foreach($row as $k => $v){
		$user[$k] = stripslashes($v);
	}
	$user['country'] = (int) $user['country'];
	$user['reg_date'] = (int) $user['reg_date'];
	$user['last_signin'] = (int) $user['last_signin'];
	
	$user['followers_count'] = (int) $user['followers_count'];
	$user['following_count'] = (int) $user['following_count'];
	$user['unread_notifications_count'] = (int) $user['unread_notifications_count'];
	
	$user['avatar_url'] = get_avatar_url($user['avatar'], $user['id']);
	$user['followers_compact'] = pm_compact_number_format($user['followers_count']);
	$user['following_compact'] = pm_compact_number_format($user['following_count']);
	$user['statuses_compact'] = pm_compact_number_format($user['statuses_count']);
	
	return $user;
}

function generate_unique_id(){
	return substr(md5(uniqid(time(), true)), 0, 7);
}

function username_to_id($username)
{
	if(!$username) return false;
	$username = stripslashes($username);
	$sql = "SELECT id FROM pm_users where username LIKE '". secure_sql($username) ."'";
	$result = mysql_query($sql);
	if(!$result)
		return 0;
	$total = mysql_num_rows($result);
	if($total > 0)
	{
		$r = mysql_fetch_assoc($result);
		mysql_free_result($result);
		return $r['id'];
	}
	return 0;
}

function banlist($user_id)
{
	$sql = "SELECT * FROM pm_banlist WHERE user_id = '".$user_id."'";
	$result = mysql_query($sql) or die(mysql_error());
	if(!$result)
		return false;
	$ban = array();
	if(mysql_num_rows($result) > 0)
		$ban = mysql_fetch_assoc($result);
	return $ban;
}

function mod_can($action = '')
{
	global $config;

	$user_can = $temp = $buff = array();
	
	$temp = explode(';', $config['moderator_can']);
	
	foreach ($temp as $key => $value)
	{
		$buff = explode(':', $value);
		
		$user_can[$buff[0]] = (int) $buff[1]; 
	}
	
	return ('' != $action) ? $user_can[$action] : $user_can;
}

function mod_cannot($action)
{
	$mod_can = mod_can($action);
	return ($mod_can) ? false : true;
}

function is_moderator()
{
	global $userdata;
	return ($userdata['power'] == U_MODERATOR) ? true : false;
}

function is_editor()
{
	global $userdata;
	return ($userdata['power'] == U_EDITOR) ? true : false;
}

function is_admin()
{
	global $userdata;
	return ($userdata['power'] == U_ADMIN) ? true : false;
}

function is_regular_user()
{
	global $userdata;
	if (is_array($userdata) && is_user_logged_in())
	{
		return ($userdata['power'] == U_ACTIVE) ? true : false;
	}
	
	return false;
}

function is_registered_user()
{
	if (is_regular_user() || is_editor() || is_moderator() || is_admin())
	{
		return true;
	}
	return false;
}

function get_avatar_url($avatar = '', $user_id = 0)
{
	if ($avatar != '')
	{
		return _URL .'/'. _UPFOLDER .'/avatars/'. $avatar;
	}
	
	return _URL .'/'. _UPFOLDER .'/avatars/default.gif';
}

