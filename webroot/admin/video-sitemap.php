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


/*
 * START local functions
 */
function clean_feed($input) 
{
	$original = array("<", ">", "&", '"', "'", "<br/>", "<br>");
	$replaced = array("&lt;", "&gt;", "&amp;", "&quot;","&apos;", "", "");
	$newinput = str_replace($original, $replaced, $input);
	
	return $newinput;
}

function rss_show_thumb($uniq_id, $source_id, $yt_thumb, $t_id = 1)
{
	if(_THUMB_FROM == 1) 	//	Outsource
	{	
		if(($source_id == 1) || ($yt_thumb != '' && strstr($yt_thumb, "http://") === false))	//	thumbnail is hosted locally
		{
			if($source_id == 1 && $yt_thumb == '')
			{
				//	default thumbnail
				$thumb_url = _NOTHUMB;
			}
			elseif($yt_thumb != '' && strstr($yt_thumb, "http://") === false)
			{
				if(!file_exists(_THUMBS_DIR_PATH . $yt_thumb))
				{
					$thumb_url = _NOTHUMB;
				}
				else
				{
					$thumb_url = _THUMBS_DIR . $yt_thumb;
				}
			}
			else
			{
				$thumb_url = $yt_thumb;
			}
		}
		else
		{
			if($yt_thumb == '')
			{
				$thumb_url = _NOTHUMB;
			}
			else
			{
				$thumb_url = $yt_thumb;
			}
		}
	}
	else 	//	Localhost
	{
		if(!file_exists(_THUMBS_DIR_PATH . $uniq_id . "-" . $t_id . ".jpg"))
		{
			$thumb_url = _NOTHUMB;
		}
		else
		{
			$thumb_url = _THUMBS_DIR . $uniq_id . "-" . $t_id . ".jpg";
		}
	}
	return $thumb_url;
}

function xmlnl()
{
	global $xml_output;
	$xml_output .= "\r\n";
}

function vsitemap_load_options()
{
	global $config;
	return unserialize(stripslashes($config['video_sitemap_options']));
}

function vsitemap_save_options($args = array())
{
	global $config;
	
	$defaults = array('media_keywords' => 0,
					  'media_category' => 0,
					  'item_pubDate' => 0,
					  'last_build' => 0
					);
	$o = array_merge($defaults, $args);
		
	return update_config('video_sitemap_options', serialize($o), true);
}

function vsitemap_header($args = array()) 
{
	global $xml_output, $lang, $config;
	
	$defaults = array(	'xml_version' => '1.0',
						'encoding' => 'UTF-8', 
						'rss_version' => '2.0',
						'xmlns' => array('media' => 'http://search.yahoo.com/mrss/',
										 'dcterms' => 'http://purl.org/dc/terms/'
									),
						'link_url' => _URL,
					);
	
	$options = array_merge($defaults, $args);
	extract($options); 
	
	$xml_output .= '<?xml version="'. $xml_version .'" encoding="'. $encoding .'"?>';
	xmlnl();
	$xml_output .= '<rss version="'. $rss_version .'" ';
	
	foreach ($xmlns as $type => $url)
	{
		$xml_output .= ' xmlns:'. $type .'="'. $url .'" ';
	}
	$xml_output .= '>';
	xmlnl();
	$xml_output .= '<channel>';
	xmlnl();
	$xml_output .= '<link>'. $link_url .'</link>';
	xmlnl();
	
	// <title>
	if ($config['homepage_title'] != '')
	{
		$channel_title .= clean_feed($config['homepage_title']);
	}
	else
	{
		$channel_title .= clean_feed(sprintf($lang['homepage_title'], _SITENAME));
	}
	$xml_output .= '<title>'. $channel_title .'</title>';
	xmlnl();
	
	// <description>
	$xml_output .= '<description>';
	if ($config['homepage_description'] != '')
	{
		$xml_output .= clean_feed($config['homepage_description']);
	}
	else 
	{
		$xml_output .= $channel_title;
	}
	$xml_output .= '</description>';
	xmlnl();
	
	return;
}

function vsitemap_item($item = array(), $args = array()) 
{
	global $xml_output, $config, $lang, $mime_types, $video_sources;
	
	$no_thumb = ABSPATH .'/templates/'. _TPLFOLDER .'/images/no-thumbnail.jpg';
	if (count($item) == 0)
		return;
	
	$defaults = array('media_keywords' => false,
					  'media_category' => false,
					  'item_pubDate' => false
				);
	$options = array_merge($defaults, $args);
	extract($options); 
	
	
	$item['source_id'] = (int) $item['source_id'];
	$item['restricted'] = (int) $item['restricted'];
	
	$date 	= date('Y-m-d', $item['added']);
	$pubDate= date('r', $item['added']);
	$title	= clean_feed($item['video_title']);
	$desc 	= generate_excerpt($item['description'], 255);
	$link = makevideolink($item['uniq_id'], $item['video_title'], $item['video_slug']);
	
	// description
	if (strlen($desc) == 0)
	{
		$desc = clean_feed($item['video_title']);
	}
	//$desc = htmlentities($desc, ENT_QUOTES); // does not validate
	$desc	= '<![CDATA['. $desc .']]>';

	// media:content type
	$mime_type = '';
	if ($item['source_id'] == 1 || $item['source_id'] == 2)
	{
		$tmp_parts = explode('.', $item['url_flv']);
		$ext = array_pop($tmp_parts);
		$ext = strtolower($ext);
	
		if (array_key_exists($ext, $mime_types))
		{
			$mime_type = $mime_types[$ext];
		}
		else if (function_exists('finfo_open')) 
		{
			$finfo 		= finfo_open(FILEINFO_MIME);
			$mime_type 	= finfo_file($finfo, _VIDEOS_DIR_PATH . $item['url_flv']);
			finfo_close($finfo);
		}
	}
	else
	{
		$mime_type = $mime_types['flv'];
	}
	
	// fileSize
	$fileSize = 0;
	if ($item['source_id'] == 1) // localhost
	{
		if (@file_exists(_VIDEOS_DIR_PATH . $item['url_flv']))
		{
			$fileSize = (int) @filesize(_VIDEOS_DIR_PATH . $item['url_flv']);
		}
	}
	
	$thumb_url = rss_show_thumb($item['uniq_id'], $item['source_id'], $item['yt_thumb']);
	if (strpos($thumb_url, '?'))
	{
		$pieces = explode('?', $thumb_url);
		$thumb_url = $pieces[0] .'?'. clean_feed($pieces[1]);
	}
	
	$thumb_w = 0;
	$thumb_h = 0;
	
	if ($item['yt_thumb'] != '')
	{
		if (strpos($item['yt_thumb'], 'http') === false)
		{
			if (@file_exists(_THUMBS_DIR_PATH . $item['yt_thumb']))
			{
				list($thumb_w, $thumb_h) = getimagesize(_THUMBS_DIR_PATH . $item['yt_thumb']);
			}
		}
		else if (_THUMB_FROM == 2)
		{
			if (@file_exists(_THUMBS_DIR_PATH . $item['uniq_id'] . '-1.jpg'))
			{
				list($thumb_w, $thumb_h) = getimagesize(_THUMBS_DIR_PATH . $item['uniq_id'] . '-1.jpg');
			}
		}
	}

	// media:player START
	//$player_url = _URL .'/player.swf';
	$player_url = '';
	$flashvars = '';
	$swf_player_type = '';
	
	if ($item['source_id'] == $video_sources['youtube']['source_id'] && $item['direct'] == '')
	{
		$item['direct'] = 'http://www.youtube.com/watch?v='. $item['yt_id'];
	}
	
	$swf_player_type = $config['video_player'];
	
	switch ($config['video_player'])
	{
		case 'jwplayer':
		case 'flvplayer':
			
			if ($video_sources[ $item['source_id'] ]['flv_player_support'] == 0 || 
				$video_sources[ $item['source_id'] ]['user_choice'] == 'embed')
			{
				$swf_player_type	= 'embed';
			}
			
		break;
	
		case 'embed':
			
			if ($video_sources[ $item['source_id'] ]['embed_player_support'] == 0)
			{
				$swf_player_type	= 'flvplayer';
			}
			
		break;
	}
			
	if ($item['source_id'] == 0)
	{
		$sql = "SELECT * 
				FROM pm_embed_code 
				WHERE uniq_id = '". $item['uniq_id'] ."'";

		$result = mysql_query($sql);
		$row = mysql_fetch_assoc($result);
		mysql_free_result($result);
		
		if (is_serialized($row['embed_code']))
		{
			$swf_player_type = 'jwplayer';
			
			$item_flashvars = unserialize($row['embed_code']);
			$pieces = explode(';', $item['url_flv'], 2);
			
			$player_url = _URL .'/jwplayer.swf';
			$flashvars .= 'file='. str_replace(array('?', '=', '&'), array('%3F', '%3D', '%26'), $pieces[0]);
			$flashvars .= '&streamer='. str_replace(array('?', '=', '&'), array('%3F', '%3D', '%26'), $pieces[1]);
			$flashvars .= ($item_flashvars['provider'] != '') ? '&provider='. $item_flashvars['provider'] : '';
			$flashvars .= ($item_flashvars['startparam'] != '') ? '&http.startparam='. $item_flashvars['startparam'] : '';
			$flashvars .= ($item_flashvars['loadbalance'] != '') ? '&rtmp.loadbalance='. $item_flashvars['loadbalance'] : '';
			$flashvars .= ($item_flashvars['subscribe'] != '') ? '&rtmp.subscribe='. $item_flashvars['subscribe'] : '';
			$flashvars .= '&config='. urlencode(_URL ."/jwembed.xml");
			unset($item_flashvars, $pieces);
		}
		else
		{
			if (preg_match('/src="(.*?)"/i', $row['embed_code'], $matches) != 0)
			{
				$player_url = $matches[1];
			}
			else if (preg_match('/name="movie" value="(.*?)"/i', $row['embed_code'], $matches) != 0)
			{
				$player_url = $matches[1];
			}
			
			if (strpos($player_url, '//') === 0)
			{
				$player_url = (strpos($player_url, 'http') === false) ? 'http:'. $player_url : ltrim($player_url, '/');
			}
			
			if (preg_match('/flashvars="(.*?)"/i', $row['embed_code'], $matches) != 0)
			{
				$flashvars = $matches[1];
			}
			
			$swf_player_type = 'embed';
		}
	}
	
	if ($item['source_id'] == 1 || $item['source_id'] == 2)
	{
		// PLAYER TYPE + File TYPE => 
		$tmp_parts = explode('.', $item['url_flv']);
		$ext = array_pop($tmp_parts);
		$ext = strtolower($ext);
		switch ($ext)
		{
			case 'mov': case '3gp': case '3g2':	case 'm4a': case 'wmv': case 'asf': case 'wma': case 'mkv': case 'divx': case 'avi':
			$player_url = '';
			break;

			case 'mp3':
				
				$item['url_flv'] = _URL .'/videos.php?vid='. $item['uniq_id'];
				
				$player_url = _URL .'/jwplayer.swf';		
				$flashvars .= 'file='. str_replace(array('?', '=', '&'), array('%3F', '%3D', '%26'), $item['url_flv']);
				$flashvars .= '&type=sound';
				$flashvars .= '&config='. urlencode(_URL ."/jwembed.xml");

			break;
		}
	}

	if ($item['source_id'] > 2)
	{
		switch ($swf_player_type)
		{
			case 'jwplayer':
				
				$player_url = _URL .'/jwplayer.swf';
				
				if ($item['source_id'] == 3)
				{
					$flashvars .= 'file='. urlencode($item['direct']);
					$flashvars .= '&type=youtube';
				}
				else
				{
					$flashvars .= 'file='. urlencode(_URL ."/videos.php?vid=". $item['uniq_id']);
					$flashvars .= '&type=video';
				}
				$flashvars .= '&config='. urlencode(_URL ."/jwembed.xml");
				
			break;
			
			case 'flvplayer':
				
				$player_url = _URL .'/fpembed-'. $item['uniq_id'] .'.swf';
				$flashvars = '';

			break;
			
			case 'embed':
				
				$embed_code = $video_sources[ $item['source_id'] ]['embed_code'];
				$embed_code = str_replace("%%yt_id%%", $item['yt_id'], $embed_code);
				$temp_url_flv = str_replace("&", "&amp;", $item['url_flv']);
				$embed_code = str_replace("%%url_flv%%", $temp_url_flv, $embed_code);
				$embed_code = str_replace("%%direct%%", $item['direct'], $embed_code);
				$embed_code = str_replace("%%player_w%%", _PLAYER_W_EMBED, $embed_code);
				$embed_code = str_replace("%%player_h%%", _PLAYER_H_EMBED, $embed_code);
				$embed_code = str_replace("%%player_autoplay%%", '0', $embed_code);
			
				if ($item['source_id'] == $video_sources['trilulilu']['source_id'] && $item['direct'] != '')
				{
					$temp = '';
					$temp = rtrim($item['direct'], "/");
					$temp = str_replace(array('http://', 'www.'), "", $temp);
					
					@preg_match('/^trilulilu\.ro\/(.*?)\/([a-zA-Z0-9]+)$/i', $temp, $matches);
					$embed_code = str_replace("%%username%%", $matches[1], $embed_code);
				}
				
				if (preg_match('/src="(.*?)"/i', $embed_code, $matches) != 0)
				{
					$player_url = $matches[1];
				}
				else if (preg_match('/name="movie" value="(.*?)"/i', $embed_code, $matches) != 0)
				{
					$player_url = $matches[1];
				}
				
				if (preg_match('/flashvars="(.*?)"/i', $embed_code, $matches) != 0)
				{
					$flashvars = $matches[1];
				}

			break;
		}
	}
	// media:player END

	// media:content url
	$media_content_url = '';
	if ($player_url == '')
	{
		if ((strpos($item['url_flv'], 'http') !== false) || ($item['source_id'] == 1))
		{
			$tmp_parts = explode('.', $item['url_flv']);
			$ext = array_pop($tmp_parts);
			$ext = strtolower($ext);
	
			if (array_key_exists($ext, $mime_types))
			{
				$media_content_url = _URL .'/videos.php?vid='. $item['uniq_id'];
			}
		}
	}
	
	$flashvars = str_replace('?', '', $flashvars);
	$flashvars = ($flashvars != '') ? '?'.$flashvars : $flashvars;
	
	// START output 
	$xml_output = '<item>';
	xmlnl();
	
	$xml_output .= '<link>'. $link .'</link>';
	xmlnl();
	
	$xml_output .= '<media:content medium="video"';
	$xml_output .= ($media_content_url != '') ? ' url="'. $media_content_url .'" ' : '';
	$xml_output .= ($item['yt_length'] > 0) ? ' duration="'. $item['yt_length'] .'" ' : '';
	$xml_output .= ($mime_type != '' ) ? ' type="'. $mime_type .'" ' : '';
	$xml_output .= ($fileSize > 0) ? ' fileSize="'. $fileSize .'" ' : '';
	$xml_output .= '>';
	xmlnl();
	
	if ($player_url != '')
	{
		$xml_output .= '<media:player url="'. str_replace('&', '&amp;', $player_url . $flashvars) .'" height="'. _PLAYER_H_EMBED .'" width="'. _PLAYER_W_EMBED .'" />';
		xmlnl();
	}
	
	$xml_output .= '<media:title>'. $title .'</media:title>';
	xmlnl();
	
	$xml_output .= '<media:description type="html">'. $desc .'</media:description>';
	xmlnl();
	
	$xml_output .= '<media:thumbnail url="'. $thumb_url .'" ';
	$xml_output .= ($thumb_w > 0) ? ' width="'. $thumb_w .'" ' : '';
	$xml_output .= ($thumb_h > 0) ? ' height="'. $thumb_h .'" ' : '';
	$xml_output .= '/>';
	xmlnl();
	
	if ($media_keywords)
	{
		$tags_str = '';
		$tags = (array) get_video_tags($item['uniq_id'], 0);
		
		$count = 0;
		foreach ($tags as $t)
		{
			$tags_str .= clean_feed($t['tag']).',';
			$count++;
			if ($count == 10)
				break;
		}
		$tags_str = substr($tags_str, 0, -1);
		
		if ($tags_str != '')
		{
			$xml_output .= '<media:keywords>'. clean_feed($tags_str) .'</media:keywords>';
			xmlnl();
		}
	}
	
	
	if ($media_category)
	{
		$categories = load_categories();
	
		$long_cat = '';
		$parent =  0;
		$c = explode(',', $item['category']);
		
		if (count($c) > 0)
		{
			foreach ($c as $k => $c_id)
			{
				$long_cat = $categories[$c_id]['name'];
				$parent =  $categories[$c_id]['parent_id'];
				while ($parent != 0)
				{
					if ($long_cat == '')
					{
						$long_cat = $categories[$parent]['name'];
					}
					else
					{
						$long_cat = $categories[$parent]['name'] .'/'. $long_cat;
					}
					
					$parent = $categories[$parent]['parent_id'];
				}
				
				$xml_output .= '<media:category label="'. clean_feed($categories[$c_id]['name'])  .'">'. clean_feed(strtolower($long_cat)) .'</media:category>';
				xmlnl();
			}
		}
	}
	
	if ($item['restricted'] == 1)
	{
		$xml_output .= '<media:restriction type="sharing" relationship="deny" />';
		xmlnl();
	}
	
	$xml_output .= '</media:content>';
	xmlnl();
	
	if ($item_pubDate)
	{
		$xml_output .= '<pubDate>'. $pubDate .'</pubDate>';
		xmlnl();
	}
	
	$xml_output .= '</item>';
	xmlnl();
	
	return;
}

function vsitemap_footer() 
{
	global $xml_output;
	xmlnl();
	$xml_output .= '</channel>';
	xmlnl();
	$xml_output .= '</rss>';
}
/*
 * END local functions
 */

$mime_types = array('flv' => 'video/x-flv',
					'mp4' => 'video/mp4',
					'mov' => 'video/quicktime',
					'wmv' => 'video/x-ms-wmv',
					'divx' => 'video/divx',
					'avi' => 'video/divx',
					'mkv' => 'video/divx',
					'asf' => 'video/x-ms-asf', 
					'wma' => 'audio/x-ms-wma', 
					'mp3' => 'audio/mpeg', 
					'm4v' => 'video/mp4', 
					'm4a' => 'audio/mp4', 
					'3gp' => 'video/3gpp', 
					'3g2' => 'video/3gpp2'
					);

// Handle AJAX requests - START 
if ($_GET['do'] == 'map')
{
	$ajax_state = '';
	$default_options = array('limit' => 50000,
							 'start-from' => 1,
							 'xml_file' => 'video-sitemap-1.xml',
							 'media_keywords' => false,
							 'media_category' => false,
							 'item_pubDate' => false, // include <pubDate> in <item>
							);

	session_start();
	require_once('../config.php');
	include_once('functions.php');
	include_once( ABSPATH . 'include/user_functions.php');
	include_once( ABSPATH . 'include/islogged.php');

	if ( ! $logged_in || ! is_admin())
	{
		$ajax_state = 'error';
		$ajax_msg = ($logged_in) ? 'Access denied!' : 'Please log in.'; 	
	}
	
	$ajax_state = 'init';
	
	$file = ($_GET['file'] == '') ? $default_options['xml_file'] : $_GET['file'];

	if ( ! in_array($file, array('video-sitemap-1.xml', 'video-sitemap-2.xml', 'video-sitemap-3.xml', 'video-sitemap-4.xml', 'video-sitemap-5.xml')))
	{
		$ajax_state = 'error';
		$ajax_msg = 'Error: invalid filename given.';
	}
	
	if ( ! file_exists(ABSPATH . $file))
	{
		$ajax_state = 'error';
		$ajax_msg = 'Error: file <code>'. ABSPATH . $file .'</code> not found.'; 
	}
	else if ( ! is_writeable(ABSPATH . $file))
	{
		$ajax_state = 'error';
		$ajax_msg = 'Error: file <code>'. ABSPATH . $file .'</code> is not writeable! Please set fully writtable permissions (CHMOD 0777) and try again.';
	}
	
	if ($ajax_state == 'error')
	{
		$ajax_response = array('state' => $ajax_state,
							   'message' => '<div class="alert alert-error">'. $ajax_msg .'</div>'
						);
		echo json_encode($ajax_response);
		exit();
	}
	
	$step = 100;	//	sql limit
	$limit = (int) $_GET['limit']; // max. items to process 
	$start = (int) $_GET['start'];
	$progress = (float) $_GET['progress'];
	$start--;
	$count = (int) $_GET['c'];
	$start_from = (int) $_GET['sf'];
	
	$options = array(
			'media_keywords' => (isset($_GET['tags']) && $_GET['tags'] == '1') ? true : false,
			'media_category' => (isset($_GET['cats']) && $_GET['cats'] == '1') ? true : false,
			'item_pubDate' => (isset($_GET['pub']) && $_GET['pub'] == '1') ? true : false
		);
	
	if ($limit > $config['total_videos'])
		$limit = $config['total_videos'];

	if ((($start_from + $limit) - 1) > $config['total_videos'])
		$limit = $config['total_videos'] - $start_from;

	if ($step > ($limit - $count))
		$step = $limit - $count;

	if ($start < 0)
		$start = 0;
	
	$video_sources = fetch_video_sources();
	$xml_output = '';
	
	if ($count == 0)
	{
		$xml = fopen(ABSPATH . $file, 'w');
		vsitemap_header(array('link_url' => _URL .'/'. $file));
		fwrite($xml, $xml_output);
	}
	else
	{
		$xml = fopen(ABSPATH . $file, 'a');
	}
	
	$ajax_state = 'processing';
	
	if ($count >= $limit)
	{
		$xml_output = '';
		vsitemap_footer();
		fwrite($xml, $xml_output);

		$ajax_state = 'finished';
	}
	
	if ($count < $limit)
	{
		$sql = "SELECT pm_videos.*, pm_videos_urls.mp4, pm_videos_urls.direct 
				FROM pm_videos 
				LEFT JOIN pm_videos_urls 
				     ON (pm_videos.uniq_id = pm_videos_urls.uniq_id)
				WHERE added <= '". time() ."' 
				ORDER BY added DESC 
				LIMIT $start, $step";

		$result = mysql_query($sql);
		
		if ( ! $result)
		{
			$ajax_state = 'error';
			$ajax_msg = 'There was a error while generating sitemap. <br /><strong>MySQL returned:</strong> '. mysql_error();
		}
		else
		{
			while ($row = mysql_fetch_assoc($result))
			{
				$xml_output = '';
				vsitemap_item($row, $options);
				fwrite($xml, $xml_output);
				$count++;
			}
		}
	}
	
	switch ($ajax_state)
	{
		default:
		case 'init':
		case 'processing':
			
			$ajax_response = array('state' => $ajax_state,
								  'start' => ($start + $step + 1),
								  'sf' => $start_from,
								  'limit' => $limit,
								  'progress' => round(($count * 100)/$limit, 2),
								  'c' => $count,
								  'message' => ''
							);
		break;
		
		case 'finished':
		
			$options['last_build'] = time();
			
			vsitemap_save_options($options);
			
			$size = filesize(ABSPATH . $file);
			
			$ajax_response = array('state' => $ajax_state,
							  'start' => $limit,
							  'sf' => $start_from,
							  'limit' => $limit,
							  'progress' => 100,
							  'c' => $count,
							  'message' => '<div class="alert alert-success">The sitemap is now available at: <code>'. _URL .'/'. $file .'</code> ('. readable_filesize($size) .')</div>'
						);	
		break;
		
		case 'error':
			$ajax_response = array('state' => $ajax_state,
							  'start' => $start+1,
							  'sf' => $start_from,
							  'limit' => $limit,
							  'progress' => round(($count * 100)/$limit, 2),
							  'c' => $count,
							  'message' => '<div class="alert alert-error">'. $ajax_msg .'</div>'
						);
		break;
	}
	
	fclose($xml);
	sleep(1); 
	echo json_encode($ajax_response);
	exit(); 
}
// Handle AJAX requests - END

$showm = '8';
$load_scrolltofixed = 1;
$load_jquery_ui = 1;
$_page_title = 'Generate video sitemap';
include('header.php');

?>
<script type="text/javascript">
	function build_map(start, limit, params, html_output_sel)
	{
		$.ajax({
			url: 'video-sitemap.php',
			data: 'do=map&start='+ start +
				  '&sf='+ params.sf +
				  '&limit='+ limit +
				  '&tags='+ params.tags +
				  '&cats='+ params.cats +
				  '&pub='+ params.pub +
				  '&progress=' + params.progress +
				  '&c=' + params.c +
				  '&file='+ params.file,
			success: function(data){
						switch (data['state'])
						{
							case 'processing':
								$( "#progressbar" ).show();
								$( "#progressbar" ).progressbar({value: data['progress'] });
								params.progress = data['progress'];
								params.c = data['c'];
								build_map(data['start'], data['limit'], params, html_output_sel);
								
							break;
							
							case 'finished':
								$('#build-map-button').attr('disabled', false);
							case 'error':
								if (data['state'] == 'finished') {
									$( "#progressbar" ).hide();
									$('#ajax-response').html(data['message']);
								} else {
									$( "#progressbar" ).progressbar({value: data['progress'] });
									$('#ajax-response').html(data['message']);
								}
								$('#build-map-button').attr('disabled', false);
							break;
						}
					},
			dataType: 'json'
		});
	}

	$(document).ready(function(){
		$('#build-map-button').click(function(){
			var start, limit;
			var tags, cats, pub, file;
			var params = new Object();
			
			tags = ($("input[name='media_keywords']").attr('checked')) ? '1' : '0';
			cats = ($("input[name='media_category']").attr('checked')) ? '1' : '0';
			pub = ($("input[name='item_pubDate']").attr('checked')) ? '1' : '0';
			file = $("select[name='file']").val();
			
			start = parseInt($("input[name='start-from']").val());
			limit = parseInt($("input[name='limit']").val());
			max_limit = parseInt($("input[name='max-limit']").val());
			
			if ( ! start)
				start = 0;
			if ( ! limit)
				limit = max_limit;
			
			if (max_limit < limit) {
				alert('"Videos per sitemap" cannot be greater than the total number of videos (' + max_limit + ').');
			} else if (max_limit < start) {
				alert('"Start from" cannot be greater than the total number of videos (' + max_limit + ').');
			} else {
				params.tags = tags;
				params.cats = cats;
				params.pub = pub;
				params.file = file;
				params.progress = 0;
				params.c = 0;
				params.sf = start;
				
				$(this).attr('disabled', true);
				build_map(start, limit, params, '#ajax-response');
				
			}
			return false;
		});
	});
</script>
<div id="adminPrimary">

    <div class="row-fluid" id="help-assist">
        <div class="span12">
        <div class="tabbable tabs-left">
          <ul class="nav nav-tabs">
            <li class="active"><a href="#help-overview" data-toggle="tab">Overview</a></li>
          </ul>
          <div class="tab-content">
            <div class="tab-pane fade in active" id="help-overview">
    		<p>Video sitemaps help search engines crawl and index your videos. These results will appear in the search results with a video snapshot alongside your title, increasing the likelihood of search engine traffic.</p>
            <p>Google allows a maximum of 50,000 entries per sitemap file. As as result we've included the option to split your video sitemap into two files.</p>
			<p>That's not the case yet, no changes are required to the form below.<br />Hitting &quot;Generate sitemap&quot; is sufficient. As with regular sitemaps, video sitemaps should also be submitted to the <a href="https://www.google.com/webmasters/tools/home?hl=en" target="_blank">Google Webmaster Tools</a>.</p>
			<p>For more details about the Google Video Sitemap guidelines, visit <a href="http://support.google.com/webmasters/bin/answer.py?hl=en&answer=80472" target="_blank">http://support.google.com/webmasters/bin/answer.py?hl=en&amp;answer=80472</a></p>
			</div>
          </div>
        </div> <!-- /tabbable -->
        </div><!-- .span12 -->
    </div><!-- /help-assist -->
    <div class="content">
	<a href="#" id="show-help-assist">Help</a>

<?php
$xml = file_get_contents(ABSPATH .'video-sitemap-1.xml');

$count = preg_match_all('/<item>/', $xml, $matches);

$form_options = array(
					'limit' => 50000,
					'start-from' => 1,
					'media_keywords' => false,
					'media_category' => false,
					'item_pubDate' => false,
					);

$last_options_used = vsitemap_load_options();
$form_options = array_merge($form_options, $last_options_used);

if ($config['total_videos'] < 50000)
{
	$form_options['limit'] = $config['total_videos'];
}

?>

<h2>Generate Video Sitemap</h2>
<div id="ajax-response"></div>

<?php if ($form_options['last_build'] > 0) : ?>
<div id="more_v_details" class="alert alert-info">
Last build on <strong><?php echo date('F j, Y g:i a', $form_options['last_build']);?></strong>
</div>
<?php endif;?>
<div class="t1">
<form action="video-sitemap.php" method="post">
<table cellpadding="0" cellspacing="0" width="100%" class="table table-striped table-bordered pm-tables pm-tables-settings">
      <tr class="table_row1">
        <td width="20%">Start from entry</td> 
        <td>
		<input type="text" name="start-from" size="9" value="<?php echo $form_options['start-from'];?>" />
		<a href="#" rel="popover" data-placement="right" data-trigger="hover" data-content="If you decide to split the sitemap into two files, choose the range of videos assigned to each sitemap file."><i class="icon-info-sign"></i></a>

		</td>
     </tr>
      <tr class="table_row1">
        <td width="20%">Videos per sitemap</td>
        <td>
		<input type="text" name="limit" size="9" value="<?php echo $form_options['limit'];?>" />
		<input type="hidden" name="max-limit" value="<?php echo $config['total_videos'];?>" />
		<a href="#" rel="popover" data-placement="right" data-trigger="hover" data-content="Limit the number of videos you add into each sitemap file."><i class="icon-info-sign"></i></a>
		</td>
     </tr>
      <tr class="table_row1">
        <td width="20%">Output file</td>
        <td>
		<select name="file">
		<option value="video-sitemap-1.xml">video-sitemap-1.xml</option>
		<option value="video-sitemap-2.xml">video-sitemap-2.xml</option>
        <option value="video-sitemap-3.xml">video-sitemap-3.xml</option>
        <option value="video-sitemap-4.xml">video-sitemap-4.xml</option>
        <option value="video-sitemap-5.xml">video-sitemap-5.xml</option>
		</select>
		<a href="#" rel="popover" data-placement="right" data-trigger="hover" data-content="If your database is large you should split your videos into two or more sitemap files."><i class="icon-info-sign"></i></a>
		</td>
     </tr>
      <tr class="table_row1">
        <td width="20%" valign="top">Add additional data</td>
        <td>
		<label><input type="checkbox" name="media_keywords" value="1" <?php echo ($form_options['media_keywords']) ? 'checked="checked"' : '';?> /> Include keywords</label>
		<br /> 
		<label><input type="checkbox" name="media_category" value="1" <?php echo ($form_options['media_category']) ? 'checked="checked"' : '';?> /> Include categories</label>
		<br />
		<label><input type="checkbox" name="item_pubDate" value="1" <?php echo ($form_options['item_pubDate']) ? 'checked="checked"' : '';?> /> Include <code>&lt;pubDate&gt;</code> tag</label>
		</td>
     </tr>
</table>

<div class="clearfix"></div>
<div id="progressbar" style="width: 250px; height: 15px;" class="progress progress-striped active"></div>
<div id="stack-controls" class="list-controls">
<div class="btn-toolbar">
    <div class="btn-group">
		<button type="submit" name="submit" data-loading-text="Generating..." class="btn btn-small btn-success btn-strong" id="build-map-button">Generate sitemap</button>
	</div>
</div>
</div><!-- #list-controls -->
</form>
</div>

    </div><!-- .content -->
</div><!-- .primary -->
<?php
include('footer.php');
?>