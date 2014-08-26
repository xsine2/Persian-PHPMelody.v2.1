<?php
session_start();

if(ini_get('max_execution_time') > 60)
{
	set_time_limit(60);
}

header("Expires: Mon, 1 Jan 1999 01:01:01 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");


define('VS_UNCHECKED', 0);
define('VS_OK', 1);
define('VS_BROKEN', 2);
define('VS_RESTRICTED', 3);
define('VS_UNCHECKED_IMG', "vs_unchecked");
define('VS_OK_IMG', "vs_ok");
define('VS_BROKEN_IMG', "vs_broken");
define('VS_RESTRICTED_IMG', "vs_restricted");
define('VS_NOTAVAILABLE_IMG', "vs_na");

define('SLEEP', 1);
define('TIME_DIFF', 5 * 60);

function response($video_id, $status = 0, $message = "")
{
	$status_img = VS_UNCHECKED_IMG;
	
	switch($status)
	{
		case VS_UNCHECKED: 	$status_img = VS_UNCHECKED_IMG;  break;
		case VS_OK: 		$status_img = VS_OK_IMG; 		 break;
		case VS_BROKEN: 	$status_img = VS_BROKEN_IMG; 	 break;
		case VS_RESTRICTED: $status_img = VS_RESTRICTED_IMG; break;
	}	
	return json_encode(array("video_id" => $video_id, "status_img" => $status_img, "message" => $message));
}

function get_video_details($video_id = 0)
{
	if( $video_id )
	{
		$sql = "SELECT * 
				FROM pm_videos 
				LEFT JOIN pm_videos_urls 
				  ON (pm_videos.uniq_id = pm_videos_urls.uniq_id) 
				WHERE pm_videos.id = '". $video_id ."' 
				LIMIT 1";
			
		$result = @mysql_query($sql);
		if(!$result)
		{
			return false;
		}
		$video = mysql_fetch_assoc($result);
		mysql_free_result($result);
		return $video;
	}
	return false;
}

function update_video($video_id = 0, $status = 0)
{
	if($video_id)
	{
		$sql = "UPDATE pm_videos SET status = '".$status."', last_check = '".time()."' WHERE id = '".$video_id."'";
		$result = mysql_query($sql);
		if(!$result)
			return false;
	}
	return true;
}

function vscheck_fetch_headers($url)
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
				return array('error' => $errormsg);
			}				
			$headers = explode("\n", $data);
		}
		else if(ini_get('allow_url_fopen') == 1)
		{
			$fp = @fopen($url, "r");
			if ( ! $fp)
			{
				$error = 1;
			}
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
	if ($error)
	{
		return array('error' => 'خطا در دسترسی به فایل.');
	}
	return $headers;
}

require_once('../config.php');
include_once('functions.php');
include_once( ABSPATH . 'include/user_functions.php');
include_once( ABSPATH . 'include/islogged.php');

if ( ! is_user_logged_in() || ( ! is_admin() && ! is_moderator()) || (is_moderator() && mod_cannot('manage_videos')))
{
	log_error("Unauthorized access attempt", "Video Status Checker", 1);
	$message = json_encode(array("message" => "$x  شما باید به سطح مدیر ارشد دسترسی داشته باشید !"));
	echo $message;
	exit();
}


$job_type = 0;
if( ($_GET['job_type'] != '') || ($_POST['job_type'] != '') )
{
	$job_type = (int) ($_GET['job_type'] != '') ? $_GET['job_type'] : $_POST['job_type'];
}

switch($job_type)
{
	case 0: break;
	case 1:

		$video_id = (int) trim($_POST['vid_id']);

		$video = array();
		$status = 0;
		$message = "";
		
		if($video_id != 0)
		if($video = get_video_details($video_id))
		{
			switch($video['source_id'])
			{
			  case 3:	//	Youtube
				
				if ($video['yt_id'] == "")
				{
					$message = response($video_id, $status, "ویدیو '".$video['uniq_id']."' دارای آی دی اشتباهی می باشد .");
					break;
				}
				
				if((time() - $video['last_check']) > TIME_DIFF)
				{
					$reason = '';
					
					define('PHPMELODY', true);
					include("src/youtube.php");
					
					$video_data = get_info("http://www.youtube.com/watch?v=".$video['yt_id']);

					if(strpos($video_data[0], 'xml') !== FALSE)
					{
						$arr_length = count($video_data);
						for($i = 0; $i < $arr_length; $i++)
						{
							$video_data[$i] = str_replace( array("\n", "\t", "\r"), '', $video_data[$i]);
							if(preg_match('/<yt:noembed/', $video_data[$i]) != 0)
							{
								$status = VS_BROKEN;
								break;
							}
							
							if(preg_match('/<yt:private/', $video_data[$i]) != 0)
							{
								$status = VS_BROKEN;
								break;
							}
							
							if(preg_match('/<media:restriction(.*?)>/', $video_data[$i]) != 0)
							{
								$status = VS_RESTRICTED;
								break;
							}
							
							if(preg_match('/<yt:state name=\'(restricted|deleted|rejected|failed)\' reasonCode=\'(.*?)\'(.*)>(.*?)<\/yt:state>/', $video_data[$i], $matches) != 0)
							{
								$status = ($matches[1] == 'deleted') ? VS_BROKEN : VS_RESTRICTED;
								
								$reason = $video['uniq_id'] .': '. $matches[count($matches) - 1];
								break;
							}

						}
					}
					else
					{
						$status = VS_BROKEN;
					}
					
					if(!$status)
					{
						$status = VS_OK;
					}
					
					unset($video_data, $matches);
				
					update_video($video_id, $status);
				}
				else
				{
					$status = $video['status'];
				}
				
				if(strlen($reason) > 0)
				{
					$message = response($video_id, $status, $reason);
				}
				else
				{
					$message = response($video_id, $status, "");
				}
				
				sleep(SLEEP);
			
			  break;
			  
			  case 22:	//	MTV Music (deprecated)

			  	if ($video['yt_id'] == "")
				{
					$message = response($video_id, $status, "ویدیو '".$video['uniq_id']."' دارای آی دی اشتباهی می باشد .");
					break;
				}
				
				if ((time() - $video['last_check']) > TIME_DIFF)
				{
					$url = '';
					if (preg_match('/mtv\.com\/overdrive\/\?vid=/', $video['direct']) != 0)
					{
						$headers = (array) vscheck_fetch_headers($video['direct']);
						
						$arr_length = count($headers);
						for($i = 0; $i < $arr_length; $i++)
						{
							if(strpos($headers[$i], 'ocation:') !== FALSE)
							{
								$temp = explode("ocation:", $headers[$i]);
								$url  = trim($temp[1]);
								break;
							}
						}
						unset($headers, $str, $temp);
						
						if (strlen($url) > 0)
						{
							//	update new video location
							$sql = "UPDATE pm_videos_urls 
									SET direct = '". secure_sql($url) ."' 
									WHERE uniq_id = '". $video['uniq_id'] ."'";
							$result = mysql_query($sql);
						}
					}
					else if (strpos($video['direct'], 'api.mtvnservices') !== false)
					{
						preg_match('/artist\/(.*?)\//', $video['direct'], $matches);
						$artist_uri = $matches[1];
						$tmp_parts = explode(':', $video['url_flv']);
						$vid = array_pop($tmp_parts);
						
						$url = 'http://www.mtvmusic.com/'. $artist_uri .'/videos/'. $vid .'/video.jhtml';
					}
					else
					{
						$url = $video['direct'];
					}

					if (strlen($url) > 0)
					{
						define('PHPMELODY', true);
						include("./src/mtvmusic.php");

						$video_data = get_info($url);
						if ($video_data === false)
						{
							$status = VS_BROKEN;
						}
						else if (strlen($video_data['MEDIA:RESTRICTION']) > 0)
						{
							$status = VS_RESTRICTED;
						}
					}
					else
					{
						$status = VS_UNCHECKED;
						$reason = $video['uniq_id'] .': سیستک قابلیت بررسی این ویدیو را ندارد.';
					}
					
					if ( ! $status)
					{
						$status = VS_OK;
					}
					
					update_video($video_id, $status);
					
				}	//	end if (time() - Last Check > Time Diff.)
				else
				{
					$status = $video['status'];
				}
				
				if (strlen($reason) > 0)
				{
					$message = response($video_id, $status, $reason);
				}
				else
				{
					$message = response($video_id, $status, "");
				}
				
				sleep(SLEEP);
				
			  break;
			
			  default:
			
				$message = response($video_id, $status, "متاسفانه, ویدیویی با آی دی '".$video['uniq_id']."' قابل بررسی نیست .");
				
			  break;
			  
			  case 1: // localhost
			  	
			  	$error_msg = '';
				
			  	if ($video['url_flv'] != '')
				{
					if (file_exists(_VIDEOS_DIR_PATH . $video['url_flv']))
					{
						if (($size = filesize(_VIDEOS_DIR_PATH . $video['url_flv'])) === 0)
						{
							$status = VS_BROKEN;
							$error_msg = 'فایل <code>'. $video['url_flv'] .'</code> حجمش <strong>'. $size .' بایت</strong> می باشد.';
						}
						else
						{
							$status = VS_OK;
						}
					}
					else
					{
						$status = VS_BROKEN;
						$error_msg = 'فایل <code>'. $video['url_flv'] .'</code> در آدرس <code>'. _VIDEOS_DIR_PATH .'</code> یافت نشد.';
					}
					
					$error_msg = ($error_msg != '') ? $video['uniq_id'] .': '. $error_msg : '';
					
					$message = response($video_id, $status, $error_msg);
					update_video($video_id, $status);
				}
				else
				{
					// url_flv = ''
				}
				
			  break;
			  
			  case 2: // remote file location
			  
			  	$error_msg = '';
				$status = $video['status'];
				
			  	if (is_url($video['url_flv']) || is_ip_url($video['url_flv']))
				{
					$headers = (array) vscheck_fetch_headers($video['url_flv']);
					
					if (array_key_exists('error', $headers))
					{
						$error_msg = 'امکان دریافت اطلاعات وجود ندارد . <br />خطا <code>'. $headers['error'] .'</code>';
					}
					else
					{
						preg_match('/[0-9]{3}/', $headers[0], $matches);
						$code = (int) $matches[0];
						
						unset($matches);
						
						switch ($code)
						{
							case 200:
							case 304: 
								
								$status = VS_OK;
								
							break;
							
							case 301:
							case 302:
								
								// get new location
								foreach ($headers as $k => $v)
								{
									if(strpos($headers[$i], "ocation:") !== false)
									{
										$str1 = explode("ocation:", $headers[$i]);
										$link = trim($str1[1]);
										break;
									}
								}
								
								$status = VS_BROKEN;
								
								$error_msg = 'File moved ';
								$error_msg .= ($code == 301) ? 'برای همیشه' : 'موقتا';
								$error_msg .= ' در این مکان: <code>'. $link .'</code>';
								
							break;
	
							case 400:
							case 401:
							case 403:
							case 404:
							case 501:
							case 502:
								
								$status = VS_BROKEN;
								$error_msg = 'سرور اطلاعات زیر را دربرابر درخواست شما صادر نمود : <code>'. $headers[0] .'</code>.'; 
							
							break;
							
							case 500:
							case 503:
								
								$error_msg = 'سرور موقتا غیر فعال می باشد . بعدا تلاش نمایید.';
								
							break;
						}						
					}
				}
				else
				{
					$error_msg = 'این شبیه یک لینک صحیح نیست : <code>'. $video['url_flv'] .'</code>';
				}
				
				$error_msg = ($error_msg != '') ? $video['uniq_id'] .': '. $error_msg : '';
				
			  	update_video($video_id, $status);
				$message = response($video_id, $status, $error_msg);
				
			  break;
			}	//	end main switch()
		}
		else
		{
			$message = response($video_id, $status, "خطا : امکان بازیابی ویدیو وجود ندارد.");
		}

		echo $message;
	break;
}
exit();
?>