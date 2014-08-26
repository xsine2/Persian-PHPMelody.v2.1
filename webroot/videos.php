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
require_once('config.php');
require_once('include/functions.php');
require_once('include/user_functions.php');

//	set ad cookie ?
if (empty($_COOKIE[COOKIE_VIDEOAD]))
{
	if ($config['total_videoads'] > 0)
	{
		if ($config['videoads_delay'] != 0)
		{
			setcookie(COOKIE_VIDEOAD, $video_ad_hash, time() + $config['videoads_delay'], COOKIE_PATH);
		}
	}
}

if (empty($_GET['vid'])) 
{
	exit();
} 
else 
{
	$video_sources = fetch_video_sources();
	$video_src_name = '';
	
	$temp		= array();
	$video		= array();
	$mime_type	= 'video/x-flv';
	
	$video_id 	= secure_sql($_GET['vid']);
		
	$sql = "SELECT pm_videos.*, pm_videos_urls.mp4, pm_videos_urls.direct 
			FROM pm_videos 
			LEFT JOIN pm_videos_urls 
				   ON (pm_videos.uniq_id = pm_videos_urls.uniq_id) 
			WHERE pm_videos.uniq_id = '". $video_id ."'";

	$result =  @mysql_query($sql);
	$video = @mysql_fetch_assoc($result);
	mysql_free_result($result);
	
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
						
	define('PHPMELODY', true);
	
	$divx_player = false;
	if ($video['source_id'] == 1 || $video['source_id'] == 2)
	{
		$tmp_parts = explode('.', $video['url_flv']);
		$ext = array_pop($tmp_parts);
		$ext = strtolower($ext);

		if ($ext == 'mkv' || $ext == 'avi' || $ext == 'mkv')
		{
			$divx_player = true;
		}
	}
	
	if ( ! $divx_player)
	{
		if ( ! is_user_logged_in() && $video['restricted'] == '1')
		{
			echo $lang['registration_req'];
			exit();
		}	
	}
	
	$video_src_name = strtolower($video_sources[$video['source_id']]['source_name']);

	switch ($video_src_name)
	{
		case 'youtube':	// Youtube
		
			include(ABSPATH . "admin/src/youtube.php");

			if ($video['direct'] == '')
			{
				$video['direct'] = 'http://www.youtube.com/watch?v='. $video['yt_id'];
			}
			
			$flv_url = get_flv($video['direct'], _USE_HQ_VIDS);

		break;
		
		
		case 'dailymotion': // Dailymotion

			@include(ABSPATH . "admin/src/dailymotion.php");
			
			if (_USE_HQ_VIDS)
			{
				$flv_url = get_hd($video['direct']);
			}
			if ((_USE_HQ_VIDS && ($flv_url == '' || strlen($flv_url) < 10)) || ( ! _USE_HQ_VIDS))
			{
				$flv_url = get_flv($video['direct']);
			}
		break;
		
		case 'veoh':	// VEOH

			@include(ABSPATH . "admin/src/veoh.php");
			
			do_main($temp, $video['direct']);
			$flv_url = $temp['url_flv'];
			unset($temp);
		break;
		
		case 'metacafe': // Metacafe
		
			@include(ABSPATH . "admin/src/metacafe.php");

			$flv_url = get_flv($video['direct']);
			unset($temp);
		break;
		
		case 'funnyordie': // FunnyOrDie
		
			$parts = explode("/", $video['direct']);
			$vid_id = $parts[ count($parts)-2 ];
			$flv_url = 'http://videos0.ordienetworks.com/videos/'.$vid_id.'/sd.flv';
		break;
		
		case 'vimeo': // Vimeo
		
			@include(ABSPATH . "admin/src/vimeo.php");
			
			do_main($temp, $video['direct']);
			$flv_url = $temp['url_flv'];
			unset($temp);
		break;
		
		
		case 'google':	//	Google
			
			@include(ABSPATH . "admin/src/google.php");
			
			do_main($temp, $video['direct']);
			
			$flv_url = $temp['url_flv'];
			unset($temp);
		break;
		
		case 'myspace': // Myspace

			@include(ABSPATH . "admin/src/myspace.php");
			do_main($temp, $video['direct']);
				
			$flv_url = $temp['url_flv'];
			unset($temp);
			
			if ($flv_url == '')
			{
				report_video($video['uniq_id'], '1', 'The *.FLV URL was not found', 'PM Bot');
			}
			
		break;
		
		case 'break': //	break.com
			
			if (strpos($video['url_flv'], 'media1.break'))
			{
				$video['url_flv'] = str_replace('media1.', 'video1.', $video['url_flv']);
				
				$sql = "UPDATE pm_videos SET url_flv = '". secure_sql($video['url_flv']) ."' 
							WHERE id = '". $video['id'] ."'";
				@mysql_query($sql);
			}
			
			$flv_url = $video['url_flv'];
			
		break;
		
		case 'sevenload': // sevenload
			
			@include(ABSPATH . "admin/src/sevenload.php");

			if ($video['direct'] == '')
			{
				$video['direct'] = 'http://en.sevenload.com/videos/'. $video['yt_id'] .'-';

				$headers = fetch_headers($video['direct']);
				$arr_length = count($headers);
	
				for($i = 0; $i < $arr_length; $i++)
				{
					if(strpos($headers[$i], "ocation:") !== false)
					{
						$str1 = explode("ocation:", $headers[$i]);
						$video['direct'] = trim($str1[1]);
						break;
					}
				}
	
				@mysql_query("UPDATE pm_videos_urls SET direct='". $video['direct'] ."' WHERE uniq_id = '". $video['uniq_id'] ."'");
			}

			do_main($temp, $video['direct']);
			
			$flv_url = str_replace('&amp;', '&', $temp['url_flv']);
			unset($temp);

		break;
		
		case 'trilulilu': // trilulilu.ro
		
			if (strlen($video['direct']) == 0)
			{
				if (strlen($video['url_flv']) > 0)
				{
					$flv_url = $video['url_flv'];
				}
				else
				{
					report_video($video['uniq_id'], '1', 'The *.FLV URL was not found', 'PM Bot');
				}
			}
			else
			{
				@include(ABSPATH . "admin/src/trilulilu.php");
				do_main($temp, $video['direct']);
				$flv_url = $temp['url_flv'];
				unset($temp);	
			}			
		break;
		
		case 'vbox7':
			
			@include(ABSPATH . "admin/src/vbox7.php");
			do_main($temp, $video['direct']);
			$flv_url = $temp['url_flv'];
			unset($temp);	

		break;
		
		case 'mynet':
			
			@include(ABSPATH . "admin/src/mynet.php");
			
			do_main($temp, $video['direct']);
			
			$flv_url = $temp['url_flv'];
			$flv_url = str_replace('&amp;', '&', $flv_url);
			unset($temp);
			
		break;
		
		default:
		
			if ($video['source_id'] == 1 || $video['source_id'] == 2)
			{
				if(strpos($video['url_flv'], 'http://') !== false || strpos($video['url_flv'], 'https://') !== false)
				{
					$flv_url = $video['url_flv'];
				}
				else
				{
					$flv_url = _VIDEOS_DIR . $video['url_flv'];
				}
				
				$tmp_parts = explode('.', $video['url_flv']);
				$ext = array_pop($tmp_parts);
				$ext = strtolower($ext);

				if (array_key_exists($ext, $mime_types))
				{
					$mime_type = $mime_types[$ext];
				}
				else if (function_exists('finfo_open')) 
				{
					$finfo 		= finfo_open(FILEINFO_MIME);
					$mime_type 	= finfo_file($finfo, _VIDEOS_DIR_PATH . $video['url_flv']);
					finfo_close($finfo);
				}
			}
			else
			{
				$flv_url = $video['url_flv'];
			}
			
		break;
	}

	@update_view_count($video['id'], $video['site_views']);


	header("Cache-Control: no-store, no-cache, must-revalidate");
	header("Content-Type: ". $mime_type);
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	header('Location: '. $flv_url);
}
exit();
?>