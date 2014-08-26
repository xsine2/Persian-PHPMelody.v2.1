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

// Update comment?
if ('' != $_POST['update'])
{
	session_start();
	require('../config.php');
	include_once(ABSPATH . 'include/user_functions.php');
	include_once(ABSPATH . 'include/islogged.php');
	include(ABSPATH .'admin/functions.php');
	
	$response = array('success' => false, 'msg' => '');
	
	if ( ! $logged_in )
	{
		$response['msg'] = 'لطفا اول وارد شوید.';
		echo json_encode($response);
		exit();
	}
	if ( ! is_admin() || (is_moderator() && ! mod_can('manage_comments')))
	{
		$response['msg'] = 'متاسفانه شما نباید به این ناحیه دسترسی داشته باشید.';
		echo json_encode($response);
		exit();
	}
	
	$comment_id = (int) $_POST['comment_id'];
	if ($comment_id)
	{
		$comment = trim($_POST['comment_txt']);
		$comment = nl2br($comment);
		$comment = secure_sql($comment);
		
		$sql = "UPDATE pm_comments 
				SET comment = '". $comment ."' 
				WHERE id = '". $comment_id ."'";
		if ( ! $result = mysql_query($sql))
		{
			$response['msg'] = ' ویرایش نظر از دست رفت. گزارش mysql : '. mysql_error();
		}
		else
		{
			$response['success'] = true;
		}
	}
	
	echo json_encode($response);
	exit();
}


$filter = '';
$filters = array('articles', 'videos', 'flagged', 'pending');

if(in_array(strtolower($_GET['filter']), $filters) !== false)
{
	$filter = strtolower($_GET['filter']);
}

if($filter == 'videos') { $showsm = '51'; }
if($filter == 'articles') { $showsm = '52'; }
if($filter == 'flagged') { $showsm = '53'; }
if($filter == 'pending') { $showsm = '54'; }

$showm = '5';
$load_scrolltofixed = 1;
$_page_title = 'نظرات';
include('header.php');

$vid 		= trim($_GET['vid']);
$action 	= $_GET['a'];
$comment_id = (int) trim($_GET['cid']);
$page 		= $_GET['page'];

$filter = '';
$filters = array('articles', 'videos', 'flagged', 'pending');

if(in_array(strtolower($_GET['filter']), $filters) !== false)
{
	$filter = strtolower($_GET['filter']);
}

if($page == 0)
	$page = 1;

$limit 		= 20;	//	comments per page
$from 		= $page * $limit - ($limit);


//	Batch Delete/Approve Comments/Remove flag
if (($_POST['Submit'] == 'Delete checked' || $_POST['Submit'] == 'Approve checked' || $_POST['Submit'] == 'Remove flag') &&  ! csrfguard_check_referer('_admin_comments'))
{	
	$info_msg = '<div class="alert alert-error">رمز عبور اشتباه است یا سشن منقضی شده. لطفا صفحه را رفرش نمایید و از دوباره تلاش کنید.</div>';
}
else if($_POST['Submit'] == 'Delete checked' || $_POST['Submit'] == 'Approve checked' || $_POST['Submit'] == 'Remove flag')
{
	$video_ids = array();
	$video_ids = $_POST['video_ids'];
	
	$total_ids = count($video_ids);
	if($total_ids > 0)
	{
		$in_arr = '';
		for($i = 0; $i < $total_ids; $i++)
		{
			$in_arr .= $video_ids[ $i ] . ", ";
		}
		$in_arr = substr($in_arr, 0, -2);
		if(strlen($in_arr) > 0)
		{
			if($_POST['Submit'] == 'Approve checked')
			{
				$sql = "UPDATE pm_comments SET approved = '1' WHERE id IN (" . $in_arr . ")";
				$result = @mysql_query($sql);
	
				if(!$result)
					$info_msg = '<div class="alert alert-error">در هنگام بروزرسانی دیتابیس تان خطایی رخ داد است.<br /> mysql بر می گرداند : '.mysql_error().'</div>';
				else
					$info_msg = '<div class="alert alert-success">نظرات انتخاب شده تایید شدند.</div>';
				
				if (_MOD_SOCIAL)
				{
					$sql = "SELECT id, uniq_id, user_id 
							FROM pm_comments WHERE id IN (" . $in_arr . ")";
					$result = mysql_query($sql);
					while ($row = mysql_fetch_assoc($result))
					{
						if (strpos($row['uniq_id'], 'article-') !== false)
						{
							$tmp_parts = explode('-', $row['uniq_id']);
							$id = array_pop($tmp_parts);
							$article = get_article($id);
							log_activity(array(
									'user_id' => $row['user_id'],
									'activity_type' => ACT_TYPE_COMMENT,
									'object_id' => $row['id'],
									'object_type' => ACT_OBJ_COMMENT,
									'object_data' => array(),
									'target_id' => $id,
									'target_type' => ACT_OBJ_ARTICLE,
									'target_data' => $article
									)
								);
						}
						else
						{
							$video = request_video($row['uniq_id']);
							log_activity(array(
									'user_id' => $row['user_id'],
									'activity_type' => ACT_TYPE_COMMENT,
									'object_id' => $row['id'],
									'object_type' => ACT_OBJ_COMMENT,
									'object_data' => array(),
									'target_id' => $video['id'],
									'target_type' => ACT_OBJ_VIDEO,
									'target_data' => $video
									)
								);
						}
					}
				}
			}
			else if ($_POST['Submit'] == 'Remove flag')
			{
				$sql = "UPDATE pm_comments SET report_count = '0' WHERE id IN (" . $in_arr . ")";
				$result = @mysql_query($sql);
				
				if ( ! $result)
				{
					$info_msg = '<div class="alert alert-error">در هنگام بروزرسانی دیتابیس تان خطایی رخ داده.<br />mysql بر می گرداند : '.mysql_error().'</div>';
				}
				else
				{
					@mysql_query("DELETE FROM pm_comments_reported WHERE comment_id IN (" . $in_arr . ")");
					$info_msg = '<div class="alert alert-success">پرچم های انتخاب شده حذف شدند.</div>';
				}
			}
			else
			{
				if (_MOD_SOCIAL)
				{
					$sql = "SELECT id, uniq_id, user_id 
							FROM pm_comments WHERE id IN (" . $in_arr . ")";
					if ($result = mysql_query($sql))
					{
						while (	$row = mysql_fetch_assoc($result))
						{
							$sql = "DELETE FROM pm_activity 
									WHERE user_id = '". $row['user_id'] ."' 
									  AND activity_type = '". ACT_TYPE_COMMENT ."'
									  AND object_id = '". $row['id'] ."' 
									  AND object_type = '". ACT_OBJ_COMMENT ."'";
							@mysql_query($sql);
						}
						mysql_free_result($result);
					}
				}
				
				$sql = "DELETE FROM pm_comments WHERE id IN (" . $in_arr . ")";
				$result = @mysql_query($sql);
				
				if(!$result)
				{
					$info_msg = '<div class="alert alert-error">در هنگام بروزرسانی دیتابیس تان خطایی رخ داده.<br />mysql بر می گرداند : '.mysql_error().'</div>';
				}
				else
				{
					// remove reports
					$sql = "DELETE FROM pm_comments_reported WHERE comment_id IN (" . $in_arr . ")";
					$result = @mysql_query($sql);
					
					$in_arr = '';
					for($i = 0; $i < $total_ids; $i++)
					{
						if ($video_ids[ $i ] > 0)
						{
							$in_arr .= "'com-". $video_ids[ $i ] . "', ";
						}
					}
					$in_arr = substr($in_arr, 0, -2);
					
					// remove likes/dislikes
					$sql = "DELETE FROM pm_bin_rating_votes WHERE uniq_id IN (". $in_arr .")";
					$result = @mysql_query($sql);
					
					$info_msg = '<div class="alert alert-success">نظرات انتخاب شده حذف شدند.</div>';
				}
			}
		}
	}
	else
		$info_msg = '<div class="alert alert-error">لطفا ابتدا چیزی را انتخاب کنید.</div>';
}

switch($action)
{
	case 1:
		if (csrfguard_check_referer('_admin_comments'))
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
			@mysql_query("DELETE FROM pm_comments WHERE id = '".$comment_id."'");
			@mysql_query("DELETE FROM pm_comments_reported WHERE comment_id = '".$comment_id."'");
			@mysql_query("DELETE FROM pm_bin_rating_votes WHERE uniq_id = 'com-".$comment_id."'");
			$info_msg = '<div class="alert alert-info">Comment(s) deleted.</div>';
		}
		else
		{
			$info_msg = '<div class="alert alert-error">رمز عبور اشتباه است یا سشن منقضی شده. لطفا صفحه را رفرش نمایید و از دوباره تلاش کنید.0</div>';
		}
	break;
	case 2:
		if (csrfguard_check_referer('_admin_comments'))
		{
			@mysql_query("UPDATE pm_comments SET approved='1' WHERE id = '".$comment_id."'");
			
			if (_MOD_SOCIAL)
			{
				$sql = "SELECT id, uniq_id, user_id 
						FROM pm_comments WHERE id = '" . $comment_id . "'";
				$result = mysql_query($sql);
				$row = mysql_fetch_assoc($result);
				if (strpos($row['uniq_id'], 'article-') !== false)
				{
					$tmp_parts = explode('-', $row['uniq_id']);
					$id = array_pop($tmp_parts);
					$article = get_article($id);
					log_activity(array(
							'user_id' => $row['user_id'],
							'activity_type' => ACT_TYPE_COMMENT,
							'object_id' => $row['id'],
							'object_type' => ACT_OBJ_COMMENT,
							'object_data' => array(),
							'target_id' => $id,
							'target_type' => ACT_OBJ_ARTICLE,
							'target_data' => $article
							)
						);
				}
				else
				{
					$video = request_video($row['uniq_id']);
					log_activity(array(
							'user_id' => $row['user_id'],
							'activity_type' => ACT_TYPE_COMMENT,
							'object_id' => $row['id'],
							'object_type' => ACT_OBJ_COMMENT,
							'object_data' => array(),
							'target_id' => $video['id'],
							'target_type' => ACT_OBJ_VIDEO,
							'target_data' => $video
							)
						);
				}
			}
			$info_msg = '<div class="alert alert-success">نظر(ها) تایید شد.</div>';
		}
		else
		{
			$info_msg = '<div class="alert alert-error">رمز عبور اشتباه است یا سشن منقضی شده. لطفا صفحه را رفرش نمایید و از دوباره تلاش کنید.</div>';
		}
	break;
}

$comments_nonce = csrfguard_raw('_admin_comments');

//	Search
if(!empty($_GET['submit']) || !empty($vid))
{
	if(!empty($vid))
	{
		$comments = a_list_comments($vid, 'uniq_id', $from, $limit, $page);
	}
	else
	{
		$search_query = ($_POST['keywords'] != '') ? trim($_POST['keywords']) : trim($_GET['keywords']);
		$search_type = ($_POST['search_type'] != '') ? $_POST['search_type'] : $_GET['search_type'];
		$search_query = urldecode($search_query);
		$comments = a_list_comments($search_query, $search_type, $from, $limit, $page);
	}
	$total_comments = $comments['total'];
}
else 
{
	$total_comments = count_entries('pm_comments', '', '');
	
	if($total_comments - $from == 1)
		$page--;
		
	$comments = a_list_comments('', '', $from, $limit, $page, $filter);

	if($total_comments - $from == 1)
		$page++;
	
	$total_comments = $comments['total'];
}

// generate smart pagination
$filename = 'comments.php';
$uri = $_SERVER['REQUEST_URI'];
$uri = explode('?', $uri);
$uri[1] = str_replace(array("<", ">", '"', "'", '/'), '', $uri[1]);

$pagination = '';
$pagination = a_generate_smart_pagination($page, $total_comments, $limit, 1, $filename, $uri[1]);


?>
<div id="adminPrimary">
    <div class="row-fluid" id="help-assist">
        <div class="span12">
        <div class="tabbable tabs-left">
          <ul class="nav nav-tabs">
            <li class="active"><a href="#help-overview" data-toggle="tab">Overview</a></li>
            <li><a href="#help-onthispage" data-toggle="tab">Filtering</a></li>
          </ul>
          <div class="tab-content">
            <div class="tab-pane fade in active" id="help-overview">
            <p>نظراتی که به سایت شما ارسال می شوند، شناخته می شوند به &quot;نظرات ویدئوها&quot; و &quot;نظرات مقالات&quot;. یک آیکن نشانگر نوع نظر است. با انتخاب &quot;نظرات&quot; آیتم از منو همه نظرات موجود از آخر به اول لیست می کند.</p>
			<p>اگر سایت مدیر نظر فعال شده باشد ، نظرات در حالت انتظار همچنین در لیست نمایش داده خواهد شد. برای تایید نظر بر روی آیکن &quot;بررسی کردن&quot; از ستون &quot;اقدام&quot; کلیک کن.</p>
			<p>زمانی که ماوس را بر روی هر پیام موجودی می برید پیام های در حالت انتظار و  انتشار یافته برای ویرایش اسان به شما نشان داده می شود. و این 
			برای حذف موقعی که تبلیغات ناخواسته ، داده های حساس و همانند آن نشان داده می شوند مفید خواهند بود.</p>
            </div>
            <div class="tab-pane fade" id="help-onthispage">
            <p>در هنگام مواجهه با تعداد زیادی از  داده ها صفحات فهرست همانند این یکی شامل ناحیه فیلتر که بصورت دستی  می ایند. گزینه فیلتر همیشه نشانگر
			آیکنی است که در قسمت بالای راست جداول لیست قرار داده می شود. با کلیک بر روی این آیکن همیشه یک فرم جستجو و یا یک فیلتر کشویی یا بیش تر نشان داده می شود.</p>
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
                <div class="floatL"><strong class="blue"><?php echo pm_number_format($total_comments); ?></strong><span>نظر(ها)</span></div>
                <div class="blueImg"><img src="img/ico-comments-new.png" width="19" height="13" alt="" /></div>
            </li>
        </ul><!-- .pageControls -->
    </div>
	<h2><?php if($filter == 'articles') { ?>مقاله <?php } elseif($filter == 'videos') { ?>ویدئو <?php } else {} ?> نظرات</h2>
	<?php echo $info_msg; ?>
    
	<?php if (!empty($_GET['keywords'])) : ?>
    <div class="pull-left">
    	<h4>نتیجه جستجو برای "<em><?php echo $_GET['keywords']; ?></em>" <a href="#" onClick="parent.location='comments.php'" class="opac5"><i class="icon-remove-sign"></i></a></h4>
    </div>
    <div class="clearfix"></div>
    <?php endif; ?>
    
    <div class="tablename">
        <div class="row-fluid">
            <div class="span8">
                <div class="qsFilter pull-left">
                <div class="btn-group input-prepend">
                  <div class="form-filter-inline">
                  <?php
                  if(!empty($_GET['filter'])) {
                  ?>
                  <button type="button" id="appendedInputButtons" class="btn btn-danger btn-strong" onClick="parent.location='comments.php'">حذف کردین فیلتر</button>
                  <?php } else { ?>
                  <button type="button" id="appendedInputButtons" class="btn">فیلتر</button>
                  <?php } ?>
                    <form name="other_filter" action="comments.php" class="form-inline">
                      <select name="URL" onChange="window.parent.location=this.form.URL.options[this.form.URL.selectedIndex].value" class="inline last-filter">
                        <option value="comments.php">انتخاب کن...</option>
                        <option value="comments.php?filter=flagged&page=1" <?php if ($filter == 'flagged') echo 'selected="selected"'; ?>>پرچم</option>
                        <option value="comments.php?filter=pending&page=1" <?php if ($filter == 'pending') echo 'selected="selected"'; ?>>در حالت انتظار</option>
                      </select>
                    </form>
                  </div><!-- .form-filter-inline -->
                </div><!-- .btn-group -->
                </div><!-- .qsFilter -->
            </div>
            <div class="span4">
            	<div class="pull-right">
                    <form name="search" action="comments.php" method="get" class="form-search-listing form-inline">
                    <div class="input-append">
                    <input type="text" name="keywords" value="<?php echo $_GET['keywords']; ?>" size="30" class="search-query search-quez input-medium" placeholder="Enter keyword" id="form-search-input" />
                    <select name="search_type" class="input-small">
                     <option value="comment" <?php echo ($_GET['search_type'] == "comment") ? 'selected="selected"' : ''; ?> >نظر</option>
                     <option value="uniq_id" <?php echo ($_GET['search_type'] == "uniq_id") ? 'selected="selected"' : ''; ?> >شناسه یکتای ویدئو</option>
                     <option value="username" <?php echo ($_GET['search_type'] == "username") ? 'selected="selected"' : ''; ?> >نام کاربری</option>
                     <option value="ip" <?php echo ($_GET['search_type'] == "ip") ? 'selected="selected"' : ''; ?> >آدرس آی پی</option>
                    </select> 
                    <button type="submit" name="submit" class="btn" value="Search" id="submitFind"><i class="icon-search findIcon"></i><span class="findLoader"><img src="img/ico-loading.gif" width="16" height="16" /></span></button>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="clearfix"></div>
    <?php 
	/*
	 * */
	$form_action = 'comments.php?page='. $page;
	
	$form_action .= ($filter != '') ? '&filter='. $filter : '';
	$form_action .= ($_GET['vid'] != '') ? '&vid='. $_GET['vid'] : '';
	$form_action .= ($_GET['keywords'] != '') ? '&keywords='. $_GET['keywords'] .'&search_type='. $_GET['search_type'] .'&submit=Search' : '';
	?>
    <form name="comments_checkboxes" action="<?php echo $form_action;?>" method="post">
    <table cellpadding="0" cellspacing="0" width="100%" class="table table-striped table-bordered pm-tables tablesorter">
     <thead>
      <tr>
        <th align="center" width="20"><input type="checkbox" name="checkall" id="selectall" onclick="checkUncheckAll(this);"/></th>
        <th align="center" style="text-align:center" width="20"> </th>
        <th width="35%">نظر برای</th>
        <th width="100">اضافه شده</th>
        <th>نظر</th>
        <th width="120">ارسال شده توسط</th>
        <th width="100">آی پی</th>
        <th width="" style="width: 90px">اقدام</th>
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
		
        <?php echo $comments['comments']; ?>
        
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
    <div class="btn-toolbar">
        <div class="btn-group">
		<button type="submit" name="Submit" value="Remove flag" class="btn btn-small btn-strong">حذف کردن پرچم</button>
        </div>
        <div class="btn-group">
		<button type="submit" name="Submit" value="Approve checked" class="btn btn-small btn-success btn-strong">تایید کردن انتخاب شده</button>
        </div>
        <div class="btn-group">
		<button type="submit" name="Submit" value="Delete checked" class="btn btn-small btn-danger btn-strong">حذف کردن انتخاب شده</button>
        </div>
    </div>
    </div><!-- #list-controls -->
   
	<input type="hidden" name="_pmnonce" id="_pmnonce<?php echo $comments_nonce['_pmnonce'];?>" value="<?php echo $comments_nonce['_pmnonce'];?>" />
	<input type="hidden" name="_pmnonce_t" id="_pmnonce_t<?php echo $comments_nonce['_pmnonce'];?>" value="<?php echo $comments_nonce['_pmnonce_t'];?>" />
    
    </form>

    </div><!-- .content -->
</div><!-- .primary -->
<?php
include('footer.php');
?>