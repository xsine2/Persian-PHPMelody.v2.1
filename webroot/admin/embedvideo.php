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
*/
$load_scrolltofixed = 1;
$load_chzn_drop = 1;
$load_tagsinput = 1;
$load_uniform = 1;
$load_tinymce = 1;
$load_swfupload = 1;
$load_swfupload_upload_image_handlers = 1;
$_page_title = 'جاسازی کردن ویدئو جدید';
include('header.php');

define('PHPMELODY', true);

$step = 2;

$inputs = array('source_id' => 0,
				'language' => 1,
				'age_verification' => 0,
				'featured' => 0,
				'restricted' => 0,
				'allow_comments' => 1 
				);

$modframework->trigger_hook('admin_embed_top');
if ($_POST['submit'] != '')
{
	$return_msg = '';
	
	foreach ($_POST as $k => $v)
	{
		if ( ! is_array($_POST[$k]))
			$_POST[$k] = stripslashes(trim($v));
	}
	
	if (strlen($_POST['video_title']) == 0)
	{
		$return_msg = 'عنوان ویدئو را وارد کن';
	}
	else if (strlen($_POST['embed_code']) == 0)
	{
		$return_msg = 'لطفا برای این ویدئو یک کد جاسازی اختصاص بده.';
	}
	else if ((is_array($_POST['category']) && count($_POST['category']) == 0) || ( ! isset($_POST['category'])))
	{
		$return_msg = 'لطفا برای این دسته یک دسته انتخاب کن.';
	}
	$modframework->trigger_hook('admin_embed_add_start');
	if ($return_msg == '')
	{
		$video_details = array(	'uniq_id' => '',
								'video_title' => '',
								'description' => '',
								'yt_id' => '',
								'yt_length' => '',
								'category' => '',
								'submitted' => '',
								'source_id' => 0,
								'language' => 1,
								'age_verification' => 0,
								'url_flv' => '',
								'yt_thumb' => '',
								'yt_thumb_local' => '',
								'mp4' => '',
								'direct' => '',
								'tags' => '', 
								'featured' => 0,
								'added' => '',
								'restricted' => 0,
								'allow_comments' => 1 
							);
						
		$video_details['submitted']   = $userdata['username'];
		$video_details['featured'] 	  = (int) $_POST['featured'];
		$video_details['description'] = $_POST['description'];
		$video_details['yt_thumb'] 	  = $_POST['yt_thumb'];
		$video_details['yt_thumb_local'] = $_POST['yt_thumb_local'];
		$video_details['video_title'] = $_POST['video_title'];
		$video_details['category'] 	  = (is_array($_POST['category'])) ? implode(',', $_POST['category']) : $_POST['category'];
		$video_details['tags'] 		  = $_POST['tags'];
		$video_details['embed_code']  = $_POST['embed_code'];
		$video_details['direct']	  = $_POST['direct'];
		$video_details['restricted']  = $_POST['restricted'];
		$video_details['yt_length']	  = ($_POST['yt_min'] * 60) + $_POST['yt_sec'];
		$video_details['meta']		  = $_POST['meta'];
		
		$added = validate_item_date($_POST);
		if ($added === false)
		{
			$return_msg .= 'تاریخ غیرمعتبر <br />';
		}
		else
		{
			$video_details['added'] = pm_mktime($added);
		}
		
		$video_details['embed_code'] = str_replace(array("'", "\n", "\r"), array('"', '', ''), $video_details['embed_code']);
		
		//	remove extra html tags
		$video_details['embed_code'] = strip_tags($video_details['embed_code'], '<iframe><embed><object><param><video><script>');
		
		//	remove left-overs
		if (strpos($video_details['embed_code'], '<object') !== false)
		{
			$video_details['embed_code'] = preg_replace('/\/object>(.*)/i', '/object>', $video_details['embed_code']);
		}

		//	replace width, height and wmode values with variables
		$video_details['embed_code'] = preg_replace('/width="([0-9]+)"/i', 'width="%%player_w%%"', $video_details['embed_code']);
		$video_details['embed_code'] = preg_replace('/height="([0-9]+)"/i', 'height="%%player_h%%"', $video_details['embed_code']);
		$video_details['embed_code'] = preg_replace('/value="(window|opaque|transparent)"/i', 'value="%%player_wmode%%"', $video_details['embed_code']);
		$video_details['embed_code'] = preg_replace('/wmode="(.*?)"/i', 'wmode="%%player_wmode%%"', $video_details['embed_code']);
		$video_details['embed_code'] = preg_replace('/width=([0-9]+)/i', 'width=%%player_w%%', $video_details['embed_code']);
		$video_details['embed_code'] = preg_replace('/height=([0-9]+)/i', 'height=%%player_h%%', $video_details['embed_code']);
		
		$video_details['embed_code'] = secure_sql($video_details['embed_code']);
		
		 
		
		//	generate unique id;
		$found = 0;
		$uniq_id = '';
		$i = 0;
		do
		{
			$found = 0;
			$str = md5(time());
			$uniq_id = substr($str, 0, 9);
			if(count_entries('pm_videos', 'uniq_id', $uniq_id) > 0)
				$found = 1;
		} while($found === 1);

		$video_details['uniq_id'] = $uniq_id;
		$video_details['yt_id'] = $uniq_id;
		$modframework->trigger_hook('admin_embed_uploadthumb');
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
				$return_msg .= $img->error;
			}
			else
			{
				$img->save_resizedimage(_THUMBS_DIR_PATH, $new_thumb_name);
			}
			$video_details['yt_thumb'] = $new_thumb_name . "." . strtolower($img->output);
		}
		else if ($video_details['yt_thumb_local'] != '')
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
		}
		else if (strlen($video_details['yt_thumb']) > 0)
		{
			//	download thumbnail
			require_once( "./src/other.php");

			$img = download_thumb($video_details['yt_thumb'], _THUMBS_DIR_PATH, $uniq_id);
			if($img === false)
			{
				$return_msg .= "در هنگام دانلود این تامبنیل خطایی رخ داده.<br />";
			}
		}

		if (strlen($return_msg) == 0)
		{
			
		do {
				$dobreak = false;
				$modframework->trigger_hook('admin_embed_insertvideo_pre');
				if($dobreak) break;
			$new_video = insert_new_video($video_details, $new_video_id);
				$modframework->trigger_hook('admin_embed_insertvideo_post');
				if($dobreak) break;
			if($new_video !== true)
			{
				$return_msg = "<em>یک مشکلی رخ داده! ویدئو جدید به دیتابیس تان اضافه نشد!</em><br /><strong>گزارش mysql :</strong> ".$new_video[0]."<br /><strong>Error Number:</strong> ".$new_video[1]."<br />";
			}
			else
			{
				//	tags?
				if($video_details['tags'] != '')
				{
					$tags = explode(",", $_POST['tags']);
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
					$modframework->trigger_hook('admin_embed_insert_final');
				$step = 3;
				$return_msg = 'ویدئو با موفقیت ارسال شد.';
				}
			}
			while(false);
		}
	}	//	endif $return_msg == ''

	$inputs = $_POST;
}

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
            <p>این صفحه به شما اجازه می دهد که  محتوی را فقط با استفاده از کد جاسازی شده اضافه کنید. تگ های html اجازه داده شده &lt;iframe&gt; &lt;embed&gt; &lt;object&gt; &lt;param&gt; &lt;video&gt; and &lt;script&gt;</p>
            <p>برای اختصاصی دادن یک تامبنیل به این مطلب ارسالی ، بر روی تصویر تامبنیل کلیک کنید.</p>
            <p>شما همچنین یک زوج گزینه های انتشار یافته دارید همانند مشخص کردن یک زمان و تاریخ نشر در آینده. مطالب ارسالی همچنین می تواند از کاربران ثبت نام نشده بصورت خصوصی ساخته شود. این یک راه عالی است برای افزایش میزان ثبت نام.</p>
            <p>توجه : ما بسیار توصیه می کنیم به شما که در طی  هر مطلب ارسالی ویدئو را اضافه کنید.</p>
            <p></p>
            <p>یاد بگیر که چگونه استفاده بکنی از <strong>فیلدهای سفارشی</strong>: <a href="http://help.phpmelody.com/how-to-use-the-custom-fields/" target="_blank">http://help.phpmelody.com/how-to-use-the-custom-fields/</a></p>
            </div>
          </div>
        </div> <!-- /tabbable -->
        </div><!-- .span12 -->
    </div><!-- /help-assist -->
    <div class="content">
	<a href="#" id="show-help-assist">راهنما</a>
	<h2>جاسازی کردن ویدئو جدید</h2>

<?php 

	if ($step == 2)
	{
		if (strlen($return_msg) > 0)
		{
			echo '<div class="alert alert-error">'. $return_msg .'</div>';
		}
?>	
	
	<form method="post" enctype="multipart/form-data" action="embedvideo.php" name="embed_video" onsubmit="return validateFormOnSubmit(this, 'Please fill in the required fields (highlighted)')">

<div class="container row-fluid" id="post-page">
    <div class="span9">
    <div class="widget border-radius4 shadow-div">
    <h4>توضیحات &amp; عنوان</h4>
    <div class="control-group">
    <input name="video_title" type="text" id="must" value="<?php echo $inputs['video_title']; ?>" style="width: 99%;" />
    <div class="permalink-field">

	<?php if (_SEOMOD) : ?>
		<strong>لینک ثابت:</strong> <?php echo _URL .'/';?><input class="permalink-input" type="text" name="video_slug" placeholder="<?php echo urldecode($video_details['video_slug']);?>" value="<?php echo urldecode($video_details['video_slug']);?>" /><?php echo '_UniqueID.html';?>
	<?php endif; ?>
	
	
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
	<textarea name="description" cols="100" id="textarea-WYSIWYG" class="tinymce" style="height: 100px;width:100%"><?php echo $inputs['description']; ?></textarea>
    <span class="autosave-message">&nbsp;</span>
    </div>
    </div>

	<div class="widget border-radius4 shadow-div">
	<h4>کد جاسازی شده</h4>
    <div class="control-group">
    <div class="controls">
    <!-- 
    <strong>Direct link to video page</strong><br /><small>Optional field</small>
    <input type="text" name="direct" value="<?php echo $inputs['direct']; ?>" style="width: 500px;" />
    -->
    <textarea name="embed_code" id="must" rows="2" class="textarea-embed"><?php echo $inputs['embed_code']; ?></textarea>
    <span class="help-block">تگ های html مورد قبول : <strong>&lt;iframe&gt;</strong>  <strong>&lt;embed&gt;</strong> <strong>&lt;object&gt;</strong> <strong>&lt;param&gt;</strong> <strong>&lt;video&gt;</strong> and <strong>&lt;script&gt;</strong></span>
	</div>
    </div>
	</div>
	
	<div class="widget border-radius4 shadow-div" id="custom-fields">
	<h4>فیلدهای سفارشی <a href="http://help.phpmelody.com/how-to-use-the-custom-fields/" target="_blank"><i class="icon-question-sign"></i></a></h4>
    	<div class="control-group">
		<div class="row-fluid">
			<div class="span3"><strong>نام</strong></div>
			<div class="span9"><strong>مقدار</strong></div>
		</div>
		<?php if (count($_POST['meta']) > 0) :
				foreach ($_POST['meta'] as $meta_id => $meta) : 
					$meta['meta_key'] = $meta['key'];
					$meta['meta_value'] = $meta['value'];
					
					echo admin_custom_fields_row($meta_id, $meta);
				endforeach;
			endif; ?>
		<?php echo admin_custom_fields_add_form(0, IS_VIDEO); ?>
		
		</div>
	</div>
    
    </div><!-- .span8 -->
    <div class="span3">
		<div class="widget border-radius4 shadow-div">
        <div class="pull-right"><span class="btn btn-mini btn-upload-widget"><span id="thButtonPlaceholder"></span></span></div>
		<h4>تامبنیل</h4>
            <div class="control-group">
            <div class="controls row-fluid">
			<small><div id="thUploadProgress"></div></small>
            <div class="pm-swf-upload">
                <ol id="uploadThumbLog"></ol>
            </div>            
            <div id="showThumb">
            <?php if (empty($_POST['yt_thumb_local'])) : ?>
            <a href="#" id="show-thumb" rel="tooltip" title="Click here to specify a custom thumbnail URL" class="pm-sprite no-thumbnail"></a>
            <?php else : ?>
            <a href="#" id="show-thumb" rel="tooltip" title="Click here to specify a custom thumbnail URL"><img src="<?php echo $_POST['yt_thumb_local']; ?>" id="must" style="display:block;min-width:120px;width:100%;min-height:80px; no-repeat center center;" /></a>
            <?php endif; ?>
            <div class="">
                <div id="show-opt-thumb">
                <br />
                <input type="text" name="yt_thumb" value="<?php echo $_POST['yt_thumb_local']; ?>" class="bigger span10" placeholder="http://" /> <i class="icon-info-sign" rel="tooltip" data-position="top" title="The thumbnail will refresh after you hit the 'Submit' button."></i>
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
            <input type="hidden" name="categories_old" value="<?php echo $inputs['category'];?>"  />
            <?php 
			$categories_dropdown_options = array(
											'attr_name' => 'category[]',
											'attr_id' => 'main_select_category must',
											'attr_class' => 'category_dropdown span12',
											'select_all_option' => false,
											'spacer' => '&mdash;',
											'selected' => explode(',', $inputs['category']),
											'other_attr' => 'multiple="multiple"'
											);
			echo categories_dropdown($categories_dropdown_options);
            ?>
            </div>
			<a href="#" id="inline_add_new_category" />+ اضافه کردن دسته جدید</a>
			<div id="inline_add_new_category_form" class="hide">
				<input name="add_category_name" type="text" placeholder="Category name" id="add_category_name" />
				<input name="add_category_slug" type="text" placeholder="Slug" /> <a href="#" rel="tooltip" title="اسلاگ ها در آدرس استفاده می شوند و می توانند شامل اعداد ، حروف ، خط تیره و خط زیر باشند."><i class="icon-info-sign" rel="tooltip" title="Sاسلاگ ها در آدرس استفاده می شوند و می توانند شامل اعداد ، حروف ، خط تیره و خط زیر باشند."></i></a>
				<label>ایجاد کردن در (<em>اختیاری</em>)</label>
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
            <div class="control-group">
			<?php
            if($inputs['yt_min'] > 0 || $inputs['yt_sec'] > 0) {	
                $yt_minutes = $inputs['yt_min'];
                $yt_seconds = $inputs['yt_sec'];
				$inputs['yt_length'] = trim(($yt_minutes * 60) + $yt_seconds);
            } else {
                $yt_minutes = 0;
                $yt_seconds = 0;
            }
            ?>
            <label class="control-label" for="">مدت: <span id="value-yt_length"><strong><?php echo sec2min($inputs['yt_length']);?></strong></span> <a href="#" id="show-duration">ویرایش کردن</a></label>
            <div class="controls" id="show-opt-duration">
            <input type="text" name="yt_min" id="yt_length" value="<?php echo $yt_minutes; ?>" size="4" class="smaller-select" /> <small>دقیقه.</small>
            <input type="text" name="yt_sec" id="yt_length" value="<?php echo $yt_seconds; ?>" size="3" class="smaller-select" /> <small>ثانیه.</small>
            <input type="hidden" name="yt_length" id="yt_length" value="<?php echo trim(($yt_minutes * 60) + $yt_seconds); ?>" />
            </div>
            </div>

			<div class="control-group">
            <label class="control-label" for="">نظرات: <span id="value-comments"><strong><?php echo ($inputs['allow_comments'] == 1) ? 'allowed' : 'closed';?></strong></span> <a href="#" id="show-comments">Edit</a></label>
            <div class="controls" id="show-opt-comments">
                <label><input name="allow_comments" id="allow_comments" type="checkbox" value="1" <?php if ($inputs['allow_comments'] == 1) echo 'checked="checked"';?> /> Allow comments on this video</label>
            </div>
            </div>
			
            <div class="control-group">
            <label>ویژه: <span id="value-featured"><strong><?php echo ($inputs['featured'] == 1) ? 'yes' : 'no';?></strong></span> <a href="#" id="show-featured">Edit</a></label>
            <div class="controls" id="show-opt-featured">
                <label><input type="checkbox" name="featured" id="featured" value="1" <?php if($inputs['featured'] == 1) echo 'checked="checked"';?> /> Yes, mark as featured</label>
            </div>
            </div>

            <div class="control-group">
            <label class="control-label" for="">لازم بودن ثبت نام <span id="value-register"><strong><?php echo ($inputs['restricted'] == 1) ? 'yes' : 'no';?></strong></span> <a href="#" id="show-visibility">Edit</a></label>
            <div class="controls" id="show-opt-visibility">
                <label class="checkbox inline"><input type="radio" name="restricted" id="restricted" value="1" <?php echo ($inputs['restricted'] == 1) ? 'checked="checked"' : '';?> /> Yes</label> 
                <label class="checkbox inline"><input type="radio" name="restricted" id="restricted" value="0" <?php echo ($inputs['restricted'] != 1) ? 'checked="checked"' : '';?> /> No</label>
            </div>
            </div>
			
            <div class="control-group">
            <label class="control-label" for=""> انتشار: <span id="value-publish"><strong>بلافاصله</strong></span> <a href="#" id="show-publish"> ویرایش</a></label>
            <div class="controls" id="show-opt-publish">
            <?php echo ($_POST['date_month'] != '') ? show_form_item_date( pm_mktime($_POST) ) : show_form_item_date();	?>
            </div>
            </div><?php 
  		$modframework->trigger_hook('admin_embed_publishfields');
  ?>
        </div><!-- .widget -->

		<div class="widget border-radius4 shadow-div">
		<h4>تگ ها</h4>
            <div class="control-group">
            <div class="controls">
                <div class="tagsinput" style="width: 100%;">
                <input type="text" name="tags" value="<?php echo $inputs['tags']; ?>" id="tags_addvideo_1" />
                </div>
            </div>
            </div>
        </div><!-- .widget -->
		<?php 
  		$modframework->trigger_hook('admin_embed_input');
  ?>
    </div>
    
</div>
<div class="clearfix"></div>
<input type="hidden" name="language" value="1" />
<input type="hidden" name="source_id" value="0" />
<input type="hidden" name="age_verification" value="0" />

<div id="stack-controls" class="list-controls">
<div class="btn-toolbar">
    <div class="btn-group">
	<button type="submit" name="submit" value="Submit" class="btn btn-small btn-success btn-strong">ویدئو جاسازی شده</button>
	</div>
</div>
</div><!-- #list-controls -->
</form>
<?php
	}	//	endif step == 2
	else if ($step == 3)
	{
		echo '<div class="alert alert-success">'. $return_msg .'</div>';
		
		echo '<br />';
		echo '<div class="btn-group"><input name="embed_new" type="button" value="&larr; Embed another video" onClick="location.href=\'embedvideo.php\'" class="btn btn-small" />';
		echo '<input name="add_new" type="button" value="Add / upload new video" onClick="location.href=\'addvideo.php?step=1\'" class="btn btn-small" />';
		echo '<input name="import_new" type="button" value="Import from YouTube" onClick="location.href=\'import.php\'" class="btn btn-small" />';
		echo '</div>';
	}
?>	
    </div><!-- .content -->
</div><!-- .primary -->
<?php
include('footer.php');
?>