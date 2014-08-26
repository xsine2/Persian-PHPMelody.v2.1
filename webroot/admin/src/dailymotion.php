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

if(!defined('PHPMELODY'))
	die('Restricted Access!');

function get_info($url)
{
	$video_data = array();
	
	preg_match('/video\/([a-zA-Z0-9]+)_/', $url, $matches);
	$target_url = "http://www.dailymotion.com/rss/video/" . $matches[1];
	
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
			echo $errormsg;
			return false;
		}
	}
	else if(ini_get('allow_url_fopen') == 1)
	{
		$video_data = @file($target_url);
		if($video_data === false)
			$error = 1;
	}
	if(!is_array($video_data))
	{
		$video_data = explode("\n", $video_data);
	}
	
	//	cleanup
	$buff_arr = array();
	$i = 0;
	foreach($video_data as $k => $v)
	{
		$v = trim($v, " \t\r\n");
		if($v != '')
			$buff_arr[$i++] = $v;
	}
	$video_data = $buff_arr;
	
	return $video_data;
}

function get_flv($url, $video_data)
{
	$flv_link = '';
	$target_url = $url;
	$buff = $video_data;
	
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
			echo $errormsg;
			return false;
		}
	}
	else if(ini_get('allow_url_fopen') == 1)
	{
		$video_data = @file($target_url);
		if($video_data === false)
			$error = 1;
	}
	
	if(!is_array($video_data))
	{
		$video_data = explode("\n", $video_data);
	}
	
	$arr_count = count($video_data);
	for($i = 10; $i < $arr_count; $i++)
	{
		$video_data[$i] = trim($video_data[$i], " \t\r\n");
		
		//	video id
		if(preg_match("/addVariable\(\"video\", \"(.*?)\"\)/i", $video_data[$i], $matches) != 0)
		{			
			$url2 = urldecode($matches[1]);
			unset($matches);
			$matches = explode("@", $url2);
			$url2 = $matches[0];
			
			if(strstr($url2, "http:") === false)
			{
				$url2 = "http://www.dailymotion.com".$url2;
			}
			$headers = fetch_headers($url2);
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
			break;
		}
	}
	
	if ('' == $link)
	{
		$video_data = $buff;
		unset($buff);
		
		$arr_length = count($video_data);
		for($i = 0; $i < $arr_length; $i++)
		{
			if(preg_match('/<media:content url="(.*?)" type="video/', $video_data[$i], $matches) != 0)
			{
				$link = $matches[1];
				break;
			}
		}
		if ('' != $link)
		{
			$headers = fetch_headers($link);
			$arr_length = count($headers);
			
			for($i = 0; $i < $arr_length; $i++)
			{
				if(strstr($headers[$i], "ocation:"))
				{
					$str1 = explode("ocation:", $headers[$i]);
					$link = trim($str1[1]);
					break;
				}
			}
		}
	}
	
	$flv_link = ('' != $link) ? $link : $url;
	return $flv_link;
}

function get_hd($url)
{
	$flv_link = '';
	$target_url = $url;
	
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
			echo $errormsg;
			return false;
		}
	}
	else if(ini_get('allow_url_fopen') == 1)
	{
		$video_data = @file($target_url);
		if($video_data === false)
			$error = 1;
	}
	
	if(!is_array($video_data))
	{
		$video_data = explode("\n", $video_data);
	}
	
	$arr_count = count($video_data);
	for($i = 10; $i < $arr_count; $i++)
	{
		$video_data[$i] = trim($video_data[$i], " \t\r\n");
		
		//	video id
		if(preg_match("/addVariable\(\"video\", \"(.*?)\"\)/i", $video_data[$i], $matches) != 0)
		{
			$url2 = urldecode($matches[1]);
			unset($matches);
			$matches = explode("@@", $url2);
			$url2 = $matches[ count($matches)-2 ];
			unset($matches);
			$matches = explode("||", $url2);
			
			$url2 = $matches[1];
			if(strstr($url2, "http:") === false)
			{
				$url2 = "http://www.dailymotion.com".$url2;
			}
			
			$headers = fetch_headers($url2);
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
			break;
		}
	}
	$flv_link = $link;
	
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
				echo $errormsg;
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

function get_thumb_link($video_data, $url) 
{
	$thumb_link = '';
	
	$pieces = explode('video/', $url);
	$target_url = 'http://www.dailymotion.com/thumbnail/160x120/video/'. $pieces[1];
	
	$headers = fetch_headers($target_url);
	$arr_length = count($headers);
	$link = $url2;
	for($i = 0; $i < $arr_length; $i++)
	{
		if(strstr($headers[$i], "ocation:"))
		{
			$str1 = explode("ocation:", $headers[$i]);
			return trim($str1[1]);
		}
	}
	
	// backup
	$arr_length = count($video_data);
	for($i = 0; $i < $arr_length; $i++)
	{
		if(preg_match('/<media:thumbnail url=\"(.*?)"(.*?)\/>/', $video_data[$i], $matches) != 0)
		{
			$link = $matches[1];
			break;
		}
	}
	//$thumb_link = str_replace("320x240", "160x120", $link);
	$thumb_link = $link;
	if(strstr($thumb_link, "?"))
	{
		$temp = explode("?", $thumb_link);
		$thumb_link = $temp[0];
	}

	return $thumb_link;
}

function video_details($video_data, $url, &$info) 
{	
	$arr_length = count($video_data);
	
	for($i = 0; $i < $arr_length; $i++)
	{
		$video_data[$i] = str_replace( array("\n", "\t", "\r"), '', $video_data[$i]);

		//	video id
		if(strlen($info['yt_id']) == 0)
		{
			if(preg_match("/<dm:id>(.*?)<\/dm:id>/", $video_data[$i], $matches) != 0)
			{			
				$info['yt_id'] = $matches[1];
			}
		}
		
		//	duration
		if(strlen($info['yt_length']) == 0)
		{
			if(preg_match('/<media:content url=\"(.*?)\" type=\"application\/x-shockwave-flash\" duration=\"(.*?)\"(.*?)\/>/i', $video_data[$i], $matches) != 0)
			{			
				$info['yt_length'] = $matches[2];
			}
		}
		
		//	video title
		if(strlen($info['video_title']) == 0)
		{
			if(preg_match("/<media:title>(.*?)<\/media:title>/", $video_data[$i], $matches) != 0)
			{
				$info['video_title'] = $matches[1];
			}	
		}
		
		//	tags
		if(strlen($info['tags']) == 0)
		{
			if(preg_match("/<itunes:keywords>(.*?)<\/itunes:keywords>/", $video_data[$i], $matches) != 0)
			{
				$info['tags'] = $matches[1];
			}
		}
		
		//	description
		if(strlen($info['description']) == 0)
		{
			if(preg_match("/<itunes:summary>(.*?)<\/itunes:summary>/", $video_data[$i], $matches) != 0)
			{
				$info['description'] = $matches[1];
			}
		}
		
		//	mp4/3gp
		if(strlen($info['mp4']) == 0)
		{
			if(preg_match('/<media:content url=\"(.*?)\" type=\"video\/3gpp\"(.*?)\/>/i', $video_data[$i], $matches) != 0)
			{
				$info['mp4'] = $matches[1];
			}
		}
	}
	
	$info['url_flv']	= get_flv($url, $video_data);
	$info['yt_thumb']	= get_thumb_link($video_data, $url);
	
	$direct = $url;
	if(strstr($direct, "http://") === FALSE)
	{
		$direct = "http://" . $direct;
	}
	$info['direct'] = $direct;
}

function download_thumb($thumbnail_link, $upload_path, $video_uniq_id) {
	
	$last_ch = substr($upload_path, strlen($upload_path)-1, strlen($upload_path));
	if($last_ch != "/")
		$upload_path .= "/"; 

	$ext = ".jpg";
	
	$thumb_name = $video_uniq_id . "-1" . $ext;
	
	if(is_file( $upload_path . $thumb_name )) {
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
function do_main(&$video_details, $url)
{
	$video_data = get_info($url);
	if($video_data != false)
	{
		video_details($video_data, $url, $video_details);
	}
	else
	{
		$video_details = array();
	}
}

?>