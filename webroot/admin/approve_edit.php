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

$showm = '2';
/*
$load_uniform = 0;
$load_ibutton = 0;
$load_tinymce = 0;
$load_swfupload = 0;
$load_colorpicker = 0;
$load_prettypop = 0;
$load_tagsinput = 0;
*/
$load_scrolltofixed = 1;
$load_chzn_drop = 1;
$load_tagsinput = 1;
$load_uniform = 1;
$load_tinymce = 1;
$load_swfupload = 1;
$load_swfupload_upload_image_handlers = 1;
$_page_title = 'ویرایش پیشنهاد/ویدئو آپلود شده';
include('header.php');

$pm_temp_id = (int) $_GET['id'];
$r = array();
$errors = array();
$success = false;


if ($pm_temp_id)
{
	$sql = "SELECT * 
			FROM pm_temp 
			WHERE id = '". $pm_temp_id ."'";
	$result = mysql_query($sql);
	$r = mysql_fetch_assoc($result);
	mysql_free_result($result);
	
	$r['featured'] = 0;
	$r['restricted'] = 0;
	$r['site_views'] = 0;
	$r['submitted'] = $r['username'];
	$r['url_flv'] = $r['url'];
	$r['direct'] = $r['url'];
	$r['yt_thumb'] = '';
	if (preg_match('/http(s)?:/', $r['thumbnail']))
	{
		$r['yt_thumb'] = $r['thumbnail'];
	}
	elseif ($r['thumbnail'] != '')
	{
		$r['yt_thumb'] = _URL .'/uploads/thumbs/'. $r['thumbnail'];
	}
	$my_tags_str = $r['tags'];
	
}
else
{
	$errors[] = 'Missing video ID';
}

if ($_POST['submit'] != '' && $pm_temp_id)
{
	$inputs = array();
	
	foreach($_POST as $k => $v)
	{
		if ( ! is_array($v))
		{
			$inputs[$k] = stripslashes(trim($v));
		}
		else
		{
			$inputs[$k] = $v;
		}
	}
	
	if (strlen($inputs['video_title']) == 0)
	{
		$errors[] = 'عنوان ویدئو را وارد کن.';
	}
	if ((is_array($inputs['category']) && count($inputs['category']) == 0) || ( ! isset($inputs['category'])))
	{
		$errors[] = 'لطفا برای این ویدئو دسته ای راانتخاب کنید.';
	}
	
	$added = validate_item_date($_POST);
	
	if ($added === false)
	{
		$errors[] = 'Invalid publish date provided.';
		$result = false;
	}
	
	// save and approve video.
	if (count($errors) == 0)
	{
		define('PHPMELODY', true);
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
								'yt_thumb_local' => '',
								'mp4' => '',	
								'direct' => '',	
								'tags' => '',
								'restricted' => 0,
								'allow_comments' => 1 
								);
		$sources = a_fetch_video_sources();
		
		$video_details = array_merge($video_details, $inputs);
		
		$video_details['yt_length'] = ($inputs['yt_min'] * 60) + $inputs['yt_sec'];
		$video_details['added'] = pm_mktime($added);
		$video_details['site_views'] = $inputs['site_views_input'];
		//	generate unique id;
		$found = 0;
		$uniq_id = '';
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
		} while($found == 1);
		
		$video_details['uniq_id'] = $uniq_id;
		
		//	upload or download thumbnail picture.
		if($_FILES['thumb']['name'] != '')
		{
			require_once('img.resize.php');
			$img = new resize_img();
			$img->sizelimit_x = THUMB_W_VIDEO;
			$img->sizelimit_y = THUMB_H_VIDEO;
			
			$new_thumb_name = $uniq_id . "-1";
			
			//	resize image and save it
			if($img->resize_image($_FILES['thumb']['tmp_name']) === false)
			{
				$message .= $img->error;
			}
			else
			{
				$img->save_resizedimage(_THUMBS_DIR_PATH, $new_thumb_name);
			}
			$inputs['yt_thumb'] = _URL."/uploads/thumbs/". $new_thumb_name . "." . strtolower($img->output);
			$video_details['yt_thumb'] = $inputs['yt_thumb'];
			
			// delete uploaded thumbnail
			if ($r['thumbnail'] != '' && $r['source_id'] == $sources['localhost']['source_id'])
			{
				if (file_exists(_THUMBS_DIR_PATH . $r['thumbnail']))
				{
					unlink(_THUMBS_DIR_PATH . $r['thumbnail']);
				}
			}
		}
		
		if ($inputs['yt_thumb'] != '' && $r['thumbnail'] != '' && $r['source_id'] == $sources['localhost']['source_id'])
		{
			// thumbnail URL changed?
			if ($inputs['yt_thumb'] != $r['yt_thumb'])
			{
				// delete uploaded thumbnail
				if (file_exists(_THUMBS_DIR_PATH . $r['thumbnail']))
				{
					unlink(_THUMBS_DIR_PATH . $r['thumbnail']);
				}
			}
		}
		
		// just uploaded
		if ($inputs['yt_thumb_local'] != '')
		{
			$video_details['yt_thumb'] = $input['yt_thumb_local'];
		}
		
		//	fetch information about this video
		if ($inputs['source_id'] != $sources['localhost']['source_id'])
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
			
			require_once( "./src/" . $sources[ $video_details['source_id'] ]['source_name'] . ".php");
			do_main($temp, $video_details['direct'], false);
			
			if($temp['yt_id'] == '')
			{
				$video_details['yt_id'] = substr( md5( time() ), 2, 9);
			}
			else
			{
				$video_details['yt_id'] = $temp['yt_id'];
			}
			
			if ($video_details['source_id'] == $sources['other']['source_id'])
			{
				$video_details['url_flv']	=	$video_details['direct'];
			}
			else
			{
				$video_details['url_flv']	=	$temp['url_flv'];
			}
			
			$video_details['mp4']		=	$temp['mp4'];
			
			if ($video_details['yt_thumb'] == '')
			{
				$video_details['yt_thumb']	= $temp['yt_thumb'];
			}
			
			if ($video_details['yt_length'] == 0)
			{
				$video_details['yt_length']	= (int) $temp['yt_length'];
			}
		}
		else // user uploaded video
		{
			if ($video_details['url_flv'] == '')
			{
				$video_details['url_flv'] = $r['url'];
			}

			if ($r['yt_thumb'] == $video_details['yt_thumb'])
			{
				// rename thumbnail
				$tmp_parts = explode('.', $r['thumbnail']);
				$ext = array_pop($tmp_parts);
				$ext = strtolower($ext);
				
				if ($r['thumbnail'] != '' && file_exists(_THUMBS_DIR_PATH . $r['thumbnail']))
				{
					if (rename(_THUMBS_DIR_PATH . $r['thumbnail'], _THUMBS_DIR_PATH . $uniq_id . '-1.'. $ext))
					{
						$r['thumbnail'] =  $uniq_id . '-1.'. $ext;
					}
				}
				
				$inputs['yt_thumb'] = _THUMBS_DIR . $r['thumbnail'];
				$video_details['yt_thumb'] = $inputs['yt_thumb'];
			}
		}
		
		//	download thumbnail
		if ('' != $video_details['yt_thumb'] && $video_details['source_id'] != $sources['localhost']['source_id'])
		{
			$img = download_thumb($video_details['yt_thumb'], _THUMBS_DIR_PATH, $uniq_id);
		}
		
		// uploaded thumbnail
		if ($video_details['yt_thumb_local'] != '')
		{
			$tmp_parts = explode('/', $video_details['yt_thumb_local']);
			$thumb_filename = array_pop($tmp_parts);
			$tmp_parts = explode('.', $thumb_filename);
			$thumb_ext = array_pop($tmp_parts);
			$thumb_ext = strtolower($thumb_ext);
			$renamed = false;
			
			if (file_exists(_THUMBS_DIR_PATH . $thumb_filename))
			{
				if (rename(_THUMBS_DIR_PATH . $thumb_filename, _THUMBS_DIR_PATH . $uniq_id . '-1.'. $thumb_ext))
				{
					$video_details['yt_thumb'] = $uniq_id . '-1.'. $thumb_ext;
					$renamed = true;
				}
			}

			if ( ! $renamed)
			{
				$video_details['yt_thumb'] = $video_details['yt_thumb_local'];
			}
			
			// delete user-uploaded thumbnail
			if ($r['thumbnail'] != '' && strpos($r['thumbnail'], 'http') === false && file_exists(_THUMBS_DIR_PATH . $r['thumbnail']))
			{
				unlink(_THUMBS_DIR_PATH . $r['thumbnail']);
			}
		}
		
		foreach($video_details as $k => $v)
		{
			$video_details[$k] = str_replace("&amp;", "&", $v);
		}
		
		if (is_array($video_details['category']))
		{
			$video_details['category'] = implode(',', $video_details['category']);
		}

		//	Ok, let's add this video to our database
		$new_video = insert_new_video($video_details, $new_video_id);
		
		if ($new_video !== true)
		{
			$errors[] = '<em>اووه. متاسفانه ویدئو جدید به دیتابیس تان اضافع نشد.</em><br /><strong>گزارش mysql : '.$new_video[0].'<br /><strong>شماره خطا :</strong> '.$new_video[1].'</div>';				
		}
		else
		{
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
			
			//	remove the suggested video from 'pm_temp'
			@mysql_query("DELETE FROM pm_temp WHERE id = '". $pm_temp_id ."'");
			
			$success = 'ویدئو ذخیره و تایید شد.</strong> <a href="'. _URL .'/watch.php?vid='. $uniq_id .'" target="_blank" title="ویدئو را ببین" >تماشا کردن این ویدئو</a>';
		}
		
	}
	
	$r = $inputs;
	$r['category'] = implode(',', $r['category']);
}

?>
<div id="adminPrimary">
    <div class="content">
    <h2>ویرایش پیشنهاد/ویدئو آپلود شده</h2>
	
	<?php if ($success) : ?>
		<div class="alert alert-success">
			<?php echo $success;?>
		</div>
		<hr />
		<a href="approve.php" class="btn">&larr; تایید کردن ویدئوها</a> 
		<a href="modify.php?vid=<?php echo $uniq_id;?>" class="btn">ویرایش</a>
		</div><!-- .content -->
		</div><!-- .primary -->
		<?php
		include('footer.php');
		exit();
		?>	
	<?php endif;?>
	
	<?php if (count($errors) > 0): ?>
		<div class="alert alert-error">
			<ul>
			<?php foreach ($errors as $k => $error) : ?>
			<li><?php echo $error;?></li>
			<?php endforeach; ?>
			</ul>
		</div>
	<?php endif; ?>
	
	<form name="update" enctype="multipart/form-data" action="approve_edit.php?id=<?php echo $pm_temp_id; ?>" method="post" onsubmit="return validateFormOnSubmit(this, 'لطفا فیلدهایی که اجباری هستند را تکمیل کنید (برجسته شده اند)')">
	<div class="container row-fluid" id="post-page">
    <div class="span9">
    <div class="widget border-radius4 shadow-div">
    <h4>توضیح &amp; عنوان</h4>
    <div class="control-group">
    <input name="video_title" type="text" id="must" value="<?php echo htmlspecialchars($r['video_title']); ?>" style="width: 99%;" />
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
    </div>
    
    <div class="controls">
    <textarea name="description" cols="100" id="textarea-WYSIWYG" class="tinymce" style="width:100%"><?php echo $r['description']; ?></textarea>
    <span class="autosave-message">&nbsp;</span>
    </div>
    </div>
	
	 </div><!-- .span8 -->
	 <div class="span3">
	 	
		<div class="widget border-radius4 shadow-div">
		<div class="pull-right"><span class="btn btn-mini btn-upload-widget"><span id="thButtonPlaceholder"></span></span></div>
		<h4>تامبنیل</h4>
            <div class="control-group container-fluid">
            <div class="controls row-fluid">
			<small><div id="thUploadProgress"></div></small>
            <div class="pm-swf-upload">
                <ol id="uploadThumbLog"></ol>
            </div>            
            <div id="showThumb">
			<?php
            if (($r['source_id'] == 0 || $r['source_id'] == 1 || $r['source_id'] == 2) && strpos($r['yt_thumb'], 'http') === false && $r['yt_thumb'] != '') 
			{
                $r['yt_thumb'] = _URL."/uploads/thumbs/".$r['yt_thumb'];
			}
			if (empty($r['yt_thumb'])) : ?>
            <a href="#" id="show-thumb" rel="tooltip" title="برای مشخص کردن آدرس تامبنیل سفارشی اینجا کلیک کنید"><img src="img/no-thumbnail.jpg" width="139" height="113" alt="" /></a>
            <?php else : ?>
            <a href="#" id="show-thumb" rel="tooltip" title="برای مشخص کردن آدرس تامبنیل سفارشی اینجا کلیک کنید"><img src="<?php echo $r['yt_thumb']; ?>" id="must" style="display:block;min-width:120px;width:100%;min-height:80px; no-repeat center center;" /></a>
            <?php endif; ?>
            <div class="">
                <div id="show-opt-thumb">
                <br />
                <input type="text" name="yt_thumb" id="must" value="<?php echo $r['yt_thumb']; ?>" class="bigger span10" placeholder="http://" /> <i class="icon-info-sign" rel="tooltip" data-position="top" title="بعد از اینکه شما دکمه 'ارسال' را زدید تامبنیل رفرش خواهد شد"></i>
                </div>
            </div><!-- .span8 -->
            </div>
			<div class="">
            </div><!-- .span4 -->
			
            </div><!-- .controls .row-fluid -->
            </div>
        </div><!-- .widget -->
		
		<div class="widget border-radius4 shadow-div">
		<h4>دسته</h4>
            <div class="control-group">
            <div class="controls">
            <input type="hidden" name="categories_old" value="<?php echo $r['category'];?>"  />
            <?php 
			$categories_dropdown_options = array(
											'attr_name' => 'category[]',
											'attr_id' => 'main_select_category must',
											'attr_class' => 'category_dropdown span12',
											'select_all_option' => false,
											'spacer' => '&mdash;',
											'selected' => explode(',', $r['category']),
											'other_attr' => 'multiple="multiple"'
											);
			echo categories_dropdown($categories_dropdown_options);
            ?>
            </div>
			<a href="#" id="inline_add_new_category" />+ ایجاد دسته جدید</a>
			<div id="inline_add_new_category_form" class="hide">
				<input name="add_category_name" type="text" placeholder="Category name" id="add_category_name" />
				<input name="add_category_slug" type="text" placeholder="Slug" /> <a href="#" rel="tooltip" title="اسلاگ ها در آدرس استفاده می شوند و می توانند شامل اعداد ، حروف ، خط تیره و خط زیر باشند."><i class="icon-info-sign" rel="tooltip" title="اسلاگ ها در آدرس استفاده می شوند و می توانند شامل اعداد ، حروف ، خط تیره و خط زیر باشند."></i></a>
				<label>ایجاد شده در(<em>اختیاری</em>)</label>
				<?php 
					$categories_dropdown_options = array(
											'first_option_text' => '&ndash; Parent Category &ndash;', 
											'first_option_value' => '-1',
											'attr_name' => 'add_category_parent_id',
											'attr_id' => '',
											'attr_class' => '',
											'select_all_option' => true,
											'spacer' => '&mdash;'
											);
					echo categories_dropdown($categories_dropdown_options); 
				?>
				<br />
				<button name="add_category_submit_btn" value="Add category" class="btn btn-mini btn-normal" />اضافه کردن دسته جدید</button>
				<span id="add_category_response"></span>
			</div>
            </div>
		</div><!-- .widget -->
		
        <div class="widget border-radius4 shadow-div">
        <h4>انتشار</h4>
			<?php
            if($r['yt_length'] > 0) {	
                $yt_minutes = intval($r['yt_length'] / 60); 
                $yt_seconds = intval($r['yt_length'] % 60); 
            } else {
                $yt_minutes = 0;
                $yt_seconds = 0;
            }
            ?>
            <div class="control-group">
            <label class="control-label" for="">مدت :<span id="value-yt_length"><strong><?php echo sec2min($r['yt_length']);?></strong></span> <a href="#" id="show-duration">ویرایش کردن</a></label>
            <div class="controls" id="show-opt-duration">
            <input type="text" name="yt_min" id="yt_length" value="<?php echo $yt_minutes; ?>" size="4" class="smaller-select" /> <small>دقیقه.</small>
            <input type="text" name="yt_sec" id="yt_length" value="<?php echo $yt_seconds; ?>" size="3" class="smaller-select" /> <small>ثانیه.</small>
            <input type="hidden" name="yt_length" id="yt_length" value="<?php echo trim(($yt_minutes * 60) + $yt_seconds); ?>" />
            </div>
            </div>
            
            <div class="control-group">
            <label>ویژه : <span id="value-featured"><strong><?php if($r['featured'] == 1) { echo 'yes'; } else { echo 'no'; } ?></strong></span> <a href="#" id="show-featured">ویرایش کردن</a></label>
            <div class="controls" id="show-opt-featured">
                <label><input type="checkbox" name="featured" id="featured" value="1" <?php if($r['featured'] == 1) echo 'checked="checked"';?> /> بله به عنوان برجسته علامت بزن</label>
            </div>
            </div>

            <div class="control-group">
            <label class="control-label" for="">ثبت نام های الزامی : <span id="value-register"><strong><?php if($r['restricted'] == 1) { echo 'yes'; } else { echo 'no'; } ?></strong></span> <a href="#" id="show-visibility">ویرایش کردن</a></label>
            <div class="controls" id="show-opt-visibility">
                <label class="checkbox inline"><input type="radio" name="restricted" id="restricted" value="1" <?php if ($r['restricted'] == 1) echo 'checked="checked"'; ?> />بله</label>
				<label class="checkbox inline"><input type="radio" name="restricted" id="restricted" value="0" <?php if ($r['restricted'] == 0) echo 'checked="checked"'; ?> />خیر</label>
            </div>
            </div>
			<div class="control-group">
            <label class="control-label" for="">نظردهی ازاد باشد : <span id="value-comments"><strong><?php echo ($r['allow_comments'] == '1') ? 'yes' : 'no';?></strong></span> <a href="#" id="show-comments">ویرایش کردن</a></label>
            <div class="controls" id="show-opt-comments">
                <label class="checkbox inline"><input type="radio" name="allow_comments" id="allow_comments" value="1" <?php echo ($r['allow_comments'] == '1') ? 'checked="checked"' : '';?> /> بله</label> 
                <label class="checkbox inline"><input type="radio" name="allow_comments" id="allow_comments" value="0" <?php echo ($r['allow_comments'] != '1') ? 'checked="checked"' : '';?> /> خیر</label>
            </div>
            </div>

            <div class="control-group">
            <label class="control-label" for="">نمایش ها<span id="value-views"><strong><?php echo $r['site_views'];?></strong></span> <a href="#" id="show-views">ویرایش کردن</a></label>
            <div class="controls" id="show-opt-views">
            <input type="hidden" name="site_views" value="<?php echo $r['site_views'];?>" />
            <input type="text" name="site_views_input" id="site_views_input" value="<?php echo $r['site_views']; ?>" size="10" class="bigger span4" />
            </div>
            </div>

            <div class="control-group">
            <label class="control-label" for="">ارسال شده توسط : <span id="value-submitted"><strong><?php echo htmlspecialchars($r['submitted']); ?></strong></span> <a href="#" id="show-user">ویرایش کردن</a></label>
            <div class="controls" id="show-opt-user">
            <input type="text" name="submitted" id="submitted" value="<?php echo htmlspecialchars($r['submitted']); ?>" class="bigger span4" />
            </div>
            </div>

            <div class="control-group">
            <label class="control-label" for="">انتشار یافته : <span id="value-publish"><strong><?php echo date("M d, Y", $r['added']);?></strong></span> <a href="#" id="show-publish">ویرایش کردن</a></label>
            <div class="controls" id="show-opt-publish">
            <?php //echo ($_POST['date_month'] != '') ? show_form_item_date( pm_mktime($_POST) ) : show_form_item_date($r['date']);	?>
            <?php echo show_form_item_date($r['added']);?>
            </div>
            </div>
        </div><!-- .widget -->

		<div class="widget border-radius4 shadow-div">
		<h4>برچسب ها</h4>
            <div class="control-group">
            <div class="controls">
                <div class="tagsinput" style="width: 100%;">
                <input type="text" name="tags" value="<?php echo $my_tags_str; ?>"  id="tags_addvideo_1" />
                </div>
            </div>
            </div>
        </div><!-- .widget -->
    </div>
    
</div>
<div class="clearfix"></div>


<input type="hidden" name="categories_old" value="<?php echo $r['category'];?>" />
<input type="hidden" name="language" value="1" />
<input type="hidden" name="source_id" value="<?php echo $r['source_id']; ?>" />
<input type="hidden" name="user_id" value="<?php echo $r['user_id'];?>" />
<input type="hidden" name="url_flv" value="<?php echo $r['url_flv']; ?>" />
<input type="hidden" name="direct" value="<?php echo $r['direct']; ?>" />
    
<div id="stack-controls" class="list-controls">
<div class="btn-toolbar">
    <div class="btn-group">
	<button name="submit" type="submit" value="Save &amp; Approve" class="btn btn-small btn-success btn-strong">تایید &amp; ذخیره</button>
    </div>
</div>
</div><!-- #list-controls -->
</form>

    </div><!-- .content -->
</div><!-- .primary -->
<?php
$uniq_id = substr(md5($_POST['uniq_id'] . time()), 1, 8); // temporary value; defined specifically for upload_image.php
include('footer.php');
?>	