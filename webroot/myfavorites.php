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
$modframework->trigger_hook('favorites_top');
$who = trim($_GET['u']);
$who = str_replace( array('"', "/"), "", $who);
$who = username_to_id($who);

if($who == 0)
{
	header("Location: "._URL. "/index."._FEXT);
	exit();
}

$owner = fetch_user_advanced($who);

if($owner['id'] == $userdata['id'])
{
	header('Location: '._URL.'/favorites.php?a=show');
	exit();
}

if($owner['favorite'] == 0)
{
	$error_msg = $lang['favorites_msg2'];
}
else
{
	$videos = array();
	$videos = request_user_playlist($owner['id']);
	
	if($videos === false || (count($videos) == 0))
	{
		$error_msg = $lang['favorites_msg3'];
	}
	else
	{
		$i = 1;
		$list = array();
		foreach ($videos as $uniq_id => $video)
		{
			$list[$i++] = $video;
		}
		unset($videos);
	}
}
$modframework->trigger_hook('favorites_bottom');

$meta_title = sprintf($lang['myfavorites_title'], $owner['username']);

$smarty->assign('action', 	'show');
//$smarty->assign('favorite_videos_list', $videos);
$smarty->assign('favorite_videos_list', $list);
$smarty->assign('problem', 	$error_msg);
//$smarty->assign('video', 	$first_video);
$smarty->assign('video_data', 	$list[1]);
$smarty->assign('self', false);

// --- DEFAULT SYSTEM FILES - DO NOT REMOVE --- //
$smarty->assign('meta_title', 		$meta_title);
$smarty->assign('meta_description', $meta_description);
$smarty->assign('template_dir', 	$template_f);

$smarty->display('profile-favorites.tpl');
?>