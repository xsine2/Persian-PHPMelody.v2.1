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

if ($_GET['p'] == 'upload' || $_POST['p'] == 'upload')
{
	if ($_POST['SID'] != '')
	{
		session_id($_POST['SID']);
	}
}
session_start();

@header('Content-Type: text/html; charset=UTF-8;');

require('config.php');
require_once('include/functions.php');
require_once('include/user_functions.php');
require_once('include/islogged.php');

$illegal_chars = array(">", "<", "&", "'", '"', '*', '%');

$message = '';
$page	 = '';
$action  = '';

if ($_GET['p'] != '' || $_POST['p'] != '')
{
	$page = ($_GET['p'] != '') ? $_GET['p'] : $_POST['p'];
}

if ($_GET['do'] != '' || $_POST['do'] != '')
{
	$action = ($_GET['do'] != '') ? $_GET['do'] : $_POST['do'];
}

if ($page == '')
{
	exit();
}
$modframework->trigger_hook('ajax_top');
switch ($page)
{
	case 'comments':
		
		if (isset($_GET['do']))
		{
			switch ($action)
			{
				case 'show_comments':
					$modframework->trigger_hook('ajax_show_comments');
					
					$page 	 = (int) $_GET['page'];
					$uniq_id = $_GET['vid'];
					$uniq_id = str_replace($illegal_chars, "", $uniq_id);
					
					if ($page > 0 && preg_match('/([0-9a-zA-Z-]{5,20})/', $uniq_id) != 0)
					{
						$most_liked_comment  = false;
						
						$comment_list = get_comment_list($uniq_id, $page);
						$comment_count = count_entries('pm_comments', 'uniq_id', $uniq_id."' AND approved='1");
						
						if ($comment_count > 0)
						{
							$most_liked_comment = get_most_liked_comment($uniq_id);
							$most_liked_comment = (array) $most_liked_comment[0];
							
							if ($most_liked_comment['up_vote_count'] <= 2)
							{
								$most_liked_comment = false;
							}
							
							// remove duplicate
							if ($config['comment_default_sort'] == 'score' && $page == 1 && is_array($most_liked_comment))
							{
								unset($comment_list[0]);
							}
						}
						$smarty->assign('most_liked_comment', $most_liked_comment);
						
						$mod_can = mod_can();
				
						if ($userdata['power'] == U_ADMIN || ($userdata['power'] == U_MODERATOR && $mod_can['manage_comments']))
						{
							$smarty->assign('can_manage_comments', true);
						}
						else
						{
							$smarty->assign('can_manage_comments', false);
						}
						
						$smarty->assign('comment_list', $comment_list);
						$smarty->assign('comment_count', $comment_count);
						
						$comment_list_html = $smarty->fetch('comment-list.tpl');
						
						$comment_pagination_obj = '';
						if ($comment_count > $config['comments_page'])
						{
							$comment_pagination_obj = generate_comment_pagination_object($uniq_id, $page, $comment_count, $config['comments_page']);
						}
						$smarty->assign('comment_pagination_obj', $comment_pagination_obj);
						$comment_pagination_html = $smarty->fetch('comment-pagination.tpl');
						$modframework->trigger_hook('ajax_show_comments_output');
						echo $comment_list_html . "\n". $comment_pagination_html;
						exit();
					}
					
				break;
				
				case 'onpage_delete_comment':
					$modframework->trigger_hook('ajax_onpage_delete_comment');
					$uniq_id 	= $_GET['vid'];
					$comment_id	= (int) $_GET['cid'];
					$uniq_id = str_replace($illegal_chars, "", $uniq_id);
					$uniq_id = secure_sql($uniq_id);
					
					if (is_admin() || (is_moderator() && mod_can('manage_comments')) 
						&& (preg_match('/([0-9a-zA-Z-]{5,20})/', $uniq_id) != 0)  
						&& $comment_id != 0
						)
					{
						if (_MOD_SOCIAL)
						{
							$sql = "SELECT id, uniq_id, user_id 
									FROM pm_comments WHERE id = '" . $comment_id . "'";
							if ($result = mysql_query($sql))
							{
								$row = mysql_fetch_assoc($result);
								$sql = "DELETE FROM pm_activity 
										WHERE user_id = '". $row['user_id'] ."' 
										  AND activity_type = '". ACT_TYPE_COMMENT ."'
										  AND object_id = '". $row['id'] ."' 
										  AND object_type = '". ACT_OBJ_COMMENT ."'";
								@mysql_query($sql);
								mysql_free_result($result);
							}
						}
						$sql = "DELETE 
								FROM pm_comments 
								WHERE id = '". $comment_id ."' 
								  AND uniq_id = '". $uniq_id ."' 
								LIMIT 1";
						$result = mysql_query($sql);
						
						$sql = "DELETE FROM pm_comments_reported WHERE comment_id = '". $comment_id ."'";
						$result = mysql_query($sql);
						
						$sql = "DELETE FROM pm_bin_rating_votes WHERE uniq_id = 'com-". $comment_id ."'";
						$result = mysql_query($sql);
					}
					exit();
				break;
				
				case 'like':
				case 'dislike':
				case 'upvote':
				case 'downvote':
				
					if ( ! is_user_logged_in())
					{
						echo json_encode(array('success' => false,
											   'msg' => $lang['login_first']
											  ));
						exit();
					}
	
					$response 	= array('success' => false, 'msg' => '');
					$comment_id = (int) $_GET['comment_id'];
					
					if ( ! $comment_id)
					{
						echo json_encode($response);
						exit();
					}
					
					// check if comment exists
					$sql = "SELECT uniq_id, user_id, approved, up_vote_count, down_vote_count, score 
							FROM pm_comments 
							WHERE id = '". $comment_id ."'";
					$result = mysql_query($sql);
					$comment_data = mysql_fetch_assoc($result);
					mysql_free_result($result);
					
					if ( ! $comment_data || $comment_data['approved'] != 1 || $userdata['id'] == $comment_data['user_id'])
					{
						echo json_encode($response);
						exit();
					}
					
					require_once('include/rating_functions.php');
					
					$uniq_id = 'com-'. $comment_id;
					$vote_value = 0;
							
					switch ($action)
					{
						case 'like':
						case 'upvote':
							$vote_value = 1;
						break;
					}
					
					if ( ! bin_rating_user_can_vote())
					{
						echo json_encode($response);
						exit();
					}
					
					$current_vote_value = bin_rating_user_has_voted($uniq_id);
					if ($current_vote_value === false && $vote_value < 0)
					{
						// shouldn't come to this but if it does,
						// we won't let the user delete something he doesn't own
						$response['success'] = true; // 'true' so it won't trigger any unnecessary errors
						echo json_encode($response);
						exit(); 
					}
					
					if ($current_vote_value === false) // new vote
					{
						$vote = bin_rating_insert_vote($uniq_id, $vote_value);
						$up_vote_count = ($vote_value) ? $comment_data['up_vote_count'] + 1 : $comment_data['up_vote_count'];
						$down_vote_count = ($vote_value) ? $comment_data['down_vote_count'] : $comment_data['down_vote_count'] + 1;
					}
					else
					{
						if ($current_vote_value != $vote_value) // user wants to change vote 
						{
							if ($vote_value < 0) // delete
							{
								$vote = bin_rating_delete_vote($uniq_id);
								$up_vote_count = ($current_vote_value) ? $comment_data['up_vote_count'] - 1 : $comment_data['up_vote_count'];
								$down_vote_count = ($current_vote_value) ? $comment_data['down_vote_count'] : $comment_data['down_vote_count'] - 1;
							}
							else // update
							{
								$vote = bin_rating_update_vote_value($uniq_id, $vote_value);
								
								if ($vote_value > 0)
								{
									$up_vote_count = $comment_data['up_vote_count'] + 1;
									$down_vote_count = $comment_data['down_vote_count'] - 1;
								}
								else
								{
									$up_vote_count = $comment_data['up_vote_count'] - 1;
									$down_vote_count = $comment_data['down_vote_count'] + 1;
								}
							}
						}
						else
						{
							$vote = bin_rating_delete_vote($uniq_id);
							$up_vote_count = ($current_vote_value) ? $comment_data['up_vote_count'] - 1 : $comment_data['up_vote_count'];
							$down_vote_count = ($current_vote_value) ? $comment_data['down_vote_count'] : $comment_data['down_vote_count'] - 1;
						}
					}
					
					if ($vote)
					{
						$score = bin_rating_calc_score($up_vote_count, $down_vote_count);
						$sql = "UPDATE pm_comments 
								SET up_vote_count = '". $up_vote_count ."',
									down_vote_count = '". $down_vote_count ."',
									score = '". $score ."' 
								WHERE id = '". $comment_id ."'";
						$result = mysql_query($sql);
						
						$response['up_vote_count'] = (int) $up_vote_count;
						$response['down_vote_count'] = (int) $down_vote_count;
						$response['success'] = true;
					}
					else
					{
						// error
					}

					echo json_encode($response);
					exit();
					
				break;
				case 'flag':
					
					if ( ! is_user_logged_in())
					{
						echo json_encode(array('success' => false,
											   'msg' => $lang['login_first']
											  ));
						exit();
					}
	
					$response 	= array('success' => false, 'msg' => '');
					$comment_id = (int) $_GET['comment_id'];
					
					if ( ! $comment_id)
					{
						echo json_encode($response);
						exit();
					}
					
					// check if user has already flagged this comment
					$flagged = user_has_flagged_comment($comment_id);
					
					if ($flagged)
					{
						// remove flag
						$sql = "DELETE FROM pm_comments_reported  
								WHERE user_id = '". $userdata['id'] ."' 
							  	  AND comment_id = '". $comment_id ."'";
						mysql_query($sql);
						
						// report_count--
						$sql = "UPDATE pm_comments SET report_count = report_count - 1
								WHERE id = '". $comment_id ."'";
						mysql_query($sql);
						
						$response['success'] = true; 
					}
					else
					{
						// raise flag
						$sql = "INSERT INTO pm_comments_reported
										(user_id, comment_id)  
								VALUES ('". $userdata['id'] ."', '". $comment_id ."')";
						mysql_query($sql);
						
						// report_count++
						$sql = "UPDATE pm_comments SET report_count = report_count + 1
								WHERE id = '". $comment_id ."'";
						mysql_query($sql);
						$response['success'] = true;
					}
					
					echo json_encode($response);
					exit();
				break;
				
				default:
					exit();
				break;
			}
		}
	break;
	
	case 'video':
		
		switch ($action)
		{
			case 'request':
				$modframework->trigger_hook('ajax_request_video');
				$video_id 	= $_GET['vid'];
				$video_id 	= str_replace($illegal_chars, "", $video_id);
				$video 		= request_video($video_id, 'detail', true);
				
				if ( ! is_user_logged_in() && $video['restricted'] == '1')
				{
					$smarty->assign('lang', $lang);
					$smarty->display('restricted_video.tpl');
					exit();
				}

				if ( ! is_array($video))
				{
					exit();
				}
				
				echo $video['embed_code'];
			break;
			
			case 'report':
				$modframework->trigger_hook('ajax_report_video');
				
				$video_id 	= $_GET['vid'];
				$video_id 	= str_replace($illegal_chars, "", $video_id);
				
				if (preg_match('/([0-9a-zA-Z-]{5,20})/', $video_id) != 0)
				{
					$video 		= request_video($video_id, 'detail', true);
					if (is_array($video))
					{
						
						report_video($video['uniq_id'], '1', 'This video might be "dead".', 'PM Bot');
					}
				}

			break;
			
			case 'like':
			case 'upvote':
			case 'dislike':
			case 'downvote':
				
				$allow_anon = (int) get_config('bin_rating_allow_anon_voting');
					
				if ( ! $allow_anon && ! is_user_logged_in())
				{
					echo json_encode(array('success' => false,
										   'msg' => $lang['login_first']
										  ));
					exit();
				}

				require_once('include/rating_functions.php');
				
				$response 	= array('success' => false, 'msg' => '');
				$video_id 	= trim($_GET['vid']);
				
				if (preg_match('/([0-9a-zA-Z-]{5,20})/', $video_id) != 0)
				{
					$video = request_video($video_id, 'detail', true);
					if (is_array($video))
					{
						$vote_value = 0;
						
						switch ($action)
						{
							case 'like':
							case 'upvote':
								$vote_value = 1;
							break;
						}
						
						$voted = bin_rating_vote($video['uniq_id'], $vote_value);
						
						$response['success'] = true;
						$item_meta = bin_rating_get_item_meta($video['uniq_id']);
						$balance = bin_rating_calc_balance($item_meta['up_vote_count'], $item_meta['down_vote_count']);
						$response = array_merge($response, $balance, $item_meta);
					}
					else
					{
						$response['success'] = false;
						$response['msg'] = $lang['video_not_found'];
					}
				}
				else
				{
					$response['success'] = false;
					$response['msg'] = $lang['video_not_found'];
				}
				
				echo json_encode($response);
				exit();
			break;
			
			case 'getplayer': // called after a Pre-roll ad has finished running
			
				$uniq_id = trim($_GET['vid']);
				$ad_id = (int) $_GET['aid'];
				$player_page = trim($_GET['player']);
				
				if ($player_page == '' || ! in_array($player_page, array('index', 'detail', 'favorites', 'embed')))
				{
					$player_page = 'detail';
				}
	
				if(strlen($uniq_id) < 10 && strlen($uniq_id) > 5)
				{
					if(!ctype_alnum($uniq_id))
						$uniq_id = '';
					else
						$uniq_id = secure_sql($uniq_id);
				}
				else
				{
					$uniq_id = '';
				}
				
				if ($uniq_id == '')
				{
					exit('Invalid video ID');
				}
				
				//	set ad delay cookie ?
				if (empty($_COOKIE[COOKIE_PREROLLAD]))
				{
					if ($config['total_preroll_ads'] > 0)
					{
						if ($config['preroll_ads_delay'] != 0)
						{
							setcookie(COOKIE_PREROLLAD, PREROLL_AD_HASH, time() + $config['preroll_ads_delay'], COOKIE_PATH);
						}
					}
				}

				$video = request_video($uniq_id);
				
				$modframework->trigger_hook('ajax_request_player');
				$smarty->assign('embedcode', generate_embed_code($video['uniq_id'], $video, false, 'iframe'));
				$smarty->assign('voth', $video['uniq_id']); // for $page == index;
				$smarty->assign('page', $player_page);
				$smarty->assign('video_data', $video);
				$html = $smarty->fetch('player.tpl');
				echo $html;
				
				exit();
			break;
		
			default:
				exit();
			break;
		}
		
		
	break;
	
	case 'favorites':
	
		switch ($action)
		{
			case 'request':
				$modframework->trigger_hook('ajax_favorites_request');
				$video_id 	= $_GET['vid'];
				$video_id 	= str_replace($illegal_chars, "", $video_id);
				$video 		= request_video($video_id, 'favorites', true);
				$embed_code	= '';
				
				if ( ! is_array($video))
				{
					exit();
				}
				
				if ( ! is_user_logged_in() && $video['restricted'] == '1')
				{
					$smarty->assign('lang', $lang);
					$smarty->display('restricted_video.tpl');
					exit();
				}
				
				if ($video['video_player'] == 'flvplayer')
				{
					$embed_code  = '<embed src="'. _URL .'/player.swf?config=';
					$embed_code .= '{';
					$embed_code .= "embedded: true,
									showOnLoadBegin: true, 
									useHwScaling: false, 
									showStopButton: true, 
									menuItems: [false, false, true, true, true, false, false], 
									timeDisplayFontColor: '0x". _TIMECOLOR ."', 
									controlBarBackgroundColor: '0x". _BGCOLOR ."', 
									progressBarColor2: '0x000000', 
									progressBarColor1: '0xFFFFFF', 
									watermarkLinkUrl: '". _WATERMARKURL ."', 
									showWatermark: '". _WATERMARKSHOW ."', 
									watermarkUrl: '". _WATERMARKURL  ."', 
									controlsOverVideo: 'locked', 
									controlBarGloss: 'high', 
									useNativeFullScreen: true, 
									showPlayListButtons: false, 
									initialScale: 'fit', 
									hideControls: false, 
									loop: false, 
									bufferLength: 5, 
									startingBufferLength: 2, 
									autoBuffering: ". _AUTOBUFF .", 
									autoPlay: true, 
									baseURL: '', 
									useSmoothing: true,";
									
					$embed_code .= "playList: [ { overlayId: 'play', 
												  name: 'ClickToPlay'
												 }, 
												 {  linkWindow: '_blank', 
												 	linkUrl: '". _URL ."/watch.php?vid=". $video['uniq_id'] ."', 
													url: '". _URL ."/videos.php?vid=". $video['uniq_id'] ."', 
													name: '". rawurlencode($video['video_title']) ."'
												}]";
					$embed_code .= '}");';
					
					$embed_code .= '" width="'. $config['player_w_favs'] .'" height="'. $config['player_h_favs'] . '"';
					$embed_code .= ' scale="noscale" bgcolor="'. _BGCOLOR .'"';
					$embed_code .= ' type="application/x-shockwave-flash" allowFullScreen="true" allowScriptAccess="always" ';
					$embed_code .= ' allowNetworking="all" pluginspage="http://www.macromedia.com/go/getflashplayer">';
					$embed_code .= '</embed>';
					$embed_code = str_replace( array("\n", "\r", "\t"), "", $embed_code);
				}
				else if ($video['video_player'] == 'jwplayer')
				{
					$embed_code  = '<embed src="'. _URL .'/jwplayer.swf" ';
					$embed_code .= ' width="'. $config['player_w_favs'] .'" height="'. $config['player_h_favs'] . '"';
					$embed_code .= ' scale="noscale" bgcolor="'. _BGCOLOR .'"';
					$embed_code .= ' type="application/x-shockwave-flash" allowFullScreen="true" ';
					$embed_code .= ' allowScriptAccess="always" wmode="transparent" ';
					$embed_code .= ' flashvars="';
					
					if ($video['source_id'] == 3)
					{
						$embed_code .= '&file='. urlencode($video['direct']);
						$embed_code .= '&type=youtube';
					}
					else if ($video['source_id'] == 0)
					{
						$embed_code .= '&file='. urlencode($video['jw_flashvars']['file']);
						$embed_code .= '&streamer='. urlencode($video['jw_flashvars']['streamer']);
						$embed_code .= ($video['jw_flashvars']['provider'] != '') ? '&provider='. $video['jw_flashvars']['provider'] : '';
						$embed_code .= ($video['jw_flashvars']['startparam'] != '') ? '&http.startparam='. $video['jw_flashvars']['startparam'] : '';
						$embed_code .= ($video['jw_flashvars']['loadbalance'] != '') ? '&rtmp.loadbalance='. $video['jw_flashvars']['loadbalance'] : '';
						$embed_code .= ($video['jw_flashvars']['subscribe'] != '') ? '&rtmp.subscribe='. $video['jw_flashvars']['subscribe'] : '';
					}
					else
					{
						$embed_code .= '&file='. urlencode(_URL ."/videos.php?vid=". $video['uniq_id']);
						$embed_code .= '&type=video';
					}
					$embed_code .= '&backcolor='. _BGCOLOR;
					$embed_code .= '&frontcolor='. _TIMECOLOR;
					$embed_code .= '&screencolor=000000';
					$embed_code .= '&bufferlength=5';
					$embed_code .= '&controlbar=over';
					$embed_code .= '&autostart=true';
					$embed_code .= '&logo='. urlencode(_WATERMARKURL);
					$embed_code .= '&link='. urlencode(_WATERMARKLINK);
					$embed_code .= '&skin='. urlencode(_URL).'/skins/'._JWSKIN;
					$embed_code .= '&plugins=timeslidertooltipplugin-2';
					$embed_code .= '">';
					$embed_code .= '</embed>';
					$embed_code = str_replace( array("\n", "\r", "\t"), "", $embed_code);
				}
				else if ($video['video_player'] == 'jwplayer6')
				{
					$jw_file = $video['url_flv'];
					
					if ($video['source_id'] == 3)
					{
						$jw_file = $video['direct'];
					}
					else if ($video['source_id'] == 0)
					{
						$jw_file = $video['jw_flashvars']['file'];
					}
					else
					{
						if (_SEOMOD)
						{
							$jw_file = _URL ."/videos.flv?vid=". $video['uniq_id'];
						}
					}
					
					$rtmp = '';
					$rtmp .= ($video['jw_flashvars']['provider'] != '') ? " provider: '". $video['jw_flashvars']['provider'] ."', " : '';
					$rtmp .= ($video['jw_flashvars']['startparam'] != '') ? " startparam: '". $video['jw_flashvars']['startparam'] ."', " : '';
					$rtmp .= ($video['jw_flashvars']['loadbalance'] != '') ? " loadbalance: ". $video['jw_flashvars']['loadbalance'] .", " : '';
					$rtmp .= ($video['jw_flashvars']['subscribe'] != '') ? " subscribe: ". $video['jw_flashvars']['subscribe'] .", " : '';
					$rtmp .= ($video['jw_flashvars']['securetoken'] != '') ? " securetoken: '". $video['jw_flashvars']['securetoken'] ."', " : '';
					$rtmp = rtrim($rtmp, ',');
					$rtmp = ($rtmp != '') ? 'rtmp: { '. $rtmp .'}, ' : '';
					
					$embed_code .= '<script type="text/javascript" src="'. _URL .'/jwplayer.js"></script>';
					$embed_code .= '<script type="text/javascript">';
					$embed_code .= "
							var flashvars = {
								file : '". $jw_file ."',
								$rtmp
								primary: 'flash',
								width: '". $config['player_w_favs'] ."',
								height: '". $config['player_h_favs'] ."',
								image: '".$video['yt_thumb']."',
								logo: {file: '". _WATERMARKURL ."',link: '". _WATERMARKLINK ."'},
								autostart: 'true'
							};
							jwplayer('Playerholder').setup(flashvars);
						</script>";
					$embed_code .= '<div id="Playerholder"></div>';
					
					$embed_code = str_replace( array("\n", "\r", "\t"), "", $embed_code);
				}
				else
				{
					$embed_code = $video['embed_code'];	
				}
				$modframework->trigger_hook('ajax_favorites_request_output');
				update_view_count($video['id'], $video['site_views']);
				
				echo $embed_code;
				
			break;
			
			case 'onpage_delete_favorite':
				$modframework->trigger_hook('ajax_favorites_delete');
				if ($logged_in && is_int($userdata['id']) && $userdata['id'] > 0)
				{
					$video_id = $_GET['vid'];
					$video_id = str_replace($illegal_chars, "", $video_id);
					$video_id = secure_sql($video_id);
					
					if (preg_match('/([0-9a-zA-Z-]{5,20})/', $video_id) != 0)
					{
						$sql = "DELETE 
								FROM pm_favorites 
								WHERE uniq_id = '".$video_id."' 
								  AND user_id = '".$userdata['id']."'";
						$result =  @mysql_query($sql);
					}
				}
				
			break;
			
			default:
				exit();
			break;
		}
		
	break;
	
	case 'users':
	
		if (is_admin() || (is_moderator() && mod_can('manage_users')))
		{
			$user_id = (int) $_GET['uid'];
			$user_id = abs($user_id);
			
			if ($user_id == 0)
			{
				if ($config['guests_can_comment'] == 1)
				{
					exit(json_encode(array('success' => false, 'msg' => '', 'error' => 'Visitors cannot be banned. You can disable visitor commenting from your Admin Area.')));
				}
				
				exit(json_encode(array('success' => false, 'msg' => '', 'error' => 'No user ID provided.')));
			}
			
			if ($user_id == $userdata['id'])
			{
				exit(json_encode(array('success' => false, 'msg' => '', 'error' => 'You?')));
			}
	
			$banned = array();
					
			$sql = "SELECT * 
					FROM pm_users 
					WHERE id = '". $user_id ."'";
			$result = @mysql_query($sql);
			if ( !  $result)
			{
				log_error('MySQL Error: '. mysql_error() . '<br>File: '. __FILE__ .' on line '. __LINE__, 'User Management', '1');
				exit(json_encode(array('success' => false, 'msg' => '', 'error' => 'Could not ban account. Check the System Log for more details.')));
			}
			
			$user = mysql_fetch_assoc($result);
			mysql_free_result($result);
	
			$banned = banlist($user['id']);
			$span_id = (int) $_GET['spanid'];
			$html = '';
			
			if ($action == 'allow' && ! $banned)
			{
				$action = 'ban';
			}
			else if ($action == 'ban' && $banned['user_id'] == $user['id'])
			{
				$action = 'allow';
			}
			
			switch ($action)
			{
				case 'allow':
	
					if($banned['user_id'] == $user['id'])
					{
						$sql = "DELETE 
								FROM pm_banlist 
								WHERE user_id ='". $user['id'] ."'";
	
						$result = @mysql_query($sql);
						if ( ! $result)
						{
							log_error('MySQL Error: '. mysql_error() . '<br>File: '. __FILE__ .' on line '. __LINE__, 'User Management', '1');
							exit(json_encode(array('success' => false, 'msg' => '', 'error' => 'Could not ban account. Check the System Log for more details.')));
						}
						else
						{
							$response = array('success' => true, 
											  'msg' => 'Done',
											  'hide_label' => true
											  );
							exit(json_encode($response));
						}
					}
	
				break;
				
				case 'ban':
				
					if ($user['power'] == U_ADMIN)
					{
						exit(json_encode(array('success' => false, 'msg' => '', 'error' => 'Administrator accounts cannot be banned.')));
					}
					
					if ($banned['user_id'] == $user['id'])
					{
						exit(json_encode(array('success' => false, 'msg' => '', 'error' => 'This account is already banned.'))); 
					}
					
				
					$sql = "INSERT INTO pm_banlist 
							SET user_id = '". $user['id'] ."', 
								reason = ''";
					$result = @mysql_query($sql);
					if ( ! $result)
					{
						log_error('MySQL Error: '. mysql_error() . '<br>File: '. __FILE__ .' on line '. __LINE__, 'User Management', '1');
						exit(json_encode(array('success' => false, 'msg' => '', 'error' => 'Could not ban account. Check the System Log for more details.'))); 
					}
					$response = array('success' => true, 
									  'msg' => $lang['user_account_banned'],
									  'show_label' => true
									  );
					exit(json_encode($response));
					
				break;
			
				default:
					exit();
				break;
			}
		}
		
		exit(json_encode(array('success' => false, 'msg' => 'Not allowed')));
		
	break;
	
	case 'detail': 
		
		switch ($action)
		{
			
			case 'share':
			case 'report':
				
				$uniq_id = trim($_POST['vid']);
				if(strlen($uniq_id) < 10 && strlen($uniq_id) > 5)
				{
					if(!ctype_alnum($uniq_id))
						$uniq_id = '';
					else
						$uniq_id = secure_sql($uniq_id);
				}
				else
				{
					$uniq_id = '';
				}
				
				if ($uniq_id == '')
				{
					echo json_encode(array('success' => false,
										    'msg' => 'Invalid video ID'
										  ));
					exit();
				}
				
				$video = request_video($uniq_id);
				
				if ( ! is_user_logged_in() && $video['restricted'] == '1')
				{
					echo json_encode(array('success' => false,
										    'msg' => $lang['registration_req']
										  ));
					exit ();
				}
				
				foreach ($_POST as $k => $v)
				{
					$_POST[$k] = htmlspecialchars($v);
				}
				
				if ( ! is_user_logged_in())
				{
					// check captcha code
			    	include ("include/securimage.php");
			    	$img = new Securimage();
					$valid = $img->check($_POST['imagetext']);
			    	if ( ! $valid)
					{
						echo json_encode(array('success' => false,
										    'msg' => $lang['register_err_msg1']
										  ));
						exit ();
					}
				}
			
				$post_email = trim($_POST['email']);
				$post_name = secure_sql(trim($_POST['name']));
				$post_reason = secure_sql(trim($_POST['reason']));
				
				if ($action == 'share')
				{
					if ( ! is_real_email_address($post_email)) 
					{
						echo json_encode(array('success' => false,
										       'msg' => $lang['register_err_msg2']
										  	  ));
						exit();
					}
					
					// ** SENDING EMAIL ** //
					require_once("include/class.phpmailer.php");
					$mailsubject = sprintf($lang['mailer_subj5'], $post_name);
					$array_content[]=array("mail_from", $post_name);  
					$array_content[]=array("video_id", $video['uniq_id']);
					$array_content[]=array("video_name", $video['video_title']);
					$array_content[]=array("site_url", _URL);
					
					if(file_exists('./email_template/'.$_language_email_dir.'/email_send_to_friend.txt'))
					{
						$mail = send_a_mail($array_content, $post_email, $mailsubject, 'email_template/'.$_language_email_dir.'/email_send_to_friend.txt');
					}
					elseif(file_exists('./email_template/english/email_send_to_friend.txt'))
					{
						$mail = send_a_mail($array_content, $post_email, $mailsubject, 'email_template/english/email_send_to_friend.txt');
					}
					elseif(file_exists('./email_template/email_send_to_friend.txt'))
					{
						$mail = send_a_mail($array_content, $post_email, $mailsubject, 'email_template/email_send_to_friend.txt');
					}
					else
					{
						@log_error('Email template "email_send_to_friend.txt" not found!', 'Share Video', 1);
						$mail = TRUE;
					}
					if($mail !== TRUE)
					{
						@log_error($mail, 'Share Video', 1);
					}
					
					if (_MOD_SOCIAL && is_user_logged_in())
					{
						log_activity(array(
							'user_id' => $userdata['id'],
							'activity_type' => ACT_TYPE_SEND_VIDEO,
							'object_id' => $video['id'],
							'object_type' => ACT_OBJ_VIDEO,
							'object_data' => $video
							)
						);
					}
					
					echo json_encode(array('success' => true,
										   'msg' => $lang['share_msg1']
										   ));
				}
				else
				{
					report_video($uniq_id, 1, $post_reason, $post_name);
					
					echo json_encode(array('success' => true,
										   'msg' => $lang['report_msg2']
										   ));
				}

			break;
		}
		
	break;
	case 'suggest':
		
		$response = array('failed' => true);
			
		require_once('admin/functions.php');
		
		switch ($action)
		{
			case 'getdata':
				$url = trim($_POST['url']);
				$url = str_replace('https', 'http', $url);
				$url = str_replace('youtu.be/', 'youtube.com/watch?v=', $url);

				if ($url == '')
				{
					// empty URL
					$response = array('failed' => true,
									  'message' => 'Video URL '. $lang['register_err_msg8']
									 );
				}
				else
				{
					if ( ! is_url($url) && ! is_ip_url($url))
					{
						// invalid URL given
						$response = array('failed' => true,
									 	  'message' => $lang['suggest_msg3']
									 );
					}
					else
					{
						$sources = a_fetch_video_sources();
						$use_this_src = -1;
						foreach($sources as $src_id => $source)
						{
							if($use_this_src > -1)
							{
								break;
							}
							else
							{
								if(@preg_match($source['source_rule'], $url))
								{
									$use_this_src = $source['source_id'];
								}
							}
						}
						
						if ($use_this_src > -1)
						{
							if ( ! file_exists( "./admin/src/" . $sources[ $use_this_src ]['source_name'] . ".php"))
							{
								// reply as 'not a supported video source'
								$response = array('failed' => true, 
										 	  	  'message' => $lang['suggest_msg5']
										 	);
							}
							else
							{
								define('PHPMELODY', true);
								require_once( "./admin/src/" . $sources[ $use_this_src ]['source_name'] . ".php");
								do_main($video_details, $url);
								
								//	Lookup this URL in the database, check for existence to avoid duplication.
								$sql = "SELECT COUNT(*) as total_results 
										  FROM pm_videos_urls 
										 WHERE direct = '". $video_details['direct'] ."'";
								$result = mysql_query($sql);
								$row = mysql_fetch_assoc($result);
								mysql_free_result($result);
								
								if ($row['total_results'] > 0)
								{
									$response = array('failed' => true, 
										 	  		  'message' => $lang['suggest_msg1']
										 		);
									break;
								}
								unset($sql, $result, $row);
								
								$sql = "SELECT COUNT(*) as total_results 
										  FROM pm_temp 
										 WHERE url = '". secure_sql($url) ."'";
								$result = mysql_query($sql);
								$row = mysql_fetch_assoc($result);
								mysql_free_result($result);
								
								if ($row['total_results'] > 0)
								{
									$response = array('failed' => true, 
										 	  		  'message' => $lang['suggest_msg2']
										 		);
									break;
								}
								
								$video_details['source_id'] = $use_this_src;
								
								$response = array('success' => true,
												  'videodata' => $video_details
											);
							}
						}
						else
						{
							// not a supported video source
							$response = array('failed' => true, 
									 	  	  'message' => $lang['suggest_msg5']
									 	);
						}
					}
				}
					
			break;
			
			case 'submitvideo':
				
				$required_fields = array('yt_id' => 'URL',
										 'category' => $lang['category'],
										 'video_title' => $lang['video'], 
								   );
				foreach( $_POST as $key => $value) 
				{
					$value = trim($value);
					if (array_key_exists(strtolower($key), $required_fields) && empty($value))
						$errors[$key] = $required_fields[$key] .' '. $lang['register_err_msg8'];
				}
				
				if ($_POST['category'] == '-1') 
				{
					$errors['category'] = $lang['choose_category'];
				}
				
				$url = trim($_POST['yt_id']);
				$url = str_replace('https', 'http', $url);
				$url = str_replace('youtu.be/', 'youtube.com/watch?v=', $url);
				
				$sources = a_fetch_video_sources();
				$use_this_src = $source_id = (int) $_POST['source_id'];
				
				if ( ! $source_id || ! array_key_exists($source_id, $sources))
				{
					$use_this_src = -1;

					foreach($sources as $src_id => $source)
					{
						if($source['source_name'] != 'localhost' && $source['source_name'] != 'other')
						{
							if(@preg_match($source['source_rule'], $url))
							{
								$use_this_src = $source['source_id'];
								break;
							}
						}
					}
					
					if ($url != '' && $use_this_src == -1)
					{
						$errors['yt_id'] = $lang['suggest_msg5'];
					}
				}
				
				if ($use_this_src > -1)
				{
					if ( ! file_exists('./admin/src/' . $sources[ $use_this_src ]['source_name'] . '.php'))
					{
						$response = array('failed' => true, 
							 	  	  	  'message' => $lang['suggest_msg5']
							 			 );
						exit(json_encode($response));
					}
					else
					{
						define('PHPMELODY', true);
						require_once('./admin/src/' . $sources[ $use_this_src ]['source_name'] . '.php');
						@do_main($video_details, $url);
						
						$video_details['source_id'] = $use_this_src;
					}
				}
				
				if (count($errors) == 0)
				{
					$url = secure_sql($url);
					//	Lookup this URL in the database, check for existence to avoid duplication.
					$sql = "SELECT COUNT(*) as total_results 
							  FROM pm_videos_urls 
							 WHERE direct = '". $url ."'";
					$result = mysql_query($sql);
					$row = mysql_fetch_assoc($result);
					mysql_free_result($result);
					
					if ($row['total_results'] > 0)
					{
						$response = array('failed' => true, 
							 	  		  'message' => $lang['suggest_msg1']
							 		);
						break;
					}
					unset($sql, $result, $row);
					
					$sql = "SELECT COUNT(*) as total_results 
							  FROM pm_temp 
							 WHERE url = '". secure_sql($url) ."'";
					$result = mysql_query($sql);
					$row = mysql_fetch_assoc($result);
					mysql_free_result($result);
					
					if ($row['total_results'] > 0)
					{
						$response = array('failed' => true, 
							 	  		  'message' => $lang['suggest_msg2']
							 		);
						break;
					}
					
					$description = trim($_POST['description']);
					$description = nl2br($description);
					$description = stripslashes($description);
					$description = str_replace(array("\r", "\n"), '', $description);
					$description = removeEvilTags($description);
					$description = secure_sql($description);
					
					if(_STOPBADCOMMENTS == '1') 
					{
						$description = search_bad_words($description);
					}
					$description = word_wrap_pass($description);
					
					$video_title = 		secure_sql($_POST['video_title']);
					$video_title = 		str_replace( array("<", ">"), '', $video_title);
					$submitted = secure_sql($userdata['username']);
					$category = secure_sql($_POST['category']);
					
					$yt_id = specialchars($yt_id, 0);
					
					$user_id = $userdata['id'];
					$tags = removeEvilTags($_POST['tags']);
					$tags = secure_sql($tags);
					
					$duration = 0;
					if ( ! empty($video_details['yt_length']))
					{
						$duration = (int) $video_details['yt_length'];
					}
					
					if ($config['auto-approve_suggested_videos'] == '1') 
					{
						// insert new video procedure

						// overwrite some data with user input
						$video_details['video_title'] = $video_title;
						$video_details['description'] = $description;
						$video_details['category'] = $category;
						$video_details['yt_length'] = $duration;
						$video_details['tags'] = $tags;
						$video_details['language'] = 1;
						$video_details['age_verification'] = 0;
						$video_details['submitted'] = $submitted;
						$video_details['added'] = time();
						$video_details['source_id'] = $use_this_src;
						$video_details['featured'] = 0;
						$video_details['restricted'] = 0;
						$video_details['allow_comments'] = 1;
						$video_details['direct'] = (empty($video_details['direct'])) ? $url : $video_details['direct'];
						$video_details['url_flv'] = ($video_details['url_flv'] == '') ? $url : $video_details['url_flv']; 
						
						//	generate unique id;
						$found = 0;
						$uniq_id = '';
						$i = 0;
						do
						{
							$found = 0;
							if(function_exists('microtime'))
								$str = microtime();
							else
								$str = time();
							$str = md5($str);
							$uniq_id = substr($str, 0, 9);
							if(count_entries('pm_videos', 'uniq_id', $uniq_id) > 0)
								$found = 1;
						} while($found === 1);
		
						$video_details['uniq_id'] = $uniq_id;
						
						// insert to database
						$new_video = insert_new_video($video_details, $new_video_id);
						
						if ($new_video !== true)
						{
							$response = array('failed' => true, 
							 	  	  		  'message' => $lang['suggest_msg6']
							 				 );
							exit(json_encode($response));
						}
						else
						{
							// download thumbnail
							if ('' != $video_details['yt_thumb'])
							{
								$img = download_thumb($video_details['yt_thumb'], _THUMBS_DIR_PATH, $uniq_id);
							}
							else
							{ 
								$img = true;
							}
							
							// do tags
							if ($video_details['tags'] != '')
							{
								$tags = explode(",", $video_details['tags']);
								foreach($tags as $k => $tag)
								{
									$tags[$k] = stripslashes(trim($tag));
								}
								//	remove duplicates and 'empty' tags
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
								//	insert tags
								if(count($tags) > 0)
									insert_tags($uniq_id, $tags);
							}
						}
						$response = array('success' => true, 
								 	  	  'message' => $lang['suggest_msg7']  
								 	);
					}
					else
					{
						$sql = "INSERT INTO pm_temp (url, video_title, description, yt_length, tags, category, username, user_id, added, source_id, language, thumbnail, yt_id, url_flv, mp4)  
								 VALUES ('". $url ."', 
								 		 '". $video_title ."', 
										 '". $description ."',
										 '". $duration ."', 
										 '". $tags ."', 
										 '". $category ."', 
										 '". $submitted ."', 
										 '". $user_id ."', 
										 '". time() ."', 
										 '". $use_this_src ."', 
										 '1', 
										 '". $video_details['yt_thumb'] ."',
										 '". $video_details['yt_id'] ."', 
										 '". $video_details['url_flv'] ."', 
										 '". $video_details['mp4'] ."')";

						$query = @mysql_query($sql);
						
						if ( ! $query)
						{
							$response = array('failed' => true, 
								  	  		  'message' => $lang['suggest_msg6']
							 				  );
						}
						else
						{
							$response = array('success' => true, 
									 	  	  'message' => $lang['suggest_msg4']
									 	);
						}
					}
					break;
				}
				else
				{
					$error_msg = '<ul>';
					foreach ($errors as $k => $msg)
					{
						$error_msg .= '<li>'. $msg .'</li>';
					}
					$error_msg .= '</ul>';
					// not a supported video source
					$response = array('failed' => true, 
							 	  	  'message' => $error_msg
							 	);
				}

			break;
		}
		
		echo json_encode($response);
		exit();
	break;
	
	case 'profile':

		switch ($action)
		{
			case 'follow':
			case 'unfollow':
			case 'getfollowers':
			case 'getfollowing':
			case 'activity-stream':
			case 'update-status':
			case 'load-notifications':
				
				if ( ! _MOD_SOCIAL)
				{
					exit('Activate social module first.');
				}
				
			break;
		}
		$profile_user_id = ($_GET['uid'] != '')  ? (int) $_GET['uid'] : (int) $_POST['uid'];
		if ( ! $profile_user_id && $action != 'load-notifications')
		{
			exit('Invalid user ID provided.');
		}
		
		switch ($action)
		{
			case 'load-notifications':

				$page = ($_GET['page'] != 0) ? (int) $_GET['page'] : 1;
				$from = $page * NOTIFICATIONS_PER_PAGE - (NOTIFICATIONS_PER_PAGE); 
				
				$notification_list = get_latest_notifications($from, NOTIFICATIONS_PER_PAGE);
				
				// mark as read
				mark_notification_read(NOTIFICATIONS_PER_PAGE);
				if ($notification_list != false)
				{
					$smarty->assign('total_notifications', count($notification_list));
					$smarty->assign('notification_list', $notification_list);
				}
				else
				{
					$smarty->assign('total_notifications', 0);
					$smarty->assign('notification_list', array());
				}
				$html = $smarty->fetch('notification-list.tpl');
				
				exit($html);
				
			break;
			
			case 'follow': // return JSON
				
				$response = array('success' => false,
								  'msg' => '', 
								  'html' => '');
				
				if ( ! is_user_logged_in() || $profile_user_id == 0)
				{
					$response['msg'] = $lang['registration_req'];
					exit(json_encode($response));
				}
				
				// check if this user has reached the user_following_limit
				if ($userdata['following_count'] >= $config['user_following_limit'])
				{
					$response['msg'] = $lang['follow_error_max_limit'];
					exit(json_encode($response));
				}
				
				// check if user-to-follow exists
				$sql = "SELECT COUNT(*) as total 
						FROM pm_users 
						WHERE id = '". $profile_user_id ."'";
				$result = mysql_query($sql);
				$row = mysql_fetch_assoc($result);
				mysql_free_result($result);
				
				if ($row['total'] == 0)
				{
					$response['msg'] = $lang['login_msg12'];
					exit(json_encode($response));
				}
				
				$follow = follow($profile_user_id);

				$response['success'] = true;
				
				$smarty->assign('profile_user_id', $profile_user_id);
				$smarty->assign('profile_data', array('am_following' => true));
				$response['html'] = $smarty->fetch('user-follow-button.tpl');

				exit(json_encode($response));
			break;
			
			case 'unfollow': // return JSON
			
				$response = array('success' => false,
								  'msg' => '', 
								  'html' => '');
				
				if ( ! is_user_logged_in() || $profile_user_id == 0)
				{
					$response['msg'] = $lang['registration_req'];
					exit(json_encode($response));
				}
				
				// check if relationship exists
				if (is_follow_relationship($profile_user_id, $userdata['id']))
				{
					unfollow($profile_user_id);
				}
				$response['success'] = true;

				$smarty->assign('profile_user_id', $profile_user_id);
				$smarty->assign('profile_data', array('am_following' => false));
				$response['html'] = $smarty->fetch('user-follow-button.tpl');
				
				exit(json_encode($response));
			break;
			
			
			case 'getfollowers': // return HTML
				
				$page = ($_GET['page'] != 0) ? (int) $_GET['page'] : 1;
				$profiles_per_page = FOLLOW_PROFILES_PER_PAGE;
				$from = $page * $profiles_per_page - ($profiles_per_page); 
				
				$followers_count = 0;
				if ($profile_user_id == $userdata['id'])
				{
					$followers_count = $userdata['followers_count'];
				}
				else
				{
					$sql = "SELECT followers_count 
							FROM pm_users 
							WHERE id = '". $profile_user_id ."'";
					$result = mysql_query($sql);
					$row = mysql_fetch_assoc($result);
					mysql_free_result($result);
					$followers_count = (int) $row['followers_count'];
				}

				if ($followers_count)
				{
					load_countries_list();
					
					$total_pages = ceil($followers_count / $profiles_per_page);
					
					// get list
					$list = get_followers_list($profile_user_id, $from, $profiles_per_page);
					
					if (count($list) > 0)
					{
						$my_following_list = $my_followers_list = array();
						$user_ids = array();
						
						foreach ($list as $uid => $u)
						{
							$user_ids[] = $uid;
						}
						
						check_multiple_relationships($user_ids, $my_followers_list, $my_following_list);

						foreach ($list as $user_id => $u)
						{
							if ($user_id != $userdata['id'])
							{
								$list[$user_id]['is_following_me'] = (in_array($user_id, $my_followers_list)) ? true : false;
								$list[$user_id]['am_following'] = (in_array($user_id, $my_following_list)) ? true : false;
							}
						}
						
						$smarty->assign('follow_count', $followers_count);
						$smarty->assign('profile_list', $list);
						$smarty->assign('total_profiles', count($list));
						$html = $smarty->fetch('user-follow-list.tpl');
						exit($html);
					}
				}
				
				$smarty->assign('follow_count', 0);
				$smarty->assign('profile_list', array());
				$smarty->assign('total_profiles', 0);
				$html = $smarty->fetch('user-follow-list.tpl');
				exit($html);
				
			break;
			
			case 'getfollowing': // return HTML
				
				$page = ($_GET['page'] != 0) ? (int) $_GET['page'] : 1;
				$profiles_per_page = FOLLOW_PROFILES_PER_PAGE;
				$from = $page * $profiles_per_page - ($profiles_per_page); 
				
				$following_count = 0;
				if ($profile_user_id == $userdata['id'])
				{
					$following_count = $userdata['following_count'];
				}
				else
				{
					$sql = "SELECT following_count 
							FROM pm_users 
							WHERE id = '". $profile_user_id ."'";
					$result = mysql_query($sql);
					$row = mysql_fetch_assoc($result);
					mysql_free_result($result);
					$following_count = (int) $row['following_count'];
				}
				
				if ($following_count > 0)
				{
					load_countries_list();
					
					$total_pages = ceil($following_count / $profiles_per_page);
					
					// get list
					$list = get_following_list($profile_user_id, $from, $profiles_per_page);
					
					if (count($list) > 0)
					{
						$my_following_list = $my_followers_list = array();
						$user_ids = array();
						
						foreach ($list as $uid => $u)
						{
							$user_ids[] = $uid;
						}
						
						check_multiple_relationships($user_ids, $my_followers_list, $my_following_list);
						
						foreach ($list as $user_id => $u)
						{
							if ($user_id != $userdata['id'])
							{
								$list[$user_id]['is_following_me'] = (in_array($user_id, $my_followers_list)) ? true : false;
								$list[$user_id]['am_following'] = (in_array($user_id, $my_following_list)) ? true : false;
							}
						}
						
						$smarty->assign('follow_count', $following_count);
						$smarty->assign('profile_list', $list);
						$smarty->assign('total_profiles', count($list));
						$html = $smarty->fetch('user-follow-list.tpl');
						exit($html);
					}
				}
				
				$smarty->assign('follow_count', 0);
				$smarty->assign('profile_list', array());
				$smarty->assign('total_profiles', 0);
				$html = $smarty->fetch('user-follow-list.tpl');
				exit($html);
				
			break;
			
			case 'activity-stream':
				
				if ( ! is_user_logged_in())
				{
					exit($lang['registration_req']);
				}
				if ($userdata['id'] != $profile_user_id)
				{
					exit();
				}
				
				$page = ($_GET['page'] != 0) ? (int) $_GET['page'] : 1;
				$from = $page * ACTIVITIES_PER_PAGE - (ACTIVITIES_PER_PAGE); 
				
				$actor_bucket = array();
				$object_bucket = array();
				$target_bucket = array();
				$activity_meta_bucket = array();
				$activity_stream = get_following_activity_stream($from, ACTIVITIES_PER_PAGE);
				
				activity_stream_rollup($activity_stream, $actor_bucket, $object_bucket, $target_bucket, $activity_meta_bucket);
				
				$smarty->assign('total_activities', count($activity_stream));
				unset($activity_stream);
			
				$smarty->assign('actor_bucket', $actor_bucket);
				$smarty->assign('object_bucket', $object_bucket);
				$smarty->assign('target_bucket', $target_bucket);
				$smarty->assign('activity_meta_bucket', $activity_meta_bucket);
				
				$activity_stream_html = $smarty->fetch('activity-stream.tpl');
				exit($activity_stream_html);
				
			break;
			
			case 'user-activity':
				
				if ( ! is_user_logged_in())
				{
					exit($lang['registration_req']);
				}

				if ($profile_user_id != $userdata['id'] && ! is_follow_relationship($profile_user_id, $userdata['id']))
				{
					exit();
				}
				
				$page = ($_GET['page'] != 0) ? (int) $_GET['page'] : 1;
				$from = $page * ACTIVITIES_PER_PAGE - (ACTIVITIES_PER_PAGE);
				
				$actor_bucket = array();
				$object_bucket = array();
				$target_bucket = array();
				$activity_meta_bucket = array();

				$activity_stream = get_user_activity($profile_user_id, $from, ACTIVITIES_PER_PAGE);
				activity_stream_rollup($activity_stream, $actor_bucket, $object_bucket, $target_bucket, $activity_meta_bucket);
				
				$smarty->assign('total_activities', count($activity_stream));
				unset($activity_stream);
				
				$smarty->assign('actor_bucket', $actor_bucket);
				$smarty->assign('object_bucket', $object_bucket);
				$smarty->assign('target_bucket', $target_bucket);
				$smarty->assign('activity_meta_bucket', $activity_meta_bucket);
 
				$user_activity_html = $smarty->fetch('user-activity.tpl');
				
				exit($user_activity_html);
				
			break;
			
			case 'user-activity-hide':
				
				$activity_id = (int) $_GET['activity_id'];

				if ( ! is_user_logged_in() || ! $activity_id)
				{
					exit($lang['registration_req']);
				}
				
				$activity_data = get_activity_data($activity_id);
				
				if ( ! $activity_data || $activity_data['user_id'] != $userdata['id'])
				{
					exit();
				}
				
				hide_activity($activity_id);
				exit();
				
			break;
			
			case 'update-status':

				if ( ! is_user_logged_in() || $profile_user_id == 0)
				{
					exit(json_encode(array('success' => false, 'msg' => $lang['registration_req'], 'html' => '')));
				}
				
				$status = trim($_POST['txt']);
				$status = stripslashes($status);
				$status = str_replace("\n", '<br />', $status);//nl2br($status);
				$status = removeEvilTags($status);
				//$status = secure_sql($status);
				
				if(_STOPBADCOMMENTS == '1') 
				{
					$status = search_bad_words($status);
				}
				$status = word_wrap_pass($status);
				
				if ( ! strlen($status))
				{
					exit(json_encode(array('success' => false, 'msg' => $lang['user_status_error_empty'], 'html' => '')));
				}
				
				

				$activity_id = log_activity(array(
												'user_id' => $userdata['id'],
												'activity_type' => ACT_TYPE_STATUS,
												'metadata' => array('statustext' => $status)
												)
											);
				if ( ! $activity_id)
				{
					exit(json_encode(array('success' => false, 'msg' => $lang['comment_msg4'], 'html' => ''))); 
				}
				
				$actor_bucket = array();
				$object_bucket = array();
				$target_bucket = array();
				$activity_meta_bucket = array();

				activity_stream_rollup(get_user_activity($userdata['id'], 0, 1), $actor_bucket, $object_bucket, $target_bucket, $activity_meta_bucket);
				$smarty->assign('total_activities', 1);
				
				$smarty->assign('actor_bucket', $actor_bucket);
				$smarty->assign('object_bucket', $object_bucket);
				$smarty->assign('target_bucket', $target_bucket);
				$smarty->assign('activity_meta_bucket', $activity_meta_bucket);
 
				$user_activity_html = $smarty->fetch('user-activity.tpl');
				
				exit(json_encode(array('success' => true, 'msg' => '', 'html' => $user_activity_html)));

			break;
		}
	break;
	
	case 'upload':
		
		switch($action) 
		{
			case 'useruploadvideo':
				
				$error_msg = '';
				$max_filesize_bytes = return_bytes($config['allow_user_uploadvideo_bytes']);
				
				// message for Admin/Content Manager, in case the Upload Process stops mid-point
				$tmp_description = 'You are seeing this because the user has not finished the upload process by submitting the Upload Form.';
				$tmp_description .= '<br />';
				$tmp_description .= 'Now, you can either Edit & Approve this item or Delete it.';
				
				$whitelist	   = array('flv', 'mov', 'avi', 'divx', 'mp4', 'wmv', 'mkv',
									   'asf', 'wma', 'mp3', 'm4v', 'm4a', '3gp', '3g2');

				$allowed_types = array( 'video/x-flv', 	'video/quicktime', 'video/x-msvideo', 
										'video/x-divx', 'video/mp4', 'video/x-ms-wmv', 
										'application/octet-stream',  'video/avi', 'video/x-matroska',
										'video/x-ms-asf', 'audio/x-ms-wma',	'audio/mp4', 'video/3gpp', 
										'video/3gpp2', 'audio/mpeg', 'video/mpeg', 'application/force-download', 
										'audio/mp3', 'audio/mpeg3', 'video/x-m4v', 'audio/x-m4a');
				
				$file = $_FILES['Filedata'];
				
				$nonce_name = substr(md5('_uploadform'.$_POST['form_id'].$userdata['id'].$_SERVER['REMOTE_ADDR']), 3, 8);
				
				if ( ! csrfguard_validate_token($nonce_name, $_POST['_pmnonce_t']))
				{
					$error_msg = $lang['upload_errmsg_badtoken'];
				}
				
				if ( ! is_user_logged_in()) 
				{
					$error_msg = $lang['upload_login_first'];
				}
				
				if ($error_msg == '')
				{
					$tmp_parts = explode('.', $file['name']);
					$ext = array_pop($tmp_parts);
					$ext = strtolower($ext);
					if (($file['size'] > 0 && $file['size'] <= $max_filesize_bytes) && strlen($file['name']) > 0 && $file['error'] == 0)
					{
						if (in_array($file['type'], $allowed_types) && in_array($ext, $whitelist))
						{					
							do
							{
								$new_name  = md5($file['name'].rand(1,888));
								$new_name  = substr($new_name, 2, 10);
								$new_name .= '.'.$ext;
							}
							while (file_exists(_VIDEOS_DIR_PATH . $new_name));
							
							$modframework->trigger_hook('upload_moveupload');
							
							if ($move = move_uploaded_file($file['tmp_name'], _VIDEOS_DIR_PATH . $new_name))
							{
								// INSERT INTO pm_temp, disregarding the current configuration
								// with special title and shit. 
								$sql = "INSERT INTO pm_temp
												(url, video_title, description, yt_length, tags, category, username, user_id, 
												 added, source_id, language, thumbnail, yt_id, url_flv, mp4)
										VALUES ('". $new_name ."', 'n/a', '". secure_sql($tmp_description) ."', 0, '', 0, '". $userdata['username'] ."', 
												'". $userdata['id'] ."', '". time() ."', 1, 1, '', '', '', '')";
								$result = @mysql_query($sql);
								
								if ( ! $result)
								{
									$error_msg = $lang['upload_errmsg1'];
								}
								else
								{
									$success_msg = '__success__'. mysql_insert_id();
								}
								
								$modframework->trigger_hook('upload_insertvideo_after');
							}
							else
							{
								$error_msg = $lang['upload_errmsg1'];
							}
						}
						else
						{
							$error_msg = $lang['upload_errmsg2'];
						}	
					}
					else
					{
						switch ($file['error'])
						{	
		
							case UPLOAD_ERR_INI_SIZE:
								$error_msg = $lang['upload_errmsg3'];
							break;
							
							case UPLOAD_ERR_FORM_SIZE:
								$error_msg = $lang['upload_errmsg4'];
							break;
							
							case UPLOAD_ERR_PARTIAL:
								$error_msg = $lang['upload_errmsg5'];
							break;
							
							case  UPLOAD_ERR_NO_FILE:
								$error_msg = $lang['upload_errmsg6'];
							break;
							
							case UPLOAD_ERR_NO_TMP_DIR:
								$error_msg = $lang['upload_errmsg7'];
							break;
							
							case 7: //UPLOAD_ERR_CANT_WRITE:
								$error_msg = $lang['upload_errmsg8'];
							break;
							
							case 8: //UPLOAD_ERR_EXTENSION:
								$error_msg = $lang['upload_errmsg9'];
							break;
							
							default:
							case UPLOAD_ERR_OK:
							break;
						}
					}
				}
				
				if ($success_msg) 
				{
					echo $success_msg;
				}
				
				if ($error_msg != '')
				{
					if ($file['tmp_name'] != '' && file_exists($file['tmp_name']))
					{
						@unlink($file['tmp_name']);
					}
					echo $error_msg;
				}

				exit();

			break;
		}
		
	break;
	
	case 'stats': // advertisments
	
		$ad_id = (int) $_GET['aid'];
		$ad_type = (int) $_GET['at'];
		
		switch ($action) 
		{
			case 'show':
				
				if ( ! pm_detect_crawler())
				{
					if ($ad_id && in_array($ad_type, array(_AD_TYPE_CLASSIC, _AD_TYPE_VIDEO, _AD_TYPE_PREROLL)))
					{
						$sql_table = '';
						switch ($ad_type)
						{
							case _AD_TYPE_CLASSIC:
								$sql_table = 'pm_ads';
							break;
							
							case _AD_TYPE_VIDEO:
								$sql_table = 'pm_videoads';
							break;
							
							case _AD_TYPE_PREROLL:
								$sql_table = 'pm_preroll_ads';
							break;
						}
						
						$sql = "SELECT COUNT(*) as total_found FROM $sql_table WHERE id = $ad_id";
						if ($result = @mysql_query($sql))
						{
							$row = mysql_fetch_assoc($result);
							mysql_free_result($result);
							
							if ($row['total_found'] > 0)
							{
								$sql = "INSERT INTO pm_ads_log (date, ad_id, ad_type, impressions)
										VALUES (CURDATE(), $ad_id, $ad_type, 1) 
										ON DUPLICATE KEY 
											UPDATE impressions = impressions + 1";
								@mysql_query($sql);
				
							}
						}
					}
				}

				header("Content-type: image/gif"); 
				header("Expires: Wed, 5 Feb 1986 06:06:06 GMT"); 
				header("Cache-Control: no-cache"); 
				header("Cache-Control: must-revalidate"); 
				printf('%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%', 71,73,70,56,57,97,1,0,1,0,128,255,0,192,192,192,0,0,0,33,249,4,1,0,0,0,0,44,0,0,0,0,1,0,1,0,0,2,2,68,1,0,59);
				
				exit();
			
			break;
			
			case 'skip':
				
				if ($ad_id && in_array($ad_type, array(_AD_TYPE_CLASSIC, _AD_TYPE_VIDEO, _AD_TYPE_PREROLL)))
				{
					$sql_table = '';
					switch ($ad_type)
					{
						case _AD_TYPE_CLASSIC:
							$sql_table = 'pm_ads';
						break;
						
						case _AD_TYPE_VIDEO:
							$sql_table = 'pm_videoads';
						break;
						
						case _AD_TYPE_PREROLL:
							$sql_table = 'pm_preroll_ads';
						break;	
					}
					
					$sql = "SELECT COUNT(*) as total_found FROM $sql_table WHERE id = $ad_id";
					if ($result = @mysql_query($sql))
					{
						$row = mysql_fetch_assoc($result);
						mysql_free_result($result);
						
						if ($row['total_found'] > 0)
						{
							$sql = "INSERT INTO pm_ads_log (date, ad_id, ad_type, skips)
									VALUES (CURDATE(), $ad_id, $ad_type, 1) 
									ON DUPLICATE KEY 
										UPDATE skips = skips + 1";
							@mysql_query($sql); 
						}
					}
				}

			break;
			
			case 'click': // EDITME @todo
			break;
		}

	break;
	
}	//	end switch ($page)

exit();
