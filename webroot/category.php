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
$modframework->trigger_hook('category_top');
$cat_name = secure_sql($_GET['cat']);
$page = $_GET['page'];
$sortby = secure_sql($_GET['sortby']);
$order = secure_sql($_GET['order']);

if ( ! in_array($sortby, array('views', 'date', 'rating', 'title')))
{
	$sortby = '';
}

if ( ! in_array($order, array('desc', 'asc', 'DESC', 'ASC')))
{
	$order = '';
}

if($sortby == 'views') {
	$sql_sortby = 'site_views';
	$order = 'DESC';
}
elseif($sortby == 'date') {
	$sql_sortby = 'added';
	$order = 'DESC';
}
elseif($sortby == 'rating') {
	$sql_sortby = 'total_value';
	$order = 'DESC';
}
elseif($sortby == 'title') {
	$sql_sortby = 'video_title';
	$order = 'ASC';
}

if(!empty($cat_name)) { 
	$cats = get_catid($cat_name);
	// CHECK IF CATEGORY EXISTS IN DB. OTHERWISE REDIRECT.
	if(empty($cats)){
		header("Location: index."._FEXT);
		exit;
	}

$limit = _BROWSER_PAGE;
$category_name = get_catname($cat_name);
$list_subcats = list_subcategories($cats, '');

} 
else {
	header('Location: '. _URL .'/404.php'); 
	exit();
}
$modframework->trigger_hook('category_mid_1');
// Default Page 
if (empty($page) || !is_numeric($page) || $page < 0 ) { $page = 1 ; }
if(is_numeric($cats)){
	
	$resync = 0;
	$category = array();
	$total_items = 0;
	
	$categories = load_categories(array('db_table' => 'pm_categories'));
	
	foreach ($categories as $c_id => $c)
	{
		if ($c_id == $cats)
		{
			$category = $c;
		}
	}

	$total_items = $category['published_videos'];
	
	if ($total_items < 10)
	{
		$resync = 1;
	}
	
	$total_pages = ceil($total_items / $limit);
	$set_limit = $page * $limit - ($limit);
	
	if ($total_items == 0) 
	{
			$problem = $lang['browse_msg2'];
	}
	else
	{
		if (isset($sortby) && isset($order)) 
		{
			$sql_sortby	= ($sql_sortby == '') ? "added" : $sql_sortby;
			$order		= ($order == '') ? "DESC" : $order;
		}
		
		if ($sortby == 'rating')
		{
			$sql = "SELECT pm_videos.uniq_id, COALESCE(pm_bin_rating_meta.score, 0) as score 
					FROM pm_videos 
					LEFT JOIN pm_bin_rating_meta ON (pm_videos.uniq_id = pm_bin_rating_meta.uniq_id) 
					WHERE added <= '". time() ."' 
					AND (category LIKE '%,$cats,%' 
						 OR category like '%,$cats' 
						 OR category like '$cats,%' 
						 OR category='$cats') 
					ORDER BY score DESC
					LIMIT $set_limit, $limit" ;

			$modframework->trigger_hook('category_mid_2');
			$result = mysql_query($sql);
			$uniq_ids = array();
			while ($row = mysql_fetch_array($result)) 
			{ 
				$uniq_ids[] = $row['uniq_id'];
			} 
			mysql_free_result($result);
			$modframework->trigger_hook('category_mid_3');
			$unsorted_list = get_video_list('', '', 0, $limit, 0, array(), $uniq_ids);
			$videos = array();
			$i = 0;
			foreach ($uniq_ids as $k => $uniq_id)
			{
				foreach ($unsorted_list as $k => $video_data)
				{
					if ($video_data['uniq_id'] == $uniq_id)
					{
						$videos[$i] = (array) $video_data;
						break;
					}
				}
				$i++;
			}
			unset($unsorted_list);
		}
		else
		{
			$videos = get_video_list($sql_sortby, $order, $set_limit, $limit, $cats);
		}
		
		if ($page == $total_pages && $total_items > 0 && $row_count == 0)
		{
			$resync = 1;
		}
		else if ($row_count == 0 && $page > 1 && $total_items > 0)
		{
			$resync = 1;
		}
		else if (($row_count + $set_limit > $total_items) && $total_items > 0)
		{
			$resync = 1;
		}
		else if (($row_count + $set_limit >= $total_items) && ($category['total_videos'] > $total_items))
		{
			$resync = 1;
		}
		else if (($row_count + $set_limit >= $category['published_videos'])  && $page == $total_pages)
		{
			$resync = 1;
		}
	}

	if ($resync)
	{
		$sql_t = "SELECT COUNT(*) as total FROM pm_videos WHERE (category LIKE '%,$cats,%' OR category LIKE '%,$cats' OR category LIKE '$cats,%' OR category='$cats')"; 
		$result_t = @mysql_query($sql_t);
		$row = @mysql_fetch_assoc($result_t);
		$total_items = $row['total'];
		@mysql_free_result($result_t);
		@mysql_query("UPDATE pm_categories SET total_videos = '". $total_items ."' WHERE id = '". $cats ."'");
		
		$sql_t = "SELECT COUNT(*) as total FROM pm_videos WHERE added <= '". time() ."' AND (category LIKE '%,$cats,%' OR category LIKE '%,$cats' OR category LIKE '$cats,%' OR category='$cats')"; 
		$result_t = @mysql_query($sql_t);
		$row = @mysql_fetch_assoc($result_t);
		$total_items = $row['total'];
		@mysql_free_result($result_t);
		@mysql_query("UPDATE pm_categories SET published_videos = '". $total_items ."' WHERE id = '". $cats ."'");
	}
$modframework->trigger_hook('category_mid_4');
}
else { 
	header("Location: index."._FEXT);
	exit();
}

// generate smart pagination
$pagination = '';
if ($total_items > $limit)
{
	$append_url = '';
	
	if ( ! _SEOMOD)
	{
		$append_url = '';
		$filename = 'category.php';
		if($cat_name != '')	$append_url .= 'cat='		. $cat_name;
		if($page != '')		$append_url .= '&page='	. $page;
		if($sortby != '')	$append_url .= '&sortby='	. $sortby;
		if($order != '')	$append_url .= '&order='	. $order;
	}
	else 
	{
		$filename = "browse-" . $cat_name . "-videos-" . $page . "-" . $sortby . ".html";
	}
	
	$pagination = generate_smart_pagination($page, $total_items, $limit, 1, $filename, $append_url, _SEOMOD);
}


$meta_title = $category['meta_title'];
if ($category['meta_title'] == '')
{
	if($page != 1 && is_numeric($page))
	{
		$meta_title = $category_name." ".$lang['videos']." - ".sprintf($lang['page_number'], $page);
	}
	else
	{
		$meta_title = $category_name." ".$lang['videos'];
	}
}
$meta_description = ($category['meta_description'] != '') ? $category['meta_description'] : $category_name." ".$lang['videos']." - "._SITENAME." - ". $page;
$meta_keywords = $category['meta_keywords'];

// MAKE SORT BY STICK
if(!empty($cats)) {
	$list_cats = list_categories(0, $cats);
	$smarty->assign('list_categories', $list_cats);
} else {
	$list_cats = list_categories(0, '');
	$smarty->assign('list_categories', $list_cats);
} 
if($config['show_tags'] == 1)
{
	$tag_cloud = tag_cloud(0, $config['tag_cloud_limit'], $config['shuffle_tags']);
	$smarty->assign('tags', $tag_cloud);
	$smarty->assign('show_tags', 1);
}
$smarty->assign('cat_id', $cats);
$smarty->assign('problem', $problem);
$smarty->assign('gv_category_name', $category_name);
$smarty->assign('gv_cat', $cat_name);
$smarty->assign('gv_pagenumber', $page);
$smarty->assign('gv_sortby', $sortby);
$smarty->assign('gv_category_description', $category['description']);

$smarty->assign('list_subcats', $list_subcats);
$smarty->assign('pagination', $pagination);

$smarty->assign('page_count_info', $page_count_info);
$smarty->assign('pag_left', $pag_left);
$smarty->assign('pag_right', $pag_right);

$smarty->assign('results', $videos);
// --- DEFAULT SYSTEM FILES - DO NOT REMOVE --- //
$smarty->assign('meta_title', $meta_title);
$smarty->assign('meta_keywords', $meta_keywords);
$smarty->assign('meta_description', $meta_description);
$smarty->assign('template_dir', $template_f);
$smarty->display('video-category.tpl');
?>