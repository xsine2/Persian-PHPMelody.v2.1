</div><!-- #wrapper -->
<div class="clearfix"></div>

<footer class="row-fluid" id="footer">

	<p>قدرت گرفته توسط <a href="/" target="_blank">سیستم اشتراک ویدیو پارسیان کلیپ</a><br />
	<span>هرگونه کپی برداری از این سیستم پیگرد قانونیخ واهد داشت .</span>
    </p>
</footer>

<div class="modal hide fade" id="addVideo" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
<div class="modal-header">
<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
<h3 id="myModalLabel">اضافه کردن ویدئو</h3>
</div>
<div class="modal-body" style="margin:0;padding:0;">
<table cellpadding="0" cellspacing="0" width="100%" class="pm-add-tables">
  <tr>
    <td width="13%" align="center" style="text-align:center; height:60px"><div class="pm-sprite ico-add-yt"></div></td>
    <td width="83%" align="left">
    <form name="search_yt_videos" action="import.php?action=search" method="post" class="form-inline">
    <input name="keyword" type="text" value="" placeholder="Type keywords to search for..." style="width:282px" id="yt_query" /> 
	<input type="hidden" name="autofilling" value="1" />
	<input type="hidden" name="autodata" value="1" />
    <input type="hidden" name="results" value="20"> <button type="submit" name="submit" class="btn" id="searchVideos" data-loading-text="Searching...">جستجو</button> <span class="searchLoader"><img src="img/ico-loading.gif" width="16" height="16" /></span>
    </form>
    </td>
  </tr>
  <tr>
    <td align="center" style="text-align:center;"><div class="pm-sprite ico-add-link"></div></td>
    <td align="left">
    <form name="add" action="addvideo.php?step=2" method="post" onSubmit="return checkFields(this);" class="form-inline">
    <input type="text" id="addvideo_direct_input" name="url" placeholder="http://" style="width:282px" /> 
    <input type="hidden" name="" value=""> 
    <button type="submit" id="addvideo_direct_submit" name="Submit" value="Step 2" class="btn">Continue</button> <span class="addLoader"><img src="img/ico-loading.gif" width="16" height="16" /></span>
    </form>
    </td>
  </tr>
  <tr>
    <td align="center" style="text-align:center;"><div class="pm-sprite ico-add-local"></div></td>
    <td align="left">
    <form id="upload_flv" enctype="multipart/form-data" action="upload_file.php" method="post" style="margin-bottom:0;">
    <span id="uploader">
        <label for="myFile"> </label>
        <input name="MAX_FILE_SIZE" value="<?php echo (int) get_true_max_filesize(); ?>" type="hidden" />
        <input name="mediafile" id="myFile" type="file" />
        <input type="submit"  name="submit" id="upload_submit" value="Step 2 &rarr;" class="btn" />
    </span>
    </form>
    </td>
  </tr>
</table>
</div>
</div>

<?php if($config['keyboard_shortcuts'] == 1) : ?>
<div class="modal hide fade" id="seeShortcuts" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
<div class="modal-header">
<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
<h3 id="myModalLabel">میان برها</h3>
</div>
<div class="modal-body" style="margin:0;padding:0;">
    <div class="row-fluid">
        <div class="span6">
		<h6>دسترسی اسان به صفحات</h6>
        <ul>
            <li><span class="keycombo">ALT + v</span> ویدئوها</li>
            <li><span class="keycombo">ALT + a</span> مقالات</li>
            <li><span class="keycombo">ALT + p</span> صفحات</li>
            <li><span class="keycombo">ALT + c</span> نظرات</li>
            <li><span class="keycombo">ALT + s</span> تنظیمات عمومی</li>
            <li><span class="keycombo">ALT + l</span> تنظیمات طرح</li>
        </ul>

        <h6>مدال ها</h6>
        <ul>
            <li><span class="keycombo">c</span> راه اندازی 'اضافه کردن' ویدئو</li>
            <li><span class="keycombo">?</span> این (راهنما) اسکرین</li>
        </ul>
        
		<h6>لیست ها</h6>
        <ul>
            <li><span class="keycombo">shift+a</span> انتخاب کردن همه لیست ها (ویدئو ، نظرات و ...)</li>
            <li><span class="keycombo">shift+s</span> برو به صفحه جستجو</li>
        </ul>
        </div>
        <div class="span6">
        <h6>در داخل ویرایشگرهای متن</h6>
        <ul>
            <li><span class="keycombo">ctrl+z</span> آندو کردن</li>
            <li><span class="keycombo">ctrl+y</span> ریدو کردن</li>
            <li><span class="keycombo">ctrl+b</span> بولد کردن</li>
            <li><span class="keycombo">ctrl+i</span> ایتالیک کردن</li>
            <li><span class="keycombo">ctrl+u</span> زیرخط کردن</li>
            <li><span class="keycombo">ctrl+1-6</span> h1-h6</li>
            <li><span class="keycombo">ctrl+7</span> p</li>
            <li><span class="keycombo">ctrl+8</span> div</li>
            <li><span class="keycombo">ctrl+9</span> آدرس</li>
        </ul>
        </div>
    </div>
</div>
</div>
<script src="js/jquery.hotkeys.js" type="text/javascript"></script>
<?php endif; ?>
<script type="text/javascript" src="js/jquery.typewatch.js"></script>
<script src="js/bootstrap.min.js" type="text/javascript"></script>
<script src="js/bootstrap-hover-dropdown.min.js" type="text/javascript"></script>
<script src="js/jquery.ajaxmanager.js" type="text/javascript"></script>
<script src="js/jquery.cookee.js" type="text/javascript"></script>
<script src="js/jquery.ba-dotimeout.min.js" type="text/javascript"></script>
<?php if ($load_datepicker) : ?>
<script src="js/bootstrap-datepicker.js" type="text/javascript"></script>
<?php endif;?>
<?php if($load_tagsinput == 1): ?>
<script src="js/jquery.tagsinput.js" type="text/javascript"></script>
<?php endif; ?>
<script src="js/melody.js" type="text/javascript"></script>

<script type="text/javascript" src="js/jquploader/jquery.flash.js"></script>
<script type="text/javascript" src="js/jquploader/jquery.jqUploader.js"></script>
<script type="text/javascript">
$("#uploader").jqUploader({
	uploadScript:		'../../upload_file.php?PHPSESSID=<?php echo session_id();?>',
	afterScript:		'addvideo.php?step=2',
	background:			"FFFFFF",
	barColor:			"666666",
	allowedExt:     	"*.flv; *.mp4; *.wmv; *.mov; *.divx; *.avi; *.mkv; *.asf; *.wma; *.mp3; *.m4v; *.m4a; *.3gp; *.3g2",
	allowedExtDescr: 	"(*.flv,*.mp4,*.wmv,*.mov,*.divx,*.avi,*.mkv, *.asf, *.wma, *.mp3, *.m4v, *.m4a, *.3gp, *.3g2)",
	width:				450,
	height:				50,
	src:				'js/jquploader/jqUploader.swf',
	hideSubmit:			true,
	errorSizeMessage: 	'flv خیلی بزرگ است!',
	validFileMessage: 	'اکنون برای پردازش کردن بر روی "آپلود" کلیک کنید',
	progressMessage:  	'لطفا صبر کنید, در حال آپلود ',
	endMessage:    	  	'ویدئو آپلود شده',
	maxFileSize: 		<?php echo (int) get_true_max_filesize(); ?>
});
</script>

<script type="text/javascript" src="js/vscheck.js"></script>
<script type="text/javascript">
	jQuery(function($){
		$(document).ready(function(){
			if(($.browser.msie)&(parseInt($.browser.version)<7)){
				$("img[src$='.png']").each(function(){$(this).addClass("png");});
				//$("span").each(function(){$(this).addClass("pngbg");});
			}
		});
	});
</script>
<?php if($load_colorpicker == 1): ?>
<script src="js/bootstrap-colorpicker.min.js" type="text/javascript"></script>
<?php endif; ?>
<?php if($load_tinymce == 1): ?>
<script src="js/tiny_mce/jquery.tinymce.js" type="text/javascript"></script>
<script type="text/javascript">
// Initializes all textareas with the tinymce class
$(document).ready(function () {
   $('textarea.tinymce').tinymce({
      script_url: 'js/tiny_mce/tiny_mce.js',
      disk_cache: true,
      theme : "advanced",
	  skin:"cirkuit",
	  language:"en",
	  plugins : "pdw,autosave,fullscreen,wordcount,lists,preview,paste,directionality,media,tabfocus,autolink,spellchecker",
      theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,hr,|,formatselect,fontselect,fontsizeselect,|,pdw_toggle,",
      theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,",
      theme_advanced_buttons3 : "preview,|,forecolor,backcolor,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,advhr,|,print,|,ltr,rtl,|,media,fullscreen",
	  theme_advanced_font_sizes: "12px,13px,14px,15px,16px,18px,20px",
	  font_size_style_values : "12px,13px,14px,15px,16px,18px,20px",
	  pdw_toggle_on : 1,
      pdw_toggle_toolbars : "2,3",
      theme_advanced_resizing : true,
      theme_advanced_resize_horizontal : false,
	  relative_urls : false,
	  browser_spellcheck : true,
	  content_css : "css/fronend-look.css"
   });
});
</script>
<?php endif; ?>
<?php if ($load_jquery_ui) : ?>
<link rel="stylesheet" href="css/jquery-ui.css" type="text/css" media="screen" charset="utf-8" />
<script type="text/javascript" src="js/jquery-ui.min.js"></script>
<?php endif; ?>
<?php if ($showm == 'mod_article' || $showm == '3' || $showm == 'mod_pages' || $load_swfupload == 1): ?>
<script type="text/javascript" src="js/article.js"></script>
<script type="text/javascript" src="js/swfupload.js"></script>
<script type="text/javascript" src="js/swfupload.handlers.js"></script>
<script type="text/javascript" src="js/jquery.swfupload.js"></script>
<?php endif; ?>
<?php if($load_uniform == 1): ?>
<link rel="stylesheet" href="css/uniform.default.css" type="text/css" media="screen" charset="utf-8" />
<script src="js/jquery.uniform.min.js" type="text/javascript"></script>
<?php endif; ?>
<?php if($load_ibutton == 1): ?>
<script type="text/javascript" src="js/jquery.ibutton.js"></script>
<?php endif; ?>
<?php if($load_prettypop == 1): ?>
<link rel="stylesheet" href="css/prettyPop.css" type="text/css" media="screen" charset="utf-8" />
<script type="text/javascript" src="js/jquery.prettyPhoto.js"></script>
<?php endif; ?>
<?php if($load_scrolltofixed == 1): ?>
<script type="text/javascript" src="js/jquery-scrolltofixed-min.js"></script>
<?php endif; ?>
<script type="text/javascript" src="js/a_general.js"></script>
<?php if($load_chzn_drop == 1): ?>
<script type="text/javascript" src="js/chosen.jquery.min.js"></script>
<?php endif; ?>
<script type="text/javascript" src="js/jquery.gritter.js"></script>

<script type="text/javascript">
	var show_pm_notes = $.cookie('showNotice');
	if (show_pm_notes != 'off') {
		$(document).ready(function () {
			<?php show_pm_notes(); ?>
		});
	}
</script>

<?php include("footer-js.php"); ?>
<?php
if (is_user_logged_in() && is_admin()) 
{
    $force = false;
    if ($_GET['forcesync'] == '1')
    {
        $force = true;
    }
    autosync($force);
}

if ($conn_id)
{
    mysql_close($conn_id);
}
?>

</body>
</html>