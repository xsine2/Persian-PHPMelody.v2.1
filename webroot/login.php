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

session_start();

@header( 'Expires: Wed, 11 Jan 1984 05:00:00 GMT' );
@header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s' ) . ' GMT' );
@header( 'Cache-Control: no-cache, must-revalidate, max-age=0' );
@header( 'Pragma: no-cache' );

require('config.php');
require_once('include/functions.php');
require_once('include/user_functions.php');
// define meta tags & common variables
$meta_title = $lang['login']." - "._SITENAME;
$meta_description = '';
// end

// Initialize some variables
$errors = array();
$nr_errors = 0;
$logged_in = 0;
load_countries_list();

$smarty->register_function('list_categories', 'list_categories');
$smarty->assign('meta_title', $meta_title);
$smarty->assign('meta_description', $meta_description);
$smarty->assign('template_dir', $template_f);
$smarty->assign('show_countries_list', 1);
$smarty->assign('countries_list', $_countries_list);

$mode = '';
$success = 0;
$redir = '';
$mode = ($_GET['do']) ? $_GET['do'] : '';

switch($mode){ 
	default:
	case 'login':
		
		$smarty->assign('display_form', 'login');

		// get the last referer so that we can redirect the user to his last visited page after logging him in.
		$redir = get_last_referer();
		if( $redir === false){ 	
			$redir = '/index.'._FEXT;
		}
		$dobreak = false;
		$modframework->trigger_hook('login_login_pre');
		if($dobreak) break;
		//	check if user is already logged in
		//	if he already is, redirect him to index page
		if (is_user_logged_in()) 
		{
			header("Location: ". _URL . $redir);
			exit();
		}
		
		//	check if the form has been submitted
		if( isset($_POST['Login'])) 
		{
			$email = $username = '';
			if (strpos($_POST['username'], '@') !== false && strlen($_POST['username']) > 5)
			{
				$email = trim($_POST['username']);
				$email = str_replace("\'", "''", $email);
				$email = secure_sql($email);
				
				if (is_real_email_address($email))
				{
					$sql = "SELECT username 
							FROM pm_users 
							WHERE email LIKE '$email'";
				
					if ($result = @mysql_query($sql))
					{
						$row = mysql_fetch_assoc($result);
						mysql_free_result($result);
						$username = $row['username'];
					}
				}
			}
			else
			{
				$username = sanitize_user(trim($_POST['username'], 0));
			}
			$pass = $_POST['pass'];

			if (empty($username))
			{
				$errors['username'] = $lang['login_msg1'];
				
				if ($email != '')
				{
					$errors['username'] = $lang['login_msg3'];
				}
			}
			if (empty($pass))
			{
				$errors['pass'] = $lang['login_msg2'];
			}
			
			if ( ! confirm_login($username, $pass, false) && $username != '' && $pass != '')
			{
				$errors[] = $lang['login_msg3'];
			}
			
			if (count($errors) == 0)
			{
				$user_id = username_to_id($username);
				$ban = banlist($user_id);

				if ($ban['user_id'] == $user_id && $user_id != '')
				{				
					$errors[] = sprintf($lang['login_msg16'], $ban['reason']);
				}
			}
			
			if (count($errors) > 0)
			{
				$smarty->assign('errors', $errors);
				$smarty->display('user-auth.tpl');
				exit();
			}
			else 
			{
				// this means everything is ok!
				// log him in.
				$remember = $_POST['remember'];
				$dobreak = false;
				$modframework->trigger_hook('login_login_mid');
				if($dobreak) break;
				if( is_user_account_active('', $username) == 0 )
				{
					if ($config['account_activation'] == AA_USER)
					{
						$errors[] = $lang['login_msg4'];
					}
					
					if ($config['account_activation'] == AA_ADMIN)
					{
						$errors[] = $lang['login_msg17'];
					}
					
					$smarty->assign('success', 0);
					$smarty->assign('errors', $errors);
				}
				else
				{
					log_user_in($username, $pass, $remember, false);
					header("Location: ". _URL . $redir);
					exit();
				}
				$dobreak = false;
				$modframework->trigger_hook('login_login_post');
				if($dobreak) break;
				$smarty->display('user-auth.tpl');
				exit();
			}
		}
		else { 
			// show the form. 
			$dobreak = false;
			$modframework->trigger_hook('login_login_show');
			if($dobreak) break;
			$smarty->display('user-auth.tpl');
			exit();
		}
	break;
	
	
	case 'register':
		header("Location: " ._URL. "/register."._FEXT);
		exit();
	break;
	
	
	case 'logout':
		$dobreak = false;
		$modframework->trigger_hook('login_logout');
		if($dobreak) break;
		logout();
		$redir = get_last_referer();
		if( $redir === false){ 	
			$redir = '/index.'._FEXT;
		}
		header("Location: " ._URL. $redir);
		exit();
	break;
	
	
	case 'forgot_pass':

		$smarty->assign('display_form', 'forgot_pass');
		
		if(is_user_logged_in()) { 
			logout();
		}
		$dobreak = false;
		$modframework->trigger_hook('login_forgotpass_pre');
		if($dobreak) break;
		if (isset($_POST['Send']))
		{
			$email = $username = '';
			if (strpos($_POST['username_email'], '@') !== false)
			{
				$email = trim($_POST['username_email']);
			}
			else
			{
				$username = trim($_POST['username_email']);
			}
			$inputs = array();
			
			foreach($_POST as $k => $v)
			{
				$inputs[$k] = htmlspecialchars($v);
			}
			$smarty->assign('inputs', $inputs);
			
			if( empty($email) && empty($username) )
			{
				$errors['username_email'] = $lang['login_msg8'];
			}
			elseif ($email != '')
			{
				$validation = validate_email($email);
				
				if ($validation == 1)
				{
					$errors['email'] = $lang['register_err_msg2'];
				}
				else if ($validation == false)
				{
					$errors['email'] = $lang['login_msg7'];
				}
				
			}
			else
			{
				$validation = check_username($username);

				if ($validation == 1)
				{
					$errors['username'] = $lang['register_err_msg4'];
				}
				else if ($validation == 2)
				{
					$errors['username'] = $lang['register_err_msg5'];
				}
			}
			
			if (count($errors) > 0)
			{
				$smarty->assign('errors', $errors);
				$smarty->assign('success', 0);
				$smarty->display('user-auth.tpl');
				exit();
			}
			
			$sql = "SELECT id, username, name, email, power, activation_key
						FROM pm_users 
						WHERE ";
			if ($email != '')
			{
				$email = $email;
				$email = stripslashes($email);
				$email = secure_sql($email);

				$sql .= " email LIKE '". $email ."'";
			}
			else
			{
				$username = stripslashes($username);
				$username = strtolower($username);
				$username = secure_sql($username);
				
				$sql .= " LOWER(username) = '". $username ."'";
			}

			$result = @mysql_query($sql);
			$user = @mysql_fetch_assoc($result);
			@mysql_free_result($result);
			
			$dobreak = false;
			$modframework->trigger_hook('login_forgotpass_send');
			if($dobreak) break;
			
			if ($user == false)
			{
				$errors[] = $lang['login_msg8']; // user not found
			}
			else if( $user['power'] == U_INACTIVE )
			{
				if ($user['activation_key'] != '')
				{
					$errors[] = $lang['login_msg4'];
				}
				else
				{
					$errors[] = $lang['login_msg17'];
				}
			}
			else
			{
				$new_pass = array();
				$new_pass = reset_password($user['email']);
				
				if( ! $new_pass ) {
					$errors[] = $lang['login_msg9'];
					$smarty->assign('errors', $errors);
					$smarty->assign('success', 0);
					$smarty->display('user-auth.tpl');
					exit();
				}
				else
				{
					$email = $user['email'];
					
					$activation_link  =    _URL;
					$activation_link .=    "/login." . _FEXT;
					$activation_link .=    "?do=pwdreset&u=" . $user['id'] . "&key=" . $new_pass['key'];
					
					if (preg_match("/^(([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.){3}([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])$/", $_SERVER['REMOTE_ADDR']) !== false)
					{
						$ip = $_SERVER['REMOTE_ADDR'];
					}
					else
					{
						$ip = 'Unknown';
					}
					
					require_once("include/class.phpmailer.php");
						//*** DEFINING E-MAIL VARS
						$mailsubject = sprintf($lang['mailer_subj3'], _SITENAME);
						
						$array_content[]=array("mail_username", $user['username']);  
						$array_content[]=array("mail_new_pass", $new_pass['pass']);
						$array_content[]=array("mail_ip", $ip);
						$array_content[]=array("mail_sitename", _SITENAME);
						$array_content[]=array("mail_url", _URL);
						$array_content[]=array("mail_activation_link", $activation_link);
						//*** END DEFINING E-MAIL VARS
						if(file_exists('./email_template/'.$_language_email_dir.'/email_forgot_password.txt'))
						{
							$mail = send_a_mail($array_content, $email, $mailsubject, 'email_template/'.$_language_email_dir.'/email_forgot_password.txt');
						}
						elseif(file_exists('./email_template/english/email_forgot_password.txt'))
						{
							$mail = send_a_mail($array_content, $email, $mailsubject, 'email_template/english/email_forgot_password.txt');
						}
						elseif(file_exists('./email_template/email_forgot_password.txt'))
						{
							$mail = send_a_mail($array_content, $email, $mailsubject, 'email_template/email_forgot_password.txt');
						}
						else
						{
							@log_error('Email template "email_forgot_password.txt" not found!', 'User Login Page', 1);
							$mail = TRUE;
						}
						if($mail !== TRUE)
						{
							@log_error($mail, 'User Login Page', 1);
						}
					
					// ** END SENDING EMAIL ** //
					$smarty->assign('success', 1);
					$smarty->display('user-auth.tpl');
					exit();
				}
			}
			
			$smarty->assign('errors', $errors);
			$smarty->assign('success', 0);
			$smarty->display('user-auth.tpl');
			exit();
		}// end if $_POST['send'] == 'send'
		else{ 
		
			// show the form;
			$smarty->assign('success', 0);
			$smarty->display('user-auth.tpl');
			exit();
		}
	break;
	case 'pwdreset':
	case 'activate':
		
		$dobreak = false;
		$modframework->trigger_hook('login_activate_pre');
		if($dobreak) break;
		if(is_user_logged_in()) {
			header("Location: "._URL. "/index."._FEXT);
			exit();
		}
		
		$user_id	= (int) $_GET['u'];
		$key		= trim($_GET['key']);
		$success	= 0;

		if($user_id == '' || $key == '')
		{
			$errors[] = 'Invalid request.';
		}
		else
		{
			$sql = "SELECT * FROM pm_users WHERE id = '".secure_sql($user_id)."'";
			$result = mysql_query($sql) or die(mysql_error());
			if( ! $result )
			{
				echo $lang['login_msg11'].' <em>' . $config['contact_mail'] . '</em>';
				exit();
			}
			$user = mysql_fetch_assoc($result);
			mysql_free_result($result);
			
			if($mode == 'activate')
			{
				if($user == '' || is_array($user) === FALSE)
				{
					$errors[] = $lang['login_msg12'];
				}
				elseif($user['power'] != U_INACTIVE)
				{
					$errors[] = $lang['login_msg13'];
				}
				elseif($user['activation_key'] == '' || (strcmp($user['activation_key'], $key) != 0))
				{
					$errors[] = $lang['login_msg14'];
				}
				else
				{
					$sql = "UPDATE pm_users SET power = '".U_ACTIVE."', activation_key = '' WHERE id = '".$user['id']."'";
					$result = @mysql_query($sql);
					$dobreak = false;
					$modframework->trigger_hook('login_activate_post');
					if($dobreak) break;
					if( ! $result )
					{
						$errors[] = $lang['login_msg11'].' <em>' . $config['contact_mail'] . '</em>';
					}
					else
					{
						$success = 1;
					}
				}
			}
			elseif($mode == 'pwdreset')
			{
				if($user == '' || is_array($user) === FALSE)
				{
					$errors[] = $lang['login_msg12'];
				}
				elseif($user['activation_key'] == '' || (strcmp($user['activation_key'], $key) != 0))
				{
					$errors[] = $lang['login_msg14'];
				}
				else
				{
					$sql = "UPDATE pm_users SET password = '".$user['new_password']."', activation_key = '', new_password = '' WHERE id = '".$user['id']."'";
					$dobreak = false;
					$modframework->trigger_hook('login_pwdreset_post');
					if($dobreak) break;
					$result = @mysql_query($sql);
					if( ! $result )
					{
						$errors[] = $lang['login_msg11'].' <em>' . $config['contact_mail'] . '</em>';
					}
					else $success = 1;
				}
			}
		}
		
		$smarty->assign('errors', $errors);
		$smarty->assign('success', $success);
		if($mode == 'activate')
		{
			$smarty->assign('display_form', 'activate_acc');
			$smarty->display('user-auth.tpl');
		}
		elseif($mode == 'pwdreset')
		{
			$smarty->assign('display_form', 'pwdreset');
			$smarty->display('user-auth.tpl');
		}
		exit();
	break;	

}// end big Switch
exit();
?>