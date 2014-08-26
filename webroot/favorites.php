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
if( ! $logged_in)
{
	header("Location: "._URL. "/index."._FEXT);
	exit();
}

// define meta tags & common variables
$meta_title	 = ucfirst($userdata['username'])." - ".$lang['my_favorites'];
$video_id 	 = secure_sql($_GET['vid']);
$action 	 = trim($_GET['a']);
$add_problem = '';
$meta_description = '';

if( ! empty($userdata['id']) && $action == 'show')
{
	$sql	 = "SELECT * 
				FROM pm_favorites 
				WHERE user_id = '".$userdata['id']."' 
				ORDER BY id DESC";
				
	$query 	 = @mysql_query($sql);
	
	if (@mysql_num_rows($query) == 0) 
	{
		$problem = $lang['profile_msg_list_empty'];
	} 
	else 
	{
		$item = '';
		$i = 1;
		$first_video = array();
		$list = array();
		
		while ($fav = mysql_fetch_array($query)) 
		{
			$row = request_video($fav['uniq_id'], 'favorites');
			$modframework->trigger_hook('favorites_videoloop');
			$list[$i] = $row;
			$i++;
		}
	}
} 
else 
{
	header("Location: index."._FEXT);
	exit();
}

//	generate share link
$share_link = _URL."/";
if(_SEOMOD)
{
	$share_link .= "playlist/".$userdata['username'];
}
else
{
	$share_link .= "myfavorites.php?u=".$userdata['username'];
}

if($userdata['favorite'] == 1)
{
	$smarty->assign('share_link', $share_link);
}
 $modframework->trigger_hook('favorites_bottom');
$smarty->assign('action', 		$action);
$smarty->assign('add_problem', 	$add_problem);
$smarty->assign('problem', 		$problem);
$smarty->assign('favorite_videos_list', $list);
$smarty->assign('video_data',		$list[1]);
$smarty->assign('self', true);

// --- DEFAULT SYSTEM FILES - DO NOT REMOVE --- //
$smarty->assign('meta_title', 		$meta_title);
$smarty->assign('meta_description', $meta_description);
$smarty->assign('template_dir', 	$template_f);

if($action == 'add') 
{
	$smarty->display('favorites_add.tpl');
} 
else 
{
	$smarty->display('profile-favorites.tpl');
}
?>