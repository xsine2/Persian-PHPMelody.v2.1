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
$load_chzn_drop = 0;
*/
$load_scrolltofixed = 1;
$load_chzn_drop = 1;
$load_tagsinput = 1;
$load_ibutton = 1;
$load_prettypop = 1;
$_page_title = 'وارد کردن از یوتیوب';
include('header.php');

$action = '';
$action = trim($_GET['action']);

$post_n_get = 0;
$post_n_get = count($_POST) + count($_GET);

set_time_limit(120);
/*
 ** Define Local Functions Here **
 */
function get_micro_time()
{
	list($microsec, $sec) = explode(" ", microtime());
	return ((float)$microsec + (float)$sec);
}

function get_exec_time($end, $start)
{
	return round($end - $start, 2);
}

function startElement($parser, $name, $attrs) 
{
	global $youtube, $youtube_array_counter, $youtube_index;
	switch($name) 
	{
		case 'ENTRY':
			$youtube_index = "";
			break;
		case 'YT:DURATION':
			$youtube_index = $name;
			$youtube[$youtube_array_counter][$youtube_index] = $attrs['SECONDS'];
			break;
		case 'MEDIA:RESTRICTION':
			$youtube_index = $name;
			$youtube[$youtube_array_counter][$youtube_index] = array('TYPE' => $attrs['TYPE'],
																	 'RELATIONSHIP' => $attrs['RELATIONSHIP'],
																	 'LIST' => ''
																	);
			break;
		case 'YT:STATE':
			$youtube_index = $name;
			$youtube[$youtube_array_counter][$youtube_index] = array('NAME' => $attrs['NAME'],
																	 'REASONCODE' => $attrs['REASONCODE'],
																	 'REASON' => ''
																	);
			break;
		case 'MEDIA:CONTENT':
			$youtube_index = $name;
			$youtube[$youtube_array_counter][$youtube_index][$attrs['YT:FORMAT']] = array('URL' => $attrs['URL'],
																	 'TYPE' => $attrs['TYPE']
																	);
		break;
		default:
			$youtube_index = $name;
			$youtube[$youtube_array_counter][$youtube_index] = "";
			break;
	}
}

function endElement($parser, $name) 
{
	global $youtube, $youtube_index, $youtube_array_counter;
	switch($name) 
	{
		case "ENTRY":
			$youtube_array_counter++;
			break;
	}
	$youtube_index = "";
}

function characterData($parser, $data) 
{
	global $youtube, $youtube_array_counter, $youtube_index;
	if ($youtube_index != "")
	{
		if ($youtube_index == 'MEDIA:RESTRICTION')
		{
			$youtube[$youtube_array_counter][$youtube_index]['LIST'] .= trim($data);
		}
		else if ($youtube_index == 'YT:STATE')
		{
			$youtube[$youtube_array_counter][$youtube_index]['REASON'] .= trim($data);
		}
		else
		{
			$youtube[$youtube_array_counter][$youtube_index] .= trim($data);
		}
	}
}
/*
	** Finish defining Local Functions **
*/

?>
<div id="adminPrimary">
    <div class="row-fluid" id="help-assist">
        <div class="span12">
        <div class="tabbable tabs-left">
          <ul class="nav nav-tabs">
            <li class="active"><a href="#help-overview" data-toggle="tab">نمای کلی</a></li>
            <li><a href="#help-onthispage" data-toggle="tab">در این صفحه</a></li>
          </ul>
          <div class="tab-content">
            <div class="tab-pane fade in active" id="help-overview">
			<p>این صفحه تمام دیتابیس ویدئو یوتیوب را در اختیارت قرار می دهد. این موثر است که php melody همچنین تمام اطلاعات جزئی را از هر ویدئو یوتیوب بازیابی خواهد کرد پس شما نباید این کار را تکرار کنید. هنگام ذخیره کردن حساب های عظیم زمان شما می توانید سایت تان را بروز نگه دارید.</p>
			<p>با وارد کردن چند کیورد شروع کن ، انتخاب کن که شما چند نتیجه را می خواهید ببینید و بر روی لینک &quot;گزینه ها" کلیک کن تا داده ها بصورت خودکار از یوتیوب گرفته بشود.
توجه : در این زمان ، یوتیوب حداکثر برای هر صفحه 50 صفحه را اجازه می دهد تا نمایش داده شود.</p>
            </div>
            <div class="tab-pane fade" id="help-onthispage">
			<p>Each result is organized in a stack containing thumbnails, the video title, category, description and tags. Data such as video duration, original URL and more will be imported automatically.</p>
            <p>Youtube provides three thumbnails for each video and PHP MELODY allows you to choose the best one for your site. By default, the chosen thumbnail is the largest one, but changing it will be represented by a blue border.
            You can also do a quality control by using the video preview. Just click the play button overlaying the large thumbnail image and the video will be loaded in a window.</p>
            <p>By default none of the results is selected for import. Clicking on the top right switch from each stack will select it for importing. This is indicated by a green highlight of the stack. If you're satisfied with all the results and wish to import them all at once, you can do that as well by selecting the &quot;SELECT ALL VIDEOS” checkbox (bottom left).<br />
            Enjoy!</p>
            </div>
          </div>
        </div> <!-- /tabbable -->
        </div><!-- .span12 -->
    </div><!-- /help-assist -->
    <div class="content">
	<a href="#" id="show-help-assist">راهنما</a>
	<h2>وارد کردن از یوتیوب</h2>
	<?php echo $info_msg; ?>

<?php 

load_categories();
if (count($_video_categories) == 0) 
{ 
?>
	<div class="alert alert-error">Please <a href="cat_manager.php">create a category</a> first.</div>
<?php
}

if($_POST['keyword'] != '' || $_GET['username'] != '' || $_GET['keyword'] != '')
{
?>
<div class="list-choice btn-group pull-right">
<button class="btn btn-normal btn-small" data-toggle="button" id="list"><i class="icon-th"></i> </button>
<button class="btn btn-normal btn-small" data-toggle="button" id="stacks"><i class="icon-th-list"></i> </button>
</div>
<?php
}
?>
<form name="search_yt_videos" action="import.php?action=search" method="post" class="form-inline">
 <input name="keyword" type="text" value="<?php echo ($_POST['keyword'] != '') ? $_POST['keyword'] : str_replace("+", " ", $_GET['keyword']); ?>" size="35" placeholder="Type keywords to search for..." /> <!--onFocus="clearText(this)" onBlur="clearText(this)" -->
 <select name="results" style="width: 100px;">
  <option value="10" <?php if($_POST['results'] == 10 || $_GET['results'] == 10) echo 'selected="selected"'; ?>>10 results</option>
  <option value="20" <?php if($_POST['results'] == 20 || $_GET['results'] == 20 || $post_n_get == 0) echo 'selected="selected"'; ?>>20 results</option>
  <option value="30" <?php if($_POST['results'] == 30 || $_GET['results'] == 30) echo 'selected="selected"'; ?>>30 results</option>
  <option value="50" <?php if($_POST['results'] == 50 || $_GET['results'] == 50) echo 'selected="selected"'; ?>>50 results</option>
 </select>
 <button type="submit" name="submit" class="btn" id="searchVideos" data-loading-text="Searching...">Search</button> <span class="searchLoader"><img src="img/ico-loading.gif" width="16" height="16" /></span>
 <button type="button" class="btn btn-link btn-mini" data-toggle="button" id="import-options">options</button>
<hr />
<div id="import-opt-content">
<!--
<input type="checkbox" name="autofilling" id="autofilling" value="1" <?php if($_POST['autofilling'] == "1" || $_GET['autofilling'] == "1" || $post_n_get == 0) echo 'checked="checked"'; ?> />
<label for="autofilling">Auto-populate the video title</label>-->
<input type="checkbox" name="autodata" id="autodata" value="1" <?php if($_POST['autodata'] == "1" || $_GET['autodata'] == "1" || $post_n_get == 0) echo 'checked="checked"'; ?> />
<label for="autodata">Auto-populate with data from Youtube</label>
<br />
<label>Auto-populate results with this category</label>
<?php 
$selected_categories = array();
if (is_array($_POST['use_this_category']))
{
    $selected_categories = $_POST['use_this_category'];
}
else if (is_string($_POST['use_this_category']) && $_POST['use_this_category'] != '') 
{
    $selected_categories = (array) explode(',', $_POST['use_this_category']);
}
if ($_GET['utc'] != '')
{
    $selected_categories = (array) explode(',', $_GET['utc']);
}

$categories_dropdown_options = array(
                                'attr_name' => 'use_this_category[]',
                                'attr_id' => 'main_select_category',
                                'select_all_option' => false,
                                'spacer' => '&mdash;',
                                'selected' => $selected_categories,
                                'other_attr' => 'multiple="multiple" size="3" data-placeholder="Import videos into..."',
                                'option_attr_id' => 'check_ignore'
                                );
echo categories_dropdown($categories_dropdown_options);
?>
<hr />
</div>
</form>
<?php
if(empty($_GET['action'])) {
?>
<div id="stack-controls" style="display: none;"></div>
<?php
}
/*
 *  Import
 */  
if($_POST['submit'] == 'Import' && ($action == 'import'))
{
	$exec_start = get_micro_time();

	$arr_video_title = $_POST['video_title'];
	$arr_description = $_POST['description'];
	$arr_tags		 = $_POST['tags'];
	$arr_category	 = $_POST['genre'];
	$arr_url		 = $_POST['video_ids'];
	$yt_username	 = $_POST['yt_username'];
	$featured		 = $_POST['featured'];
	$thumb_url		 = $_POST['thumb_url'];
	
	$sources = a_fetch_video_sources();
	$source_id = $sources['youtube']['source_id'];

	$total_videos = count($arr_url);
	$imported_total = 0;
	
	define('PHPMELODY', true);
	require_once( "./src/youtube.php");
	
	$duplicate = 0;

	if(is_array($arr_url))
	foreach($arr_url as $i => $v)
	{	
		$duplicate = count_entries('pm_videos_urls', 'direct', $arr_url[$i]);
		
		if($duplicate == 0)
		{
			$video_details = array(	
								'uniq_id' => '',	
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
								'mp4' => '',	
								'direct' => '',	
								'tags' => '', 
								'featured' => 0,
								'restricted' => 0,
								'allow_comments' => 1 
								);
			
			$video_details['video_title'] = trim( $arr_video_title[$i] );
			$video_details['description'] = trim( $arr_description[$i] );
			$video_details['tags'] 		  = trim( $arr_tags[$i]		   );
			$video_details['category'] 	  = is_array($arr_category[$i]) ? implode(',', $arr_category[$i]) : '';
			$video_details['direct'] 	  = trim( $arr_url[$i]		   );
			$video_details['source_id']	  = $source_id;
			$video_details['language']	  = 1;
			$video_details['submitted']	  = $userdata['username'];

			$video_details['description'] = nl2br($video_details['description']);
			
			//	generate unique id;
			$found = 0;
			$uniq_id = '';
			//$i = 0;
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
			
			if($video_details['video_title'] != '')
			{
				$temp = array();
				//	grab video information
				do_main($temp, $video_details['direct'], false);
				
				$video_details['url_flv']	=	$temp['url_flv'];
				$video_details['mp4']		=	$temp['mp4'];
				$video_details['yt_length']	=	$temp['yt_length'];
				$video_details['yt_thumb']	=	$temp['yt_thumb'];
				$video_details['yt_id']	=	$temp['yt_id'];
				
				if($video_details['tags'] == '')
				{
					$video_details['tags']	=	$temp['tags'];
				}
				
				if ($thumb_url[$i] != '')
				{
					$video_details['yt_thumb'] = $thumb_url[$i];
				}
				
				//	download thumbnail
				$img = download_thumb($video_details['yt_thumb'], _THUMBS_DIR_PATH, $uniq_id);
				
				if ($featured[$i] == "1")
				{	
					$video_details['featured'] = 1;
				}
				$modframework->trigger_hook('admin_import_insertvideo_pre');
				$new_video = insert_new_video($video_details, $new_video_id);
				if($new_video !== true)
				{
					echo '<div class="alert alert-error"><em>A problem occurred! Could not insert this video in your database;</em><br /><strong>MySQL Reported:</strong> "'.$new_video[0].'"<br /><strong>Error Number:</strong> '.$new_video[1].'</div>';
				}
				else
				{
					$modframework->trigger_hook('admin_import_insertvideo_post');
					//	tags?
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
							insert_tags($video_details['uniq_id'], $tags);
					}
					$imported_total++;
				}
				unset($video_details, $temp);
			}
		}
		sleep(1);
	}	//	end for()
	
	$exec_end = get_micro_time();
	
	if ($imported_total == $total_videos)
	{
			$info_msg = '<div class="alert alert-success">The selected videos were successfully imported.';
	}
	else
	{
		$info_msg = '<div class="alert alert-success">Imported <strong>'.$imported_total.'</strong> out of <strong>'.$total_videos.'</strong> selected videos.';
	}

	if ($imported_total < $total_videos)
	{
		$info_msg .= '<br />Duplicated videos and videos without a title were skipped.';
	}
	
	$info_msg .= '<br />Import took <strong>' . get_exec_time($exec_end, $exec_start) . '</strong> seconds.';

	if($yt_username != '') 
	{
		$params = '';
		$params .= 'action=search&username='. $yt_username;
		$params .= '&results=' .$_POST['results'];
		$params .= '&autofilling=' .$_POST['autofilling'];
		$params .= '&autodata='. $_POST['autodata'];
		$params .= (is_array($_POST['use_this_category'])) ? '&oc=1&utc='. implode(',', $_POST['use_this_category']) : '&oc=0&utc=';
		$info_msg .= '</div><hr /><a href="import_user.php?'. $params .'" class="btn">&larr; Return to <em>'. $yt_username .'\'s</em> videos</a>';
	}
	$info_msg .= '</div>';
	echo $info_msg;
	echo '<div id="stack-controls" style="display: none;"></div>';
}

/*
 *  Search
 */ 
if($action == 'search' && ($_POST['keyword'] != '' || $_GET['keyword'] != ''))
{
   ?>
    <form name="import_videos" action="import.php?action=import" method="post">
    <?php $modframework->trigger_hook('admin_import_importopts'); ?>
    <div id="vs-grid">
	<?php
	
	$page = (int) $_GET['page'];

	if(empty($page))
		$page = 1;
	
	$autodata = 0;
	$autofilling = 0;
	$overwrite_category = array();
	
	if(isset($_POST['submit']) && !empty($_POST['keyword']))
	{
		$v				= trim($_POST['keyword']);
		$import_results	= $_POST['results'];
		
		if($_POST['autofilling'] == '1') 
		{
			$autofill = $_POST['keyword'];			
			$autofilling = 1;
		}
		if($_POST['autodata'] == '1')
		{
			$autodata = 1;
		}
		if (is_array($_POST['use_this_category']))
		{
			$overwrite_category = $_POST['use_this_category'];
		}
	}
	elseif($_GET['keyword'] != '')
	{
		$v				= urldecode($_GET['keyword']);
		//$v				= str_replace(" ", "+", $v);
		
		if($_GET['results'] != '')
		{
			$import_results	= (int) $_GET['results'];
		}
		else
		{
			$import_results = 20;
		}
		
		if($_GET['autofilling'] == 1)
		{
			$autofill = urldecode($_GET['keyword']);
			$autofilling = 1;
		}
		if($_GET['autodata'] == 1)
		{
			$autodata = 1;
		}
		if($_GET['oc'] == 1)	//	oc = overwrite_category
		{
			$overwrite_category = (array) explode(',', $_GET['utc']);	//	utc = use_this_cateogory
		}
	}

	$start_from = ($page * $import_results) - $import_results + 1;
	
	//	Send Request
	$search_term = str_replace("-", " ", $v);	
	$search_term = urlencode($search_term);
	$v 		 = urlencode($v);
	$file 	 = "http://gdata.youtube.com/feeds/api/videos?vq=".$search_term."&start-index=".$start_from."&max-results=".$import_results;
	$file 	.= '&format=5';
	$youtube = array();
	$youtube_index = "";
	$youtube_array_counter = 0;
	
	$xml_parser = xml_parser_create();
	xml_set_element_handler($xml_parser, "startElement", "endElement");
	xml_set_character_data_handler($xml_parser, "characterData");
	$error = 0;
	$curl_error = '';
	
	if ( function_exists('curl_init') ) 
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $file);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.7.5) Gecko/20041107 Firefox/1.0');
		
		$data = curl_exec($ch);
		$curl_error = curl_error($ch);
		curl_close($ch);
		
		if ( !xml_parse($xml_parser, $data, TRUE) ) 
		{
		   die(sprintf('<div class="alert alert-error">XML error: %s at line %d</div>',
		       xml_error_string(xml_get_error_code($xml_parser)),
		       xml_get_current_line_number($xml_parser)));
		}
	}
	else if ( ini_get('allow_url_fopen') == 1 ) 
	{
	   if ( $fp = @fopen($file, "r") ) 
	   {
	      while ($data = fread($fp, 4096)) 
	      {
	        if ( !xml_parse($xml_parser, $data, feof($fp)) ) 
	        {
	           die(sprintf('<div class="alert alert-error">XML error: %s at line %d</div>',
	            xml_error_string(xml_get_error_code($xml_parser)),
	            xml_get_current_line_number($xml_parser)));
	         }
	      }
	   }
	   else 
	   {
	      $error = 1;
	   }
	   @fclose($fp);
	}
	else 
	{
		$error = 1;
	}

	if( $error == 1 )
	{
	   // fopen failed, cURL failed, there's nothing else to do but quit
	   ?>
		<div class="alert alert-error">
			<strong>Unable to retrieve requested data.</strong>
			<br />
			<br />
			<?php if ($curl_error != '') 
			{
				echo $curl_error .'<br />';
			}
			?>
			<?php if ( ! function_exists('curl_init') && ! ini_get('allow_url_fopen')) : ?>
			Your system doesn't support remote connections.
			<br /> 
			Ask your hosting provider to enable either <strong>allow_url_fopen</strong> or the <strong>cURL extension</strong>.
			<?php endif;?>
		</div>
   </div><!-- .content -->
</div><!-- .primary -->
			<?php
			include('footer.php');
			exit();
	}
	xml_parser_free($xml_parser);

	// begin formatting
	$alt = 0;
	$total_results = count($youtube);
	$counter = 1;
	$duplicates = 0;
	$total_search_results = $youtube[0]['OPENSEARCH:TOTALRESULTS'];
	
	if($youtube[0]['OPENSEARCH:TOTALRESULTS'] != 0 && $youtube[0]['OPENSEARCH:TOTALRESULTS'] > $start_from)
	{	
		for($i = 0; $i < $total_results; $i++) 
		{
			$yid		 = explode("/", $youtube[$i]["ID"]);
			$id 		 = $yid[ count($yid)-1 ];
			$count_vids	 = 0;
			$count_vids	 = count_entries('pm_videos', 'yt_id', $id);
			if($count_vids == 0)
			{
				$title		 = str_replace('"', '&quot;', $youtube[$i]['MEDIA:TITLE']);
				$url		 = 'http://www.youtube.com/watch?v='.$id;
				$description = $youtube[$i]["MEDIA:DESCRIPTION"];
				$keywords	 = $youtube[$i]["MEDIA:KEYWORDS"];		
				$restriction = $youtube[$i]['MEDIA:RESTRICTION'];
				$yt_length = $youtube[$i]['YT:DURATION'];

				$no_embed	 = 0;
				if(array_key_exists('YT:NOEMBED', $youtube[$i]))
				{
					$no_embed = 1;
				}
				
				$private	 = 0;
				if(array_key_exists('YT:NOEMBED', $youtube[$i]))
				{
					$private = 1;
				}
				$col = ($alt % 2) ? 'table_row1' : 'table_row2';
				$alt++;		
				
				$buff_video_title = $youtube[$i]['MEDIA:TITLE'];
		?>



<?php
$col_unembed = '';
if($no_embed == 1 || $private == 1)
{
$col_unembed = 'table_row_unembed';
?>
<!--<div class="css_yellow_warn"><span onMouseover="showhint('This video will not work since the owner decided to disable embedding.', this, event, '350px')">YouTube disabled embedding for this video.</span></div>-->
<?php 
}
if(is_array($restriction))
{
$col_unembed = 'table_row_unembed';
	$georestriction = 'This video is ';
	$georestriction .=  ($restriction['RELATIONSHIP'] == 'deny') ? 'geo-restricted' : 'available only'; 
	$georestriction .= ' in the following countries: '.$restriction['LIST'];
}
?>
<div class="video-stack" id="stackid-[<?php echo $counter;?>]">
	<div style="font-size: 10px; font-weight: normal">
	<div class="on_off" rel="tooltip" title="Select this video for import">
    <label for="video_ids[<?php echo $counter;?>]">IMPORT</label>
    <input type="checkbox" id="import-[<?php echo $counter;?>]" name="video_ids[<?php echo $counter;?>]" value="<?php echo $url.'" '; if($no_embed == 1 || $private == 1) echo 'disabled="disabled" id="check_ignore"'; ?> />
    </div>
	</div>
	<a id="video-id-[<?php echo $counter;?>]"></a>
    <input id="video-title[<?php echo $counter;?>]" name="video_title[<?php echo $counter;?>]" type="text" value="<?php echo ucwords($buff_video_title); ?>" size="20" class="video-stack-title required_field" rel="tooltip" title="Click to edit" onClick="SelectAll('video-title[<?php echo $counter;?>]');" />
    <div class="clearfix"></div>
    <div class="video-stack-left">
	<ul class="thumbs_ul_import">
                    <li class="stack-thumb-selected stack-thumb">
                    <?php if (is_array($restriction)) : ?>
                    <span class="video-stack-geo"><a href="#video-id-[<?php echo $counter;?>]" rel="tooltip" data-placement="right" title="<?php echo $georestriction; ?>"><img src="img/ico-geo-warn.png" /></a></span>
                    <?php endif; ?>
                    <span class="stack-thumb-text"><a href="#video-id-[<?php echo $counter;?>]" rel="tooltip" data-placement="right" title="The default thumbnail for this video."><i class="icon-ok icon-white"></i></a></span>
                    <span class="stack-video-duration"><?php echo sec2hms($yt_length); ?></span>
                    <span class="stack-preview"><a href="http://www.youtube.com/v/<?php echo $id; ?>&autoplay=1&v=<?php echo $id; ?>" rel="prettyPop[flash]" title="<?php echo $title; ?>"><div class="pm-sprite ico-playbutton"></div></a></span>
                    <img src="http://img.youtube.com/vi/<?php echo $id; ?>/mqdefault.jpg" alt="Thumb 1" width="154" height="117" border="0" name="video_thumbnail" videoid="<?php echo $id; ?>" rowid="<?php echo $counter;?>" class="" />
                    </li>
                    <li class="thumbs_li_default stack-thumb-small">
                    <span class="stack-thumb-text"><a href="#video-id-[<?php echo $counter;?>]" rel="tooltip" data-placement="right" title="The default thumbnail for this video."><i class="icon-ok icon-white"></i></a></span>
                    <img src="http://img.youtube.com/vi/<?php echo $id; ?>/2.jpg" alt="Thumb 2" width="71" height="53" border="0" name="video_thumbnail" videoid="<?php echo $id; ?>" rowid="<?php echo $counter;?>" class="" />
                    </li>
                    <li class="thumbs_li_default stack-thumb-small">
                    <span class="stack-thumb-text"><a href="#video-id-[<?php echo $counter;?>]" rel="tooltip" data-placement="right" title="The default thumbnail for this video."><i class="icon-ok icon-white"></i></a></span>
                    <img src="http://img.youtube.com/vi/<?php echo $id; ?>/3.jpg" alt="Thumb 3" width="71" height="53" border="0" name="video_thumbnail" videoid="<?php echo $id; ?>" rowid="<?php echo $counter;?>" class="" />
                    </li>
	</ul>
    <div class="clearfix"></div>
    
    <label>
    <input type="checkbox" name="featured[<?php echo $counter;?>]" id="check_ignore" value="1" /> <small>Mark as <span class="label label-featured">FEATURED</span></small>
    </label>
    </div><!-- .video-stack-left -->
    <div class="video-stack-right noSearch clearfix">
    <label>CATEGORY <small style="color:red;">*</small></label>
    <div class="video-stack-cats">
	<?php
    $categories_dropdown_options = array(
                'attr_name' => 'genre['. $counter .'][]',
                'attr_id' => 'select_category-'. $counter,
                'select_all_option' => false,
                'spacer' => '&mdash;',
                'selected' => $overwrite_category,
                'other_attr' => 'multiple="multiple" size="3"',
                'option_attr_id' => 'check_ignore'
                );
    echo categories_dropdown($categories_dropdown_options);
    ?>
    </div>

    <div class="clear"></div>
    <label>DESCRIPTION</label>
    <textarea name="description[<?php echo $counter;?>]" id="description[<?php echo $counter;?>]" rows="2" class="video-stack-desc"><?php if($autodata) echo $description;?></textarea>
    <label class="control-label" for="tags">TAGS</label>
    <div class="tagsinput">
    <input type="text" id="tags_addvideo_<?php echo $counter;?>" name="tags[<?php echo $counter;?>]" value="<?php if($autodata) echo $keywords;?>" class="tags" />
    </div>          
    <input type="hidden" id="thumb_url_<?php echo $counter;?>" name="thumb_url[<?php echo $counter;?>]" value="http://img.youtube.com/vi/<?php echo $id; ?>/mqdefault.jpg" />
    </div> <!-- .video-stack-right -->
</div><!-- .video-stack -->
		<?php
				$counter++;
			}
			else
			{
				$duplicates++;
			}
		}	//	end for()
	}	//	end if()
	else
	{
		?>
 
        <div class="alert alert-block">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        Sorry...No results found! Try again or use different keywords.
        </div>

		<?php
	}
	
	if($duplicates == $total_results)
	{
		//	All videos found 
		?>

        <div class="alert alert-block">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <p>The videos results for this page are already in your database.</p>
        <p>Please try again by visiting the next page, selecting more than <?php echo $import_results; ?> results or by using different keywords.</p>
        </div>

	<?php
	}
	?>
        
        
			<div class="clearfix"></div>
            <div id="stack-controls" class="row-fluid">
            <div class="span4" style="text-align: left;">
                <label class="checkbox import-all">
                <input type="checkbox" name="checkall" id="checkall" class="btn" onclick="checkUncheckAll(this);"/> <small>SELECT ALL VIDEOS</small>
                </label>
            </div>

			<div class="span4">
			
			<?php
			//	generate pagination	
			if($total_search_results > 0)
			{
			 if($total_search_results > 1000)
			 {
				$total_search_results = 1000; // API limit
			 }
			?>
			
			<div class="pagination pagination-centered">
			<?php
			
			  // generate smart pagination
			  $filename = 'import.php';
			  $ext = 'action=search&keyword='.$v.'&results='.$import_results.'&page='.$page.'&autofilling='.$autofilling.'&autodata='.$autodata;
			  $ext .= (count($overwrite_category) > 0) ? '&oc=1&utc='. implode(',', $overwrite_category) : '&oc=0&utc=';
			  $pagination = '';
			  $pagination = a_generate_smart_pagination($page, $total_search_results, $import_results, 1, $filename, $ext);
			  echo $pagination;
			?>
			</div>
		   
		
			<div class="clearfix"></div>
			<?php
			} // end if($youtube[0]['OPENSEARCH:TOTALRESULTS'] > 0)
			?>
			
			</div>
            
			<div class="span4">
            <span class="importLoader"><img src="img/ico-loader.gif" width="16" height="16" /></span>
            <button type="submit" name="submit" class="btn btn-success btn-strong" value="Import" id="submitImport" data-loading-text="Importing...">Import <span id="status"><span id="count">0</span></span> videos </button>
			</div>
            </div><!-- #stack-controls -->
		</div><!-- #vs-grid -->
        <?php
		// end <table>
		?>
	<!-- search form information -->
	<input type="hidden" name="keyword" value="<?php echo htmlspecialchars(urldecode($v), ENT_COMPAT,'UTF-8',true); ?>" />
	<input type="hidden" name="results" value="<?php echo $import_results; ?>" />
	<input type="hidden" name="autofilling" value="<?php echo $autofilling; ?>" />
	<input type="hidden" name="autodata" value="<?php echo $autodata; ?>" />
	<input type="hidden" name="overwrite_category" value="<?php echo ($_GET['oc'] == 1 || is_array($_POST['use_this_category'])) ? '1' : '0'; ?>" />
	<input type="hidden" name="use_this_category" value="<?php echo implode(',', $overwrite_category); ?>" />

   </form>

<?php
}
?>
    </div><!-- .content -->
</div><!-- .primary -->
<?php
include('footer.php');
?>