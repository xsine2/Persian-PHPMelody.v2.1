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
$load_scrolltofixed = 1;
$load_chzn_drop = 1;
$load_tagsinput = 1;
$load_tinymce = 1;
$load_swfupload = 1;
$load_swfupload_upload_image_handlers = 1;
$_page_title = 'ویرایش کردن دسته';
include('header.php');

$mode = ($_GET['mode'] != '') ? $_GET['mode'] : 'add';
$category_type = $_GET['type']; // 'video' or 'article'
$category_id = (int) $_GET['cid'];


$form_data = $errors = array();
$success_add = $success_edit = $show_footer_early = false;

$categories_dropdown_options = array('first_option_text' => '- Root -', 
									 'attr_class' => 'category_dropdown span12',
									 'spacer' => '&mdash;',
									 'selected' => 0,
									 'db_table' => ($category_type == 'video') ? 'pm_categories' : 'art_categories'
									);

// $all_categories -> narrowing to a single array to work with; simpler this way
$all_categories = ($category_type == 'video') ? load_categories() : load_categories(array('db_table' => 'art_categories'));

$category_data = $all_categories[$category_id];

if ($mode == 'edit' && empty($category_id))
{
	$errors[] = 'شناسه غیرمعتبر دسته.';
}
else if ($mode == 'edit')
{
	$form_data = $category_data;
	$categories_dropdown_options['selected'] = $category_data['parent_id'];
}

if ($_POST['save'] != '' && count($errors) == 0)
{
	foreach ($_POST as $k => $v)
	{
		$_POST[$k] = stripslashes( trim($v) );
	}
	
	switch ($mode)
	{
		case 'add':
			
			$parent_cid = (int) $_POST['category'];
			$parent_cid = ($parent_cid < 0) ? 0 : $parent_cid;
			
			$name = $_POST['name'];
			$name = str_replace('&amp;', '"', $name);
			
			$tag = $_POST['tag'];
			$description = $_POST['description'];
			$meta_title = $_POST['meta_title'];
			$meta_keywords = $_POST['meta_keywords'];
			$meta_description = $_POST['meta_description'];
			
			$tag = trim($tag);
			$tag = sanitize_title($tag);
			
			if (empty($tag) || empty($name))
			{
				$errors[] = '<code>نام دسته</code> و <code>اسلاگode> فیلدهای اجباری هستند.';
			}
			else
			{
				if( ! preg_match('/(^[a-z0-9_-]+)$/i', $tag)) 
				{
					$errors[] = 'لطفا مطمئن شو که اسلاگ به درستی تایپ شده (بدون فاصله ، فقط کاراکترهای لاتین a تا z کوچک و بزرگ ، اعداد ، "_" و "-").';
				}
				
				if (count($all_categories) > 0)
				{
					foreach ($all_categories as $id => $c)
					{
						if ($c['tag'] == $tag)
						{
							$errors[] = 'این اسلاگ در حقیقت استفاده می شود برای دسته <strong>'. $c['name'] .'</strong>.';
							break;
						}
					}
				}
			}
			
			if (count($errors) == 0)
			{
				$sql_table = ($category_type == 'video') ? 'pm_categories' : 'art_categories';
				
				// get position of the last category
				$sql = "SELECT MAX(position) as max  
 						  FROM $sql_table 
						 WHERE parent_id = '". $parent_cid ."'";
				$result = mysql_query($sql);
				$row = mysql_fetch_assoc($result);
				mysql_free_result($result);
				
				$position = ($row['max'] > 0) ? ($row['max'] + 1) : 1;
				
				if ($category_type == 'video')
				{
					$meta_tags = '';

					if ($meta_title != '' || $meta_keywords != '' || $meta_description != '')
					{
						$meta_tags = array('meta_title' => str_replace('"', '&quot;', $meta_title),
										   'meta_keywords' => str_replace('"', '&quot;', $meta_keywords),
										   'meta_description' => str_replace('"', '&quot;', $meta_description)
										  );
						$meta_tags = serialize($meta_tags);
					}

					$sql = "INSERT INTO pm_categories (parent_id, tag, name, published_videos, total_videos, position, description, meta_tags) 
								 VALUES ('". $parent_cid ."', 
								 		 '". secure_sql($tag) ."', 
										 '". secure_sql($name) ."', 
										 0, 
										 0, 
										 ". $position .", 
										 '". secure_sql($description) ."',
										 '". secure_sql($meta_tags) ."'
										)";
					if ( ! ($result = mysql_query($sql)))
					{
						$errors[] = 'در هنگام دسته جدید خطایی رخ داده.<br /><strong>گزارش mysql :</strong>: '.mysql_error();
					}
					else
					{
						$success_add = true;
						$show_footer_early = true;
					}
				}
				else
				{
					$_POST['name'] = $name;
					$result = art_insert_category($_POST);
					if ($result['type'] == 'error')
					{
						$errors[] = $result['msg'];
					}
					else
					{
						$success_add = true;
						$show_footer_early = true;
					}
				}
			}
			
			$form_data = $_POST;
			$categories_dropdown_options['selected'] = $form_data['category'];
			
		break;
		
		case 'edit':
			
			$parent_cid = (int) $_POST['category'];
			$parent_cid = ($parent_cid < 0) ? 0 : $parent_cid;
			$parent_cid = ($parent_cid == $category_data['id']) ? $category_data['parent_id'] : $parent_cid; 
			
			$name = $_POST['name'];
			$name = str_replace('&amp;', '"', $name);
			$tag = $_POST['tag'];
			$description = $_POST['description'];
			$meta_title = $_POST['meta_title'];
			$meta_keywords = $_POST['meta_keywords'];
			$meta_description = $_POST['meta_description'];
			
			$tag = trim($tag);
			$tag = sanitize_title($tag);
			
			if (empty($tag) || empty($name))
			{
				$errors[] = '<code>نام دسته</code> و <code>اسلاگ</code> فیلدهای اجباری هستند.';
			}
			else
			{
				if ($tag != $category_data['tag'] && ! preg_match('/(^[a-z0-9_-]+)$/i', $tag))
				{
					$errors[] = 'لطفا مطمئن شو که اسلاگ به درستی تایپ شده (بدون فاصله ، فقط کاراکترهای لاتین a تا z کوچک و بزرگ ، اعداد ، "_" و "-").';
				}

				if ($tag != $category_data['tag'])
				{
					foreach ($all_categories as $id => $c)
					{
						if ($c['tag'] == $tag && $c['id'] != $category_data['id'])
						{
							$errors[] = 'این اسلاگ در حقیقت استفاده می شود برای دسته <strong>'. $c['name'] .'</strong>.';
							break;
						}
					}
				}
			}
			
			if (count($errors) == 0)
			{
				$position = $category_data['position'];

				if ($parent_cid != $category_data['parent_id'])
				{
					$sql_table = ($category_type == 'video') ? 'pm_categories' : 'art_categories';
					// get position of the last category
					$sql = "SELECT MAX(position) as max  
	 						  FROM $sql_table 
							 WHERE parent_id = '". $parent_cid ."'";
					$result = mysql_query($sql);
					$row = mysql_fetch_assoc($result);
					mysql_free_result($result);
					
					$position = ($row['max'] > 0) ? ($row['max'] + 1) : 1;
				}
				
				$meta_tags = '';

				if ($meta_title != '' || $meta_keywords != '' || $meta_description != '')
				{
					$meta_tags = array('meta_title' => str_replace('"', '&quot;', $meta_title),
									   'meta_keywords' => str_replace('"', '&quot;', $meta_keywords),
									   'meta_description' => str_replace('"', '&quot;', $meta_description)
									  );
					$meta_tags = serialize($meta_tags);
				}

				if ($category_type == 'video')
				{
					$sql = "UPDATE pm_categories 
							SET parent_id = '". $parent_cid ."', 
								tag =  '". secure_sql($tag) ."', 
								name = '". secure_sql($name) ."',
								position = '". $position ."', 
								description = '". secure_sql($description) ."',
								meta_tags = '". secure_sql($meta_tags) ."'
							WHERE id = '$category_id'";

					if ( ! ($result = mysql_query($sql)))
					{
						$errors[] = 'در هنگام بروزرسانی دسته خطایی رخ داده.<br /><strong>گزارش mysql :</strong>: '.mysql_error();
					}
					else
					{
						if ($parent_cid != $category_data['parent_id'])
						{
							$sql = "UPDATE pm_categories 
									SET position = position - 1
									WHERE parent_id = '". $category_data['parent_id'] ."' 
									  AND position > '". $category_data['position'] ."'";
							mysql_query($sql);
						}

						$success_edit = true;
						$show_footer_early = false;
					}
				}
				else
				{
					$result = art_update_category($category_id, array('name' => $name,
																	  'tag' => $tag,
																	  'old_tag' => $category_data['tag'],
																	  'parent_id' => $parent_cid,
																	  'position' => $position,
																	  'description' => $description,
																	  'meta_title' => $meta_title,
																	  'meta_keywords' => $meta_keywords,
																	  'meta_description' => $meta_description
																	 )
												);

					if ($result['type'] == 'error')
					{
						$errors[] = $result['msg'];
					}
					else
					{
						$success_edit = true;
						$show_footer_early = false;
					}
				}
			}
			
			$form_data = $_POST;
			$categories_dropdown_options['selected'] = $form_data['category'];
			
		break;
	}
}

?>
<div id="adminPrimary"> 
    <div class="content">
    <?php if ($mode == 'add') : ?>
	<h2>اضافه کردن دسته <?php echo ($category_type == 'video') ? 'Video' : 'Article'; ?> جدید</h2> 
	<?php else : ?>
	<h2>ویرایش کردن <?php echo ($category_type == 'video') ? 'Video' : 'Article'; ?> دسته: <?php echo $form_data['name'];?></h2>
	<?php endif; ?>
	
	<?php if (is_array($errors) && count($errors) > 0) : ?>
	<div class="alert alert-error">
		<?php if (count($errors) > 1) : ?>
		<ul>
		<?php foreach ($errors as $k => $err_msg) : ?>
		<li><?php echo $err_msg; ?></li>
		<?php endforeach; ?>
		</ul>
		<?php else : ?>
		<?php echo $errors[0]; ?>
		<?php endif; ?>
	</div>
	<?php endif;?>
	
	<?php if ($success_add) : ?>
	<div class="alert alert-success">
		دسته <strong><?php echo $name;?></strong> با موفقیت اضافه شد.
	</div>
	<hr />
	<?php if ($category_type == 'video') : ?>
	<a href="cat_manager.php" class="btn">&larr; دسته های ویدئو</a>
	<a href="edit_category.php?mode=add&type=video" class="btn btn-success">اضافه کردن دسته ویدئو دیگر</a>
	<?php else : ?>
	<a href="article_categories.php" class="btn">&larr; دسته های مقاله</a>
	<a href="edit_category.php?mode=add&type=article" class="btn btn-success">اضافه کردن دسته مقاله دیگر &rarr;</a>
	<?php endif; ?>
	
	<?php if ($show_footer_early) : ?>
	    </div><!-- .content -->
	</div><!-- .primary -->
	<?php
	include('footer.php');
	exit();
	endif; // show_footer_early
	?>
	<?php endif; //if ($success_add) : ?>
	
	<?php if ($success_edit) : ?>
	<div class="alert alert-success">
		دسته <strong><?php echo $name;?></strong> آپدیت شد
	</div>
	<hr />
	<?php endif; ?>
	<?php if ($show_footer_early) : ?>
	    </div><!-- .content -->
	</div><!-- .primary -->
	<?php
	include('footer.php');
	exit();
	endif; // show_footer_early
	?>
	
	<form name="edit-category" method="POST" action="edit_category.php?mode=<?php echo $mode; ?>&type=<?php echo $category_type; echo ($mode == 'edit') ? '&cid='. $category_id : '';?>" class="form-horizontal">



<div class="container row-fluid" id="post-page">
    <div class="span9">
    <div class="widget border-radius4 shadow-div">
    <h4>عنوان &amp; توضیحات</h4>
    <div class="control-group">
    <input type="text" name="name" id="must" value="<?php echo str_replace('"', '&quot;', $form_data['name']); ?>" style="width: 99%;" />
    <div class="controls">
    </div>
    </div>
    
    <div class="control-group">
	<div class="pull-right" style=" position: absolute; top: -2px; right: 0px;">
	<span class="btn btn-mini btn-upload"><span id="ButtonPlaceHolder"></span></span>
    <small><div id="fsUploadProgress"></div></small>
    <div id="divStatus"></div>
	<ol id="uploadLog"></ol>
    </div>
	<div class="clear"></div>
    <div class="controls">
    <textarea name="description" cols="100" id="textarea-WYSIWYG" class="tinymce" style="width:100%"><?php echo $form_data['description']; ?></textarea>
    <span class="autosave-message">&nbsp;</span>
    </div>
    </div>
    </div>
    
    </div><!-- .span8 -->

    <div class="span3">

    <div class="widget border-radius4 shadow-div">
    <h4>اسلاگ <i class="icon-info-sign" rel="tooltip" title="مشخص کن که چگونه آدرس  در نوار آدرس تان نشان داده شود.نیاز به اکستنشن نیست "></i></h4>
        <div class="control-group">
        <div class="controls">
            <input name="tag" id="item-slug" type="text" class="default span12" value="<?php echo $form_data['tag']; ?>" size="50" style="width:95%" />
            <small>بروزرسانی این فیلد اثر شدیدی برای صفحات ایندکس شده خواهد داشت.</small>

            <div id="preview_url" class="small-ok">
            <?php 
                if(_SEOMOD == 1) 
                {
            ?>
                 <small>پیش نمایش زنده : <?php echo _URL."/browse-"; ?><span id="preview_complete_url"><?php echo ($form_data['tag'] != '') ? $form_data['tag'] : '';?></span>-1-date.html</small>
            <?php
                } else {
            ?>
                 <small>پیش نمایش زنده : <?php echo _URL."/category.php?cat="; ?><span id="preview_complete_url"></span></small>
            <?php			
                }
            ?>
            </div>
        </div>
        </div>
    </div><!-- .widget -->
            
    <div class="widget border-radius4 shadow-div">
    <h4>دسته والد</h4>
        <div class="control-group">
        <div class="controls">
		<?php echo categories_dropdown($categories_dropdown_options);?>
        </div>
        </div>
    </div><!-- .widget -->

    <div class="widget border-radius4 shadow-div">
    <h4>عنوان متا</h4>
        <div class="control-group">
        <div class="controls">
        	<input type="text" name="meta_title" class="default span12" value="<?php echo str_replace('"', '&quot;', $form_data['meta_title']);?>" />
        </div>
        </div>
    </div><!-- .widget -->
      
    <div class="widget border-radius4 shadow-div">
    <h4>کیوردهای متا</h4>
        <div class="control-group">
        <div class="controls">
            <div class="tagsinput" style="width: 100%;">
            <input type="text" name="meta_keywords" value="<?php echo str_replace('"', '&quot;', $form_data['meta_keywords']);?>" id="tags_addvideo_1" size="50" />
            </div>
        </div>
        </div>
    </div><!-- .widget -->

    <div class="widget border-radius4 shadow-div">
    <h4>توضیحات متا</h4>
        <div class="control-group">
        <div class="controls">
            <textarea name="meta_description" rows="1" style="width:95%" /><?php echo str_replace('"', '&quot;', $form_data['meta_description']);?></textarea>
        </div>
        </div>
    </div><!-- .widget -->
    
    </div>
</div>
<div class="clearfix"></div>

      
    <div id="stack-controls" class="list-controls">
    <div class="btn-toolbar">
        <div class="btn-group">
        	<button type="submit" name="save" value="<?php echo ($mode == 'add') ? 'Submit' : 'Save';?>" class="btn btn-small btn-success btn-strong"><?php echo ($mode == 'add') ? 'Submit' : 'Save';?></button>
	    </div>
    </div>
	</div><!-- #list-controls -->
	
	</form>

	<?php echo csrfguard_form('_admin_catmanager'); ?>
    </div><!-- .content -->
</div><!-- .primary -->
<?php
include('footer.php');
?>