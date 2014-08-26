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

$showm = '8';
/*
$load_uniform = 0;
$load_ibutton = 0;
$load_tinymce = 0;
$load_colorpicker = 0;
$load_prettypop = 0;
*/
$load_swfupload = 1;
$load_colorpicker = 1;
$load_scrolltofixed = 1;
$load_settings_theme_resources = 1;
$_page_title = 'Layout settings';
include('header.php');
include_once('syndicate_news.php');

//$config	= get_config();

$inputs = array();
$info_msg = '';
$video_sources = a_fetch_video_sources();

if ($_POST['submit'] == "Save" && ( ! csrfguard_check_referer('_admin_settings')))
{
	$info_msg = 'Invalid token or session expired. Please load this page from the menu and try again.'; 
}
else if ($_POST['submit'] == "Save")
{
	$req_fields = array("browse_page" => "Videos per browsing page",
						"top_page_limit" => "Top videos page (limit)",
						"new_page_limit" => "New videos page (limit)",
						"comments_page" => "Comments per page",
						"thumb_video_w" => "Video thumbnail width",
						"thumb_video_h" => "Video thumbnail height",
						"thumb_article_w" => "Article thumbnail width",
						"thumb_article_h" => "Article thumbnail height",
						"thumb_avatar_w" => "User avatar width",
						"thumb_avatar_h" => "User avatar heigh"
					);
	$num_fields = array('new_videos', 'article_widget_limit', 'chart_days', 'top_videos', 'playingnow_limit', 'watch_related_limit', 'watch_toprated_limit', 'fav_limit', 'browse_page', 'comments_page', 'thumb_video_w', 'thumb_video_h', 'thumb_article_w', 'thumb_article_h', 'thumb_avatar_w', 'thumb_avatar_h', 'chart_days', 'show_stats', 'show_tags', 'tag_cloud_limit', 'search_suggest', 'show_addthis_widget', 'browse_articles');
	foreach($_POST as $k => $v)
	{
		if($_POST[$k] == '' && in_array($k, $req_fields))
		{
			$info_msg .= "'".$req_fields[$k] . "' field cannot be left blank!";
		}
		if(in_array($k, $num_fields))
		{
			$v = (int) $v;
			$v = abs($v);
			$inputs[$k] = $v;
		}
		else if ( ! is_array($v))
			$inputs[$k] = $v;
	}
	
	$inputs['homepage_title'] = str_replace('"', '&quot;', $inputs['homepage_title']);
	$inputs['homepage_keywords'] = str_replace('"', '&quot;', $inputs['homepage_keywords']);
	$inputs['homepage_description'] = str_replace('"', '&quot;', $inputs['homepage_description']);
	
	// Save config	
	if($info_msg == '')
	{
		foreach ($inputs as $config_name => $config_value)
		{
			if ($config_name != 'submit' && $config_name != 'allow_user_uploadvideo_unit')
			{	
				update_config($config_name, $config_value, true);
			}
		}
	}
	//	Template has changed? Clear the Smarty Cache & Compile directories
	if ($inputs['template_f'] != $config['template_f'])
	{
		//	empty compile directory
		$dir = @opendir($smarty->compile_dir);
		if ($dir)
		{
			while (false !== ($file = readdir($dir)))
			{
				if(strlen($file) > 2)
				{
					$tmp_parts = explode('.', $file);
					$ext = array_pop($tmp_parts);
					$ext = strtolower($ext);
					
					if ($ext == 'php' && strpos($file, '%') !== false)
					{
						unlink($smarty->compile_dir .'/'. $file);
					}
				}
			}
			closedir($dir);
		}
		
		//	empty cache directory
		$dir = @opendir($smarty->cache_dir);
		if ($dir)
		{
			while (false !== ($file = readdir($dir)))
			{
				if(strlen($file) > 2)
				{
					$tmp_parts = explode('.', $file);
					$ext = array_pop($tmp_parts);
					$ext = strtolower($ext);
					
					if ($ext == 'php' && strpos($file, '%') !== false)
					{
						unlink($smarty->cache_dir .'/'. $file);
					}
				}
			}
			closedir($dir);
		}
	}
}

$selected_tab_view = '';
$page_tab_views = array('tabname1', 't1', 't2', 't3', 'general', 'customize', 'store');

if ($_POST['settings_selected_tab'] != '' || $_GET['view'] != '')
{
	$selected_tab_view = ($_POST['settings_selected_tab'] != '') ? $_POST['settings_selected_tab'] : $_GET['view'];
	if ( ! in_array($selected_tab_view, $page_tab_views)) 
	{
		$selected_tab_view = '';
	}
}

?>
<div id="adminPrimary">
    <div class="content">
<?php 
if ('' != $info_msg)
{
	echo '<div class="alert alert-error">'.$info_msg.'</div>';
}
else if ($_POST['submit'] == "Save" && $info_msg == '')
{
	echo '<div class="alert alert-success">The new settings were saved.</div>';
}
?>
<form name="sitesettings" method="post" action="settings_theme.php">
<?php echo csrfguard_form('_admin_settings'); ?>
        <div id="settings-jump"></div>
        <nav id="import-nav" class="tabbable" role="navigation">
        <h2 class="h2-import pull-left">Layout Settings</h2>
            <ul class="nav nav-tabs pull-right">
            <li class="<?php echo ($selected_tab_view == 'tabname1' || $selected_tab_view == '' || $selected_tab_view == 't1' || $selected_tab_view == 'general') ? 'active' : '';?>"><a href="#tabname1" data-toggle="tab" class="tab-pane">General Settings</a></li>
            <li class="<?php echo ($selected_tab_view == 't2' || $selected_tab_view == 'customize') ? 'active' : '';?>"><a href="#t2" data-toggle="tab" class="tab-pane<?php echo ($selected_tab_view == 't2' || $selected_tab_view == 'customize') ? ' active' : '';?>">Customize Theme</a></li>
            <li class="<?php echo ($selected_tab_view == 't3' || $selected_tab_view == 'store') ? 'active' : '';?>"><a data-toggle="tab" href="#t3" class="tab-pane<?php echo ($selected_tab_view == 't3' || $selected_tab_view == 'store') ? ' active' : '';?>">Theme Store</a></li>
            </ul>
        </nav>
		<div style="clear:both"></div>
<div class="">
<table width="100%" border="0" cellspacing="1" cellpadding="0">
	<tr>
		<td>
		</td>
	</tr>
    <td valign="top">
	<div class="tab-content">
	<div class="tab-pane fade<?php echo ($selected_tab_view == 'tabname1' || $selected_tab_view == 't1' || $selected_tab_view == ''  || $selected_tab_view == 'general') ? ' in active' : '';?>" id="tabname1">

	<script type="text/javascript">
    $(document).ready(function(){
        $('#ButtonPlaceHolder').swfupload({
            upload_url: "upload_image.php",
            
            file_size_limit : "<?php echo ($upload_max_filesize > 0) ? $upload_max_filesize.'' : '0';?>",
            file_types : "*.jpg;*.png;*.gif",
            file_types_description : "Image files",
            file_upload_limit : 0,
			file_queue_limit : 1,
            flash_url : "js/swfupload/swfupload.swf",
            button_width : 114,
            button_height : 20,
            custom_settings : {
                progressTarget : "fsUploadProgress"
            },
            post_params: {
                    "PHPSESSID" : "<?php echo session_id(); ?>",
					"doing" : "logo"
                    },
            // Button settings
            //button_image_url: "js/swfupload/upload.png",
            button_placeholder_id: "ButtonPlaceHolder",
            button_width: "110",
            button_height: "24",
            button_text: 'Upload logo',
            button_text_style: '.button-text { text-align: center; font-size: 11px; font-weight: bold;font-family: Arial, Geneva, Verdana, sans-serif; letter-spacing:-0.045em; text-shadow: 0 1px 0 #FFF; }',
            button_text_top_padding: 5,
            button_text_left_padding: 0,
            //button_window_mode: "window",
            button_window_mode: SWFUpload.WINDOW_MODE.TRANSPARENT,
            button_cursor: SWFUpload.CURSOR.HAND,
            debug: false
        })
            .bind('fileQueued', function(event, file){
                var listitem='<li id="'+file.id+'" >'+
                    'File: <em>'+file.name+'</em> ('+Math.round(file.size/1024)+' KB) <span class="progressvalue" ></span>'+
                    '<div class="progressbar" ><div class="progress" ></div></div>'+
                    '<p class="status" >Pending</p>'+
                    '<span class="cancel" >&nbsp;</span>'+
                    '</li>';
                $('#uploadLog').append(listitem);
                $('li#'+file.id+' .cancel').bind('click', function(){
                    var swfu = $.swfupload.getInstance('#swfupload-control');
                    swfu.cancelUpload(file.id);
                    $('li#'+file.id).slideUp('fast');
                });
                // start the upload since it's queued
                $(this).swfupload('startUpload');
            })
            .bind('fileQueueError', function(event, file, errorCode, message){
                alert('Size of the file '+file.name+' is greater than the limit');
            })
            .bind('fileDialogComplete', function(event, numFilesSelected, numFilesQueued){
                $('#fsUploadProgress').text('Uploaded: '+numFilesSelected+' file(s)');
            })
            .bind('uploadStart', function(event, file){
                $('#uploadLog li#'+file.id).find('p.status').text('Uploading...');
                $('#uploadLog li#'+file.id).find('span.progressvalue').text('0%');
                $('#uploadLog li#'+file.id).find('span.cancel').hide();
            })
            .bind('uploadProgress', function(event, file, bytesLoaded){
                //Show Progress
                var percentage=Math.round((bytesLoaded/file.size)*100);
                $('#uploadLog li#'+file.id).find('div.progress').css('width', percentage+'%');
                $('#uploadLog li#'+file.id).find('span.progressvalue').text(percentage+'%');
            })
            .bind('uploadSuccess', function(event, file, serverData){
                var item=$('#uploadLog li#'+file.id);
                item.find('div.progress').css('width', '100%');
                item.find('span.progressvalue').text('100%');
                var pathtofile='<a href="uploads/'+file.name+'" target="_blank" >view &raquo;</a>';
                item.addClass('success').find('p.status').html('Uploaded! Click the "Save changes" button below to apply the logo.');
				$('#show-logo').html(serverData);
                setTimeout( function() {
                $('#uploadLog li#'+file.id).fadeOut('slow');
                }, 6000);
            })
            .bind('uploadComplete', function(event, file){
                // upload has completed, try the next one in the queue
                $(this).swfupload('startUpload');
            })
        
    });	
    </script>
    
    <h2 class="sub-head-settings">Header</h2>
    <div class="pm-swf-upload">
        <div id="divStatus"></div>
        <ol id="uploadLog"></ol>
    </div>
	<table cellpadding="0" cellspacing="0" width="100%" class="table table-striped table-bordered pm-tables pm-tables-settings">
      <tr>
        <td width="20%">Site theme</td>
        <td>
			<select name="template_f">
			<?php echo dropdown_templates($config['template_f']); ?>
			</select> 
			<?php
            if ($config['template_f'] == 'default') 
            {
            ?>
             <a href="customize.php" class="btn btn-small" target="_blank"><i class="icon-share"></i> Customize</a>
            <?php
            }
            ?>
			</td>
        </tr>
      <tr>
          <td width="20%" valign="top">Site Title</td>
          <td>
          <input name="homepage_title" type="text"  size="45" value="<?php echo $config['homepage_title']; ?>" />
          </td>
      </tr>
      <tr>
          <td width="20%" valign="top">Header Image (Logo)</td>
          <td>
            <div id="show-logo">
            	<?php if ($config['custom_logo_url'] != ''): ?>
					<img src="<?php echo $config['custom_logo_url'];?>" border="0" />
				<?php endif; ?>
            </div>
			<button class="btn btn-medium btn-danger <?php if ($config['custom_logo_url'] == '') echo 'hide';?>" id="btn-remove-logo">Remove logo</button>
            <span class="btn btn-small btn-swf-upload"><span id="ButtonPlaceHolder"></span></span>
            <a href="#" rel="popover" data-placement="right" data-trigger="hover" data-content="We recommend using a transparent PNG image with a suggested width of <strong>233 pixels</strong> and maximum height of <strong>80 pixels</strong>. Large images will be automatically resized to fit within the header."><i class="icon-info-sign"></i></a>
          </td>
      </tr>
      <tr>
          <td>Meta keywords</td>
          <td>
              <input name="homepage_keywords" type="text" size="45" value="<?php echo stripslashes($config['homepage_keywords']); ?>" />
          </td>
      </tr>
      <tr>
          <td valign="top">Meta description</td>
          <td>
              <textarea name="homepage_description" rows="2" cols="55"><?php echo stripslashes($config['homepage_description']); ?></textarea>
          </td>
      </tr>
	 	<tr>
		<td>Live search recommandations</td>
		<td>
		<label><input name="search_suggest" type="radio" value="1" <?php echo ($config['search_suggest']==1) ? 'checked="checked"' : "";?> /> Enabled</label>
		<label><input name="search_suggest" type="radio" value="0" <?php echo ($config['search_suggest']==0) ? 'checked="checked"' : "";?> /> Disabled</label>
        <a href="#" rel="popover" data-placement="right" data-trigger="hover" data-content="If <em>enabled</em>, users will see a search suggestions list as they type the search query."><i class="icon-info-sign"></i></a>
        </td>
		</tr>
    </table>
    
    <h2 class="sub-head-settings">Homepage</h2>
	<table cellpadding="0" cellspacing="0" width="100%" class="table table-striped table-bordered pm-tables pm-tables-settings">
	  <tr>
        <td width="20%">Top videos widget: sort by</td>
        <td>
			<select name="top_videos_sort">
			 <option value="views" <?php if($config['top_videos_sort'] == "views") echo ' selected="selected" ';?>>Most viewed</option>
			 <option value="rating"<?php if($config['top_videos_sort'] == "rating") echo ' selected="selected" ';?>>Most liked</option>
			 <option value="chart" <?php if($config['top_videos_sort'] == "chart") echo ' selected="selected" ';?>>Most viewed (last <?php echo $config['chart_days'];?> days)</option>
			</select>
	  </tr>
	  <tr>
        <td>Top videos widget: limit</td>
        <td>
		    <input name="top_videos" type="text" size="8" class="span1" value="<?php echo $config['top_videos']; ?>" /> videos
            <a href="#" rel="popover" data-placement="right" data-trigger="hover" data-content="Set how many videos you want to list in the <em>Top Videos</em> widget from your homepage."><i class="icon-info-sign"></i></a>
            </td>
        </tr>
        <tr>
        <td>'Being watched' limit</td>
        <td><input name="playingnow_limit" type="text" size="8" class="span1" value="<?php echo $config['playingnow_limit']; ?>" /> videos
        <a href="#" rel="popover" data-placement="right" data-trigger="hover" data-content="Set how many videos you want to list in the <em>'Being watched now'</em> widget from your homepage (under the homepage 'Featured' video)."><i class="icon-info-sign"></i></a>
        </td>
        </tr>
        <tr>
        <td>New videos limit</td>
        <td><input name="new_videos" type="text" size="8" class="span1" value="<?php echo $config['new_videos']; ?>" /> videos
        <a href="#" rel="popover" data-placement="right" data-trigger="hover" data-content="Set how many videos you want to list in the <em>New Videos</em> widget from your homepage."><i class="icon-info-sign"></i></a>
        </td>
        </tr>
        <tr>
        <td>Articles widget: limit</td>
        <td><input name="article_widget_limit" type="text" size="8" class="span1" value="<?php echo $config['article_widget_limit']; ?>" /> articles
        <a href="#" rel="popover" data-placement="right" data-trigger="hover" data-content="Set how many articles you want to show in the <em>Latest Articles</em> widget from your homepage."><i class="icon-info-sign"></i></a>
        </td>
        </tr>
		<tr>
		<td>Show statistics</td>
			<td>
			<label><input name="show_stats" type="radio" value="1" <?php echo ($config['show_stats']==1) ? 'checked="checked"' : "";?> /> Yes</label>
			<label><input name="show_stats" type="radio" value="0" <?php echo ($config['show_stats']==0) ? 'checked="checked"' : "";?> /> No</label>
            <a href="#" rel="popover" data-placement="right" data-trigger="hover" data-content="If enabled, a widget containing details such as <em>member count</em>, <em>video count</em>, etc. will appear on your homepage."><i class="icon-info-sign"></i></a>
            </td>
		</tr>
		<tr>
			<td>Show tag cloud</td>
			<td>
			<label><input name="show_tags" type="radio" value="1" <?php echo ($config['show_tags']==1) ? 'checked="checked"' : "";?> /> Yes</label>
			<label><input name="show_tags" type="radio" value="0" <?php echo ($config['show_tags']==0) ? 'checked="checked"' : "";?> /> No</label>
            <a href="#" rel="popover" data-placement="right" data-trigger="hover" data-content="If enabled, a widget listing the most common tags will appear on your homepage. This helps visitors find popular content on your site."><i class="icon-info-sign"></i></a>
            </td>
			</tr>
		 <tr>
			<td>Tag cloud limit</td>
			<td><input name="tag_cloud_limit" type="text" size="8" class="span1" value="<?php echo $config['tag_cloud_limit']; ?>" /> tags</td>
			</tr>
		<tr>
			<td>Order tag cloud</td>
			<td>
			<label><input name="shuffle_tags" type="radio" value="0" <?php echo ($config['shuffle_tags']==0) ? 'checked="checked"' : "";?> /> Descending</label> 
			<label><input name="shuffle_tags" type="radio" value="1" <?php echo ($config['shuffle_tags']==1) ? 'checked="checked"' : "";?> /> Shuffle</label>
            </td>
		</tr>
    </table>

    <h2 class="sub-head-settings">Video &amp; Content Pages</h2>
	<table cellpadding="0" cellspacing="0" width="100%" class="table table-striped table-bordered pm-tables pm-tables-settings">
    <tr>
      <td>"Related" videos limit</td>
      <td><input name="watch_related_limit" type="text" size="8" class="span1" value="<?php echo $config['watch_related_limit']; ?>" /> videos
      <a href="#" rel="popover" data-placement="right" data-trigger="hover" data-content="This value must be greater than 0 (zero)."><i class="icon-info-sign"></i></a></td>
    </tr>
    <tr>
      <td>"Popular" videos limit</td>
      <td><input name="watch_toprated_limit" type="text" size="8" class="span1" value="<?php echo $config['watch_toprated_limit']; ?>" /> videos
      <a href="#" rel="popover" data-placement="right" data-trigger="hover" data-content="This value must be greater than 0 (zero)."><i class="icon-info-sign"></i></a></td>
    </tr>
    <tr>
        <td width="20%">Show a floating AddThis.com widget (share buttons)</td>
        <td>
            <label><input name="show_addthis_widget" type="radio" value="1" <?php echo ($config['show_addthis_widget']==1) ? 'checked="checked"' : "";?> /> Yes</label>  
            <label><input name="show_addthis_widget" type="radio" value="0" <?php echo ($config['show_addthis_widget']==0) ? 'checked="checked"' : "";?> /> No</label>
            <a href="#" rel="popover" data-placement="right" data-trigger="hover" data-content="If enabled, a floating widget of sharing buttons (Facebook, Twitter and Google) will appear next to your content (videos, articles and pages)."><i class="icon-info-sign"></i></a>
        </td>
    </tr>
    </table>
    
    <h2 class="sub-head-settings">Listings</h2>
	<table cellpadding="0" cellspacing="0" width="100%" class="table table-striped table-bordered pm-tables pm-tables-settings">
      <tr>
          <td>Articles per browsing page</td>
          <td><input name="browse_articles" type="text" size="8" class="span1" value="<?php echo $config['browse_articles']; ?>" /> articles</td>
      </tr>
      <tr>
        <td width="20%">Videos per browsing page</td>
        <td><input name="browse_page" type="text" size="8" class="span1" value="<?php echo $config['browse_page']; ?>" /> videos
        <a href="#" rel="popover" data-placement="right" data-trigger="hover" data-content="Limit how many videos to show on each category or search results page."><i class="icon-info-sign"></i></a></td>
      </tr>
	 <tr>
		<td>"<a href="<?php echo _URL; ?>/newvideos.php">New videos</a>" page</td>
		<td><input name="new_page_limit" type="text" size="8" class="span1" value="<?php echo $config['new_page_limit']; ?>" /> videos
        <a href="#" rel="popover" data-placement="right" data-trigger="hover" data-content="Limit how many videos to list on the 'New Videos' page."><i class="icon-info-sign"></i></a></td>
	 </tr>
	 <tr>
		<td>"<a href="<?php echo _URL; ?>/topvideos.php?do=recent">Top videos</a>" page</td>
		<td><input name="top_page_limit" type="text" size="8" class="span1" value="<?php echo $config['top_page_limit']; ?>" /> videos
        <a href="#" rel="popover" data-placement="right" data-trigger="hover" data-content="Limit how many videos to list on the 'Top Videos' page."><i class="icon-info-sign"></i></a></td>
	 </tr>
      <tr>
        <td>Refresh "<a href="<?php echo _URL; ?>/topvideos.php?do=recent">Top videos</a>" page</a> every</td>
        <td>
		<input name="chart_days" type="text" size="8" class="span1" value="<?php echo $config['chart_days']; ?>" /> days
        <a href="#" rel="popover" data-placement="right" data-trigger="hover" data-content="Insert <strong>0 (zero)</strong> to prevent the list from being refreshed. This will result in having an 'All time' top videos chart/list."><i class="icon-info-sign"></i></a>
        </td>
        </tr>
	  <tr>
        <td>"My Favorites" limit</td>
        <td>
		<input name="fav_limit" type="text" size="8" class="span1" value="<?php echo $config['fav_limit']; ?>" /> videos
        <a href="#" rel="popover" data-placement="right" data-trigger="hover" data-content="Define how many videos a user can store in his <em>favorites</em> list"><i class="icon-info-sign"></i></a>
        </td>
      </tr>
	 <tr>
		<td>Comments per page</td>
		<td><input name="comments_page" type="text" size="8" class="span1" value="<?php echo $config['comments_page']; ?>" />
        <a href="#" rel="popover" data-placement="right" data-trigger="hover" data-content="Limit the number of comments for each article/video."><i class="icon-info-sign"></i></a></td>
	 </tr>
    </table>

    <h2 class="sub-head-settings">Thumbnails &amp; Avatars</h2>
	<table cellpadding="0" cellspacing="0" width="100%" class="table table-striped table-bordered pm-tables pm-tables-settings">
      <tr>
        <td width="20%">Video Thumbnails</td>
        <td>
        <input name="thumb_video_w" type="text" size="8" class="span1" value="<?php echo $config['thumb_video_w']; ?>" style="width:30px;" /> x <input name="thumb_video_h" type="text" size="8" class="span1" value="<?php echo $config['thumb_video_h']; ?>" style="width:30px;" /> <small>px</small>
        <a href="#" rel="popover" data-placement="right" data-trigger="hover" data-content="Assign the maximum width and height for video thumbnails. Uploaded thumbnails will be resized to fit these specifications. <br><strong>Format</strong>: WIDTH x HEIGHT (in pixels)"><i class="icon-info-sign"></i></a>
        </td>
      </tr>
	 <tr>
		<td>Article Thumbnails</td>
        <td>
        <input name="thumb_article_w" type="text" size="8" class="span1" value="<?php echo $config['thumb_article_w']; ?>" style="width:30px;" /> x <input name="thumb_article_h" type="text" size="8" class="span1" value="<?php echo $config['thumb_article_h']; ?>" style="width:30px;" /> <small>px</small>
        <a href="#" rel="popover" data-placement="right" data-trigger="hover" data-content="Assign the maximum width and height for article thumbnails. Uploaded thumbnails will be resized to fit these specifications. <br><strong>Format</strong>: WIDTH x HEIGHT (in pixels)"><i class="icon-info-sign"></i></a>
        <?php if ( $config['mod_article'] != 1 ) : ?>
		<span class="label label-warning">The 'Article Module' is disabled. Visit the 'Settings' page to enable it.</span>
		<?php endif; ?>
        </td>
	 </tr>
	 <tr>
		<td>User Avatar</td>
        <td>
        <input name="thumb_avatar_w" type="text" size="8" class="span1" value="<?php echo $config['thumb_avatar_w']; ?>" style="width:30px;" /> x <input name="thumb_avatar_h" type="text" size="8" class="span1" value="<?php echo $config['thumb_avatar_h']; ?>" style="width:30px;" /> <small>px</small>
        <a href="#" rel="popover" data-placement="right" data-trigger="hover" data-content="Limit how many videos to show on each category browing page."><i class="icon-info-sign"></i></a>
        </td>
	 </tr>
    </table>
	</div>
    
	<div class="tab-pane fade<?php echo ($selected_tab_view == 't2' || $selected_tab_view == 'customize') ? ' in active' : '';?>" id="t2">    
	<h2 class="sub-head-settings">Customize Theme</h2>
    <?php
	if ($config['template_f'] != 'default') 
	{
	?>
    <div class="alert alert-warning">Sorry, the <strong><?php echo ucfirst($config['template_f']); ?></strong> theme doesn't support any customizations.</div>
    <?php
	} else {
	?>
    <div class="alert alert-success">
    The <strong><?php echo ucfirst($config['template_f']); ?></strong> theme supports customizations.
    </div>
	<a href="customize.php" class="btn btn-medium btn-blue" target="_blank"><i class="icon-share icon-white"></i> Launch the customizer</a>
    <?php
	}
	?>
	</div>

	<div class="tab-pane fade<?php echo ($selected_tab_view == 't3' || $selected_tab_view == 'store') ? ' in active' : '';?>" id="t3">
	<h2 class="sub-head-settings">Theme Store</h2>

    <div class="well well-small">Personalize your video site by using a premium theme from the PHPSUGAR's theme collection. Below is a list of the available themes compatible with PHP Melody v<?php echo _PM_VERSION; ?>.</div>
    <hr />
    <div class="pm-themes">
    	<?php
 
 		$data_serialized = cache_this('get_theme_store_data', 'get_theme_store_data');
		$data = unserialize($data_serialized);

		if (is_array($data) && count($data) > 0) : 

			if ($data['items_count'] > 0) : ?>

			<ul class="row-fluid pm-themes-list">
	            <?php 
				foreach ($data['items'] as $k => $theme) : 
					
					$theme_mark_new = false;
					
					if (array_key_exists('pubDate', $theme) && $theme['pubDate'] != '')
					{
						$theme['release_date_timestamp'] = strtotime($theme['pubDate']);
						if ((time() - $theme['release_date_timestamp']) <= 2678400) // a month
						{
							$theme_mark_new = true;
						}
					}
					else
					{
						$theme['release_date_timestamp'] = 0;
					}
				
				?>
				<li class="theme-item">
					<h3><?php echo $theme['title'];?></h3>
	                <?php if ($theme_mark_new) : ?>
						<div class="theme-label">NEW</div>
					<?php endif; ?>
	                <a href="<?php echo $theme['preview_url'];?>" class="theme-preview" target="_blank" title="Preview <?php echo str_replace('"', '', $theme['title']);?> Theme">
					<img src="<?php echo $theme['thumb_url'];?>" alt="Theme Image" border="0" class="theme-thumb" />
					</a>
	                <a href="<?php echo $theme['preview_url'];?>" target="_blank" class="btn btn-small btn-link">Preview</a>
	                <a href="<?php echo $theme['buy_url'];?>" target="_blank" class="btn btn-small btn-link">Order now</a>
	            </li>
				<?php endforeach; ?>
	        </ul>
			<?php else : ?>
				<div class="alert alert-warning">No themes available at the moment. <strong><a href="http://www.96down.com/phpmelody_templates.html?utm_source=install_footer" target="_blank">Click here</a></strong> to visit the Theme Store.</div>
			<?php endif; ?>
		<?php else : ?>
			<div class="alert alert-warning">Sorry, couldn't retrieve data from the Theme Store. <strong><a href="http://www.96down.com/phpmelody_templates.html?utm_source=install_footer" target="_blank">Click here</a></strong> to visit the Theme Store.</div>
		<?php endif;?>
    </div><!--.pm-themes-->


    </div>
    </div>
	</td>
    </tr>
</table>
</div>

<div class="clearfix"></div>

<div id="stack-controls" class="list-controls">
<input name="views_from" type="hidden" value="2"  />
<input type="hidden" name="settings_selected_tab" value="<?php echo ($selected_tab_view != '') ? $selected_tab_view:  't1';?>" />
<div class="btn-toolbar">
    <div class="btn-group">
		<button type="submit" name="submit" value="Save" class="btn btn-small btn-success btn-strong">Save changes</button>
	</div>
</div>
</div><!-- #list-controls -->

</div>
</form>

    </div><!-- .content -->
</div><!-- .primary -->
<?php
include('footer.php');
?>