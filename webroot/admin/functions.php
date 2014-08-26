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

function get_catnamefromid($id) 
{
	$id = (int) $id;
	if ($id == 0)
		return '- Root -';

	$categories = load_categories();
	return $categories[$id]['name'];
}

function vnamefromvid($vid) 
{
	$type = 'video';

	if (strpos($vid, 'article') !== false)
	{
		$pieces = explode('-', $vid);
		$id = (int) $pieces[1];
		$q = mysql_query("SELECT title FROM art_articles WHERE id = '". $id ."'");
		$type = 'article';
	}
	else
	{
		$q = mysql_query("SELECT video_title FROM pm_videos WHERE uniq_id = '".$vid."'");
	}
	
	$r = mysql_fetch_assoc($q);

	if ($type == 'video')
	{
		$title = $r['video_title'];
	}
	else if ($type == 'article')
	{
		$title = $r['title'];
	}
	
	mysql_free_result($q);
	
	return (strlen($title) > 0) ? $title : 'Missing title';
}


function a_list_videos($search_term, $search_type = 'video_title', $from = 0, $to = 20, $page = 1, $filter = "", $filter_value = "") 
{
	global $userdata;
	
	if( ! $page)	$page = 1;
	
	if(!empty($search_term) && $search_type == 'video_title' ) 
	{
		$query = mysql_query("SELECT * FROM pm_videos WHERE video_title LIKE '%".$search_term."%' ORDER BY added DESC");
	} 
	else if(!empty($search_term) && $search_type == 'yt_id' )
	{
		$query = mysql_query("SELECT * FROM pm_videos WHERE yt_id LIKE '".$search_term."' ORDER BY added DESC");
	} 
	else if(!empty($search_term) && $search_type == 'uniq_id' )
	{
		$query = mysql_query("SELECT * FROM pm_videos WHERE uniq_id LIKE '".$search_term."' ORDER BY added DESC");
	}
	else if(!empty($search_term) && $search_type == 'submitted' )
	{
		$query = mysql_query("SELECT * FROM pm_videos WHERE submitted LIKE '".$search_term."' ORDER BY added DESC");
	}
	else 
	{
		$sql = '';
		$orderby = 'added';
		$order = 'DESC';
		
		if($filter != '')
		{
			$sql = "SELECT * FROM pm_videos ";
			switch($filter)
			{
				case 'broken':
				
					$sql .= " WHERE status='".VS_BROKEN."' ";
					
				break;
				
				case 'restricted':
				
					$sql .= " WHERE status='".VS_RESTRICTED."' ";
					
				break;
				
				case 'unchecked':
				
					$sql .= " WHERE status='".VS_UNCHECKED."' AND source_id = '3' ";
					
				break;
				
				case 'localhost':
				
					$sql .= " WHERE source_id='1' ";
					
				break;
				
				case 'featured':
				
					$sql .= " WHERE featured='1' ";
					
				break;
				
				case 'category':
					
					if ($filter_value == 0)
					{
						$sql .= " WHERE category LIKE '0' "; 
					}
					else
					{
						$sql .= " WHERE category LIKE '". $filter_value ."' 
								   OR category LIKE '". $filter_value .",%' 
								   OR category LIKE '%,". $filter_value ."' 
								   OR category LIKE '%,". $filter_value .",%' ";
					}
						   
				break;
				
				case 'source':
				
					$sql .= " WHERE source_id='". $filter_value ."' ";
					
				break;
				
				case 'access':
					
					$sql .= " WHERE restricted = '". $filter_value ."' ";
					
				break;
				
				case 'added': // sorting
					
					$order = (in_array($filter_value, array('DESC', 'ASC', 'desc', 'asc'))) ? $filter_value : 'DESC';
					$orderby =  'added';

				break;
				
				case 'views': // sorting
					
					$order = (in_array($filter_value, array('DESC', 'ASC', 'desc', 'asc'))) ? $filter_value : 'DESC';
					$orderby =  'site_views';
					
				break;
				
				case 'mostviewed': // sorting

					$orderby = 'site_views';
					$order = 'DESC'; 
					
				break;
			}
			
			$sql .= " ORDER BY ". $orderby .' '. $order;
			
			$sql .= " LIMIT ".$from.", ".$to;
		}
		else
		{
			$sql = "SELECT * FROM pm_videos ORDER BY added DESC LIMIT ".$from.", ".$to;
		}
		$query = mysql_query($sql);
	}

	$count = mysql_num_rows($query);	
	$categories = a_list_cats_simple();

	// LIST VIDEOS
	if($count >= 1) 
	{
		$videos = '';
		$sources = a_fetch_video_sources();
		
		$alt = 1;
		while($r = mysql_fetch_array($query)) 
		{
			$bin_rating_meta = false;
			if (function_exists('bin_rating_get_item_meta'))
			{
				$bin_rating_meta = bin_rating_get_item_meta($r['uniq_id']);
			}
			
			$alt++;
			if(!empty($r['last_check']) && $r['last_check'] != '0') {
				$last_check = date("M d, Y h:i a", $r['last_check']);
			} else {
				$last_check = 'n/a';		
			}
			$status = '';
			$status_img = '';
			switch($r['status'])
			{
				default:
				case VS_UNCHECKED: 	$status = "Video Status: Unchecked";		$status_img = VS_UNCHECKED_IMG;		break;
				case VS_OK: 		$status = "Video Status: OK";				$status_img = VS_OK_IMG; 			break;
				case VS_BROKEN: 	$status = "Video Not Found";				$status_img = VS_BROKEN_IMG; 		break;
				case VS_RESTRICTED:	$status = "Video Status: Geo-restricted";	$status_img = VS_RESTRICTED_IMG;	break;
			}
			//$status_img .= ".png";

			//	Video row
			$videos .= '<tr>';
			//	checkbox
			$videos .= '<td align="center" style="text-align:center" width="3%">';
			
			if(in_array($r['source_id'], array(3, /*22,*/ 1, 2)))	//	Youtube, MTV(deprecated), "localhost" and "other"
			{
				$videos .= "<input name=\"video_ids[]\" type=\"checkbox\" value=\"".$r['uniq_id']."\" id=\"".$r['id']."\" />";
			}
			else
			{
				$videos .= "<input name=\"video_ids[]\" type=\"checkbox\" value=\"".$r['uniq_id']."\" />";
				$status_img = VS_NOTAVAILABLE_IMG;
				$status = "Not Available";
			}
			
			//	Video Source Icon
			$source_icon = strtolower($sources[$r['source_id']]['source_name']);
			if ($r['source_id'] == 0)
			{
				$source_icon = 'embed_flash';
			}
			else if ($r['source_id'] == 1 || $r['source_id'] == 2)
			{
				$tmp_parts = explode('.', $r['url_flv']);
				$source_icon = array_pop($tmp_parts);
				$source_icon = strtolower($source_icon);
			}
			else
			{
				$source_icon = str_replace('.', '', $source_icon);
			}
			
			$source_icon_title = ucfirst($sources[$r['source_id']]['source_name']);
			if ($sources[$r['source_id']]['source_name'] == '')
			{
				$source_icon_title = 'جاسازی شده';
			}

			$videos .= '</td>';
			$videos .= '<td align="center" style="text-align:center" width="2%">';
			//	video source icon
			$videos .= ' <a href="videos.php?page=1&filter=source&fv='. $r['source_id'] .'" rel="tooltip" title="توسط منبع این ویدئو فیلتر شده (<strong>'. $source_icon_title .'</strong>)">';
			$videos .= '<div class="sprite '. $source_icon .'"></div>';
			$videos .= ' </a>';
			$videos .= '</td>';
			//	unique id
			$videos .= '<td align="center" style="text-align:center"><small>'. $r['uniq_id'] .'</small></td>';
			//	video status icon
			$videos .= '<td align="center" style="text-align:center; width: 12px;">';
			$videos .= '<div class="pm-sprite '. $status_img .'" id="status_'. $r['id'] .'" alt="" rel="tooltip" title="'.$status.' <br> Last checked: '.$last_check.'"></div>';
			$videos .= '</td>';

			//  Video Restriction icon
			if ($r['restricted'] == '1')
			{
				$videos .= '<td align="center" style="text-align:center; width: 10px;">';
				$videos .= '<div class="pm-sprite ico-locked" rel="tooltip" align="absbottom" title="فقط کاربرانی که ثبت نام نموده اند قادر به مشاهده این ویدئو هستند"></div>';
				$videos .= '</td>';
			} else {
				$videos .= '<td align="center" style="text-align:center" width="12"></td>';
			}
			//	Video title
			$videos .= '<td><span style="visibility:hidden; display:none;">'.stripslashes($r['video_title']).'</span>';
			if ($r['featured'] == '1')
			{
				$videos .= '<span class="label label-featured"><a href="videos.php?filter=featured" rel="tooltip" title="Click to list only featured videos">FEATURED</a></span> ';
			}
			$videos .= ' <a href="'. _URL.'/watch.php?vid='. $r['uniq_id'] .'" target="blank">'; 
			$videos .= stripslashes($r['video_title']);
			$videos .= ' </a>';
			if ($r['added'] > time())
			{
				$videos .= ' &mdash; <small>برای بعد برنامه ریزی شود</small>'; 
			}
			
			if ($bin_rating_meta)
			{
				$videos .= '<span class="pull-right">';
				$videos .= '<i class="icon-thumbs-up opac5"></i> <small>'. pm_number_format($bin_rating_meta['up_vote_count']) .'</small>';
				$videos .= '&nbsp;&nbsp;';
				$videos .= '<i class="icon-thumbs-down opac5"></i> <small>'. pm_number_format($bin_rating_meta['down_vote_count']) .'</small>';
				$videos .= '</span>';
			}
			
			$videos .= '</td>';
			//	date
			$videos .= '<td align="center" style="text-align:center">';
			$videos .= ' <span style="font-size:0.1pt; position:absolute; color:#fff; display:none;">'. $r['added'] .'</span>';
			$videos .= '<span rel="tooltip" title="'. date('l, F j, Y g:i A', $r['added']) .'">';
			$videos .=  date('M d, Y', $r['added']);
			$videos .= '</span>';
			$videos .= '</td>';
			//	views
			$videos .= '<td align="center" style="text-align:center">';
			$videos .= pm_number_format($r['site_views']);
			$videos .= '</td>';
			
			//	category
			$videos .= '<td>';
			$video_cats = explode(',', $r['category']);
			foreach ($video_cats as $k => $cid)
			{
				$cid = (int) $cid;
				if($k >= 1) 
					$videos .= ' / ';
				$videos .= '<a href="videos.php?page=1&filter=category&fv='. $cid .'" ';
				if ($cid == '0')
				{
					$videos .= ' title="فقط ویدئوهای دسته بندی نشده را لیست کن">';
					$videos .= 'دسته بندی نشده';
				}
				else
				{
					$videos .= ' title="List videos from '. $categories[ $cid ]['name'] .' only">';
					$videos .= $categories[ $cid ]['name'];
				}
				$videos .= '</a> ';
			}
			$videos .= '</td>';
			//	comments control
			$temp 	 = count_entries('pm_comments', 'uniq_id', $r['uniq_id']);
			$videos .= '<td align="center" style="text-align:center">';
			$videos .= ' <a href="comments.php?vid='. $r['uniq_id'] .'">';
			$videos .= 'View';
			$videos .= ' </a>';
						
			if (is_admin() || (is_moderator() && mod_can('manage_comments')))
			{
				$videos .= ' | ';
				$videos .= ' <a href="#" ';
				$videos .= ' onClick=\'del_video_comments("'. $r['uniq_id'] .'", "'. $page .'", "'. $filter .'")\'>';
				$videos .= 'Delete ('. $temp .')';
			}
			
			$videos .= '</td>';
			//	actions
			$videos .= '<td align="center" class="table-col-action" style="text-align:center">';
			$videos .= ' <a href="modify.php?vid='. $r['uniq_id'] .'" class="btn btn-mini btn-link" rel="tooltip" title="ویرایش کردن ویدئو"><i class="icon-pencil"></i></a> ';
			$videos .= ' <a href="#" onClick=\'del_video_id("'. $r['uniq_id'] .'", "'. $page .'", "'. $filter .'")\' class="btn btn-mini btn-link" rel="tooltip" title="حذف کردن ویدئو"><i class="icon-remove"></i></a>';
			$videos .= '</td>';
			
			$videos .= '</tr>';
		}
	} 
	elseif($count == 0) 
	{
		$videos .= '<tr>';
		$videos .= ' <td colspan="11" align="center" style="text-align:center">';
		$videos .= 'هیچ ویدئویی پیدا نشد. <a href="#addVideo" data-toggle="modal"><strong>الان ویدئو را اضافه کن</strong></a>.';
		$videos .= ' </td>';	
		$videos .= '</tr>';
	}
	return $videos;
}

function a_list_temp($search_term, $search_type = 'video_title', $from = 0, $to = 20, $page = 1) {
	
	global $approve_nonce;
	
	$mimetype = array(	'flv' => 'video/x-flv', 
						'mov' => 'video/quicktime', 
						//'avi' => 'video/x-msvideo', 
						'divx' => 'video/x-divx', 
						'mp4' => 'video/mp4', 
						'wmv' => 'video/x-ms-wmv', 
						'bin' => 'application/octet-stream', 
						'avi' => 'video/avi',
						'mkv' => 'video/x-matroska',
						'asf' => 'video/x-ms-asf', 
						'wma' => 'audio/x-ms-wma', 
						'mp3' => 'audio/mpeg', 
						'm4v' => 'video/mp4', 
						'm4a' => 'audio/mp4', 
						'3gp' => 'video/3gpp', 
						'3g2' => 'video/3gpp2' 
						);

	if(!$page)	$page = 1;
	
	$query = mysql_query("SELECT * FROM pm_temp ORDER BY added DESC LIMIT ".$from.", ".$to);
	$count = mysql_num_rows($query);

	// LIST VIDEOS
	if($count >= 1) 
	{
		$videos = '';
		$alt	= 1;
		$sources = a_fetch_video_sources();
		while($r = mysql_fetch_assoc($query)) 
		{
			$col = ($alt % 2) ? 'table_row1' : 'table_row2';
			$alt++;
			
			$status = '';
			$status_img = '';
			switch($r['status'])
			{
				default:
				case VS_UNCHECKED: 	$status = "Video Status: Unchecked";		$status_img = VS_UNCHECKED_IMG;		break;
				case VS_OK: 		$status = "Video Status: OK";				$status_img = VS_OK_IMG; 			break;
				case VS_BROKEN: 	$status = "Video Not Found";				$status_img = VS_BROKEN_IMG; 		break;
				case VS_RESTRICTED:	$status = "Video Status: Geo-restricted";	$status_img = VS_RESTRICTED_IMG;	break;
			}
			//$status_img .= ".png";
			
			//	video row
			$videos .= '<tr class="'. $col .'">';
			//	checkbox
			$videos .= '<td>';
			$videos .= ' <input name="video_ids[]" type="checkbox" value="'. $r['id'] .'" />';
			$videos .= '</td>';
			//	video source icon
			$videos .= '<td valign="top" style="vertical-align: top">';
			if ($r['source_id'] == $sources['localhost']['source_id'])
			{
				$filesize = readable_filesize( @filesize(_VIDEOS_DIR_PATH . $r['url']));
				$tmp_parts = explode('.', $r['url']);
				$buff_ext = array_pop($tmp_parts);
				$buff_ext = strtolower($buff_ext);
				$thumb_url = ($r['thumbnail'] != '') ? _URL.'/uploads/thumbs/'.$r['thumbnail'] : _URL .'/admin/img/no-thumbnail.jpg';
                $videos .= '
					<div class="stack-thumb-selected stack-thumb" style="width: 134px;">
                    <span class="stack-video-source">
					<div class="sprite '. strtolower($sources[$r['source_id']]['source_name']) .'" rel="tooltip" title="Source: '. ucfirst($sources[$r['source_id']]['source_name']).'"></div>
					</span>
                    <span class="stack-preview"><a href="'. _VIDEOS_DIR . $r['url'] .'" rel="prettyPop[flash]" title="'. htmlentities($r['video_title']) .'"><div class="pm-sprite ico-playbutton"></div></a></span>
                    <img src="'.$thumb_url.'" alt="" width="134" height="103" border="0" name="video_thumbnail" class="" />
                    </div>';

				$videos .= ' <strong>اندازه</strong>: '. $filesize .' / ';
				$videos .= ' <strong>نوع</strong>: '. $mimetype[$buff_ext];
			}
			else if ($r['source_id'] == $sources['youtube']['source_id'])
			{
				preg_match("/v=([^(\&|$)]*)/", $r['url'], $matches);
				$yt_id = $matches[1];
                $videos .= '
					<div class="stack-thumb-selected stack-thumb" style="width: 134px;">
                    <span class="stack-video-source">
					<div class="sprite '. strtolower($sources[$r['source_id']]['source_name']) .'" rel="tooltip" title="Source: '. ucfirst($sources[$r['source_id']]['source_name']).'"></div>
					</span>
                    <span class="stack-preview"><a href="http://www.youtube.com/v/'. $yt_id .'&autoplay=1&v='. $yt_id .'" rel="prettyPop[flash]" title="'. htmlentities($r['video_title']) .'"><div class="pm-sprite ico-playbutton"></div></a></span>
                    <img src="http://img.youtube.com/vi/'. $yt_id .'/mqdefault.jpg" alt="" width="134" border="0" name="video_thumbnail" class="" />
                    </div>';
			}
			else
			{
				$thumb_url = _URL .'/admin/img/no-thumbnail.jpg';
				if (strpos($r['thumbnail'], 'http') !== false)
				{
					$thumb_url = $r['thumbnail'];
				}
				elseif ($r['thumbnail'] != '')
				{
					$thumb_url = _URL.'/uploads/thumbs/'.$r['thumbnail'];
				}

				$videos .= '<div class="stack-thumb-selected stack-thumb" style="width: 134px;">
                    <span class="stack-video-source">
					<div class="sprite '. strtolower($sources[$r['source_id']]['source_name']) .'" rel="tooltip" title="Source: '. ucfirst($sources[$r['source_id']]['source_name']).'"></div>
					</span>
                    <img src="'. $thumb_url .'" alt="" width="134" border="0" name="video_thumbnail" class="" />
                    </div>';
			}
			$videos .= '</td>';
			
			//	video title
			$videos .= '<td valign="top" style="vertical-align: top">';
			$videos .= '<h5 style="line-height: 1em">';
			$videos .= stripslashes($r['video_title']);
			$videos .= ' </h5>';
			if (str_word_count($r['description'], 0) > 30)
			{
				preg_match('/^(.{1,255})\b/s', $r['description'], $matches);
				$excerpt = $matches[1];
				$videos .= '<span id="excerpt-'. $r['id'] .'">'. $excerpt .' ... </span>';
				$videos .= '<a href="#" id="show-more-'. $r['id'] .'" title="Show more">show more</a>';
				$videos .= '<span id="full-text-'. $r['id'] .'" style="display:none;">'. $r['description'] .'</span>';
				$videos .= '<br /><a href="#" id="show-less-'. $r['id'] .'" style="display:none;" title="کمتر">بیشتر</a>';
			}
			else
			{
				$videos .= ' '. $r['description'];
			}
			$videos .= '</td>';
			//	tags
			$videos .= '<td>';
			$videos .= str_replace(",", ", ", $r['tags']);
			$videos .= '</td>';
			//	date
			$videos .= '<td align="center" style="text-align: center">';
			$videos .= date('M d, Y h:m a', $r['added']);
			$videos .= '</td>';
			//	submitted by 
			$videos .= '<td align="center" width="5%" style="text-align: center">';
			$videos .= ' <a href="'. _URL . '/profile.php?u='. $r['username'] .'">'. $r['username'] .'</a>';
			$videos .= '</td>';
			//	category
			$videos .= '<td>';
			$videos .= make_cats($r['category']); 
			$videos .= '</td>';
			//	actions
			$videos .= '<td align="center" style="text-align: center;width: 100px;" class="table-col-action">';
			$videos .= ' <a href="approve_edit.php?id='. $r['id'] .'" class="btn btn-mini btn-link" rel="tooltip" title="ویرایش کردن"><i class="icon-pencil"></i></a>';
			$videos .= ' <a href="approve.php?a=approve&vid='. $r['id'] .'&page='. $page .'&_pmnonce='. $approve_nonce['_pmnonce'] .'&_pmnonce_t='. $approve_nonce['_pmnonce_t'] .'" class="btn btn-mini btn-link" rel="tooltip" title="تایید کردن"><i class="icon-ok"></i></a>';
			$videos .= ' <a href="#" onClick=\'del_temp_video_id("'. $r['id'] .'", "'. $page .'")\' class="btn btn-mini btn-link" rel="tooltip" title="حذف کردن"><i class="icon-remove"></i></a>';
			$videos .= '</td>';
			
			$videos .= '</tr>';
		}
	} 
	else if($count == 0) 
	{
		$videos .= '<tr>';
		$videos .= ' <td colspan="9" align="center" style="text-align: center">';
		$videos .= 'هیچ ویدئو در حال انتظاری برای تایید وجود ندارد';
		$videos .= ' </td>';	
		$videos .= '</tr>';
	}
	return $videos;
}

function a_list_comments($search_term = '', $search_type = 'comment', $from = 0, $limit = 20, $page = 1, $filter = '') 
{
	global $comments_nonce;
	
	if(!$from)	$from = 0;
	if(!$limit)	$limit = 20;
	if(!page)	$page = 1;
	
	if($search_term != '') 
	{
		$sql = 'SELECT * FROM pm_comments WHERE ';
		switch($search_type)
		{
			default:
			case 'comment' : $sql .= 'comment';  break;
			case 'username' : $sql .= 'username'; break;
			case 'ip' : $sql .= 'user_ip'; break;
			case 'uniq_id' : $sql .= 'uniq_id'; break;
		}
			$sql .= " LIKE '%".secure_sql($search_term)."%' ORDER BY added DESC";
		$query = mysql_query($sql);
		$total = mysql_num_rows($query);
	}
	else 
	{
		$sql = '';
		if($filter != '')
		{
			$sql = "SELECT * FROM pm_comments ";
			$sql_count = "SELECT COUNT(*) as total_found FROM pm_comments ";

			switch($filter)
			{
				case 'articles':
				
					$sql .= " WHERE uniq_id LIKE 'article-%' ";
					$sql_count .= " WHERE uniq_id LIKE 'article-%' ";
					
				break;
				
				case 'videos':
				
					$sql .= " WHERE uniq_id NOT LIKE 'article-%' ";
					$sql_count .=  " WHERE uniq_id NOT LIKE 'article-%' ";
					
				break;
				
				case 'flagged':
					$sql .= " WHERE report_count > 0 ";
					$sql_count .= " WHERE report_count > 0 ";
				break;

				case 'pending':
					$sql .= " WHERE approved='0' ";
					$sql_count .= " WHERE approved='0' ";
				break;
			}
			$sql .= " ORDER BY added DESC LIMIT ".$from.", ".$limit;
		}
		else
		{
			$sql = "SELECT * FROM pm_comments ORDER BY added DESC LIMIT ".$from.", ".$limit;
			$sql_count = "SELECT COUNT(*) as total_found FROM pm_comments";
		}
		
		//	First, count all entries
		if (strlen($sql_count) > 0)
		{
			$result_count = @mysql_query($sql_count);
			if ( ! $result_count)
			{
				$total = $limit;
			}
			else
			{
				$row_count = mysql_fetch_assoc($result_count);
				mysql_free_result($result_count);
				
				$total = $row_count['total_found'];
				
				unset($sql_count, $result_count, $row_count);
			}
		}
		else
		{
			$total = $limit;
		}

		$query = mysql_query($sql);
	}
	$count = mysql_num_rows($query);

	// LIST COMMENTS
	if($count > 0) 
	{
		$res_arr = array();
		while($r = @mysql_fetch_array($query)) 
		{
			$res_arr[] = $r;
		}
		$res_arr_len = count($res_arr);
		
		if($from == 0)
			$start = 0;
		elseif($from >= $res_arr_len)
			//$start = $from - $limit;
			$start = 0;
		else
			$start = $from;
		
		if( ($start + $limit) >= $res_arr_len)
			$to = $res_arr_len;
		else $to = ($limit + $start);
		
		$comments = '';
		$alt = 1;
		for($i = $start; $i < $to; $i++)
		{
			$col = ($alt % 2) ? 'even' : 'odd';//'odd' : 'even';
			$alt++;

			$comments .= "
			  <tr id=\"category_update\">
				<td align=\"center\" style=\"text-align: center\" width=\"20\"><input name=\"video_ids[]\" type=\"checkbox\" class=\"checkbox\" value=\"".$res_arr[$i]['id']."\" /></td>
				<td  align=\"center\" style=\"text-align: center\" width=\"10\">";
				
			if (strpos($res_arr[$i]['uniq_id'], 'article') !== false)
			{
				$comments .= '<img src="img/ico-articles-bw.png" width="13" height="12" align="absmiddle" class="opac5" /> ';

			} else {

				$comments .= '<img src="img/ico-videos-bw.png" width="16" height="14" align="absmiddle" class="opac5" /> ';

			}
				$comments .= "</td>	
				<td>";
			
			if (strpos($res_arr[$i]['uniq_id'], 'article') !== false)
			{
				$article_id = str_replace('article-', '', $res_arr[$i]['uniq_id']);
				$comments .= '<a href="'. _URL .'/article_read.php?a='.$article_id.'&mode=preview#comments">';
			}
			else
			{
				$comments .= '<a href="'. _URL .'/watch.php?vid='.$res_arr[$i]['uniq_id'].'#comments">';
			}
			
			$comments .= vnamefromvid($res_arr[$i]['uniq_id'])."</a>";
			$comments .= "</td>";
			$comments .= "<td align=\"center\" style=\"text-align: center\">".date('M d, Y', $res_arr[$i]['added'])."</td>";
			
			
			$comments .= '<td>';
			$comments .= '<div id="comment_update_'. $i .'" name="'. $i .'">';
			if($res_arr[$i]['approved'] == 0)
				$comments .= '<span class="label label-warning">در حالت انتظار</span><br />';
				
			$comments .= '<span class="comment_update_hover" id="comment_span_'. $i .'">'. $res_arr[$i]['comment'] .'</span>'; 
			$comments .= '<div class="comment_update_form" id="comment_update_form_'. $i .'">'; 
			$comments .= '<div style="display:inline; margin:0;padding:0;">';
			$comments .= '<textarea id="commenttxt_'. $i .'" name="comment_txt" rows="3" style="width: 95%;" >'. str_replace('<br />', '', $res_arr[$i]['comment']) .'</textarea>';
			$comments .= '<input type="hidden" name="comment_id" id="commentid_'. $i .'" value="'. $res_arr[$i]['id'] .'" />';
			$comments .= '<input name="update" type="submit" value="Update" class="btn btn-mini btn-success border-radius0" id="comment_update_btn_'. $i .'" />';
			$comments .= ' <a href="#" id="comment_update_'. $i .'" class="btn-mini">Cancel</a>';
			$comments .= '</div>';
			
			$comments .= '</div></div>';
			if ($res_arr[$i]['up_vote_count'] > 0 || $res_arr[$i]['down_vote_count'] > 0)
			{
				$comments .= '<div class="pull-right">';
				$comments .= '<i class="icon-thumbs-up opac6"></i>  <small>'. pm_number_format($res_arr[$i]['up_vote_count']) .'</small>';
				$comments .= '&nbsp;&nbsp;';
				$comments .= '<i class="icon-thumbs-down opac6"></i> <small>'. pm_number_format($res_arr[$i]['down_vote_count']) .'</small>';
				$comments .= '</div>';
			}
			$comments .= "</td>";
			
				
			if($res_arr[$i]['user_id'] == 0 || $res_arr[$i]['user_id'] == 1)
				$comments .= "<td align=\"center\" style=\"text-align: center\"><strong>".($res_arr[$i]['username'])."</strong></td>";
			else
				$comments .= "<td align=\"center\" style=\"text-align:center\"><a href=\""._URL."/profile.php?u=".$res_arr[$i]['username']."\">".($res_arr[$i]['username'])."</a></td>";
				
			$comments .= "<td align=\"center\" style=\"text-align: center\"><small>".$res_arr[$i]['user_ip']."</small></td>";
			$comments .= "<td align=\"center\" class=\"table-col-action\" style=\"text-align:center;\">";
			
			$append_url = ($filter != '') ? '&filter='. $filter : '';
			$append_url .= ($_GET['vid'] != '') ? '&vid='. $_GET['vid'] : '';
			$append_url .= ($_GET['keywords'] != '') ? '&keywords='. $_GET['keywords'] .'&search_type='. $_GET['search_type'] .'&submit=Search' : '';
			
			if($res_arr[$i]['approved'] == 0)
			{
				$approve_url = 'comments.php?a=2&cid='. $res_arr[$i]['id'] .'&page='. $page . $append_url;
				$approve_url .= '&_pmnonce='. $comments_nonce['_pmnonce'] .'&_pmnonce_t='. $comments_nonce['_pmnonce_t'];
				$comments .= '<a href="'. $approve_url .'" class="btn btn-mini btn-success" rel="tooltip" title="Approve"><i class="icon-ok" ></i></a>';
				//$comments .= "<a href=\"comments.php?a=2&cid=".$res_arr[$i]['id']."&page=".$page."&filter=".$filter."&_pmnonce=". $comments_nonce['_pmnonce'] ."&_pmnonce_t=". $comments_nonce['_pmnonce_t'] ."\" class=\"btn btn-mini btn-success\" rel=\"tooltip\" title=\"Approve\"><i class=\"icon-ok\" ></i></a>";
			}
			else
			{
				$comments .= "";
			}
			
			if ($res_arr[$i]['report_count'] > 0)
			{
				$flag_title = 'این نظر پرچم (فلگ) خورده.';
				$flag_title .= ($res_arr[$i]['report_count'] > 1) ? ' by '. $res_arr[$i]['report_count'] .' different users' : '';
				$flag_title .= ' as inappropriate.';
				$comments .= "<a href=\"comments.php?filter=flagged&page=1\" rel=\"tooltip\" title=\"". $flag_title ."\"><i class=\"icon-flag\"></i></a>";
			}
			
			$comments.= '<a href="#" class="btn btn-mini btn-link" rel="tooltip" title="Edit comment" id="comment_update_pencil_'. $i .'"><i class="icon-pencil"></i></a>';
			$comments .= "<a href=\"#\" onClick=\"del_comment_id('".$res_arr[$i]['id']."', '".$page."', '". $filter . $append_url ."')\" class=\"btn btn-mini btn-link\" rel=\"tooltip\" title=\"حذف کردن نظر\"><i class=\"icon-remove\" ></i></a>";
			
			$comments .= '</tr>';
		}
	} 
	elseif($count == 0 && ($_GET['keywords'] != '' || $_GET['vid'] != '')) 
	{
		$comments .= "
		  <tr>
			<td colspan=\"8\" align=\"center\" style=\"text-align:center\">هیچ نظری با این ضواسط برابری نمی کند.</td>
		  </tr>";
	}
	else
	{
		if ($filter == 'flagged')
		{
			$comments .= "
			  <tr>
				<td colspan=\"8\" align=\"center\" style=\"text-align:center\">هنوز هیچ نظری پرچم (فلگ) نخورده.</td>
			  </tr>";			
		}
		elseif ($filter == 'pending')
		{
			$comments .= "
			  <tr>
				<td colspan=\"8\" align=\"center\" style=\"text-align:center\">هیچ نظر در حالت انتظاری برای تایید وجود ندارد.</td>
			  </tr>";			
		}
		else
		{
			$comments .= "
			  <tr>
				<td colspan=\"8\" align=\"center\" style=\"text-align:center\">هیچ نظری هنوز ارسال نشده.</td>
			  </tr>";
		}
	}
	return array('comments' => $comments, 'total' => $total);
}

function a_category_table_row($item, &$all_children, $all_categories, $level = 0, $options, &$alternate)
{
	$output = '';	
	
	if ($level > 1)
	{
		$padding = str_repeat($options['spacer'], $level-1);
	}
	
	// build output here.
	$col = ($alternate++ % 2) ? 'table_row1' : 'table_row2'; 
	
	$move_up_href = '<a href="'. $options['page'] .'?move=up&id='. $item['id']. '" rel="tooltip" title="به سمت بالا بردن"><i class="icon-chevron-up"></i></a>'; 
	$move_down_href = '<a href="'. $options['page'] .'?move=down&id='. $item['id']. '" rel="tooltip" title="به سمت پایین بردن"><i class="icon-chevron-down"></i></a>';

	
	

	$output .= '<form name="'. $item['id'] .'" action="'. $options['form_action'] .'" method="post" class="form-inline">';
	$output .= "\n";
	$output .= ' <tr id="category_update" title="category-'. $item['id'] .'"';
	$output .= ($level == 0) ?  ' class="category_parent"> ' : '>';
	$output .= '  <td align="center" style="text-align: center">'. $item['id'] .'</td>';$output .= "\n";
	$output .= '  <td>';$output .= "\n";
	$output .= '   <div class="category_update_name">';$output .= "\n";
	$output .= ($level > 0) ? $padding .' &#8212; ' : '';$output .= "\n";
	$output .= '    <strong>'. htmlentities($item['name'],ENT_COMPAT,'UTF-8') .'</strong>';$output .= "\n";
	$output .= '   </div>';$output .= "\n";
	$output .= '   <div class="category_update_form">';$output .= "\n";
	$output .= '    <div class="category_update_form form-inline"><input name="name" type="text" size="22" value="'. $item['name'] .'" />';$output .= "\n";
	$output .= '   </div></div>';$output .= "\n";
	$output .= '   <input name="cid" type="hidden" value="'. $item['id'] .'" />';$output .= "\n";
	$output .= '   <input name="parent_id" type="hidden" value="'. $item['parent_id'] .'" />';
	$output .= '   <input name="old_tag" type="hidden" value="'. $item['tag'] .'" />';
	$output .= '  </td>';$output .= "\n";
	$output .= '  <td>';$output .= "\n";
	$output .= '   <div class="category_update_name">'. $item['tag'] .'</div>';$output .= "\n";
	$output .= '   <div class="category_update_form form-inline"><input name="tag" size="15" type="text" value="'. $item['tag'] .'" /> <!--<a href="#" rel="tooltip" data-placement="left" title="تغییر اسلاگ ساختار آدرس را عوض می کند. <br>اگر دسته شما پیش از این توسط موتورهای جستجو ایندکس شده پیشنهاد نمی شود."> <i class="icon-warning-sign" style="opacity:0></i> </a>--> ';$output .= "\n";
	$output .= '    <button name="update" type="submit" value="Update" class="btn btn-success" />بروزرسانی</button>';$output .= "\n";
	$output .= '   </div>';$output .= "\n";
	$output .= '  </td>';$output .= "\n";
	$output .= '  <td style="text-align: center">'. $all_categories[$item['parent_id']]['name'] .'</td>';
	$output .= "\n";
	$output .= '  <td align="center" style="text-align: center">';
	$output .= ($options['page'] == 'cat_manager.php') ? $item['total_videos'] : $item['total_articles'];
	$output .= '  </td>';
	$output .= "\n"; 
	$output .= '  <td align="center" class="table-col-action" style="text-align: center">'. $move_up_href .' '. $move_down_href .'</td>';$output .= "\n";
	$output .= '  <td align="center" class="table-col-action" style="text-align: center">';$output .= "\n";
	if ($options['page'] == 'cat_manager.php')
	{
		$output .= '<a href="edit_category.php?mode=edit&type=video&cid='. $item['id'] .'" rel="tooltip" title="Edit category" class="btn btn-mini btn-link"><i class="icon-pencil"></i></a> ';
		$output .= '<a href="#" onClick="del_cat(\''. $item['id'] .'\')" rel="tooltip" title="Delete"><i class="icon-remove"></i></a>'; 
	}
	else
	{
		$output .= '<a href="edit_category.php?mode=edit&type=article&cid='. $item['id'] .'" rel="tooltip" title="ویرایش دسته" class="btn btn-mini btn-link"><i class="icon-pencil"></i> </a> ';
		$output .= '<a href="#" onClick="onpage_delete_category(\''. $item['id'] .'\', \'#display_result\', \'tr[title=category-'. $item['id'] .']\')" rel="tooltip" title="حذف"><i class="icon-remove"></i></a>';
	}
	$output .= "\n"; 
	$output .= '  </td>';
	$output .= "\n";
	$output .= ' </tr>';
	$output .= "\n";
	$output .= '</form>';
	$output .= "\n";$output .= "\n";
	
	
	if (isset($all_children[$item['id']]))
	{
		foreach ($all_children[$item['id']] as $k => $child)
		{
			$output .= a_category_table_row($child, $all_children, $all_categories, $level+1, $options, $alternate);
		}
		unset($all_children[$item['id']]);
	}
	
	return $output;
}

function a_category_table_body($categories = false, $args = array())
{
	$output = '';
	$empty = array();
	$defaults = array(
		'page' => 'cat_manager.php',
		'col_span' => 7,
		'form_action' => 'cat_manager.php',
		'spacer' => '&nbsp;&nbsp;&nbsp;&nbsp;',		
	);
	
	if ( ! is_array($categories))
		return;
	
	$options = array_merge($defaults, $args);
	
	extract($options);
	
	$parents = $parent_ids = $children = array();
	
	if (count($categories) == 0)
	{
		return '<tr><td colspan="'. $col_span .'" align="center" style="text-align:center;">هیچ دسته ای مشخص نشده.</td></tr>';
	}
	
	foreach ($categories as $k => $row)
	{
		if ($row['parent_id'] == 0)
		{
			$parents[] = $row;
			$parent_ids[] = $row['id'];
		}
		else
		{
			$children[$row['parent_id']][] = $row;
		}
	}
	
	$alt = 1;
	
	foreach ($parents as $k => $p)
	{
		$output .= a_category_table_row($p, $children, $categories, 0, $options, $alt);
	}
	
	foreach ($children as $parent_id => $orphans)
	{
		foreach ($orphans as $k => $orphan)
		{
			$orphan['parent_id'] = 0;
			$output .= a_category_table_row($orphan, $empty, $categories, 0, $options, $alt);
		}
	}
	
	
	return $output; 
}

function a_list_cats()
{	
	$categories = load_categories();
	return a_category_table_body($categories, array());
}

// LISTING USERS 
function a_list_users($search_term, $search_type = 'username', $from = 0, $limit = 20, $page = 1, $filter = '', $filter_value = '') 
{
	global $members_nonce;
	
	if(!$page)	$page = 1;
	
	if($search_term != '') 
	{
		$sql = 'SELECT * FROM pm_users WHERE ';
		switch($search_type)
		{
			default:
			case 'username' : $sql .= 'username'; break;
			case 'fullname' : $sql .= 'name';  break;
			case 'email' : $sql .= 'email'; break;
			case 'ip' : 
				$sql .= " reg_ip LIKE '%".secure_sql($search_term)."%' OR last_signin_ip "; 
			break;
		}
		$sql .= " LIKE '%".secure_sql($search_term)."%' ORDER BY id DESC";
		$query = mysql_query($sql);
		$total = mysql_num_rows($query);
	}
	else 
	{
		$where = '';
		$orderby = ' ORDER BY id DESC '; // default
		
		switch ($filter)
		{
			case 'power':
				$where = " WHERE power = '". $filter_value ."' ";
			break;
			
			case 'id':
				$order = (in_array($filter_value, array('DESC', 'ASC', 'desc', 'asc'))) ? $filter_value : 'DESC';
				$orderby = ' ORDER BY id '. $order;
			break;
			
			case 'register': // sorting
				$order = (in_array($filter_value, array('DESC', 'ASC', 'desc', 'asc'))) ? $filter_value : 'DESC';
				$orderby = ' ORDER BY reg_date '. $order;
			break;
			
			case 'lastlogin': // sorting
				$order = (in_array($filter_value, array('DESC', 'ASC', 'desc', 'asc'))) ? $filter_value : 'DESC';
				$orderby = ' ORDER BY last_signin '. $order;
			break;
			
			case 'followers': // sorting
				$order = (in_array($filter_value, array('DESC', 'ASC', 'desc', 'asc'))) ? $filter_value : 'DESC';
				$orderby = ' ORDER BY followers_count '. $order;
			break;
			
			case 'following': // sorting
				$order = (in_array($filter_value, array('DESC', 'ASC', 'desc', 'asc'))) ? $filter_value : 'DESC';
				$orderby = ' ORDER BY following_count '. $order;
			break;
			
		}
		
		$sql = 'SELECT * FROM pm_users ';
		$sql .= $where;
		$sql .= $orderby;
		$sql .= ' LIMIT '. $from .', '. $limit;

		$query = mysql_query($sql);
	}

	$count = mysql_num_rows($query);
	// LIST USERS
	if($count > 0) 
	{
		$banlist = get_banlist();
		
		$res_arr = array();
		while($r = @mysql_fetch_array($query)) 
		{
			$res_arr[] = $r;
		}
		$res_arr_len = count($res_arr);
		
		if($from == 0)
			$start = 0;
		elseif($from >= $res_arr_len)
			//$start = $from - $limit;
			$start = 0;
		else
			$start = $from;
		
		if( ($start + $limit) >= $res_arr_len)
			$to = $res_arr_len;
		else $to = ($limit + $start);
		
		$col = '';
		$alt = 1;
		for($i = $start; $i < $to; $i++)
		{
			//$username = (array_key_exists($res_arr[$i]['id'], $banlist)) ? '<s>'.$res_arr[$i]['username'].'</s>' : $res_arr[$i]['username'];
			$username = $res_arr[$i]['username'];
			$alt++;
			
			// checkbox
			$checkbox = "<td align=\"center\" style=\"text-align:center\">";
			if ($res_arr[$i]['power'] != U_ADMIN)
			{
				$checkbox .= "<input name=\"user_ids[]\" type=\"checkbox\" value=\"".$res_arr[$i]['id']."\" />";
			}
			$checkbox .= '</td>';
			
			$users .= "
			  <tr>
			    ". $checkbox ."
				<td align=\"center\" style=\"text-align:center\">".$res_arr[$i]['id']."</td>
				<td><a href=\""._URL."/profile.php?u=".$res_arr[$i]['username']."\" target=\"_blank\">".stripslashes($username)."</a></td>
				<td>".stripslashes($res_arr[$i]['name'])."</td>
				<td><a href=\"mailto:".$res_arr[$i]['email']."\">".$res_arr[$i]['email']."</a></td>
				<td align=\"center\" style=\"text-align:center\">".date('M d, Y', (int) $res_arr[$i]['reg_date'])."</td>
				<td style=\"text-align:center\">". $res_arr[$i]['followers_count'] ."</td>
				<td style=\"text-align:center\">". $res_arr[$i]['following_count'] ."</td>";

				if (time_since($res_arr[$i]['last_signin']) == "0 seconds") 
				{
					$users .= "<td align=\"center\" style=\"text-align:center\"><span class=\"label label-success\">Online now</span></td>";
				} 
				else 
				{
					$users .= "<td align=\"center\" style=\"text-align:center\">";
					if ($res_arr[$i]['last_signin'] == 0)
					{
						$users .= 'هرگز';
					}
					else
					{
						$users .= time_since($res_arr[$i]['last_signin'])." قبل";
					}
					$users .= '</td>';
				}
				
				$users .= "
				<td align=\"center\" style=\"text-align:center\">". (($res_arr[$i]['last_signin_ip'] != '') ? $res_arr[$i]['last_signin_ip'] : 'هنوز آی پی وجود ندارد') ."</td>
				<td style=\"text-align:center\">";
			if (array_key_exists($res_arr[$i]['id'], $banlist))
			{
				$users .= "<span class=\"label label-important\">Banned</span>";
			}
			else if ($res_arr[$i]['power'] == U_INACTIVE)
			{
				$users .= "<a href=\"edit_user_profile.php?uid=".$res_arr[$i]['id']."&action=1&filter=". $filter ."&fv=". $filter_value ."&_pmnonce=". $members_nonce['_pmnonce'] ."&_pmnonce_t=". $members_nonce['_pmnonce_t'] ."\" class=\"btn btn-mini btn-success\">فعال کن</a>";
			}
			else if ($res_arr[$i]['power'] == U_ADMIN)
			{
				$users .= "<strong>مدیر</strong>";
			}
			else if ($res_arr[$i]['power'] == U_MODERATOR)
			{
				$users .= "<strong>مدیر ارشد</strong>";
			}
			else if ($res_arr[$i]['power'] == U_EDITOR)
			{
				$users .= "معمولی";
			}
			else
			{	
				$users .= "Active";
			}
			$users .= "</td><td align=\"center\" class=\"table-col-action\" style=\"text-align:center\">";
			$users .= "<a href=\"edit_user_profile.php?uid=".$res_arr[$i]['id']."\" rel=\"tooltip\" title=\"Edit profile\" class=\"btn btn-mini btn-link\"><i class=\"icon-pencil\"></i></a>";
			$users .= "<a href=\"#\" onClick=\"del_member_id('".$res_arr[$i]['id']."', '".$page."')\" rel=\"tooltip\" title=\"حذف کردن حساب\" class=\"btn btn-mini btn-link\"><i class=\"icon-remove\"></i></a></td>
			  </tr>";
			}
	} 
	elseif($count == 0) 
	{
		$users .= "
		  <tr>
			<td colspan=\"12\" align=\"center\">متاسفانه هیچ کاربری پیدا نشد.</td>
		  </tr>";
 	    $total = $count;
	}
	return array('users' => $users, 'total' => $total);
}


// LIST VIDEO REPORTS

function a_list_vreports($r_type, $from = 0, $to = 50, $page = 1) {

	if(!$page)	$page = 1;
	$sql = "SELECT pm_reports.*, pm_videos.id as vid, pm_videos.status, pm_videos.last_check, pm_videos.source_id, pm_videos.category  
			FROM pm_reports JOIN pm_videos 
							ON (pm_reports.entry_id = pm_videos.uniq_id) 
			WHERE r_type = '".$r_type."' 
			ORDER BY pm_reports.id DESC 
			LIMIT ".$from.", ".$to;
			
	$query = mysql_query($sql) or die(mysql_error());
	$count = mysql_num_rows($query);	

	// LIST REPORTS
	if($count >= 1) {
		$reports = '';
		$sources = a_fetch_video_sources();
		
		$i = 1;
		$alt = 1;
		while($r = mysql_fetch_assoc($query)) {
		$col = ($alt % 2) ? 'table_row1' : 'table_row2';
		$alt++;
		
		if(!empty($r['last_check']) && $r['last_check'] != '0') {
			$last_check = date("M d, Y h:i a", $r['last_check']);
		} else {
			$last_check = 'n/a';		
		}
		
		$status = '';
		$status_img = '';
		switch($r['status'])
		{
			default:
			case VS_UNCHECKED: 	$status = "Video Status: Unchecked";		$status_img = VS_UNCHECKED_IMG;		break;
			case VS_OK: 		$status = "Video Status: OK";				$status_img = VS_OK_IMG; 			break;
			case VS_BROKEN: 	$status = "Video Missing";					$status_img = VS_BROKEN_IMG; 		break;
			case VS_RESTRICTED:	$status = "Video Status: Geo-restricted";	$status_img = VS_RESTRICTED_IMG;	break;
		}
		//$status_img .= ".png";

		$reports .= "<tr><td style=\"text-align:center\">";
		  
			 if($r['source_id'] == 3 || $r['source_id'] == 22)
			 {
				$reports .= "<input name=\"video_ids[]\" type=\"checkbox\" class=\"checkbox\" value=\"".$r['entry_id']."\" id=\"".$r['vid']."\" />";
			 }
			 else
			 {
				$reports .= "<input name=\"video_ids[]\" type=\"checkbox\" class=\"checkbox\" value=\"".$r['entry_id']."\" />";
				$status_img = VS_NOTAVAILABLE_IMG;
				$status = "Not Available";
			 }
		$reports .= '<input name="video_cat_ids[]" type="hidden" value="'. $r['category'] .'" />';
		$reports .= "
			</td>
			<td style=\"text-align:center\">";
		$reports .= '<div class="sprite '. strtolower($sources[$r['source_id']]['source_name']) .'" rel="tooltip" title="Source: '. ucfirst($sources[$r['source_id']]['source_name']).'"></div>';
		$reports .= "</td><td align=\"center\" style=\"text-align:center\"><small>".$r['entry_id']."</small></td>";
		$reports .= '<td align="center" style="text-align:center; width: 12px;"><div class="pm-sprite '. $status_img .'" id="status_'. $r['vid'] .'" alt="" rel="tooltip" title="'.$status.' <br> آخرین بررسی '.$last_check.'"></div></td>';					
		$reports .= "<td><a href=\""._URL."/watch.php?vid=".$r['entry_id']."\" target=\"_blank\">".vnamefromvid($r['entry_id'])."</a></td>
					<td>".$r['reason']."</td>
					<td style=\"text-align:center\">".$r['submitted']."</td>
					<td align=\"center\" class=\"table-col-action\" style=\"text-align:center\"><a href=\"modify.php?vid=".$r['entry_id']."\" class=\"btn btn-mini btn-link\" rel=\"tooltip\" title=\"بروزرسانی ویدئو\"><i class=\"icon-pencil\" ></i></a><a href=\"#\" onClick=\"del_report('".$r['id']."', '".$page."')\" class=\"btn btn-mini btn-link\" rel=\"tooltip\" title=\"حذف کردن گزارش\"><i class=\"icon-remove\" ></i></a>
					<!--<a href=\"#\" class=\"b_delete\" onClick=\"del_video_id('".$r['entry_id']."')\">Delete Video</a> -->
			
			</td>
		  </tr>";

		  $i++;
		}
	} elseif($count == 0) {

		$reports .= "
		  <tr>
			<td colspan=\"8\" align=\"center\" style=\"text-align: center;\">هیچ ویدئویی گزارش داده نشده</td>
		  </tr>";
	}
	return $reports;
}
function unhtmlspecialchars( $string ) {
        $string = str_replace ( '&amp;', '&', $string );
        $string = str_replace ( '&#039;', '\'', $string );
        $string = str_replace ( '&quot;', '\"', $string );
        $string = str_replace ( '&lt;', '<', $string );
        $string = str_replace ( '&gt;', '>', $string );
       
        return $string;
}

function get_rss_news($limit = 5) {
	$rss = new lastRSS; 
	$rssurl = "http://feeds.feedburner.com/pmFeed";
	$nowTime = strtotime(date('F jS, Y'));
		
	if ($rs = $rss->get($rssurl)) { 
		for( $i = 0; $i < $limit; $i++){
			$lastTime = strtotime($rs['items'][$i]['pubDate']);
			if(($nowTime-$lastTime) < 1330000) {
				$ret .= "<li class='news-recent'>\n";
			} else {
				$ret .= "<li>\n";
			}
			$ret .= "<a href=\"".$rs['items'][$i]['link']."\" target=\"_blank\">\n"; //<span class=\"news-tag border-radius3\">UNREAD</span>\n";
			$ret .= "<h4>".$rs['items'][$i]['title']."</h4></a>\n";
			$ret .= "<p>";
			$ret .= unhtmlspecialchars($rs['items'][$i]['description'])."</p>\n";
			$ret .= "\n";
			$ret .= "</li>";
		}
	} 
	else { 
		$ret = "<li>متاسفانه اخبار گرفته نشده اند. برای بروز نگه داشتن در خبرنامه <a href=\"http://www.96down.com/\" target=\"_blank\">www.96down.com</a> ثبت نام کنید.</"; 
	} 
	return $ret;
}

function download_thumbs($yt_id) {
	$uniq_id = md5($yt_id);
	$uniq_id = substr($uniq_id, 0, 9);
	
	$image_1 = "http://img.youtube.com/vi/".$yt_id."/default.jpg";
	$image_4 = "http://img.youtube.com/vi/".$yt_id."/3.jpg";
	if (is_file( ABSPATH . _UPFOLDER ."/thumbs/".$yt_id."-1.jpg")) {
	}
	else {
	$newimage = $uniq_id."-1.jpg";
	$url4 = ABSPATH . _UPFOLDER ."/thumbs";
  	if ( !copy("$image_1", "$url4/".$uniq_id."-1.jpg") )
		{
	return FALSE;
		}
	$image = imagecreatefromjpeg("$url4/$newimage"); 
	@Imagejpeg($image, 80); 
	//
	$newimage = $uniq_id."-2.jpg";
  	if ( !copy("$image_4", "$url4/".$uniq_id."-2.jpg") )
		{
	return FALSE;
		}
	$image = imagecreatefromjpeg("$url4/$newimage"); 
	@Imagejpeg($image, 80); 
	//
	}
}


function show_pm_notes() {

	global $config, $userdata;
	
	if ($userdata['power'] != U_ADMIN)
		return '';

	$txt_notes  = array();
	$i = 0;
	
	if ( ! is_array($config))
	{
		$config = get_config();
	}
	// check for new versions
	$official_version = cache_this('read_version', 'pm_version'); 
	if (version_compare($official_version, $config['version']) == 1) 
	{
		$txt_notes[$i]['title'] = 'اخبار عالی!';
		$txt_notes[$i]['desc']  = '<strong>PHP Melody '.$official_version.'</strong> در دسترس هست برای دانلود.<br /> برای دانلود  <a href="http://www.96down.com/customer/" target="_blank">حساب مشتری تان</a> وارد شوید. ';
		$txt_notes[$i]['ico']  = 'gritter-ico_download.png';
		$txt_notes[$i]['bgcolor'] = 'green';
		$i++;
	}
	// check for default password
	$admin_pass = md5('admin');
	if ($userdata['password'] == $admin_pass) 
	{
		$txt_notes[$i]['title'] = 'از سایت تام محافظت کنید';
		$txt_notes[$i]['desc']  = 'لطفا رمز عبور پیش فرض مدیر را تغییر بدهید <br /><a href="password.php">برای امن کردن وب سایت تان اینجا کلیک کنید</a>.';
		$txt_notes[$i]['ico']  = 'gritter-ico_pass.png';
		$txt_notes[$i]['bgcolor'] = 'red';
		$i++;
	}


	/*
	//Curt
	if (_CUSTOMER_ID == 'YOUR_CUSTOMER_ID') 
	{
		$txt_notes[$i]['title'] = 'System Message';
		$txt_notes[$i]['desc']  = 'Your Customer ID is <strong>invalid</strong>. Update <strong>config.php</strong> to add your real Customer ID. It\'s also available in your <a href="http://www.96down.com/customer/">customer account</a>.';
		$txt_notes[$i]['ico']  = 'gritter-ico_info.png';
		$txt_notes[$i]['bgcolor'] = 'blue';
		$i++;
	}*/
	if(file_exists("db_update.php"))
	{
		$txt_notes[$i]['title'] = 'بروزرسانی دیتابیس';
		$txt_notes[$i]['desc']  = 'بروزرسانی هنوز کامل نشد. برای بروزرسانی دیتابیس اینجا <a href="db_update.php">کلیک کنید/a>.';
		$txt_notes[$i]['ico']  = 'gritter-ico_warn.png';
		$txt_notes[$i]['bgcolor'] = 'red';
		$i++;
	}	
	if ($config['mail_server'] == 'mail.domain.com')
	{
		$txt_notes[$i]['title'] = 'تنظیمات ایمیل';
		$txt_notes[$i]['desc']  = 'php medoly برای مدیریت کردن ایمیل های سایت نیاز به حساب ایمیل دارد. برو به <a href="settings.php"><strong>تنظیمات < تنظیمات ایمیل</strong></a> برای انجام دادن این گام.';
		$txt_notes[$i]['ico']  = 'gritter-ico_warn.png';
		$txt_notes[$i]['bgcolor'] = 'red';
		$i++;
	}
	if (($config['custom_logo_url'] == '') && ($config['template_f'] == 'default'))
	{
		$txt_notes[$i]['title'] = 'شخصی سازی ویدئو سایت شما';
		$txt_notes[$i]['desc']  = 'برای آپلود <strong>لوگو</strong> یا تغییر دادن <strong>رنگ برنامه استاندارد</strong>, تنظیمات طرح <a href="settings_theme.php"><strong>را ببین</strong></a>.';
		$txt_notes[$i]['ico']  = 'gritter-ico_setup.png';
		$txt_notes[$i]['bgcolor'] = 'red';
		$i++;
	}
	if (count($txt_notes) == 0) 
	{
		$txt_notes = false;
	}
	
	$result = '';
	if (is_array($txt_notes))
	{
		foreach ($txt_notes as $k => $arr) 
		{
			$result .= "show_pm_note('".$arr['title']."', '".secure_sql($arr['desc'])."', 'img/".$arr['ico']."','".$arr['bgcolor']."');\n\r"; 
		}
	}
	
	echo $result;
}


function read_version() {

	// You like?
	$ww = 'ht';
	$ww .= 'tp:/';
	$ww .= '/w';
	$ww .= 'ww';	
	$ww .= '.php';
	$ww .= 'sug';
	$ww .= 'ar.co';
	$ww .= 'm/updates/';
	
	$url = $ww.'pm_version.txt';
	if (ini_get('allow_url_fopen') == 1) 
	{
		$content = @file_get_contents($url);
	} else 
	{
	   $ch = @curl_init();
	   @curl_setopt($ch, CURLOPT_URL, $url);
	   @curl_setopt($ch, CURLOPT_HEADER, 0);
	   @curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	   @curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.7.5) Gecko/20041107 Firefox/1.0');
	   $content = @curl_exec($ch);
	   @curl_close($ch);
	}

	return $content;
}

function a_generate_smart_pagination($page = 1, $totalitems, $limit = 15, $adjacents = 1, $targetpage = "/", $pagestring = "&page=")
{		
	if(!$adjacents) $adjacents = 1;
	if(!$limit) $limit = 15;
	if(!$page) $page = 1;
	if(!$targetpage) $targetpage = "/";
	
		
	$prev = $page - 1;
	$next = $page + 1;
	$lastpage = ceil($totalitems / $limit);
	$lpm1 = $lastpage - 1;
	
	if(strpos($pagestring, 'page=', 0) === FALSE)
		$pagestring .= "&page=";
	
	$pagestring1 = preg_replace('/page=([0-9]*)/', 'page=1', $pagestring);
	$pagestring2 = preg_replace('/page=([0-9]*)/', 'page=2', $pagestring);
	$pagestringlpm1 = preg_replace('/page=([0-9]*)/', 'page='.$lpm1, $pagestring);
	$pagestringlast = preg_replace('/page=([0-9]*)/', 'page='.$lastpage, $pagestring);

	$pagination = "";
	if($lastpage > 1)
	{	
		$pagination .= "<ul";
		$pagination .= ">";

		//previous button
		if ($page > 1)
		{
			$url_query = preg_replace('/page=([0-9]*)/', 'page='.$prev, $pagestring); 
			$pagination .= "<li><a href=\"$targetpage?$url_query\">قبلی</a><li>";
		}
		else
			$pagination .= "<li class='disabled'><a href='#'>قبلی</a></li>";	
		
		//pages	
		if ($lastpage < 7 + ($adjacents * 2))	//not enough pages to bother breaking it up
		{	
			for ($counter = 1; $counter <= $lastpage; $counter++)
			{
				if ($counter == $page)
					$pagination .= "<li class=\"active\"><a href=\"#\">$counter</a></li>";
				else
				{
					$url_query = preg_replace('/page=([0-9]*)/', 'page='.$counter, $pagestring);
					$pagination .= "<li><a href=\"$targetpage?$url_query\">$counter</a></li>";
				}					
			}
		}
		elseif($lastpage >= 7 + ($adjacents * 2))	//enough pages to hide some
		{
			//close to beginning; only hide later pages
			if($page < 2 + ($adjacents * 2))
			{
				for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++)
				{					
					if ($counter == $page)
						$pagination .= "<li class=\"active\"><a href=\"#\">$counter</a></li>";
					else
					{
						$url_query = preg_replace('/page=([0-9]*)/', 'page='.$counter, $pagestring);
						$pagination .= "<li><a href=\"$targetpage?$url_query\">$counter</a></li>";	
					}				
				}
				$pagination .= "<li><a href='#'>...</a></li>";
				$pagination .= "<li><a href=\"$targetpage?$pagestringlpm1\">$lpm1</a></li>";
				$pagination .= "<li><a href=\"$targetpage?$pagestringlast\">$lastpage</a></li>";		
			}
			//in middle; hide some front and some back
			elseif($lastpage - ($adjacents * 2) > $page && $page > ($adjacents * 2))
			{	
				$pagination .= "<li><a href=\"$targetpage?$pagestring1\">1</a></li>";
				$pagination .= "<li><a href=\"$targetpage?$pagestring2\">2</a></li>";
				$pagination .= "<li><a href='#'>...</a></li>";
				for ($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++)
				{
					if ($counter == $page)
						$pagination .= "<li class=\"active\"><a href=\"#\">$counter</a></li>";
					else
					{
						$url_query = preg_replace('/page=([0-9]*)/', 'page='.$counter, $pagestring);
						$pagination .= "<li><a href=\"$targetpage?$url_query\">$counter</a></li>";
					}
				}
				$pagination .= "<li><a href='#'>...</a></li>";
				$pagination .= "<li><a href=\"$targetpage?$pagestringlpm1\">$lpm1</a></li>";
				$pagination .= "<li><a href=\"$targetpage?$pagestringlast\">$lastpage</a></li>";		
			}
			//close to end; only hide early pages
			else
			{
				$pagination .= "<li><a href=\"$targetpage?$pagestring1\">1</a></li>";
				$pagination .= "<li><a href=\"$targetpage?$pagestring2\">2</a></li>";
				$pagination .= "<li><a href='#'>...</a></li>";
				for ($counter = $lastpage - (1 + ($adjacents * 3)); $counter <= $lastpage; $counter++)
				{
					if ($counter == $page)
						$pagination .= "<li class=\"active\"><a href=\"#\">$counter</a></li>";
					else
					{
						$url_query = preg_replace('/page=([0-9]*)/', 'page='.$counter, $pagestring);
						$pagination .= "<li><a href=\"$targetpage?$url_query\">$counter</a></li>";
					}
				}
			}
		}
		
		//next button
		if ($page < $counter - 1) 
		{
			$url_query = preg_replace('/page=([0-9]*)/', 'page='.$next, $pagestring);
			$pagination .= "<li><a href=\"$targetpage?$url_query\">بعدی</a></li>";
		}
		else
			$pagination .= "<li class=\"disabled\"><a href='#'>بعدی</a></li>";
		$pagination .= "</ul>\n";
	}
	
	return $pagination;

}

function a_get_video_tags($uniq_id = '')
{
	$sql = "SELECT * FROM pm_tags WHERE uniq_id = '".$uniq_id."' ORDER BY tag_id ASC";
	$result = mysql_query($sql);
	$tags = array();
	while($row = mysql_fetch_assoc($result))
	{
		$tags[] = $row;
	}
	return $tags;
}

// Moved to /include/functions.php since version 2.0 

function a_fetch_video_sources($sort = '')
{
	if ($sort != '')
	{
		$sql = "SELECT * FROM pm_sources ORDER BY ". $sort ." DESC";
	}
	else
	{
		$sql = "SELECT * FROM pm_sources";
	}
	
	$result = mysql_query($sql);
	if(!$result)
		return false;
	$src = array();
	$id = 0;

	while($row = mysql_fetch_assoc($result))
	{
		if ($row['source_name'] == 'mp3')
		{
			$row['source_rule'] = '/(.*?)\.mp3/i';	
		}
		
		if ($row['source_name'] == 'other')
		{
			$row['source_rule'] = '/(.*?)\.(flv|mp4|mov|avi|divx|mp3|wmv|mkv|asf|wma|m4v|m4a|3gp|3g2)/i';
		}
		
		$src[ $row['source_id'] ] = $row;
	}
	foreach($src as $id => $source)
	{
		$src[$source['source_name']] = $source;
	}
	
	return array_reverse($src, true);
}


function is_url($url)
{
	$url_regex = "/^(((ht|f)tp(s?))\:\/\/)?(www\.|[a-zA-Z]+\.)*[a-zA-Z0-9\-\.]+\.([a-zA-Z]{2,6})(\:[0-9]+)*(\/($|[a-zA-Z0-9\.\,\;\?\'\\\+:&%\$#\=~_\-]+))*$/";
	if(preg_match($url_regex, $url))
		return true;

	return false;
}

function is_ip_url($url)
{
	$url_ip_regex = '/^(((ht|f)tp(s?))\:\/\/)?\b\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}\b/';
	if(preg_match($url_ip_regex, $url))
		return true;

	return false;
}

/**
 * @deprecated since 1.7
 * @return 
 */
function fetch_languages() 
{
	return array();
}

function a_list_cats_simple()
{
	$categories = load_categories();
	if (is_array($categories))
		return $categories;
	
	$query = mysql_query("SELECT * FROM pm_categories ORDER BY position ASC");
	$count = mysql_num_rows($query);
	if($count > 0)
	{
		while($r = mysql_fetch_assoc($query))
		{
			$categories[ $r['id'] ] = $r;
		}
	}
	mysql_free_result($query);
	return $categories;
}

// Moved function insert_new_video to /include/functions.php since version 2.0

function a_list_banned($from = 0, $limit = 20)
{
	global $banlist_nonce;
	
	$sql = "SELECT * FROM pm_banlist ORDER BY id DESC LIMIT ".$from.", ".$limit;
	$result = mysql_query($sql);
	if(!$result)
		return "در هنگام پردازش تاریخ خطایی رخ داد. <br /><strong>mysql برمی گرداند :</strong> ".mysql_error();
	$banlist = array();
	while($row = mysql_fetch_assoc($result))
	{
		$banlist[ $row['user_id'] ] = $row;
	}
	mysql_free_result($result);
	
	$total = count($banlist);
	if($total > 0)
	{
		$entries = '';
		$alt = 1;
		foreach($banlist as $user_id => $info)
		{
			$col = ($alt % 2) ? 'table_row1' : 'table_row2';
			$alt++;
			
			$sql = "SELECT username FROM pm_users WHERE id = '".$user_id."'";
			$result = mysql_query($sql);
			$username = mysql_fetch_assoc($result);
			mysql_free_result($result);
			
			if($info['reason'] == '')
				$info['reason'] = "None";
			$entries .= "
			  <tr class=\"".$col."\">
			    <td align=\"center\" style=\"text-align:center\">".$user_id."</td>
				<td><a href=\""._URL."/admin/edit_user_profile.php?uid=".$user_id."\">".$username['username']."</a></td>
				<td>".$info['reason']."</td>
				<td align=\"center\" class=\"table-col-action\" style=\"text-align:center\"><a href=\"banlist.php?a=delete&uid=".$user_id."&_pmnonce=". $banlist_nonce['_pmnonce'] ."&_pmnonce_t=". $banlist_nonce['_pmnonce_t'] ."\" rel=\"tooltip\" title=\"حذف از محرومیت\"><i class=\"icon-remove\"></i></a></td>
			  </tr>";
		}
	} 
	elseif($count == 0) 
	{
		$entries .= "
		  <tr>
			<td colspan=\"4\" align=\"center\">رکوردی پیدا نشد.</td>
		  </tr>";
	}
	return $entries;
}

function is_user_banned($user_id)
{
	$sql = "SELECT COUNT(*) as total_found 
			FROM pm_banlist 
			WHERE user_id = '". $user_id ."'";
	$result = @mysql_query($sql);
	if ( ! $result)
	{
		return false;
	}
	$row = mysql_fetch_assoc($result);
	mysql_free_result($result);
	
	if ($row['total_found'] > 0)
	{
		return true;
	}
	
	return false;
}

function get_banlist()
{
	$banlist = array();
	
	$sql = "SELECT * 
			FROM pm_banlist";
	$result = @mysql_query($sql);
	if ( ! $result)
	{
		return array();
	}
	
	while ($row = mysql_fetch_assoc($result))
	{
		$banlist[ $row['user_id'] ] = $row;
	}
	mysql_free_result($result);
	
	return $banlist;
}

if (!function_exists('json_encode'))
{
  function json_encode($a = false)
  {
    if (is_null($a)) return 'null';
    if ($a === false) return 'false';
    if ($a === true) return 'true';
    if (is_scalar($a))
    {
      if (is_float($a))
      {
        // Always use "." for floats.
        return floatval(str_replace(",", ".", strval($a)));
      }

      if (is_string($a))
      {
        static $jsonReplaces = array(array("\\", "/", "\n", "\t", "\r", "\b", "\f", '"'), array('\\\\', '\\/', '\\n', '\\t', '\\r', '\\b', '\\f', '\"'));
        return '"' . str_replace($jsonReplaces[0], $jsonReplaces[1], $a) . '"';
      }
      else
        return $a;
    }
    $isList = true;
    for ($i = 0, reset($a); $i < count($a); $i++, next($a))
    {
      if (key($a) !== $i)
      {
        $isList = false;
        break;
      }
    }
    $result = array();
    if ($isList)
    {
      foreach ($a as $v) $result[] = json_encode($v);
      return '[' . join(',', $result) . ']';
    }
    else
    {
      foreach ($a as $k => $v) $result[] = json_encode($k).':'.json_encode($v);
      return '{' . join(',', $result) . '}';
    }
  }
}

function cache_this($type, $signature) {

	$cacheFile = './temp/'. md5($signature) .'-'. date('Ym');
	$cacheTime = 24 * 3600;
	$now = time();
	$last_update = 0;
	if ($file_exists = file_exists($cacheFile))
	{
		$last_update = filemtime($cacheFile);
	}
	
	// Serve the cached content if present
	if ($file_exists &&  ($now - $cacheTime) < $last_update) 
	{
		return file_get_contents($cacheFile);
	}

	$date = getdate();
	$last_mo = mktime(0, 0, 0, $date['mon']-1, 1, $date['year']);
	
	$prev_cache = './temp/'. md5($signature) .'-'. date('Ym', $last_mo);
	if (file_exists($prev_cache))
	{
		unlink($prev_cache);
	}

	// Cache the contents to a file
	$cached = @fopen($cacheFile, 'w');
	if ($type == 'read_version')
	{
		$content = read_version();
	}
	else if ($type == 'get_rss_news')
	{
		$content = get_rss_news(5);
	}
	else if ($type == 'get_theme_store_data')
	{
		$content = get_theme_store_data();
	}
	@fwrite($cached, $content, strlen($content));
	@fclose($cached);
	return $content;
}

function get_true_max_filesize()
{
	$upload_max_filesize = return_bytes(ini_get('upload_max_filesize'));
	$post_max_size = return_bytes(ini_get('post_max_size'));
	// uploads shouldn't exceed the size limit of the total post
	// $post_max_size = round((100 * $post_max_size) / 100, 0); // Commented since v2.0; confuses customers when we don't show the correct size
	
	$max_size = 0;
	$max_size = ($upload_max_filesize < $post_max_size) ? $upload_max_filesize : $post_max_size;
	
	return $max_size;
}

function mass_delete_videos($uniq_ids = array())
{
	$delete_ids_str = '';
	$total_videos = count($uniq_ids);
	
	if ($total_videos > 0)
	{
		if ($total_videos > 20)
		{
			$start  = 0;
			$inc	= 15;

			while ($start <= $total_videos)
			{	
				$delete_ids_str = '';
				$i = 0;
				
				for ($i = $start; $i < $start + $inc; $i++)
				{
					$delete_ids_str .= "'". $uniq_ids[$i] ."', ";
				}

				$delete_ids_str = substr($delete_ids_str, 0, -2);
			
				if (strlen($delete_ids_str) > 2)
				{
					@mysql_query("DELETE FROM pm_videos		 WHERE uniq_id  IN (". $delete_ids_str .")");
					@mysql_query("DELETE FROM pm_comments 	 WHERE uniq_id  IN (". $delete_ids_str .")");
					@mysql_query("DELETE FROM pm_reports 	 WHERE entry_id IN (". $delete_ids_str .")");
					@mysql_query("DELETE FROM pm_videos_urls WHERE uniq_id  IN (". $delete_ids_str .")");
					@mysql_query("DELETE FROM pm_favorites 	 WHERE uniq_id  IN (". $delete_ids_str .")");
					@mysql_query("DELETE FROM pm_chart 		 WHERE uniq_id  IN (". $delete_ids_str .")");
					@mysql_query("DELETE FROM pm_tags 		 WHERE uniq_id  IN (". $delete_ids_str .")");
					@mysql_query("DELETE FROM pm_embed_code  WHERE uniq_id  IN (". $delete_ids_str .")");
					@mysql_query("DELETE FROM pm_bin_rating_meta  WHERE uniq_id IN (". $delete_ids_str .")");
					@mysql_query("DELETE FROM pm_bin_rating_votes  WHERE uniq_id IN (". $delete_ids_str .")");
				}
				$start = $start + $inc;
			}
		}
		else
		{
			$delete_ids_str = '';
			foreach ($uniq_ids as $k => $uniq_id)
			{
				$delete_ids_str .= "'". $uniq_id ."', ";
			}
			$delete_ids_str = substr($delete_ids_str, 0, -2);
			
			@mysql_query("DELETE FROM pm_videos		 WHERE uniq_id  IN (". $delete_ids_str .")");
			@mysql_query("DELETE FROM pm_comments 	 WHERE uniq_id  IN (". $delete_ids_str .")");
			@mysql_query("DELETE FROM pm_reports 	 WHERE entry_id IN (". $delete_ids_str .")");
			@mysql_query("DELETE FROM pm_videos_urls WHERE uniq_id  IN (". $delete_ids_str .")");
			@mysql_query("DELETE FROM pm_favorites 	 WHERE uniq_id  IN (". $delete_ids_str .")");
			@mysql_query("DELETE FROM pm_chart 		 WHERE uniq_id  IN (". $delete_ids_str .")");
			@mysql_query("DELETE FROM pm_tags 		 WHERE uniq_id  IN (". $delete_ids_str .")");
			@mysql_query("DELETE FROM pm_embed_code  WHERE uniq_id  IN (". $delete_ids_str .")");
			@mysql_query("DELETE FROM pm_bin_rating_meta  WHERE uniq_id IN (". $delete_ids_str .")");
			@mysql_query("DELETE FROM pm_bin_rating_votes  WHERE uniq_id IN (". $delete_ids_str .")");
		}
		return true;
	}
	
	return false;
}

function add_config($name, $value)
{
	global $config;
	
	if (array_key_exists($name, $config))
	{
		update_config($name, $value, true);
		return true;
	}
	$value = trim($value);
	$value = secure_sql($value);
	$name = secure_sql($name);
	
	$sql = "INSERT INTO pm_config (name, value) 
			VALUES ('". $name ."', '". $value ."')";
	$result = mysql_query($sql);
	if ( ! $result)
	{
		return array(mysql_error(), mysql_errno());
	}
	
	$config[$name] = $value;
	
	return true;
}

function autosync($force = false)
{
	global $config;
	$now = time();
	
	$config['last_autosync'] = (int) $config['last_autosync'];
	
	if (($config['last_autosync'] < ($now - 2592000)) || $force === true) 
	{
		@ini_set('max_execution_time', 180);
		
		// Total videos
		$total = 0;
		$query = "SELECT COUNT(*) as total 
				  FROM pm_videos
				  WHERE added <= '". $now ."'";
		$result =  mysql_query($query);
		$total = mysql_fetch_assoc($result);
		mysql_free_result($result);
		
		$sql[] = "UPDATE pm_config SET value='". $total['total'] ."' WHERE name = 'published_videos'";
		
		$total = 0;
		$query = "SELECT COUNT(*) as total 
				  FROM pm_videos";
		$result =  mysql_query($query);
		$total = mysql_fetch_assoc($result);
		mysql_free_result($result);
		
		$sql[] = "UPDATE pm_config SET value='". $total['total'] ."' WHERE name = 'total_videos'";
		
		
		$categories = a_list_cats_simple();
		
		if ($total['total'] > 0 && count($categories) > 0)
		{
			// Count total videos for each category	
			$k = 1;
			foreach ($categories as $cid => $arr)
			{
				$total = 0;
				$query = "SELECT COUNT(*) as total 
							FROM pm_videos 
							WHERE category LIKE '". $cid ."' 
							   OR category LIKE '". $cid .",%' 
							   OR category LIKE '%,". $cid ."' 
							   OR category LIKE '%,". $cid .",%'";
	
				$result =  mysql_query($query);
				$total = mysql_fetch_assoc($result);
				mysql_free_result($result);
	
				$sql[] = "UPDATE pm_categories SET total_videos = '". $total['total'] ."' WHERE id = '". $cid ."'";
				
				$total = 0;
				$query = "SELECT COUNT(*) as total 
							FROM pm_videos 
							WHERE added <= '". $now ."' 
							  AND (category LIKE '". $cid ."' 
							   OR category LIKE '". $cid .",%' 
							   OR category LIKE '%,". $cid ."' 
							   OR category LIKE '%,". $cid .",%')";
	
				$result =  mysql_query($query);
				$total = mysql_fetch_assoc($result);
				mysql_free_result($result);
	
				$sql[] = "UPDATE pm_categories SET published_videos = '". $total['total'] ."' WHERE id = '". $cid ."'";
				
				if ($k % 3 == 0)
				{
					sleep(1);
				}
				
				$k++;
			}
		}
		
		// Total articles
		if ($config['mod_article'])
		{
			$total = 0;
			$query = "SELECT COUNT(*) as total 
					  FROM art_articles
					  WHERE date <= '". $now ."'";
			$result =  mysql_query($query);
			$total = mysql_fetch_assoc($result);
			mysql_free_result($result);
			
			$sql[] = "UPDATE pm_config SET value='". $total['total'] ."' WHERE name = 'published_articles'";
			
			$total = 0;
			$query = "SELECT COUNT(*) as total 
					  FROM art_articles";
			$result =  mysql_query($query);
			$total = mysql_fetch_assoc($result);
			mysql_free_result($result);
			
			$sql[] = "UPDATE pm_config SET value='". $total['total'] ."' WHERE name = 'total_articles'";
			
			// Count total articles for each category
			$categories = art_get_categories();
			
			$k = 0;
			foreach ($categories as $cid => $arr)
			{
				$total = 0;
				$query = "SELECT COUNT(*) as total 
							FROM art_articles 
							WHERE category LIKE '". $cid ."' 
							   OR category LIKE '". $cid .",%' 
							   OR category LIKE '%,". $cid ."' 
							   OR category LIKE '%,". $cid .",%'";
		
				$result =  mysql_query($query);
				$total = mysql_fetch_assoc($result);
				mysql_free_result($result);
		
				$sql[] = "UPDATE art_categories SET total_articles = '". $total['total'] ."' WHERE id = '". $cid ."'";
				
				$total = 0;
				$query = "SELECT COUNT(*) as total 
							FROM art_articles 
							WHERE date <= '". $now ."'  
							  AND (category LIKE '". $cid ."' 
							   OR category LIKE '". $cid .",%' 
							   OR category LIKE '%,". $cid ."' 
							   OR category LIKE '%,". $cid .",%')";
		
				$result =  mysql_query($query);
				$total = mysql_fetch_assoc($result);
				mysql_free_result($result);
		
				$sql[] = "UPDATE art_categories SET published_articles = '". $total['total'] ."' WHERE id = '". $cid ."'";
				
				if ($k % 3 == 0)
				{
					sleep(1);
				}
				$k++;
			}
		}
		
		$sql[] = "UPDATE pm_config SET value='". $now ."' WHERE name='last_autosync'";
		
		// Total pages
		$total = 0;
		$query = "SELECT COUNT(*) as total 
				  FROM pm_pages";
		$result =  @mysql_query($query);
		$total = @mysql_fetch_assoc($result);
		@mysql_free_result($result);
		
		$sql[] = "UPDATE pm_config SET value='". $total['total'] ."' WHERE name = 'total_pages'";
		
		$total = count($sql);
		$errors = array();
		
		for($i = 0; $i < $total; $i++)
		{
			$result = @mysql_query($sql[ $i ]);
			if(!$result)
			{
				$errors[] = mysql_error();
			}
		}
		
		if (count($errors) > 0)
		{
			return false;
		}
	}
	
	return true;
}

function restricted_access($exit = true)
{
	echo '
	<div id="adminPrimary">
	<div class="content">
	<h2>دستیابی محدود شده</h2>
	<div class="row-fluid">
	<div class="alert alert-warning">
 		متاسفانه شما به این ناحیه دسترسی ندارید.
	</div>
	<hr />
	<a href="index.php" class="btn">&larr; داشبورد</a>
	</div></div></div>';
	include('footer.php');
	if ($exit) exit();	
	return true;
}
function dropdown_jwskins() {

$path = ABSPATH ."/skins";
$dh = opendir($path);
$form_file = '';
while (($file = readdir($dh)) !== false) {
    if($file != "." && $file != ".."  && $file != "..") {
		if (strpos(strtolower($file), ".zip"))
			$form_file .= "<option value=\"".$file."\">".ucfirst(trim($file, ".zip"))."</option> \n";
    }
}
closedir($dh);
return $form_file;
}

function show_form_item_date($timestamp = 0) 
{
	if ( ! $timestamp)
		$timestamp = time();
	
	$months = array(1 => 'Jan',
					2 => 'Feb',
					3 => 'Mar',
					4 => 'Apr',
					5 => 'May',
					6 => 'Jun',
					7 => 'Jul',
					8 => 'Aug',
					9 => 'Sep',
					10 => 'Oct',
					11 => 'Nov',
					12 => 'Dec' 
				);	
	
	$sel_mon = date('n', $timestamp);
	$sel_day = date('d', $timestamp);
	$sel_year = date('Y', $timestamp);
	$sel_hour = date('h', $timestamp);
	$sel_min = date('i', $timestamp);
	$sel_sec = date('s', $timestamp);
	$sel_ampm = date('A', $timestamp);
	
	$return = '';
	
	$return .= '<select name="date_month" class="pubDate">' . "\n";
	for ($i = 1; $i <= 12; $i++)
	{
		$selected = ($i == $sel_mon) ? 'selected="selected"' : '';
		$return .= '<option value="'. $i .'" '. $selected .'>'. $months[$i] .'</option>' . "\n";
	}
	$return .= '</select>' . "\n";
	
	$return .= '<input type="text" name="date_day" value="'. $sel_day .'" size="2" maxlength="2" class="pubDate" /> ' . "\n";
	$return .= ' , ';
	$return .= '<input type="text" name="date_year" value="'. $sel_year .'" size="4" maxlength="4" class="pubDate" /> ' . "\n";
	$return .= ' @ ';
	$return .= '<input type="text" name="date_hour" value="'. $sel_hour .'"  size="2" maxlength="2" class="pubDate" /> : ' . "\n";
	$return .= '<input type="text" name="date_min" value="'. $sel_min .'" size="2" maxlength="2" class="pubDate" />' . "\n";
	$return .= '<select name="date_ampm" class="pubDate">' . "\n";
	$return .= ' <option value="am"';
	$return .= ($sel_ampm == 'AM') ? ' selected="selected" ' : '';
	$return .= '>AM</option>';
	$return .= ' <option value="pm"';
	$return .= ($sel_ampm == 'PM') ? ' selected="selected" ' : '';
	$return .= '>PM</option>';
	$return .= '</select>' . "\n";
		
	$return .= '<input type="hidden" name="date_sec" value="'. $sel_sec .'" size="2" maxlength="2" class="pubDate" />' . "\n";
	
	// explain
	$return .= "\n\n";
	$return .= '';
	return $return;
}

function validate_item_date($post)
{
	$mon = (int) $post['date_month'];
	$day = (int) $post['date_day'];
	$year = (int) $post['date_year'];
	$hour = (int) $post['date_hour'];
	$min = (int) $post['date_min'];
	$sec = (int) $post['date_sec'];
	$ampm = strtoupper($post['date_ampm']);
	
	if (($mon > 12 || $mon < 1) || ($day > 31 || $day < 1) || ($year < 1970 || $year > 9999) || ($hour > 12 || $hour < 0) || ($min > 60 || $min < 0) || ($sec > 60 || $sec < 0))
	{
		return false;
	}
	
	$days_in_month = date('t', $mm = mktime(1, 0, 0, $mon, 1, $year));
	 
	// the user meant the last day of the month for sure. autofix if mistake was made
	if ($day > $days_in_month)
	{
		$day = $days_in_month;
	}
	
	if ($ampm == 'AM')
	{
		if ($hour == 12)
		{
			$hour = 0;
		}
	}
	
	if ($ampm == 'PM')
	{
		$hour += 12;
		if ($hour == 24)
		{
			$hour = 12;
		}
	}
	
	return array('date_month' => $mon, 
				 'date_day' => $day, 
				 'date_year' => $year, 
				 'date_hour' => $hour, 
				 'date_min' => $min,
				 'date_sec' => $sec,
				 'date_ampm' => $ampm);
}

// wrapper for mktime() - uses data from $_POST
function pm_mktime($post = array())
{
	return mktime((int) $post['date_hour'], (int) $post['date_min'], (int) $post['date_sec'], (int) $post['date_month'], (int) $post['date_day'], (int) $post['date_year']);
}

function array_sort($array, $on, $order=SORT_ASC)
{
	$new_array = array();
	$sortable_array = array();

	if (count($array) > 0) {
		foreach ($array as $k => $v) {
			if (is_array($v)) {
				foreach ($v as $k2 => $v2) {
					if ($k2 == $on) {
						$sortable_array[$k] = $v2;
					}
				}
			} else {
				$sortable_array[$k] = $v;
			}
		}

		switch ($order) {
			case SORT_ASC:
				asort($sortable_array);
			break;
			case SORT_DESC:
				arsort($sortable_array);
			break;
		}

		foreach ($sortable_array as $k => $v) {
			$new_array[$k] = $array[$k];
		}
	}

	return $new_array;
}

function create_preroll_ad($ad_data)
{	
	$defaults = array('name' => '',
					  'duration' => 30,
					  'user_group' => 0, // 0 = everyone; 1 = logged only; 2 = guests only
					  'impressions' => 0,
					  'status' => 1,
					  'code' => '',
					  'skip' => 0,
					  'skip_delay_seconds' => 5,
					  'ignore_category' => array(),
					  'ignore_source' => array(),
					  'disable_stats' => 0,
					);
	
	$ad_data['duration'] = abs((int) $ad_data['duration']);
	$ad_data['skip'] = abs((int) $ad_data['skip_delay_radio']);
	$ad_data['skip_delay_seconds'] = abs((int) $ad_data['skip_delay_seconds']);
	$ad_data['disable_stats'] = abs((int) $ad_data['disable_stats']);

	$ad_data = array_merge($defaults, $ad_data);
	
	if ($ad_data['name'] == '')
	{
		$ad_data['name'] = date('F j, Y g:i A');
	}
	
	$ad_data['duration'] = (int) $ad_data['duration'];
	if ($ad_data['duration'] == 0)
	{
		$ad_data['duration'] = 30;
	}
	
	$ad_options = array();
	
	$options = array('skip' => (int) $ad_data['skip_delay_radio'],
					 'skip_delay_seconds' => (int) $ad_data['skip_delay_seconds'],
					 'ignore_category' => (array) $ad_data['ignore_category'],
					 'ignore_source' => (array) $ad_data['ignore_source'],
					 'disable_stats' => (int) $ad_data['disable_stats']
					);
	$options = serialize($options);
	
	$sql = "INSERT INTO pm_preroll_ads 
					(name, duration, user_group, impressions, status, code, options)
			VALUES ('". secure_sql(trim($ad_data['name'])) ."',
					'". $ad_data['duration'] ."',
					'". $ad_data['user_group'] ."',
					'0',
					'". $ad_data['status'] ."',
					'". secure_sql($ad_data['code']) ."',
					'". secure_sql($options) ."')";
	
	if ( ! $result = mysql_query($sql))
	{
		return false;
	}
	return true;
}

function update_preroll_ad($ad_id, $ad_data)
{
	$ad_id = (int) $ad_id;
	
	if ( ! $ad_id)
		return false;

	$ad_data['name'] = trim($ad_data['name']);
	$ad_data['duration'] = abs( (int) $ad_data['duration']);
	$ad_data['user_group'] = (int) $ad_data['user_group'];
	$ad_data['skip_delay_seconds'] = abs( (int) $ad_data['skip_delay_seconds']);
	
	if ($ad_data['duration'] > 0 && $ad_data['duration'] < $ad_data['skip_delay_seconds'])
	{
		$ad_data['skip_delay_seconds'] = $ad_data['duration'] - 1;
	}
	
	$options = array('skip' => (int) $ad_data['skip_delay_radio'],
					 'skip_delay_seconds' => (int) $ad_data['skip_delay_seconds'],
					 'ignore_category' => (array) $ad_data['ignore_category'],
					 'ignore_source' => (array) $ad_data['ignore_source'],
					 'disable_stats' => (int) $ad_data['disable_stats']
					);
	$options = serialize($options);
	
	$sql = "UPDATE pm_preroll_ads 
			SET name = '". secure_sql($ad_data['name']) ."', 
				duration = ". $ad_data['duration'] .",
				user_group = ". $ad_data['user_group'] .", 
				status = '". $ad_data['status'] ."',
				code = '". secure_sql($ad_data['code']) ."',
				options = '". secure_sql($options) ."'
			WHERE id = $ad_id";

	return mysql_query($sql);
}

function delete_preroll_ad($ad_id)
{
	$ad_id = (int) $ad_id;
	
	if ( ! $ad_id)
		return false;
	
	$sql = "DELETE FROM pm_preroll_ads 
			WHERE id = '". $ad_id ."'";

	return mysql_query($sql);
}

function get_theme_store_data()
{
	$data = array();
	$rss = new lastRSS;
	$rssurl = "http://feeds.feedburner.com/PMThemes";

	if ( ! $data = $rss->get($rssurl))
	{
		$data = array();
	}
	
	return serialize($data);
}
function detect_russian($text) {
    return preg_match('/[А-Яа-яЁё]/u', $text);
}

function admin_custom_fields_row($meta_id, $meta)
{
	if (is_meta_key_reserved($meta['meta_key']))
		return '';
	$html = '
	<div id="meta-row-'. $meta_id .'">
		<span id="update-response-'. $meta_id .'"></span>
		<div class="row-fluid">
			<div class="span3">
				<input type="text" name="meta['. $meta_id .'][key]" value="'. htmlspecialchars($meta['meta_key'], ENT_QUOTES) .'" maxlength="255" class="span12 normal-input" placeholder="Custom name" />
			</div>
			<div class="span9">
				<input type="text" name="meta['. $meta_id .'][value]" value="'. $meta['meta_value'] .'" class="span12 normal-input" id="meta_value_field" />
			</div>
		</div>
		<button name="update_meta_btn" id="update_meta_btn_'. $meta_id .'" value="Update" class="btn btn-mini btn-normal">بروزرسانی</button>
		<button name="delete_meta_btn" id="delete_meta_btn_'. $meta_id .'" value="Delete" class="btn btn-mini btn-link">حذف</button>
	<hr />
	</div>';
	
	return $html;
}

function admin_custom_fields_add_form($item_id, $item_type)
{
	$select_html = '';
	$item_type = (int) $item_type;
	
	$sql_where = ($item_type)  ? " WHERE item_type = $item_type " : '';
	
	$sql = "SELECT meta_key 
			FROM pm_meta 
			$sql_where
			GROUP BY meta_key
			HAVING meta_key NOT LIKE '\_%' 
			ORDER BY meta_key ASC
			LIMIT 30";
	
	$keys = array();
	
	if ( $result = mysql_query($sql))
	{
		while ($row = mysql_fetch_assoc($result))
		{
			$keys[] = $row['meta_key'];
		}
		
		mysql_free_result($result);
		
		if (count($keys) > 0)
		{
			$select_html = '<select id="meta_key_select" name="meta_key_select" class="span12">';
			$select_html .= "\n\t";
			$select_html .= '<option value="_nokey">فیلد موجود را انتخابکنید...</option>';
			foreach ($keys as $k => $key)
			{
				$select_html .= "\n\t";
				$select_html .= '<option value="'. htmlspecialchars($key, ENT_QUOTES) .'">'. $key .'</option>';
			}
			$select_html .= "\n";
			$select_html .= '</select>';
		}
	}
	
	$html = '
	<div id="new-meta-placeholder"></div>
	<div class="well well-small">
		<h5>اضافه کردن فیلد سفارشی جدید</h5>
		<div id="new-meta-error"></div>
		<div class="row-fluid">
		<div class="span3">';
		
	if ($select_html != '')
	{
		$html .= $select_html;
		$html .= '<input type="text" name="meta_key" value="" maxlength="255" class="span12 normal-input hide" placeholder="Custom name" />';
	}
	else
	{
		$html .= '<input type="text" name="meta_key" value="" maxlength="255" class="span12 normal-input" placeholder="Custom name" />';
	}
	
	$html .= '</div>
		<div class="span9">
		<input type="text" name="meta_value" class="span12 normal-input" value="" placeholder="Custom value" />
		<input type="hidden" name="meta_item_id" value="'. $item_id .'" />
		<input type="hidden" name="meta_item_type" value="'. $item_type .'" />
		</div>';
	
	if ($select_html != '')
	{
		$html .= '<small><strong><a href="#" id="meta_switch_input_select" class="hide">فیلد موجود را انتخاب کن</a></strong></small>';
		$html .= '<small><strong><a href="#" id="meta_switch_select_input">+ اضافه کردن فیلد جدید</a></strong></small>';
		$html .= '<br /><br />';
	}
	
	$html .= '<button name="add_meta_btn" id="add_meta_btn" value="Update" class="btn btn-small" />اضافه کردن فیلد سفارشی</button>
		
		</div>
	</div>';

	return $html;
}
?>