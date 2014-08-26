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

error_reporting(0);
session_start();
@header('Content-Type: text/html; charset=UTF-8;');
require('config.php');
require_once('include/functions.php');
ob_end_clean();

$output 	 = '';

$queryString = trim($_POST['queryString']);
// Is there a posted query string?
if($queryString != '') 
{
	$queryString = secure_sql($queryString);
	$queryString = str_replace(array('%', ','), '', $queryString);
	
	//	only perform queries if the length of the search string is greather than 3 characters
	if(strlen($queryString) >= 3)
	{
		$num_res = 0;
		if(strlen($queryString) > 3)
		{
			$sql	 = "SELECT uniq_id, video_title, yt_id, yt_thumb, source_id, video_slug  
						FROM pm_videos 
						WHERE MATCH(video_title) 
						AGAINST ('$queryString') AS score 
						  AND added <= '". time() ."' 
						ORDER BY score ASC 
						LIMIT 0, 10";
			$query	 = @mysql_query($sql);
			$num_res = @mysql_num_rows($query);
		}
		
		if($num_res == 0)
		{
			$sql = "SELECT video_title, uniq_id, yt_id, yt_thumb, source_id, video_slug 
					FROM pm_videos 
					WHERE added <= '". time() ."' 
					  AND  (video_title LIKE '%$queryString%') 
					LIMIT 0, 10";
			$query = @mysql_query($sql);
		}
		
		if($query)
		{
			while($result = mysql_fetch_array($query))
			{
				$output .= '<li onClick="fill(\''.$result['video_title'].'\');">';
				$output .= '<a href="'. makevideolink($result['uniq_id'], $result['video_title'], $result['video_slug']) .'">';
				
				if (_THUMB_FROM == 2)	//	Localhost
				{
					$output .= '<img src="'. show_thumb($result['uniq_id'], 1, $result) .'" width="40" align="absmiddle" class="pm-sl-thumb opac7" alt="'. htmlentities($result['video_title']).'" />';
				}
				$output .= ''.fewchars($result['video_title']."", 45).'</a>';
				$output .= '</li>';
			}
		} 
		else 
		{
			$output = $lang['search_results_msg3'];
		}
	}
}
echo $output;
exit();