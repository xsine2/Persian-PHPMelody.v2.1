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

$xml = array();
$xml_index = "";
$xml_array_counter = 0;
$xml_error_msg = '';

function get_info($url)
{
	global $xml, $xml_array_counter, $xml_index, $xml_error_msg;
	
	$channel = '';
	$video_data = array();
	
	$pieces = explode('/', $url);

	if (strpos($pieces[3], '#'))
	{
		$buff = explode('#', $pieces[3]);
		$pieces[3] = $buff[0];		
	}
	
	if ($pieces[4] == "b" && is_numeric($pieces[5]))
	{
		$type = 'video';
		$channel = $pieces[3];
		
		$target_url = 'http://api.justin.tv/api/broadcast/show/'. $pieces[5] .'.xml';
	}
	else
	{
		$type = 'channel';
		$channel = $pieces[3];

	 	$channel = strtolower($channel);
		//$channel = str_replace('_', '-', $channel);
		
		$target_url = 'http://www.justin.tv/meta/'. $channel .'.xml';
	}
	
	unset($buff, $matches);
	
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
	
	$xml_parser = xml_parser_create();
	xml_set_element_handler($xml_parser, "parser_startElement", "parser_endElement");
	xml_set_character_data_handler($xml_parser, "parser_characterData");
	if(is_array($video_data))
	{
		$video_data = implode("", $video_data);
	}
	
	if(!xml_parse($xml_parser, $video_data, TRUE))
	{
		$xml_error_msg = sprintf("XML error: %s at line %d", 
					xml_error_string(xml_get_error_code($xml_parser)),
					xml_get_current_line_number($xml_parser));
	}
	xml_parser_free($xml_parser);
	
	unset($video_data);
		
	$xml['video_type'] = $type;
	$xml['channel'] = $channel;


	return $xml;
}

function get_flv($video_data, $url)
{
	$url_flv = '';
	$pieces = explode('/', $url);
	
	if ($video_data['video_type'] == 'channel')
	{
		$url_flv = 'http://www.justin.tv/widgets/live_embed_player.swf?channel='. $video_data['channel'];
	}
	else 
	{	
		$url_flv = 'http://www.justin.tv/widgets/archive_embed_player.swf?archive_id='. $video_data[0]['ID'] .'&channel='. $video_data[0]['CHANNEL_NAME'];
	}
	
	return $url_flv;
}

function get_thumb_link($video_data) 
{
	$thumb_url = '';
	
	if ($video_data['video_type'] == 'channel')
	{
		$thumb_url = str_replace('28x28', '150x150', $video_data[0]['URL']);
	}
	else
	{
		$thumb_url = 'http://static-cdn.justin.tv/jtv.thumbs/archive-'. $video_data[0]['ID'] .'-150x113.jpg';
	}
	
	if ($thumb_url == '')
	{
		$thumb_url = $video_data[0]['SCREEN_CAP'];
	}

	return $thumb_url;
}


function video_details($video_data, $url, &$info) 
{
	//	flv
	$info['url_flv'] = get_flv($video_data, $url);
	
	//	thumbnail link
	$info['yt_thumb'] = get_thumb_link($video_data);
	
	//	title
	$info['video_title'] = $video_data[0]['TITLE'];
	
	//	video id
	$info['yt_id'] = $video_data['channel'];
	
	//	direct link
	$info['direct'] = $url;
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

function do_main(&$video_details, $url, $show_warnings = true)
{
	$video_data = get_info($url);
	if($video_data != false)
	{
		video_details($video_data, $url, $video_details);
	}
	else
	{
		if ($show_warnings && $xml_error_msg != '')
		{
			echo '<div class="alert alert-error">'. $xml_error_msg .'</div>';
		}
		$video_details = array();
	}
}

function parser_startElement($parser, $name, $attrs) 
{
	global $xml, $xml_array_counter, $xml_index;
	switch($name) 
	{
		case "ENTRY":
			$xml_index = "";
			break;
		default:
			$xml_index = $name;
			if(count($attrs) > 0)
			{
				foreach($attrs as $key => $value) 
				{
					$xml[$xml_array_counter][$xml_index][strtolower($key)] = $value;
				}
			}
			else
			{
				$xml[$xml_array_counter][$xml_index] = "";
			}
			break;
	}
}

function parser_endElement($parser, $name) 
{
	global $xml, $xml_index, $xml_array_counter;
	switch($name) 
	{
		case "ENTRY":
			$xml_array_counter++;
			break;
	}
	$xml_index = "";
}

function parser_characterData($parser, $data) 
{
	global $xml, $xml_array_counter, $xml_index;
	if ($xml_index != "") 
	{
		$xml[$xml_array_counter][$xml_index] .= trim($data);
	}
}

?>