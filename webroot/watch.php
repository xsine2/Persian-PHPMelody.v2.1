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
require_once('include/rating_functions.php');

$unique_id = $_GET['vid'];
$video_is_restricted = false;
$modframework->trigger_hook('detail_top');

if ( ! empty($unique_id) && strlen($unique_id) < 10) 
{
	$video = request_video($unique_id);

	$modframework->trigger_hook('detail_mid');
	if ( ! $video)
	{
		header('Location: '. _URL .'/404.php');
		exit();		
	}
	
	if ( ! is_admin() && ( ! is_moderator() || (is_moderator() && mod_cannot('manage_videos'))) && $video['added'] > time())
	{
		$video = 0;
	}
	
	$category_name = make_cats($video['category']);
	$now = time();

	if (is_user_logged_in()) 
	{
		$query = @mysql_query("SELECT COUNT(*) as total FROM pm_favorites WHERE user_id = '".$userdata['id']."' AND uniq_id = '".$video['uniq_id']."'");
		if ( ! $query)
		{
			$isfavorite = 0;
		}
		else
		{
			$res = mysql_fetch_array($query);
			@mysql_free_result($query);
			
			if ($res['total'] > 0) 
			{
				$isfavorite = 1;
			}
		}
		$total_f = count_entries('pm_favorites', 'user_id', $userdata['id']);
	}
	
	if ( ! $logged_in && $video['restricted'] == '1')
	{
		$video_is_restricted = true;
	}
	
	$update_view_count = update_view_count($video['id'], $video['site_views']);
	if ( ! $video_is_restricted && $update_view_count)
	{
		add_to_chart($video['uniq_id']);
	}
	
	if (_MOD_SOCIAL && $update_view_count && is_user_logged_in())
	{
		log_activity(array( 'user_id' => $userdata['id'],
							'activity_type' => ACT_TYPE_WATCH,
							'object_id' => $video['id'],
							'object_type' => ACT_OBJ_VIDEO,
							'object_data' => $video
							));
	}
	
	if (_MOD_SOCIAL && is_user_logged_in())
	{
		$video['am_following'] = is_follow_relationship($video['author_user_id'], $userdata['id']);
	}
}
else 
{
	header("Location: index.". _FEXT);
	exit();
}
$tags_arr = array();
$tags_arr = get_video_tags($video['uniq_id'], 1);
$tags	  = '';
if (count($tags_arr) > 0) 
foreach ($tags_arr as $k => $tag)
{
	$tags .= $tag['href'].", ";
}
$tags = substr($tags, 0, -2);
if(isset($_COOKIE[COOKIE_AUTHOR]) && $_COOKIE[COOKIE_AUTHOR] != '')
	$smarty->assign('guestname', str_replace( array('"', '>', '<'), "", $_COOKIE[COOKIE_AUTHOR]) );
else
	$smarty->assign('guestname', '');
$must_sign_in = sprintf($lang['must_sign_in'], _URL."/login."._FEXT, _URL."/register."._FEXT);
$smarty->assign('must_sign_in', $must_sign_in);

$twitter_status  = '';
$twitter_status  = $video['video_title'];
$twitter_status .= ' '. makevideolink($video['uniq_id'], $video['video_title'], $video['video_slug']);
$twitter_status = urlencode($twitter_status);

// define meta tags
$meta_title = $video['video_title'];
$video['excerpt'] = (empty($video['excerpt'])) ? $video['video_title'] : $video['excerpt'];
$meta_description = generate_excerpt(str_replace('"', '&quot;', $video['excerpt']), 150) .'...';

$meta_keywords = '';
if(is_array($tags_arr))
foreach($tags_arr as $id => $v)
{
	$meta_keywords .= $v['tag'] . ', ';
}
$meta_keywords = substr($meta_keywords, 0, -2);
// end

$most_liked_comment = false;
if ( ! $video_is_restricted && $video['allow_comments'] == 1)
{	
	$comment_list = get_comment_list($video['uniq_id'], 1);
	$comment_count = count_entries('pm_comments', 'uniq_id', $video['uniq_id']."' AND approved='1");
	$mod_can = mod_can();
	
	if ($userdata['power'] == U_ADMIN || ($userdata['power'] == U_MODERATOR && $mod_can['manage_comments']))
	{
		$smarty->assign('can_manage_comments', true);
	}
	else
	{
		$smarty->assign('can_manage_comments', false);
	}
	
	$comment_pagination_obj = '';
	if ($comment_count > $config['comments_page'])
	{
		$comment_pagination_obj = generate_comment_pagination_object($video['uniq_id'], 1, $comment_count, $config['comments_page']);
	}
	if ($comment_count > 0)
	{
		$most_liked_comment = get_most_liked_comment($video['uniq_id']);
		$most_liked_comment = (array) $most_liked_comment[0];
		
		if ($most_liked_comment['up_vote_count'] <= 2)
		{
			$most_liked_comment = false;
		}
		
		// remove duplicate
		if ($config['comment_default_sort'] == 'score' && is_array($most_liked_comment))
		{
			unset($comment_list[0]);
		}
	}
}
else
{
	$comment_list = array();
	$comment_count = 0;
	$smarty->assign('can_manage_comments', false);
}
$tmp_parts = explode(',', $video['category']);
$same_category_id = array_pop($tmp_parts);
$related_video_list = get_related_video_list($same_category_id, $video['video_title'], $config['watch_related_limit'], $video['id']);
$top_rated_video_list = get_top_rated_video_list($same_category_id, $config['watch_toprated_limit'] , $video['id']);
unset($same_category_id);

// exclude current video from these lists
foreach ($related_video_list as $k => $vid)
{
	if ($vid['uniq_id'] == $unique_id)
	{
		unset($related_video_list[$k]);
	}
}
foreach ($top_rated_video_list as $k => $vid)
{
	if ($vid['uniq_id'] == $unique_id)
	{
		unset($top_rated_video_list[$k]);
	}
}

$facebook_image_src = str_replace('mqdefault','0', show_thumb($video['uniq_id']));

$video_link = urldecode(makevideolink($video['uniq_id'], $video['video_title'], $video['video_slug']));
$smarty->assign('video_data', $video);
$smarty->assign('twitter_status', $twitter_status);
$smarty->assign('facebook_image_src', $facebook_image_src);

$smarty->assign('facebook_like_title', urlencode($video['video_title']));
$smarty->assign('facebook_like_href', urlencode($video_link));
$smarty->assign('show_addthis_widget', $config['show_addthis_widget']);
$smarty->assign('jwplayerkey', $config['jwplayerkey']);

$smarty->assign('embedcode_to_share', generate_embed_code($video['uniq_id'], $video, true, 'iframe'));
$smarty->assign('embedcode', generate_embed_code($video['uniq_id'], $video, false, 'iframe'));
$smarty->assign('uniq_id', $video['uniq_id']);
$smarty->assign('related_video_list', $related_video_list);
$smarty->assign('popular_video_list', $top_rated_video_list);
$smarty->assign('isfavorite', $isfavorite);
$smarty->assign('countfavorites', $total_f);
$smarty->assign('comment_list', $comment_list);
$smarty->assign('most_liked_comment', $most_liked_comment);
$smarty->assign('comment_count', $comment_count);
$smarty->assign('category_name', $category_name);
$smarty->assign('comment_pagination_obj', $comment_pagination_obj);

$smarty->register_function('list_categories', 'list_categories');
$smarty->assign('tags', $tags);
$smarty->assign('guests_can_comment', ($video_is_restricted) ? 0 : $config['guests_can_comment']);
$smarty->assign('user_id', $userdata['id']);
$smarty->assign('bin_rating_vote_value', bin_rating_user_has_voted($video['uniq_id'])); // value = 1, 0 or false

serve_preroll_ad('detail', $video);

// --- DEFAULT SYSTEM FILES - DO NOT REMOVE --- //

$smarty->assign('meta_title', $meta_title);
$smarty->assign('meta_keywords', $meta_keywords);
$smarty->assign('meta_description', $meta_description);
$smarty->assign('template_dir', $template_f);
$modframework->trigger_hook('detail_bottom');
$smarty->display('video-watch.tpl');
?>