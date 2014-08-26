<?php
$showm = '6';
$load_scrolltofixed = 1;
$_page_title = 'جریان فعالیت';
include('header.php');

$action = $_GET['a'];
$aid = (int) $_GET['aid'];
$page = (int) $_GET['page'];

if(empty($page))
	$page = 1;
$limit = 20;	//	users per page
$from = $page * $limit - ($limit);

$filter = '';
$filters = array('type', 'user_id'); 
$filter_value = '';

if(in_array(strtolower($_GET['filter']), $filters) !== false)
{
	$filter = strtolower($_GET['filter']);
	$filter_value = $_GET['fv'];
}

if ($_POST['Submit'] != '' && ! csrfguard_check_referer('_admin_members_activity'))
{
	$info_msg = '<div class="alert alert-error">رمز عبور نامعتبر است یا سشن منقضی شده. لطفا صفحه را رفرش نمایید و از دوباره  تلاش کنید.</div>';
}
else if ($_POST['Submit'] != '') 
{
	$activity_ids = $_POST['activity_id'];
	if (count($activity_ids) > 0)
	{
		$sql = "DELETE FROM pm_activity 
				WHERE activity_id IN (". implode(',', $activity_ids) .")";
		$result = @mysql_query($sql);
		$affected_rows = mysql_affected_rows();
		
		if ( ! $result)
		{
			$info_msg = '<div class="alert alert-error">در هنگام بروزرسانی دیتابیستان خطایی رخ داده.<br />خطای برگشتی :'.mysql_error().'</div>';
		}
		else
		{
			if ($affected_rows == 1)
			{
				$info_msg = '<div class="alert alert-success">یک فعالیت حذف شد.</div>';
			}
			else
			{
				$info_msg = '<div class="alert alert-success">'. $affected_rows .' با موفقیت گزارش این فعالیت ها حذف شد ..</div>';
			}
		}
	}
	else
	{
		$info_msg = '<div class="alert alert-info">لطفا ابتدا چیزی را انتخاب کنید.</div>';
	}
}

if ($action == 'delete' && ! csrfguard_check_referer('_admin_members_activity'))
{
	$info_msg = '<div class="alert alert-error">رمز عبور نامعتبر است یا سشن منقضی شده. لطفا صفحه را رفرش نمایید و از دوباره  تلاش کنید.</div>';
}
elseif ($aid != 0 && $action == 'delete')
{
	$result = delete_activity($aid);
	
	if ( ! $result)
	{
		$info_msg = '<div class="alert alert-error">در هنگام بروزرسانی دیتابیستان خطایی رخ داده.<br />خطای برگشتی :'.mysql_error().'</div>';
	}
	else
	{
		$info_msg = '<div class="alert alert-success">یک فعالیت حذف شد.</div>';
	}
}

$activity_stream_nonce = csrfguard_raw('_admin_members_activity');

// Search
if($_GET['keywords'] != '')
{
	$search_query = ($_POST['keywords'] != '') ? trim($_POST['keywords']) : trim($_GET['keywords']);
	
	$filter_value = username_to_id($search_query);
	$filter = 'user_id';
} 

if ($filter != '')
{
	switch ($filter)
	{
		case 'user_id': 
			
			$total_items = count_entries('pm_activity', 'user_id', $filter_value);
		break;
		
		case 'type':
			$total_items = count_entries('pm_activity', 'activity_type', $filter_value);
		break;
	}
}
else
{
	$total_items = count_entries('pm_activity', '', '');
}

if($total_items - $from == 1)
	$page--;

$items = admin_get_activities($from, $limit, $page, $filter, $filter_value);

// generate smart pagination
$filename = 'activity-stream.php';
$uri = $_SERVER['REQUEST_URI'];
$uri = explode('?', $uri);
$uri[1] = str_replace(array("<", ">", '"', "'", '/'), '', $uri[1]);
parse_str($uri[1], $temp);
unset($temp['_pmnonce'], $temp['_pmnonce_t'], $temp['a'], $temp['aid']);
$uri[1] = http_build_query($temp);

$pagination = '';
$pagination = a_generate_smart_pagination($page, $total_items, $limit, 1, $filename, $uri[1]);

?>
<div id="adminPrimary">

	<div class="content">
	<a href="#" id="show-help-assist">راهنما</a>
    <div class="entry-count">
        <ul class="pageControls">
            <li>
                <div class="floatL"><strong class="blue"><?php echo pm_number_format($total_items); ?></strong><span>فعالیت ها</span></div>
                <div class="blueImg"><img src="img/ico-users-new.png" width="19" height="18" alt="" /></div>
            </li>
        </ul><!-- .pageControls -->
    </div>
	
	<h2>جریان فعالیت</h2>
	
	<?php echo $info_msg; ?>
		
    <?php if (!empty($_GET['keywords'])) : ?>
    <div class="row-fluid">
    <div class="span12">
        <div class="pull-left">
        <h4>نتیجه جستجو برای "<em><?php echo $_GET['keywords']; ?></em>" <a href="#" onClick="parent.location='activity-stream.php'" class="opac5"><i class="icon-remove-sign"></i></a></h4>
        </div>
    </div><!-- .span12 -->
    </div>
    <?php endif; ?>
	
	<div class="tablename">
	<div class="row-fluid">
	<div class="span8">
        <div class="qsFilter pull-left">
            <form name="activity_type_filter" action="activity-stream.php" method="get" class="form-inline">
            <input type="hidden" name="filter" value="type" />
            <div class="btn-group input-prepend">
            <div class="form-filter-inline">
            <?php if ( ! empty($_GET['filter'])) : ?>
            <button type="button" class="btn btn-danger btn-strong" onClick="parent.location='activity-stream.php'">حذف فیلتر</button>
            <?php else : ?>
            <button type="button" class="btn">فیلتر</button>
            <?php endif; ?>
            <select name="fv" class="span2 last-filter" onchange="submit()">
            <option value="">توسط فعالیت ...</option>
            <?php 
                $activity_types = activity_load_options();
                foreach ($activity_types as $type => $v) : ?>
                    <option value="<?php echo $type;?>" <?php if ($filter_value == $type) echo 'selected="selected"'; ?> ><?php echo $activity_labels[$type];?></option>
                <?php endforeach;?> 
            </select>
            </div>
            </div><!-- .btn-group -->
            </form>
        </div><!-- .qsFilter -->
	</div>
	<div class="span4">
   		<div class="pull-right">
        <form name="search" action="activity-stream.php" method="get" class="form-search-listing form-inline" >
        <div class="input-append">
        <input type="text" name="keywords" value="<?php echo $_GET['keywords']; ?>" size="30" class="search-query search-quez input-medium" placeholder="کلمه کلیدی" id="form-search-input" />
        <select name="search_type" class="input-small">
         <option value="username" <?php echo ($_GET['search_type'] == "username") ? 'selected="selected"' : ''; ?> >نام کاربری</option>
        </select> 
        <button type="submit" name="submit" class="btn" value="Search" id="submitFind"><i class="icon-search findIcon"></i><span class="findLoader"><img src="img/ico-loading.gif" width="16" height="16" /></span></button>
        </div>
        </form>	
        </div>
	</div>
	</div>
	</div> 
	<div class="clearfix"></div>
	<form name="activity_checkboxes" id="activity_checkboxes" action="activity-stream.php?page=<?php echo $page;?>&filter=<?php echo $filter;?>&fv=<?php echo $filter_value;?>" method="post">
	<table cellpadding="0" cellspacing="0" width="100%" class="table table-striped table-bordered pm-tables tablesorter">
	 <thead>
	  <tr>
	   <th align="center" style="text-align:center" width="3%"><input type="checkbox" name="checkall" id="selectall" onclick="checkUncheckAll(this);"/></th>
	   <th width="10"></th>
	   <th width="10%">نام کاربری</th>
	   <th>فعالیت</th>
	   <th width="10%">زمان</th>
	   <th style="width: 90px;">رفتار</th>
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
	  
	  <?php 
	  if (count($items) == 0) : ?>
	  <tr>
	  	<td colspan="6" align="center" style="text-align:center">هنوز فعالیتی وجود ندارد</td>
	  </tr>
	  <?php else : ?>
	  <?php 
		$banlist = get_banlist();
	  	$time_now = time();
	  ?>
	  <?php foreach ($items as $activity_id => $activity) : ?>
	  <tr id="activity-<?php echo $activity_id;?>">
	  	<td align="center" style="text-align:center">
	  		<input name="activity_id[]" type="checkbox" value="<?php echo $activity['activity_id'];?>" />
		</td>
		<td align="center" style="text-align:center" width="10">
			<?php if ($activity['hide'] == 1) : ?>
			<a href="#" rel="tooltip" title="کاربر این فعالیت را از پروفایلش مخفی کرده."><i class="icon-eye-close"></i></a>
			<?php endif; ?>
		</td>
		<td>
			<?php if ($activity['user_id'] != 0) : ?>
			<a href="<?php echo _URL .'/profile.php?u='. $activity['username'];?>" target="_blank"><?php echo (array_key_exists($activity['user_id'], $banlist)) ? $activity['username'] .' <span class="label label-important pull-right">مسدود شده</span>' : $activity['username'];?></a>
			<?php else : ?>
			<?php echo ($activity['username'] != '') ? $activity['username'] : 'Visitor'; ?>
			<?php endif;?>
		</td>
		<td>
			<?php
			switch ($activity['activity_type'])
			{
				case ACT_TYPE_LIKE:
				?>
					<!--<i class="icon-thumbs-up opac5"></i>-->
				<?php
					echo ucfirst($lang['activity_'. $activity['activity_type']]);
				break;
				
				case ACT_TYPE_DISLIKE:
				?>
					<!--<i class="icon-thumbs-down opac5"></i>-->
				<?php
					echo ucfirst($lang['activity_'. $activity['activity_type']]);
				break;
				
				case ACT_TYPE_COMMENT:
					echo ucfirst($lang['activity_'. $activity['activity_type']]) .' '. $lang['activity_obj_'.$activity['target_type']];
				break;
				
				case ACT_TYPE_STATUS:
					?>
					<strong>وضعیت بروزرسانی :</strong>
					<?php
					if (str_word_count($activity['metadata']['statustext'], 0) > 30)
					{
						preg_match('/^(.{1,255})\b/s', $activity['metadata']['statustext'], $matches);
						?>
						<span id="excerpt-<?php echo $activity_id;?>">
							<?php echo str_replace('<br />', '', $matches[1]); ?>...
						</span>
						<a href="#" id="show-more-<?php echo $activity_id;?>" title="نمایش بیشتر">بیش تر</a>
						<span id="full-text-<?php echo $activity_id;?>" style="display:none;">
							<?php echo $activity['metadata']['statustext']; ?>
						</span>
						<a href="#" id="show-less-<?php echo $activity_id;?>" style="display:none;" title="نمایش کمتر">کم تر</a>
						<?php
					}
					else
					{
						echo $activity['metadata']['statustext'];
					}
				break;
				default:
					echo ucfirst($lang['activity_'. $activity['activity_type']]);
				break;
			}
			
			if ($activity['object_id'] != 0)
			{
				$meta = $activity['metadata']['object'];
				
				switch ($activity['object_type'])
				{
					case ACT_OBJ_USER:
					?>
						<a href="<?php echo $meta['profile_url'];?>"><?php echo $meta['username'];?></a>
					<?php
					break;
					
					case ACT_OBJ_VIDEO:
					?>
						<a href="<?php echo $meta['video_href'];?>"><?php echo $meta['video_title'];?></a>
					<?php
					break;
					
					case ACT_OBJ_COMMENT:
					?>
					<?php
					break;
					
					case ACT_OBJ_ARTICLE:
					?>
						<a href="<?php echo $meta['link'];?>"><?php echo $meta['title'];?></a>
					<?php
					break;
					
					case ACT_OBJ_PROFILE:
					?>
					<?php
					break;
					
					case ACT_OBJ_PLAYLIST:
					?>
					<?php
					break;
					
					case ACT_OBJ_STATUS:
					?>
					<?php
					break;
					
				}
			}
			
			if ($activity['target_id'] != 0)
			{
				$meta = $activity['metadata']['target'];
				
				switch ($activity['target_type'])
				{
					case ACT_OBJ_USER:
					?>
						<a href="<?php echo $meta['profile_url'];?>"><?php echo $meta['username'];?></a>
					<?php
					break;
					
					case ACT_OBJ_VIDEO: 
					?>
						<a href="<?php echo $meta['video_href'];?>"><?php echo $meta['video_title'];?></a>
					<?php 
					break;
					
					case ACT_OBJ_COMMENT:
					?>
					<?php
					break;
					
					case ACT_OBJ_ARTICLE:
					?>
						<a href="<?php echo $meta['link'];?>"><?php echo $meta['title'];?></a>
					<?php
					break;
					
					case ACT_OBJ_PROFILE:
					?>
					<?php
					break;
					
					case ACT_OBJ_PLAYLIST:
					?>
					<?php
					break;
					
					case ACT_OBJ_STATUS:
					?>
					<?php
					break;
					
				}
			}
			
			?>
			
		</td>
		<td align="center" style="text-align:center" width="15%">
			<?php echo ($time_now - $activity['time'] <= (86400 * 3)) ? time_since($activity['time']) .' ago' : date('M d, Y h:i:s A', $activity['time']);?>
		</td>
		<td align="center" class="table-col-action" style="text-align:center">
			<a href="#" onclick="javascript: del_activity_id(<?php echo $activity_id;?>, <?php echo $page;?>)" rel="tooltip" title="حذف" class="btn btn-mini btn-link"><i class="icon-remove"></i></a>
		</td>
	  </tr>
	  <?php endforeach; ?>
	  <?php endif; ?>
	  
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
        <button type="submit" name="Submit" value="حذف انتخاب شده ها" class="btn btn-small btn-danger btn-strong" onClick="return confirm_delete_all();">حذف از حالت انتخاب</button>
		</div>
	</div>
	</div><!-- #list-controls -->
    <input type="hidden" name="_pmnonce" id="_pmnonce<?php echo $activity_stream_nonce['_pmnonce'];?>" value="<?php echo $activity_stream_nonce['_pmnonce'];?>" />
    <input type="hidden" name="_pmnonce_t" id="_pmnonce_t<?php echo $activity_stream_nonce['_pmnonce'];?>" value="<?php echo $activity_stream_nonce['_pmnonce_t'];?>" />
    <input type="hidden" name="filter" id="listing-filter" value="<?php echo $filter;?>" />
    <input type="hidden" name="fv" id="listing-filter_value"value="<?php echo $filter_value;?>" />
	</form>
	
    </div><!-- .content -->
</div><!-- .primary -->
<?php
include('footer.php');
?>