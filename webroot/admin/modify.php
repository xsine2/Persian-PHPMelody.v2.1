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

$showm = '2';
/*
$load_uniform = 0;
$load_ibutton = 0;
$load_tinymce = 0;
$load_swfupload = 0;
$load_colorpicker = 0;
$load_prettypop = 0;
$load_tagsinput = 0;
*/
$load_scrolltofixed = 1;
$load_chzn_drop = 1;
$load_tagsinput = 1;
$load_uniform = 1;
$load_tinymce = 1;
$load_swfupload = 1;
$load_swfupload_upload_image_handlers = 1;
$_page_title = 'Edit video';
include('header.php');
include('../include/ffmpeg.php');

$message = '';

$uniq_id	=	$_GET['vid'];
$action		=	(int) $_GET['a'];
$page		=	(int) $_GET['page'];

if(empty($action))	$action = 0;
if(empty($page))	$page	= 0;

if(empty($uniq_id))
{
	$message = "<div class=\"alert alert-error\">Missing video ID parameter from URL.</div>";
	echo '<div id="adminPrimary">
			<div id="content">';
	echo $message;
	echo '</div></div>';
	include('footer.php');
	exit();
} else {

if($_GET['Action'] == 'NewScreen') {
	
	$sql_fetch = mysql_fetch_array(mysql_query("SELECT * FROM pm_videos WHERE uniq_id = '". $uniq_id ."'"));
    if($sql_fetch['yt_thumb'] == '') {
		$NewScreen = new ffmpeg();
		$NewScreen->ScreenShot($sql_fetch['url_flv'],'false');
		$IMG_NAME = base64_encode($sql_fetch['url_flv']).'_thumb.jpg';
		mysql_query("UPDATE pm_videos SET yt_thumb = '".$IMG_NAME."' WHERE uniq_id = '". $uniq_id ."'");
	} else {
		unlink('../uploads/thumbs/'.$sql_fetch['yt_thumb']);
		$NewScreen = new ffmpeg();
		$NewScreen->ScreenShot($sql_fetch['url_flv'],'false');
		$IMG_NAME = base64_encode($sql_fetch['url_flv']).'_thumb.jpg';
		mysql_query("UPDATE pm_videos SET yt_thumb = '".$IMG_NAME."' WHERE uniq_id = '". $uniq_id ."'");		
	}
		
} }

$modframework->trigger_hook('admin_modify_file_pre');
$my_tags = a_get_video_tags($uniq_id);
$my_tags_str = '';
foreach($my_tags as $k => $arr)
{
	$my_tags_str .= $arr['tag'] . ", ";
}
$my_tags_str = substr($my_tags_str, 0, -2);


if($_POST['submit'] != '')
{
	$stream = false;
	$input = $c_inc = $c_dec = array();
	$modframework->trigger_hook('admin_modify_save_start');
	define('PHPMELODY', true);
	$sources = a_fetch_video_sources();
	
	foreach($_POST as $k => $v)
	{
		if ( ! is_array($v))
		{
			$input[$k] = secure_sql(trim($v));
		}
		else
		{
			$input[$k] = $v;
		}
	}
	if( is_array($_POST['category']) ){
		$categories = implode(",", $_POST['category']);
	}
	else 
		$categories = $_POST['category'];
	
	if (strcmp($categories, $_POST['categories_old']) != 0)
	{
		$buffer_new = $buffer_old = array();
		$buffer_new = explode(',', $categories);
		$buffer_old = explode(',', $_POST['categories_old']);
		
		foreach ($buffer_new as $k => $cid)
		{
			if ( ! in_array($cid, $buffer_old))
			{
				$c_inc[] = $cid;
			}
		}
		foreach ($buffer_old as $k => $cid)
		{
			if ( ! in_array($cid, $buffer_new))
			{
				$c_dec[] = $cid;
			}
		}
	}

	$description			=	$input['description'];
	$input['video_title']	=	html_entity_decode($input['video_title']);
	$input['video_title']	=	str_replace( array("<", ">"), array("&lt;", "&gt;"), $input['video_title']);
	$input['featured'] 		=	(int) $_POST['featured'];
	$input['allow_comments'] = (int) $_POST['allow_comments'];
	$input['source_id']		= 	(int) $_POST['source_id'];
	
	
	if (strlen($input['embed_code']) > 0)
	{
		$input['embed_code'] = $_POST['embed_code'];
		if (ini_get('magic_quotes_gpc') == 1)
		{
			$input['embed_code'] = stripslashes($input['embed_code']);
		}
		
		$input['embed_code'] = str_replace(array("'", "\n", "\r"), array('"', '', ''), $input['embed_code']);
		
		//	remove extra html tags
		$input['embed_code'] = strip_tags($input['embed_code'], '<iframe><embed><object><param><video><script>');
		
		//	remove left-overs
		if (strpos($input['embed_code'], '<object') !== false)
		{
			$input['embed_code'] = preg_replace('/\/object>(.*)/i', '/object>', $input['embed_code']);
		}

		//	replace width, height and wmode values with variables
		$input['embed_code'] = preg_replace('/width="([0-9]+)"/i', 'width="%%player_w%%"', $input['embed_code']);
		$input['embed_code'] = preg_replace('/height="([0-9]+)"/i', 'height="%%player_h%%"', $input['embed_code']);
		$input['embed_code'] = preg_replace('/value="(window|opaque|transparent)"/i', 'value="%%player_wmode%%"', $input['embed_code']);
		$input['embed_code'] = preg_replace('/wmode="(.*?)"/i', 'wmode="%%player_wmode%%"', $input['embed_code']);
		$input['embed_code'] = preg_replace('/width=([0-9]+)/i', 'width=%%player_w%%', $input['embed_code']);
		$input['embed_code'] = preg_replace('/height=([0-9]+)/i', 'height=%%player_h%%', $input['embed_code']);
		
		$input['embed_code'] = secure_sql($input['embed_code']);
		
		if ($input['source_id'] > 0)
			$input['source_id'] = 0;
	}
	else if ($input['source_id'] == 0 && array_key_exists('embed_code', $_POST) && strlen($input['embed_code']) == 0 && strlen($input['jw_file']) == 0)
	{
		if (strlen($input['url_flv']) > 0)
		{
			$allowed_ext = array('.flv', '.mp4', '.mov', '.wmv', '.divx', '.avi', '.mkv', 
								'.asf', '.wma', '.mp3', '.m4v', '.m4a', '.3gp', '.3g2');
			$last_4_chars = substr($input['url_flv'], strlen($input['url_flv'])-4, strlen($input['url_flv']));
			
			if(in_array($last_4_chars, $allowed_ext) && (preg_match('/photobucket\.com/', $input['url_flv']) == 0))
			{
				if(strpos($input['url_flv'], _URL) !== false)
				{
					$input['source_id'] = 1;
				}
				else
				{
					$input['source_id'] = 2;
				}
			}
		}
		else
		{
			$input['source_id'] = 1234;
		}
		
		$sql = "DELETE FROM pm_embed_code WHERE uniq_id = '". $_POST['uniq_id'] ."'";
		mysql_query($sql);
	}

	if($description != '')
	{
		if((strlen($description) == 4) && ($description == "<br>"))
		{
			$description = '';
		}
	}
	
	if($input['tags'] != '')
	{
		$tags = explode(",", $input['tags']);

		//	remove duplicate tags and 'empty' tags
		$temp = array();
		for($i = 0; $i < count($tags); $i++)
		{
			if($tags[$i] != '')
				if($i <= (count($tags)-1))
				{
					$found = 0;
					for($j = $i + 1; $j < count($tags); $j++)
					{
						if(strcmp($tags[$i], $tags[$j]) == 0)
							$found++;
					}
					if($found == 0)
						$temp[] = $tags[$i];
				}
		}
		$tags = $temp;

		$tags_insert = array();
		foreach($tags as $k => $tag)
		{
			//	handle mistakes
			$tag = stripslashes(trim($tag));
			$tags[$k] = $tag;
			if($tag != '' && (strlen($tag) > 0))
			{
				//	new tags vs old tags
				$found = 0;
				$safe_tag = safe_tag($tag);
				
				foreach($my_tags as $key => $arr)
				{
					if(in_array($safe_tag, $arr))
						$found++;
				}
				if($found == 0)
					$tags_insert[] = $tag;
			}
		}
		//	were there any tags changed or removed?
		$remove_tags = array();
		foreach($my_tags as $k => $v)
		{
			if(in_array($v['tag'], $tags) === false)
			{
				$remove_tags[] = $v['tag_id'];
			} 
		}
		//	insert new tags in database
		if(count($tags_insert) > 0)
		{
			insert_tags($_POST['uniq_id'], $tags_insert);
		}
		
		//	remove left-out tags
		if(count($remove_tags) > 0)
		{
			$this_arr = '';
			$this_arr = implode(",", $remove_tags);
			$sql2 = "DELETE FROM pm_tags WHERE tag_id IN(".$this_arr.")";
			$result2 = mysql_query($sql2);	// or die(mysql_error());
		}
	}
	elseif(($input['tags'] == '') && (strlen($my_tags_str) > 0))
	{
		//	remove all tags for this video
		$sql = "DELETE FROM pm_tags WHERE uniq_id = '".$_POST['uniq_id']."'";
		$result = mysql_query($sql);
		if(!$result)
			echo "<div class=\"alert alert-error\">Sorry, something went wrong: <strong>tags cannot be updated</strong>!</div>";
	}
	
	//	Reset tags so that they show up nice and updated in the form
	$my_tags = a_get_video_tags($uniq_id);
	$my_tags_str = '';
	
	foreach($my_tags as $k => $arr)
	{
		$my_tags_str .= $arr['tag'] . ", ";
	}
	$my_tags_str = substr($my_tags_str, 0, -2);
	$yt_length = ($input['yt_min'] * 60) + $input['yt_sec'];
	$modframework->trigger_hook('admin_modify_save_pre_save');

	if ($input['yt_thumb'] != $input['yt_thumb_old'] && strpos($input['yt_thumb'], 'http') !== false)
	{
		$input['yt_thumb'] = str_replace('https://', 'http://', $input['yt_thumb']);
		define('PHPMELODY', true);
		require_once( './src/youtube.php'); // just for download_thumb()
		$img = download_thumb($input['yt_thumb'], _THUMBS_DIR_PATH, $uniq_id, true);
	}
	
	if ($input['yt_thumb_local'] != '')
	{
		$input['yt_thumb'] = $input['yt_thumb_local'];
	}
	
	if ($input['video_slug'] == '')
	{
		$input['video_slug'] = $input['video_title'];
	}
	$input['video_slug'] = sanitize_title($input['video_slug']);

	$sql = "UPDATE pm_videos 
			SET video_title = '". $input['video_title'] ."', 
				submitted = '". $input['submitted'] ."',  
				category= '". $categories ."', 
				description = '". $description ."',
				yt_thumb = '". $input['yt_thumb'] ."',
				language = '". $input['language'] ."',
				video_slug = '". $input['video_slug'] ."'";

	// update site_views only if the input value has changed
	if ($input['site_views'] != $input['site_views_input'])
	{
		$input['site_views_input'] = abs((int) $input['site_views_input']); // positive values only		
		$sql .= ", site_views = '". $input['site_views_input'] ."'";
	}
	
	if ($yt_length != $input['yt_length'])
	{
		$sql .= ", yt_length = '". $yt_length ."'";
	}
	
	if($input['source_id'] > 2 || ($input['source_id'] == 0 && $input['embed_code'] == '' && $input['jw_file'] == ''))
	{
		//	was the Direct link to video changed?
		if(strcmp($input['direct'], $input['direct-original']) != 0)
		{
			$use_this_src = -1;

			if ($sources === false || count($sources) == 0)
			{
				$use_this_src = $input['source_id'];
			}
			else
			{
				if (strpos($input['direct'], _URL) !== false)
				{
					$use_this_src = (int) $sources['localhost']['source_id'];
				}
				else if (@preg_match($sources['other']['source_rule'], $input['direct']) != 0)
				{
					$use_this_src = (int) $sources['other']['source_id'];
				}
				else
				{
					foreach ($sources as $src_id=>$source)
					{
						if ($source['source_id'] != 1 && $source['source_id'] != 2)
						{
							if (@preg_match($source['source_rule'], $input['direct']))
							{
								$use_this_src = $source['source_id'];
								break;
							}
						}
					}
				}
			}

			if ($use_this_src == -1)
			{
				if (strpos($input['direct'], _URL) !== false)
				{
					$use_this_src = (int) $sources['localhost']['source_id'];
				}
				else if (@preg_match($sources['other']['source_rule'], $input['direct']) != 0)
				{
					$use_this_src = (int) $sources['other']['source_id'];
				}
				else
				{
					$use_this_src = (int) $input['source_id'];	
				}
			}

			$input['source_id'] = $use_this_src;

			require_once( "./src/" . $sources[ $use_this_src ]['source_name'] . ".php");
			
			$temp = array();
			do_main($temp, $input['direct']);
			
			$sql .= ", yt_id = '".$temp['yt_id']."'";
			
			if ($use_this_src == 1)
			{
				if (strpos($input['direct'], _VIDEOS_DIR) !== false)
				{
					$tmp_parts = explode('/', $input['direct']);
					$filename = array_pop($tmp_parts);
					
					$temp['url_flv'] = $filename;
					$input['direct'] = $filename;
				}
				else
				{
					$temp['url_flv'] = $input['direct'];
					$use_this_src = 2;
				}
			}
			else if ($use_this_src == 2)
			{
				if ( ! is_url($input['direct']))
				{
					$use_this_src = 1;
				}
				$temp['url_flv'] = $input['direct'];
			}
			
			$input['source_id'] = $use_this_src;
 
			$sql .= ", url_flv = '". $temp['url_flv'] ."'";
			$sql .= ", source_id = '". $input['source_id'] ."'";
			$sql .= ", status = '0'";

			if(empty($input['direct-original']))
			{
				$sql2 = "INSERT INTO pm_videos_urls (uniq_id, direct) VALUES ('".$_POST['uniq_id']."', '". $input['direct'] ."')";
				$result = mysql_query($sql2);		
			}
			else
			{
				$sql2 = "UPDATE pm_videos_urls SET direct='".$input['direct']."' WHERE uniq_id='". $_POST['uniq_id'] ."'";
				$result = mysql_query($sql2);			
			}
			unset($temp, $sql2);
		}
		//	was the flv location changed?
		elseif(strcmp($input['url_flv'], $input['url_flv-original']) != 0  && $input['source_id'] > 0)
		{
			$use_this_src = -1;
			
			if ($sources === false || count($sources) == 0)
			{
				$use_this_src = $input['source_id'];
			}
			else
			{
				if (strpos($input['direct'], _URL) !== false)
				{
					$use_this_src = (int) $sources['localhost']['source_id'];
				}
				else if (@preg_match($sources['other']['source_rule'], $input['direct']) != 0)
				{
					$use_this_src = (int) $sources['other']['source_id'];
				}
				else
				{
					foreach ($sources as $src_id=>$source)
					{
						if ($source['source_id'] != 1 && $source['source_id'] != 2)
						{
							if (@preg_match($source['source_rule'], $input['url_flv']))
							{
								$use_this_src = $source['source_id'];
								break;
							}
						}
					}
				}
			}
			
			if ($use_this_src == -1)
			{
				if (strpos($input['url_flv'], _URL) !== false)
				{
					$use_this_src = $sources['localhost']['source_id'];
				}
				else if (@preg_match($sources['other']['source_rule'], $input['url_flv']) != 0)
				{
					$use_this_src = $sources['other']['source_id'];
				}
				else
				{
					$use_this_src = $input['source_id'];	
				}
			}
			
			$input['source_id'] = $use_this_src;
			
			$yt_id = '';
			if(preg_match("/youtube\.com/i", $input['url_flv']))
			{
				preg_match("/video_id=([^(\&|$)]*)/i", $input['url_flv'], $matches);
				$yt_id = $matches[1];
				unset($matches);
			}
			
			if($yt_id != '')
			{
				$sql .= ", yt_id = '".$yt_id."'";
			}
			$sql .= ", url_flv = '".$input['url_flv']."'";
			$sql .= ", source_id = '".$input['source_id']."'";
		}
	}
	else if ($input['source_id'] == 0 && array_key_exists('jw_file', $_POST) && $input['embed_code'] == '')
	{
		$stream = true;
		$input['url_flv'] = trim($_POST['jw_file']) .';'. trim($_POST['jw_streamer']);
		
		$sql .= ", url_flv = '". $input['url_flv'] ."'";
	}
	else
	{
		$source_id = $input['source_id'];
		
		if(strcmp($input['url_flv'], $input['url_flv-original']) != 0)
		{
			$input['direct'] = $input['url_flv'];
			$use_this_src = -1;

			if ($sources === false || count($sources) == 0)
			{
				$use_this_src = $input['source_id'];
			}
			else
			{
				foreach ($sources as $src_id=>$source)
				{
					if ($source['source_id'] != 1 && $source['source_id'] != 2)
					{
						if (@preg_match($source['source_rule'], $input['direct']))
						{
							$use_this_src = $source['source_id'];
							break;
						}
					}
				}
			}

			if ($use_this_src == -1)
			{
				if (strpos($input['direct'], _URL) !== false)
				{
					$use_this_src = $sources['localhost']['source_id'];
				}
				else if (@preg_match($sources['other']['source_rule'], $input['direct']) != 0)
				{
					$use_this_src = $sources['other']['source_id'];
				}
				else
				{
					$use_this_src = $input['source_id'];	
				}
			}
			$source_id = $use_this_src;
			$input['source_id'] = $use_this_src;

			require_once( "./src/" . $sources[ $use_this_src ]['source_name'] . ".php");
			
			$input['url_flv'] = ($temp['url_flv'] == '') ? $input['url_flv'] : $temp['url_flv'];
			
			$temp = array();
			do_main($temp, $input['direct']);
			$sql .= ", yt_id = '". $temp['yt_id'] ."'";
			$sql .= ", url_flv = '". $input['url_flv'] ."'";
			//$sql .= ", source_id = '".$input['source_id']."'";
			$sql .= ", status = '0'";
			
			
			if ($input['source_id'] == 2) 
			{
				$sql2 = "INSERT INTO pm_videos_urls (uniq_id, direct) VALUES ('". $input['uniq_id'] ."', '". $input['direct'] ."')";
				$result = mysql_query($sql2);
			}
			else
			{
				$sql2 = "UPDATE pm_videos_urls SET direct='".$input['direct']."' WHERE uniq_id='". $input['uniq_id'] ."'";
				$result = mysql_query($sql2);			
			}
			unset($temp, $sql2);
			
			//$sql .= ", url_flv = '".$input['url_flv']."'";
			
			if ( ! is_url($input['url_flv']) && ! is_ip_url($input['url_flv']) && is_file(_VIDEOS_DIR_PATH . $input['url_flv']))
			{
				$source_id = 1;
			}
		}
		
		$sql .= ", source_id = '". $source_id ."'";
	}
	
	$sql .= ", featured = '".$input['featured']."'";
	$sql .= ", restricted = '".$input['restricted']."'";
	$sql .= ", allow_comments = '".$input['allow_comments']."'";
	
	$modframework->trigger_hook('admin_modify_save_pre_save_final');
	
	$added = validate_item_date($_POST);
	if ($added === false)
	{
		$message = "<div class=\"alert alert-error\">Invalid publish date provided.</div>";
		$result = false;
	}
	else
	{
		$sql .= ", added = '". pm_mktime($added) ."'";
		$sql .= " WHERE uniq_id= '".$_POST['uniq_id']."' LIMIT 1";
		$result = @mysql_query($sql);
	}

	if ( ! $result)
	{
		if (empty($message))
		{
			$message = "<div class=\"alert alert-error\">Something went wrong. Update failed!<br />MySQL returned this error: ".mysql_error()."</div>";
		}
	}
	else
	{	
		if (count($c_inc) > 0)
		{
			$str = implode(',', $c_inc);
			$sql = "UPDATE pm_categories SET total_videos=total_videos+1 ";
			$sql .= ($added <= time()) ? ", published_videos = published_videos + 1 " : '';
			$sql .= " WHERE id IN (". $str .")";
			mysql_query($sql);
			unset($str);
		}
		if (count($c_dec) > 0)
		{
			$str = implode(',', $c_dec);
			$sql = "UPDATE pm_categories SET total_videos=total_videos-1 ";
			$sql .= ($added <= time()) ? ", published_videos = published_videos - 1 " : '';
			$sql .= " WHERE id IN (". $str .")";
			mysql_query($sql);
			unset($str);
		}
		if (strlen($input['embed_code']) > 0)
		{
			if (count_entries('pm_embed_code', 'uniq_id', $_POST['uniq_id']) > 0)
			{
				$sql = "UPDATE pm_embed_code SET embed_code = '". $input['embed_code'] ."' WHERE uniq_id = '". $_POST['uniq_id'] ."'";
			}
			else
			{
				$sql = "INSERT INTO pm_embed_code (uniq_id, embed_code) VALUES ('". $_POST['uniq_id'] ."', '". $input['embed_code'] ."')";
			}
			mysql_query($sql);
		}
		
		if ($stream)
		{
			$jw_flashvars = array();
			
			$jw_flashvars['provider'] 			= $_POST['jw_provider'];
			if ($jw_flashvars['provider'] == 'rtmp')
			{
				$jw_flashvars['loadbalance'] 	= $_POST['jw_rtmp_loadbalance'];
				$jw_flashvars['subscribe'] 	= $_POST['jw_rtmp_subscribe'];
				$jw_flashvars['securetoken'] 	= $_POST['jw_securetoken'];
			}
			else if ($jw_flashvars['provider'] == 'http')
			{
				$jw_flashvars['startparam'] 	= trim($_POST['jw_startparam']);
			}
			
			$jw_flashvars = (string) serialize($jw_flashvars);
			$sql = "UPDATE pm_embed_code SET embed_code =  '". secure_sql($jw_flashvars) ."' WHERE uniq_id = '". $_POST['uniq_id'] ."'";
			$result = mysql_query($sql);
		}
		
		$message = '<div class="alert alert-success"><strong>The video was updated successfully!</strong> <a href="'. _URL .'/watch.php?vid='. $_POST['uniq_id'] .'" target="_blank" title="Watch video">Watch this video</a></div>';
		$modframework->trigger_hook('admin_modify_save_post_save');
	}
}
elseif($_POST['submit'] == "Update" && empty($_POST['categories']))
{
	$message = "<div class=\"alert alert-error\">Please select a category for this video.</div>";
}

if($message != '')
	$message;

$type = 'video';

if (strpos($uniq_id, 'article') !== false)
{	
	$pieces = explode('-', $uniq_id);
	$id = (int) $pieces[1];

	$query = mysql_query("SELECT * FROM art_articles WHERE id = '".$id."'");
	$type = 'article';
}
else
{
	$query = mysql_query("SELECT * FROM pm_videos WHERE uniq_id = '".$uniq_id."'");
}
$count = mysql_num_rows($query);

if ($count == 0) 
{
	?>
	<div id="adminPrimary">
    <div class="content">
	<h2>Update Video</h2>
	<div class="alert alert-error">The requested video was not found.</div>
	</div>
	<?php
	include('footer.php');
	exit();
}
if ($type == 'video')
{
	$r = mysql_fetch_assoc($query);
	$query2 = mysql_query("SELECT mp4, direct FROM pm_videos_urls WHERE uniq_id = '".$uniq_id."'");
	$r_extent = mysql_fetch_assoc($query2);

	mysql_free_result($query);
	mysql_free_result($query2);
	
	if($r['source_id'] == 0 || $r['source_id'] != 1 || $r['source_id'] != 2)
	{
		$row = array();
		$sql = "SELECT * FROM pm_embed_code WHERE uniq_id = '". $uniq_id ."'";
		$result = mysql_query($sql);
		$row = mysql_fetch_assoc($result);
		mysql_free_result($result);
		if (is_array($row))
		{
			if (is_serialized($row['embed_code']))
			{
				$r['jw_flashvars'] = unserialize($row['embed_code']);
			}
			else
			{
				$r = array_merge($r, $row);
			}
		}
		unset($row);
	}
}
else
{
	$r_extend = array();
}

if(is_array($r_extent))
	$r = array_merge($r, $r_extent);

if ($r['video_slug'] == '')
{
	$r['video_slug'] = sanitize_title($r['video_title']).'-video';
}

switch($action)
{
	case 1:
		
		$row = request_video($uniq_id);
		
		$row['category'] = trim($row['category'], ',');
		
		$sql = "UPDATE pm_categories SET total_videos=total_videos-1 ";
		if ($row['added'] <= time())
		{
			$sql .= ", published_videos = published_videos - 1 ";
			update_config('published_videos', $config['published_videos']-1);
		}
		$sql .= " WHERE id IN (". $row['category'] .")";
		
		@mysql_query($sql);
		update_config('total_videos', $config['total_videos']-1);
		
		// REMOVE VIDEO AND EVERYTHING RELATED TO RESPECTIVE VIDEO
		@mysql_query("DELETE FROM pm_videos WHERE uniq_id = '".$uniq_id."'");
		@mysql_query("DELETE FROM pm_comments WHERE uniq_id = '".$uniq_id."'");
		@mysql_query("DELETE FROM pm_reports WHERE entry_id = '".$uniq_id."'");
		@mysql_query("DELETE FROM pm_videos_urls WHERE uniq_id = '".$uniq_id."'");
		@mysql_query("DELETE FROM pm_favorites WHERE uniq_id = '".$uniq_id."'");
		@mysql_query("DELETE FROM pm_chart WHERE uniq_id = '".$uniq_id."'");
		@mysql_query("DELETE FROM pm_tags WHERE uniq_id = '".$uniq_id."'");
		@mysql_query("DELETE FROM pm_embed_code WHERE uniq_id = '".$uniq_id."'");
		@mysql_query("DELETE FROM pm_bin_rating_meta WHERE uniq_id = '".$uniq_id."'");
		@mysql_query("DELETE FROM pm_bin_rating_votes WHERE uniq_id = '".$uniq_id."'");
		@mysql_query("DELETE FROM pm_meta WHERE item_id = ". $row['id'] ." AND item_type = ". IS_VIDEO);
		
		if ($row['source_id'] == 1) 
		{
			if (file_exists(_VIDEOS_DIR_PATH . $row['url_flv']) && strlen($row['url_flv']) > 0)
			{
				unlink(_VIDEOS_DIR_PATH . $row['url_flv']);					
			}
		}
		if (file_exists(_THUMBS_DIR_PATH . $row['uniq_id'] .'-1.jpg'))
		{
			unlink(_THUMBS_DIR_PATH . $row['uniq_id'] .'-1.jpg');
		}
		
		if (_MOD_SOCIAL)
		{
			remove_all_related_activity($row['id'], ACT_OBJ_VIDEO);
		}
		
		$pieces = explode('/', $_SERVER['HTTP_REFERER']);
		$pieces = explode('?', $pieces[ count($pieces)-1 ]);

		echo "<meta http-equiv=\"refresh\" content=\"0;videos.php?action=deleted&". $pieces[1] ."\" />";
		exit();
	break;
	case 2:
		
		if (csrfguard_check_referer('_admin_videos_listcontrols') || csrfguard_check_referer('_admin_articles'))
		{
			//	REMOVE COMMENTS ONLY 
			@mysql_query("DELETE FROM pm_comments WHERE uniq_id = '".$uniq_id."'");
			$response_type = 'deletedcomments';
		}
		else
		{
			$response_type = 'badtoken';
		}
		$pieces = explode('/', $_SERVER['HTTP_REFERER']);
		$pieces = explode('?', $pieces[ count($pieces)-1 ]);
		
		if ($type == 'video')
		{
			echo "<meta http-equiv=\"refresh\" content=\"0;videos.php?action=". $response_type ."&". $pieces[1] ."\" />";
		}
		else if ($type == 'article')
		{
			echo "<meta http-equiv=\"refresh\" content=\"0;articles.php?action=". $response_type ."&". $pieces[1] ."\" />";
		}
		exit();
	break;
}

$meta_data = get_all_meta_data($r['id'], IS_VIDEO);

?>
<script type="text/javascript">
	$(document).ready(function(){
		switch ($('select[name="jw_provider"]').val())
		{
			default:
			case '':
				$('.provider_http').hide();
				$('.provider_rtmp').hide();
			break;
			case 'rtmp':
				$('.provider_http').hide();
			break;
			case 'http':
				$('.provider_rtmp').hide();
			break;
			
		}
		
		$('select[name="jw_provider"]').change(function(){
			switch(($(this).val()))
			{
				default:
				case '':
					$('.provider_http').fadeOut('fast');
					$('.provider_rtmp').fadeOut('fast');
				break;
				case 'rtmp':
					$('.provider_http').hide();
					$('.provider_rtmp').fadeIn('slow');
				break;
				case 'http':
					$('.provider_rtmp').hide();
					$('.provider_http').fadeIn('slow');
				break;
			}
		});
	});
</script>
<div id="adminPrimary">
    <div class="content">
	<h2>Update Video</h2>

	<?php echo $message; ?>
    
<form name="update" enctype="multipart/form-data" action="modify.php?vid=<?php echo $uniq_id; ?>" method="post" onsubmit="return validateFormOnSubmit(this, 'Please fill in the required fields (highlighted)')">
<div class="container row-fluid" id="post-page">
    <div class="span9">
    <div class="widget border-radius4 shadow-div">
    <h4>Title &amp; Description</h4>
    <div class="control-group">
    <input name="video_title" type="text" id="must" value="<?php echo htmlspecialchars($r['video_title']); ?>" style="width: 99%;" />
    <div class="permalink-field">
	
	<?php if (_SEOMOD) : ?>
		<strong>Permalink:</strong> <?php echo _URL .'/';?><input class="permalink-input" type="text" name="video_slug" value="<?php echo urldecode($r['video_slug']);?>" /><?php echo  '_'. (($r['uniq_id'] == '') ? 'ID' : $r['uniq_id']) .'.html';?>
	<?php endif; ?>	
	
	
	</div>
    </div>
    
    <div class="control-group">
	<div class="pull-right" style=" position: absolute; top: -2px; right: 0px;">
	<span class="btn btn-mini btn-upload"><span id="ButtonPlaceHolder"></span></span>
    <small><div id="fsUploadProgress"></div></small>
    <div id="divStatus"></div>
	<ol id="uploadLog"></ol>
    </div>
    </div>
    
    <div class="controls">
    <textarea name="description" cols="100" id="textarea-WYSIWYG" class="tinymce" style="width:100%"><?php echo $r['description']; ?></textarea>
    <span class="autosave-message">&nbsp;</span>
    </div>
    </div>

	<?php if($r['source_id'] == 0 || $r['source_id'] != 1 || $r['source_id'] != 2) : ?>    
	<div class="widget border-radius4 shadow-div">
	<h4>Embed Code <i class="icon-info-sign" rel="popover" data-trigger="hover" data-animation="true" title="Info" data-content="Add or edit the embed code ONLY if you wish to change this video's source. Once an embed code is given, PHP Melody will consider it to be the default video."></i></h4>
    <div class="control-group">
    <div class="controls">
    <!-- 
    <strong>Direct link to video page</strong><br /><small>Optional field</small>
    <input type="text" name="direct" value="<?php echo $inputs['direct']; ?>" style="width: 500px;" />
    -->
    <textarea name="embed_code" rows="2" class="textarea-embed"><?php echo $r['embed_code']; ?></textarea>
    <span class="help-block">Accepted HTML tags: <strong>&lt;iframe&gt;</strong>  <strong>&lt;embed&gt;</strong> <strong>&lt;object&gt;</strong> and <strong>&lt;param&gt;</strong> (<em>For safety reasons JavaScript embedding is not supported</em>)</span>
	</div>
    </div>
	</div>
	<?php endif; ?>
    
	<div class="widget border-radius4 shadow-div" id="custom-fields">
	<h4>Custom Fields <a href="http://help.phpmelody.com/how-to-use-the-custom-fields/" target="_blank"><i class="icon-question-sign"></i></a></h4>
    	<div class="control-group">	
    	<?php if (count($meta_data) > 0) : ?>
		<div class="row-fluid">
			<div class="span3"><strong>Name</strong></div>
			<div class="span9"><strong>Value</strong></div>
		</div>
    	<?php	
			foreach ($meta_data as $meta_id => $meta) : 
					echo admin_custom_fields_row($meta_id, $meta);
			endforeach;
		endif; 
		?>
		
		<?php echo admin_custom_fields_add_form($r['id'], IS_VIDEO); ?>
		
		</div>
	</div>
	
    </div><!-- .span8 -->
    <div class="span3">

		<div class="widget border-radius4 shadow-div">
		<div class="pull-right"><span class="btn btn-mini btn-upload-widget"><span id="thButtonPlaceholder"></span></span></div>
		<h4>تصویر ویدیو :: <a href="modify.php?vid=<?php echo $uniq_id; ?>&Action=NewScreen">ساخت تصویر جدید</a></h4>
            <div class="control-group container-fluid">
            <div class="controls row-fluid">
			<small><div id="thUploadProgress"></div></small>
            <div class="pm-swf-upload">
                <ol id="uploadThumbLog"></ol>
            </div>
            <div id="showThumb">
			<?php
            if (($r['source_id'] == 0 || $r['source_id'] == 1 || $r['source_id'] == 2) && strpos($r['yt_thumb'], 'http') === false && $r['yt_thumb'] != '') 
			{
				$r['yt_thumb'] = _URL."/uploads/thumbs/". $r['yt_thumb'];
			}
			if (empty($r['yt_thumb'])) : ?>
            <a href="#" id="show-thumb" rel="tooltip" title="Click here to specify a custom thumbnail URL" class="pm-sprite no-thumbnail"></a>
            <?php else : ?>
            <a href="#" id="show-thumb" rel="tooltip" title="Click here to specify a custom thumbnail URL"><img src="<?php echo $r['yt_thumb']; ?>?cache_buster=<?php echo time();?>" id="must" style="display:block;min-width:120px;width:100%;min-height:80px; no-repeat center center;" /></a>
            <?php endif; ?>
            <div class="">
                <div id="show-opt-thumb">
                <br />
                <input type="text" name="yt_thumb" value="<?php echo $r['yt_thumb']; ?>" class="bigger span10" placeholder="http://" /> <i class="icon-info-sign" rel="tooltip" data-position="top" title="The thumbnail will refresh after you hit the 'Submit' button."></i>
				<input type="hidden" name="yt_thumb_old" value="<?php echo $r['yt_thumb']; ?>" class="bigger span10" placeholder="http://" />
                </div>
            </div><!-- .span8 -->
            </div>
			<div class="">
            </div><!-- .span4 -->
			
            </div><!-- .controls .row-fluid -->
            </div>
        </div><!-- .widget -->

		<?php if ($r['source_id'] == 1) : ?>
		<div class="widget border-radius4 shadow-div">
		<div class="pull-right"><span class="btn btn-mini btn-upload-widget"><span id="changeFlvButtonPlaceholder"></span></span></div>
		<h4>Source File</h4>
			<div class="control-group container-fluid">
	            <div class="controls row-fluid">
				<small><div id="changeFlvUploadProgress"></div></small>
	            <div class="pm-swf-upload">
	                <ol id="uploadFlvLog"></ol>
	            </div>            
	            <div id="showFlv">
	            	<span class="pull-right">
	            		<i class="icon-download opac7"></i> <strong><a href="<?php echo _VIDEOS_DIR . $r['url_flv']; ?>" title="Download file">Download</a></strong>
	            	</span>
					<strong><?php echo $r['url_flv']; ?></strong>
	            </div>
				</div>
			</div>
		</div>
		<?php endif; ?>
		
		<div class="widget border-radius4 shadow-div">
		<h4>Category</h4>
            <div class="control-group">
            <div class="controls">
            <input type="hidden" name="categories_old" value="<?php echo $r['category'];?>"  />
            <?php 
			$categories_dropdown_options = array(
											'attr_name' => 'category[]',
											'attr_id' => 'main_select_category must',
											'attr_class' => 'category_dropdown span12',
											'select_all_option' => false,
											'spacer' => '&mdash;',
											'selected' => explode(',', $r['category']),
											'other_attr' => 'multiple="multiple"'
											);
			echo categories_dropdown($categories_dropdown_options);
            ?>
            </div>
			<a href="#" id="inline_add_new_category" />+ Add New Category</a>
			<div id="inline_add_new_category_form" class="hide">
				<input name="add_category_name" type="text" placeholder="Category name" id="add_category_name" />
				<input name="add_category_slug" type="text" placeholder="Slug" /> <a href="#" rel="tooltip" title="Slugs are used in the URL and can only contain numbers, letters, dashes and underscores."><i class="icon-info-sign" rel="tooltip" title="Slugs are used in the URL and can only contain numbers, letters, dashes and underscores."></i></a>
				<label>Create in (<em>optional</em>)</label>
				<?php 
					$categories_dropdown_options = array(
											'first_option_text' => '&ndash; Parent Category &ndash;', 
											'first_option_value' => '-1',
											'attr_name' => 'add_category_parent_id',
											'attr_id' => '',
											'attr_class' => '',
											'select_all_option' => true,
											'spacer' => '&mdash;'
											);
					echo categories_dropdown($categories_dropdown_options); 
				?>
				<br />
				<button name="add_category_submit_btn" value="Add category" class="btn btn-mini btn-normal" />Add New Category</button>
				<span id="add_category_response"></span>
			</div>
            </div>
		</div><!-- .widget -->
        
        <div class="widget border-radius4 shadow-div">
        <h4>Publish</h4>
			<?php
            if($r['yt_length'] > 0) {	
                $yt_minutes = intval($r['yt_length'] / 60);
                $yt_seconds = intval($r['yt_length'] % 60); 
            } else {
                $yt_minutes = 0;
                $yt_seconds = 0;
            }
            ?>
            <div class="control-group">
            <label class="control-label" for="">Duration: <span id="value-yt_length"><strong><?php echo sec2min($r['yt_length']);?></strong></span> <a href="#" id="show-duration">Edit</a></label>
            <div class="controls" id="show-opt-duration">
            <input type="text" name="yt_min" id="yt_length" value="<?php echo $yt_minutes; ?>" size="4" class="smaller-select" /> <small>min.</small>
            <input type="text" name="yt_sec" id="yt_length" value="<?php echo $yt_seconds; ?>" size="3" class="smaller-select" /> <small>sec.</small>
            <input type="hidden" name="yt_length" id="yt_length" value="<?php echo trim(($yt_minutes * 60) + $yt_seconds); ?>" />
            </div>
            </div>

			<div class="control-group">
            <label class="control-label" for="">Comments: <span id="value-comments"><strong><?php echo ($r['allow_comments'] == 1) ? 'allowed' : 'closed';?></strong></span> <a href="#" id="show-comments">Edit</a></label>
            <div class="controls" id="show-opt-comments">
                <label><input name="allow_comments" id="allow_comments" type="checkbox" value="1" <?php if ($r['allow_comments'] == 1) echo 'checked="checked"';?> /> Allow comments on this video</label>
				<?php if ($config['comment_system'] == 'off') : ?>
				<div class="alert">
				Comments are disabled site-wide. 
				<br />
				To enable comments, visit the <a href="settings.php?view=comment" title="Settings page" target="_blank">Settings</a> page.
				</div>
				<?php endif;?>
				
            </div>
            </div>
		    
            <div class="control-group">
            <label>Featured: <span id="value-featured"><strong><?php echo ($r['featured'] == 1) ? 'yes' : 'no';?></strong></span> <a href="#" id="show-featured">Edit</a></label>
            <div class="controls" id="show-opt-featured">
                <label><input type="checkbox" name="featured" id="featured" value="1" <?php if($r['featured'] == 1) echo 'checked="checked"';?> /> Yes, mark as featured</label>
            </div>
            </div>

            <div class="control-group">
            <label class="control-label" for="">Requires registration: <span id="value-register"><strong><?php echo ($r['restricted'] == 1) ? 'yes' : 'no';?></strong></span> <a href="#" id="show-visibility">Edit</a></label>
            <div class="controls" id="show-opt-visibility">
                <label class="checkbox inline"><input type="radio" name="restricted" id="restricted" value="1" <?php if ($r['restricted'] == 1) echo 'checked="checked"'; ?> /> Yes</label>
		<label class="checkbox inline"><input type="radio" name="restricted" id="restricted" value="0" <?php if ($r['restricted'] == 0) echo 'checked="checked"'; ?> /> No</label>
            </div>
            </div>

            <div class="control-group">
            <label class="control-label" for="">Views: <span id="value-views"><strong><?php echo $r['site_views'];?></strong></span> <a href="#" id="show-views">Edit</a></label>
            <div class="controls" id="show-opt-views">
            <input type="hidden" name="site_views" value="<?php echo $r['site_views'];?>" />
            <input type="text" name="site_views_input" id="site_views_input" value="<?php echo $r['site_views']; ?>" size="10" class="bigger span4" />
            </div>
            </div>

            <div class="control-group">
            <label class="control-label" for="">Submitted by: <span id="value-submitted"><strong><?php echo htmlspecialchars($r['submitted']); ?></strong></span> <a href="#" id="show-user">Edit</a></label>
            <div class="controls" id="show-opt-user">
            <input type="text" name="submitted" id="submitted" value="<?php echo htmlspecialchars($r['submitted']); ?>" class="bigger span4" />
            </div>
            </div>

            <div class="control-group">
            <label class="control-label" for="">Published: <span id="value-publish"><strong><?php echo date("M d, Y g:i A", $r['added']);?></strong></span> <a href="#" id="show-publish">Edit</a></label>
            <div class="controls" id="show-opt-publish">
            <?php echo show_form_item_date($r['added']);?>
            </div>
            </div>
            <?php 
  		$modframework->trigger_hook('admin_modify_publishfields');
  ?>
        </div><!-- .widget -->

		<?php if ($r['source_id'] != 1) : ?>
		<div class="widget border-radius4 shadow-div">
		<h4>Video Source</h4>
		<?php
	   if ($r['source_id'] == 0 && is_array($r['jw_flashvars'])) :
	//     if ($r['source_id'] != 0) :
	        $pieces = explode(';', $r['url_flv'], 2);
	    ?>
	    <div class="control-group">
	    <label>File <i class="icon-info-sign" rel="popover" data-trigger="hover" data-animation="true" title="" data-content="Internal URL of video or audio file you want to stream.<br />This is the equivalent of JW Player's <code><i>file</i></code> flashvar. "></i> <a href="#" id="show-vs1">Edit</a></label>
	    <div class="controls" id="show-opt-vs1">
	    <input name="jw_file" type="text" id="must" value="<?php echo $pieces[0]; ?>" class="bigger span12" />
	    </div>
	    </div>
	    
	    <div class="control-group">
	    <label>Streamer <i class="icon-info-sign" rel="popover" data-trigger="hover" data-animation="true" title="" data-content="Location of an RTMP or HTTP server instance to use for streaming."></i> <a href="#" id="show-vs2">Edit</a></label>
	    <div class="controls" id="show-opt-vs2">
	    <input name="jw_streamer" type="text" id="must" value="<?php echo $pieces[1]; ?>" class="bigger span12" />
	    </div>
	    </div>
	    
	    <div class="control-group">
	    <label>Provider (<small>Optional</small>) <i class="icon-info-sign" rel="popover" data-trigger="hover" data-animation="true" title="" data-content="RTMP or HTTP"></i></label>
	    <div class="controls">
	    <select name="jw_provider" class="span3">
	        <option value=''></option>
	        <option value="rtmp" <?php echo ($r['jw_flashvars']['provider'] == 'rtmp') ? 'selected="selected"' : '';?>>RTMP</option>
	        <option value="http" <?php echo ($r['jw_flashvars']['provider'] == 'http') ? 'selected="selected"' : '';?>>HTTP</option>
	    </select>
	    </div>
	    </div>
	    
	    <!-- .provider_rtmp -->
	    <div class="control-group provider_rtmp">
	    <label>Load Balancing (<small>Optional</small>) <i class="icon-info-sign" rel="popover" data-trigger="hover" data-animation="true" title="" data-content="This is the equivalent of JW Player's <code><i>rtmp.loadbalance</i></code> flashvar."></i></label>
	    <div class="controls">
	    <label><input class="checkbox inline" type="radio" name="jw_rtmp_loadbalance" value="true" <?php echo ($r['jw_flashvars']['loadbalance'] == 'true') ? 'checked="checked"' : '';?> /> On</label> 
	    <label><input class="checkbox inline" type="radio" name="jw_rtmp_loadbalance" value="" <?php echo ($r['jw_flashvars']['loadbalance'] != 'true') ? 'checked="checked"' : '';?> /> Off</label>
	    </div>
	    </div>
	    <!-- .provider_rtmp -->
	    
	    <!-- .provider_rtmp -->
	    <div class="control-group provider_rtmp">
	    <label>Subscribe (<small>Optional</small>) <i class="icon-info-sign" rel="popover" data-trigger="hover" data-animation="true" title="" data-content="This is the equivalent of JW Player's <code>rtmp.subscribe</code> flashvar."></i></label>
	    <div class="controls">
	    <label><input class="checkbox inline" type="radio" name="jw_rtmp_subscribe" value="true" <?php echo ($r['jw_flashvars']['subscribe'] == 'true') ? 'checked="checked"' : '';?> /> Yes</label> 
	    <label><input class="checkbox inline" type="radio" name="jw_rtmp_subscribe" value="" <?php echo ($r['jw_flashvars']['subscribe'] != 'true') ? 'checked="checked"' : '';?> /> No</label>
	    </div>
	    </div>
	    <!-- .provider_rtmp -->
		
		<!-- .provider_rtmp -->
	    <div class="control-group provider_rtmp">
	    <label>Secure Token (<small>Optional</small>) <i class="icon-info-sign" rel="popover" data-trigger="hover" data-animation="true" title="" data-content="Some service providers (e.g Wowza Media Server) have a feature called Secure Token that is used to protect your streams from downloading.<br />This <code>securetoken</code> parameter is optional and might not be compatible with all RTMP Service providers."></i></label>
	    <div class="controls">
	    <input type="text" name="jw_securetoken" value="<?php echo $r['jw_flashvars']['securetoken'] ;?>" size="20" class="bigger span12" />
	    </div>
	    </div>
	    <!-- .provider_rtmp -->
		
	    <div class="control-group provider_http">
	    <label>Startparam (<small>Optional</small>) <i class="icon-info-sign" rel="popover" data-trigger="hover" data-animation="true" title="" data-content="This is the equivalent of JW Player's <code><i>rtmp.startparam</i></code> flashvar."></i></label>
	    <div class="controls">
	    <input type="text" name="jw_startparam" value="<?php echo $r['jw_flashvars']['startparam'];?>" size="20" class="bigger span12" />
	    </div>
	    </div>
	    <!-- .provider_rtmp -->
	    <?php else: ?>
	    <?php if ($r['source_id'] != 1 && $r['source_id'] != 2) : ?>
	    <div class="control-group">
	    <label>Original Video URL <i class="icon-info-sign" rel="popover" data-trigger="hover" data-animation="true" title="Warning" data-content="Changing this URL will re-import the video. All other data (title, tags, description, etc.) will remain the same."></i> <a href="#" id="show-vs1">Edit</a></label>
	    <div class="controls" id="show-opt-vs1">
	    <input type="text" name="direct" class="bigger span12" value="<?php echo $r['direct']; ?>" />
	    <input type="hidden" name="direct-original" value="<?php echo $r['direct']; ?>" placeholder="http://"  />
	    </div>
	    </div>
	    <?php endif; ?>
	    <div class="control-group">
	    <label>File Location <i class="icon-info-sign" rel="popover" data-trigger="hover" data-animation="true" title="Warning" data-content="Changing the FLV/MOV/WMV/MP4 location of this video may cause it to stop working!"></i> <a href="#" id="show-vs2">Edit</a></label>
	    <div class="controls" id="show-opt-vs2">
	    <input type="text" name="url_flv" value="<?php echo $r['url_flv']; ?>" class="bigger span12" />	
	    <input type="hidden" name="url_flv-original" value="<?php echo $r['url_flv']; ?>" placeholder="http://" />
	    </div>
	    </div>
	    <?php endif; ?>
        </div><!-- .widget -->
		<?php endif; ?>
		<div class="widget border-radius4 shadow-div">
		<h4>Tags</h4>
            <div class="control-group">
            <div class="controls">
                <div class="tagsinput" style="width: 100%;">
                <input type="text" name="tags" value="<?php echo $my_tags_str; ?>"  id="tags_addvideo_1" />
                </div>
            </div>
            </div>
        </div><!-- .widget -->
<?php 
  		$modframework->trigger_hook('admin_modify_fields');
  ?>
    </div>
    
</div>
<div class="clearfix"></div>


<input type="hidden" name="categories_old" value="<?php echo $r['category'];?>" />
<input type="hidden" name="language" value="1" />
<input type="hidden" name="uniq_id" value="<?php echo $uniq_id; ?>" />
<input type="hidden" name="source_id" value="<?php echo $r['source_id']; ?>" />
    
<div id="stack-controls" class="list-controls">
<div class="btn-toolbar pull-left">
    <div class="btn-group">
    	<a href="#" onClick='del_video_id("<?php echo $uniq_id; ?>", "1", "")' class="btn btn-small btn-danger btn-strong" rel="tooltip" title="Are you sure?">Delete Video</a>
    </div>
</div>
<div class="btn-toolbar">
    <div class="btn-group">
        <button name="cancel" type="button" value="Cancel" onClick="location.href='videos.php'" class="btn btn-small btn-normal btn-strong">Cancel</button>
    </div>
    <div class="btn-group">
    	<button name="submit" type="submit" value="Save" class="btn btn-small btn-success btn-strong">Save</button>
	</div>
</div>
</div><!-- #list-controls -->
</form>

    </div><!-- .content -->
</div><!-- .primary -->
<?php
include('footer.php');
?>