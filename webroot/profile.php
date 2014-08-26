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

$username = secure_sql( urldecode($_GET['u']) );
$query = mysql_query("SELECT * FROM pm_users where username = '".$username."'");
$rows = mysql_num_rows($query);
$r = mysql_fetch_array($query);

if($rows == 0) {
	header("Location: ". _URL.'/404.php');
	exit();
}
$full_name = $r['name'];
$username = $r['username'];
//$gender = ucwords($r['gender']);
$gender = ucwords($lang[ $r['gender'] ]);
$country = countryid2name($r['country']);
$reg_date = time_since($r['reg_date']).' '.$lang['ago'];
$last_seen = time_since($r['last_signin']).' '.$lang['ago'];
$about = $r['about'];
$favorite = $r['favorite'];

$query_total = @mysql_query("SELECT COUNT(*) as total FROM pm_videos WHERE submitted = '".$username."' AND added <= '". time() ."'");
$total_submissions = @mysql_fetch_assoc($query_total);
$total_submissions = $total_submissions['total'];
@mysql_free_result($query_total);

if( $favorite == 1 ) 
{
	$fav_video_list = array();
	$uniq_ids = array();
	
	$sql = "SELECT uniq_id 
			FROM pm_favorites 
			WHERE user_id = '".$r['id']."' 
			LIMIT 16";
	
	if ($result = mysql_query($sql))
	{
		while ($row = mysql_fetch_assoc($result))
		{
			$uniq_ids[] = $row['uniq_id'];
		}
		mysql_free_result($result);
	}
	
	
	if (count($uniq_ids) == 0)
	{
		$problem = $lang['profile_msg_list_empty'];
	}
	else
	{
		$fav_video_list = get_video_list('', '', 0, 0, 0, array(), $uniq_ids); 
	}
	
	$share_link = _URL."/";
	if(_SEOMOD)
	{
		$share_link .= "playlist/".$r['username'];
	}
	else
	{
		$share_link .= "myfavorites.php?u=".$r['username'];
	}
	if($results == 0)
		$share_link = '';
}

// videos suggested by this user
$submitted_video_list = array();

$sql = "SELECT uniq_id 
		FROM pm_videos 
		WHERE submitted = '". secure_sql($username) ."' 
		  AND added <= '". time() ."' 
		ORDER BY id DESC
		LIMIT 16";
$result = @mysql_query($sql);

if ($result)
{
	$uniq_ids = array();
	while ($row = mysql_fetch_assoc($result))
	{
		$uniq_ids[] = $row['uniq_id'];
	}	
	mysql_free_result($result);
	
	$submitted_video_list = get_video_list('', '', 0, 0, 0, array(), $uniq_ids); 
}

if ($userdata['id'] == $r['id'])
{
	$pending_video_list = array(); 
	
	$sql = "SELECT id, video_title, yt_length, added, thumbnail 
			FROM pm_temp 
			WHERE user_id = ". $userdata['id'] ." 
			ORDER BY added DESC  
			LIMIT 0, 8";
	
	$result = @mysql_query($sql);
	if ($result)
	{
		$i = 0;
		if (mysql_num_rows($result) > 0)
		{
			while ($row = mysql_fetch_assoc($result))
			{
				$pending_video_list[$i] = array('id' => (int) $row['id'],
												'uniq_id' => (int) $row['id'],
												'video_title' => htmlentities($row['video_title']),
												'video_href' => '#',
												'thumb_img_url' => $row['thumbnail'],
												'author_username' => $userdata['username'],
												'author_profile_href' => _URL .'/profile.'. _FEXT .'?u='. $userdata['username'],
												'added_timestamp' => (int) $row['added'],
												'html5_datetime' => date('Y-m-d\TH:i:sO', (int) $row['added']), // ISO 8601,
												'full_datetime' => date('l, F j, Y g:i A', (int) $row['added']),
												'time_since_added' => time_since((int) $row['added']),
												'yt_length' => (int) $row['yt_length'],
												'duration' => sec2hms( (int) $row['yt_length']),
												'iso8601_duration' => iso8601_duration((int) $row['yt_length']), // ISO 8601
												'views_compact' => 0,
												'views' => 0,
												'likes_compact' => 0,
												'pending_approval' => true,
												);
				
				$i++;
			}
			mysql_free_result($result);
		}
		if (count($pending_video_list) > 0)
		{
			$submitted_video_list = array_merge($pending_video_list, $submitted_video_list);
		}
	}
}

$status = (islive($r['last_signin'])) ? $lang['memberlist_on'] : $lang['memberlist_off'];

if (_MOD_SOCIAL && $userdata['id'] != $r['id'])
{
	$r['is_following_me'] = is_follow_relationship($userdata['id'], $r['id']);
	$r['am_following'] = is_follow_relationship($r['id'], $userdata['id']);
}

if (_MOD_SOCIAL && $userdata['id'] == $r['id'])
{
	$from = 0; 
	
	$actor_bucket = array();
	$object_bucket = array();
	$target_bucket = array();
	$activity_meta_bucket = array();
	$activity_stream = get_following_activity_stream($from, ACTIVITIES_PER_PAGE);
	activity_stream_rollup($activity_stream, $actor_bucket, $object_bucket, $target_bucket, $activity_meta_bucket);
	
	$smarty->assign('total_activities', count($activity_stream));
	unset($activity_stream);

	$smarty->assign('actor_bucket', $actor_bucket);
	$smarty->assign('object_bucket', $object_bucket);
	$smarty->assign('target_bucket', $target_bucket);
	$smarty->assign('activity_meta_bucket', $activity_meta_bucket);
	
	if (empty($_COOKIE['suggest_profiles']) || $_COOKIE['suggest_profiles'] != 'no')
	{
		$who_to_follow = suggest_who_to_follow();
		$smarty->assign('who_to_follow_list', $who_to_follow);
	}
}

$banned = banlist($r['id']);

// define meta tags & common variables
$meta_title = sprintf($lang['profile_title'], $full_name, $username, _SITENAME);
$meta_description = sprintf($lang['profile_description'], $full_name, $username).' on '._SITENAME.'. '.fewchars($about, 40);
// end




$smarty->assign('full_name', $full_name);
$smarty->assign('username', $username);
$smarty->assign('gender', $gender);
$smarty->assign('country', $country);
$smarty->assign('reg_date', $reg_date);
$smarty->assign('last_seen', $last_seen);
$smarty->assign('status', $status);
$smarty->assign('about', $about);
$smarty->assign('avatar', get_avatar_url($r['avatar'], $r['id']));
$smarty->assign('favorite', $favorite);
$smarty->assign('fav_video_list', $fav_video_list);
$smarty->assign('share_link', $share_link);
$smarty->assign('user_is_banned', ($banned['user_id'] == $r['id']) ? true : false);

$smarty->assign('social_website', $r['website']);
$smarty->assign('social_facebook', $r['facebook']);
$smarty->assign('social_twitter', $r['twitter']);
$smarty->assign('social_lastfm', $r['lastfm']);
$smarty->assign('power', $r['power']);
$smarty->assign('submitted_video_list', $submitted_video_list);
$smarty->assign('profile_data', $r);
$smarty->assign('total_submissions', $total_submissions);

// --- DEFAULT SYSTEM FILES - DO NOT REMOVE --- //
$smarty->assign('meta_title', $meta_title);
$smarty->assign('meta_description', $meta_description);
$smarty->assign('template_dir', $template_f);
$modframework->trigger_hook('user_profile_display');
$smarty->display('profile.tpl');
?>