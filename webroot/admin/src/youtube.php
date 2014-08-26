<?php
// +------------------------------------------------------------------------+
// | PHP Melody ( www.96down.com )
// +------------------------------------------------------------------------+
// | PHP Melody IS NOT FREE SOFTWARE
// | If you have downloaded this software from a website other
// | than www.96down.com or if you have otherwise received
// | this software from someone who is not a representative of
// | this site you are involved in an illegal activity.
// | ---
// | In such case, please contact us at: support@96down.com.
// +------------------------------------------------------------------------+
// | Developed by: PHPSUGAR (www.96down.com) / support@96down.com
// | Copyright: (c) 2004-2013 PHPSUGAR. All rights reserved.
// +------------------------------------------------------------------------+


if(!defined('PHPMELODY'))
	die('Restricted Access!');

$youtube_error_msg = '';

function get_info($url)
{
	global $youtube_error_msg;
	
	$video_data = array();
	$str 		= '';
	$yt_id		= '';
	$target_url = '';
	
	preg_match("/v=([^(\&|$)]*)/", $url, $matches);
	$yt_id = $matches[1];
	
	$target_url = "http://gdata.youtube.com/feeds/api/videos/" . $yt_id;
	
	$error = 0;
	if(function_exists('curl_init'))
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $target_url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.7.5) Gecko/20041107 Firefox/1.0');
		$video_data = curl_exec($ch);
		$errormsg = curl_error($ch);
		curl_close($ch);
		
		if($errormsg != '')
		{
			echo '<div class="alert alert-error">'.$errormsg.'</div>';
			return false;
		}
	}
	else if(ini_get('allow_url_fopen') == 1)
	{
		$video_data = @file($target_url);
		if($video_data === false)
			$error = 1;
	}
	if( ! is_array($video_data))
	{
		$video_data = explode("\n", $video_data);
	}
	
	$str = implode("", $video_data);
	$str = str_replace('><', ">\n<", $str);
	
	unset($video_data);
	$video_data = array();
	
	$video_data = explode("\n", $str);
	
	return $video_data;
}

function get_flv($url, $hq = 0)
{
	global $youtube_error_msg;
	
	$flv_link = "";

	preg_match("/v=([^(\&|$)]*)/", $url, $matches);
	$yt_id = $matches[1];

	$target_url = "http://www.youtube.com/get_video_info?video_id=".$yt_id;
	
	$error = 0;
	if(function_exists('curl_init'))
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $target_url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.7.5) Gecko/20041107 Firefox/1.0');
		$data = curl_exec($ch);
		$errormsg = curl_error($ch);
		curl_close($ch);
		
		if($errormsg != '')
		{
			echo '<div class="alert alert-error">'.$errormsg.'</div>';
			return false;
		}
	}
	else if(ini_get('allow_url_fopen') == 1)
	{
		$data = @file_get_contents($target_url);
		if($data === false)
			$error = 1;
	}
	
	preg_match("/status=([a-zA-Z]+)\&/i", $data, $matches);
	
	if(strtolower($matches[1]) == "fail")
	{
		//	What's the reason?
		$data = urldecode($data);
		
		if(preg_match("/reason=(.*?)</i", $data, $matches) != 0)
		{
			$youtube_error_msg .= 'Warning! '. $matches[1] .'<br />';
		}
		else
		{
			$youtube_error_msg .= 'WARNING: This video is either geo-restricted or embedding was disabled by the author.<br />';
		}
	}
	else
	{
		//	get token id
		preg_match("/\&token=(.*?)\&/i", $data, $matches);
		$token_id = urldecode($matches[1]);
		$target_url = "http://www.youtube.com/get_video?video_id=" . $yt_id . "&t=" . $token_id;
		
		if($hq != 0)
		{
			$target_url .= "&fmt=18";
		}
	
		$headers = fetch_headers($target_url);
		
		if(strpos($headers[0], '404'))
		{
			$youtube_error_msg .= 'Warning: This video is either geo-restricted or embedding was disabled by the author.<br />';
		}
		else
		{
			$arr_length = count($headers);
			$link = $url2;
			for($i = 0; $i < $arr_length; $i++)
			{
				if(strstr($headers[$i], "ocation:"))
				{
					$str1 = explode("ocation:", $headers[$i]);
					$link = trim($str1[1]);
					break;
				}
			}
	
			$flv_link = $link;
		}
	}
	
	return $flv_link;
}

function fetch_headers($url)
{
	$headers = array();
	$url = trim($url);
	
	$error = 0;
	if(function_exists('get_headers'))
	{
		$url = str_replace(' ', '%20', $url);
		if( ! strstr($url, "http://"))
		{
			$url = "http://" . $url;
		}
		$headers = @get_headers($url, 0);
		if(!$headers)
		{
			$error = 1;
		}
	}
	
	if($error == 1 || function_exists('get_headers') === FALSE)
	{
		$error = 0;

		if(function_exists('curl_init'))
		{
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_HEADER, 1);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_NOBODY ,1);
			curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.7.5) Gecko/20041107 Firefox/1.0');
			$data = curl_exec($ch);
			$errormsg = curl_error($ch);
			curl_close($ch);
			
			if($errormsg != '')
			{
				echo '<div class="alert alert-error">'.$errormsg.'</div>';
				return false;
			}				
			$headers = explode("\n", $data);
		}
		else if(ini_get('allow_url_fopen') == 1)
		{
			$fp = @fopen($url, "r");
			if(!$fp)
				$error = 1;
			else
			{
				if(function_exists('stream_get_meta_data'))
				{
					$data = @stream_get_meta_data($fp);
					$headers = $data['wrapper_data'];
				}
				else
				{
					$headers = $http_response_header;
				}
			}
			@fclose($fp);
		}
	}
	return $headers;
}

function get_thumb_link($video_data) 
{
	$thumb_link = '';

	$arr_length = count($video_data);
	for($i = 0; $i < $arr_length; $i++)
	{
		$video_data[$i] = str_replace( array("\n", "\t", "\r"), '', $video_data[$i]);
		
		if(preg_match("/<media:thumbnail url=\'(.*?)\'(.*?)\/>/", $video_data[$i], $matches))
		{
			$thumb_link = $matches[1];
			break;
		}
	}
	$thumb_link = str_replace('0.jpg', 'mqdefault.jpg', $thumb_link);
	return $thumb_link;
}


function video_details($video_data, $url, &$info, $show_warnings = true) 
{
	global $youtube_error_msg;
	
	$arr_length = count($video_data);
	for($i = 0; $i < $arr_length; $i++)
	{
		$video_data[$i] = str_replace( array("\n", "\t", "\r"), '', $video_data[$i]);
		
		//	video title
		if(strlen($info['video_title']) == 0)
		{
			if(preg_match("/<media:title type=\'plain\'>(.*?)<\/media:title>/", $video_data[$i], $matches) != 0)
			{
				$info['video_title'] = $matches[1];
			}
		}
		
		//	description
		if(strlen($info['description']) == 0)
		{
			if(preg_match("/<media:description type=\'plain\'>(.*?)<\/media:description>/", $video_data[$i], $matches) != 0)
			{
				$info['description'] = $matches[1];
			}
		}
					
		//	tags
		if(strlen($info['tags']) == 0)
		{
			if(preg_match("/<media:keywords>(.*?)<\/media:keywords>/", $video_data[$i], $matches) != 0)
			{
				$info['tags'] = $matches[1];
			}
		}
		
		//	length
		if(strlen($info['yt_length']) == 0)
		{
			if(preg_match("/<yt:duration seconds=\'(.*?)\'\/>/", $video_data[$i], $matches) != 0)
			{
				$info['yt_length'] = $matches[1];
			}
		}
		
		//	mp4/3gp
		if(strlen($info['mp4']) == 0)
		{
			if(preg_match("/\/><media:content url=\'(.*?)\' type=\'video\/3gpp\'(.*?)\/>/", $video_data[$i], $matches) != 0)
			{
				$info['mp4'] = $matches[1];
			}
		}
		
		//	direct link
		/*if(strlen($info['direct']) == 0)
		{
			if(preg_match("/<media:player url=\'(.*?)\'\/>/", $video_data[$i], $matches) != 0)
			{
				$info['direct'] = $matches[1];
			}
		}
		*/
	}

	//	video id
	preg_match("/v=([^(\&|$)]*)/", $url, $matches);
	$info['yt_id'] = $matches[1];
	
	$info['direct'] = (strlen($info['direct']) == 0) ? 'http://www.youtube.com/watch?v='.$info['yt_id'] : $info['direct'];
	
	
	//	flv
	$info['url_flv'] = get_flv($url);
	$info['url_flv'] = (strlen($info['url_flv']) == 0) ? $info['direct'] : $info['url_flv'];
	
	//	thumbnail link
	$info['yt_thumb'] = get_thumb_link($video_data);
	
	if(strlen($info['url_flv']) == 0 && $show_warnings)
	{
		echo '<div class="alert alert-error">'.$youtube_error_msg.'</div>';
	}
	
}

function download_thumb($thumbnail_link, $upload_path, $video_uniq_id, $overwrite_existing_file = false) {
	
	$last_ch = substr($upload_path, strlen($upload_path)-1, strlen($upload_path));
	if($last_ch != "/")
		$upload_path .= "/"; 

	$ext = ".jpg";
	
	$thumb_name = $video_uniq_id . "-1" . $ext;
	
	if(is_file( $upload_path . $thumb_name ) && ! $overwrite_existing_file) {
		return FALSE;
	}
	
	$error = 0;

	if ( function_exists('curl_init') ) 
	{

		$ch = curl_init();
		$timeout = 0;
		curl_setopt ($ch, CURLOPT_URL, $thumbnail_link);
		curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		
		// Getting binary data
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
		
		$image = curl_exec($ch);
		curl_close($ch);
		
		//	create & save image;
		$img_res = @imagecreatefromstring($image);
		if($img_res === false)
			return FALSE;
		
		$img_width = imagesx($img_res);
		$img_height = imagesy($img_res);
		
		$resource = @imagecreatetruecolor($img_width, $img_height);
		
		if( function_exists('imageantialias'))
		{
			@imageantialias($resource, true); 
		}
		
		@imagecopyresampled($resource, $img_res, 0, 0, 0, 0, $img_width, $img_height, $img_width, $img_height);
		@imagedestroy($img_res);
	
		switch($ext)
		{
			case ".gif":
				//GIF
				@imagegif($resource, $upload_path . $thumb_name);
			break;
			case ".jpg":
				//JPG
				@imagejpeg($resource, $upload_path . $thumb_name);
			break;  
			case ".png":
				//PNG
				@imagepng($resource, $upload_path . $thumb_name);
			break;
		}
	}
	else if( ini_get('allow_url_fopen') == 1 )
	{
		// try copying it... if it fails, go to backup method.
		if(!copy($thumbnail_link, $upload_path . $thumb_name ))
		{
			//	create a new image
			list($img_width, $img_height, $img_type, $img_attr) = @getimagesize($thumbnail_link);

			$image = '';

			switch($img_type)
			{
				case 1:
					//GIF
					$image = imagecreatefromgif($thumbnail_link);
					$ext = ".gif";
				break;
				case 2:
					//JPG
					$image = imagecreatefromjpeg($thumbnail_link);
					$ext = ".jpg";
				break;  
				case 3:
					//PNG
					$image = imagecreatefrompng($thumbnail_link);
					$ext = ".png";
				break;
			}
			
			$resource = @imagecreatetruecolor($img_width, $img_height);
			if( function_exists('imageantialias'))
			{
				@imageantialias($resource, true); 
			}
			
			@imagecopyresampled($resource, $image, 0, 0, 0, 0, $img_width, $img_height, $img_width, $img_height);
			@imagedestroy($image);
		}
		
		$thumb_name = $video_uniq_id . "-1" . $ext;
		
		$img_type = 2;
		switch($img_type)
		{
			default:
			case 1:
				//GIF
				@imagegif($resource, $upload_path . $thumb_name);
			break;
			case 2:
				//JPG
				@imagejpeg($resource, $upload_path . $thumb_name);
			break;  
			case 3:
				//PNG
				@imagepng($resource, $upload_path . $thumb_name);
			break;
		}
		
		if($resource === '')
			$error = 1;
	} 

	return $upload_path . $thumb_name;
}


function do_main(&$video_details, $url, $show_warnings = true)
{
	//	$show_warnings is required when importing a batch of videos from YT or MTVMusic.
	$video_data = @get_info($url);
	if($video_data != false)
	{
		video_details($video_data, $url, $video_details, $show_warnings);
	}
	else
	{
		$video_details = array();
	}
}

?>