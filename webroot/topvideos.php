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

$page = (int) $_GET['page'];
if ( ! $page)
{
	$page = 1;
}
$limit	= $config['top_page_limit'];
$from 	= $page * $limit - ($limit);
$total_videos = (int) $config['published_videos'];
$total_pages = ceil($total_videos / $limit);

//	Reset chart?
$now = time();
$span = ($config['chart_days'] * (3600 * 24))/4;
if($span > 0)
{
	if($config['chart_last_reset'] < ($now - $span))
	{
		reset_chart();
	}
}
$modframework->trigger_hook('topvideos_top');

$cats = trim($_GET['c']);
if ( ! preg_match('/(^[a-zA-Z0-9_-]+)$/i', $cats))
{
	$cats = '';
}
$action = trim($_GET['do']);

if ( ! in_array($action, array('recent', 'rating')))
{
	$action = '';
}

$categories_list = categories_dropdown(array('options_only' => true, 'select_all_option' => false, 'value_attr_db_col' => 'tag', 'selected' => $cats));
$categories_list = preg_replace('/value="(.*?)"/', 'value="'. _URL .'/topvideos.'. _FEXT .'?c=$1"', $categories_list);

if ($cats != '') 
{
	$catid = get_catid($cats);
	$cat_name = get_catname($cats);	
	
	$sql = "SELECT published_videos 
			FROM pm_categories
			WHERE id = '". $catid ."'";
	$result = mysql_query($sql); 
	$row = mysql_fetch_assoc($result);
	mysql_free_result($result);
	
	$total_videos = $row['published_videos'];
	
	unset($sql, $result, $row);
	
	$sql = "SELECT id  
			 FROM pm_videos 
			 WHERE added <= '". time() ."'
			 AND (category LIKE '$catid' 
			 	  OR category LIKE '$catid,%' 
			 	  OR category LIKE '%,$catid' 
			 	  OR category LIKE '%,$catid,%') 
			 ORDER BY site_views DESC LIMIT $from, $limit";
} 
elseif ($action == 'recent') 
{
	$videos = get_chart(50);
	$total_videos = 50;
}
elseif ($action == 'rating')
{
	$sql = "SELECT id, COALESCE(pm_bin_rating_meta.score, 0) as score 
			FROM pm_videos 
			LEFT JOIN pm_bin_rating_meta ON (pm_videos.uniq_id = pm_bin_rating_meta.uniq_id) 
			WHERE added <= '". time() ."'
			ORDER BY score DESC 
			LIMIT $from, $limit" ;
}
else 
{
	$sql = "SELECT id 
			FROM pm_videos 
			WHERE added <= '". time() ."' 
			ORDER BY site_views DESC 
			LIMIT $from, $limit" ;

}

if($action == '')
{
	$ids = array();
	$result = mysql_query($sql);
	while ($row = mysql_fetch_assoc($result))
	{
		$ids[] = $row['id'];
	}
	mysql_free_result($result);
	
	$list = array();
	if (count($ids) > 0)
	{
		$list = get_video_list('site_views', 'DESC', 0, 0, 0, $ids);
	}
	
	if ($page == $total_pages || $total_pages == 0)
	{
		// recount published videos count
		$count = count_entries('pm_videos', '1',  '1\' AND added <= \''. time());
		if ($config['published_videos'] != $count)
		{
			$total_videos = $count;
			update_config('published_videos', $count);
		}
	}
	
}
elseif ($action == 'recent') 
{
	$list = array();

	if (count($videos) > 0)
	{
		$ids = array();
		$unsorted_list = array(); // for sorting
		foreach ($videos as $uniq_id => $v)
		{
			$ids[] = $v['id'];
		}
		
		$unsorted_list = get_video_list('', '', 0, 0, 0, $ids);
	
		$i = 0;
		foreach ($videos as $uniq_id => $v)
		{
			foreach ($unsorted_list as $k => $video_data)
			{
				if ($video_data['id'] == $v['id'])
				{
					$list[$i] = (array) $video_data;
					break;
				}
			}
			$i++;
		}	
		unset($unsorted_list);
	}
}
elseif ($action == 'rating')
{
	$ids = array();
	$result = mysql_query($sql);
	while ($row = mysql_fetch_assoc($result))
	{
		$ids[] = $row['id'];
	}
	mysql_free_result($result);
	
	if (count($ids) > 0)
	{
		$list = array();
		$unsorted_list = get_video_list('', '', 0, 0, 0, $ids);
		$i = 0;
		foreach ($ids as $k => $id)
		{
			foreach ($unsorted_list as $kk => $video_data)
			{
				if ($video_data['id'] == $id)
				{
					$list[$i] = (array) $video_data;
					break;
				}
			}
			$i++;
		}	
		unset($unsorted_list);
	}
}

$count_last_days = count_days($config['chart_last_reset'], time());

if($config['chart_days'] == 0) 
{
	if($count_last_days >= 0 && $count_last_days <= 1)
	{
		$smarty->assign('chart_days', $lang['yesterdays_top']);
	}
	else
	{
		$smarty->assign('chart_days', sprintf($lang['top_videos_last_x_days'], $count_last_days));
	}
} 
else 
{
	if(($count_last_days >= 0 && $count_last_days <= 1) && $config['chart_days'] <= 1)
	{
		$smarty->assign('chart_days', $lang['yesterdays_top']);
	}
	else
	{
		$smarty->assign('chart_days', sprintf($lang['top_videos_last_x_days'], $config['chart_days']));
	}
}

$i = 1;
foreach ($list as $k => $v)
{
	$list[$k]['position'] = $from + $i++;
}

$pagination = '';
if ($total_videos > $limit)
{
	$filename = (_SEOMOD) ? 'topvideos.html' : 'topvideos.php';
	
	$extra = '';
	if ($cats != '')
	{
		$extra = 'c='.$cats;
	}
	if ($action != '')
	{
		$extra = 'do='.$action;
	}
	
	$pagination = generate_smart_pagination($page, $total_videos, $limit, 1, $filename, $extra);
}

// define meta tags & common variables
$meta_title = $lang['top_m_videos_from'];
if(!empty($date)) {
	$meta_title .= ' - '.$lang["added"].' '.$date;
} 
if(!empty($cats)) {
	$meta_title .= ' - '.$cat_name;
}
if(!empty($page) && $page > 1) {
	$meta_title .= ' - '.sprintf($lang['page_number'], $page);
}
$meta_title = sprintf($meta_title, _SITENAME);
$meta_description = $meta_title;
// end
$smarty->assign('cat_name', $cat_name);
$smarty->assign('results', $list);
$smarty->assign('categories_list', $categories_list);
$smarty->assign('pagination', $pagination);

// --- DEFAULT SYSTEM FILES - DO NOT REMOVE --- //
$smarty->assign('meta_title', $meta_title);
$smarty->assign('meta_description', $meta_description);
$smarty->assign('template_dir', $template_f);
$modframework->trigger_hook('topvideos_bottom');
$smarty->display('video-top.tpl');
?>