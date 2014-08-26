<?php
$showm = '2';
/*
$load_uniform = 0;
$load_ibutton = 0;
$load_tinymce = 0;
$load_swfupload = 0;
$load_colorpicker = 0;
$load_prettypop = 0;
*/
if(isset($_GET['step'])) {
$load_scrolltofixed = 1;
$load_chzn_drop = 1;
$load_tagsinput = 1;
$load_uniform = 1;
$load_tinymce = 1;
$load_swfupload = 1;
$load_swfupload_upload_image_handlers = 1;
}
$_page_title = 'افزودن ویدیو';
include('header.php');

define('PHPMELODY', true);

$message = '';
$allowed_ext = array('.flv', '.mp4', '.mov', '.wmv', '.divx', '.avi', '.mkv', '.asf', '.wma', '.mp3', '.m4v', '.m4a', '.3gp', '.3g2');

$step = (int) $_GET['step'];
if($step == '')
	$step = 1;


if($step == 2 && isset($_POST['Submit']))
{
	if(trim($_POST['url']) == '')
	{
		$step = 1;
		$message = '<div class="alert alert-danger">لطفا یک آدرس معتبر وارد کنید.</div>';	
	}
}


function add_video_form($video_details = array())
{
	global $modframework;
	$categories_dropdown_options = array(
									'attr_name' => 'category[]',
									'attr_id' => 'main_select_category',
									'select_all_option' => false,
									'spacer' => '&mdash;',
									'selected' => 0,
									'other_attr' => 'multiple="multiple"'
									);

	if($video_details['url_flv'] == '') {
		$video_lookup = '<div class="alert alert-warning"><strong>متاسفانه ویدئویی پیدا نشد </strong> لطفا از دوباره تلاش کن یا یا منبع ویدئوی دیگری را استفاده کن.</div>';
	} else {
		$video_lookup = '<div class="alert alert-success">ویدیو با موفقیت پیدا شد .</div>';
	}
	
	if ($video_details['video_title'] != '')
	{
		$video_details['video_slug'] = sanitize_title($video_details['video_title']);
	}
	
// Generate a video title from the file name
if(isset($_POST['filename']) && $_POST['filename'] != '')
{
	$generated_title = basename($_POST['filename']);

	$uploaded_file = pathinfo($_POST['filename']);
	$uploaded_file_name =  basename($_POST['filename'],'.'.$uploaded_file['extension']);
	$unwanted_chars = array("-", "_", ",","'",".","(",")","[","]","*","{","}","  ","   ");
	$video_details['video_title'] = ucwords(str_replace($unwanted_chars, " ", $uploaded_file_name));
}
?>
<form method="post" enctype="multipart/form-data" action="addvideo.php?step=3" name="addvideo_form_step2" onsubmit="return validateFormOnSubmit(this, 'تمام فیلدهای ضروری را پر کنید و یا مطمن شوید که لینکی که در مرحله اول وارد کردید صحیح است .')">

<div class="container row-fluid" id="post-page">
    <div class="span9">
	<?php echo $video_lookup; ?>
    <div class="widget border-radius4 shadow-div">
    <h4>عنوان &amp; توضیح</h4>
    <div class="control-group">
    <input name="video_title" type="text" id="must" value="<?php echo str_replace('"', '&quot;', $video_details['video_title']); ?>" style="width: 99%;" />
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
	<textarea name="description" cols="100" id="textarea-WYSIWYG" class="tinymce" style="width:100%"><?php echo $video_details['description']; ?></textarea>
    <span class="autosave-message">&nbsp;</span>
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
		<h4>تصویر ویدیو</h4>
            <div class="control-group container-fluid">
            <div class="controls row-fluid">
			<small><div id="thUploadProgress"></div></small>
            <div class="pm-swf-upload">
                <ol id="uploadThumbLog"></ol>
            </div>            
            <div id="showThumb">
			<?php			
			if (($video_details['source_id'] == 0 || $video_details['source_id'] == 1 || $video_details['source_id'] == 2) && strpos($video_details['yt_thumb'], 'http') === false && $video_details['yt_thumb'] != '') 
			{
                $video_details['yt_thumb'] = _URL."/uploads/thumbs/".$video_details['yt_thumb'];
			}
			if (empty($video_details['yt_thumb']) && empty($video_details['yt_thumb_local'])) : ?>
            <a href="#" id="show-thumb" rel="tooltip" title="برای تعیین یک تامبنیل جدید اینجا کلیک کن" class="pm-sprite no-thumbnail"></a>
            <?php else : ?>
            <a href="#" id="show-thumb" rel="tooltip" title="برای تعیین یک آدرس تامبنیل سفارشی اینجا کلیک کن"><img src="<?php echo $video_details['yt_thumb']; ?>" id="must" style="display:block;min-width:120px;width:100%;min-height:80px; no-repeat center center;" /></a>
            <?php endif; ?>
            <div class="">
                <div id="show-opt-thumb">
                <br />
                <input type="text" name="yt_thumb" value="<?php echo $video_details['yt_thumb']; ?>" class="bigger span10" placeholder="http://" /> <i class="icon-info-sign" rel="tooltip" data-position="top" title="تامبنیل بعد از اینکه گارسالگ را زدی رفرش خواهد شد"></i>
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
            <input type="hidden" name="categories_old" value="<?php echo $video_details['category'];?>"  />
            <?php 
			$categories_dropdown_options = array(
											'attr_name' => 'category[]',
											'attr_id' => 'main_select_category must',
											'attr_class' => 'category_dropdown span12',
											'select_all_option' => false,
											'spacer' => '&mdash;',
											'selected' => explode(',', $video_details['category']),
											'other_attr' => 'multiple="multiple"'
											);
			echo categories_dropdown($categories_dropdown_options);
            ?>
            </div>
			<a href="#" id="inline_add_new_category" />+ ایجاد دسته جدید</a>
			<div id="inline_add_new_category_form" class="hide">
				<input name="add_category_name" type="text" placeholder="Category name" id="add_category_name" />
				<input name="add_category_slug" type="text" placeholder="Slug" /> <a href="#" rel="tooltip" title="اسلاگ ها استفاده می شوند در  آدرس و فقط شامل اعدا ، حروف خط تیره و خط زیری. "><i class="icon-info-sign" rel="tooltip" title="اسلاگ ها استفاده می شوند در  آدرس و فقط شامل اعدا ، حروف خط تیره و خط زیری."></i></a>
				<label>ایجاد در (<em>اختیاری</em>)</label>
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
				<button name="add_category_submit_btn" value="Add category" class="btn btn-mini btn-normal" />ایجاد موضوع جدید</button>
				<span id="add_category_response"></span>
			</div>
            </div>
		</div><!-- .widget -->
        
        <div class="widget border-radius4 shadow-div">
        <h4>انتشار</h4>
			<?php
            if($video_details['yt_length'] > 0) {	
                $yt_minutes = intval($video_details['yt_length'] / 60); 
                $yt_seconds = intval($video_details['yt_length'] % 60); 
            } else {
                $yt_minutes = 0;
                $yt_seconds = 0;
            }
            ?>
            <div class="control-group">
            <label class="control-label" for="">مدت : <span id="value-yt_length"><strong><?php echo sec2min($video_details['yt_length']);?></strong></span> <a href="#" id="show-duration">ویرایش</a></label>
            <div class="controls" id="show-opt-duration">
            <input type="text" name="yt_min" id="yt_length" value="<?php echo $yt_minutes; ?>" size="4" class="smaller-select" /> <small>دقیقه.</small>
            <input type="text" name="yt_sec" id="yt_length" value="<?php echo $yt_seconds; ?>" size="3" class="smaller-select" /> <small>ثانیه </small>
            <input type="hidden" name="yt_length" id="yt_length" value="<?php echo trim(($yt_minutes * 60) + $yt_seconds); ?>" />
            </div>
            </div>

			<div class="control-group">
            <label class="control-label" for="">نظر : <span id="value-comments"><strong><?php echo ($video_details['allow_comments'] == 1) ? 'allowed' : 'closed';?></strong></span> <a href="#" id="show-comments">ویرایش</a></label>
            <div class="controls" id="show-opt-comments">
                <label><input name="allow_comments" id="allow_comments" type="checkbox" value="1" <?php if ($video_details['allow_comments'] == 1) echo 'checked="checked"';?> />بر روی این ویدئو اجازه نظر دادن بده</label>
            </div>
            </div>
			
            <div class="control-group">
            <label>ویژه : <span id="value-featured"><strong><?php echo ($video_details['featured'] == 1) ? 'yes' : 'no';?></strong></span> <a href="#" id="show-featured">ویرایش</a></label>
            <div class="controls" id="show-opt-featured">
                <label><input type="checkbox" name="featured" id="featured" value="1" <?php if($video_details['featured'] == 1) echo 'checked="checked"';?> /> بله به عنوان ویژه علات بزن</label>
            </div>
            </div>
            <div class="control-group">
            <label class="control-label reqreg" for="">ثبت نام های اجباری : <span id="value-register"><strong><?php echo ($video_details['restricted'] == 1) ? 'yes' : 'no';?></strong></span> <a href="#" id="show-visibility">ویرایش</a></label>
            <div class="controls" id="show-opt-visibility">
                <label class="checkbox inline"><input type="radio" name="restricted" id="restricted" value="1" <?php echo ($video_details['restricted'] == 1) ? 'checked="checked"' : '';?> /> بله</label> 
                <label class="checkbox inline"><input type="radio" name="restricted" id="restricted" value="0" <?php echo ($video_details['restricted'] != 1) ? 'checked="checked"' : '';?> /> خیر</label>
            </div>
            </div>
			
            <div class="control-group">
            <label class="control-label" for="">انتشار<span id="value-publish"><strong>بلافاصله</strong></span> <a href="#" id="show-publish">ویرایش</a></label>
            <div class="controls" id="show-opt-publish">
            <?php echo ($_POST['date_month'] != '') ? show_form_item_date( pm_mktime($_POST) ) : show_form_item_date();	?>
            </div>
            </div>
            <?php 
            $modframework->trigger_hook('admin_addvideo_publishoptions');
            ?>
        </div><!-- .widget -->

		<div class="widget border-radius4 shadow-div">
		<h4>تگ ها</h4>
            <div class="control-group">
            <div class="controls">
                <div class="tagsinput" style="width: 100%;">
                <input type="text" name="tags" value="<?php echo $video_details['tags']; ?>" id="tags_addvideo_1" />
                </div>
            </div>
            </div>
        </div><!-- .widget -->
        <?php 
		$modframework->trigger_hook('admin_addvideo_input');
		?>
    </div>
    
</div>
<div class="clearfix"></div>
<input type="hidden" name="language" value="1" />
<input type="hidden" name="yt_id" value="<?php echo $video_details['yt_id']; ?>" />
<input type="hidden" name="url_flv" value="<?php echo $video_details['url_flv']; ?>" />
<input type="hidden" name="source_id" value="<?php echo $video_details['source_id']; ?>" />
<input type="hidden" name="submitted" value="<?php echo $video_details['submitted']; ?>" />
<input type="hidden" name="mp4" value="<?php echo $video_details['mp4']; ?>" />
<input type="hidden" name="direct" value="<?php echo $video_details['direct']; ?>" />
<input type="hidden" name="age_verification" value="0" />
    
<div id="stack-controls" class="list-controls">
<div class="btn-toolbar">
    <div class="btn-group">
	<button type="submit" name="submit" value="Submit" class="btn btn-small btn-success btn-strong">اضافه کردن ویدئو</button>
	</div>	
</div>
</div><!-- #list-controls -->

<?php
if($video_details['yt_id'] == '') 
	$video_details['yt_id'] = generate_activation_key(9); 
?>
</form>
<?php
}

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
						'featured' => 0,
						'added' => '',
						'restricted' => 0, 
						'allow_comments' => 1
						);

?>
<script language="javascript">
function checkFields(Form) {

	var msg;
	if(Form.elements.url.value == "")
		msg = "Please insert a link valid link as instructed below.";
	
	if(msg)
	{
		document.forms["add"].elements.url.style.background = "#FFDDDE";
		alert(msg);
		return false;
	}
	else 
		return true;
}
</script>

<div id="adminPrimary">
    <div class="row-fluid" id="help-assist">
        <div class="span12">
        <div class="tabbable tabs-left">
          <ul class="nav nav-tabs">
            <li class="active"><a href="#help-overview" data-toggle="tab">هفتگی</a></li>
            <li><a href="#help-onthispage" data-toggle="tab">اضافه کردن یک ویدئو معمولی</a></li>
            <li><a href="#help-bulk" data-toggle="tab">اضافه کردن یک ویدئو ریموت</a></li>
          </ul>
          <div class="tab-content">
            <div class="tab-pane fade in active" id="help-overview">
            <p>این صفحه امکان این را فراهم میاورد که شما می توانید  ویدئوهای  ریموت و معمولی را به اسانی کپی کنید.</p>
            <p>آدرس های اجازه داده شده این دو هستند : ویدئوهای غیر ریموت مانند .flv ، .mp4 و ... . یا ویدئوهای میزبانی شده به واسطه سرویس های ریموت مانند یوتیوب و ... .</p>
            <p>همچنین شما می توانید از دکمه "اضافه کردن ویدئو" استفاده کنید که در قسمت هدر قرار دارد . به اسانی می توانید آدرس یک ویدئو را در آنجا کپی کنید.</p>
            </div>
            <div class="tab-pane fade" id="help-onthispage">
              <p>اگر شما قصد داشتید تا ز ویدئو های غیر ریموت استفاده کنید  میتوانید از سرویس های 3rd party همانند AWS S3 یا حتی از هاست خودتان استفاده کنید. فرم زیر اجازه می دهد که از ویدئوهای غیر ریموت را از هر جایی اضافه کنید. فقط آدرس ویدئوهایتان را کپی کنید.</p>
            </div>
            <div class="tab-pane fade" id="help-bulk">
            <p>ویدئوهای ریموت توسط سایت های ویدئو 3rd party میزبانی می شوند. در زیر لیستی از سایت های پشتیبانی شده می بینید :</p>
             <ul style="height:200px; overflow-y: scroll; margin:3px 0;padding: 3px; color:#666; border: 1px solid #e2d59c; box-shadow: inset 0 1px 2px #ccc;" class="border-radius3">
             <?php
                $sources = a_fetch_video_sources();
                $sources = array_reverse($sources);
				$sources = array_sort($sources, 'source_name', SORT_ASC);
                $counter = 1;
                
                foreach ($sources as $k => $src)
                {
                    if (is_int($k) && $k >= 2): 
                    ?>
                    <li><?php echo $counter.'. '. ucfirst($src['source_name']);?> <small>(e.g. <?php echo $src['url_example'];?>)</small></li>
                    <?php 
                    $counter++;
                    endif;
                }
             ?>
             </ul>
             <p></p>
            <p>بعد از کپی کردن آدرس زیر ، php melody بصورت اتوماتیک  داده های زیاد را از جای دیگر بصورت ریموت بازیابی می کند. که شامل تامبنیل ، عنوان ویدئو ، توضیحات و موارد دیگر می شود. به خاطر چندین که به چندین دلیل شما باید این قبیل داده ها را بصورت دستی اضافه کنید.</p>
            <p>لطفا توجه بکنید که  هیچ فایل ویدئویی در این پردازش به دامنه تان دانلود نخواهد شد.</p>
            <p>یاد بگیر چگونه استفاده بکنی از <strong>فیلدهای سفارشی </strong>: <a href="http://help.phpmelody.com/how-to-use-the-custom-fields/" target="_blank">http://help.phpmelody.com/how-to-use-the-custom-fields/</a></p>
            </div>
          </div>
        </div> <!-- /tabbable -->
        </div><!-- .span12 -->
    </div><!-- /help-assist -->
    
    <div class="content">
	<a href="#" id="show-help-assist">راهنما</a>
	<h2>اضافه کردن ویئو از آدرس</h2>
	<?php 
	
	echo $message; 


switch($step)
{

	case 1:		//	STEP 1
?>
<form name="add" action="addvideo.php?step=2" method="post" class="form-inline" onSubmit="return checkFields(this);">
<input type="text" id="addvideo_direct_input" name="url" size="30" class="input-xlarge" placeholder="http://" /> 
<input type="submit" id="addvideo_direct_submit" name="Submit" value="Step 2 &raquo;" class="btn" />  <strong><small><a href="#" id="show-help-link-assist">به کمک احتیاج دارید؟</a></small></strong>
</form>
<hr />
<?php
	break;
	
	case 2:		//	STEP 2
	$modframework->trigger_hook('admin_addvideo_step2_pre');
		if(isset($_POST['Submit']) || $_GET['url'] != '' || isset($_POST['filename']))
		{
			if($_POST['url'] != '' || $_GET['url'] != '')
				$url = (isset($_POST['url'])) ? trim($_POST['url']) : trim($_GET['url']);
			
			if($_POST['submitted'] != '' || $_GET['submitted'] != '')
				$submitted = (isset($_POST['submitted'])) ? $_POST['submitted'] : trim($_GET['submitted']);
			else
				$submitted = $userdata['username'];
			/*
				MODE
				1 = Outsource (e.g. youtube)
				2 = Direct URL to video file
				3 = Direct URL/Path/Filename to video hosted locally
			*/
			
			$mode = 0;
			$temp = '';
			
			$url = str_replace('https:/', 'http:/', $url);
			$url = str_replace('youtu.be/', 'youtube.com/watch?v=', $url);
			
			//	Is this a direct link to a video file?
			if (strpos($url, '?') !== false)
			{
				$temp = explode('?', $url);
				$url = $temp[0];
			}
			
			$tmp_parts = explode('.', $url);
			$ext = array_pop($tmp_parts);
			$ext = strtolower($ext);
			$ext = '.'. $ext;
			
			if (is_array($temp) && count($temp) > 0)
			{
				$url = '';
				$temp[0] = rtrim($temp[0], '?');
				$temp[0] = $temp[0] .'?';
				foreach ($temp as $k => $v)
				{
					$url .= $v;
				}
			}
			
			if(in_array($ext, $allowed_ext) && (preg_match('/photobucket\.com/', $url) == 0))
			{
				if(!is_url($url))
				{
					// maybe it's an IP address
					if (is_ip_url($url))
					{
						$mode = 2;
					}
					else
					{
						$mode = 3;
					}
				}
				else if(strpos($url, _URL) !== false)
				{
					$mode = 3;
				}
				else
				{
					$mode = 2;
				}
			}
			elseif(is_url($url))
			{
				$mode = 1;
			}
			else	//	default;
			{
				$mode = 2;
			}
			if(isset($_POST['filename']) && $_POST['filename'] != '')
				$mode = 3;
			
			//	Build the $video_details array;
			switch($mode)
			{
				case 1: 	//	 Outsource (e.g. youtube); 
					$sources = a_fetch_video_sources();
					$use_this_src = -1;

					if($sources === false || count($sources) == 0)
					{
						$message = "هیچ سورس فعالی یافت نشد.";
						break;
					}
					
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

					if($use_this_src > -1)
					{
						if(!file_exists( "./src/" . $sources[ $use_this_src ]['source_name'] . ".php"))
						{
							$message = "File '/src/" . $sources[ $use_this_src ]['source_name'] . ".php'" . " not found.";
							break;
						}
						else
						{
							$temp = array();
							require_once( "./src/" . $sources[ $use_this_src ]['source_name'] . ".php");
							do_main($temp, $url);
							
							$video_details = array_merge($video_details, $temp);
							
							unset($temp);
							
							$video_details['source_id'] = $use_this_src;
						}
					}
					else
					{
						$message = "<strong>این سایت ویدئو پشتیبانی نمی شود.</strong>. برای دیدن لیست سایت های ویدئویی پشتیبانی شده لطفا <a href=\"addvideo.php?step=1\">به عقب برگردید .</a> و 'دستورالعمل' را بخوانید.";
					}
				break;
				
				case 2:		//	2 = direct link to .flv/.mp4 (outsource)
					if(!is_url($url) && ! is_ip_url($url))
					{
						$message = '<strong>'.$url.'</strong><br />این شبیه به لینک معتبر نیست. لطفا <a href="addvideo.php?step=1">برگرد</a> و از دوباره تلاش کن.';
						break;
					}
					$video_details['source_id'] = 2;
					$video_details['url_flv'] = $url;
					$video_details['direct'] = $url;
				break;
				case 3:		//	flv hosted locally or just uploaded
				
					if(isset($_POST['filename']) && $_POST['filename'] != '')
					{
						$contents = get_config('last_video');
						update_config('last_video', '');
						
						//	try the backup file
						if($contents == '')
						{
							$fp = fopen('tmp.pm', 'r');
							$contents = fread($fp, 512);
							fclose($fp);
						}
						
						//	clear file contents anyway
						$fp = fopen('tmp.pm', 'w');
						fwrite($fp, '');
						fclose($fp);
						
						if ($contents == '')	
						{
							$message  = 'نام فایل آپلود شده بازیابی نشد.';
							$message .= '<br />بررسی کنید <a href="'. _URL .'/admin/readlog.php">ورد به سیستمتان را</a> برای هر پیام خطایی.';
							
							if ( ! is_writable(ABSPATH . 'admin/tmp.pm'))
							{
								$message .= '<br />مطمئن شو که "<em>/admin/tmp.pm</em>"فایل مجوز لازم را دارد (0777) ';
								$message .= 'و از دوباره ویدئو را آپلود کن.';
							}
						}
						else
						{
							//	get filename
							$content  = explode("/", $contents);
							$filename = $content[ count($content)-1 ];
							
							//	move the new file to the videos directory 
							$oldpath = $contents;
							$newpath = _VIDEOS_DIR_PATH . $filename;
							if ($oldpath != $newpath)
							{
								if(!rename($oldpath, $newpath))
								{
									$message  = 'فایل آپلود شده به پوشه آپلودها انتقال پیدا نکرد. ';
									$message .= 'مطمئن شو که پوشه آپلودها قابل نوشتن است )0777(. ';
									break;
								}
							}
							$video_details['url_flv'] = $filename;
							$video_details['direct'] = $filename;
							
						}				
					}
					else
					{
						//	this means $url is either the path or a direct link to the .flv file whick is hosted locally(!)
						//	we only need the filename
						$temp = explode("/", $url);
						$video_details['url_flv'] = $temp[ count($temp)-1 ];
						unset($temp);
					}
					$sources = a_fetch_video_sources();
					
					$use_this_src = -1;
					foreach($sources as $src_id => $source)
						if($source['source_name'] == 'localhost')
							$use_this_src = $source['source_id'];
						$video_details['source_id'] = ($use_this_src != -1) ? $use_this_src : 1; //	1 = Default for LOCALHOST
				break;
			}
			$modframework->trigger_hook('admin_addvideo_step2_mid');
			//	Prevent adding the same video twice
			if ($video_details['direct'] != '')
			{
				$sql = "SELECT * FROM pm_videos_urls WHERE direct = '". $video_details['direct'] ."'";
				
				$result = mysql_query($sql);
				if (mysql_num_rows($result) > 0)
				{
					$row = mysql_fetch_assoc($result);
					mysql_free_result($result);
					
					$message .= 'This video is already in your database.';
					$message .= '</div><div><br />';
					$message .= '<input name="view_video" type="button" value="Watch this video" onClick="location.href=\''. _URL .'/watch.php?vid='. $row['uniq_id'] .'\'" class="btn" /> ';
					$message .= '<input name="edit_video" type="button" value="Edit video &raquo;" onClick="location.href=\'modify.php?vid='. $row['uniq_id'] .'\'" class="btn btn-info" />';
					$message .= '</strong>';
				}
				unset($row, $sql, $result);
			}
			if (strlen($message) == 0 && $video_details['url_flv'] != '')
			{
				$sql = "SELECT * FROM pm_videos WHERE url_flv = '". $video_details['url_flv'] ."'";
				
				$result = mysql_query($sql);
				if (mysql_num_rows($result) > 0)
				{
					$row = mysql_fetch_assoc($result);
					mysql_free_result($result);
					
					$message .= 'این ویدئو در دیتابیستان وجود دارد.';
					$message .= '</div><div><br />';
					$message .= '<input name="view_video" type="button" value="مشاهده این ویدیو" onClick="location.href=\''. _URL .'/watch.php?vid='. $row['uniq_id'] .'\'" class="btn" /> ';
					$message .= '<input name="edit_video" type="button" value="ویرایش این ویدیو &raquo;" onClick="location.href=\'modify.php?vid='. $row['uniq_id'] .'\'" class="btn btn-info" />';
					$message .= '</strong>';

				}
				unset($row, $sql, $result);
			}
			$modframework->trigger_hook('admin_addvideo_step2_post');
			if($message != '')
			{
				echo "<div class='alert alert-info'>".$message."</div>";
			}
			else	//	show form
			{
				$video_details['submitted'] = $submitted;
				add_video_form($video_details);
			}
		}	//	endif isset(POST or GET)
		else
		{
			echo "<a href=\"addvideo.php?step=1\">&larr; لطفا به مرحلهاول بروید.</a>";
			if ( ! headers_sent())
			{
				header("Location: addvideo.php?step=1");
			}
			else 
			{
				echo '<meta http-equiv="refresh" content="0;URL=addvideo.php?step=1" />';
			}
			exit();
		}
	break;
	case 3:		//	STEP 3
	
		$modframework->trigger_hook('admin_addvideo_step3_pre');

		if(isset($_POST['submit']))
		{
			$required_fields = array('video_title' => '"عنوان ویدئو" یک فیلد اجباری است و نمی تواند خالی باشد',
									'url_flv' => 'لینک به فایل ویدئو از دست رفت', 
									'category' => 'لطفا برای این ویدئو حداقل یک دسته انتخاب کنید.'
									);
			$message = '';
			
			foreach($video_details as $field => $value)
			{
				if ($field == 'category' && is_array($_POST[$field]))
				{
					$_POST[$field] = implode(',', $_POST[$field]);
				}
				$video_details[$field] = trim($_POST[$field]);
				if(trim($_POST[$field]) == '' && array_key_exists($field, $required_fields))
					$message .= $required_fields[$field] . '<br />';
			}

			$video_details['yt_length'] = ($_POST['yt_min'] * 60) + $_POST['yt_sec'];
			$video_details['meta'] = $_POST['meta'];
			
			$added = validate_item_date($_POST);
			if ($added === false)
			{
				$message .= "تاریخ غیرمعتبر. لطفا آنرا تضحیح کنید.<br />";
			}
			else
			{
				$video_details['added'] = pm_mktime($added);
			}
			
			if($message != '')
			{				
				echo "<div class='alert alert-error'>".$message."</div>";
				add_video_form($video_details);
				break;
			}
			else
			{
				$message = '';
				//	check if this video already exists
				if(count_entries('pm_videos', 'url_flv', $video_details['url_flv']) > 0)
				{
					$message .= "این ویدئو (".$video_details['url_flv'].")در دیتابیس تان وجود دارد. لطفا برگردید و آنرا اصلاح کنید.<br />";
				}
				elseif( ($video_details['direct'] != "") && (count_entries('pm_videos_urls', 'direct', $video_details['direct']) > 0))
				{
					$message .= "این لینک مستقیم <em>'".$video_details['direct']."'</em> در دیتابیس تان وجود دارد. <br />در دیتابیس تان وجود دارد. لطفا برگردید و آنرا اصلاح کنید.<br />";
				}
				else
				{
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
					$modframework->trigger_hook('admin_addvideo_step3_mid');
					
					//	upload, download or rename thumbnail file
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
					}
					else
					{
						//	download thumbnail
						$sources = a_fetch_video_sources();
						$use_this_src = -1;
						
						foreach($sources as $src_id => $source)
						{
							if($src_id == $video_details['source_id'])
							{
								$use_this_src = $source['source_id'];
								break;
							}
						}
						require_once( "./src/" . $sources[ $use_this_src ]['source_name'] . ".php");

						if ('' != $video_details['yt_thumb'])
							$img = download_thumb($video_details['yt_thumb'], _THUMBS_DIR_PATH, $uniq_id);
						else 
							$img = true;
						
						if($img === false)
							$message .= "خطا هنگام دانلود تصویر ویدیو<br />";
					}
				}
				
				if ($img === false)
				{
					echo "<div class='alert alert-error'>در حال دانلود این تامبنیل خطایی رخ داد. </div>";
				}
				
				if($message != '')
				{
					echo "<div class='alert alert-info'>".$message."</div>";
					echo '<br /><input name="add_new" type="button" value="&larr; بازگشت" onClick="location.href=\'addvideo.php?step=1\'" class="btn" />';
				}
				else	//	Everything is good. Now we can add the new video to the database
				{
					if ($_POST['featured'] == '1')
					{
						$video_details['featured'] = 1;
					}
					else
					{
						$video_details['featured'] = 0;
					}
					$modframework->trigger_hook('admin_addvideo_step3_pre_video');
					$new_video = insert_new_video($video_details, $new_video_id);
					if($new_video !== true)
					{
						$message = "<em>یک مشکلی رخ داده! ویئو جدید به دیتابیس تان اضافه نشد;</em><br /><strong>گزارش  mysql :</strong> ".$new_video[0]."<br /><strong>شماره خطا:</strong> ".$new_video[1]."<br />";		
					}
					else
					{
						$modframework->trigger_hook('admin_addvideo_step3_post_video');
						//	tags?
						if(trim($_POST['tags']) != '')
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
						$message = "ویدیو با موفقیت ثبت شد.";
					}
					$modframework->trigger_hook('admin_addvideo_step3_final');
					echo "<div class='alert alert-success'>".$message."</div>";
					echo '<br />';
					echo '<div class="btn-group"><input name="add_new" type="button" value="&larr; یک ویدئو جدید اضافه کن" onClick="location.href=\'addvideo.php?step=1\'" class="btn btn-small" />';
					echo '<input name="import_new" type="button" value="افزودن ویدیو از یوتیوب" onClick="location.href=\'import.php\'" class="btn btn-small" />';
					}
			}
		}	//	end if post['submit'];
		else
		{
			if(headers_sent())
			{
				echo '<meta http-equiv="refresh" content="0;URL=addvideo.php?step=1" />';
			}
			else
			{
				header("Location: addvideo.php?step=1");
			}
			exit();
		}
	break;
}
?>
    </div><!-- .content -->
</div><!-- .primary -->
<?php
include('footer.php');
?>