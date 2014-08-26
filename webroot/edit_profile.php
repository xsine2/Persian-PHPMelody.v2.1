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

require('config.php');
require_once('include/functions.php');
require_once('include/user_functions.php');
require_once('include/islogged.php');

if( !is_user_logged_in() )
{
	header("Location: "._URL. "/index."._FEXT);
	exit();
}
if($logged_in)
{
	$query = mysql_query("SELECT * FROM pm_users WHERE id = '".$userdata['id']."'");
	$rows = mysql_num_rows($query);
	$r = mysql_fetch_array($query);
	mysql_free_result($query);

	if($rows == 0)
	{
		header("Location: "._URL."");
		exit();
	}
	$userdata['about'] = str_replace("<br />", "\n", $userdata['about']);

	$smarty->assign('userdata', $userdata);
	$smarty->assign('form_action', 'edit_profile.'._FEXT);

	if(isset($_POST['save']))
	{
		$errors 	= array();
		$links 		= array();
		$link_patterns	= array('facebook' => '#facebook\.#',
								'twitter' => '#twitter\.com\/#',
								'lastfm' => '#last\.fm\/#');
		$nr_errors	= 0;
		$success 	= 0;

		$aboutme	= $_POST['aboutme'];
		$pass		= md5($_POST['pass']);
		$new_pass	= $_POST['new_pass'];
		$email		= trim($_POST['email']);
		$name		= trim($_POST['name']);
		$gender		= secure_sql($_POST['gender']);
		$country	= secure_sql( (int) $_POST['country']);
		$favorite	= secure_sql( (int) $_POST['favorite']);
		$links['website']	= trim($_POST['website']);
		$links['facebook']	= trim($_POST['facebook']);
		$links['twitter']	= trim($_POST['twitter']);
		$links['lastfm']	= trim($_POST['lastfm']);

		$inputs = array();
		foreach($_POST as $key => $val)
		{
			$val = trim($val);
			$val = specialchars($val, 1);
			$inputs[$key] = $val;
		}
		$smarty->assign('inputs', $inputs);

		$modframework->trigger_hook('edit_profile_pre');
		if(isset($aboutme))
		{
			$aboutme = removeEvilTags($aboutme);
			$aboutme = word_wrap_pass($aboutme);
			$aboutme = secure_sql($aboutme);
			$aboutme = specialchars($aboutme, 1);
			$aboutme = str_replace('\n', "<br />", $aboutme);
		}
		if(strcmp($name, $userdata['name']) != 0)
		{
			$name = removeEvilTags($name);
			$name = secure_sql($name);
			$name = specialchars($name, 1);
		}
		else
		{
			$name = secure_sql($userdata['name']);
			$name = specialchars($name, 0);
		}
		if ( ! in_array($gender, array('male', 'female')))
		{
			$gender = '';
		}

		$email_validation = validate_email($email);

		switch($email_validation)
		{
			case 1:
				$errors['email'] = $lang['register_err_msg2'];
			break;
			case 2:
				if( strcmp($email, $userdata['email']) != 0 )
					$errors['email'] = $lang['register_err_msg3'];
			break;
		}

		if(strcmp($pass, $userdata['password']) != 0)
		{
			$errors['pass'] = $lang['ep_msg6'];
		}
		if($country == -1 || $country == '')
		{
			$errors['country'] = $lang['ep_msg7'];
		}

		foreach ($links as $k => $v)
		{
			if (strlen($v) > 0 && strpos($v, "http://") === false)
			{
				$links[$k] = "http://". $v;
			}
		}

		foreach ($link_patterns as $field => $pattern)
		{
			if (strlen($links[$field]) > 0)
			{
				if ( ! preg_match($pattern, $links[$field]))
				{
					$errors[$field] = $lang['profile_msg_social_link'];
				}
			}
		}

		$nr_errors = count($errors);
		if( $nr_errors == 0 )
		{
			foreach ($links as $k => $v)
			{
				$links[$k] = htmlspecialchars($v);
				$links[$k] = secure_sql($links[$k]);
			}

			$sql = "UPDATE pm_users SET ";

			if($new_pass != '')
			{
				$sql .= "password = '".md5($new_pass)."', ";
			}
			$sql .= "name = '".$name."', gender = '".$gender."', country = '".$country."', email = '".$email."', about = '".$aboutme."', favorite = '".$favorite."'";
			$sql .= ", website = '". $links['website'] ."', facebook = '". $links['facebook'] ."' ";
			$sql .= ", twitter = '". $links['twitter'] ."', lastfm = '". $links['lastfm'] ."' ";
			$modframework->trigger_hook('edit_profile_sql');
			$sql .= " WHERE id = '".$userdata['id']."'";
			$result = @mysql_query($sql);
			$modframework->trigger_hook('edit_profile_post');

			if( !$result )
			{
				$errors['failure'] = $lang['ep_msg8'];
				$success = 0;
			}
			else
			{
				$success = 1;
			}
		}
		else
		{
			$success = 0;
		}
		$smarty->assign('nr_errors', $nr_errors);
		$smarty->assign('errors', $errors);
		$smarty->assign('success', $success);
		if( $new_pass != '' )
		{
			$smarty->assign('changed_pass', 1);
		}
	}
}
// define meta tags & common variables
$meta_title = $lang['edit_profile'];
$meta_description = '';
// end

$show_countries_list = 1;
load_countries_list();

$smarty->assign('show_countries_list', $show_countries_list);
$smarty->assign('countries_list', $_countries_list);
$modframework->trigger_hook('edit_profile_display');

// --- DEFAULT SYSTEM FILES - DO NOT REMOVE --- //
$smarty->assign('meta_title', $meta_title);
$smarty->assign('meta_description', $meta_description);
$smarty->assign('template_dir', $template_f);
$smarty->display('profile-edit.tpl');
?>