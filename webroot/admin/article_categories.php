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
$_page_title = 'دسته مقالات';
include('header.php');
?>
<div id="adminPrimary">
    <div class="content">
    
    
<?php
if ( $config['mod_article']) {
$categories = art_get_categories();
$total_categories = count($categories);

$categories_dropdown_options = array('db_table' => 'art_categories',
									 'first_option_text' => '- Root -', 
									 'selected' => $_POST['parent_id'], 
									 'attr_name' => 'parent_id',
									 'attr_class' => 'inline',
									 'spacer' => '&mdash;',
									 );
?>
<div class="entry-count">
    <ul class="pageControls">
        <li>
            <div class="floatL"><strong class="blue"><?php echo pm_number_format($total_categories); ?></strong><span>موضوعات</span></div>
            <div class="blueImg"><img src="img/ico-cats-new.png" width="18" height="17" alt="" /></div>
        </li>
    </ul><!-- .pageControls -->
</div>
<?php
} //end if
?>


	<h2>موضوعات مقاله<?php if ($config['mod_article']) : ?><a class="label opac5" onClick="parent.location='edit_category.php?mode=add&type=article'">+ اضافه کردن جدید</a><?php endif;?></h2>
	<?php
    if ( ! $config['mod_article'])
    {
    ?>
    <div class="alert alert-info">
    ماژول مقالات هم اکنون غیرفعال شده است. لطفا آنرا از '<a href="settings.php">صفحه تنظیمات </a>فعال کنید'.
    </div>
    </div>
	<?php
	include('footer.php');
	exit();
	}
	?>
<?php 

$action = $_GET['do'];
if ( ! in_array($action, array('update', 'new', 'delete', 'move')) )
{
	$action = 'new';	//	default action
}

if ('' != $_POST['update'] || '' != $_POST['submit']) 
{
	if ($action == 'new')
	{
		$result = art_insert_category($_POST);
	}
	else if ($action == 'update')
	{
		$cid = (int) $_POST['cid'];
		$result = art_update_category($cid, $_POST);
	}
	
	if ($result['type'] == 'error')
	{
		echo '<div class="alert alert-error">'. $result['msg'] .'</div>';
	}
	else
	{
		$_POST = array();
		echo '<div class="alert alert-success">'. $result['msg'] .'</div>';
		unset($_article_categories);
		$categories = art_get_categories();
		$total_categories = count($categories);
	}
}

if ($_GET['move'] != '' && $_GET['id'] != '')
{
	$id = (int) $_GET['id'];
	
	if ($id > 0)
	{
		$cat = art_get_categories();
		
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
					$sql_1 = "UPDATE art_categories
							   SET position = '". ($cat[$prev_cat_id]['position'] + 1) ."' 
							 WHERE id = '". $prev_cat_id ."'";
					$sql_2 = "UPDATE art_categories
							   SET position = '". ($cat[$id]['position'] - 1) ."' 
							 WHERE id = '". $id ."'";
					
					$cat[$prev_cat_id]['position']++;
					$cat[$id]['position']--;
				}
				
			break;
	
			case 'down':
				
				if ($current_position >= 1 && $current_position < $limit && $next_cat_id)
				{
					$sql_1 = "UPDATE art_categories
							   SET position = '". ($cat[$id]['position'] + 1) ."' 
							 WHERE id = '". $id ."'";
					
					$sql_2 = "UPDATE art_categories
							   SET position = '". ($cat[$next_cat_id]['position'] - 1) ."' 
							 WHERE id = '". $next_cat_id ."'";
					
					$cat[$id]['position']++;
					$cat[$next_cat_id]['position']--;
				}
				
			break;
		}

		if ($sql_1 != '' && $sql_2 != '')
		{
			if ( ! ($result = mysql_query($sql_1)))
			{
				$info_msg  = '<div class="alert alert-error">در هنگام بروزرسانی دیتابیس تان خطایی رخ داد!<br />';
				$info_msg .= '<strong>گزارش mysql :</strong>: '.mysql_error().'</div>';
			}
			else
			{
				if ( ! ($result = mysql_query($sql_2)))
				{
					$info_msg  = '<div class="alert alert-error">در هنگام بروزرسانی دیتابیس تان خطایی رخ داد!<br />';
					$info_msg .= '<strong>گزارش mysql :</strong>: '.mysql_error().'</div>';
				}
			}
			
			echo $info_msg;
			unset($_article_categories);
		}
	}
	
	if ($info_msg == '')
	{
		echo '<meta http-equiv="refresh" content="0;URL=article_categories.php?cid='. $id .'&moved='. $_GET['move'] .'" />';
		exit();
	}
	
}

if ($_GET['moved'] == 'up' || $_GET['moved'] == 'down') : ?>
<div class="alert alert-success">دسته <strong><?php echo $categories[$_GET['cid']]['name'];?></strong> انتقال یافته <?php echo $_GET['moved'];?>.</div>
<?php endif; ?>

<div id="display_result" style="display:none;"></div>

<div class="enclosed enclosed-quick-add shadow-div">
	<h3>اضافه کردن دسته جدید</h3>
	<div class="enclosed-body">
		<form name="article_new_category" action="article_categories.php?do=new" method="post" class="form-inline">
			<input name="name" type="text" value="<?php if($_POST['name'] != '') { echo $_POST['name']; } ?>" placeholder="Category name" size="22" />
			<input name="tag" type="text" value="<?php if($_POST['tag'] != '') { echo $_POST['tag']; } ?>" placeholder="Slug" size="10" class="span2" /> 
		
		   <a href="#" rel="tooltip" title="اسلاگ ها در آدرس استفاده می شوند و می توانند شامل اعداد ، حروف ، خط تیره و خط زیر باشند."><i class="icon-info-sign"rel="tooltip" title="اسلاگ ها در آدرس استفاده می شوند و می توانند شامل اعداد ، حروف ، خط تیره و خط زیر باشند."></i></a>
		   
		   Create in
		   <?php echo categories_dropdown($categories_dropdown_options); ?>
		   <button name="submit" type="submit" value="Add category" class="btn btn-strong btn-blue" />اضافه کردن دسته</button>
		</form>
		<strong><small><a href="edit_category.php?mode=add&type=article">از فروم حرفه ای استفاده کن</a></small></strong>
	</div>
</div>

<hr />    
<div class="tablename">
<h6>دسته های مقالات موجود</h6>
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
	<th width="5%">مقالات</th>
	<th width="5%">محل</th>
    <th width="10%" align="center" style="width:90px;">اقدام</th>
  </tr>
 </thead>
 <tbody>
	
	<?php 
	$args = array('page' => 'article_categories.php',
				  'form_action' => 'article_categories.php?do=update',
				);		
	echo a_category_table_body($categories, $args);	
	?>
 </tbody>
</table>
<?php echo csrfguard_form('_admin_catmanager'); ?>
    </div><!-- .content -->
</div><!-- .primary -->
<?php
include('footer.php');
?>