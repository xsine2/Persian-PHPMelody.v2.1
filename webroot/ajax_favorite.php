<?php
// +------------------------------------------------------------------------+
// | PHP Melody ( www.96down.com )
// +------------------------------------------------------------------------+
// | PHP Melody IS NOT FREE SOFTWARE
// | If you have downloaded this software from a website other
// | than www.96down.com or if you have received
// | this software from someone who is not a representative of
// | phpSugar, you are involved in an illegal activity.
// | ---
// | In such case, please contact: support@96down.com.
// +------------------------------------------------------------------------+
// | Developed by: phpSugar (www.96down.com) / support@96down.com
// | Copyright: (c) 2004-2011 96down.com. All rights reserved.
// +------------------------------------------------------------------------+

session_start();
require('config.php');
require_once('include/functions.php');
require_once('include/user_functions.php');
require_once('include/islogged.php');

// if user is not logged in redirect to homepage
if( !is_user_logged_in() )
{
	header("Location: "._URL. "/index."._FEXT);
	exit();
}

$video_id = secure_sql(trim($_POST['video_id']));
$user_id  = (int) $_POST['user_id'];
$user_id  = secure_sql($user_id);
$res = array();

if(!empty($video_id) && !empty($user_id))
{
	$video = request_video($video_id);
		
	if($video != 0) 
	{
		$res = array();
		
		// CHECK IF THIS VIDEO ISN'T ALREADY MARKED AS FAVORITE
		$query = @mysql_query("SELECT COUNT(*) as total FROM pm_favorites WHERE user_id = '".$user_id."' AND uniq_id = '".$video_id."'");
		$res   = @mysql_fetch_array($query);
		@mysql_free_result($query);
		
		if($res['total'] == 0)
		{
			unset($res);
			$res = array();
			//	CHECK IF ADDING THIS VIDEO TO THE USER'S FAVS LIST WILL EXCEED THE _FAV_LIMIT LIMIT
			$query_l = @mysql_query("SELECT COUNT(*) as total FROM pm_favorites WHERE user_id = '".$user_id."'");
			$res     = @mysql_fetch_array($query_l);

			@mysql_free_result($query_l);
			if($res['total'] < _FAV_LIMIT)
			{
				//	EVERYTHING IS OK, ADD THIS VIDEO TO THE USER'S FAVS LIST
				$query = @mysql_query("INSERT INTO pm_favorites SET uniq_id = '".$video_id."', user_id = '".$user_id."'");
				
				if (_MOD_SOCIAL)
				{
					log_activity(array(
								'user_id' => $userdata['id'],
								'activity_type' => ACT_TYPE_FAVORITE,
								'object_id' => $video['id'],
								'object_type' => ACT_OBJ_VIDEO,
								'object_data' => $video
								)
							);
							
					notify_user(username_to_id($video['submitted']), 
								$userdata['id'],
								ACT_TYPE_FAVORITE, 
								array( 'from_userdata' => $userdata,
								 		'object_type'=> ACT_OBJ_VIDEO,
										'object' => $video
									  )
								);
				}
				
				echo json_encode(array('success' => true, 'btn_label' => $lang["remove_from_fav"], 'msg' => '', 
									   'html' => '<button class="btn btn-small border-radius0 btn-video active" id="fav_save_button" type="button" data-loading-text="Saving..."><i class="icon-heart"></i> '.$lang["remove_from_fav"].'</button>'));
				exit();
			}
		}
		else // remove from favorites
		{
			$query = @mysql_query("DELETE FROM pm_favorites WHERE uniq_id = '".$video_id."' AND user_id = '".$user_id."' LIMIT 1");
			
			if (_MOD_SOCIAL)
			{
				$activity_id = get_activity_id(array('user_id' => $userdata['id'], 
											 'activity_type' => ACT_TYPE_FAVORITE, 
											 'object_id' => $video['id'],
											 'object_type' => ACT_OBJ_VIDEO
											)
									  );
		
				if ($activity_id)
				{
					$activity_data = get_activity_data($activity_id);

					cancel_notification(username_to_id($video['submitted']), 
										$userdata['id'],
										ACT_TYPE_FAVORITE, 
										$activity_data['time']);
					
					delete_activity($activity_id);
				}
			}
			
			echo json_encode(array('success' => true, 'btn_label' => 'Add to favorites', 'msg' => '', 
								   'html' => '<button class="btn btn-small border-radius0 btn-video" id="fav_save_button" type="button" data-loading-text="Saving..."><i class="icon-heart"></i> '.$lang["add_to_fav"].'</button>'));
			exit();
		}
	}
}
exit();
?>