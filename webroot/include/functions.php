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

function db_connect(){
	global $db_host, $db_user, $db_pass, $db_name;
	$connection = @mysql_connect($db_host, $db_user, $db_pass);
	if(!$connection)  
		return false;
	$db = @mysql_select_db($db_name, $connection);
	@mysql_query('SET NAMES utf8');
	@mysql_query('SET CHARACTER SET utf8');
	@mysql_query('SET COLLATION_CONNECTION="utf8_general_ci"');
	@mysql_query("SET @@global.sql_mode='MYSQL40'");
	if(!$db) { return false; }
	return $connection;
}

function magicSlashes($strString){
	return secure_sql($strString);
}

function safename($title)
{
	// replaces every unwanted character form a string with a dash "-"
	$title = str_replace("&", "and", $title);
	$arrStupid = array('feat.', 'feat', '.com', '(tm)', ' ', '*', "'s", '"', ",", ":", ";", "@", "#", "(", ")", "?", "!", "_", "$", "+", "=", "|", "'", '/', "~", "`s", "`", "\\", "^", "[", "]", "{", "}", "<", ">", "%", "");
	
	$title = htmlentities($title);
	$title = preg_replace('/&([a-zA-Z])(.*?);/', '$1', $title); // get rid of html entities
	$title = strtolower("$title");
	$title = str_replace(".", "", $title);
	$title = str_replace($arrStupid, "-", $title);
	$flag = 1;
	while ($flag)
	{
		$newtitle = str_replace("--", "-", $title);
		if ($title != $newtitle)
		{
			$flag = 1;
		}
		else
			$flag = 0;
		$title = $newtitle;
	}
	$len = strlen($title);
	if ($title[$len - 1] == "-")
	{
		$title = substr($title, 0, $len - 1);
	}
	return $title;
}



function fewchars($s, $length) 
{
	$str_to_count = html_entity_decode($s);
	if (mb_strlen($str_to_count, "UTF-8" ) <= $length)
	{
		return $s;
	}
	$s2 = mb_substr($str_to_count, 0, $lenght - 3, "UTF-8")."...";

	return $s2;
} 

function smarty_fewchars($params, &$smarty)
{
	if (empty($params['s']))
		return '';
	if (empty($params['length']))
		$params['length'] = 255;

	//return fewchars($params['s'], $params['length']);
	if (mb_strlen($params['s'], "UTF-8" ) <= $params['length'])
	{
		return $params['s'];
	}
	return mb_substr($params['s'], 0, $params['length'] - 3, "UTF-8")."...";
}

function load_categories($args = array())
{
	global $_video_categories, $_article_categories;
	
	$defaults = array('db_table' => 'pm_categories',
					  'order_by' => 'position',
					  'sort' => 'ASC',
					  'columns' => '*'
					);
	
	$options = array_merge($defaults, $args);
	extract($options);
	
	if ($db_table == 'pm_categories' && is_array($_video_categories))
	{
		return $_video_categories;
	}
	else if ($db_table == 'art_categories' && is_array($_article_categories))
	{
		return $_article_categories;
	}	
	
	if ($columns != '*' && strpos($columns, 'id') === false)
	{
		$columns = 'id, '.$columns;
	}
	
	$sql = "SELECT $columns FROM $db_table 
			ORDER BY $order_by $sort";
	
	$result = mysql_query($sql);
	if ( ! $result)
	{
		return false;
	}
	
	$categories = array();
	
	while ($row = mysql_fetch_assoc($result))
	{
		$categories[$row['id']] = $row;
		
		if ($row['meta_tags'] != '')
		{
			$temp_arr = unserialize($row['meta_tags']);
			foreach ($temp_arr as $k => $v)
			{
				$categories[$row['id']][$k] = $v;
			}
		}
	}
	mysql_free_result($result);
	
	if ($db_table == 'pm_categories') 
	{
		$_video_categories = $categories;
	}
	else
	{
		$_article_categories = $categories;
	}
	
	return $categories;
}

function categories_dropdown_display_option($parent, &$all_children, $level = 0, $options)
{
	$output = '';
	
	if ( ! $parent)
		return '';
	
	$value_attr_db_col = ($options['value_attr_db_col'] == '') ? 'id' : $options['value_attr_db_col']; 
	
	$padding = str_repeat($options['spacer'], $level);

	$output .= '<option value="'. $parent[$value_attr_db_col] .'" ';
	if ( ! is_array($options['selected']))
	{
		$output .= ($options['selected'] == $parent[$value_attr_db_col]) ? 'selected="selected"' : '';
	}
	else
	{
		// multiple selected items for DDCL
		if (in_array($parent[$value_attr_db_col], $options['selected']))
		{
			$output .= 'selected="selected"';
		}
	}
	$output .= ($options['option_attr_id'] != '') ? ' id="'. $options['option_attr_id'] .'" ' : '';
	$output .= '>'. $padding .' '. htmlentities($parent['name'],ENT_COMPAT,'UTF-8') .'</option>';
	$output .= "\n";
	
	if (isset($all_children[$parent['id']]))
	{
		foreach ($all_children[$parent['id']] as $k => $child)
		{
			$output .= categories_dropdown_display_option($child, $all_children, $level+1, $options);
		}
		unset($all_children[$parent['id']]);
	}
	
	return $output;
	
}

function categories_dropdown($args = array())
{
	global $lang;
	
	$output = '';
	
	$defaults = array(
		'db_table' => 'pm_categories',
		'attr_name' => 'category',
		'attr_class' => 'category_dropdown',
		'attr_id' => 'category_dropdown',
		'other_attr' => '',
		'selected' => 0, 
		'select_all_option' => true,
		'first_option_text' => $lang['select'],
		'first_option_value' => '-1',
		'no_options_text' => 'No categories',
		'order_by' => 'position',
		'sort' => 'ASC',
		'parents_only' => false,
		'options_only' => false,
		'value_attr_db_col' => 'id',
		'spacer' => '&nbsp;&nbsp;&nbsp;',
		'option_attr_id' => ''
	);
	$empty = array();
	
	$options = array_merge($defaults, $args);
	extract($options);
	
	$parents = $parent_ids = $children = array();
	
	$categories = load_categories($options);
	$count = count($categories);

	if($count > 0)
	{
		foreach ($categories as $c_id => $c)
		{
			if ($c['parent_id'] == 0)
			{
				$parents[] = $c;
				$parent_ids[] = $c['id'];
			}
			else
			{
				$children[$c['parent_id']][] = $c;
			}
		}
	}
	
	if ( ! $options_only)
	{
		$output .= "<select name='$attr_name' id='$attr_id' class='$attr_class' $other_attr>\n";
	}
	
	if ($select_all_option)
	{
		$first_opt_selected = (empty($selected)) ? 'selected="selected"' : '';
		$output .= "<option value='$first_option_value' $first_opt_selected>$first_option_text</option>\n";
	}

	if ($count)
	{
		foreach ($parents as $k => $p)
		{
			if ($parents_only == true)
			{
				$output .= categories_dropdown_display_option($p, $empty, 0, $options);
			}
			else
			{
				$output .= categories_dropdown_display_option($p, $children, 0, $options);
			}
			
		}
	}
	
	if (count($children) > 0 && ( ! $parents_only))
	{
		foreach ($children as $parent_id => $orphans)
		{
			foreach ($orphans as $k => $orphan)
			{
				$output .= categories_dropdown_display_option($orphan, $empty, 0, $options);
			}
		}
	}
	
	if ( ! $options_only)
	{
		$output .= "</select>\n";
	}
	
	return $output;
}

function list_categories_display_item($item, &$all_children, $level = 0, $options)
{
	$li_class = $caturl = $output = $li_item = '';

	if ( ! $item)
		return;
	
	$padding = str_repeat($options['spacer'], $level);
		
	// href
	if(_SEOMOD == 1) 
	{
		$caturl = _URL."/browse-". $item['tag'] ."-videos-1-date.html";
	}
	else
	{
		$caturl = _URL."/category.php?cat=". $item['tag'];
	}
	
	$sub_cats = '';

	if (isset($all_children[$item['id']]) && ($level < $options['max_levels'] || $options['max_levels'] == 0))
	{
		$sub_cats .= "\n";
		
		foreach ($all_children[$item['id']] as $k => $child)
		{
			if ( ! isset($newlevel))
			{
				$newlevel = true;
				$li_class .= 'topcat';
				$subcats_ul_class = ($child['id'] == $options['selected'] || $options['expand_items'] == true) ? 'visible_li' : 'hidden_li';
				$sub_cats .= $padding."<ul class='".$subcats_ul_class."'>\n";
			}
			$sub_cats .= list_categories_display_item($child, $all_children, $level+1, $options);
		}
		unset($all_children[$item['id']]);
	}
	
	// li class
	if ($item['id'] == $options['selected'])
	{
		if ($item['parent_id'] == 0)
		{
			$li_class .= ' selectedcat';
		}
		else 
		{
			$li_class .= ' selectedsubcat';
		}
	}
	else 
	{
		$li_class .= '';
	}
	
	if ($options['selected_grandfather'] > 0)
	{
		if ($item['id'] == $options['selected_grandfather'])
		{
			if ($item['parent_id'] == 0)
			{
				$li_class .= ' selectedcat';
			}
			else 
			{
				$li_class .= ' selectedsubcat';
			}
		}
	}
		
	// li
	$output .= $padding .'<li class="'. $li_class .'"><a href="'. $caturl .'" class="'.$li_class.'">'. htmlentities($item['name'],ENT_COMPAT,'UTF-8') .'</a>';
	$output .= $sub_cats;
	
	if (isset($newlevel) && $newlevel)
	{
		$output .= $padding."</ul>\n";
	}
		
	$output .= $padding."</li>\n";
	
	return $output;
}

function list_categories($parent = 0, $selected = 0, $args = array()) // deprecated: $parent 
{
	$output = '';
	
	$defaults = array(
		'db_table' => 'pm_categories',
		'selected' => 0, 
		'order_by' => 'position',
		'sort' => 'ASC',
		'selected_grandfather' => 0, 
		'spacer' => "\t",
		'max_levels' => 1,
		'ul_wrapper' => true
	);
	
	$options = array_merge($defaults, $args);
	$options['selected'] = ( ! is_object($selected)) ? $selected : 0;
	extract($options);
	
	$parents = $parent_ids = $children = array();
	$categories = load_categories($options);
	
	foreach ($categories as $c_id => $c)
	{
		if ($c['parent_id'] == 0)
		{
			$parents[] = $c;
			$parent_ids[] = $c['id'];
		}
		else
		{
			$children[$c['parent_id']][] = $c;
		}
	}

	// find "grandfather" of selected child category
	if (count($parent_ids) > 0 && $selected > 0 && ( ! in_array($selected, $parent_ids)))
	{
		$options['selected_grandfather'] = $selected;

		$counter = 0;
		$exit_limit = count($parent_ids) * 3;
		while (( ! in_array($options['selected_grandfather'], $parent_ids)) && $counter < $exit_limit)
		{
			$find = $options['selected_grandfather'];
			foreach ($children as $pid => $children_arr)
			{
				$found = false;
			
				if (count($children_arr) > 0)
				{
					foreach ($children_arr as $k => $child)
					{
						if ($child['id'] == $find)
						{
							$found = true;
							$options['selected_grandfather'] = $child['parent_id'];
							break;
						}
					}
					if ($found)
					{
						break;
					}
				}
			}
			
			$counter++;
		}
	}
	
	foreach ($parents as $k => $p)
	{
		$options['expand_items'] = ($options['selected_grandfather'] == $p['id']) ? true : false;
		$output .= list_categories_display_item($p, $children, 0, $options);
	}

	if (count($children) > 0 && $options['max_levels'] == 0)
	{
		foreach ($children as $parent_id => $orphans)
		{
			foreach ($orphans as $k => $orphan)
			{
				$orphan['parent_id'] = 0;
				$output .= list_categories_display_item($orphan, $empty, 0, $options);
			}
		}
	}
	
	//	wrapper
	if ($ul_wrapper)
	{
		return "<ul id='ul_categories'>\n".$output."\n</ul>";
	}
	
	return $output;
}

function smarty_html_list_categories($params, &$smarty)
{
	$selected = ($params['selected']) ? $params['selected'] : 0;
	unset($params['selected']);
	return list_categories(0, $selected, $params);
}
function list_subcategories($parent = 0, $selected) 
{ 
	if (empty($parent))
	{
		return '';
	}

	$subcategories = '';
	$url = '';
	
	$categories = load_categories();
	
	foreach ($categories as $c_id => $c)
	{
		if ($c['parent_id'] == $parent)
		{
			if (_SEOMOD == 1)
			{
				$url = _URL."/browse-".$c['tag']."-videos-1-date.html";
			}
			else
			{
				$url = _URL."/category.php?cat=".$c['tag'];
			}
			$subcategories .= ($c['id'] == $selected) ? '<li class="selectedcat">' : '<li>';
			$subcategories .= '<a href="'. $url .'">'. $c['name'] .'</a>'; //('. $c['published_videos'] .')
			$subcategories .= '</li>';
		}
	}
	return $subcategories;
}

function get_all_children($parent_id, &$children, &$all_descendents)
{
	$all_descendents[] = $parent_id;
	if (isset($children[$parent_id]))
	{
		foreach ($children[$parent_id] as $k => $child)
		{
			get_all_children($child['id'], $children, $all_descendents);
		}
		unset($children[$parent_id]);
	}	
}

function top_videos($sortby = "views", $limit = 7)
{
	global $config;

	$sql	= '';
	$query	= '';

	if ('' == $sortby)
	{
		$mode = 'views';
	}
	if ('' == $limit)
	{
		$limit = 7;
	}
	
	switch ($sortby)
	{
		default:
		case 'views':
		
			$sql = "SELECT uniq_id  
					FROM pm_videos 
					WHERE added <= '". time() ."' 
					ORDER BY site_views DESC  
					LIMIT ".$limit;
		break;

		case 'rating':
			$sql = "SELECT uniq_id 
					FROM pm_bin_rating_meta 
					WHERE score > 0 
					ORDER BY score DESC
					LIMIT ".$limit;
		break;
		case 'chart':
			$sql = "SELECT uniq_id 
					FROM pm_chart  
					ORDER BY pm_chart.views DESC 
					LIMIT ".$limit;

		break;
	}
	
	$result = @mysql_query($sql);
	if ( ! $result)
	{
		return array();
	}
	
	$uniq_ids = array();
	while ($row = mysql_fetch_assoc($result))
	{
		$uniq_ids[] = $row['uniq_id'];
	}
	
	$unsorted_list = get_video_list('', '', 0, $limit, 0, array(), $uniq_ids);
	
	if (count($unsorted_list) == 0)
	{
		return array();
	}
	
	$i = 0;	
	$list = array();
	foreach ($uniq_ids as $k => $uniq_id)
	{
		foreach ($unsorted_list as $k => $video_data)
		{
			if ($video_data['uniq_id'] == $uniq_id)
			{
				$list[$i] = (array) $video_data;
				break;
			}
		}
		$i++;
	}
	
	return $list;

}

function request_video($unique_id = '', $page = "detail", $autoplay = false)
{
	global $config;
	
	$old_src_id = 0;
	$video 		= array();
	
	if($unique_id == '')
	{
		return 0;
	}
	
	if(ctype_alnum($unique_id) === false)
	{
		return 0;
	}
	
	$unique_id = secure_sql($unique_id);
	
	$sql = "SELECT pm_videos.*, pm_videos_urls.mp4, pm_videos_urls.direct 
			FROM pm_videos 
			LEFT JOIN pm_videos_urls 
				   ON (pm_videos.uniq_id = pm_videos_urls.uniq_id) 
			WHERE pm_videos.uniq_id = '". $unique_id ."'";

	$result =  @mysql_query($sql);
	if ( ! $result)
	{
		return 0;
	}
	
	if (mysql_num_rows($result) > 0)
	{
		$video = mysql_fetch_assoc($result);
	}
	else
	{
		return 0;
	}
	
	mysql_free_result($result);
	
	if (function_exists('bin_rating_get_item_meta'))
	{
		$rating_meta = bin_rating_get_item_meta($video['uniq_id']);
		$balance = bin_rating_calc_balance($rating_meta['up_vote_count'], $rating_meta['down_vote_count']);
		
		$video['up_vote_count'] = (int) $rating_meta['up_vote_count'];
		$video['likes'] = $video['up_vote_count'];
		$video['down_vote_count'] = (int) $rating_meta['down_vote_count'];
		$video['dislikes'] = $video['down_vote_count'];
		
		$video['up_vote_count_formatted'] = pm_number_format($video['up_vote_count']);
		$video['down_vote_count_formatted'] = pm_number_format($video['down_vote_count']);
		$video['up_vote_count_compact'] = pm_compact_number_format($video['up_vote_count']);
		$video['down_vote_count_compact'] = pm_compact_number_format($video['down_vote_count']);
		
		$video['likes_formatted'] = $video['up_vote_count_formatted'];
		$video['dislikes_formatted'] = $video['down_vote_count_formatted'];
		$video['likes_compact'] = $video['up_vote_count_compact'];
		$video['dislikes_compact'] = $video['down_vote_count_compact'];
		
		$video = array_merge($video, $balance);
	}

	$video['is_stream'] 	  = false;
	$video['video_title']	  = stripslashes($video['video_title']);
	$video['added_timestamp'] = $video['added'];
	$video['added']			  = time_since($video['added_timestamp']);
	$video['submitted']		  = ($video['submitted'] == 'bot') ? 'admin' : $video['submitted'];
	
	$video_sources = fetch_video_sources();
	
	$video['video_player']	= $config['video_player'];

	if (($video['source_id'] == 3) && ($config['video_player'] == 'jwplayer6')) 
	{
		$config['video_player'] = 'jwplayer6';
		$video['video_player'] = 'jwplayer6';
	}	
	elseif ($video['source_id'] == 3)
	{
		$config['video_player'] = 'jwplayer';
		$video['video_player'] = 'jwplayer';
	} 
	
	
	switch ($config['video_player'])
	{
		case 'jwplayer':
		case 'jwplayer6':
		case 'flvplayer':
	
			if ($video_sources[ $video['source_id'] ]['flv_player_support'] == 0 || 
				$video_sources[ $video['source_id'] ]['user_choice'] == 'embed')
			{
				$video['video_player']	= 'embed';
			}

		break;
		
		case 'embed':
			
			if ($video_sources[ $video['source_id'] ]['embed_player_support'] == 0)
			{
				$video['video_player']	= 'flvplayer';
			}
			
		break;
	}

	if ($video['source_id'] == 0)
	{
		$sql = "SELECT * 
				FROM pm_embed_code 
				WHERE uniq_id = '". $video['uniq_id'] ."'";

		$result = mysql_query($sql);
		$row = mysql_fetch_assoc($result);
		mysql_free_result($result);
		
		if (is_serialized($row['embed_code']))
		{
			if($config['video_player'] == 'jwplayer') {
				$video['video_player'] = 'jwplayer';
			} elseif($config['video_player'] == 'jwplayer6') {
				$video['video_player'] = 'jwplayer6';
			} else {
				$video['video_player'] = 'jwplayer';
			}
			$video['is_stream'] = true;
			$video['jw_flashvars'] = array();
			
			$jw_flashvars = unserialize($row['embed_code']);

			foreach ($jw_flashvars as $k => $v)
			{
				$video['jw_flashvars'][$k] = $v;
			}
			$pieces = explode(';', $video['url_flv'], 2);
			$video['jw_flashvars']['file'] = str_replace(array('?', '=', '&'), array('%3F', '%3D', '%26'), $pieces[0]);
			$video['jw_flashvars']['streamer'] = str_replace(array('?', '=', '&'), array('%3F', '%3D', '%26'), $pieces[1]);
		}
		else
		{
			$video['video_player'] = 'embed';
			$video['embed_code'] = $row['embed_code'];
		}
	}
	
	if ($video['source_id'] == 1 || $video['source_id'] == 2)
	{
		$tmp_parts = explode('.', $video['url_flv']);
		$ext = array_pop($tmp_parts);
		$ext = strtolower($ext);
		$old_src_id = $video['source_id'];
		switch ($ext)
		{
			case 'mov':
			case '3gp':
			case '3g2':
			case 'm4a':
			//case 'm4v':
				
				$video['video_player']  = 'embed';
				$video['source_id'] 	= $video_sources['quicktime']['source_id'];
				$video['url_flv'] = _URL .'/videos.php?vid='. $video['uniq_id'];
				
			break;
			
			case 'wmv':
			case 'asf':
			case 'wma':
				
				$video['video_player']  = 'embed';
				$video['source_id'] 	= $video_sources['windows media player']['source_id'];
				$video['url_flv'] = _URL .'/videos.php?vid='. $video['uniq_id'];
				//$video['url_flv'] = _VIDEOS_DIR . $video['url_flv'];
				
			break;
			
			case 'mp3':
				
				$video['video_player']  = 'embed';
				$video['source_id'] = $video_sources['mp3']['source_id'];
				$video['url_flv'] = _URL .'/videos.php?vid='. $video['uniq_id'];
			
			break;
			
			case 'mkv':
			case 'divx':
			case 'avi':
				
				$video['video_player']  = 'embed';
				$video['source_id'] 	= $video_sources['divx']['source_id'];
				$video['url_flv'] = _URL .'/videos.php?vid='. $video['uniq_id'];
				
			break;
		}
	}
	
	if ($video['source_id'] == $video_sources['mp3']['source_id'])
	{
		$video['url_flv'] = _URL .'/videos.php?vid='. $video['uniq_id'];
		$video['video_player']  = 'embed';
	}
	
	if ($video['source_id'] == 3 && $video['direct'] == '')
	{
		$video['direct'] = 'http://www.youtube.com/watch?v='. $video['yt_id'];
	}
	
	if ($video['yt_thumb'] != '')
	{
		$video['preview_image'] = show_thumb($video['uniq_id'], 1, $video);
		/*if (strpos($video['yt_thumb'], 'http') !== false)
		{
			$video['preview_image'] = $video['yt_thumb'];
			
		}
		else
		{
			$video['preview_image'] = _THUMBS_DIR . $video['yt_thumb'];
		}*/
	}
	
	if ($video['video_player'] == 'embed')
	{
		// EDITME temporary, since v2.0
		if ($video['source_id'] == $video_sources['bliptv']['source_id'] && ctype_digit($video['yt_id']) && $video['direct'] != '')  
		{
			// "in-line" updater for blip.tv videos to work with blip.tv's embed player; we need the embed ID. Messy but necessary.
			if ( ! defined('PHPMELODY'))
			{
				define('PHPMELODY', true);
			}
			include_once(ABSPATH .'admin/src/bliptv.php');
			
			$blip_direct_url = ($video['direct'] != '') ? $video['direct'] : 'http://blip.tv/file/'. $video['yt_id'];
			do_main($blip_video_details, $blip_direct_url);
			
			if ($blip_video_details['yt_id'] != '')
			{
				$video['yt_id'] = $blip_video_details['yt_id'];
				
				// quiet update
				$sql = "UPDATE pm_videos 
						SET yt_id = '". secure_sql($blip_video_details['yt_id']) ."'
						WHERE id = ". $video['id'];
				@mysql_query($sql);
			}
			
			unset($blip_video_details);
		}
		
		if ($video['source_id'] > 0)
		{
			$embed_code = $video_sources[ $video['source_id'] ]['embed_code'];
		}
		else
		{
			$embed_code = $video['embed_code'];
		}
		
		if ($video['source_id'] == $video_sources['sevenload']['source_id'] && strlen($video['yt_id']) > 7)
		{
			$video['yt_id'] = substr($video['yt_id'], 0, 7);
		}
		
		$embed_code = str_replace("%%yt_id%%", $video['yt_id'], $embed_code);
		$embed_code = str_replace("%%player_bgcolor%%", _BGCOLOR, $embed_code);
		$embed_code = str_replace("%%player_timecolor%%", _TIMECOLOR, $embed_code);
		$video['url_flv'] = str_replace("&", "&amp;", $video['url_flv']);
		$embed_code = str_replace("%%url_flv%%", $video['url_flv'], $embed_code);
		$embed_code = str_replace("%%direct%%", $video['direct'], $embed_code);
		$embed_code = str_replace("%%use_hq_vids%%", $config['use_hq_vids'], $embed_code);
		$embed_code = str_replace("%%yt_thumb%%", urlencode($video['preview_image']), $embed_code);
		
		
		if ($autoplay == true)	//	Override autoplay
		{
			$embed_code = str_replace("%%player_autoplay%%", '1', $embed_code);
		}
		else
		{
			if ($page == 'embed')
			{
				$embed_code = str_replace("%%player_autoplay%%", '0', $embed_code);
			}
			else
			{
				$embed_code = str_replace("%%player_autoplay%%", $config['player_autoplay'], $embed_code);
			}
		}
		
		if ($video['source_id'] == 17 && $video['direct'] != '')	//	trilulilu.ro
		{
			$temp = '';
			$temp = rtrim($video['direct'], "/");
			$temp = str_replace(array('http://', 'www.'), "", $temp);
			
			@preg_match('/^trilulilu\.ro\/(.*?)\/([a-zA-Z0-9]+)$/i', $temp, $matches);
			$embed_code = str_replace("%%username%%", $matches[1], $embed_code);
		}
		
		$embed_code = str_replace( array("\n", "\r", "'"), array(' ', ' ', '"'), $embed_code);
		
		switch ($page)
		{
			case 'index':
				
				$embed_code = str_replace("%%player_w%%", _PLAYER_W_INDEX, $embed_code);
				$embed_code = str_replace("%%player_h%%", _PLAYER_H_INDEX, $embed_code);
				$embed_code = str_replace("%%player_wmode%%", 'opaque', $embed_code);
				
			break;
			
			case 'favorites':
				
				$embed_code = str_replace("%%player_w%%", _PLAYER_W_FAVS, $embed_code);
				$embed_code = str_replace("%%player_h%%", _PLAYER_H_FAVS, $embed_code);
				$embed_code = str_replace("%%player_wmode%%", 'opaque', $embed_code);
				
			break;
			
			case 'embed':
				
				$embed_code = str_replace("%%player_w%%", _PLAYER_W_EMBED, $embed_code);
				$embed_code = str_replace("%%player_h%%", _PLAYER_H_EMBED, $embed_code);
				$embed_code = str_replace("%%player_wmode%%", 'opaque', $embed_code);
							
			break; 
			
			default:
			case 'detail':
			
				$embed_code = str_replace("%%player_w%%", _PLAYER_W, $embed_code);
				$embed_code = str_replace("%%player_h%%", _PLAYER_H, $embed_code);
				$embed_code = str_replace("%%player_wmode%%", 'opaque', $embed_code);
				
			break;
		}		
		
		$embed_code = str_replace("%%site_url%%", _URL, $embed_code);
		
		$video['embed_code'] = $embed_code;
		
		if ($old_src_id > 0)
		{
			$video['source_id'] = $old_src_id;
		}
	}
	if ($video['video_player'] == 'jwplayer6' && ! _SEOMOD)
	{
		if ($video['source_id'] == 1)
		{
			$video['url_flv'] = _VIDEOS_DIR . $video['url_flv'];
		}
	}
	else if ($video['video_player'] == 'jwplayer6' && _SEOMOD)
	{
		if ($video_sources[$video['source_id']]['flv_player_support'] == '1')
		{
			$video['url_flv'] = _URL .'/videos.flv?vid='. $video['uniq_id'];
		}
	}

	if ($video['video_player'] == 'jwplayer6' && $video['source_id'] == 0)
	{
		$rtmp_url = rtrim($video['jw_flashvars']['streamer'], '/');
		$rtmp_url .= '/'. $video['jw_flashvars']['file'];
		$video['jw_flashvars']['file'] = $rtmp_url; 
	}

	$video['site_views_formatted'] = pm_number_format($video['site_views']);
	
	
	$sql_date = date('Y-m-d', $video['added']);
	$date_diff = round( abs(strtotime(date('Y-m-d'))-strtotime($sql_date)) / 86400, 0 );
	
	$video['attr_alt'] = htmlspecialchars(stripslashes($video['video_title']));
	$video['excerpt'] = generate_excerpt($video['description'], 255);

	if ($date_diff < _ISNEW_DAYS)
	{
		$video['mark_new'] = true; 
	}
	
	if ($video['site_views'] > _ISPOPULAR)
	{
		$video['mark_popular'] = true;
	}
	
	$author_data = fetch_user_info($video['submitted']);
	
	$video['duration'] = sec2hms($video['yt_length']);
	$video['video_href'] = makevideolink($video['uniq_id'], $video['video_title'], $video['video_slug']);
	$video['thumb_img_url'] = $video['preview_image'];//show_thumb($video['uniq_id'], 1, $video);
	$video['author_username'] = $video['submitted'];
	$video['author_user_id'] = $author_data['id'];
	$video['author_power'] = $author_data['power'];
	$video['author_name'] = $author_data['name'];
	$video['author_avatar_url'] = $author_data['avatar_url'];
	$video['author_profile_href'] = ($video['submitted'] != 'bot') ? _URL .'/profile.'. _FEXT .'?u='. $video['submitted'] : '#';
	$video['html5_datetime'] = date('Y-m-d\TH:i:sO', $video['added_timestamp']); // ISO 8601
	$video['full_datetime'] = date('l, F j, Y g:i A', $video['added_timestamp']); 
	$video['time_since_added'] = $video['added']; //time_since($video['added_timestamp']);
	$video['views_compact'] = pm_compact_number_format($video['site_views']);
	$video['iso8601_duration'] = iso8601_duration($video['yt_length']); // ISO 8601
	$video['embed_href'] = generate_embed_code($video['uniq_id'], $video, false, 'link');
	//$video['comments'] = 0; // EDITME @todo
	//$video['comments_compact'] = pm_compact_number_format(0); // EDITME @todo
	
	if ($config['comment_system'] == 'off')
	{
		$video['allow_comments'] = 0;
	}
	
	return $video;
}

function update_view_count($video_id, $current_view_count = '', $mark_watched = true)
{
	global $config;
	$updated = false;
	$session_list = array();
	$cookie_list = array();

	$fn_encode = 'base64_encode'; //(function_exists('gzcompress')) ? 'gzcompress' : 'base64_encode';
	$fn_decode = 'base64_decode'; //(function_exists('gzuncompress')) ? 'gzuncompress' : 'base64_decode';

	if (pm_detect_crawler())
	{
		return false;
	}

	if ($_COOKIE['watched_video_list'])
	{
		$cookie_list = (array) explode(',', $fn_decode($_COOKIE['watched_video_list']));
	}

	if ( ! in_array('watched', $_SESSION))
	{
		$_SESSION['watched'] = '';
	}
	
	$session_list = (array) unserialize($_SESSION['watched']);

	$list = array_merge($cookie_list, $session_list);

	if ( ! in_array($video_id, $list))
	{
		$featured_treshold = (int) $config['auto_feature'];
		$sql_featured = ''; 
		if ($featured_treshold > 0 && ($current_view_count+1 == $featured_treshold))
		{
			$sql_featured = ", featured = '1' ";
		}
		
		$sql = "UPDATE pm_videos 
				SET site_views = site_views+1 ";
		$sql .= ($mark_watched) ? ", lastwatched = '". time() ."' " : '';	
		$sql .= $sql_featured ."  
				WHERE id = '". $video_id ."'";

		$result = @mysql_query($sql);
		$session_list[] = $cookie_list[] = $video_id;
		$_SESSION['watched'] = serialize($session_list);
		@setcookie('watched_video_list', $fn_encode(implode(',', $cookie_list)), time() + 86400, COOKIE_PATH); // keep for 24 hours
		$updated = true;
	}
	return $updated;
}

function get_video_list($orderby = '', $sort = '', $start = 0, $limit = 5, $category_id = 0, $video_ids = array(), $uniq_ids = array()) 
{
	$sql = "SELECT * FROM pm_videos 
			 WHERE added <= '". time() ."'";

	if ( ! $limit && (empty($category_id) && empty($video_ids) && empty($uniq_ids)))
	{	
		return array();
	}
	
	if (count($uniq_ids) > 0)
	{
		$sql_in = '';
		foreach ($uniq_ids as $k => $uid)
		{
			$sql_in .= "'$uid',";
		}
		$sql_in = rtrim($sql_in, ',');
		$sql .= ' AND uniq_id IN('. $sql_in .') ';
	}
	else if (count($video_ids) > 0)
	{
		$sql_in = '';
		foreach ($video_ids as $k => $vid)
		{
			$sql_in .= "'$vid',";
		}
		$sql_in = rtrim($sql_in, ',');
		$sql .= ' AND id IN('. $sql_in .') ';
	}
	else if ($category_id != 0)
	{
		$sql .= " AND (category LIKE '%,$category_id,%' or category like '%,$category_id' or category like '$category_id,%' or category='$category_id') ";
	}
	$sql .= ($orderby != '') ? " ORDER BY $orderby $sort " : '';
	$sql .= ($limit != '') ? " LIMIT $start, $limit " : '';	

	$list = array();
	$i = 0; 
	
	$result = mysql_query($sql);
	while ($row = mysql_fetch_assoc($result))
	{
		$list[$i] = $row;
			
		$sql_date = date('Y-m-d', $row['added']);
		$date_diff = round( abs(strtotime(date('Y-m-d'))-strtotime($sql_date)) / 86400, 0 );
		
		$list[$i]['attr_alt'] = htmlspecialchars(stripslashes($row['video_title']));
		$list[$i]['excerpt'] = generate_excerpt($row['description'], 255);

		if ($date_diff < _ISNEW_DAYS)
		{
			$list[$i]['mark_new'] = true; 
		}
		
		if ($row['site_views'] > _ISPOPULAR)
		{
			$list[$i]['mark_popular'] = true;
		}

		if (function_exists('bin_rating_get_item_meta'))
		{
			$rating_meta = bin_rating_get_item_meta($row['uniq_id']);
			$balance = bin_rating_calc_balance($rating_meta['up_vote_count'], $rating_meta['down_vote_count']);

			$list[$i]['up_vote_count'] = (int) $rating_meta['up_vote_count'];
			$list[$i]['likes'] = $list[$i]['up_vote_count'];
			$list[$i]['down_vote_count'] = (int) $rating_meta['down_vote_count'];
			$list[$i]['dislikes'] = $list[$i]['down_vote_count'];
			
			$list[$i]['up_vote_count_formatted'] = pm_number_format($list[$i]['up_vote_count']);
			$list[$i]['down_vote_count_formatted'] = pm_number_format($list[$i]['down_vote_count']);
			$list[$i]['up_vote_count_compact'] = pm_compact_number_format($list[$i]['up_vote_count']);
			$list[$i]['down_vote_count_compact'] = pm_compact_number_format($list[$i]['down_vote_count']);
			
			$list[$i]['likes_formatted'] = $list[$i]['up_vote_count_formatted'];
			$list[$i]['dislikes_formatted'] = $list[$i]['down_vote_count_formatted'];
			$list[$i]['likes_compact'] = $list[$i]['up_vote_count_compact'];
			$list[$i]['dislikes_compact'] = $list[$i]['down_vote_count_compact'];
			
			$list[$i] = array_merge($list[$i], $balance);
		}
		
		$author_data = fetch_user_info($row['submitted']);

		$list[$i]['duration'] = sec2hms($row['yt_length']);
		$list[$i]['video_href'] = makevideolink($row['uniq_id'], $row['video_title'], $row['video_slug']);
		$list[$i]['thumb_img_url'] = show_thumb($row['uniq_id'], 1, $row);
		$list[$i]['author_username'] = $row['submitted'];
		$list[$i]['author_user_id'] = $author_data['id'];
		$list[$i]['author_power'] = $author_data['power'];
		$list[$i]['author_name'] = $author_data['name'];
		$list[$i]['author_avatar_url'] = $author_data['avatar_url'];
		$list[$i]['author_profile_href'] = ($row['submitted'] != 'bot') ? _URL .'/profile.'. _FEXT .'?u='. $row['submitted'] : '#';
		
		$list[$i]['html5_datetime'] = date('Y-m-d\TH:i:sO', $row['added']); // ISO 8601
		$list[$i]['full_datetime'] = date('l, F j, Y g:i A', $row['added']); 
		$list[$i]['time_since_added'] = time_since($row['added']);
		$list[$i]['views_compact'] = pm_compact_number_format($row['site_views']);

		//$list[$i]['comments'] = 0; // EDITME @todo
		//$list[$i]['comments_compact'] = pm_compact_number_format(0); // EDITME @todo
		$i++;
	}
	
	mysql_free_result($result);
	
	return $list;
}

function get_related_video_list($category_id = 0, $video_title = '', $limit = 5, $video_id = 0)
{
	global $config;
	
	if ( ! $category_id && $video_title == '')
	{
		return get_video_list('', '', 0, $limit);
	}

	$ids = array();
	$categories = load_categories();
	$total_videos = (int) $categories[$category_id]['published_videos'];
	$video_id = (int) $video_id;

	if ($total_videos <= $limit)
	{
		$limit = $total_videos;
		$rand_from = 0;
	}
	else
	{
		$rand_from = abs(rand(0, $total_videos - $limit));
	}
	
	$sql = "SELECT id
			FROM pm_videos 
			WHERE MATCH (video_title) AGAINST ('". addslashes($video_title) ."')
			  AND added <= '". time() ."' 
			  AND id != $video_id
			LIMIT 0, $limit";

	$result = mysql_query($sql);

	if (mysql_num_rows($result) == 0) // backup method; just serve something
	{
		$sql = "SELECT id 
				FROM pm_videos 
				WHERE (category LIKE '%,$category_id,%' 
					 OR category like '%,$category_id' 
					 OR category like '$category_id,%' 
					 OR category='$category_id') 
				  AND added <= '". time() ."'
				  AND id != $video_id
				LIMIT $rand_from, $limit";
		$result = mysql_query($sql);
	}
	
	while ($row = mysql_fetch_assoc($result))
	{
		$ids[] = $row['id'];
	}

	mysql_free_result($result);
	$total_ids = count($ids);
	// fill it to the brim
	if ($total_ids > 0 && $total_ids < $limit)
	{

		$limit_left = $limit - $total_ids;
		
		$sql = "SELECT id 
				FROM pm_videos 
				WHERE (category LIKE '%,$category_id,%' 
					 OR category like '%,$category_id' 
					 OR category like '$category_id,%' 
					 OR category='$category_id') 
				  AND added <= '". time() ."'
				  AND id != $video_id
				LIMIT $rand_from, $limit_left";

		$result = mysql_query($sql);
		if (mysql_num_rows($result) > 0)
		{
			while ($row = mysql_fetch_assoc($result))
			{
				if ( ! in_array($row['id'], $ids))
				{
					$ids[] = $row['id'];
				}
			}
			mysql_free_result($result);
		}
	}

	$unsorted_list = get_video_list('', '', 0, $limit, 0, $ids);

	$list =  array();
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

	return $list;
}

function get_top_rated_video_list($category_id = 0,  $limit = 5, $video_id = 0)
{
	if ( ! $category)
	{
		return top_videos('rating', $limit);
	}
	$video_id = (int) $video_id;
	
	$sql = "SELECT pm_videos.id 
			FROM pm_bin_rating_meta 
			LEFT JOIN pm_videos ON (pm_bin_rating_meta.uniq_id = pm_videos.uniq_id) 
			WHERE (pm_videos.category LIKE '%,$category_id,%' 
					OR pm_videos.category LIKE '%,$category_id' 
					OR pm_videos.category LIKE '$category_id,%' 
					OR pm_videos.category LIKE '$category_id')
			AND pm_bin_rating_meta.score > 0
			ORDER BY pm_bin_rating_meta.score DESC
			LIMIT ". $limit;

	$result = mysql_query($sql);
	$total_items = mysql_num_rows($result);

	if ( ! $result || ! $total_items)
	{
		$sql = "SELECT id  
				FROM pm_videos 
				WHERE category = '".$category_id."'
				  AND added <= '". time() ."' 
				  AND id != $video_id 
				ORDER BY site_views DESC  
				LIMIT ".$limit;
		$result = mysql_query($sql);
	}
	
	while ($row = mysql_fetch_array($result))
	{
		$ids[] = $row['id'];
	}
	
	mysql_free_result($result);
	
	$unsorted_list = get_video_list('', '', 0, 0, 0, $ids);

	$list =  array();
	$i = 0; 
	foreach ($ids as $k => $id)
	{
		foreach ($unsorted_list as $k => $video_data)
		{
			if ($video_data['id'] == $id)
			{
				$list[$i] = (array) $video_data;
				break;
			}
		}
		$i++;
	}

	return $list;
}

function get_catid($tag) 
{
	$categories = load_categories();
	foreach ($categories as $c_id => $c)
	{
		if ($c['tag'] == $tag)
		{
			return $c_id;
		}
	}
	return false;
}
function get_catname($tag) 
{
	$categories = load_categories();
	foreach ($categories as $c_id => $c)
	{
		if ($c['tag'] == $tag)
		{
			return $c['name'];
		}
	}
	return '';
}


function getheaders($url,$format=0) {
   $url_info=parse_url($url);
   $port = isset($url_info['port']) ? $url_info['port'] : 80;
   $fp=fsockopen($url_info['host'], $port, $errno, $errstr, 30);
   if($fp) {
	   if(!$url_info['path']){
					 $url_info['path'] = "/";
				 }
				 if($url_info['path'] && !$url_info['host']){
					$url_info['host'] = $url_info['path'];
					$url_info['path'] = "/";
				 }
				 if( $url_info['host'][(strlen($url_info['host'])-1)] == "/" ){
					$url_info['host'][(strlen($url_info['host'])-1)] = "";
				 }
				 if(!$url_array[scheme]){
					 $url_array[scheme] = "http"; //we always use http links
					}
				 $head = "HEAD ".@$url_info['path'];
				 if( $url_info['query'] ){
					 $head .= "?".@$url_info['query'];
					}
	   $head .= " HTTP/1.0\r\nHost: ".@$url_info['host']."\r\n\r\n";
				 fputs($fp, $head);
	   while(!feof($fp)) {
		   if($header=trim(fgets($fp, 1024))) {
			   if($format == 1) {
				   $h2 = explode(':',$header);
				   // the first element is the http header type, such as HTTP/1.1 200 OK,
				   // it doesn't have a separate name, so we have to check for it.
				   if($h2[0] == $header) {
					   $headers['status'] = $header;
				   }
				   else {
					   $headers[strtolower($h2[0])] = trim($h2[1]);
				   }
			   }
			   else {
				   $headers[] = $header;
			   }
		   }
	   }
	   return $headers;
   }
   else {
	   return false;
   }
}

function make_youtube_player($y_id, $length, $width = '496', $height = '401', $auto_play = '1') {
	//	@deprecated
}

function make_youtube_player2($uniq_id, $name, $width = 496, $height = 401, $auto_play = 'false') {	
	//	@deprecated
}

function generate_embed_code($uniq_id = '', $video = array(), $append_backlink = true, $output_type = 'embed')
{
	global $config;
	$embed_code = '';
	$backlink 	= '';
	
	if ($uniq_id == '')
		return '';
	
	if ( ! is_array($video))
	{
		return '';
	}
	
	if (count($video) == 0)
	{
		$video = request_video($uniq_id, 'embed', true);
	}
	
	if ($output_type == 'embed')
	{
		$embed_code = "";
		$embed_code .= '<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000"';
		$embed_code .= ' codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,40,0"';
		$embed_code .= ' width="'. _PLAYER_W_EMBED .'" height="'. _PLAYER_H_EMBED . '"';
		$embed_code .= '>';
		$embed_code .= '<param name="allowFullScreen" value="true" />';
		$embed_code .= '<param name="allowScriptAccess" value="always" />';
		$embed_code .= '<param name="allowNetworking" value="all" />';
		$embed_code .= '<param name="bgcolor" value="#'. _BGCOLOR .'" />';
		
		if ($video['video_player'] == 'flvplayer')
		{
			// object params
			$embed_code .= '<param name="movie" value="'. _URL .'/fpembed-'. $video['uniq_id'] .'.swf" />';
	 
			// embed tag
			$embed_code .= '<embed src="'. _URL .'/fpembed-'. $video['uniq_id'] .'.swf';
			$embed_code .= '" width="'. _PLAYER_W_EMBED .'" height="'. _PLAYER_H_EMBED . '"';
			$embed_code .= ' bgcolor="#'. _BGCOLOR .'"';
			$embed_code .= ' type="application/x-shockwave-flash" allowFullScreen="true" allowScriptAccess="always" ';
			$embed_code .= ' allowNetworking="all" pluginspage="http://www.macromedia.com/go/getflashplayer" wmode="window">';
			$embed_code .= '</embed>';
			$embed_code .= '</object>';
		}
		else if ($video['video_player'] == 'jwplayer')
		{
			$jw_flashvars = '';
			if ($video['source_id'] == 3)
			{
				$jw_flashvars .= '&file='. urlencode($video['direct']);
				$jw_flashvars .= '&type=youtube';
			}
			else if ($video['source_id'] == 0)
			{
				$jw_flashvars .= '&file='. urlencode($video['jw_flashvars']['file']);
				$jw_flashvars .= '&streamer='. urlencode($video['jw_flashvars']['streamer']);
				$jw_flashvars .= ($video['jw_flashvars']['provider'] != '') ? '&provider='. $video['jw_flashvars']['provider'] : '';
				$jw_flashvars .= ($video['jw_flashvars']['startparam'] != '') ? '&http.startparam='. $video['jw_flashvars']['startparam'] : '';
				$jw_flashvars .= ($video['jw_flashvars']['loadbalance'] != '') ? '&rtmp.loadbalance='. $video['jw_flashvars']['loadbalance'] : '';
				$jw_flashvars .= ($video['jw_flashvars']['subscribe'] != '') ? '&rtmp.subscribe='. $video['jw_flashvars']['subscribe'] : '';
			}
			else
			{
				$jw_flashvars .= '&file='. urlencode(_URL ."/videos.php?vid=". $video['uniq_id']);
				$jw_flashvars .= '&type=video';
			}
			$jw_flashvars .= '&config='. urlencode(_URL ."/jwembed.xml");
			$jw_flashvars .= '&backcolor='. _BGCOLOR;
			$jw_flashvars .= '&frontcolor='. _TIMECOLOR;
			$jw_flashvars .= '&screencolor=000000';
			$jw_flashvars .= '&image='. urlencode( $video['preview_image'] ); 
			$jw_flashvars .= '&logo='. urlencode(_WATERMARKURL);
			$jw_flashvars .= '&link='. urlencode(_WATERMARKLINK);
			$jw_flashvars .= '&skin='. urlencode(_URL).'/skins/'._JWSKIN;
			$jw_flashvars .= '&bufferlength=5'; 
			$jw_flashvars .= '&plugins=timeslidertooltipplugin-2'; 
			
			// object params
			$embed_code .= '<param name="movie" value="'. _URL .'/jwplayer.swf" />';
			$embed_code .= '<param name="flashVars" value="'. $jw_flashvars .'" />';
			
			// embed tag
			$embed_code .= '<embed src="'. _URL .'/jwplayer.swf" ';
			$embed_code .= ' width="'. _PLAYER_W_EMBED .'" height="'. _PLAYER_H_EMBED . '"';
			$embed_code .= ' bgcolor="'. _BGCOLOR .'"';
			$embed_code .= ' type="application/x-shockwave-flash" allowFullScreen="true" ';
			$embed_code .= ' allowScriptAccess="always" wmode="window" ';
			$embed_code .= ' flashvars="'. $jw_flashvars .'">';
			$embed_code .= '</embed>';
			$embed_code .= '</object>';
		}
		else if ($video['video_player'] == 'jwplayer6')
		{
			$embed_code = '';

			$jw_file = $video['url_flv'];
			
			if ($video['source_id'] == 3)
			{
				$jw_file = $video['direct'];
			}
			else if ($video['source_id'] == 0)
			{
				$jw_file = $video['jw_flashvars']['file'];
			}
			else
			{
				if (_SEOMOD)
				{
					$jw_file = _URL ."/videos.flv?vid=". $video['uniq_id'];
				}
			}
					
			$embed_code .= '<div id="Playerholder"></div>';
			$embed_code .= '<script type="text/javascript" src="'. _URL .'/jwplayer.js"></script>';
			$embed_code .= '<script type="text/javascript">jwplayer.key="'.$config["jwplayerkey"].'";</script>';
			
			$rtmp = '';
			$rtmp .= ($video['jw_flashvars']['provider'] != '') ? " provider: '". $video['jw_flashvars']['provider'] ."', " : '';
			$rtmp .= ($video['jw_flashvars']['startparam'] != '') ? " startparam: '". $video['jw_flashvars']['startparam'] ."', " : '';
			$rtmp .= ($video['jw_flashvars']['loadbalance'] != '') ? " loadbalance: ". $video['jw_flashvars']['loadbalance'] .", " : '';
			$rtmp .= ($video['jw_flashvars']['subscribe'] != '') ? " subscribe: ". $video['jw_flashvars']['subscribe'] .", " : '';
			$rtmp .= ($video['jw_flashvars']['securetoken'] != '') ? " securetoken: '". $video['jw_flashvars']['securetoken'] ."', " : '';
			$rtmp = rtrim($rtmp, ',');
			$rtmp = ($rtmp != '') ? 'rtmp: { '. $rtmp .'}, ' : '';

			$embed_code .= '<script type="text/javascript">';
			$embed_code .= "
					var flashvars = {
						file : '". $jw_file ."',
						$rtmp
						primary: 'flash',
						width: '". _PLAYER_W_EMBED ."',
						height: '". _PLAYER_H_EMBED ."',
						image: '". $video['preview_image'] ."',
						logo: {file: '". _WATERMARKURL ."',link: '". _WATERMARKLINK ."'}
					};
					jwplayer('Playerholder').setup(flashvars);
				</script>";
			
			$embed_code = str_replace( array("\n", "\r", "\t"), "", $embed_code);
		}
		else
		{
			// overwrite
			$embed_code = $video['embed_code'];
			$embed_code = str_replace('width="'. _PLAYER_W .'"', 'width="'. _PLAYER_W_EMBED .'"', $embed_code);
			$embed_code = str_replace('height="'. _PLAYER_H .'"', 'height="'. _PLAYER_H_EMBED .'"', $embed_code);
		}
	}
	
	if ($output_type == 'iframe')
	{
		// new as of version 2.0
		if(_SEOMOD == 1)
		{
			$embed_code = '<iframe width="'. _PLAYER_W_EMBED .'" height="'. _PLAYER_H_EMBED . '" src="'. _URL .'/embed/'. $video['uniq_id'] .'" frameborder="0" allowfullscreen seamless></iframe>';
		} else {
			$embed_code = '<iframe width="'. _PLAYER_W_EMBED .'" height="'. _PLAYER_H_EMBED . '" src="'. _URL .'/embed.php?vid='. $video['uniq_id'] .'" frameborder="0" allowfullscreen seamless></iframe>';		
		}
	}
	
	if ($append_backlink)
	{
		$backlink = '<p><a href="'. makevideolink($video['uniq_id'], $video['video_title'], $video['video_slug']) .'">';
		$backlink .= $video['video_title'];
		$backlink .= '</a></p>';
	}
	if ($output_type == 'link')
	{
		if(_SEOMOD == 1)
		{
			$embed_code =  _URL .'/embed/'. $video['uniq_id'];
		} else {
			$embed_code =  _URL .'/embed.php?vid='. $video['uniq_id'];		
		}
	}
		
	$embed_code = str_replace( array("\n", "\r", "\t"), '', $embed_code);
	$embed_code .= ($append_backlink) ? $backlink : '';
	
	return $embed_code;
}

function make_voth() {

	global $config;
	
	if ($config['published_videos'] == 0)
	{
		return '';
	}
	
	$uniq_id = '';
	
	// get total number of featured videos
	$sql = "SELECT COUNT(*) as total 
			FROM pm_videos 
			WHERE featured = '1'
			AND added <= '". time() ."'";

	$result = mysql_query($sql);
	$row = mysql_fetch_assoc($result);
	$total_featured = (int) $row['total'];
	mysql_free_result($result);

	$found = false;
	$uniq_id = '';
	
	while ( ! $found )
	{
		if ($total_featured)
		{
			$rand_from = abs(rand(0, $total_featured - 1));
			$sql = "SELECT uniq_id 
					FROM pm_videos 
					WHERE featured = '1' 
					AND added <= '". time() ."' 
					LIMIT $rand_from, 1";
		}
		else
		{
			$rand_from = abs(rand(0, $config['published_videos'] - 1));
			
			$sql = "SELECT uniq_id 
					FROM pm_videos 
					WHERE added <= '". time() ."' 
					LIMIT $rand_from, 1";
		}

		$result =  mysql_query($sql);
		$row = mysql_fetch_assoc($result);
		mysql_free_result($result);
		
		if ($row['uniq_id'] != '')
		{
			$uniq_id = $row['uniq_id'];
			$found = true;
			break;
		}
	}

	return $uniq_id;
}
function show_more_featured($mvotd = '', $limit = 3) {
	//	todo
}
function show_voth_title() {	
	//	@deprecated
}

function return_bytes($val) 
{
    $val = trim($val);
    $last = strtolower($val[strlen($val)-1]);
    switch($last) {
    	default: 
			$val = (int) $val;
		break;
        case 'g':
            $val *= 1024;
        case 'm':
            $val *= 1024;
        case 'k':
            $val *= 1024;
    }
    return $val;
}

function readable_filesize($bytes)
{
	$filesizename = array(" Bytes", " KB", " MB", " GB", " TB", " PB", " EB", " ZB", " YB");
	return $bytes ? round($bytes/pow(1024, ($i = floor(log($bytes, 1024)))), 2) . $filesizename[$i] : '0 Bytes';
}

function today() {
	$thour=date('H');
	$tmin=date('i');
	$tsec=date('s');
	$tmonth=date('n');
	$tday=date('d');
	$tyear=date('Y');
	//convert it to unix timestamp
	$today=mktime($thour,$tmin,$tsec,$tmonth,$tday,$tyear);
	return $today;
}
function ezDate($d) {
	
		global $lang;
	$ts = time() - strtotime(str_replace("-","/",$d));
	
	if($ts > 31536000) 
	{
		$val = round($ts/31536000,0);
		$span = ($val == 1) ? 'year' : 'years';
	}
	elseif($ts > 2419200)
	{
		$val = round($ts/2419200,0);
		$span = ($val == 1) ? 'month' : 'months';
	}
	elseif($ts > 604800) 
	{
		$val = round($ts/604800,0);
		$span = ($val == 1) ? 'week' : 'weeks';
	}
	elseif($ts > 86400)
	{
		$val = round($ts/86400,0);
		$span = ($val == 1) ? 'day' : 'days';
	}
	elseif($ts > 3600)
	{
		$val = round($ts/3600,0);
		$span = ($val == 1) ? 'hour' : 'hours';
	}
	elseif($ts > 60)
	{
		$val = round($ts/60,0).' '.$lang['minute'];
		$span = ($val == 1) ? 'minute' : 'minutes';
	}
	else 
	{
		$val = $ts;
		$span = ($val == 1) ? 'second' : 'seconds';
	}
	$val .= ' '.$lang[$span];

	return $val;
}
function time_since($original, $long = false) {
	global $lang;
    // array of time period chunks
    $chunks = array(
        array(60 * 60 * 24 * 365 , $lang['year']),
        array(60 * 60 * 24 * 30 , $lang['month']),
        array(60 * 60 * 24 * 7, $lang['week']),
        array(60 * 60 * 24 , $lang['day']),
        array(60 * 60 , $lang['hour']),
        array(60 , $lang['minute']),
		array(1 , $lang['second'])
    );
    $chunks2 = array(
		$lang['years'],
		$lang['months'],
		$lang['weeks'],
		$lang['days'],
		$lang['hours'],
		$lang['minutes'],
		$lang['seconds'],
	);
	
    $since = time() - $original;
    
	$count = 0;
    for ($i = 0, $j = count($chunks); $i < $j; $i++) {
        $seconds = $chunks[$i][0];
        $name = $chunks[$i][1];
        $type = $chunks2[$i];
        // finding the biggest chunk (if the chunk fits, break)
        if (($count = floor($since / $seconds)) != 0) {
            break;
        }
    }
	
	$print = ($count == 1) ? '1 '.$name : $count." ".$type;
	
    if ($long)
	{
        if ($i + 1 < $j)
		{
			// now getting the second item
			$seconds2 = $chunks[$i + 1][0];
			$name2 = $chunks[$i + 1][1];
			$type2 = $chunks2[$i + 1];
		        	
			// add second item if it's greater than 0
			if ( ($count2 = floor( ($since - ($seconds * $count)) / $seconds2)) != 0)
			{
				$print .= ($count2 == 1) ? ', 1 '.$name2 : ", $count2 ".$type2;
			}
		}
	}
	
    return $print;
}

function sec2min($sec) 
{
    $minutes = intval($sec / 60); 
    $seconds = intval($sec % 60); 
	$time = $minutes." min. ".$seconds." sec.";
    return $time;
}

function sec2hms($sec, $padHours = false)
{
	$hms = "";
	$hours = intval(intval($sec) / 3600);
	if ($hours)
	{
		$hms .= ($padHours) ? str_pad($hours, 2, "0", STR_PAD_LEFT).':' : $hours.':';
	}
	$minutes = intval(($sec / 60) % 60);
	$hms .= str_pad($minutes, 2, "0", STR_PAD_LEFT).':';
	$seconds = intval($sec % 60);
	$hms .= str_pad($seconds, 2, "0", STR_PAD_LEFT);
	return $hms;
}

function is_real_email_address($email){
	$regex = "/^[a-zA-Z0-9._+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/i";
	if( ! @preg_match($regex, $email))
		return 0;	// not valid
	return 1;	// valid
}
function count_entries($table, $field, $field_equal) { // Usage 1 = table name 2 = field to search 3 = what to match

	if(!empty($field) && isset($field_equal)) {
	$query = @mysql_query("SELECT COUNT(*) as total FROM $table WHERE $field = '".$field_equal."'");
	} else {
	$query = @mysql_query("SELECT COUNT(*) as total FROM $table");
	}
	$result = @mysql_fetch_assoc($query);
	
	return $result['total'];
}

function send_a_mail($var_array, $destinationmail, $mailsubject, $template, $special_sender = '') {
	
	global $config;
	
	$mail = new PHPMailer();
	$mail->SetLanguage("en", "include/");
	if (_ISSMTP == 1) 
	{
		$mail->IsSMTP();
	}
	if ($config['mail_server'] != '')
	{
		$mail->Host 	= $config['mail_server'];
		$mail->SMTPAuth = true;
		$mail->Port 	= $config['mail_port'];
		$mail->Username = $config['mail_user'];
		$mail->Password = $config['mail_pass'];
		$mail->From 	= ('' != $special_sender) ? $special_sender : $config['contact_mail'];
		$mail->FromName = ('' != $special_sender) ? $special_sender : html_entity_decode(_SITENAME, ENT_QUOTES);
	}
	else if (defined('_MAIL_HOST'))
	{
		$mail->Host 	= _MAIL_HOST; 
		$mail->SMTPAuth = true;
		$mail->Port 	= _MAIL_PORT;
		$mail->Username = _MAIL_USER;
		$mail->Password = _MAIL_PASS;
		$mail->FromName = _MAIL_FROM_NAME;
		$mail->From 	= ('' != $special_sender) ? $special_sender : _MAIL_FROM;
	}
	$mail->CharSet = "UTF-8";
	$mail->AddAddress($destinationmail); 
	$mail->IsHTML(false);
	$mail->Subject = $mailsubject;
	
	// insert template 
	$filename = $template;
	$fd = fopen ($filename, "r");
	$mailcontent = fread ($fd, filesize ($filename));
	
	foreach ($var_array as $key=>$value)
	{
		$mailcontent = str_replace("%%$value[0]%%", $value[1],$mailcontent );
	}
	$mailcontent = stripslashes($mailcontent);
	fclose ($fd);
							
	$mail->Body=$mailcontent;
	if ( ! $mail->Send())
	{
		$result = $mail->ErrorInfo;
	}
	else 
	{
		$result = TRUE;
	}

	return $result;
}
// ** FOR REGISTRATION ** //
function prepare_for_mysql($value){
	return htmlspecialchars(magicSlashes(trim($value)));
}

function validate_email($email) {
	
	global $conn_id;
	
	if( empty($conn_id) ) {
		$conn_id = db_connect();
	}
	$rows = 0;
	if( !is_real_email_address($email))
		return 1;	// invalid characters;

	$sql = "SELECT email FROM pm_users WHERE email = '" . str_replace("\'", "''", $email) . "'";
	$result = @mysql_query($sql, $conn_id);
	if(!$result) { 
		return false;
	}
	$rows = @mysql_num_rows($result); 
	@mysql_free_result($result);
	if($rows > 0) 
		return 2;	// already in use
return false;
}

function sanitize_user($username, $strict = 0) 
{
	global $config;
	
	//$username = remove_accents( $username );
	$username = strip_tags($username);
	$username = str_replace(array(">", "<", "&", "'", '"', '*', '%'), '', $username);
	
	// Kill octets
	$username = preg_replace( '|%([a-fA-F0-9][a-fA-F0-9])|', '', $username );
	$username = preg_replace( '/&.+?;/', '', $username ); // Kill entities
	// If strict, reduce to ASCII, Cyrillic and Arabic characters for max portability.
	if ( $strict ) {
		$username = preg_replace ('|[^a-z\p{Arabic}\p{Cyrillic}0-9 _.\-@]|iu', '', $username);
	}
	
	$username = trim( $username );
	// Consolidate contiguous whitespace
	$username = preg_replace( '|\s+|', ' ', $username );
	$username = str_replace(" ", "", $username);
	
	return $username;
}

function sanitize_name($name, $strict = 0)
{
	global $config;
	
	$name = trim( $name );
	$name = strip_tags($name);
	$name = str_replace(array(">", "<", "&", "'", '"', '*', '%'), '', $name);
	
	// Kill octets
	$name = preg_replace( '|%([a-fA-F0-9][a-fA-F0-9])|', '', $name );
	$name = preg_replace( '/&.+?;/', '', $name ); // Kill entities
	// If strict, reduce to ASCII, Cyrillic and Arabic characters for max portability.
	if ( $strict ) {
		$name = preg_replace ('|[^a-z\p{Arabic}\p{Cyrillic}0-9_.\-@]|iu', '', $name);
	}

	return $name;
}

function seems_utf8($str) { // @used by WordPress
	$length = strlen($str);
	for ($i=0; $i < $length; $i++) {
		$c = ord($str[$i]);
		if ($c < 0x80) $n = 0; # 0bbbbbbb
		elseif (($c & 0xE0) == 0xC0) $n=1; # 110bbbbb
		elseif (($c & 0xF0) == 0xE0) $n=2; # 1110bbbb
		elseif (($c & 0xF8) == 0xF0) $n=3; # 11110bbb
		elseif (($c & 0xFC) == 0xF8) $n=4; # 111110bb
		elseif (($c & 0xFE) == 0xFC) $n=5; # 1111110b
		else return false; # Does not match any model
		for ($j=0; $j<$n; $j++) { # n bytes matching 10bbbbbb follow ?
			if ((++$i == $length) || ((ord($str[$i]) & 0xC0) != 0x80))
				return false;
		}
	}
	return true;
}
function remove_accents($string) { // @used by WordPress
	if ( !preg_match('/[\x80-\xff]/', $string) )
		return $string;

	if (seems_utf8($string)) {
		$chars = array(
		// Decompositions for Latin-1 Supplement
		chr(194).chr(170) => 'a', chr(194).chr(186) => 'o',
		chr(195).chr(128) => 'A', chr(195).chr(129) => 'A',
		chr(195).chr(130) => 'A', chr(195).chr(131) => 'A',
		chr(195).chr(132) => 'A', chr(195).chr(133) => 'A',
		chr(195).chr(134) => 'AE',chr(195).chr(135) => 'C',
		chr(195).chr(136) => 'E', chr(195).chr(137) => 'E',
		chr(195).chr(138) => 'E', chr(195).chr(139) => 'E',
		chr(195).chr(140) => 'I', chr(195).chr(141) => 'I',
		chr(195).chr(142) => 'I', chr(195).chr(143) => 'I',
		chr(195).chr(144) => 'D', chr(195).chr(145) => 'N',
		chr(195).chr(146) => 'O', chr(195).chr(147) => 'O',
		chr(195).chr(148) => 'O', chr(195).chr(149) => 'O',
		chr(195).chr(150) => 'O', chr(195).chr(153) => 'U',
		chr(195).chr(154) => 'U', chr(195).chr(155) => 'U',
		chr(195).chr(156) => 'U', chr(195).chr(157) => 'Y',
		chr(195).chr(158) => 'TH',chr(195).chr(159) => 's',
		chr(195).chr(160) => 'a', chr(195).chr(161) => 'a',
		chr(195).chr(162) => 'a', chr(195).chr(163) => 'a',
		chr(195).chr(164) => 'a', chr(195).chr(165) => 'a',
		chr(195).chr(166) => 'ae',chr(195).chr(167) => 'c',
		chr(195).chr(168) => 'e', chr(195).chr(169) => 'e',
		chr(195).chr(170) => 'e', chr(195).chr(171) => 'e',
		chr(195).chr(172) => 'i', chr(195).chr(173) => 'i',
		chr(195).chr(174) => 'i', chr(195).chr(175) => 'i',
		chr(195).chr(176) => 'd', chr(195).chr(177) => 'n',
		chr(195).chr(178) => 'o', chr(195).chr(179) => 'o',
		chr(195).chr(180) => 'o', chr(195).chr(181) => 'o',
		chr(195).chr(182) => 'o', chr(195).chr(184) => 'o',
		chr(195).chr(185) => 'u', chr(195).chr(186) => 'u',
		chr(195).chr(187) => 'u', chr(195).chr(188) => 'u',
		chr(195).chr(189) => 'y', chr(195).chr(190) => 'th',
		chr(195).chr(191) => 'y', chr(195).chr(152) => 'O',
		// Decompositions for Latin Extended-A
		chr(196).chr(128) => 'A', chr(196).chr(129) => 'a',
		chr(196).chr(130) => 'A', chr(196).chr(131) => 'a',
		chr(196).chr(132) => 'A', chr(196).chr(133) => 'a',
		chr(196).chr(134) => 'C', chr(196).chr(135) => 'c',
		chr(196).chr(136) => 'C', chr(196).chr(137) => 'c',
		chr(196).chr(138) => 'C', chr(196).chr(139) => 'c',
		chr(196).chr(140) => 'C', chr(196).chr(141) => 'c',
		chr(196).chr(142) => 'D', chr(196).chr(143) => 'd',
		chr(196).chr(144) => 'D', chr(196).chr(145) => 'd',
		chr(196).chr(146) => 'E', chr(196).chr(147) => 'e',
		chr(196).chr(148) => 'E', chr(196).chr(149) => 'e',
		chr(196).chr(150) => 'E', chr(196).chr(151) => 'e',
		chr(196).chr(152) => 'E', chr(196).chr(153) => 'e',
		chr(196).chr(154) => 'E', chr(196).chr(155) => 'e',
		chr(196).chr(156) => 'G', chr(196).chr(157) => 'g',
		chr(196).chr(158) => 'G', chr(196).chr(159) => 'g',
		chr(196).chr(160) => 'G', chr(196).chr(161) => 'g',
		chr(196).chr(162) => 'G', chr(196).chr(163) => 'g',
		chr(196).chr(164) => 'H', chr(196).chr(165) => 'h',
		chr(196).chr(166) => 'H', chr(196).chr(167) => 'h',
		chr(196).chr(168) => 'I', chr(196).chr(169) => 'i',
		chr(196).chr(170) => 'I', chr(196).chr(171) => 'i',
		chr(196).chr(172) => 'I', chr(196).chr(173) => 'i',
		chr(196).chr(174) => 'I', chr(196).chr(175) => 'i',
		chr(196).chr(176) => 'I', chr(196).chr(177) => 'i',
		chr(196).chr(178) => 'IJ',chr(196).chr(179) => 'ij',
		chr(196).chr(180) => 'J', chr(196).chr(181) => 'j',
		chr(196).chr(182) => 'K', chr(196).chr(183) => 'k',
		chr(196).chr(184) => 'k', chr(196).chr(185) => 'L',
		chr(196).chr(186) => 'l', chr(196).chr(187) => 'L',
		chr(196).chr(188) => 'l', chr(196).chr(189) => 'L',
		chr(196).chr(190) => 'l', chr(196).chr(191) => 'L',
		chr(197).chr(128) => 'l', chr(197).chr(129) => 'L',
		chr(197).chr(130) => 'l', chr(197).chr(131) => 'N',
		chr(197).chr(132) => 'n', chr(197).chr(133) => 'N',
		chr(197).chr(134) => 'n', chr(197).chr(135) => 'N',
		chr(197).chr(136) => 'n', chr(197).chr(137) => 'N',
		chr(197).chr(138) => 'n', chr(197).chr(139) => 'N',
		chr(197).chr(140) => 'O', chr(197).chr(141) => 'o',
		chr(197).chr(142) => 'O', chr(197).chr(143) => 'o',
		chr(197).chr(144) => 'O', chr(197).chr(145) => 'o',
		chr(197).chr(146) => 'OE',chr(197).chr(147) => 'oe',
		chr(197).chr(148) => 'R',chr(197).chr(149) => 'r',
		chr(197).chr(150) => 'R',chr(197).chr(151) => 'r',
		chr(197).chr(152) => 'R',chr(197).chr(153) => 'r',
		chr(197).chr(154) => 'S',chr(197).chr(155) => 's',
		chr(197).chr(156) => 'S',chr(197).chr(157) => 's',
		chr(197).chr(158) => 'S',chr(197).chr(159) => 's',
		chr(197).chr(160) => 'S', chr(197).chr(161) => 's',
		chr(197).chr(162) => 'T', chr(197).chr(163) => 't',
		chr(197).chr(164) => 'T', chr(197).chr(165) => 't',
		chr(197).chr(166) => 'T', chr(197).chr(167) => 't',
		chr(197).chr(168) => 'U', chr(197).chr(169) => 'u',
		chr(197).chr(170) => 'U', chr(197).chr(171) => 'u',
		chr(197).chr(172) => 'U', chr(197).chr(173) => 'u',
		chr(197).chr(174) => 'U', chr(197).chr(175) => 'u',
		chr(197).chr(176) => 'U', chr(197).chr(177) => 'u',
		chr(197).chr(178) => 'U', chr(197).chr(179) => 'u',
		chr(197).chr(180) => 'W', chr(197).chr(181) => 'w',
		chr(197).chr(182) => 'Y', chr(197).chr(183) => 'y',
		chr(197).chr(184) => 'Y', chr(197).chr(185) => 'Z',
		chr(197).chr(186) => 'z', chr(197).chr(187) => 'Z',
		chr(197).chr(188) => 'z', chr(197).chr(189) => 'Z',
		chr(197).chr(190) => 'z', chr(197).chr(191) => 's',
		// Decompositions for Latin Extended-B
		chr(200).chr(152) => 'S', chr(200).chr(153) => 's',
		chr(200).chr(154) => 'T', chr(200).chr(155) => 't',
		// Euro Sign
		chr(226).chr(130).chr(172) => 'E',
		// GBP (Pound) Sign
		chr(194).chr(163) => '',
		// Vowels with diacritic (Vietnamese)
		// unmarked
		chr(198).chr(160) => 'O', chr(198).chr(161) => 'o',
		chr(198).chr(175) => 'U', chr(198).chr(176) => 'u',
		// grave accent
		chr(225).chr(186).chr(166) => 'A', chr(225).chr(186).chr(167) => 'a',
		chr(225).chr(186).chr(176) => 'A', chr(225).chr(186).chr(177) => 'a',
		chr(225).chr(187).chr(128) => 'E', chr(225).chr(187).chr(129) => 'e',
		chr(225).chr(187).chr(146) => 'O', chr(225).chr(187).chr(147) => 'o',
		chr(225).chr(187).chr(156) => 'O', chr(225).chr(187).chr(157) => 'o',
		chr(225).chr(187).chr(170) => 'U', chr(225).chr(187).chr(171) => 'u',
		chr(225).chr(187).chr(178) => 'Y', chr(225).chr(187).chr(179) => 'y',
		// hook
		chr(225).chr(186).chr(162) => 'A', chr(225).chr(186).chr(163) => 'a',
		chr(225).chr(186).chr(168) => 'A', chr(225).chr(186).chr(169) => 'a',
		chr(225).chr(186).chr(178) => 'A', chr(225).chr(186).chr(179) => 'a',
		chr(225).chr(186).chr(186) => 'E', chr(225).chr(186).chr(187) => 'e',
		chr(225).chr(187).chr(130) => 'E', chr(225).chr(187).chr(131) => 'e',
		chr(225).chr(187).chr(136) => 'I', chr(225).chr(187).chr(137) => 'i',
		chr(225).chr(187).chr(142) => 'O', chr(225).chr(187).chr(143) => 'o',
		chr(225).chr(187).chr(148) => 'O', chr(225).chr(187).chr(149) => 'o',
		chr(225).chr(187).chr(158) => 'O', chr(225).chr(187).chr(159) => 'o',
		chr(225).chr(187).chr(166) => 'U', chr(225).chr(187).chr(167) => 'u',
		chr(225).chr(187).chr(172) => 'U', chr(225).chr(187).chr(173) => 'u',
		chr(225).chr(187).chr(182) => 'Y', chr(225).chr(187).chr(183) => 'y',
		// tilde
		chr(225).chr(186).chr(170) => 'A', chr(225).chr(186).chr(171) => 'a',
		chr(225).chr(186).chr(180) => 'A', chr(225).chr(186).chr(181) => 'a',
		chr(225).chr(186).chr(188) => 'E', chr(225).chr(186).chr(189) => 'e',
		chr(225).chr(187).chr(132) => 'E', chr(225).chr(187).chr(133) => 'e',
		chr(225).chr(187).chr(150) => 'O', chr(225).chr(187).chr(151) => 'o',
		chr(225).chr(187).chr(160) => 'O', chr(225).chr(187).chr(161) => 'o',
		chr(225).chr(187).chr(174) => 'U', chr(225).chr(187).chr(175) => 'u',
		chr(225).chr(187).chr(184) => 'Y', chr(225).chr(187).chr(185) => 'y',
		// acute accent
		chr(225).chr(186).chr(164) => 'A', chr(225).chr(186).chr(165) => 'a',
		chr(225).chr(186).chr(174) => 'A', chr(225).chr(186).chr(175) => 'a',
		chr(225).chr(186).chr(190) => 'E', chr(225).chr(186).chr(191) => 'e',
		chr(225).chr(187).chr(144) => 'O', chr(225).chr(187).chr(145) => 'o',
		chr(225).chr(187).chr(154) => 'O', chr(225).chr(187).chr(155) => 'o',
		chr(225).chr(187).chr(168) => 'U', chr(225).chr(187).chr(169) => 'u',
		// dot below
		chr(225).chr(186).chr(160) => 'A', chr(225).chr(186).chr(161) => 'a',
		chr(225).chr(186).chr(172) => 'A', chr(225).chr(186).chr(173) => 'a',
		chr(225).chr(186).chr(182) => 'A', chr(225).chr(186).chr(183) => 'a',
		chr(225).chr(186).chr(184) => 'E', chr(225).chr(186).chr(185) => 'e',
		chr(225).chr(187).chr(134) => 'E', chr(225).chr(187).chr(135) => 'e',
		chr(225).chr(187).chr(138) => 'I', chr(225).chr(187).chr(139) => 'i',
		chr(225).chr(187).chr(140) => 'O', chr(225).chr(187).chr(141) => 'o',
		chr(225).chr(187).chr(152) => 'O', chr(225).chr(187).chr(153) => 'o',
		chr(225).chr(187).chr(162) => 'O', chr(225).chr(187).chr(163) => 'o',
		chr(225).chr(187).chr(164) => 'U', chr(225).chr(187).chr(165) => 'u',
		chr(225).chr(187).chr(176) => 'U', chr(225).chr(187).chr(177) => 'u',
		chr(225).chr(187).chr(180) => 'Y', chr(225).chr(187).chr(181) => 'y',
		// Vowels with diacritic (Chinese, Hanyu Pinyin)
		chr(201).chr(145) => 'a',
		// macron
		chr(199).chr(149) => 'U', chr(199).chr(150) => 'u',
		// acute accent
		chr(199).chr(151) => 'U', chr(199).chr(152) => 'u',
		// caron
		chr(199).chr(141) => 'A', chr(199).chr(142) => 'a',
		chr(199).chr(143) => 'I', chr(199).chr(144) => 'i',
		chr(199).chr(145) => 'O', chr(199).chr(146) => 'o',
		chr(199).chr(147) => 'U', chr(199).chr(148) => 'u',
		chr(199).chr(153) => 'U', chr(199).chr(154) => 'u',
		// grave accent
		chr(199).chr(155) => 'U', chr(199).chr(156) => 'u',
		);

		$string = strtr($string, $chars);
	} else {
		// Assume ISO-8859-1 if not UTF-8
		$chars['in'] = chr(128).chr(131).chr(138).chr(142).chr(154).chr(158)
			.chr(159).chr(162).chr(165).chr(181).chr(192).chr(193).chr(194)
			.chr(195).chr(196).chr(197).chr(199).chr(200).chr(201).chr(202)
			.chr(203).chr(204).chr(205).chr(206).chr(207).chr(209).chr(210)
			.chr(211).chr(212).chr(213).chr(214).chr(216).chr(217).chr(218)
			.chr(219).chr(220).chr(221).chr(224).chr(225).chr(226).chr(227)
			.chr(228).chr(229).chr(231).chr(232).chr(233).chr(234).chr(235)
			.chr(236).chr(237).chr(238).chr(239).chr(241).chr(242).chr(243)
			.chr(244).chr(245).chr(246).chr(248).chr(249).chr(250).chr(251)
			.chr(252).chr(253).chr(255);

		$chars['out'] = "EfSZszYcYuAAAAAACEEEEIIIINOOOOOOUUUUYaaaaaaceeeeiiiinoooooouuuuyy";

		$string = strtr($string, $chars['in'], $chars['out']);
		$double_chars['in'] = array(chr(140), chr(156), chr(198), chr(208), chr(222), chr(223), chr(230), chr(240), chr(254));
		$double_chars['out'] = array('OE', 'oe', 'AE', 'DH', 'TH', 'ss', 'ae', 'dh', 'th');
		$string = str_replace($double_chars['in'], $double_chars['out'], $string);
	}

	return $string;
}

function check_username($username) {
	
	global $config;

	$username = trim($username);
	$username = str_replace(" ", "", $username);
	
	if(strlen($username) < 3)
	{
	 	return 1;
	}
	
	if ($config['allow_nonlatin_usernames'] == '0' && ! ctype_alnum($username)) 
	{
		return 2;
	}
	
	$sql = "SELECT COUNT(*) as total
			FROM pm_users 
			WHERE username = '". secure_sql($username) ."'";	
	if ( ! $result = mysql_query($sql))
	{
		return false;
	}
	$row = mysql_fetch_assoc($result);
	mysql_free_result($result);
	
	if ($row['total'] > 0)
	{
		return 3; // already in use
	}
	
	return false;	
}
// ** END FOR REGISTRATION ** //
function word_wrap_pass($message)
{
	$wrapAt = 45;
	$tempText = '';
	$finalText = '';
	$curCount = $tempCount = 0;
	$longestAmp = 9;
	$inTag = false;
	$ampText = '';
	$len = strlen($message);

	for ($num=0;$num<$len;$num++)
	{
		$curChar = $message{$num};

		if ($curChar == '<')
		{
			for ($snum=0;$snum<strlen($ampText);$snum++)
			{
				addWrap($ampText{$snum},$ampText{$snum+1},$wrapAt,$finalText,$tempText,$curCount,$tempCount);
			}
			$ampText = '';
			$tempText .= '<';
			$inTag = true;
		}
		elseif ($inTag && $curChar == '>')
		{
			$tempText .= '>';
			$inTag = false;
		}
		elseif ($inTag)
		{
			$tempText .= $curChar;
		}
		elseif ($curChar == '&')
		{
			for ($snum=0;$snum<strlen($ampText);$snum++)
			{
				addWrap($ampText{$snum},$ampText{$snum+1},$wrapAt,$finalText,$tempText,$curCount,$tempCount);
			}
			$ampText = '&';
		}
		elseif (strlen($ampText) < $longestAmp && $curChar == ';' && function_exists('html_entity_decode') &&
		       (strlen(html_entity_decode("$ampText;")) == 1 || preg_match('/^&#[0-9]+$/',$ampText)))
		{
			addWrap($ampText.';',$message{$num+1},$wrapAt,$finalText,$tempText,$curCount,$tempCount);
			$ampText = '';
		}
		elseif (strlen($ampText) >= $longestAmp || $curChar == ';')
		{
			for ($snum=0;$snum<strlen($ampText);$snum++)
			{
				addWrap($ampText{$snum},$ampText{$snum+1},$wrapAt,$finalText,$tempText,$curCount,$tempCount);
			}
			addWrap($curChar,$message{$num+1},$wrapAt,$finalText,$tempText,$curCount,$tempCount);
			$ampText = '';
		}
		elseif (strlen($ampText) != 0 && strlen($ampText) < $longestAmp)
		{
			$ampText .= $curChar;
		}
		else
		{
			addWrap($curChar,$message{$num+1},$wrapAt,$finalText,$tempText,$curCount,$tempCount);
		}
	}

	return $finalText . $tempText;
}

function addWrap($curChar,$nextChar,$maxChars,&$finalText,&$tempText,&$curCount,&$tempCount) {
	$wrapProhibitedChars = "([{!;,\\/:?}])";

	if ($curChar == ' ' || $curChar == "\n")
	{
		$finalText .= $tempText . $curChar;
		$tempText = '';
		$curCount = 0;
		$curChar = '';
	}
	elseif ($curCount >= $maxChars)
	{
		$finalText .= $tempText . ' ';
		$tempText = '';
		$curCount = 1;
	}
	else
	{
		$tempText .= $curChar;
		$curCount++;
	}

	// the following code takes care of (unicode) characters prohibiting non-mandatory breaks directly before them.

	// $curChar isn't a " " or "\n"
	if ($tempText != '' && $curChar != '')
	{
		$tempCount++;
	}
	// $curChar is " " or "\n", but $nextChar prohibits wrapping.
	elseif ( ($curCount == 1 && strstr($wrapProhibitedChars,$curChar) !== false) || 
	         ($curCount == 0 && $nextChar != '' && $nextChar != ' ' && $nextChar != "\n" && strstr($wrapProhibitedChars,$nextChar) !== false))
	{
		$tempCount++;
	}
	// $curChar and $nextChar aren't both either " " or "\n"
	elseif (!($curCount == 0 && ($nextChar == ' ' || $nextChar == "\n")))
	{
		$tempCount = 0;
	}

	if ($tempCount >= $maxChars && $tempText == '')
	{
		$finalText .= '&nbsp;';
		$tempCount = 1;
		$curCount = 2;
	}

	if ($tempText == ''  && $curCount > 0)
	{
		$finalText .= $curChar;
	}
}

function search_bad_words($input) {
	
	$filtered_input = $input;
	
	$phrases = explode("\r\n", file_get_contents('blacklist.txt'));
	
	if (count($phrases) > 0)
	{
		foreach ($phrases as $k => $phrase)
		{
			$filtered_input = str_ireplace($phrase, '', $filtered_input);
		}
	}

	return $filtered_input;
}
	

function RemoveCurseWords($original) 
{
	$patterns = explode("\r\n",file_get_contents(ABSPATH ."censor_words.txt"));
	$finalremove = $original;
	$piece_front = "";
	$piece_back = "";
	$piece_replace = "***";

    for ($x = 0; $x < count($patterns); $x++) 
	{
    	$safety=0; 
        while (@strstr(strtolower($finalremove), strtolower($patterns[$x]))) 
		{
	        $safety = $safety + 1;
	        if ($safety >= 100) { break; }
	
	        $occ = strpos(strtolower($finalremove), strtolower($patterns[$x]));
	        $piece_front = substr($finalremove, 0, $occ);
	        $piece_back = substr($finalremove, ($occ + strlen($patterns[$x])));
	        $finalremove = $piece_front . $piece_replace . $piece_back;
        }
    }
	return $finalremove;
}
$allowedTags = '<b><i><br>';
$stripAttrib = 'javascript:|onclick|ondblclick|onmousedown|onmouseup|onmouseover|'.
				'onblur|onchange|onfocus|onload|onsubmit|style|font|'.
               'onmousemove|onmouseout|onkeypress|onkeydown|onkeyup|object|object';
function removeEvilAttributes($tagSource)
{
   global $stripAttrib;
   return stripslashes(preg_replace("/$stripAttrib/i", 'forbidden', $tagSource));
}
function removeEvilTags($source)
{
   global $allowedTags;
   $source = RemoveCurseWords(strip_tags($source, $allowedTags));
   return preg_replace('/<(.*?)>/i', "'<'.removeEvilAttributes('\\1').'>'", $source);
}

function get_most_liked_comment($vid)
{
	return get_comment_list($vid, 1, true, 'score');
}

function get_comment_list($vid, $page =  1, $get_latest_single = false, $order_by = '', $limit = 10)
{
	global $config, $userdata;
	
	$limit = ($config['comments_page']) ? $config['comments_page'] : 10;	//	comments per page
	$from  = $page * $limit - ($limit);

	if ($get_latest_single)
	{
		$limit = 1;
		$from = 0;
	}

	if ($order_by == '')
	{
		$order_by = ($config['comment_default_sort'] == '') ? 'added' : $config['comment_default_sort'];
	}
	if ($order_by == 'score')
	{
		$order_by .= ' DESC, id ';
	}
	
	$sql = "   SELECT pm_comments.*, pm_users.name, pm_users.gender, pm_users.avatar, pm_users.power  
			     FROM pm_comments 
			LEFT JOIN pm_users ON (pm_comments.user_id = pm_users.id)
			WHERE pm_comments.uniq_id = '". $vid ."' 
			  AND pm_comments.approved = '1' 
			ORDER BY ". $order_by ." DESC  
			LIMIT ". $from .", ". $limit;
	if ( ! ($result = mysql_query($sql)))
	{
		return array();
	}
	
	$comment_list = array();
	while ($row = mysql_fetch_assoc($result)) 
	{
		$comment_list[] = $row;
	}
	mysql_free_result($result);
	
	if (count($comment_list) > 0)
	{
		foreach ($comment_list as $k => $c)
		{
			$banned = banlist($c['user_id']);
			$comment_list[$k]['user_is_banned'] = ($banned['user_id'] == $c['user_id']) ? true : false;
			
			$comment_list[$k]['user_profile_href'] = _URL .'/profile.'. _FEXT .'?u='. $c['username'];
			$comment_list[$k]['html5_datetime'] = date('Y-m-d\TH:i:sO', $c['added']); // ISO 8601
			$comment_list[$k]['full_datetime'] = date('l, F j, Y g:i A', $c['added']);
			$comment_list[$k]['time_since_added'] = time_since($c['added']);

			if ($c['user_id'] == 0)
			{
				$comment_list[$k]['name'] = $c['username'];
			}
			elseif ($c['name'] == '')
			{
				$comment_list[$k]['name'] = $c['username'];
			}
			
			$comment_list[$k]['power'] = $c['power'];

			$comment_list[$k]['avatar_url'] = get_avatar_url($c['avatar'], $c['user_id']);
			
			$comment_list[$k]['downvoted'] = false;
			if ($c['down_vote_count'] >= $config['comment_rating_hide_threshold'] && $c['score'] < 0)
			{
				$comment_list[$k]['downvoted'] = true;
			}
			
			// Has this user cast a vote on this comment?
			if (function_exists('bin_rating_user_has_voted'))
			{
				$current_vote_value = bin_rating_user_has_voted('com-'. $c['id']);
				if ($current_vote_value !== false)
				{
					$comment_list[$k]['user_likes_this'] = ($current_vote_value) ? true : false;
					$comment_list[$k]['user_dislikes_this'] = ($current_vote_value) ? false : true;
				}
			}
			
			// Has this user flagged this comment?
			$comment_list[$k]['user_flagged_this'] = user_has_flagged_comment($c['id']);
		}
		
		return $comment_list;
	}
	return array();
}

function generate_comment_pagination_object($uniq_id, $page = 1, $totalitems, $limit = 15, $adjacents = 1)
{
	global $lang, $config;
	
	if ( ! $uniq_id)
		return array();
	
	if ($limit == 0)
	{
		$limit = ($config['comments_page'] > 0) ? $config['comments_page'] : 10;
	}
	
	$counter 	= 0;
	$prev 	 	= $page - 1;
	$next 	 	= $page + 1;
	$lastpage	= ceil($totalitems / $limit);
	$lpm1 	 	= $lastpage - 1;
	
	$obj = array();

	//	Previous button
	if ($page > 1)
	{
		$obj[] = array('li' => array('class' => ''),
				       'a' =>  array('href' => '#',
				   				 'onclick' => 'ajax_request(\'comments\', \'&amp;do=show_comments&amp;page='. $prev .'&amp;vid='. $uniq_id .'\', \'#comment-list-container\', \'html\', true); return false;',
							   ),
				       'text' => '&laquo;'
			 	 	  );
	}
	else
	{
		$obj[] = array('li' => array('class' => 'disabled'),
				       'a' =>  array('href' => '#',
						 			'onclick' => 'return false;'
							   ),
				       'text' => '&laquo;'
			 		  );
	}
	
	if ($lastpage < 7 + ($adjacents * 2))	//not enough pages to bother breaking it up
	{	
		for ($counter = 1; $counter <= $lastpage; $counter++)
		{
			if ($counter == $page)
			{
				$obj[] = array('li' => array('class' => 'active'),
						       'a' =>  array('href' => '#',
						 			       'onclick' => 'return false;'
									   ),
						       'text' => $counter
						 	  );
			}
			else
			{
				$obj[] = array('li' => array('class' => ''),
						       'a' =>  array('href' => '#',
							 			   'onclick' => 'ajax_request(\'comments\', \'do=show_comments&amp;page='. $counter .'&amp;vid='. $uniq_id .'\', \'#comment-list-container\', \'html\', true); return false;'
									   ),
						       'text' => $counter
					  	 	  );
			}
		}

	}
	elseif($lastpage >= 7 + ($adjacents * 2))
	{
		//close to beginning; only hide later pages
		if($page < 2 + ($adjacents * 3))		
		{
			for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++)
			{
				if ($counter == $page)
				{
					$obj[] = array('li' => array('class' => 'active'),
							       'a' =>  array('href' => '#',
						 			  		   'onclick' => 'return false;'
										   ),
							       'text' => $counter
						     	  );
				}
				else
				{
					$obj[] = array('li' => array('class' => ''),
							       'a' =>  array('href' => '#',
								 			   'onclick' => 'ajax_request(\'comments\', \'do=show_comments&amp;page='. $counter .'&amp;vid='. $uniq_id .'\', \'#comment-list-container\', \'html\', true); return false;'
										   ),
							       'text' => $counter
						    	  );
				}
			}
			$obj[] = array('li' => array('class' => 'disabled'),
					       'a' =>  array('href' => '#',
						 			   'onclick' => 'return false;'
								   ),
					       'text' => '...'
				     	  );
			$obj[] = array('li' => array('class' => ''),
					       'a' =>  array('href' => '#',
						 			   'onclick' => 'ajax_request(\'comments\', \'do=show_comments&amp;page='. $lpm1 .'&amp;vid='. $uniq_id .'\', \'#comment-list-container\', \'html\', true); return false;'
								   ),
					       'text' => $lpm1
				     	  );
			$obj[] = array('li' => array('class' => ''),
					       'a' =>  array('href' => '#',
						 			   'onclick' => 'ajax_request(\'comments\', \'do=show_comments&amp;page='. $lastpage .'&amp;vid='. $uniq_id .'\', \'#comment-list-container\', \'html\', true); return false;'
								   ),
					       'text' => $lastpage
				    	  );
		}

		// in middle; hide some front and some back
		elseif($lastpage - ($adjacents * 2) > $page && $page > ($adjacents * 2))
		{
			$obj[] = array('li' => array('class' => ''),
					       'a' =>  array('href' => '#',
						 			   'onclick' => 'ajax_request(\'comments\', \'do=show_comments&amp;page=1&amp;vid='. $uniq_id .'\', \'#comment-list-container\', \'html\', true); return false;'
								   ),
					       'text' => '1'
				    	  );
			$obj[] = array('li' => array('class' => ''),
					       'a' =>  array('href' => '#',
						 			   'onclick' => 'ajax_request(\'comments\', \'do=show_comments&amp;page=2&amp;vid='. $uniq_id .'\', \'#comment-list-container\', \'html\', true); return false;'
								   ),
					       'text' => '2'
				    	  );
			$obj[] = array('li' => array('class' => 'disabled'),
					       'a' =>  array('href' => '#',
						 			   'onclick' => 'return false;'
								   ),
					       'text' => '...'
				    	  );

			for ($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++)
			{
				if ($counter == $page)
				{
					$obj[] = array('li' => array('class' => 'active'),
						  	       'a' =>  array('href' => '#',
						 			   		   'onclick' => 'return false;'
										   ),
							       'text' => $counter
						    	  );
				}
				else
				{
					$obj[] = array('li' => array('class' => ''),
							       'a' =>  array('href' => '#',
								 			   'onclick' => 'ajax_request(\'comments\', \'do=show_comments&amp;page='. $counter .'&amp;vid='. $uniq_id .'\', \'#comment-list-container\', \'html\', true); return false;'
										   ),
							       'text' => $counter
						    	  );
				}
			}
			$obj[] = array('li' => array('class' => 'disabled'),
					       'a' =>  array('href' => '#',
						 			   'onclick' => 'return false;'
								   ),
					       'text' => '...'
				     	  );
			$obj[] = array('li' => array('class' => ''),
					       'a' =>  array('href' => '#',
						 			   'onclick' => 'ajax_request(\'comments\', \'do=show_comments&amp;page='. $lpm1 .'&amp;vid='. $uniq_id .'\', \'#comment-list-container\', \'html\', true); return false;'
								   ),
					      'text' => $lpm1
				    	  );
			$obj[] = array('li' => array('class' => ''),
					       'a' =>  array('href' => '#',
						 			   'onclick' => 'ajax_request(\'comments\', \'do=show_comments&amp;page='. $lastpage .'&amp;vid='. $uniq_id .'\', \'#comment-list-container\', \'html\', true); return false;'
								   ),
					       'text' => $lastpage
				     	  );
		}
		//close to end; only hide early pages
		else
		{
			$obj[] = array('li' => array('class' => ''),
					       'a' =>  array('href' => '#',
						 			   'onclick' => 'ajax_request(\'comments\', \'do=show_comments&amp;page=1&amp;vid='. $uniq_id .'\', \'#comment-list-container\', \'html\', true); return false;'
								   ),
					       'text' => '1'
				     	  );
			$obj[] = array('li' => array('class' => ''),
					       'a' =>  array('href' => '#',
						 			   'onclick' => 'ajax_request(\'comments\', \'do=show_comments&amp;page=2&amp;vid='. $uniq_id .'\', \'#comment-list-container\', \'html\', true); return false;'
								   ),
					       'text' => '2'
				     	  );
			$obj[] = array('li' => array('class' => 'disabled'),
					       'a' =>  array('href' => '#',
						 			   'onclick' => 'return false;'
								   ),
					       'text' => '...'
				     	  );

			for ($counter = $lastpage - (1 + ($adjacents * 3)); $counter <= $lastpage; $counter++)
			{
				if ($counter == $page)
				{
					$obj[] = array('li' => array('class' => 'active'),
							       'a' =>  array('href' => '#',
						 			   	   	   'onclick' => 'return false;'
										   ),
							       'text' => $counter
						     	  );
				}
				else
				{
					$obj[] = array('li' => array('class' => ''),
							       'a' =>  array('href' => '#',
								 			   'onclick' => 'ajax_request(\'comments\', \'do=show_comments&amp;page='. $counter .'&amp;vid='. $uniq_id .'\', \'#comment-list-container\', \'html\', true); return false;'
										   ),
							       'text' => $counter
						    	  );
				}
			}
		}
	}

	//next button
	if ($page < $counter - 1) 
	{
		$obj[] = array('li' => array('class' => ''),
				       'a' =>  array('href' => '#',
					 			   'onclick' => 'ajax_request(\'comments\', \'do=show_comments&amp;page='. $next .'&amp;vid='. $uniq_id .'\', \'#comment-list-container\', \'html\', true); return false;'
							   ),
				       'text' => '&raquo;'
			     	  );
	}
	else
	{
		$obj[] = array('li' => array('class' => 'disabled'),
				       'a' =>  array('href' => '#',
						 		   'onclick' => 'return false;'
							   ),
				       'text' => '&raquo;'
			    	  );
	}
	
	return $obj;
}

function countryid2name($id) 
{
	global $_countries_list;
	
	if ( ! array_key_exists($id, $_countries_list))
	{
		$query = mysql_query("SELECT * FROM pm_countries WHERE countryid = '".$id."'");
		$result = mysql_fetch_array($query);
		return $result['country'];
	}

	return $_countries_list[$id];
}

function load_countries_list() 
{
	global $_countries_list;
	
	if (count($_countries_list) > 0)
	{
		return $_countries_list;
	}
	
	$sql = "SELECT * FROM pm_countries";
	$result = mysql_query($sql);
	
	if ( ! $result)
	{
		$_countries_list = array();
		return false;
	}
	
	while ($row = mysql_fetch_assoc($result))
	{
		$_countries_list[$row['countryid']] = $row['country'];
	}
	mysql_free_result($result);
	return true;
}

function make_cats($cat_ids) {
	
	$categories = load_categories(array('db_table' => 'pm_categories'));
	
	$selected = explode(',', $cat_ids);
	
	foreach ($selected as $k => $v)
	{
		$selected[$k] = (int) $v;
	}
	
	$links = '';
	
	foreach ($categories as $c_id => $c)
	{
		if (in_array($c_id, $selected))
		{
			if(_SEOMOD == 1)
			{
				$links .= "<a href=\""._URL."/browse-".$c['tag']."-videos-1-date.html\">".$c['name']."</a> ";
			}
			else
			{
				$links .= "<a href=\""._URL."/category.php?cat=".$c['tag']."\">".$c['name']."</a> ";
			}
		}
	}

	return $links;
}

function show_thumb($uniq_id, $t_id = 1, $video_data = false)
{
	if(_THUMB_FROM == 1) 	//	Outsource
	{
		if (is_array($video_data))
		{
			$r = $video_data;
		}
		else
		{
			$q = @mysql_query("SELECT yt_id, yt_thumb, source_id FROM pm_videos WHERE uniq_id = '".$uniq_id."'");
			$r = @mysql_fetch_array($q);
		}
		
		if(($r['source_id'] == 1) || ($r['yt_thumb'] != '' && strpos($r['yt_thumb'], "http://") === false))	//	thumbnail is hosted locally
		{
			if($r['source_id'] == 1 && $r['yt_thumb'] == '')
			{
				//	default thumbnail
				$thumb_url = _NOTHUMB;
			}
			elseif($r['yt_thumb'] != '' && strpos($r['yt_thumb'], "http://") === false)
			{
				if(!file_exists(_THUMBS_DIR_PATH . $r['yt_thumb']))
				{
					$thumb_url = _NOTHUMB;
				}
				else
				{
					$thumb_url = _THUMBS_DIR . $r['yt_thumb'];
				}
			}
			else
			{
				$thumb_url = $r['yt_thumb'];
			}
		}
		else
		{
			if($r['yt_thumb'] == '')
			{
				if($r['source_id'] == 3)
				{
					//	fix for videos imported in older versions using remote thumbails
					$thumb_url = 'http://img.youtube.com/vi/'. $r['yt_id'] .'/mqdefault.jpg';
				}
				else
				{
					$thumb_url = _NOTHUMB;
				}
			}
			else
			{
				$thumb_url = $r['yt_thumb'];
			}
		}
	}
	else 	//	Localhost
	{
		if ( ! file_exists(_THUMBS_DIR_PATH . $uniq_id .'-'. $t_id .'.jpg'))
		{
			$thumb_url = _NOTHUMB;
		}
		else
		{
			$thumb_url = _THUMBS_DIR . $uniq_id .'-'. $t_id .'.jpg';
		}
	}

	return $thumb_url;
}

function check_template($name) {
	// CHECK IF FOLDER EXISTS
	$template_dir = ABSPATH . "/templates/".$name;
	
	if(is_dir($template_dir)) {
	// CHECK FOR FILES
		$index = $template_dir."/index.tpl";
		$detail = $template_dir."/video-watch.tpl";
		$browse = $template_dir."/video-category.tpl";
		if(file_exists($index) && file_exists($detail) && file_exists($browse)) {
		return TRUE;		
		}
		else { 
		return FALSE;
		}
	} elseif(!is_dir($template_dir)) {
	return FALSE;
	}	
}

function dropdown_templates($current)
{
	$path = ABSPATH."/templates";
	$dh = @opendir($path);
	$form_file = '';
	if ($dh)
	{
		while ( ($file = readdir($dh)) !== false)
		{
			if ($file != "." && $file != "..")
			{
				
				if (check_template($file) && ($file == $current))
				{
					$form_file .= "<option value=\"".$file."\" selected=\"selected\">".$file."</option> \n";
				}
				elseif (check_template($file))
				{
					$form_file .= "<option value=\"".$file."\">".$file."</option> \n";
				}
			}
		}
		closedir($dh);
	}
	return $form_file;
}

function secure_sql($value)
{
	if( get_magic_quotes_gpc() )
	{
		$value = stripslashes( $value );
	}
	
	if( function_exists( "mysql_real_escape_string" ) )
	{
		$value = mysql_real_escape_string( $value );
	}
	else
	{
		$value = addslashes( $value );
	}
	return $value;
}

function specialchars( $text, $and_quotes = 0 ) {

   if( $and_quotes == 1 || $and_quotes === true )
   {
      $text = str_replace('"', '&quot;', $text);
      $text = str_replace("'", '&#039', $text);
   }
   $text = str_replace('<', '&lt;', $text);
   $text = str_replace('>', '&gt;', $text);
   return $text;
}

function make_link($type = '', $args = array())
{
	$url = _URL .'/';
	
	switch($type)
	{
		default:
		case 'index':
			$url .= 'index.'. _FEXT;
		break;
		
		case 'category':
		
			if (_SEOMOD)
			{
				$url .= 'browse-'. $args['tag'] .'-videos-'; 
				$url .= ($args['page'] != '') ? $args['page'] : '1';
				$url .= ($args['sortby'] != '') ? '-'. $args['sortby'] : '-date';
				$url .= '.html';
			}
			else
			{
				$url .= 'category.php?cat='. $args['tag'];
				$url .= ($args['page'] != '') ? '&page='. $args['page'] : '';
				$url .= ($args['sortby'] != '') ? '&sortby='. $args['sortby'] : '';
			}
			
		break;
		
		case 'memberlist':
			
			$url .= 'memberlist.'. _FEXT;
			$append = '';
			$append .= ($args['page'] != '') ? '&page='. $args['page'] : '';
			$append .= ($args['sortby'] != '') ? '&sortby='. $args['sortby'] : '';
			$append .= ($args['order'] != '') ? '&order='. $args['sortby'] : '';
			$append .= ($args['do'] != '') ? '&do='. $args['do'] : '';
			$append .= ($args['letter'] != '') ? '&letter='. $args['letter'] : '';
			$append = trim($append, '&');
			$url .= ($append != '') ? '?'. $append : '';
			
		break;
		
		case 'tag':
			
			if (_SEOMOD)
			{
				$url .= 'tags/'. $args['tag'];
				$url .= ($args['page'] != '') ? '/page-'. $args['page'] .'/' : '';
			}
			else
			{
				$url .= 'tag.php?t='. $args['tag'];
				$url .= ($args['page'] != '') ? '&page='. $args['page'] : '';
			}
			
		break;
		
		case 'search':

			$url .= 'search.php?keywords='. $args['keywords'];
			$url .= ($args['page'] != '') ? '&page='. $args['page'] : '';			
			$url .= ($args['t'] != '') ? '&t='. $args['t'] : '';
			$url .= ($args['append'] != '') ? $args['append'] : ''; // left here for other possible vars (i.e. analytics) 
			
		break;
		
		case 'newvideos':
			
			$url .= 'newvideos.'. _FEXT;
			$append = '';
			$append .= ($args['page'] != '') ? '&page='. $args['page'] : '';
			$append .= ($args['d'] != '') ? '&d='. $args['d'] : '';
			$append = trim($append, '&');
			$url .= ($append != '') ? '?'. $append : '';
			
		break;
		
		case 'topvideos':
			
			$url .= 'topvideos.'. _FEXT;
			$append = '';
			$append .= ($args['page'] != '') ? '&page='. $args['page'] : '';
			$append .= ($args['c'] != '') ? '&c='. $args['c'] : '';
			$append .= ($args['do'] != '') ? '&do='. $args['do'] : '';
			$append = trim($append, '&');
			$url .= ($append != '') ? '?'. $append : '';
			
		break;
	}
	
	return $url;
}

function makevideolink($uniq_id, $video_title = '', $video_slug = '')
{
	$r = array();
	$url_part = '';
	
	if (_SEOMOD == 1) 
	{
		if ('' != $video_slug)
		{
			$url_part = $video_slug;
		}
		else if ('' != $video_title)
		{
			$url_part = $video_title .'-video'; // pre-v2.1
		}
		else
		{
			$query 	= @mysql_query("SELECT video_title, video_slug FROM pm_videos WHERE uniq_id = '".$uniq_id."'");
			$r 		= mysql_fetch_array($query);
			mysql_free_result($query);

			if ($r['video_slug'] != '')
			{
				$url_part = $r['video_slug'];
			}
			else
			{
				$url_part = $r['video_title'] .'-video'; // pre-v2.1
			}
		}
		$video_title_clean = sanitize_title($url_part);
		
		$return = _URL .'/'. $video_title_clean .'_'. $uniq_id .'.html';
	} 
	else 
	{
		$return = _URL .'/watch.php?vid='.$uniq_id;
	}
	
	return $return;
}


// GET THE DOMAIN NAME
function get_dn($url) { 
	preg_match('@^(?:http://)?([^/]+)@i', $url, $matches);
	$host = $matches[1];
	// get last two segments of host name
	preg_match('/[^.]+\.[^.]+$/', $host, $matches);
	return $matches[0];
}

function retrieve_vid($url) {
	// establish the source: Youtube
	$source = get_dn($url);
	
	if($source == 'youtube.com') {
		$url = parse_url($url);
		$url = $url['query'];
		$url = explode('&', $url, 2);
		$vid = substr($url[0], 2);
	}

	return $vid;
}
function islive($last_seen_timestamp = 0) 
{
	global $time_now;

	if ( ! $last_seen_timestamp)
		return false;
	
	$time_now = ( ! $time_now) ? time() : $time_now; // speeds up the function

	$diff = 300; // 5 minutes 

	return ( ($time_now - $last_seen_timestamp) <= $diff) ? true : false;
}
function videosplaying($limit = 4) 
{
	$time_diff = time();
	$time_diff -= (3 * 60);
	$sql = "  SELECT id FROM pm_videos 
			   WHERE lastwatched > '".$time_diff."' 
			ORDER BY lastwatched DESC 
			   LIMIT $limit";

	if ( ! ($result = mysql_query($sql)))
	{
		return 0;
	}
	
	$num_rows = mysql_num_rows($result);

	if ($num_rows)
	{
		$ids = array();
		while($row = mysql_fetch_array($result)) 
		{
			$ids[] = $row['id'];
		}
		mysql_free_result($result);

		return get_video_list('', '', 0, $limit, 0, $ids);
	}
	
	return 0;
}
function generate_smart_pagination($page = 1, $totalitems, $limit = 15, $adjacents = 1, $targetpage = "/", $pagestring = "&page=", $seomod = 0)
{
	global $lang;

	if(!$adjacents) $adjacents = 1;
	if(!$limit) $limit = 15;
	if(!$page) $page = 1;
	if(!$targetpage) $targetpage = "/";
	
	$prev = $page - 1;
	$next = $page + 1;
	$lastpage = ceil($totalitems / $limit);
	$lpm1 = $lastpage - 1;
	
	$seo_url_regex = '/(index|browse-|topvideos|newvideos|memberlist|search|tag|tags\/)(.*?)([^(\.\/\?|\&|$)]*)/';
	
	if($seomod == 1)
	{
		@preg_match($seo_url_regex, $targetpage, $matches);

		$make_link_args = array();
		
		switch($matches[1])
		{
			case 'browse-':
				$type = 'category';
				preg_match('/browse-(.*?)-videos-([0-9]*)-(.*?)\./', $targetpage, $m);
				$tag = $m[1];
				$sortby = $m[3];
			break;
			
			case 'index':
			case 'topvideos':
			case 'memberlist':
				
				$type = $matches[1];
				parse_str($pagestring, $param);
				
				if (is_array($param))
				{
					foreach ($param as $k => $v)
					{
						$$k = $v;
					}
				}
				
			break;
			
			case 'search':
				
				$type = $matches[1];
					
				parse_str($pagestring, $param);
				
				$keywords = $param['keywords'];
				$t = $param['t'];
				$append = '';
				
				if (is_array($param))
				{
					$append = '';
					foreach ($param as $k => $v)
					{
						if ($k != 't' && $k != 'keywords')
						{
							$append .= '&'. $k .'='. $v;
						}
					}
				}
				
			break;
			
			case 'tag':
			case 'tags':
				
				$type = 'tag';
				if (preg_match('/tags\/(.*?)\//', $targetpage, $m))
				{
					$tag = $m[1];
				}
				else
				{
					parse_str($pagestring, $param);
					$tag = $param['t'];
				}
				
			break;
		}

		$make_link_args =  array('sortby' => $sortby,
							 	 'tag' => $tag,
							 	 'order' => $order,
							 	 'do' => $do,
							 	 'letter' => $letter,
							 	 'keywords' => $keywords,
								 'append' => $append,
							 	 'd' => $d,
							 	 'c' => $c,
								 't' => $t
								);

		$pagestring1	= make_link($type, array_merge($make_link_args, array('page' => 1)));
		$pagestring2	= make_link($type, array_merge($make_link_args, array('page' => 2)));
		$pagestringlpm1	= make_link($type, array_merge($make_link_args, array('page' => $lpm1)));
		$pagestringlast	= make_link($type, array_merge($make_link_args, array('page' => $lastpage)));
	}
	else
	{
		if(strpos($pagestring, 'page=', 0) === FALSE)
			$pagestring .= "&page=";
		
		$pagestring1 = preg_replace('/page=([0-9]*)/', 'page=1', $pagestring);
		$pagestring2 = preg_replace('/page=([0-9]*)/', 'page=2', $pagestring);
		$pagestringlpm1 = preg_replace('/page=([0-9]*)/', 'page='.$lpm1, $pagestring);
		$pagestringlast = preg_replace('/page=([0-9]*)/', 'page='.$lastpage, $pagestring);
	}	
	
	$obj = array();

	if($lastpage > 1)
	{
		//previous button
		if ($page > 1)
		{
			if($seomod == 1)
			{
				$url_query = make_link($type,  array_merge($make_link_args, array('page' => $prev)));
				$obj[] = array('li' => array('class' => ''),
							   'a' =>  array('href' => $url_query
										   ),
							   'text' => '&laquo;'
						 );
			}
			else
			{
				$url_query = preg_replace('/page=([0-9]*)/', 'page='.$prev, $pagestring);
				$obj[] = array('li' => array('class' => ''),
							   'a' =>  array('href' => $targetpage .'?'. $url_query
										   ),
							   'text' => '&laquo;'
						      );
			}
		}
		else
		{
			$obj[] = array('li' => array('class' => 'disabled'),
						   'a' =>  array('href' => '#',
								 		 'onclick' => 'return false;'
									),
						   'text' => '&laquo;'
						  );
		}
		
		//pages	
		if ($lastpage < 7 + ($adjacents * 2))	//not enough pages to bother breaking it up
		{	
			for ($counter = 1; $counter <= $lastpage; $counter++)
			{
				if ($counter == $page)
				{
					$obj[] = array('li' => array('class' => 'active'),
								   'a' =>  array('href' => '#',
										 		 'onclick' => 'return false;'
											),
								   'text' => $counter
							 	  );
				}
				else
				{
					if($seomod == 1)
					{
						//$url_query = preg_replace('/browse-(.*?)-videos-([0-9]*)-/', 'browse-$1-videos-'.$counter.'-', $targetpage);
						$url_query = make_link($type,  array_merge($make_link_args, array('page' => $counter))); 
						
						$obj[] = array('li' => array('class' => ''),
									   'a' =>  array('href' => $url_query
												),
									   'text' => $counter
									  );
					}
					else
					{
						$url_query = preg_replace('/page=([0-9]*)/', 'page='.$counter, $pagestring);
						
						$obj[] = array('li' => array('class' => ''),
									   'a' =>  array('href' => $targetpage .'?'. $url_query
												),
									   'text' => $counter
							     	  );
					}
				}					
			}
		}
		elseif($lastpage >= 7 + ($adjacents * 2))	//enough pages to hide some
		{
			//close to beginning; only hide later pages
			if($page < 2 + ($adjacents * 3))		
			{
				for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++)
				{
					if ($counter == $page)
					{
						$obj[] = array('li' => array('class' => 'active'),
									   'a' =>  array('href' => '#',
										 			 'onclick' => 'return false;'
												),
									   'text' => $counter
									   );
					}
					else
					{
						if($seomod == 1)
						{
							//$url_query = preg_replace('/browse-(.*?)-videos-([0-9]*)-/', 'browse-$1-videos-'.$counter.'-', $targetpage);
							$url_query = make_link($type,  array_merge($make_link_args, array('page' => $counter))); 
							$obj[] = array('li' => array('class' => ''),
										   'a' =>  array('href' => $url_query
													),
										   'text' => $counter
										  );
						}
						else
						{
							$url_query = preg_replace('/page=([0-9]*)/', 'page='.$counter, $pagestring);
							$obj[] = array('li' => array('class' => ''),
										   'a' =>  array('href' => $targetpage .'?'. $url_query
													),
										   'text' => $counter
										  );
						}
					}				
				}

				$obj[] = array('li' => array('class' => 'disabled'),
							   'a' =>  array('href' => '#',
								 			 'onclick' => 'return false;'
										),
							   'text' => '...'
						 	  );
				if($seomod == 1)
				{					
					$obj[] = array('li' => array('class' => ''),
								   'a' =>  array('href' => $pagestringlpm1
											),
								   'text' => $lpm1
								   );
						$obj[] = array('li' => array('class' => ''),
									   'a' =>  array('href' => $pagestringlast
											),
									   'text' => $lastpage
								      );
				}
				else
				{
					$obj[] = array('li' => array('class' => ''),
								   'a' =>  array('href' => $targetpage .'?'. $pagestringlpm1
											),
								   'text' => $lpm1
								  );
					$obj[] = array('li' => array('class' => ''),
								   'a' =>  array('href' => $targetpage .'?'. $pagestringlast
										),
								   'text' => $lastpage
							      );
				}
			}
			//in middle; hide some front and some back
			elseif($lastpage - ($adjacents * 2) > $page && $page > ($adjacents * 2))
			{
				if($seomod == 1)
				{					
					$obj[] = array('li' => array('class' => ''),
								   'a' =>  array('href' => $pagestring1
											),
								   'text' => '1'
								  );
						$obj[] = array('li' => array('class' => ''),
									   'a' =>  array('href' => $pagestring2
											),
									   'text' => '2'
								      );		
				}
				else
				{
					$obj[] = array('li' => array('class' => ''),
								   'a' =>  array('href' => $targetpage .'?'. $pagestring1
											),
								   'text' => '1'
								  );
						$obj[] = array('li' => array('class' => ''),
									   'a' =>  array('href' => $targetpage .'?'. $pagestring2
											),
									   'text' => '2'
								      );	
								  
				}				

				$obj[] = array('li' => array('class' => 'disabled'),
							   'a' =>  array('href' => '#',
								 			 'onclick' => 'return false;'
										),
							   'text' => '...'
							  );
							  
				for ($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++)
				{
					if ($counter == $page)
					{
						$obj[] = array('li' => array('class' => 'active'),
									   'a' =>  array('href' => '#',
											 		 'onclick' => 'return false;'
												),
									   'text' => $counter
									  );
					}
					else
					{
						if($seomod == 1)
						{
							$url_query = make_link($type,  array_merge($make_link_args, array('page' => $counter)));
							
							$obj[] = array('li' => array('class' => ''),
										   'a' =>  array('href' => $url_query
												),
										   'text' => $counter
									      );
						}
						else
						{
							$url_query = preg_replace('/page=([0-9]*)/', 'page='.$counter, $pagestring);
							
							$obj[] = array('li' => array('class' => ''),
										   'a' =>  array('href' => $targetpage .'?'. $url_query
												),
										   'text' => $counter
									      );
						}						
					}
				}

				$obj[] = array('li' => array('class' => 'disabled'),
							   'a' =>  array('href' => '#',
								 			 'onclick' => 'return false;'
										),
							   'text' => '...'
							  );
							  
				if($seomod == 1)
				{					
					$obj[] = array('li' => array('class' => ''),
								   'a' =>  array('href' => $pagestringlpm1
											),
								   'text' => $lpm1
								  );
					$obj[] = array('li' => array('class' => ''),
								   'a' =>  array('href' => $pagestringlast
										),
								   'text' => $lastpage
							      );
				}
				else
				{
					$obj[] = array('li' => array('class' => ''),
								   'a' =>  array('href' => $targetpage .'?'. $pagestringlpm1
											),
								   'text' => $lpm1
								  );
					$obj[] = array('li' => array('class' => ''),
								   'a' =>  array('href' => $targetpage .'?'. $pagestringlast
										),
								   'text' => $lastpage
							      );		
				}					
			}
			//close to end; only hide early pages
			else
			{
				if($seomod == 1)
				{					
					$obj[] = array('li' => array('class' => ''),
								   'a' =>  array('href' => $pagestring1
											),
								   'text' => '1'
								  );
					$obj[] = array('li' => array('class' => ''),
								   'a' =>  array('href' => $pagestring2
										),
								   'text' => '2'
							      );		
				}
				else
				{
					$obj[] = array('li' => array('class' => ''),
								   'a' =>  array('href' => $targetpage .'?'. $pagestring1
											),
								   'text' => '1'
								  );
					$obj[] = array('li' => array('class' => ''),
								   'a' =>  array('href' => $targetpage .'?'. $pagestring2
										),
								   'text' => '2'
							      );	
				}				
				$obj[] = array('li' => array('class' => 'disabled'),
							   'a' =>  array('href' => '#',
								  		     'onclick' => 'return false;'
											),
							   'text' => '...'
							  );
							  
				for ($counter = $lastpage - (1 + ($adjacents * 3)); $counter <= $lastpage; $counter++)
				{
					if ($counter == $page)
					{
						$obj[] = array('li' => array('class' => 'active'),
									   'a' =>  array('href' => '#',
										 		   'onclick' => 'return false;'
											),
									   'text' => $counter
								      );
					}
					else
					{
						if($seomod == 1)
						{
							$url_query = make_link($type,  array_merge($make_link_args, array('page' => $counter)));

							$obj[] = array('li' => array('class' => ''),
										   'a' =>  array('href' => $url_query
													),
										   'text' => $counter
										  );
						}
						else
						{
							$url_query = preg_replace('/page=([0-9]*)/', 'page='.$counter, $pagestring);
							$obj[] = array('li' => array('class' => ''),
									 	   'a' =>  array('href' => $targetpage .'?'. $url_query
										   				),
									 	   'text' => $counter
								  		  );
						}
					}
				}
			}
		}
		
		//next button
		if ($page < $counter - 1) 
		{
			if($seomod == 1)
			{
				$url_query = make_link($type,  array_merge($make_link_args, array('page' => $next)));
				$obj[] = array('li' => array('class' => ''),
							   'a' =>  array('href' => $url_query
							   				),
							   'text' => '&raquo;'
						      );
			}
			else
			{
				$url_query = preg_replace('/page=([0-9]*)/', 'page='.$next, $pagestring);
				$obj[] = array('li' => array('class' => ''),
							   'a' =>  array('href' => $targetpage .'?'. $url_query),
							   'text' => '&raquo;'
						      );
			}				
		}
		else
		{
			$obj[] = array('li' => array('class' => 'disabled'),
						   'a' =>  array('href' => '#',
							 			 'onclick' => 'return false;'
									    ),
						   'text' => '&raquo;'
						  );
		}
	}
	return $obj;
}

function sanitize_title($title) {
	$title = strip_tags($title);
	// Preserve escaped octets.
	$title = preg_replace('|%([a-fA-F0-9][a-fA-F0-9])|', '---$1---', $title);
	// Remove percent signs that are not part of an octet.
	$title = str_replace('%', '', $title);
	// Restore octets.
	$title = preg_replace('|---([a-fA-F0-9][a-fA-F0-9])---|', '%$1', $title);

	$title = remove_accents($title);
	if (seems_utf8($title)) {
		if (function_exists('mb_strtolower')) {
			$title = mb_strtolower($title, 'UTF-8');
		}
		$title = utf8_uri_encode($title, 200);
	}

	$title = strtolower($title);
	$title = preg_replace('/&.+?;/', '', $title); // kill entities
	$title = preg_replace('/[^%a-z0-9 _-]/', '', $title);
	$title = preg_replace('/\s+/', '-', $title);
	$title = preg_replace('|-+|', '-', $title);
	$title = trim($title, '-');

	return $title;
}

function utf8_uri_encode( $utf8_string, $length = 0 ) {
	$unicode = '';
	$values = array();
	$num_octets = 1;
	for ($i = 0; $i <strlen( $utf8_string ); $i++ ) {
		$value = ord( $utf8_string[ $i ] );
		if ( $value < 128 ) {
			if ( $length && ( strlen($unicode) + 1> $length ) )
				break;
			$unicode .= chr($value);
		} else {
			if ( count( $values ) == 0 ) $num_octets = ( $value <224 ) ? 2 : 3;
			$values[] = $value;
			if ( $length && ( (strlen($unicode) + ($num_octets * 3))> $length ) )
				break;
			if ( count( $values ) == $num_octets ) {
				if ($num_octets == 3) {
					$unicode .= '%' . dechex($values[0]) . '%' . dechex($values[1]) . '%' . dechex($values[2]);
				} else {
					$unicode .= '%' . dechex($values[0]) . '%' . dechex($values[1]);
				}
				$values = array();
				$num_octets = 1;
			}
		}
	}
	return $unicode;
}

function get_video_tags($uniq_id = '', $make_links = 1)
{
	$sql = "SELECT * FROM pm_tags WHERE uniq_id = '".$uniq_id."' ORDER BY tag_id ASC";
	$result = mysql_query($sql);
	$tags = array();
	$id = 0;
	while($row = mysql_fetch_assoc($result))
	{
		$id = $row['tag_id'];
		$tags[$id] = $row;
		if($make_links != 0)
		{
			if(_SEOMOD == 1)
			{
				$tags[$id]['href'] = "<a href=\""._URL."/tags/".$row['safe_tag']."/\">".$row['tag']."</a>";
			}
			else
			{
				$tags[$id]['href'] = "<a href=\""._URL."/tag.php?t=".$row['safe_tag']."\">".$row['tag']."</a>";
			}
		}
	}
	return $tags;
}

function safe2tag($safe_tag = '')
{
	$safe_tag = stripslashes($safe_tag);
	$sql = "SELECT * FROM pm_tags WHERE safe_tag = '".secure_sql($safe_tag)."'";
	$result = mysql_query($sql);
	if(!$result)
		return false;
	$row = mysql_fetch_assoc($result);
	return $row['tag'];
}
function safe_tag($tag)
{
	$remove_chars = array('"', "'", "?", "!");//, "#", "%");
	//$tag = str_replace($remove_chars, '', $tag);
	$tag = sanitize_title($tag);
	
	return $tag;
}
function tag_cloud($randomize = 0, $limit = 15, $shuffle = 1)
{
	$max_size = 20;
	$min_size = 11;
	
	$sql = "SELECT tag_id, tag, safe_tag, COUNT(*) as numvids FROM pm_tags GROUP BY safe_tag";
	if($randomize == 0)
		$sql .= " ORDER BY numvids DESC";
	else
		$sql .= " ORDER BY tag_id DESC";
	if($limit != 0)
		$sql .= " LIMIT ".$limit;

	$result = mysql_query($sql);
	$tags = array();
	if($result)
	{
		$max = 0;
		$min = 10000;
		while($row = mysql_fetch_assoc($result))
		{
			if($row['numvids'] > $max)
			{
				$max = $row['numvids'];
			}
			if($row['numvids'] < $min)
			{
				$min = $row['numvids'];
			}
			$tags[ $row['tag_id'] ] = $row;
		}
		
		$spread = $max - $min;
		if($spread == 0)
			$spread = 1;
		$step = ($max_size - $min_size) / ($spread);
		foreach($tags as $tag_id => $tag)
		{
			$size = round($min_size + (($tag['numvids'] - $min) * $step));
			
			if(_SEOMOD)
				$tags[ $tag_id ]['href'] = "<a href=\""._URL."/tags/".$tag['safe_tag']."/\" class=\"tag_cloud_link\" style=\"font-size:".$size."px;\">".$tag['tag']."</a> ";
			else
				$tags[ $tag_id ]['href'] = "<a href=\""._URL."/tag.php?t=".$tag['safe_tag']."\" class=\"tag_cloud_link\" style=\"font-size:".$size."px;\">".$tag['tag']."</a> ";		
		}
	}
	if($shuffle == 1)
	{
		shuffle($tags);
	}
	return $tags;
}

function log_error($log_msg, $area, $msg_type = 1) 
{
	global $config;
	
	$log_msg = stripslashes($log_msg);
	$log_msg = secure_sql($log_msg);
	
	$sql = "INSERT INTO pm_log (log_msg, area, msg_type, added) 
			VALUES ('". $log_msg ."', '". $area ."', '". $msg_type ."', '". time() ."')";
	if ($result = @mysql_query($sql))
	{
		update_config('unread_system_messages', (int) $config['unread_system_messages'] + 1);
	}

	return true;
}

// Optimized by Trace (http://www.96down.com/forum/memberlist.php?mode=viewprofile&u=5965)
function add_to_chart($uniq_id)
{
	if ('' == $uniq_id)
		return true;
	$sql = "INSERT INTO pm_chart (uniq_id, views, views_this) VALUES ('".$uniq_id."', 1, 1) ON DUPLICATE KEY UPDATE views = views+1, views_this = views_this+1";
	mysql_query($sql);
	return true;
}

function get_chart($limit = 50, $mode = 'advanced')
{
	// used only by topvideos.php
	$sql = "SELECT pm_chart.uniq_id, pm_chart.views, pm_videos.id  
			FROM pm_chart 
			LEFT JOIN pm_videos ON ( pm_chart.uniq_id = pm_videos.uniq_id ) 
			ORDER BY pm_chart.views DESC";
	if($limit > 0)
		$sql .= " LIMIT ".$limit;
		
	$result = mysql_query($sql);
	
	if($result !== false)
	{
		$vids = array();
		while($row = mysql_fetch_assoc($result))
		{
			$vids[] = $row;
		}
	}

	return (is_array($vids)) ? $vids : array();
}

// Optimized by Trace (http://www.96down.com/forum/memberlist.php?mode=viewprofile&u=5965)
function reset_chart()
{
	//Reset the chart
	mysql_query('LOCK TABLES pm_chart'); //Lock it just to make sure
	mysql_query('UPDATE pm_chart SET views = views_this + views_last + views_seclast, views_seclast = views_last, views_last = views_this, views_this = 0'); //Move views to the right
	mysql_query('DELETE FROM pm_chart WHERE views < 5'); //Some cleaning
	mysql_query('UNLOCK TABLES pm_chart'); //Unlock tables
	update_config('chart_last_reset', time());
	
	return true;
}

function count_days( $timestamp_1, $timestamp_2 )
{

	$gd_a = getdate( $timestamp_1 );
	$gd_b = getdate( $timestamp_2 );
	$a_new = mktime( 12, 0, 0, $gd_a['mon'], $gd_a['mday'], $gd_a['year'] );
	$b_new = mktime( 12, 0, 0, $gd_b['mon'], $gd_b['mday'], $gd_b['year'] );
	return round( abs( $a_new - $b_new ) / 86400 );
}

function unspecialchars($text, $and_quotes = 0) {

   if( $and_quotes == 1 || $and_quotes === true )
   {
      $text = str_replace('&quot;', '"', $text);
      $text = str_replace('&#039', "'", $text);
   }
   $text = str_replace('&lt;', '<', $text);
   $text = str_replace('&gt;', '>', $text);
   return $text;
}

function generate_activation_key($length = 9)
{
	$charset = "aAbBcCdDeEfFgGhHiIjJkKlLmMnNoOpPqQrRsStTuUvVwWxXyYzZ0123456789";
	$code = '';
	$charset_len = strlen($charset)-1;
	
	for($i = 0; $i < $length; $i++)
	{
		$code .= $charset{ rand(0, $charset_len) };	
	}
	return $code;
}

function fetch_video_sources()
{
	$sql = "SELECT * FROM pm_sources";
	$result = mysql_query($sql);
	if(!$result)
		return false;
	$src = array();
	$id = 0;
	while($row = mysql_fetch_assoc($result))
	{
		$id = $row['source_id'];
		$src[ $id ] = $row;
	}
	foreach($src as $id => $source)
	{
		$src[$source['source_name']] = $source;
	}
	
	return $src;
}

function stats()
{
	global $config;
	
	$stats 	= array();
	$r		= array();
	$sql	= '';
	$result	= '';
	$now	= time();
	$sevend	= ((60 * 60) * 24) * 7;
	$fivem	= 60 * 5;
	
	//	total number of videos
	$stats['videos'] = pm_number_format($config['published_videos']);
	
	//	videos added last week
	$sql = "SELECT COUNT(*) as total FROM pm_videos WHERE added >= '". ($now - $sevend) . "' AND added <= '". time() ."'";
	$result = @mysql_query($sql);
	$r 	= @mysql_fetch_assoc($result);
	$stats['videos_last_week'] = pm_number_format($r['total']);
	@mysql_free_result($result);
	
	//	total number of users
	$sql = "SELECT COUNT(id) as total FROM pm_users";
	$result = @mysql_query($sql);
	$r 	= @mysql_fetch_assoc($result);
	$stats['users'] = pm_number_format($r['total']);
	@mysql_free_result($result);
		
	//	online users
	$sql = "SELECT COUNT(*) as total FROM pm_users WHERE last_signin > '". ($now - $fivem) ."'";
	$result = @mysql_query($sql);
	$r 	= @mysql_fetch_assoc($result);
	$stats['online_users'] = pm_number_format($r['total']);
	@mysql_free_result($result);

	return $stats;
}
if (!function_exists('json_encode'))
{
  function json_encode($a = false)
  {
    if (is_null($a)) return 'null';
    if ($a === false) return 'false';
    if ($a === true) return 'true';
    if (is_scalar($a))
    {
      if (is_float($a))
      {
        // Always use "." for floats.
        return floatval(str_replace(",", ".", strval($a)));
      }

      if (is_string($a))
      {
        static $jsonReplaces = array(array("\\", "/", "\n", "\t", "\r", "\b", "\f", '"'), array('\\\\', '\\/', '\\n', '\\t', '\\r', '\\b', '\\f', '\"'));
        return '"' . str_replace($jsonReplaces[0], $jsonReplaces[1], $a) . '"';
      }
      else
        return $a;
    }
    $isList = true;
    for ($i = 0, reset($a); $i < count($a); $i++, next($a))
    {
      if (key($a) !== $i)
      {
        $isList = false;
        break;
      }
    }
    $result = array();
    if ($isList)
    {
      foreach ($a as $v) $result[] = json_encode($v);
      return '[' . join(',', $result) . ']';
    }
    else
    {
      foreach ($a as $k => $v) $result[] = json_encode($k).':'.json_encode($v);
      return '{' . join(',', $result) . '}';
    }
  }
}

/*
	This function returns an array containing all the info about the user's Favorite Videos
*/
function request_user_playlist($user_id)
{
	if( ! is_numeric($user_id))
	{
		return false;
	}
	
	$content	= array();
	$all_vids	= array();
	
	$sql = "SELECT * 
			FROM pm_favorites 
			WHERE user_id = '".$user_id."'
			ORDER BY id DESC";	
	$result = @mysql_query($sql);
	if(!$result)
	{
		return false;
	}
	while($row = mysql_fetch_assoc($result))
	{
		$all_vids[] = $row['uniq_id'];
	}
	
	$total_videos = mysql_num_rows($result);
	mysql_free_result($result);
	
	if($total_videos > 0)
	{
		foreach($all_vids as $k => $uniq_id)
		{
			$row = request_video($uniq_id, 'favorites', false);
			$content[ $uniq_id ] = $row;
		}
	}

	return $content;
}

function report_video($uniq_id, $r_type, $reason, $username)
{
	if ($uniq_id == '')
		return '';
	
	$sql = "SELECT COUNT(*) as total_found FROM pm_reports 
			WHERE entry_id = '". secure_sql($uniq_id) ."'";
	$result = mysql_query($sql);
	if ( ! $result)
	{
		return false;
	}
	$row = mysql_fetch_assoc($result);
	mysql_free_result($result);
	
	if ($row['total_found'] == 0)
	{
		$sql = "INSERT INTO pm_reports 
						(r_type, entry_id, reason, submitted, added) 
				VALUES ('". $r_type ."', '". $uniq_id ."', '". $reason ."', '". $username ."', '". time() ."')";
		$result = mysql_query($sql);
		if ( ! $result)
		{
			return false;
		}
	}
	return true;
}

function get_config($name = '')
{
	$config = array();
	$row = array();
	
	$sql = "SELECT * 
			FROM pm_config";
	if ('' != $name)
	{
		$sql .= " WHERE name = '". $name ."'";
	}
	
	$result = @mysql_query($sql);
	while ($row = @mysql_fetch_assoc($result))
	{
		$config[$row['name']] = $row['value'];
	}
	@mysql_free_result($result);
	
	if (count($config) < 3 && $name == '')
	{
		$sql = "SELECT * FROM pm_config WHERE id = '1'";
		$result = @mysql_query($sql);
		$config = @mysql_fetch_assoc($result);
		@mysql_free_result($result);
	}
	
	if ('' != $name)
		return $config[$name];

	return $config;
}

function update_config($name, $value, $overwrite = true)
{
	global $config;
	
	if (!array_key_exists($name, $config))
	{
		return array('Setting variable <code>"'. $name .'"</code> not found', 0);
	}
	
	$value = trim($value);
	$value = secure_sql($value);
	
	$sql = "UPDATE pm_config 
			SET value = '". $value ."' 
			WHERE name = '". $name ."'";
	$result = mysql_query($sql);
	if ( ! $result)
	{
		return array(mysql_error(), mysql_errno());
	}
	
	if ($overwrite == true)
	{
		$config[$name] = $value;
	}
	
	return true;
}

if ( ! function_exists('mb_strlen'))
{
	function mb_strlen($str, $encoding) 
	{
		if ($encoding == 'UTF-8') 
		{
			return preg_match_all('%(?:
					  [\x09\x0A\x0D\x20-\x7E] 
					| [\xC2-\xDF][\x80-\xBF]
					|  \xE0[\xA0-\xBF][\x80-\xBF]
					| [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}
					|  \xED[\x80-\x9F][\x80-\xBF]
					|  \xF0[\x90-\xBF][\x80-\xBF]{2}
					| [\xF1-\xF3][\x80-\xBF]{3}
					|  \xF4[\x80-\x8F][\x80-\xBF]{2}
					)%xs', $str, $out);
		} 
		else
		{
			return strlen($str);
		}
	}
}
if ( ! function_exists('mb_substr'))
{
	function mb_substr($str, $start, $length, $enconding)
	{
		return substr($str, $start, $length);
	}
}
if ( ! function_exists('str_ireplace'))
{
	function str_ireplace($search, $replace, $subject)
	{
		//return str_replace($search, $replace, $subject);
		$search = str_replace( array('/', "'"), array('\/', "\'"), $search);
		$return  = preg_replace('/'. $search .'/is', $replace, $subject);
		return $return;
	}
}


function add_meta($item_id = 0, $item_type, $meta_key, $meta_value, $check = false)
{
	if ($check)
	{
		$buff = get_meta($item_id, $item_type, $meta_key);
		if (count($buff) > 0)
		{
			update_meta($item_id, $item_type, $meta_key, $meta_value);
			return true;
		}
	}
	
	$meta_key = stripslashes($meta_key);
	$meta_key = trim($meta_key);
	$meta_key = secure_sql($meta_key);
	
	$meta_value = stripslashes($meta_value);
	$meta_value = trim($meta_value);
	$meta_value = secure_sql($meta_value);	
	
	$sql = "INSERT INTO pm_meta	(item_id, item_type, meta_key, meta_value) 
			VALUES ('". $item_id ."', '". $item_type ."', '". $meta_key ."', '". $meta_value ."')";
	if ($result = mysql_query($sql))
	{
		return mysql_insert_id();
	}
	
	return false;
}

function update_meta($item_id = 0, $item_type = 0, $meta_key = '', $meta_value = '', $meta_id = 0) // $meta_id added in v2.0
{
	$meta_key = stripslashes($meta_key);
	$meta_key = trim($meta_key);
	$meta_key = secure_sql($meta_key);
	
	// first, check if meta key exists
	if ($meta_id)
	{
		$sql = "SELECT meta_key 
				FROM pm_meta 
				WHERE id = '". $meta_id ."'";
	}
	else
	{
		$sql = "SELECT meta_key 
				FROM pm_meta 
				WHERE item_id = '". $item_id ."' 
				  AND item_type = '". $item_type ."' 
				  AND meta_key = '". $meta_key ."' ";
	}
	
	$result = mysql_query($sql);
	if (mysql_num_rows($result) == 0)
	{
		return add_meta($item_id, $item_type, $meta_key, $meta_value);
	}
	mysql_free_result($result);
	
	$meta_value = stripslashes($meta_value);
	$meta_value = trim($meta_value);
	$meta_value = secure_sql($meta_value);
	
	if ($meta_id)
	{
		$sql = "UPDATE pm_meta 
				   SET ";
		$sql .= ($meta_key != '') ? " meta_key = '". $meta_key ."', " : '';
		$sql .= " meta_value = '". $meta_value ."' 
				WHERE id = ". $meta_id;
	}
	else
	{
		$sql = "UPDATE pm_meta 
				   SET meta_value = '". $meta_value ."' 
				WHERE item_id = '". $item_id ."' 
				  AND item_type = '". $item_type ."' 
				  AND meta_key = '". $meta_key ."'";
	}
	
	mysql_query($sql);

	return true;
}

function delete_meta($item_id = 0, $item_type = 0, $meta_key = '', $meta_id = 0) // $meta_id added in v2.0
{
	if ((!$item_id || !$item_type) && ! $meta_id)
	{
		return false;
	}
	
	$single = ('' != $meta_key) ? true : false;
	
	if ($meta_id)
	{
		$sql = "DELETE FROM pm_meta 
				WHERE id = '". $meta_id ."' LIMIT 1";
	}
	else
	{
		$sql = "DELETE FROM pm_meta 
				WHERE item_id = '". $item_id ."' 
				  AND item_type = '". $item_type ."' ";
		$sql .= ($single) ? " AND meta_key = '". $meta_key ."' " : '';
	}
	
	return mysql_query($sql);
}

function get_meta($item_id, $item_type, $meta_key = '')
{
	$meta = array();
	$single = ('' != $meta_key) ? true : false;

	$sql = "SELECT meta_key, meta_value  
			FROM pm_meta 
			WHERE item_id = '". $item_id ."' 
			  AND item_type = '". $item_type ."' ";
	
	$sql .= ($single) ? " AND meta_key = '". $meta_key ."' " : '';
	
	$result = mysql_query($sql);
	
	if ($single)
	{
		$row = mysql_fetch_assoc($result);
		$meta[$row['meta_key']] = $row['meta_value'];
	
		return $meta;
	}
	else if(mysql_num_rows($result) > 0)
	{
		while($row = mysql_fetch_assoc($result))
		{	
			// group by meta_key 
			$meta['*'][$row['meta_key']][] = $row['meta_value'];
			
			// will overwrite the value for duplicate keys
			$meta[$row['meta_key']] = $row['meta_value'];
		}
		mysql_free_result($result);
		
		return $meta;
	}
	
	return ($single) ? '' : array();
}

function get_meta_value($item_id, $item_type, $meta_key)
{
	$meta = get_meta($item_id, $item_type, $meta_key = '');
	return $meta[$meta_key];
}

function get_all_meta_data($item_id, $item_type)
{
	$data = array();
	
	$sql = "SELECT *   
			FROM pm_meta 
			WHERE item_id = '". $item_id ."' 
			  AND item_type = '". $item_type ."' ";
	if ( ! $result = mysql_query($sql))
	{
		return array();
	}
	
	while ($row = mysql_fetch_assoc($result))
	{
		$data[$row['id']] = $row;
	}
	mysql_free_result($result);
	
	return $data;
}

function resize_then_crop($filein, $fileout, $imagethumbsize_w, $imagethumbsize_h, $red, $green, $blue, $allowed_type = array())
{
	if ($fileout == '')
	{
		return false;
	}
	
	// $allowed_type = array('image/png', 'image/gif', 'image/jpg', 'image/jpeg', 'image/pjpeg');
	$percent = 0.5;
	// Get new dimensions
	$image_info	= @getimagesize($filein);
	$width		= $image_info[0];
	$height		= $image_info[1];
	$img_type 	= $image_info[2];
	$new_width 	= $width * $percent;
	$new_height	= $height * $percent;
	
	if (is_array($allowed_type) && count($allowed_type) > 0)
	{
		if(!in_array($image_info['mime'], $allowed_type))
		{
			return false;
		}
	}
	
	switch ($img_type)
	{
		case 1: // gif
			$image = imagecreatefromgif($filein);
	    break;
		
	    case 2; // jpg
	    	$image = imagecreatefromjpeg($filein);
	    break;
		
	    case 3: // png
			$image = imagecreatefrompng($filein);
	    break;
		
		default:
			$image = '';
		break;
	}
	
	if (!$image)
	{
		return false;
	}
	
	$width	= $imagethumbsize_w;
	$height	= $imagethumbsize_h;
	
	list($width_orig, $height_orig) = @getimagesize($filein);
	
	if ($width_orig < $height_orig) 
	{
		$height = ($imagethumbsize_w / $width_orig) * $height_orig;
	} 
	else 
	{
		$width = ($imagethumbsize_h / $height_orig) * $width_orig;
	}
	
	if ($width < $imagethumbsize_w)
	{
		$width = $imagethumbsize_w;
		$height = ($imagethumbsize_w/ $width_orig) * $height_orig;;
	}
	
	if ($height < $imagethumbsize_h)
	{
		$height = $imagethumbsize_h;
		$width = ($imagethumbsize_h / $height_orig) * $width_orig;
	}
	
	$thumb		= imagecreatetruecolor($width , $height);
	$bgcolor	= imagecolorallocate($thumb, $red, $green, $blue);   
	ImageFilledRectangle($thumb, 0, 0, $width, $height, $bgcolor);
	imagealphablending($thumb, true);
	
	imagecopyresampled($thumb, $image, 0, 0, 0, 0, $width, $height, $width_orig, $height_orig);

	$thumb2 = imagecreatetruecolor($imagethumbsize_w , $imagethumbsize_h);
	
	// true color for best quality
	$bgcolor = imagecolorallocate($thumb2, $red, $green, $blue);   
	ImageFilledRectangle($thumb2, 0, 0,	$imagethumbsize_w , $imagethumbsize_h , $white);
	imagealphablending($thumb2, true);
	
	$w1 = ($width / 2) - ($imagethumbsize_w / 2);
	$h1 = ($height / 2) - ($imagethumbsize_h / 2);
	
	imagecopyresampled($thumb2, $thumb, 0,0, $w1, $h1, $imagethumbsize_w , $imagethumbsize_h ,$imagethumbsize_w, $imagethumbsize_h);

	$tmp_parts = explode('.', $fileout);
	$save_as = array_pop($tmp_parts);
	$save_as = strtolower($save_as);
	$save_as = ($save_as != '') ? $save_as : 'jpg';

	switch($save_as)
	{
		case 'gif':
			@imagegif($thumb2, $fileout);
		break;

		case 'jpg':
			@imagejpeg($thumb2, $fileout);
		break;

		case 'png':
			@imagepng($thumb2, $fileout);
		break;
	}
	
	return true;
}

// wrapper for number_format function
function pm_number_format($number, $decimals = 0, $dec_point = '.', $thousands_sep = ',')
{
	return number_format($number, $decimals, $dec_point, $thousands_sep);
}

function pm_compact_number_format($number)
{
	if ($number < 10000)
	{
		return pm_number_format($number);
	}
	$d = $number < 1000000 ? 1000 : 1000000;
	$f = round($number / $d, 1);
	
	return pm_number_format($f, $f - intval($f) ? 1 : 0) . ($d == 1000 ? 'k' : 'M');
}

function is_serialized($data) 
{
    // if it isn't a string, it isn't serialized
    if ( !is_string( $data ) )
        return false;
    $data = trim( $data );
    if ( 'N;' == $data )
        return true;
    if ( !preg_match( '/^([adObis]):/', $data, $badions ) )
        return false;
    switch ( $badions[1] ) {
        case 'a' :
        case 'O' :
        case 's' :
            if ( preg_match( "/^{$badions[1]}:[0-9]+:.*[;}]\$/s", $data ) )
                return true;
            break;
        case 'b' :
        case 'i' :
        case 'd' :
            if ( preg_match( "/^{$badions[1]}:[0-9.E-]+;\$/", $data ) )
                return true;
            break;
    }
    return false;
}

function generate_excerpt($content, $length = 255) 
{
	$excerpt = strip_tags($content);
	$excerpt = preg_replace( "/[\n\r\t ]+/", ' ', $excerpt);
	$excerpt = str_replace('&nbsp;', '', $excerpt);
	$excerpt = trim($excerpt);
	$excerpt = lwmarkup_strip_all($excerpt);
	//preg_match('/^(.{1,'. $length .'})\b/s', $excerpt, $matches); // v1.8.x
	preg_match_all( '/./u', $excerpt, $words_array ); // since v1.9
	$words_array = array_slice( $words_array[0], 0, $length + 1 );
	$sep = '';
	if (count( $words_array ) > $length) 
	{
		array_pop( $words_array );
		$excerpt = implode( $sep, $words_array );
		$excerpt = $excerpt . $more;
	}
	else 
	{
		$excerpt = implode( $sep, $words_array );
	}
	
	return $excerpt;
}

function lwmarkup_get_regex() 
{
	return '/\[video=([a-zA-Z0-9]{9})\]/i'; // regex for video tags
}

function lwmarkup_parse($content)
{
	$regex = lwmarkup_get_regex();

	return preg_replace_callback($regex, 'lwmarkup_replace_video_tag', $content);
}

function lwmarkup_replace_video_tag($matches)
{
	$code = generate_embed_code($matches[1]);
	$code = preg_replace('/<p style(.*?)<\/p>/i', '', $code); // remove the backlink
	return $code;
}

function lwmarkup_strip_all($content)
{
	$regex = lwmarkup_get_regex();
	
	return preg_replace($regex, '', $content);
}

// From php.net/manual/en/reserved.variables.server.php#108186
function request_URI() 
{
    if(!isset($_SERVER['REQUEST_URI'])) {
        $_SERVER['REQUEST_URI'] = $_SERVER['SCRIPT_NAME'];
        if($_SERVER['QUERY_STRING']) {
            $_SERVER['REQUEST_URI'] .= '?' . $_SERVER['QUERY_STRING'];
        }
    }
    return $_SERVER['REQUEST_URI'];
}

function smarty_echo_securimage_sid($params, &$smarty)
{
	return md5(uniqid(time() + rand(0, 99)));
}


// https://www.owasp.org/index.php/PHP_CSRF_Guard
function csrfguard_store_in_session($key, $value)
{
	if (isset($_SESSION))
	{
		$_SESSION[$key]=$value;
	}
}
function csrfguard_unset_session($key)
{
	$_SESSION[$key]=' ';
	unset($_SESSION[$key]);
}
function csrfguard_get_from_session($key)
{
	if (isset($_SESSION))
	{
		return $_SESSION[$key];
	}
	else {  return false; } //no session data, no CSRF risk
}

function csrfguard_generate_token($unique_form_name)
{
	if (function_exists("hash_algos") && in_array("sha512",hash_algos()))
	{
		$token = hash("sha512", mt_rand(0,mt_getrandmax()));
	}
	else
	{
		$token = ' ';
		for ($i = 0; $i < 128; ++$i) 
		{
			$r = mt_rand(0,35);
			if ($r < 26)
			{
				$c = chr(ord('a')+$r);
			}
			else
			{ 
				$c = chr(ord('0')+$r-26);
			} 
			$token .= $c;
		}
	}
	$token = substr($token, 10, 12);
	csrfguard_store_in_session($unique_form_name, $token);
	return $token;
}
function csrfguard_validate_token($unique_form_name, $token_value)
{
	$token = csrfguard_get_from_session($unique_form_name);
	if ($token === false)
	{
		return true;
	}
	elseif ($token == $token_value)
	{
		$result = true;
	}
	else
	{ 
		$result = false;
	} 
	csrfguard_unset_session($unique_form_name);
	return $result;
}

function csrfguard_raw($name = false)
{
	if ( ! $name)
	{
		$name = '_pmnonce_'. mt_rand(0, mt_getrandmax());
	}
	$token = csrfguard_generate_token($name);
	return array('_pmnonce' => $name, '_pmnonce_t' => $token);
}

function csrfguard_form($name = false)
{
	$nonce = csrfguard_raw($name);

	return "<input type='hidden' name='_pmnonce' value='". $nonce['_pmnonce'] ."' id='_pmnonce". $nonce['_pmnonce'] ."' /><input type='hidden' name='_pmnonce_t' value='". $nonce['_pmnonce_t'] ."' id='_pmnonce_t". $nonce['_pmnonce'] ."' />";
}

function csrfguard_url($url, $name = false)
{
	$nonce = csrfguard_raw($name);
	
	if (strpos($url, '?') !== false)
	{
		return $url .'&_pmnonce='. $nonce['_pmnonce'] .'&_pmnonce_t='. $nonce['_pmnonce_t'];
	}
	
	return $url . '?_pmnonce='. $nonce['_pmnonce'] .'&_pmnonce_t='. $nonce['_pmnonce_t'];
}

function csrfguard_check_referer($name = false)
{
	if ( ! $name)
	{
		$name = ($_GET['_pmnonce'] != '') ? $_GET['_pmnonce'] : $_POST['_pmnonce'];
	}
	
	if ($_GET['_pmnonce'] != '')
	{
		return csrfguard_validate_token($name, $_GET['_pmnonce_t']);
	}
	else if ($_POST['_pmnonce'] != '')
	{
		return csrfguard_validate_token($name, $_POST['_pmnonce_t']);
	}

	return false;
}

function smarty_empty($params, &$smarty)
{
	return;
}

function user_has_flagged_comment($comment_id)
{
	global $userdata;
	if ( ! $comment_id || ! isset($userdata['id']))
	{
		return false;
	}
	
	$sql = "SELECT COUNT(*) as total 
			FROM pm_comments_reported 
			WHERE user_id = '". $userdata['id'] ."' 
			  AND comment_id = '". $comment_id ."'";
	if ( $result = mysql_query($sql))
	{
		$row = mysql_fetch_assoc($result);
		
		mysql_free_result($result);
		if ($row['total'] > 0)
		{
			return true;
		}
	}
	return false;
}

function session_save_footprint()
{
	$x = explode('/', $_SERVER['SCRIPT_NAME']);
	$script_name = array_pop($x);
	$dir_name = array_pop($x);

	//If no session was started for some reason don't try to save it
	if($_SESSION === null) return;

	if ($dir_name == 'admin')
	{
		$ignore_pages = array('login.php', 'footer.php', 'header.php', 
							  'functions.php', 'img.resize.php', 'mysql_backup.php',
							  'styleme.php', 'upload_file.php', 'upload_image.php',
							  'vscheck.php', 'admin-ajax.php'
							  );
		if ( ! in_array($script_name, $ignore_pages))
		{
			if ( ! array_key_exists('previous_page', $_SESSION))
			{
				$_SESSION['previous_page'] = '';
			}
			
			parse_str($_SERVER['QUERY_STRING'], $http_query);
			$new_http_query = '';
			foreach ($http_query as $k => $v)
			{
				if (in_array($k, array('page', 'filter', 'fv', 'list', 'vid', 'uid')))
				{
					$new_http_query .= '&'. $k .'='. urlencode($v);
				}
				$new_http_query = ltrim($new_http_query, '&');
			}
			
			$_SESSION['previous_page'] = 'admin/'. $script_name;
			$_SESSION['previous_page'] .= ($new_http_query != '') ? '?'. $new_http_query : '';
		}
		unset($ignore_pages);
	}
	else
	{
		switch ($script_name)
		{
			// pages allowed to be remembered as 'previous page' when logging in
			// front end
			case 'watch.php':
			case 'article.php':
			case 'article_read.php':
			case 'category.php':
			case 'edit_profile.php':
			case 'favorites.php':
			case 'index.php':
			case 'upload.php':
			case 'upload_avatar.php':
			case 'topvideos.php':
			case 'tag.php':
			case 'suggest.php':
			case 'search.php':
			case 'profile.php':
			case 'page.php':
			case 'newvideos.php':
			case 'myfavorites.php':
			case 'memberlist.php':
				
				if ( ! array_key_exists('previous_page', $_SESSION))
				{
					$_SESSION['previous_page'] = '';
				}
				
				$uri = request_URI();
				if (_SEOMOD)
				{
					$url_pieces = explode('/', _URL);
					$url_p_count = count($url_pieces);

					if ($url_p_count > 3)
					{
						$pieces = explode('/', $uri);
						for ($i = 3; $i < $url_p_count; $i++)
						{
							$find = $url_pieces[$i];
							if ($find != '')
							{
								foreach ($pieces as $k => $v)
								{
									if ($v == $find)
									{
										unset($pieces[$k]);
										break;
									}
								}
							}
						}
						$uri = implode('/', $pieces);
					}
					$uri = str_replace('//', '/', $uri);
					
				}
				else
				{
					$whitelist_params = array('page', 'letter', 'order', 'sortby', 'vid', 'cat', 
											  'show', 'tag', 'mode', 'do', 'a', 'u', 'd', 'p', 
											  'name', 'btn', 't', 'keywords','c');
					
					parse_str($_SERVER['QUERY_STRING'], $http_query);
					$new_http_query = '';
					foreach ($http_query as $k => $v)
					{
						if (in_array($k, $whitelist_params))
						{
							$new_http_query .= '&'. $k .'='. urlencode($v);
						}
						$new_http_query = ltrim($new_http_query, '&');
					}
					$uri = $script_name;
					$uri .= ($new_http_query != '') ? '?'. $new_http_query : '';
				}
				$uri = ltrim($uri, '/');

				$_SESSION['previous_page'] = $uri;
	
			break;
		}
	}
}

function smarty_get_advanced_video_list($params, &$smarty)
{
	/*
	 * How to use in .tpl?
	 * 
	 * Example 1: get latest added 5 videos
	 * {get_advanced_video_list assignto="advanced_video_list" limit=5}
	 * - then run a foreach from=$advanced_video_list ...
	 * 
	 * Example 2: get latest 8 videos added by Admin in category with id=10 
	 * {get_advanced_video_list assignto="advanced_video_list" submitted="admin" category_id=10 limit=8}
	 * 
	 * Example 3: get latest 10 videos with tag = "Auto review"
	 * {get_advanced_video_list assignto="advanced_video_list" tag="Auto review"}
	 * 
	 * Example 4: get videos added in the past 7 days
	 * {get_advanced_video_list assignto="advanced_video_list" days_ago=7}
	 * 
	 * Example 5: get most viewed videos in the past X days (X defined in pm_config table by an Administrator)
	 * {get_advanced_video_list assignto="advanced_video_list" days_ago="recent"}
	 * 
	 * Example 6: get featured videos only
	 * {get_advanced_video_list assignto="advanced_video_list" featured="1"}
	 * 
	 * Example 7: get most liked videos in category with id=10
	 * {get_advanced_video_list assignto="advanced_video_list" category_id=10 order_by="rating"}
	 * 
	 * Example 8: get most viewed videos added by user 'CoolGuy' in category with id=10
	 * {get_advanced_video_list assignto="advanced_video_list" category_id=10 order_by="site_views" username='CoolGuy'}
	 */
	$defaults = array(
		'category_id' => 0,
		'username' => '',
		'tag' => '',
		'restricted' => false,	// false, 1 or 0
		'featured' => false,	// false, 1 or 0
		'days_ago' => 0, 		// last X days OR "recent"; "recent" uses 'pm_chart' table
		'order_by' => 'added', 	// "rating" or table column name; "rating" uses 'pm_bin_rating_meta' table
		'sort' => 'DESC',		// DESC or ASC
		'from' => 0,
		'limit' => 10
	);

	$options = array_merge($defaults, $params);
	extract($options);
	
	if ($order_by == 'duration')
	{
		$order_by = 'yt_length';
	}

	$sql_where = '';
	$sql_join = '';
	
	if ($tag != '')
	{
		$tag = safe_tag($tag);
		
		$sql_join = ' JOIN pm_tags ON (pm_tags.uniq_id = pm_videos.uniq_id) ';
		$sql_where =  " pm_tags.safe_tag = '". $tag ."' ";
		$order_by = ($order_by == 'rating' || $order_by == 'recent') ? 'added' : $order_by;
	}
	else
	{
		if ($username != '')
		{
			$sql_where .= ($sql_where != '') ? " AND " : '';
			$sql_where .= " submitted = '". secure_sql($username) ."'";
		}
		
		if ($category_id != '')
		{
			if(strpos($category_id,',') !== false){
				$cats = explode(',', $category_id);
				$sql_where .= ($sql_where != '') ? " AND " : '';
				$sql_where .= ' (';
				foreach($cats as $key => $val){
					$sql_where .= (($key!=0)? ' OR ':'')." FIND_IN_SET('".$val."',category)";
				}
				$sql_where .= ') ';
			}else{
				$sql_where .= ($sql_where != '') ? " AND " : '';
				$sql_where .= "(category LIKE '$category_id'
							 	  OR category LIKE '$category_id,%'
							 	  OR category LIKE '%,$category_id'
							 	  OR category LIKE '%,$category_id,%') ";
			}
		}
	}

	if ($order_by == 'rating')
	{
		$sql_join = ' LEFT JOIN pm_bin_rating_meta ON (pm_bin_rating_meta.uniq_id = pm_videos.uniq_id)';
		$order_by = 'score';
	}
	
	if (strcmp($days_ago, 'recent') == 0)
	{
		$sql_join = ' JOIN pm_chart ON (pm_chart.uniq_id = pm_videos.uniq_id ) ';
		$order_by = 'pm_chart.views';
	}
	
	if ($restricted !== false)
	{
		$sql_where .= ($sql_where != '') ? ' AND ' : '';
		$sql_where .= " restricted = '". $restricted ."' ";
	}
	
	if ($featured !== false)
	{
		$sql_where .= ($sql_where != '') ? ' AND ' : '';
		$sql_where .= " featured = '". $featured ."' ";
	}

	$sql_where .= ($sql_where != '') ? ' AND ' : '';
	$sql_where .= " added <= '". time() ."' ";
	
	if ($days_ago != 0 && $days_ago != 'recent')
	{
		$start_timestamp = time() - ($days_ago * 86400); // 86400 = 1 day
		
		$sql_where .= ($sql_where != '') ? ' AND ' : '';
		$sql_where .= " added >= '". $start_timestamp ."' ";
	}
	
	$sql = 'SELECT id ';
	if ($order_by == 'score')
	{
		$sql .= ', COALESCE(pm_bin_rating_meta.score, 0) as score ';
	}
	$sql .= ' FROM pm_videos '. $sql_join .'  
			  WHERE '. $sql_where .' 
			  ORDER BY '. $order_by .' '. $sort .'
			  LIMIT '. $from .', '. $limit;
	$list = array();

	$result = mysql_query($sql); 
	if ($result)
	{
		$ids = array();
		$i = 0;
		while ($row = mysql_fetch_assoc($result))
		{
			$ids[$i++] = $row['id'];
		}

		$unsorted_list = get_video_list('', '', 0, $limit, 0, $ids);
		
		if (count($unsorted_list) > 0)
		{
			$i = 0;	
			$list = array();
			foreach ($ids as $k => $vid)
			{
				foreach ($unsorted_list as $kk => $video_data)
				{
					if ($video_data['id'] == $vid)
					{
						$list[$i] = (array) $video_data;
						break;
					}
				}
				$i++;
			}
		}
	}

	$smarty->assign($params['assignto'], $list);
	return;
}

function get_preroll_ad($ad_id = '')
{
	global $config, $userdata;
	
	$sql_where = '';
	//$total_available_ads = (int) count_entries('pm_preroll_ads', 'active', '1');
	$total_available_ads = $config['total_preroll_ads'];
	
	if ($total_available_ads == 0)
		return false;
			
	if ($ad_id != '')
	{
		$sql_where .= " id = '". $ad_id ."' ";
	}
	else 
	{
		if (is_array($userdata) && $userdata['id'] != 0)
		{
			$sql_where .= " (user_group = '0' OR user_group = '1') "; // 0 = everyone; 1 = logged only; 2 = guests only
		}
		else
		{
			$sql_where .= " (user_group = '0' OR user_group = '2') "; // 0 = everyone; 1 = logged only; 2 = guests only
		}
	}
	
	$sql_where .= ($sql_where != '') ? ' AND ' : '';
	$sql_where .= " status = '1' ";
	
	$rand_from = ($ad_id != '') ? 0 : abs(rand(0, $total_available_ads - 1));
		
	$sql = "SELECT * FROM pm_preroll_ads 
			WHERE $sql_where
			LIMIT $rand_from, 1";
	
	if ( ! $result = mysql_query($sql))
	{
		return false;
	}
	
	$row = mysql_fetch_assoc($result);
	mysql_free_result($result);
	
	return $row;
}

function serve_preroll_ad($page = '', $video_data = false)
{
	global $config, $smarty, $userdata;

	if ( ! $smarty || ! $config)
	{
		return false;
	}
	
	if ($page == '' || ! in_array($page, array('index', 'detail', 'favorites', 'embed')))
	{
		$page = 'detail';
	}
	
	if ($config['total_preroll_ads'] > 0)
	{
		if (isset($_COOKIE[COOKIE_PREROLLAD]) && strlen($_COOKIE[COOKIE_PREROLLAD]) > 0)
		{
			$smarty->assign('display_preroll_ad', false);
			return false;
		}
		
		if ( ! $video_data)
		{
			$preroll_ad_data = get_preroll_ad();
			
			if (is_array($preroll_ad_data))
			{
				$smarty->assign('display_preroll_ad', true);
				$preroll_ad_data['timeleft_start'] = ($preroll_ad_data['duration'] > 60) ? sec2hms($preroll_ad_data['duration']) : $preroll_ad_data['duration'];
				$smarty->assign('preroll_ad_data', $preroll_ad_data);
				$smarty->assign('preroll_ad_player_uniq_id', $video_data['uniq_id']);
				$smarty->assign('preroll_ad_player_page', $page);
			}
			else
			{
				$smarty->assign('display_preroll_ad', false);
				return false;
			}
			return $preroll_ad_data;
		}

		//
		$sql_where = $sql = '';
		$sql_where .= (is_array($userdata) && $userdata['id'] != 0) ? " (user_group = '0' OR user_group = '1') " : " (user_group = '0' OR user_group = '2') "; // 0 = everyone; 1 = logged only; 2 = guests only
		$sql_where .= ($sql_where != '') ? ' AND ' : '';
		$sql_where .= " status = '1' ";
		
		$sql = "SELECT * FROM pm_preroll_ads WHERE $sql_where ";
		
		if ( ! $result = mysql_query($sql))
		{
			$smarty->assign('display_preroll_ad', false);
			return false;
		}

		$categories = explode(',', $video_data['category']);
		
		$units = array();
		while ($row = mysql_fetch_assoc($result))
		{
			$options = array();
			if (strlen($row['options']) > 0)
			{
				$options = (array) unserialize($row['options']);
			}
			
			if (count($options['ignore_source']) > 0)
			{
				if (in_array($video_data['source_id'], $options['ignore_source']))
				{
					continue;
				}
			}
			
			if (count($options['ignore_category']) > 0)
			{
				$found = false; 
				foreach ($options['ignore_category'] as $k => $ignore_cat_id)
				{
					if (in_array($ignore_cat_id, $categories))
					{
						$found = true;
						break;
					}
				}
				
				if ($found)
				{
					continue;
				}
			}

			$units[] = array_merge($row, $options);
			unset($options);
		}
		mysql_free_result($result);

		$units_count = count($units);
		
		if ($units_count == 0)
		{
			$smarty->assign('display_preroll_ad', false);
			return false;
		}
		
		$rand = rand(0, $units_count-1);
		$preroll_ad_data = $units[$rand];
		
		$smarty->assign('display_preroll_ad', true);
		$preroll_ad_data['timeleft_start'] = ($preroll_ad_data['duration'] > 60) ? sec2hms($preroll_ad_data['duration']) : $preroll_ad_data['duration'];
		$smarty->assign('preroll_ad_data', $preroll_ad_data);
		$smarty->assign('preroll_ad_player_uniq_id', $video_data['uniq_id']);
		$smarty->assign('preroll_ad_player_page', $page);
		
		return $preroll_ad_data;
	}
	
	return false;
}

function apply_theme_customizations()
{
	global $config, $smarty;
	
	$code = '';
	 
	if ($config['default_tpl_customizations'] == '' || $config['template_f'] != 'default')
	{
		$smarty->assign('theme_customizations', '');
		return true;
	}
	
	$data = unserialize(base64_decode($config['default_tpl_customizations']));
	
	if (is_array($data) && count($data) > 0)
	{
		foreach ($data as $element => $properties)
		{
			$code .= $element .' {';
			foreach ($properties as $property => $value)
			{
				if (is_array($value))
				{
					foreach ($value as $k => $v)
					{
						$code .= $property .': '. $v .'; ';
					}
				}
				else
				{
					$code .= $property .': '. $value .'; ';
				}
			}
			$code .= '}';
		}
		
		$smarty->assign('theme_customizations', $code);
		return true;
	}
	
	$smarty->assign('theme_customizations', '');
	return true;
}

function insert_new_video($video_details, &$insert_id) // moved from /admin/functions.php since version 2.0
{
	global $config;
	
	$defaults = array('language' => 1,
					  'age_verification' => 0,
					  'featured' => 0,
					  'added' => time(),
					  'restricted' => 0,
					  'allow_comments' => 1
					);
	
	$video_details = array_merge($defaults, $video_details);
	
	$time_now = time();
	
	if ($video_details['description'] != '')
	{
		if ((strlen($video_details['description']) == 4) && ($video_details['description'] == "<br>"))
		{
			$video_details['description'] = '';
		}
	}
	
	if ($video_details['featured'] == '')
	{
		$video_details['featured'] = 0;
	}
	
	if (empty($video_details['added']))
	{
		$video_details['added'] = $time_now - 1;
	}
	
	if ($video_details['video_slug'] == '')
	{
		$video_details['video_slug'] = $video_details['video_title'];
	}
	$video_details['video_slug'] = sanitize_title($video_details['video_slug']);
	
	$sql = "INSERT INTO pm_videos (uniq_id, video_title, description, yt_id, yt_length, yt_thumb, category, submitted, added, url_flv, source_id, language, age_verification, yt_views, site_views, featured, restricted, allow_comments, video_slug)
			VALUES ('". $video_details['uniq_id'] ."', 
					'". secure_sql($video_details['video_title']) ."', 
					'". secure_sql($video_details['description']) ."', 
					'". $video_details['yt_id'] ."', 
					'". $video_details['yt_length'] ."', 
					'". $video_details['yt_thumb'] ."', 
					'". $video_details['category'] ."', 
					'". $video_details['submitted'] ."', 
					'". $video_details['added'] ."', 
					'". $video_details['url_flv'] ."', 
					'". $video_details['source_id'] ."', 
					'". $video_details['language'] ."', 
					'". $video_details['age_verification'] ."', 
					'0', 
					'0', 
					'". $video_details['featured'] ."', 
					'". $video_details['restricted'] ."', 
					'". $video_details['allow_comments'] ."',
					'". secure_sql($video_details['video_slug']) ."')";
	
	
	if (is_array($video_details))
	{
		$result = mysql_query($sql);
	}
	
	if ( ! $result)
	{
		return array(mysql_error(), mysql_errno());
	}
	$insert_id = mysql_insert_id();
	
	$sql = "UPDATE pm_categories SET total_videos=total_videos+1 ";
	$sql .= ($video_details['added'] <= $time_now) ? ", published_videos = published_videos + 1 " : '';
	$sql .= " WHERE id IN(". $video_details['category'] .")";
	mysql_query($sql);
	
	update_config('total_videos', $config['total_videos'] + 1);
	
	if ($video_details['added'] <= $time_now)
	{
		update_config('published_videos', $config['published_videos'] + 1);
	}
	
	$sql = "INSERT INTO pm_videos_urls (uniq_id, mp4, direct) VALUES 
			('".$video_details['uniq_id']."', '".$video_details['mp4']."', '".$video_details['direct']."')";
	$result = mysql_query($sql);
	
	if (strlen($video_details['embed_code']) > 0)
	{
		$sql = "INSERT INTO pm_embed_code (uniq_id, embed_code) VALUES ('".$video_details['uniq_id']."', '".$video_details['embed_code']."')";
		$result = mysql_query($sql);
	}
	
	if (is_array($video_details['jw_flashvars']))
	{
		$jw_flashvars = serialize($video_details['jw_flashvars']);
		$sql = "INSERT INTO pm_embed_code (uniq_id, embed_code) VALUES ('".$video_details['uniq_id']."', '".secure_sql($jw_flashvars)."')";
		$result = mysql_query($sql);
	}
	
	if (is_array($video_details['meta']))
	{
		$meta_ids = array();
		foreach ($video_details['meta'] as $meta_id => $arr)
		{
			$meta_ids[] = $meta_id;
		}
		
		if (count($meta_ids) > 0)
		{
			$sql = "UPDATE pm_meta 
					SET item_id = $insert_id 
					WHERE id IN (". implode(',', $meta_ids) .")";
			mysql_query($sql);
		}
	}
	
	return true; 
}

function insert_tags($uniq_id = '', $arr_tags = array()) // moved from /admin/functions.php since version 2.0
{
	if($uniq_id != '' && count($arr_tags) > 0)
	{
		for($i = 0; $i < count($arr_tags); $i++)
		{
			$safe_tag = safe_tag($arr_tags[$i]);
			$tag = str_replace('"', '&quot;', $arr_tags[$i]);
			
			$sql = "INSERT INTO pm_tags (uniq_id, tag, safe_tag) VALUES('".$uniq_id."','".secure_sql($tag)."', '".$safe_tag."')";
			$result = mysql_query($sql);
			if(!$result)
				return false;
		}
	}
	return true;
}

function is_meta_key_reserved($key)
{
	return ($key[0] == '_') ? true : false;
}

function smarty_get_video_meta_list($params, &$smarty)
{
	global $video; 
	
	if (empty($params['uniq_id']) && empty($params['video_id']))
		return '';
	
	$video_id = 0;
	
	if ($params['uniq_id'] != '')
	{
		if (is_array($video) && $video['uniq_id'] == $params['uniq_id'] && ! empty($video['id']))
		{
			$video_id = $video['id'];
		}
		else
		{
			$sql = "SELECT id  
					FROM pm_videos 
					WHERE uniq_id = '". $params['uniq_id'] ."'";
			if ($result = mysql_query($sql))
			{
				$row = mysql_fetch_assoc($result);
				mysql_free_result($result);
				$video_id = $row['id'];
			}
		}
	}
	else
	{
		$video_id = $params['video_id'];
	}
	
	if ( ! $video_id)
		return '';
	
	return get_meta_list($video_id, IS_VIDEO);
}

function smarty_get_article_meta_list($params, &$smarty)
{
	if (empty($params['article_id']))
		return '';

	return get_meta_list($params['article_id'], IS_ARTICLE);
}

function smarty_get_page_meta_list($params, &$smarty)
{
	if (empty($params['page_id']))
		return '';

	return get_meta_list($params['page_id'], IS_PAGE);
}

function get_meta_list($item_id, $item_type)
{
	if ( ! $item_id && ! $item_type)
		return '';

	$meta_data = get_meta($item_id, $item_type);

	if ( ! is_array($meta_data))
		return '';
		
	if (count($meta_data) > 0)
	{
		$html .= '<ul class="custom-meta-ul">';
		$html .= "\n\t";
		foreach ($meta_data['*'] as $key => $value_arr)
		{
			if ( ! is_meta_key_reserved($key))
			{
				$html .= '<li class="custom-meta-li">';
				$html .= "\n\t\t";
				$html .= '<span class="custom-meta-li-key">'. $key .'</span>';
				$html .= ': ';
				$html .= '<span class="custom-meta-li-value">';
				$html .= (is_array($value_arr)) ? implode(', ', $value_arr) : $value_arr;
				$html .= '</span>';
				$html .= "\n\t";
				$html .= '</li>';
				$html .= "\n";
			}
		}
		$html .= '</ul>';
		
		return $html;
	}
	
	return '';
}

function smarty_get_video_meta($params, &$smarty)
{
	global $video; 
	
	if (empty($params['uniq_id']) && empty($params['video_id']))
		return '';
	
	$video_id = 0;
	
	if ($params['uniq_id'] != '')
	{
		if (is_array($video) && $video['uniq_id'] == $params['uniq_id'] && ! empty($video['id']))
		{
			$video_id = $video['id'];
		}
		else
		{
			$sql = "SELECT id  
					FROM pm_videos 
					WHERE uniq_id = '". $params['uniq_id'] ."'";
			if ($result = mysql_query($sql))
			{
				$row = mysql_fetch_assoc($result);
				mysql_free_result($result);
				$video_id = $row['id'];
			}
		}
	}
	else
	{
		$video_id = $params['video_id'];
	}
	
	if ( ! $video_id)
		return '';
	
	$meta = get_meta($video_id, IS_VIDEO, $params['key']);
	
	return $meta[$params['key']];
}

function smarty_get_article_meta($params, &$smarty)
{
	if (empty($params['article_id']) || empty($params['key']))
		return '';
	
	$meta = get_meta($params['article_id'], IS_ARTICLE, $params['key']);
	
	return $meta[$params['key']];
}

function smarty_get_page_meta($params, &$smarty)
{
	if (empty($params['page_id']) || empty($params['key']))
		return '';
	
	$meta = get_meta($params['page_id'], IS_PAGE, $params['key']);
	
	return $meta[$params['key']];
}

function get_current_url($seo = true, $base_url = false)
{
	$s = empty($_SERVER["HTTPS"]) ? '' : ($_SERVER["HTTPS"] == "on") ? "s" : "";
	$sp = strtolower($_SERVER["SERVER_PROTOCOL"]);
	$protocol = substr($sp, 0, strpos($sp, "/")) . $s;
	
	if ($base_url)
	{
		return $protocol . "://" . $_SERVER['HTTP_HOST'];
	}

	if ( ! $seo)
	{
		$url = $protocol . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME'];
		$url .= ($_SERVER['QUERY_STRING'] != '') ? '?'. $_SERVER['QUERY_STRING'] : '';
	
		return rtrim($url, "?&");
	}
	
	return $protocol . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
}

function get_current_base_url()
{
	return get_current_url(false, true);
}

function get_switch_ui_url()
{
	$current_url = get_current_url(false);
	
	$switch_to = 'mobile';
	
	if ($_COOKIE['melody_device'] == 'mobile')
	{
		$switch_to = 'desktop';
	}
	
	$current_url = (strpos($current_url, '?')) ? $current_url .'&ui='. $switch_to : $current_url .'?ui='. $switch_to;
	
	return $current_url;
}

function pm_detect_crawler($ua = '') 
{
	// List: http://www.useragentstring.com/pages/Crawlerlist/
	
	$ua_bot_regex =   'googlebot|googlebot-image|mediapartners-google|adsbot-google|msnbot|msnbot-media|bingbot|yahoo|yahoo! slurp|yahoo! slurp china|'
					. 'yahooseeker|yahooseeker-testing|yandexbot|yandeximages|yandexmetrika|baidu transcoder|baiduspider|bloglines subscriber|'
					. 'charlotte|dotbot|linkwalker|sogou spider|sosoimagespider|'
					. 'sosospider|speedy spider|yeti|yodaobot|yodaobot-image|youdaobot|008|abachobot|accoona-ai-agent|addsugarspiderbot|anyapexbot|'
					. 'arachmo|b-l-i-t-z-b-o-t|becomebot|beslistbot|billybobbot|bimbot|blitzbot|boitho.com-dc|boitho.com-robot|btbot|catchbot|cerberian drtrs|converacrawler|cosmos|covario ids|'
					. 'dataparksearch|diamondbot|discobot|earthcom.info|emeraldshield.com webbot|envolk[its]spider|esperanzabot|exabot|'
					. 'fast enterprise crawler|fast-webcrawler|fdse robot|findlinks|furlbot|fyberspider|g2crawler|gaisbot|galaxybot|'
					. 'geniebot|gigabot|girafabot|gurujibot|happyfunbot|hl_ftien_spider|holmes|htdig|iaskspider|ia_archiver|iccrawler|ichiro|igdespyder|'
					. 'irlbot|issuecrawler|jaxified bot|jyxobot|koepabot|l.webis|lapozzbot|larbin|ldspider|lexxebot|'
					. 'linguee bot|lmspider|lwp-trivial|mabontland|magpie-crawler|mj12bot|mlbot|mnogosearch|mogimogi|mojeekbot|moreoverbot|morning paper|msrbot|'
					. 'mvaclient|mxbot|netresearchserver|netseer crawler|newsgator|ng-search|nicebot|noxtrumbot|nusearch spider|nutchcvs|nymesis|'
					. 'obot|oegp|omgilibot|omniexplorer_bot|oozbot|orbiter|pagebiteshyperbot|peew|polybot|pompos|postpost|psbot|pycurl|qseero|radian6|rampybot|'
					. 'rufusbot|sandcrawler|sbider|scoutjet|scrubby|searchsight|seekbot|semanticdiscovery|sensis web crawler|seochat::bot|seznambot|shim-crawler|shopwiki|'
					. 'shoula robot|silk|sitebot|snappy|sogou spider|sqworm|stackrambler|suggybot|surveybot|synoobot|teoma|terrawizbot|thesubot|thumbnail.cz robot|'
					. 'tineye|truwogps|turnitinbot|tweetedtimes bot|twengabot|updated|urlfilebot|vagabondo|voilabot|vortex|voyager|vyu2|webcollage|websquash.com|wf84|'
					. 'wofindeich robot|womlpefactory|xaldon_webspider|yacy|yasaklibot|yooglifetchagent|zao|zealbot|zspider|zyborg';

	$ua_bot_regex = strtolower($ua_bot_regex);
	
	$ua = ($ua == '') ? strtolower($_SERVER['HTTP_USER_AGENT']) : strtolower($ua); 

	return preg_match("/$ua_bot_regex/", $ua);
}

function iso8601_duration($time) {
    $units = array(
        "Y" => 365*24*3600,
        "D" =>     24*3600,
        "H" =>        3600,
        "M" =>          60,
        "S" =>           1,
    );

    $str = "P";
    $istime = false;

    foreach ($units as $unitName => &$unit) {
        $quot  = intval($time / $unit);
        $time -= $quot * $unit;
        $unit  = $quot;
        if ($unit > 0) {
            if (!$istime && in_array($unitName, array("H", "M", "S"))) { // There may be a better way to do this
                $str .= "T";
                $istime = true;
            }
            $str .= strval($unit) . $unitName;
        }
    }

    return $str;
}
