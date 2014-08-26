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
$load_scrolltofixed = 1;
$load_chzn_drop = 1;
$load_tagsinput = 1;
$load_ibutton = 1;
$load_prettypop = 1;
$_page_title = 'انتقال از کاربر یوتیوب';
include('header.php');

$action = trim($_GET['action']);

$post_n_get = 0;
$post_n_get = count($_POST) + count($_GET);
$curl_error = '';
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
		case 'MEDIA:GROUP':
			$youtube_index = $name;
			$youtube[$youtube_array_counter][$youtube_index][$attrs['YT:FORMAT']] = array('URL' => $attrs['URL'],
																	 'TYPE' => $attrs['TYPE']
																	);
		break;
		case 'MEDIA:THUMBNAIL':
			$youtube_index = $name;
			$youtube[$youtube_array_counter][$youtube_index]['THUMBNAIL_'. strtoupper($attrs['YT:NAME'])] = $attrs['URL'];
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
function grab_and_parse_xml($file)
{
	global $action, $curl_error;
	
	$xml_parser = xml_parser_create();
	xml_set_element_handler($xml_parser, "startElement", "endElement");
	xml_set_character_data_handler($xml_parser, "characterData");
	$error = 0;
	
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
			if ($action == 'favorites' && strlen($data) > 0)
			{
				die(sprintf('<div class="alert alert-error">%s</div>', htmlentities($data)));
			}
			else
			{
				die(sprintf('<div class="alert alert-error">XML error: %s at line %d</div>',
			       xml_error_string(xml_get_error_code($xml_parser)),
			       xml_get_current_line_number($xml_parser)));
			}
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
	        	if ($action == 'favorites' && strlen($data) > 0)
				{
					die(sprintf('<div class="alert alert-error">%s</div>', htmlentities($data)));
				}
				else
				{
					die(sprintf('<div class="alert alert-error">XML error: %s at line %d</div>',
							xml_error_string(xml_get_error_code($xml_parser)),
							xml_get_current_line_number($xml_parser)));
				}
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
            <li class="active"><a href="#help-overview" data-toggle="tab">Overview</a></li>
            <li><a href="#help-onthispage" data-toggle="tab">Filtering</a></li>
          </ul>
          <div class="tab-content">
            <div class="tab-pane fade in active" id="help-overview">
            <p>This page allows you to easily import from any youtube channel/user. If you already have a channel on Youtube, you can get your PHP MELODY site up and running instantly.<br />
Simply type the desired YouTube username and start importing.</p>
            <p>The results will also include any available playlists and favorites belonging to the user.</p>
            </div>
            <div class="tab-pane fade" id="help-onthispage">
            <p>Each result is organized in a stack containing thumbnails, the video title, category, description and tags. Data such as video duration, original URL and more will be imported automatically.</p>
            
            <p>Youtube provides three thumbnails for each video and PHP MELODY allows you to choose the best one for your site. By default, the chosen thumbnail is the largest one, but changing it will be represented by a blue border.
            You can also do a quality control by using the video preview. Just click the play button overlaying the large thumbnail image and the video will be loaded in a window.</p>
            
            <p>By default none of the results is selected for import. Clicking on the top right switch from each stack will select it for importing. This is indicated by a green highlight of the stack. If you’re satisfied with all the results and wish to import them all at once, you can do that as well by selecting the &quot;SELECT ALL VIDEOS&quot; checkbox (bottom left).</p>
            <p>Enjoy!</p>
            </div>

          </div>
        </div> <!-- /tabbable -->
        </div><!-- .span12 -->
    </div><!-- /help-assist -->
    <div class="content">
	<a href="#" id="show-help-assist">Help</a>
    <h2>Import from Youtube User</h2>
    <?php echo $info_msg; 
	load_categories();
	if (count($_video_categories) == 0) : ?>
		<div class="alert alert-error">Please <a href="cat_manager.php">create a category</a> first.</div>
	<?php
	endif;
    if (empty($_GET['action'])) : ?>
    <div class="alert alert-info">
    Import playlists, favorites and uploaded videos from any Youtube.com user.<br /> <small>Please note that <strong>private</strong> playlists will appear as being empty.</small>
    </div>
    <?php endif; ?>

	<?php if($_POST['username'] != '' || $_GET['username'] != '') :?>
    <div class="btn-group opac7 list-choice pull-right">
    <button class="btn btn-normal btn-small" data-toggle="button" id="list"><i class="icon-th"></i> </button>
    <button class="btn btn-normal btn-small" data-toggle="button" id="stacks"><i class="icon-th-list"></i> </button>
    </div>
    <?php endif; ?>
<form name="search_username" action="import_user.php?action=search" method="post" class="form-inline">
<input name="username" type="text"  value="<?php if($_POST['username'] != '') echo $_POST['username']; elseif($_GET['username'] != '') echo $_GET['username']; else echo 'Youtube username';?>" placeholder="Youtube username" size="30" maxlength="30" />
<select name="results" style="width: 100px;">
  <option value="10" <?php if($_POST['results'] == 10 || $_GET['results'] == 10) echo 'selected="selected"'; ?>>10 results</option>
  <option value="20" <?php if(($_POST['results'] == 20 || $_GET['results'] == 20) || ($_POST['results'] == '' && $_GET['results'] == '')) echo 'selected="selected"'; ?>>20 results</option>
  <option value="30" <?php if($_POST['results'] == 30 || $_GET['results'] == 30) echo 'selected="selected"'; ?>>30 results</option>
  <option value="50" <?php if($_POST['results'] == 50 || $_GET['results'] == 50) echo 'selected="selected"'; ?>>50 results</option>
</select>
<button type="submit" name="submit" class="btn" value="Find" id="searchVideos" data-loading-text="Searching...">Find</button> <span class="searchLoader"><img src="img/ico-loading.gif" width="16" height="16" /></span>
<button type="button" class="btn btn-link btn-mini" data-toggle="button" id="import-options">options</button> 
<hr />
<div id="import-opt-content">
<input type="checkbox" name="autofilling" id="autofilling" value="1" <?php if($_POST['autofilling'] == "1" || $_GET['autofilling'] == "1" || $post_n_get == 0) echo 'checked="checked"'; ?> />
<label for="autofilling">Auto-populate the video title</label>
<br />
<input type="checkbox" name="autodata" id="autodata" value="1" <?php if($_POST['autodata'] == "1" || $_GET['autodata'] == "1" || $post_n_get == 0) echo 'checked="checked"'; ?> />
<label for="autodata">Auto-populate with data from Youtube (tags and description)</label>
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
									'other_attr' => 'multiple="multiple" size="3"',
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
$autodata = 0;
$autofilling = 0;
$overwrite_category = array();

if(isset($_POST['submit']) && !empty($_POST['username']) && ($action == 'search'))
{
	$username = trim($_POST['username']);
	if(detect_russian($username) == true) {
		echo '<div class="alert alert-warning">Unfortunately the Youtube Search API does not support usernames containing cyrillic characters. To import videos from this user, follow these simple steps: <a href="http://help.phpmelody.com/how-to-import-from-youtube-com-users-with-russian-characters/" target="_blank">http://help.phpmelody.com/how-to-import-from-youtube-com-users-with-russian-characters/</a></div>';
		echo '</div></div>';
		include('footer.php');
		exit();
	}
	$import_results = $_POST['results'];
	$autofilling = ($_POST['autofilling'] == '1') ? 1 : 0;
	$autodata = ($_POST['autodata'] == '1') ? 1 : 0;
	
	if (is_array($_POST['use_this_category']))
	{
		$overwrite_category = $_POST['use_this_category'];
	}
	
}
elseif($action != '')
{	
	$username = trim($_GET['username']);
	$import_results = ($_GET['results'] != '') ? $_GET['results'] : 20;
	$autofilling = ($_GET['autofilling'] == 1) ? 1 : 0;
	$autodata = ($_GET['autodata'] == 1) ? 1 : 0;
	
	if($_GET['oc'] == 1)	//	oc = overwrite_category
	{
		$overwrite_category = (array) explode(',', $_GET['utc']);	//	utc = use_this_cateogory
	}
}

if(($action == 'search' || $action == 'favorites' || $action == 'playlists') && ($username != ''))
{
	$exec_start = get_micro_time();
	
	//	don't allow any white spaces in the username;
	$username = str_replace(" ", "", $username);
	
	if($action == 'playlists')
	{
		define('PHPMELODY', true);
		require_once('./src/youtube.php');
	}

	//	Step 1 - grab user's playlist
	$file = "http://gdata.youtube.com/feeds/api/users/".$username."/playlists?start-index=1&max-results=".$import_results;

	$youtube = array();
	$youtube_index = "";
	$youtube_array_counter = 0;
	grab_and_parse_xml($file);
	
	$total_results = count($youtube);
?>
  <div id="playlists-jump"></div>
  <nav id="import-nav" class="tabbable" role="navigation">
  <h2 class="h2-import pull-left">Results from: <em><?php echo $username; ?></em></h2>
      <ul class="nav nav-tabs pull-right">
      <li <?php if($action == 'search') { ?> class="active"<?php } ?>><a href="import_user.php?action=search&username=<?php echo $username. '&autofilling='.$autofilling.'&autodata='.$autodata.'&oc=1&utc='. implode(',', $overwrite_category); ?>">Latest Uploads</a></li>
      <li <?php if($action == 'playlists') { ?> class="active"<?php } ?>><a href="#playlists-jump" id="show-playlists">Playlists</a></li>
      <li <?php if($action == 'favorites') { ?> class="active"<?php } ?>><a href="import_user.php?action=favorites&username=<?php echo $username. '&autofilling='.$autofilling.'&autodata='.$autodata.'&oc=1&utc='. implode(',', $overwrite_category); ?>">Favorites</a></li>
      </ul>
  </nav>
  <div id="import_user">
    <?php
	
	if($youtube[0]['OPENSEARCH:TOTALRESULTS'] != 0)
	{
		echo '<ul class="import-playlists" id="playlists">';
		$buff_link = '';
		for($i = 0; $i < $total_results; $i++) 
		{
			$temp 		 = explode('/', $youtube[$i]['ID']);
			$playlist_id = $temp[ count($temp)-1 ];
			if ($_GET['playlistid'] == $youtube[$i]['YT:PLAYLISTID']) {
				$buff_link = '<li class="playlist-selected">';
			} else {
				$buff_link = '<li class="border-radius3">';
			}
			$buff_link .= '<a href="import_user.php?action=playlists&username='.$username.'&results='. $import_results .'&playlistid='.$playlist_id.'&title='.$youtube[$i]['TITLE'];
			$buff_link .= '&autofilling='.$autofilling.'&autodata='.$autodata.'&oc=1&utc='. implode(',', $overwrite_category);
			$buff_link .= '">';
			$buff_link .= '<img src="img/playlist-overlay.png" class="playlist-overlay">';
			$buff_link .= '<img src="'.$youtube[$i]['MEDIA:THUMBNAIL']['THUMBNAIL_MQDEFAULT'].'" class="playlist-thumb" /><h4 class="alpha60">'.$youtube[$i]['TITLE'].'</h4></a>';
			$buff_link .= '</li>';
			echo $buff_link;
		}
		echo '</ul>';
		echo '<div class="clearfix"></div>';
	}

	else
	{
		echo '<div class="alert alert-info">'. $username . ' doesn\'t have any playlists.</div>';
	}

	unset($youtube, $youtube_index, $youtube_array_counter);

	//	Step 2 - generate favorites link
	echo '<div id="content-to-hide">';
	//	Step 3
	$page = (int) $_GET['page'];

	if(empty($page))
		$page = 1;
		
	$start_from = ($page * $import_results) - $import_results + 1;

	switch($action)
	{
		case 'search':
			$file = "http://gdata.youtube.com/feeds/api/users/".$username."/uploads?start-index=".$start_from."&max-results=".$import_results;
			echo "<h2 class='sub-heading'><strong>Latest Uploads</strong></h2>";
		break;
		case 'favorites':
			$file = "http://gdata.youtube.com/feeds/api/users/".$username."/favorites?start-index=".$start_from."&max-results=".$import_results;
			echo "<h2 class='sub-heading'><strong>".ucfirst($username)."'s Favorite Videos</strong></h2>";
		break;
		case 'playlists':
			$play_id = trim($_GET['playlistid']);
			$title  = urldecode($_GET['title']);
			
			$file = "http://gdata.youtube.com/feeds/api/playlists/".$play_id."?start-index=".$start_from."&max-results=".$import_results."&v=2";
			echo "<h2 class='sub-heading'><strong>Playlist: <em>".$title."</em></strong></h2>";
		break;
	}

	$youtube = array();
	$youtube_index = "";
	$youtube_array_counter = 0;
	grab_and_parse_xml($file);
    ?>
    <form name="import_videos" action="import.php?action=import" method="post">
        <?php $modframework->trigger_hook('admin_import_importopts'); ?>

    <div id="vs-grid">
	<?php
	
	// begin formatting
	$total_results = count($youtube);
	$alt 	 	= 0;
	$counter 	= 1;
	$duplicates = 0;
	$total_search_results = $youtube[0]['OPENSEARCH:TOTALRESULTS'];
	
	$youtube_mirror = array();
	$youtube_mirror = $youtube;
	
	if($youtube_mirror[0]['OPENSEARCH:TOTALRESULTS'] != 0)
	{
		for($i = 0; $i < $total_results; $i++) 
		{
			$count_vids	= 0;
			$pieces		= array();
			
			if($action == 'playlists')
			{
				$yt_id 		 = $youtube[$i]['YT:VIDEOID'];
			}
			else
			{
				$pieces		 = explode("/", $youtube[$i]["ID"]);
				$yt_id 		 = $pieces[count($pieces) - 1];
			}
			
			$count_vids	 = count_entries('pm_videos', 'yt_id', $yt_id);
			if($count_vids == 0)
			{
				$title		 = str_replace('"', '&quot;', $youtube[$i]['MEDIA:TITLE']);
				$url		 = 'http://www.youtube.com/watch?v='. $yt_id;
				$description = $youtube[$i]['MEDIA:DESCRIPTION'];
				$tags		 = $youtube[$i]['MEDIA:KEYWORDS'];
				$restriction = $youtube[$i]['MEDIA:RESTRICTION'];
				$yt_length = $youtube[$i]['YT:DURATION'];
				
				if($action == 'playlists')
				{
					$autofill 	 = array( 
									0 => $youtube[$i]["NAME"], 
									1 => $title);
				}
				$restriction = $youtube[$i]['MEDIA:RESTRICTION'];
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
				$col2 = '';
				$alt++;	
				

				if(is_array($restriction))
				{
					$col_unembed = 'table_row_unembed';
					$georestriction = 'This video is ';
					$georestriction .=  ($restriction['RELATIONSHIP'] == 'deny') ? 'geo-restricted' : 'available only'; 
					$georestriction .= ' in the following countries: '.$restriction['LIST'];
				}

		?>
        <div class="video-stack" id="stackid-[<?php echo $counter;?>]">
            <div style="font-size: 10px; font-weight: normal" rel="tooltip" title="Select this video for import">
            <div class="on_off">
            <label for="video_ids[<?php echo $counter;?>]">IMPORT</label>
            <input type="checkbox" id="import-[<?php echo $counter;?>]" name="video_ids[<?php echo $counter;?>]" value="<?php echo $url.'" '; if($no_embed == 1 || $private == 1) echo 'disabled="disabled" id="check_ignore"'; ?> />
            </div>
            </div>
            <a id="video-id-[<?php echo $counter;?>]"></a>
            <input id="video-title[<?php echo $counter;?>]" name="video_title[<?php echo $counter;?>]" type="text" value="<?php if ($autofilling) echo $title; ?>" size="20" class="video-stack-title required_field" rel="tooltip" title="Click to edit" onClick="SelectAll('video-title[<?php echo $counter;?>]');" />
            <div class="clearfix"></div>
            <div class="video-stack-left">
            <ul class="thumbs_ul_import">
                <li class="stack-thumb-selected stack-thumb">
                <?php if (is_array($restriction)) : ?>
                <span class="video-stack-geo"><a href="#video-id-[<?php echo $counter;?>]" rel="tooltip" data-placement="right" title="<?php echo $georestriction; ?>"><img src="img/ico-geo-warn.png" /></a></span>
                <?php endif; ?>
                <span class="stack-thumb-text"><a href="#video-id-[<?php echo $counter;?>]" rel="tooltip" data-placement="right" title="The default thumbnail for this video.">SELECTED</a></span>
                <span class="stack-video-duration"><?php echo sec2hms($yt_length); ?></span>
                <span class="stack-preview"><a href="http://www.youtube.com/v/<?php echo $yt_id; ?>&autoplay=1&v=<?php echo $yt_id; ?>" rel="prettyPop[flash]" title="<?php echo $title; ?>"><div class="pm-sprite ico-playbutton"></div></a></span>
                <img src="http://img.youtube.com/vi/<?php echo $yt_id; ?>/mqdefault.jpg" alt="Thumb 1" width="154" height="117" border="0" name="video_thumbnail" videoid="<?php echo $yt_id; ?>" rowid="<?php echo $counter;?>" class="" />
                </li>
                <li class="thumbs_li_default stack-thumb-small">
                <span class="stack-thumb-text"><a href="#video-id-[<?php echo $counter;?>]" rel="tooltip" data-placement="right" title="The default thumbnail for this video.">SELECTED</a></span>
                <img src="http://img.youtube.com/vi/<?php echo $yt_id; ?>/2.jpg" alt="Thumb 2" width="71" height="53" border="0" name="video_thumbnail" videoid="<?php echo $yt_id; ?>" rowid="<?php echo $counter;?>" class="" />
                </li>
                <li class="thumbs_li_default stack-thumb-small">
                <span class="stack-thumb-text"><a href="#video-id-[<?php echo $counter;?>]" rel="tooltip" data-placement="right" title="The default thumbnail for this video.">SELECTED</a></span>
                <img src="http://img.youtube.com/vi/<?php echo $yt_id; ?>/3.jpg" alt="Thumb 3" width="71" height="53" border="0" name="video_thumbnail" videoid="<?php echo $yt_id; ?>" rowid="<?php echo $counter;?>" class="" />
                </li>
            </ul>
            <div class="clearfix"></div>
            
            <label>
            <input type="checkbox" name="featured[<?php echo $counter;?>]" id="check_ignore" value="1" /> <small><span class="label label-featured">FEATURED</span></small>
            </label>
            </div><!-- .video-stack-left -->
            <div class="video-stack-right noSearch clearfix">
            <label>CATEGORY  <small style="color:red;">*</small></label>
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
            <input type="hidden" id="thumb_url_<?php echo $counter;?>" name="thumb_url[<?php echo $counter;?>]" value="http://img.youtube.com/vi/<?php echo $yt_id; ?>/mqdefault.jpg" />
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
		$exec_end = get_micro_time();
	}	//	end if()
	else
	{
		?>
        <div class="alert alert-block">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <p>Sorry, nothing found!</p>
        <p>Remember that private videos will not appear in these results.</p>
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
				
				// generate pagination
				if($total_search_results > 0)
				{
				?>
				 <div class="pagination pagination-centered">
				  <?php
					// generate smart pagination
					$filename = 'import_user.php';
					$ext = 'action='.$action.'&username='.$username.'&results='.$import_results.'&page='.$page;
					$ext.= '&autofilling='.$autofilling.'&autodata='.$autodata;
					
					$ext .= (count($overwrite_category) > 0) ? '&oc=1&utc='. implode(',', $overwrite_category) : '&oc=0&utc=';
					
					if ($action == 'playlists')
					{
						$ext .= '&playlistid='. $_GET['playlistid'] .'&title='. $_GET['title'];
					}
					
					$pagination = '';
					$pagination = a_generate_smart_pagination($page, $total_search_results, $import_results, 1, $filename, $ext);
					echo $pagination;
					
				  ?>
				 </div>
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
        
	<div align="right">
	<input name="yt_username" type="hidden" value="<?php echo $username; ?>"/>

	<!-- search form information -->
	<input type="hidden" name="username" value="<?php echo htmlentities($username, ENT_COMPAT); ?>" />
	<input type="hidden" name="results" value="<?php echo $import_results; ?>" />
	<input type="hidden" name="autofilling" value="<?php echo $autofilling; ?>" />
	<input type="hidden" name="autodata" value="<?php echo $autodata; ?>" />
	<input type="hidden" name="overwrite_category" value="<?php echo ($_GET['oc'] == 1 || is_array($_POST['use_this_category'])) ? '1' : '0'; ?>" />
	<input type="hidden" name="use_this_category" value="<?php echo implode(',', $overwrite_category); ?>" />

   </form>
  </div><!-- #import-user -->
  </div><!-- #content-to-hide -->
<?php
}
elseif($username == '' && $action != '')
{
	echo "Invalid username";
}
?>

    </div><!-- .content -->
</div><!-- .primary -->
<?php
include('footer.php');
?>