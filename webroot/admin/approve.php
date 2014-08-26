<?php
include '../include/ffmpeg.php';
$showm	= '2';
/*
$load_uniform = 0;
$load_ibutton = 0;
$load_tinymce = 0;
$load_swfupload = 0;
$load_colorpicker = 0;
$load_prettypop = 0;
*/
$load_scrolltofixed = 1;
$load_chzn_drop = 1;
$load_prettypop = 1;
$_page_title = 'تصویب ویدئوهای در حالت انتظار';
include('header.php');

$action	 = $_GET['a'];
$id 	 = (int) $_GET['vid'];
$page	 = (int) $_GET['page'];

if ($_POST['Submit'] != '')
{
	
	if ($_POST['Submit'] == 'Delete checked')
	{
		$action = 'delvids';
	}
	if ($_POST['Submit'] == 'Approve checked')
	{
		$action = 'approveall';
	}
	
}

if($page == '' || !is_numeric($page))
   $page = 1;

$limit = (isset($_COOKIE['aa_videos_per_page'])) ? $_COOKIE['aa_videos_per_page'] : 30;

$from = $page * $limit - ($limit);
$errors = array();


switch($action)
{
	case 'deleted':
		$info_msg = '<div class="alert alert-success">داده های وارد شده حذف شد.</div>';
	break;
	
	case 'approve':
		if($id == '')	break;
		
		if ( ! csrfguard_check_referer('_admin_approve'))
		{
			$info_msg = '<div class="alert alert-error">رمز عبور اشتباه است یا سشن منقضی شده. لطفا صفحه را رفرش نمایید و از دوباره تلاش کنید.</div>';
			break;
		}
		
		define('PHPMELODY', true);
		
		require_once( './src/youtube.php'); // just for download_thumb()

		$video_details = array(	'uniq_id' => '',	
								'video_title' => '',	
								'description' => '',	
								'yt_id' => '',	
								'yt_length' => '',	
								'category' => '',	
								'submitted' => '',	
								'source_id' => '',	
								'language' => '',	
								'age_verification' => '',
								'url_flv' => '',	
								'yt_thumb' => '',
								'mp4' => '',	
								'direct' => '',	
								'tags' => '',
								'restricted' => 0,
								'allow_comments' => 1 
								);		
		$sources = a_fetch_video_sources();

		$temp	= array();
		$query = mysql_query("SELECT * FROM pm_temp WHERE id = '".$id."'");
		$input = mysql_fetch_assoc($query);
		mysql_free_result($query);

		$video_details['video_title']	=	$input['video_title'];
		$video_details['description']	=	$input['description'];
		$video_details['submitted']		=	$input['username'];
		$video_details['direct']		=	$input['url'];
		$video_details['category']		=	$input['category'];
		$video_details['submitted']		=	$input['username'];
		$video_details['source_id']		=	$input['source_id'];
		$video_details['language']		=	$input['language'];
		$video_details['tags']			=	$input['tags'];
		$video_details['yt_length'] 	= 	$input['yt_length'];
		$video_details['url_flv'] 		= 	$input['url_flv'];
		$video_details['mp4'] 			= 	$input['mp4'];
		$video_details['yt_thumb'] 		= 	$input['thumbnail'];
		$video_details['yt_id'] 		= 	$input['yt_id'];
		$video_details['language'] 		= 	1;
		$video_details['age_verification'] = 0;
		$video_details['added'] 		= 	time();
		$video_details['featured'] 		= 	0;
		$video_details['restricted'] 	= 	0;
		
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
		
		//	fetch information about this video
		if ($input['source_id'] != $sources['localhost']['source_id'])
		{
			switch ($sources[ $video_details['source_id'] ]['source_name'])
			{
				case 'divx':
				case 'windows media player':
				case 'quicktime':
				case 'mp3':
					$video_details['source_id'] = $sources['other']['source_id'];
				break;
			}
			
			if ($video_details['yt_id'] == '')
			{
				$video_details['yt_id'] = substr( md5( time() ), 2, 9);
			}
			if ($video_details['url_flv'] == '')
			{
				$video_details['url_flv'] = $input['url'];
			}
		}
		else // user uploaded video
		{
			$video_details['url_flv'] = $input['url'];
			$video_details['direct'] = $input['url'];
			$video_details['yt_length'] = $input['yt_length'];
			
			if ($input['thumbnail'] != '')
			{
				$tmp_parts = explode('.', $input['thumbnail']);
				$ext = array_pop($tmp_parts);
				$ext = strtolower($ext);
				if (rename(_THUMBS_DIR_PATH . $input['thumbnail'], _THUMBS_DIR_PATH . $uniq_id . '-1.'. $ext))
				{
					$input['thumbnail'] =  $uniq_id . '-1.'. $ext;
				}
				
				$video_details['yt_thumb'] = _THUMBS_DIR . $input['thumbnail'];	
			}
			else
			{
				$VIDEO_IMAGE = new ffmpeg();
				$VIDEO_IMAGE->ScreenShot($video_details['url_flv']);
				$FILENAME_EXE = substr(strrchr($video_details['url_flv'], "."), 1);
	            $FILENAME_STR = 'MP4_'.str_replace('.'.$FILENAME_EXE,'',$video_details['url_flv']).'.mp4';
				$video_details['yt_thumb'] = base64_encode($FILENAME_STR).'_thumb.jpg';
			}
		}

		$video_details['uniq_id'] = $uniq_id;

		foreach($video_details as $k => $v)
		{
			$video_details[$k] = str_replace("&amp;", "&", $v);
		}
		
		//	Ok, let's add this video to our database
		$new_video = insert_new_video($video_details, $new_video_id);
		
		if($new_video !== true)
		{
			$info_msg = '<div class="alert alert-error"><em>اووه، متاسفانه ویدئو جدید به دیتابیس تان اضافه نشد.</em><br /><strong>گزارش mysql :</strong> ".$new_video[0]."<br /><strong>Error Number:</strong> '.$new_video[1].'</div>';				
		}
		else
		{
			//	download thumbnail
			if ('' != $video_details['yt_thumb'] && $video_details['source_id'] != $sources['localhost']['source_id'])
			{
				if (function_exists('download_thumb'))
				{
					$img = download_thumb($video_details['yt_thumb'], _THUMBS_DIR_PATH, $uniq_id);
					
					if ( ! $img)
					{
						$info_msg = '<div class="alert alert-error">تامبنیل دانلود نشد برای ویدئو<em>'. $video_details['video_title']. '</em> از <code>'. $video_details['yt_thumb'].'</code></div>';
					}
				}
			}
			
			if($video_details['tags'] != '')
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
			
			if (_MOD_SOCIAL)
			{
				$act_type = ($video_details['source_id'] == $sources['localhost']['source_id']) ? ACT_TYPE_UPLOAD_VIDEO : ACT_TYPE_SUGGEST_VIDEO; 
				
				log_activity(array(
							'user_id' => username_to_id($video_details['submitted']),
							'activity_type' => $act_type,
							'object_id' => $new_video_id,
							'object_type' => ACT_OBJ_VIDEO,
							'object_data' => $video_details
							)
					);
					

			}
			
			$info_msg = '<div class="alert alert-success">ویدئو تایید شده<br />آیا دوست داری <strong><a href="modify.php?vid='.$uniq_id.'">الان ویرایش بکنید؟</a></strong></div>'; 
		}

		//	remove the suggested video from 'pm_temp'
		@mysql_query("DELETE FROM pm_temp WHERE id = '".$id."'");
		$FILENAME_EXE = substr(strrchr($video_details['url_flv'], "."), 1);
	    $NN = 'MP4_'.str_replace('.'.$FILENAME_EXE,'',$video_details['url_flv']).'.mp4';
		@mysql_query("UPDATE pm_videos SET url_flv = '".$NN."' WHERE url_flv = '".$video_details['url_flv']."'");
		$Convert = new ffmpeg();
	    $Convert->Convert($video_details['url_flv']);
	    unlink('../uploads/videos/'.$video_details['url_flv']);
	break;
	
	case 'approveall':

		if ( ! csrfguard_check_referer('_admin_approve'))
		{
			$info_msg = '<div class="alert alert-error">رمز عبور اشتباه است یا سشن منقضی شده. لطفا صفحه را رفرش نمایید و از دوباره تلاش کنید.</div>';
			break;
		}
	
		$video_ids = array();
		$video_ids = $_POST['video_ids'];
		$total_added = 0;
		
		$total_ids = count($video_ids);
		if ($total_ids > 0)
		{
			define('PHPMELODY', true);
			require_once( './src/youtube.php'); // just for download_thumb()

			$suggestions = array();
			$sql = "SELECT * FROM pm_temp WHERE id IN (". implode(',', $video_ids) .")";
			$result = mysql_query($sql);
				
			while ($row = mysql_fetch_assoc($result))
			{
				$suggestions[] = $row;
			}
			mysql_free_result($result);
			
			$sources = a_fetch_video_sources();
			$video_details = array();
			
			foreach ($suggestions as $k => $data)
			{
				$video_details['video_title']	=	$data['video_title'];
				$video_details['description']	=	$data['description'];
				$video_details['submitted']		=	$data['username'];
				$video_details['direct']		=	$data['url'];
				$video_details['category']		=	$data['category'];
				$video_details['submitted']		=	$data['username'];
				$video_details['source_id']		=	$data['source_id'];
				$video_details['language']		=	$data['language'];
				$video_details['tags']			=	$data['tags'];
				$video_details['yt_length'] 	= 	$data['yt_length'];
				$video_details['url_flv'] 		= 	$data['url_flv'];
				$video_details['mp4'] 			= 	$data['mp4'];
				$video_details['yt_thumb'] 		= 	$data['thumbnail'];
				$video_details['yt_id'] 		= 	$data['yt_id'];
				$video_details['language'] 		= 	1;
				$video_details['age_verification'] = 0;
				$video_details['added'] 		= 	time();
				$video_details['featured'] 		= 	0;
				$video_details['restricted'] 	= 	0;
				
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
				
				if ($data['source_id'] != $sources['localhost']['source_id'])
				{
					switch ($sources[ $video_details['source_id'] ]['source_name'])
					{
						case 'divx':
						case 'windows media player':
						case 'quicktime':
						case 'mp3':
							$video_details['source_id'] = $sources['other']['source_id'];
						break;
					}
					
					if ($video_details['yt_id'] == '')
					{
						$video_details['yt_id'] = substr( md5( time() ), 2, 9);
					}
					if ($video_details['url_flv'] == '')
					{
						$video_details['url_flv'] = $data['url'];
					}
				}
				else // user uploaded video
				{
					$video_details['url_flv'] = $data['url'];
					$video_details['direct'] = $data['url'];
					$video_details['yt_length'] = $data['yt_length'];
					
					if ($data['thumbnail'] != '')
					{
						$tmp_parts = explode('.', $data['thumbnail']);
						$ext = array_pop($tmp_parts);
						$ext = strtolower($ext);

						if (rename(_THUMBS_DIR_PATH . $data['thumbnail'], _THUMBS_DIR_PATH . $uniq_id . '-1.'. $ext))
						{
							$data['thumbnail'] =  $uniq_id . '-1.'. $ext;
						}
						
						$video_details['yt_thumb'] = _THUMBS_DIR . $data['thumbnail'];	
					}
					else
					{
						$VIDEO_IMAGE = new ffmpeg();
						$VIDEO_IMAGE->ScreenShot($video_details['url_flv']);
						$FILENAME_EXE = substr(strrchr($video_details['url_flv'], "."), 1);
	          		    $FILENAME_STR = 'MP4_'.str_replace('.'.$FILENAME_EXE,'',$video_details['url_flv']).'.mp4';
						$video_details['yt_thumb'] = base64_encode($FILENAME_STR).'_thumb.jpg';
					}
				    }
				
				$video_details['uniq_id'] = $uniq_id;
				
				foreach($video_details as $k => $v)
				{
					$video_details[$k] = str_replace("&amp;", "&", $v);
				}
				
				//	Ok, let's add this video to our database
				$new_video = insert_new_video($video_details, $new_video_id);
				
				if ($new_video !== true)
				{
					$errors[] = 'ویدئو<em>'. $video_details['video_title'] .'</em> به دیتابیس تان اضافه نشد. <br /><strong>گزارش mysql :</strong> '. $new_video[0] .'<br /><strong>شماره خطا : </strong> '. $new_video[1] .'</div>';
				}
				else
				{
					$total_added++;
					
					//	download thumbnail
					if ('' != $video_details['yt_thumb'] && $video_details['source_id'] != $sources['localhost']['source_id'])
					{
						if (function_exists('download_thumb'))
						{
							$img = download_thumb($video_details['yt_thumb'], _THUMBS_DIR_PATH, $uniq_id);
							
							if ( ! $img)
							{
								$errors[] = 'تامبنیل دانلود نشد برای ویدئو <em>'. $video_details['video_title']. '</em> از <code>'. $video_details['yt_thumb'].'</code>';
							}
						}
					}
					
					if($video_details['tags'] != '')
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
					
					if (_MOD_SOCIAL)
					{
						$act_type = ($video_details['source_id'] == $sources['localhost']['source_id']) ? ACT_TYPE_UPLOAD_VIDEO : ACT_TYPE_SUGGEST_VIDEO; 
						
						log_activity(array(
									'user_id' => username_to_id($video_details['submitted']),
									'activity_type' => $act_type,
									'object_id' => $new_video_id,
									'object_type' => ACT_OBJ_VIDEO,
									'object_data' => $video_details
									)
							);
					}
				}
		
				//	remove the suggested video from 'pm_temp'
				if ($new_video == true)
				{
					@mysql_query("DELETE FROM pm_temp WHERE id = '". $data['id'] ."'");
					$FILENAME_EXE = substr(strrchr($video_details['url_flv'], "."), 1);
	                $NN = 'MP4_'.str_replace('.'.$FILENAME_EXE,'',$video_details['url_flv']).'.mp4';
	            	@mysql_query("UPDATE pm_videos SET url_flv = '".$NN."' WHERE url_flv = '".$video_details['url_flv']."'");
		            $Convert = new ffmpeg();
	                $Convert->Convert($video_details['url_flv']);
	                unlink('../uploads/videos/'.$video_details['url_flv']);
				}
			} // end foreach();

			if ($total_added > 0)
			{
				if ($total_added == $total_ids)
				{
					$info_msg = '<div class="alert alert-success">ویدئوهای انتخابی با موفقیت تایید شدند.</div>';
				}
				else
				{
					$info_msg = '<div class="alert alert-success">اضافه شده <strong>'. $total_added .'</strong> خارح از<strong>'. $total_ids .'</strong> ویدئوهای انتخاب شده.</div>';
				}
			}
			
		}
		else
		{
			$info_msg = '<div class="alert alert-error">هیچ چیزی انتخاب نشده.</div>';
		}

		
	break;

	case 'delall':
		
		if ( ! csrfguard_check_referer('_admin_approve'))
		{
			$info_msg = '<div class="alert alert-error">رمز عبور اشتباه است یا سشن منقضی شده. لطفا صفحه را رفرش نمایید و از دوباره تلاش کنید.</div>';
			break;
		}
			
		$files = array();
		$sql = "SELECT url FROM pm_temp WHERE source_id = '1'";
		$result = mysql_query($sql);
		
		if (mysql_num_rows($result) > 0)
		{
			while ($row = mysql_fetch_assoc($result))
			{
				$files[] = $row['url'];
			}
			mysql_free_result($result);
		}
		
		if (count($files) > 0)
		{
			foreach ($files as $k => $filename)
			{
				if (file_exists(_VIDEOS_DIR_PATH . $filename) && strlen($filename) > 0)
				{
					unlink(_VIDEOS_DIR_PATH . $filename);
				}
			}
		}
		
		mysql_query("TRUNCATE TABLE pm_temp");
		$info_msg = '<div class="alert alert-success">همه ویدئوهای در حالت انتظار حذف شده اند.</div>';
		
	break;
	
	case 'delvid':
		
		if ( ! csrfguard_check_referer('_admin_approve'))
		{
			$info_msg = '<div class="alert alert-error">رمز عبور اشتباه است یا سشن منقضی شده. لطفا صفحه را رفرش نمایید و از دوباره تلاش کنید.</div>';
			break;
		}
		
		$sql = "SELECT url, source_id, thumbnail FROM pm_temp WHERE id = '". $id ."'";
		$result = mysql_query($sql);
		$video = mysql_fetch_assoc($result);
		mysql_free_result($result);
		
		if ($video['source_id'] == 1)
		{
			if (file_exists(_VIDEOS_DIR_PATH . $video['url']) && strlen($video['url']) > 0)
			{
				unlink(_VIDEOS_DIR_PATH . $video['url']);
			}
			if ($video['thumbnail'] != '')
			{
				unlink(_THUMBS_DIR_PATH . $video['thumbnail']);
			}
		}

		@mysql_query("DELETE FROM pm_temp WHERE id = '".$id."'");
	
		echo '<meta http-equiv="refresh" content="0;URL=approve.php?a=deleted&page='. $page .'" />';
		exit();
	break;
	
	case 'delvids':
		
		if ( ! csrfguard_check_referer('_admin_approve'))
		{
			$info_msg = '<div class="alert alert-error">رمز عبور اشتباه است یا سشن منقضی شده. لطفا صفحه را رفرش نمایید و از دوباره تلاش کنید.</div>';
			break;
		}
		
		if($_POST['Submit'] == 'Delete checked')
		{
			$video_ids = array();
			$video_ids = $_POST['video_ids'];
			
			$total_ids = count($video_ids);
			if($total_ids > 0)
			{
				$in_arr = '';
				for($i = 0; $i < $total_ids; $i++)
				{
					$in_arr .= "'" . $video_ids[ $i ] . "', ";
				}
				$in_arr = substr($in_arr, 0, -2);
				if(strlen($in_arr) > 0)
				{
					$videos = array();
					$sql = "SELECT url, source_id, thumbnail FROM pm_temp WHERE id IN (". $in_arr .") AND source_id = '1'";
					$result = mysql_query($sql);
					
					while ($row = mysql_fetch_assoc($result))
					{
						$videos[] = $row;
					}
					mysql_free_result($result);
					
					$sql = "DELETE FROM pm_temp WHERE id IN (" . $in_arr . ")";
					$result = @mysql_query($sql);
					if(!$result)
						$info_msg = '<div class="alert alert-error">در هنگام بروزرسانی دیتابیس تان خطایی رخ داد. <br />mysql بر می گرداند :'.mysql_error().'</div>';
					else
						$info_msg = '<div class="alert alert-success">ویدئوهای انتخاب شده حذف شدند.</div>';
					
					if (count($videos) > 0)
					{
						foreach ($videos as $k => $video)
						{
							if (file_exists(_VIDEOS_DIR_PATH . $video['url']))
							{
								unlink(_VIDEOS_DIR_PATH . $video['url']);
							}
							if ($video['thumbnail'] != '')
							{
								unlink(_THUMBS_DIR_PATH . $video['thumbnail']);
							}
						}
					}
				}
			}
		}	
	break;
} //	end switch

// COUNT VIDEOS IN DB
$total_videos = count_entries('pm_temp', '', '');

if($total_videos - $from == 1)
	$page--;

$approve_nonce = csrfguard_raw('_admin_approve');

$videos = a_list_temp('', '', $from, $limit, $page); 

if($total_videos - $from == 1)
	$page++;

// generate smart pagination
$filename = ('' != $_SERVER['PHP_SELF']) ? basename($_SERVER['PHP_SELF']) : 'approve.php';
$pagination = '';

$pagination = a_generate_smart_pagination($page, $total_videos, $limit, 1, $filename, '');

?>
<div id="adminPrimary">
    <div class="row-fluid" id="help-assist">
        <div class="span12">
        <div class="tabbable tabs-left">
          <ul class="nav nav-tabs">
            <li class="active"><a href="#help-overview" data-toggle="tab">نمای کلی</a></li>
          </ul>
          <div class="tab-content">
            <div class="tab-pane fade in active" id="help-overview">
            <p>زمانی که شما ویئویی را داخل سایت تان آپلود می کنید در اینجا برای تایید شدن قرار می گیرند
            شما می توانید برای دیدن تامبنیلی از ویدئو های ارسال شده بر روی دکمه اجرا کلیک کنید.</p>
            <p>بعد از هر ارسال که موفقیت آمیز بود شما می توانید با کلیک بر روی ستون &quot;اقدامات&quot; و بعد با کلیک بر روی آیکون &quot;بررسی کردن&quot; آنرا تایید کنید. ویدئو همان جوری که توسط کاربر ارسال شد منتشر خواهد شد اما شما می توانید تنظیم کنید که بعد از اینکه در صن انتظار قرار گرفت تایید شود.</p>
            </div>
          </div>
        </div> <!-- /tabbable -->
        </div><!-- .span12 -->
    </div><!-- /help-assist -->
    <div class="content">
	<a href="#" id="show-help-assist">راهنما</a>
    <div class="entry-count">
        <ul class="pageControls">
            <li>
                <div class="floatL"><strong class="blue"><?php echo pm_number_format($total_videos); ?></strong><span>ویدئوها</span></div>
                <div class="blueImg"><img src="img/ico-videos-new.png" width="18" height="18" alt="" /></div>
            </li>
        </ul><!-- .pageControls -->
    </div>
	<h2>ویدئوهای در انتظار تایید</h2>
<?php echo $info_msg; ?>
<?php if (is_array($errors) && count($errors) > 0) : ?>
<div class="alert alert-error">
	<ul>
	<?php foreach ($errors as $k => $err_msg) : ?>
		<li><?php echo $err_msg;?></li>
	<?php endforeach; ?>
	</ul>
</div>
<?php endif; ?>

<div class="row-fluid">
<div class="span8">
</div><!-- .span8 -->
<div class="span4">
  <form name="videos_per_page" action="approve.php" method="get" class="form-inline pull-right">
  	<input type="hidden" name="page" value="1" />
  	<label><small>ویدئوها/صفحه</small></label>
    <select name="results" class="smaller-select" onChange="this.form.submit()" >
	<option value="10" <?php if($limit == 10) echo 'selected="selected"'; ?>>10</option>
	<option value="20" <?php if($limit == 20) echo 'selected="selected"'; ?>>20</option>
	<option value="50" <?php if($limit == 50) echo 'selected="selected"'; ?>>50</option>
	<option value="70" <?php if($limit == 70) echo 'selected="selected"'; ?>>70</option>
	<option value="100" <?php if($limit == 100) echo 'selected="selected"'; ?>>100</option>
    </select>
  </form>
</div>
</div>

<form name="approve_videos_checkboxes" action="approve.php?page=<?php echo $page;?>" method="post">

<table cellpadding="0" cellspacing="0" width="100%" class="table table-striped table-bordered pm-tables tablesorter">
 <thead>
  <tr> 
	<th align="center" style="text-align:center" width="20"><input type="checkbox" class="checkbox" name="checkall" id="selectall" onclick="checkUncheckAll(this);"/></th>
	<th width="15">&nbsp;</th>
    <th width="40%">توصیف &amp; عنوان</th>
	<th>Tags</th>
    <th>Submitted on</th>
	<th width="5%">ارسال شده توسط</th>
    <th>دسته</th>
    <th align="center" style="text-align:center; width: 120px;">اقدام</th>
  </tr>
 </thead>
 <tbody>
 	<?php if ($pagination != '') : ?>
 	<tr>
		<td colspan="6" class="tableFooter">
			<div class="pagination pull-right"><?php echo $pagination; ?></div>
		</td>
	</tr>
 	<?php endif; ?>
	
	<?php echo $videos; ?>
	
	<?php if ($pagination != '') : ?>
    <tr>
		<td colspan="6" class="tableFooter">
			<div class="pagination pull-right"><?php echo $pagination; ?></div>
		</td>
	</tr>
    <?php endif; ?>
 </tbody>
</table>

    <div class="clearfix"></div>
    
    <div id="stack-controls" class="list-controls">
    <div class="pull-left">
    </div>
	<div class="btn-toolbar">
	<div class="btn-group">
    <button type="submit" name="Submit" value="Approve checked" class="btn btn-small btn-success btn-strong">تایید انتخاب شده</button>
    </div>
    <div class="btn-group dropup">
    <button type="submit" name="Submit" value="Delete checked" class="btn btn-small btn-danger btn-strong" onClick="return confirm_delete_all();">حذف انتخاب شده</button>
    <button class="btn  btn-small btn-danger dropdown-toggle" data-toggle="dropdown">
    <span class="caret"></span>
    </button>
    <ul class="dropdown-menu pull-right">
        <li><a href="#" rel="tooltip" title="Remove ALL (<?php echo $total_videos; ?>) pending videos?" onClick="del_alltemp()">حذف همه</a></li>
    </ul>
    </div>
	</div>
	<input type="hidden" name="_pmnonce" id="_pmnonce<?php echo $approve_nonce['_pmnonce'];?>" value="<?php echo $approve_nonce['_pmnonce'];?>" />
	<input type="hidden" name="_pmnonce_t" id="_pmnonce_t<?php echo $approve_nonce['_pmnonce'];?>" value="<?php echo $approve_nonce['_pmnonce_t'];?>" />
    </div><!-- #list-controls -->

</form>

    </div><!-- .content -->
</div><!-- .primary -->
<?php
include('footer.php');
?>