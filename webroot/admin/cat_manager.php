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

$showm = '3';
$_page_title = 'دسته های ویدئو ها';
include('header.php');

load_categories();

$pattern = '/(^[a-z0-9_-]+)$/i';

// UPDATE CATEGORY
if(!empty($_POST['update']) && $_POST['update'] == 'Update') {
	$cid = $_POST['cid'];
 	$tag = secure_sql(trim($_POST['tag']));
	$name = secure_sql(trim($_POST['name']));
	$parent_id = (int) $_POST['parent_id'];
	$old_tag = $_POST['old_tag'];

	if(empty($tag) || empty($name)) 
		$info_msg = '<div class="alert alert-error">لطفا هیچ فیلدی را خالی نگذارید.</div>';	
	else {
		if( ! @preg_match($pattern, $tag)) {
		$info_err_msg = '<div class="alert alert-error">لطفا مطمئن شوید که اسلاگ به درستی وارد شده باشد (بدون فاصله ، فقط حروف لاتین a-z کوچک و بزرگ ، اعداد ، خط تیره و خط زیر.</div>';
		} else {
			
			if (strcmp($old_tag, $tag) != 0)
			{
				$sql = "SELECT COUNT(*) as total_found 
						FROM pm_categories 
						WHERE tag = '". $tag ."'";
				$result = @mysql_query($sql);
				$row = mysql_fetch_assoc($result);
				
				if ($row['total_found'] != 0)
				{
					$info_err_msg = '<div class="alert alert-error">این اسلاگ در حقیقت به وسیله دسته دیگری استفاده می شود.</div>'; 
				}
				else
				{
					mysql_query("UPDATE pm_categories SET tag = '".$tag."', name = '".$name."' WHERE id = '".$cid."'");
					$info_msg = '<div class="alert alert-success">دسته <strong>'.$name.'</strong> آپلود شد.</div>';
					$_POST = array();
				}
			}
			else
			{
				mysql_query("UPDATE pm_categories SET name = '".$name."' WHERE id = '".$cid."'");
				$info_msg = '<div class="alert alert-success">دسته <strong>'.$name.'</strong> آپلود شد.</div>';
				$_POST = array();
			}
		}
	}
	unset($_video_categories);
	load_categories();
}

// ADD NEW CATEGORY
if(!empty($_POST['submit']) && $_POST['submit'] == 'Add category') {

	$parent_cid = $_POST['category']; // (parent_id)
	$cid = $_POST['cid'];
	$tag = secure_sql(trim($_POST['tag']));
	$name = secure_sql(trim($_POST['name']));
	
	if ($parent_cid < 0)
	{
		$parent_cid = 0;
	}
	
	if(empty($tag) || empty($name)) 
		$info_msg = '<div class="alert alert-error">لطفا هیچ فیلدی را خالی نگذارید.</div>';	
	else {	
		$query = mysql_query("SELECT tag FROM pm_categories where tag = '".$tag."'");
		$result = mysql_num_rows($query);
		
		if($result == 0) {
		
			if( ! @preg_match($pattern, $tag)) {
			$info_err_msg = '<div class="alert alert-error">لطفا مطمئن شوید که اسلاگ شامل کاراکترهایاجازه داده شده باشد. (فقط حروف ، اعداد ، "_" و "-").</div>';
			} else {
				// get position of the last category
				$sql = "SELECT MAX(position) as max  
 						  FROM pm_categories 
						 WHERE parent_id = '". $parent_cid ."'";

				$result = mysql_query($sql);
				$row = mysql_fetch_assoc($result);
				mysql_free_result($result);
				
				$position = ($row['max'] > 0) ? ($row['max'] + 1) : 1;
				
				$sql = "INSERT INTO pm_categories (parent_id, tag, name, published_videos, total_videos, position) 
							 VALUES ('". $parent_cid ."', '". $tag ."', '". $name ."', 0, 0, ". $position .")";
				 
				if ( ! ($result = mysql_query($sql)))
				{
					$info_msg  = '<div class="alert alert-error">در هنگام ایجاد دسته جدید خطایی رخ داد.<br />';
					$info_msg .= '<strong>mysql بر می گرداند :</strong>: '.mysql_error().'</div>';
				}
				else 
				{
					$info_msg = '<div class="alert alert-success">دسته <strong>'.$name.'</strong> با موفقیت اضافه شد.</div>';
					$_POST = array();
					unset($_video_categories);
					load_categories();
				}
			}
		
		} else {
				$info_msg = '<div class="alert alert-error">این اسلاگ در حقیقت به وسیله دسته دیگری استفاده می شود.</div>';

		}
	}	
}
// DELETE (SUB)CATEGORY
if ($_GET['a'] == 1 && ! csrfguard_check_referer('_admin_catmanager'))
{
	$info_msg = '<div class="alert alert-error">رمز عبور اشتباه است یا سشن منقضی شده. لطفا صفحه را رفرش نمایید و از دوباره تلاش کنید.</div>';
}
else if( !empty($_GET['cid']) && $_GET['a'] == 1)
{
	$info_msg = '';
	$cid = (int) $_GET['cid'];
	
	$categories = a_list_cats_simple();
	
	$parents = $children = array();
	foreach ($categories as $id => $cat_arr)
	{
		if ($cat_arr['parent_id'] == 0)
		{
			$parents[] = $cat_arr;
		}
		else
		{
			$children[$cat_arr['parent_id']][] = $cat_arr;
		}
	}
	
	$delete_ids = array();
	get_all_children($cid, $children, $delete_ids);
	
	// remember these values for later
	$parent_id = $categories[$cid]['parent_id'];
	$current_position = $categories[$cid]['position'];
	
	if (count($delete_ids) > 0)
	{
		$video_uniq_ids = array();
		$delete_ids_str = '';
		
		$delete_ids_str = implode(",", $delete_ids);
		
		$sql = "DELETE 
				FROM pm_categories 
				WHERE id IN (". $delete_ids_str .")";
		$result = mysql_query($sql);
		if ( ! $result) 
		{
			$info_msg  = '<div class="alert alert-error">در هنگام بروزرسانی دیتابیس تان خطایی رخ داد است.<br />';
			$info_msg .= '<strong>گزارش mysql :</strong>: '.mysql_error().'</div>';
		}
		
		// update positions for other categories
		$update_pos_ids = array();
		foreach ($categories as $id => $cat_arr)
		{
			if (($cat_arr['parent_id'] == $parent_id) && ($cat_arr['position'] > $current_position))
			{
				$update_pos_ids[] = $id;
			}
		}
		
		if (count($update_pos_ids) > 0)
		{
			$update_pos_ids = implode(',', $update_pos_ids);
			$sql = "UPDATE pm_categories 
					   SET position = position - 1 
					 WHERE id IN (". $update_pos_ids .")";
			$result = mysql_query($sql);
		}
		
	
		foreach ($delete_ids as $k => $id)
		{
			$delete_ids_str = '';
			foreach ($delete_ids as $k => $id)
			{
				$delete_ids_str .= "'". $id ."', ";
			}
			$delete_ids_str = substr($delete_ids_str, 0, -2);
			
			$videos = array();
			
			$sql = "UPDATE pm_videos 
					SET category = '0' 
					WHERE category IN (". $delete_ids_str .")";
			mysql_query($sql);
		}
	
		$info_msg = '<div class="alert alert-success">دسته <strong>'.$categories[$cid]['name'].'</strong> حذف شد.';
		if (count($delete_ids) > 1)
		{
			$info_msg .= '<br />';
			$info_msg .= 'همچنین تمام زیردسته های موجود حذف شده اند.'; 
		}
		$info_msg .= '</div>';
	}
	
	unset($_video_categories);
}

if ($_GET['move'] != '' && $_GET['id'] != '')
{
	$id = (int) $_GET['id'];
	
	if ($id > 0)
	{
		$cat = a_list_cats_simple();
		
		$limit = 0;
		$is_parent = false;
		$is_child = false;

		if ($cat[$id]['parent_id'] == 0)
		{
			foreach ($cat as $c_id => $c_arr)
			{
				if ($c_arr['parent_id'] == 0)
				{
					$is_parent = true;
					$limit++;
				}
			}
		}
		else
		{
			foreach ($cat as $c_id => $c_arr)
			{
				if ($c_arr['parent_id'] == $cat[$id]['parent_id'])
				{
					$is_child = true;
					$limit++;
				}
			}
		}
		
		$current_position = $cat[$id]['position'];
		$prev_cat_id = $next_cat_id = 0;
		
		// find neighbours 
		foreach ($cat as $c_id => $c_arr)
		{
			if ($c_arr['position'] == ($current_position - 1) && $c_arr['parent_id'] == $cat[$id]['parent_id'])
			{
				$prev_cat_id = $c_id;
			}
			
			if ($c_arr['position'] == ($current_position + 1) && $c_arr['parent_id'] == $cat[$id]['parent_id'])
			{
				$next_cat_id = $c_id;
			}
		}
		
		switch ($_GET['move'])
		{
			case 'up':
				
				if ($current_position > 1 && $current_position <= $limit && $prev_cat_id)
				{
					$sql_1 = "UPDATE pm_categories
							   SET position = '". ($cat[$prev_cat_id]['position'] + 1) ."' 
							 WHERE id = '". $prev_cat_id ."'";
					$sql_2 = "UPDATE pm_categories
							   SET position = '". ($cat[$id]['position'] - 1) ."' 
							 WHERE id = '". $id ."'";
				}
				
			break;
	
			case 'down':
				
				if ($current_position >= 1 && $current_position < $limit && $next_cat_id)
				{
					$sql_1 = "UPDATE pm_categories
							   SET position = '". ($cat[$id]['position'] + 1) ."' 
							 WHERE id = '". $id ."'";
					
					$sql_2 = "UPDATE pm_categories
							   SET position = '". ($cat[$next_cat_id]['position'] - 1) ."' 
							 WHERE id = '". $next_cat_id ."'";
				}
				
			break;
		}
		
		if ($sql_1 != '' && $sql_2 != '')
		{
			if ( ! ($result = mysql_query($sql_1)))
			{
				$info_msg  = '<div class="alert alert-error">در هنگام بروزرسانی دیتابیس تان خطایی رخ داده!<br />';
				$info_msg .= '<strong>mysql بر می گرداند :</strong>: '.mysql_error().'</div>';
			}
			else
			{
				if ( ! ($result = mysql_query($sql_2)))
				{
					$info_msg  = '<div class="alert alert-error">در هنگام بروزرسانی دیتابیس تان خطایی رخ داده!<br />';
					$info_msg .= '<strong>mysql بر می گرداند :</strong>: '.mysql_error().'</div>';
				}
			}
			
			unset($_video_categories);
		}
	}
	
	if ($info_msg == '')
	{
		echo '<meta http-equiv="refresh" content="0;URL=cat_manager.php?cid='. $id .'&moved='. $_GET['move'] .'" />';
		exit();
	}
}

$total_categories = count_entries('pm_categories', '', '');

$categories_dropdown_options = array('first_option_text' => '- Root -', 
									 'attr_class' => 'inline',
									 'spacer' => '&mdash;',
									 'selected' => $_POST['category']
									);
?>
<div id="adminPrimary">
    <div class="row-fluid" id="help-assist">
        <div class="span12">
        <div class="tabbable tabs-left">
          <ul class="nav nav-tabs">
            <li class="active"><a href="#help-overview" data-toggle="tab">Overview</a></li>
          </ul>
          <div class="tab-content">
            <div class="tab-pane fade in active" id="help-overview">
            <p>دسته ها در داخل دسته های مقالات و ویدئوها مجزا شده اند. اگر ماژول مقاله فعال باشد دسته های مقالات نشان داده می شوند.</p>
			<p>در بالای صفحه فرمی برای اضافه کردن دسته وجود دارد. در زیر شما لیستی از درخت دسته کنونی پیدا خواهید کرد. دسته ها می توانند  بر اساس سلسله مراتب  بالا و پایین انتقال پیدا کنند.<br />بدون خروج از صفحه ویرایش دسته موجود ساخته می شود. به راحتی ماوس را بر روی دسته ببر تا آنرا ویرایش بکنی.</p>
			<p>اضافه کردن یک دسته جدید به یک &quot;اسلاگ&quot; نیاز دارد که در نسخه آدرس پسند نام دسته است. دسته ها می توانند در &quotروت&quot یا در دسته موجودی که آنرا به یک زیردسته می سازد قرار داده می شود.</p>
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
                <div class="floatL"><strong class="blue"><?php echo pm_number_format($total_categories); ?></strong><span>دسته ها</span></div>
                <div class="blueImg"><img src="img/ico-cats-new.png" width="18" height="17" alt="" /></div>
            </li>
        </ul><!-- .pageControls -->
    </div>
	<h2>Video Categories <a class="label opac5" onClick="parent.location='edit_category.php?mode=add&type=video'">+ add new</a></h2>

<?php echo $info_err_msg;?>
<?php echo $info_msg;?>
<?php if ($_GET['moved'] == 'up' || $_GET['moved'] == 'down') : ?>
<div class="alert alert-success">دسته <strong><?php echo $_video_categories[$_GET['cid']]['name'];?></strong> moved <?php echo $_GET['moved']; ?>.</div>
<?php endif; ?>
<div class="enclosed enclosed-quick-add shadow-div">
	<h3>اضافه کردن یک دسته جدید</h3>
	<div class="enclosed-body">
		<form name="search" action="cat_manager.php" method="post" class="form-inline">
		<input name="name" type="text" value="<?php if($_POST['name'] != '') { echo $_POST['name']; } ?>" placeholder="Category name" size="22" />
		<input name="tag" type="text" value="<?php if($_POST['tag'] != '') { echo $_POST['tag']; } ?>" placeholder="Slug" size="10" class="span2" /> 
		<a href="#" rel="tooltip" title="اسلاگ ها در آدرس استفاده می شوند و می توانند شامل اعداد ، حروف ، خط تیره و خط زیر باشند."><i class="icon-info-sign" rel="tooltip" title="اسلاگ ها در آدرس استفاده می شوند و می توانند شامل اعداد ، حروف ، خط تیره و خط زیر باشند."></i></a>
		ایجاد در
		<?php echo categories_dropdown($categories_dropdown_options); ?>
		<button name="submit" type="submit" value="Add category" class="btn btn-strong btn-blue" />اضافه کردن دسته</button>
		</form>
		<strong><small><a href="edit_category.php?mode=add&type=video">از فروم حرفه ای استفاده کن</a></small></strong>
	</div>
</div>

<hr />
<div class="tablename">
<h5>دسته های ویدئوی موجود</h5>
<div class="qsFilter">
<div class="btn-group input-prepend">
</div><!-- .btn-group -->
</div><!-- .qsFilter -->
</div>

<table cellpadding="0" cellspacing="0" width="100%" class="table table-striped table-bordered pm-tables tablesorter">
 <thead>
  <tr>
  	<th width="3%">شناسه</th>
	<th width="30%">نام دسته</th>
    <th width="35%">اسلاگ</th>
    <th width="15%">دسته والد</th>
	<th width="5%">ویدئوها</th>
	<th width="5%">موقعیت</th>
    <th width="10%" align="center" style="with: 90px;">اقدام</th>
  </tr>
 </thead>
 <tbody>
	<?php
	echo a_list_cats();
	?>
 </tbody>
</table>
<?php echo csrfguard_form('_admin_catmanager'); ?>
    </div><!-- .content -->
</div><!-- .primary -->
<?php
include('footer.php');
?>