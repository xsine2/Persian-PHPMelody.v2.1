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

define('_videoAdSessionC', "VideoAdSessionC");	//	clicks
define('_videoAdSessionI', "VideoAdSessionI");	//	impressions
define('_videoAdSessionSeconds', 5);

$hash = $_GET['h'];
$action = $_GET['tz'];
if($action == '')
	$action = 'video';

if($hash == '' || (strlen($hash) != 12) || !preg_match("/^[a-z0-9]+$/", $hash))
{
	header('Pragma: no-cache');
	header('Cache-Control: no-store, no-cache, must-revalidate'); 
	header('Content-type: text/html');
	header('HTTP/1.0 404 Not Found');
	exit();
}

switch($action)
{
	default: 
	case 'video':
		//	video ad request
		$sql = "SELECT * FROM pm_videoads WHERE hash='".secure_sql($hash)."'";
		$result = @mysql_query($sql);
		if(!$result)
		{
			header('Pragma: no-cache');
			header('Cache-Control: no-store, no-cache, must-revalidate'); 
			header('Content-type: text/html');
			exit();
		}
		$ad = @mysql_fetch_assoc($result);
		mysql_free_result($result);
		
		if(isset($_SESSION[_videoAdSessionI]) && (is_numeric($_SESSION[_videoAdSessionI])) && (strlen($_SESSION[_videoAdSessionI]) == 10))
		{
			if( time() - $_SESSION[_videoAdSessionI] >= _videoAdSessionSeconds)
			{
				if ($ad['disable_stats'] == 0)
				{
					$sql = "INSERT INTO pm_ads_log (date, ad_id, ad_type, impressions)
							VALUES (CURDATE(), ". $ad['id'] .", ". _AD_TYPE_VIDEO .", 1) 
							ON DUPLICATE KEY 
								UPDATE impressions = impressions + 1";
					@mysql_query($sql); 
				}
				
				//	register session
				$_SESSION[_videoAdSessionI] = time();
			}
		}
		else
		{
			if ($ad['disable_stats'] == 0)
			{
				//	update impressions
				$sql = "INSERT INTO pm_ads_log (date, ad_id, ad_type, impressions)
						VALUES (CURDATE(), ". $ad['id'] .", ". _AD_TYPE_VIDEO .", 1) 
						ON DUPLICATE KEY 
							UPDATE impressions = impressions + 1";
				@mysql_query($sql);
			}
			
			//	register session
			$_SESSION[_videoAdSessionI] = time();		
		}
		//	send headers
		header('Pragma: no-cache');
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Content-Type: video/x-flv");
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
		header('Location: '.$ad['flv_url']);
	break;
	case "t":
		//	redirect to advertiser's page
		$sql = "SELECT * FROM pm_videoads WHERE hash='".secure_sql($hash)."'";
		$result = @mysql_query($sql);
		if(!$result)
		{
			header('Pragma: no-cache');
			header('Cache-Control: no-store, no-cache, must-revalidate'); 
			header('Content-type: text/html');
			header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
			header('HTTP/1.0 404 Not Found');
			exit();
		}
		$ad = @mysql_fetch_assoc($result);
		mysql_free_result($result);

		if(isset($_SESSION[_videoAdSessionC]) && (is_numeric($_SESSION[_videoAdSessionC])) && (strlen($_SESSION[_videoAdSessionC]) == 10))
		{
			if( time() - $_SESSION[_videoAdSessionC] >= _videoAdSessionSeconds)
			{
				//	update clicks
				if ($ad['disable_stats'] == 0)
				{
					$sql = "INSERT INTO pm_ads_log (date, ad_id, ad_type, clicks)
							VALUES (CURDATE(), ". $ad['id'] .", ". _AD_TYPE_VIDEO .", 1) 
							ON DUPLICATE KEY 
								UPDATE clicks = clicks + 1";
					@mysql_query($sql); 
				}
				
				//	register session
				$_SESSION[_videoAdSessionC] = time();
			}
		}
		else
		{
			if ($ad['disable_stats'] == 0)
			{
				$sql = "INSERT INTO pm_ads_log (date, ad_id, ad_type, clicks)
						VALUES (CURDATE(), ". $ad['id'] .", ". _AD_TYPE_VIDEO .", 1) 
						ON DUPLICATE KEY 
							UPDATE clicks = clicks + 1";
				@mysql_query($sql); 
			}
			
			//	register session
			$_SESSION[_videoAdSessionC] = time();
		}
		
		//	redirect user
		header('Pragma: no-cache');
		header('Cache-Control: no-store, no-cache, must-revalidate'); 
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
		header('Location: '.$ad['redirect_url']);
	break;
}
exit();
?>