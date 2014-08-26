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
$load_scrolltofixed = 1;
$load_chzn_drop = 1;
$_page_title = 'مدیریت ویدیوها';
include('header.php');

include_once(ABSPATH . 'include/rating_functions.php');

$action	= (int) $_GET['action'];
$page	= (int) $_GET['page'];
$filter = 'added';
$filters = array('broken', 'restricted', 'unchecked', 'localhost', 'featured', 'category', 'source', 'mostviewed', 'access', 'views', 'added');
$filter_value = 'desc';

if(in_array(strtolower($_GET['filter']), $filters) !== false)
{
	$filter = strtolower($_GET['filter']);
	$filter_value = $_GET['fv'];

	if (($filter == 'source' || $filter == 'category') && $filter_value == '')
	{
		$filter = 'added';
	}
}

if($page == 0)
	$page = 1;

// videos per page
$limit = (isset($_COOKIE['aa_videos_per_page'])) ? $_COOKIE['aa_videos_per_page'] : 30;

$from = $page * $limit - ($limit);

$categories = a_list_cats_simple();

switch($filter)
{
	case 'broken':
		
		$total_videos = count_entries('pm_videos', 'status', VS_BROKEN);
		
	break;
	
	case 'restricted':
		
		$total_videos = count_entries('pm_videos', 'status', VS_RESTRICTED);
		
	break;
	
	case 'unchecked':
		
		$total_videos = count_entries('pm_videos', 'status', VS_UNCHECKED);
		
	break;
	
	case 'localhost':
		
		$total_videos = count_entries('pm_videos', 'source_id', '1');
		
	break;
	
	case 'featured':
		
		$total_videos = count_entries('pm_videos', 'featured', '1');
		
	break;
	
	case 'category':
		
		$filter_value = (int) $filter_value;
		if ($filter_value > 0)
		{
			$total_videos = $categories[$filter_value]['total_videos'];
		}
		else if ($_GET['fv'] == '0')
		{
			$total_videos = count_entries('pm_videos', 'category', '0');
		}
		else
		{
			$total_videos = 0;
			unset($filter_value);
		}
		
	break;
	
	case 'source':
		
		$filter_value = (int) $filter_value;
		$total_videos = count_entries('pm_videos', 'source_id', $filter_value);
		
	break;
	
	default:
	case 'added':
	case 'views':
	case 'mostviewed':
		
		//$total_videos = count_entries('pm_videos', '', '');
		$total_videos = $config['total_videos'];
		
	break;
	
	case 'access':
		
		$filter_value = '1';
		$total_videos = count_entries('pm_videos', 'restricted', $filter_value);
		
	break;
}

if(!empty($_POST['submit']) && $_POST['submit'] == 'Search') 
{
	$search_query = secure_sql(trim($_POST['keywords']));
	$search_type = $_POST['search_type'];

	$videos = a_list_videos($search_query, $search_type, $from, $limit, $page);
	$total_videos = preg_match_all("/<\/tr>/", $videos, $matches);
} 
else 
{
	if($total_videos - $from == 1)
		$page--;
		
	$videos = a_list_videos('', '', $from, $limit, $page, $filter, $filter_value); 
	
	if($total_videos - $from == 1)
		$page++;	
}

// generate smart pagination
$filename = 'videos.php';
$pagination = '';

if(!isset($_POST['submit'])) 
	$pagination = a_generate_smart_pagination($page, $total_videos, $limit, 5, $filename, '&filter='. $filter .'&fv='. $filter_value);

?>
<div id="adminPrimary">
    <div class="row-fluid" id="help-assist">
        <div class="span12">
        <div class="tabbable tabs-left">
          <ul class="nav nav-tabs">
            <li class="active"><a href="#help-overview" data-toggle="tab">Overview</a></li>
            <li><a href="#help-onthispage" data-toggle="tab">Filtering</a></li>
            <li><a href="#help-bulk" data-toggle="tab">Terminology</a></li>
          </ul>
          <div class="tab-content">
            <div class="tab-pane fade in active" id="help-overview">
    		<p></p>
            <p>This page provides an excellent overview of your existing video database. Listed below are the latest videos and a bunch of tools to help you get the work done. Most of the times you can do maintenance work without leaving this page.</p>
            <p>The listings contain as much data as we could reasonably fit on a screen. The actionable items (edit, delete, etc.) will always be located in the last column (right side). Some icons such as the video source icons (2nd column) can be used to filter results belonging to that video source. </p>
            <p>As you scroll down the page you will notice a hovering panel at the bottom of your screen. The purpose is to place all major action within easy reach.</p>
            <p>This page also contains a &quot;DELETE ALL VIDEOS&quot; button which if clicked will delete your entire database of videos. This process is irreversible.</p>
            <p></p>
            </div>
            <div class="tab-pane fade" id="help-onthispage">
			<p>Listing pages such as this one contain a filtering area which comes in handy when dealing with a large number of entries. The filtering options is always represented by a gear icon positioned on the top right area of the listings table. Clicking this icon usually reveals a  search form and one or more drop-down filters.</p>
            </div>
            <div class="tab-pane fade" id="help-bulk">
<p><strong>Video sources</strong>:
Since PHP Melody can automatically recognize, import and handle videos from a vast selection of top video sites, as well as handle video uploads, it's important to define each video as having a source. For example, sources can be: Youtube, Vimeo but also your own AWS S3 hosted videos and/or videos uploaded from this admin area.</p>
<p><strong>Featured videos</strong>: videos marked as featured will appear within the homepage player. If more than one video is featured, they will be loaded randomly.</p>
<p><strong>Video status</strong>: we incorporated a way to automatically check videos from remote locations and see whether they are still working or not.  While the system works well for Youtube and a dozen other sources, we recommend using it as a guide rather than a reliable indicator before deleting videos in bulk.</p>
<p>The video status is represented by a round icon on the 4th column (from left). You can choose to check more than one video at a time by using the select all box (top left table corner) and then clicking the &quot;check status&quot; button.</p>
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
                <div class="floatL"><strong class="blue"><?php echo pm_number_format($total_videos); ?></strong><span>ویدیوها</span></div>
                <div class="blueImg"><div class="pm-sprite ico-videos-small"></div></div>
            </li>
        </ul><!-- .pageControls -->
    </div>
<?php

if ($_GET['action'] == 'deleted') 
{
	echo "<div class=\"alert alert-success\">Video(s) successfully removed.</div>";
}

if ($_GET['action'] == 'deletedcomments') 
{
	echo "<div class=\"alert alert-success\">Comment(s) successfully removed.</div>";
}

if ($_GET['action'] == 'badtoken') 
{
	echo '<div class="alert alert-error">Invalid token or session expired. Please refresh this page and try again.</div>';
}

// Fix for IE
if ($_POST['Submit_restrict'] != '' && $_POST['Submit_restrict'] != '')
{
	$_POST['Submit'] = 'Restrict access';
}
if ($_POST['Submit_derestrict'] != '' && $_POST['Submit_derestrict'] != '')
{
	$_POST['Submit'] = 'Derestrict access';
}

//	Batch Delete
if (($_POST['Submit'] == 'Delete checked') && ! csrfguard_check_referer('_admin_videos_listcontrols'))
{
	echo '<div class="alert alert-error">Invalid token or session expired. Please refresh this page and try again.</div>';
}
else if ($_POST['Submit'] == 'Delete checked')
{
	$video_ids = array();
	$video_ids = $_POST['video_ids'];
	
	$total_ids = count($video_ids);
	
	if($total_ids > 0)
	{
		$in_arr = '';
		for($i = 0; $i < $total_ids; $i++)
		{
			$in_arr .= "'" . $video_ids[ $i ] . "', ";
		}
		$in_arr = substr($in_arr, 0, -2);
		if(strlen($in_arr) > 0)
		{
			$video_list_data = array();
			$sql = "SELECT id, uniq_id, category, url_flv, added, submitted, source_id FROM pm_videos WHERE uniq_id IN (" . $in_arr . ")";
			$result = mysql_query($sql);
			while ($row = mysql_fetch_assoc($result))
			{
				$video_list_data[$row['uniq_id']] = $row;
			}
			mysql_free_result($result);

			$sql = "DELETE FROM pm_videos WHERE uniq_id IN (" . $in_arr . ")";
			$result = mysql_query($sql);
			
			if(!$result)
			{
				echo '<div class="info_msg_errimg">There was an error while updating your database.<br />MySQL returned: '.mysql_error().'</div>';
			}
			else
			{
				mysql_query("DELETE FROM pm_comments WHERE uniq_id IN (" . $in_arr . ")");
				mysql_query("DELETE FROM pm_reports WHERE entry_id IN (" . $in_arr . ")");
				mysql_query("DELETE FROM pm_videos_urls WHERE uniq_id IN (" . $in_arr . ")");
				mysql_query("DELETE FROM pm_favorites WHERE uniq_id IN (" . $in_arr . ")");
				mysql_query("DELETE FROM pm_chart WHERE uniq_id IN (" . $in_arr . ")");
				mysql_query("DELETE FROM pm_tags WHERE uniq_id IN (" . $in_arr . ")");
				mysql_query("DELETE FROM pm_bin_rating_meta WHERE uniq_id IN (" . $in_arr . ")");
				mysql_query("DELETE FROM pm_bin_rating_votes WHERE uniq_id IN (" . $in_arr . ")");
				
				$ids = array();
				foreach ($video_list_data as $uniq_id => $row)
				{
					$ids[] = $row['id'];
				}
				mysql_query("DELETE FROM pm_meta WHERE item_id IN (". implode(',', $ids) .") AND item_type = ". IS_VIDEO);
				unset($ids);

				echo '<div class="alert alert-success">Video(s) removed successfully.</div>';
			}
			
			// update video count for each category
			$total_published_ids = 0;
			$video_count = array();
			$video_published_count = array();
			$time_now = time();
			
			foreach ($video_list_data as $uniq_id => $row)
			{
				// delete hosted files
				if ($row['source_id'] == 1)
				{
					if (file_exists(_VIDEOS_DIR_PATH . $row['url_flv']) && strlen($row['url_flv']) > 0)
					{
						unlink(_VIDEOS_DIR_PATH . $row['url_flv']);
					}
				}
				
				if (file_exists(_THUMBS_DIR_PATH . $row['uniq_id'] .'-1.jpg'))
				{
					unlink(_THUMBS_DIR_PATH . $row['uniq_id'] .'-1.jpg');
				}
				
				$buffer = explode(',', $row['category']);
				foreach ($buffer as $k => $id)
				{
					$video_count[$id]++;
					if ($row['added'] <= $time_now)
					{
						$video_published_count[$id]++;
					}
				}
				
				if ($row['added'] <= $time_now)
				{
					$total_published_ids++;
				}
			}
								
			if (count($video_count) > 0)
			foreach ($video_count as $cid => $count)
			{
				if ('' != $cid && 0 != $cid)
				{
					$sql = "UPDATE pm_categories SET total_videos=total_videos-". $count;
					if ($video_published_count[$cid] > 0)
					{
						$sql .= ", published_videos = published_videos - ". $video_published_count[$cid];
					}
					$sql .= " WHERE id = '". $cid ."'";
					mysql_query($sql);
				}
			}
		}
		update_config('total_videos', $config['total_videos'] - $total_ids);

		if ($total_published_ids)
		{
			update_config('published_videos', $config['published_videos'] - $total_published_ids);
		}
		
		if (_MOD_SOCIAL)
		{
			foreach ($video_list_data as $uniq_id => $video)
			{
				remove_all_related_activity($video['id'], ACT_OBJ_VIDEO);
			}
		}
		
		$videos = a_list_videos('', '', $from, $limit, $page, $filter); 
	}
	else
	{
		echo '<div class="alert alert-info">Please select something first.</div>';
	}
}
//	Mark video(s) as featured/regular video
if ($_POST['Submit'] == 'Mark as featured' || $_POST['Submit'] == 'Mark as regular')
{
	$video_ids = array();
	$video_ids = $_POST['video_ids'];
	$total_ids = count($video_ids);
	if($total_ids > 0)
	{
		$in_arr = '';
		for($i = 0; $i < $total_ids; $i++)
		{
			$in_arr .= "'" . $video_ids[ $i ] . "', ";
		}
		$in_arr = substr($in_arr, 0, -2);
		if(strlen($in_arr) > 0)
		{
			$sql = "UPDATE pm_videos ";
			if ($_POST['Submit'] == 'Mark as featured')
			{
				$sql .= "SET featured = '1' ";
			}
			else
			{
				$sql .= "SET featured = '0' ";
			}
			$sql .=	" WHERE uniq_id IN (" . $in_arr . ")";
			$result = mysql_query($sql);
			
			if(!$result)
			{
				echo '<div class="alert alert-error">There was an error while updating your database.<br />MySQL returned: '.mysql_error().'</div>';
			}
			else
			{
				echo '<div class="alert alert-success">The selected videos have been updated.</div>';
			}
		}
		$videos = a_list_videos('', '', $from, $limit, $page, $filter); 
	}
	else
	{	
		echo '<div class="alert alert-info">Please select something first.</div>';
	}
}

if (($_POST['Submit'] == 'Move') && ! csrfguard_check_referer('_admin_videos_listcontrols'))
{
	echo '<div class="alert alert-error">Invalid token or session expired. Please refresh this page and try again.</div>';
}
else if ($_POST['Submit'] == 'Move')
{
	$video_ids = array();
	$video_ids = $_POST['video_ids'];
	$new_cid   = (int) $_POST['move_to_category'];
	
	$total_ids = count($video_ids);
	
	if ($new_cid == '' || !array_key_exists($new_cid, $categories))
	{
		echo '<div class="alert alert-info">Please select a category first.</div>';
	}
	else
	{
		if($total_ids == 0)
		{
			echo '<div class="alert alert-info">Please select something first.</div>';	
		}
		else
		{
			$in_arr = '';
			for($i = 0; $i < $total_ids; $i++)
			{
				$in_arr .= "'" . $video_ids[ $i ] . "', ";
			}
			$in_arr = substr($in_arr, 0, -2);
			
			$sql = "SELECT category, added  
					FROM pm_videos 
					WHERE uniq_id IN (". $in_arr .")";
			$result = mysql_query($sql);
			if ( !$result)
			{
				echo '<div class="alert alert-error">Mysql Error: '.mysql_error().'</div>';
			}
			else
			{				
				$add = $total_ids;
				$add_published = 0;
				$deduct_total = array();
				$deduct_published = array();
				$time_now = time();
				
				while ($row = mysql_fetch_assoc($result))
				{
					if (strpos($row['category'], ','))
					{
						$buff = explode(',', $row['category']);
						foreach ($buff as $k => $v)
						{
							$deduct_total[ (int) $v ]++;
							if ($row['added'] <= $time_now)
							{
								$deduct_published[ (int) $v ]++;
							}
						}
					}
					else
					{
						$deduct_total[ (int) $row['category'] ]++;
						
						if ($row['added'] <= $time_now)
						{
							$deduct_published[ (int) $row['category'] ]++;
						}
					}
					
					if ($row['added'] <= $time_now)
					{
						$add_published++;
					}
				}

				mysql_free_result($result);
				
				// update pm_videos
				$sql = "UPDATE pm_videos 
						SET category = '". $new_cid ."' 
						WHERE uniq_id IN (". $in_arr .")";
				$result = mysql_query($sql);
				if ( !$result)
				{
					echo '<div class="alert alert-error">Something went wrong. <br />Mysql Error: '.mysql_error().'</div>';
				}
				
				// update pm_categories (deduct video count)
				foreach ($deduct_total as $cid => $count)
				{
					$sql = "UPDATE pm_categories 
							SET total_videos = total_videos - ". $count ;
							
					if (count($deduct_published[$cid]) > 0)
					{
						$sql .= ", published_videos = published_videos - ". $count;
					}
					
					$sql .= " WHERE id = '". $cid ."'";
					
					mysql_query($sql);
				}
				
				// update pm_categories (add video count)
				$sql = "UPDATE pm_categories 
						SET total_videos=total_videos+". $add .",
							published_videos = published_videos + ". $add_published ." 
						WHERE id = '". $new_cid ."'";
				
				$result = mysql_query($sql);
				
				echo '<div class="alert alert-success">Video(s) successfully moved to <strong>'. $categories[$new_cid]['name'].'</strong>.</div>';
				
				// update table
				$videos = a_list_videos('', '', $from, $limit, $page, $filter); 
			}
		}
	}
}
if ($_POST['Submit'] == 'Restrict access' || $_POST['Submit'] == 'Derestrict access')
{
	$video_ids = array();
	$video_ids = $_POST['video_ids'];
	$total_ids = count($video_ids);
	
	if ($total_ids > 0)
	{
		$access = ($_POST['Submit'] == 'Restrict access') ? '1' : '0';
		
		$in_arr = '';
		for($i = 0; $i < $total_ids; $i++)
		{
			$in_arr .= "'" . $video_ids[ $i ] . "', ";
		}
		$in_arr = substr($in_arr, 0, -2);
		
		$sql = "UPDATE pm_videos 
				SET restricted = '". $access ."'
				WHERE uniq_id IN (". $in_arr .")";
		$result = mysql_query($sql);
			
		if ( ! $result)
		{
			echo '<div class="alert alert-error">There was an error while updating your database.<br />MySQL returned: '.mysql_error().'</div>';
		}
		else
		{
			echo '<div class="alert alert-success">Video(s) updated successfully</div>';
			$videos = a_list_videos('', '', $from, $limit, $page, $filter, $filter_value); 
		}
	}
	else
	{
		echo '<div class="alert alert-info">Please select something first.</div>';	
	}
}

//	Delete all videos
if($action == 9 && is_admin())
{
	//	clear database of all videos
	if (isset($_POST['Submit']) && ! csrfguard_check_referer('_admin_videos_deleteall'))
	{
		echo '<div class="alert alert-error"> Invalid token or session expired. Please refresh this page and try again. </div>';
		echo '</div><!-- .content -->';
		echo '</div><!-- .primary -->';
		echo '</div>';
	}
	else if (isset($_POST['Submit']))
	{
		if($_POST['Submit'] == 'Yes')
		{
			$files = array();
			$sql = "SELECT url_flv FROM pm_videos WHERE source_id = '1'";
			$result = mysql_query($sql);
			
			if (mysql_num_rows($result) > 0)
			{
				while ($row = mysql_fetch_assoc($result))
				{
					$files[] = $row['url_flv'];
				}
				mysql_free_result($result);
				
				foreach ($files as $k => $filename)
				{
					if (file_exists(_VIDEOS_DIR_PATH . $filename) && strlen($filename) > 0)
					{
						unlink(_VIDEOS_DIR_PATH . $filename);
					}
				}
			}
			
			$sql = "TRUNCATE TABLE pm_videos";
			$result = @mysql_query($sql);
			if(!$result)
			{
				echo '<div class="alert alert-error">Mysql Error: '.mysql_error().'</div>';
			}
			else
			{
			
				update_config('total_videos', 0);
				update_config('published_videos', 0);
				
				$sql = " UPDATE pm_categories SET total_videos = 0, published_videos = 0 ";
				@mysql_query($sql);
				
				//	pm_videos extension table
				$sql = "TRUNCATE TABLE pm_videos_urls";
				$result = @mysql_query($sql);
				if(!$result)
					echo '<div class="alert alert-error">MySQL Error: '.mysql_error().'</div>';
				
				//	comments table
				$sql = "TRUNCATE TABLE pm_comments";
				$result = @mysql_query($sql);
				if(!$result)
					echo '<div class="alert alert-error">MySQL Error: '.mysql_error().'</div>';
				
				//	favorites table
				$sql = "TRUNCATE TABLE pm_favorites";
				$result = @mysql_query($sql);
				if(!$result)
					echo '<div class="alert alert-error">MySQL Error: '.mysql_error().'</div>';
				
				//	tags table
				$sql = "TRUNCATE TABLE pm_tags";
				$result = @mysql_query($sql);
				if(!$result)
					echo '<div class="alert alert-error">MySQL Error: '.mysql_error().'</div>';
				
				//	reports table
				$sql = "TRUNCATE TABLE pm_reports";
				$result = @mysql_query($sql);
				if(!$result)
					echo '<div class="alert alert-error">MySQL Error: '.mysql_error().'</div>';
				
				//	chart table
				//	tags table
				$sql = "TRUNCATE TABLE pm_chart";
				$result = @mysql_query($sql);
				if(!$result)
					echo '<div class="alert alert-error">MySQL Error: '.mysql_error().'</div>';
			}
			echo '<div class="addfirstvideo"><img src="img/img-addfirstvideo.png" width="238" height="49" /></div>';
			echo '<div class="alert alert-success">Nothing compares with a fresh start, ey? Enjoy!</div>';
			echo '</div><!-- .content -->';
			echo '</div><!-- .primary -->';
			echo '</div>';
		}
		else
		{
			echo '<meta http-equiv="refresh" content="0;URL=videos.php" />';
			exit();
		}
	}
	else
	{
	?>
		<div class="alert alert-error">
			Are you sure you want to delete all your videos? This operation is not reversible. Clicking 'YES' will empty your video database.
		</div>

		<form name="delete" method="post" action="videos.php?action=9">
			<?php echo csrfguard_form('_admin_videos_deleteall');?>
			<input type="submit" name="Submit" value="Yes" class="btn btn-small btn-danger" style="position:relative"/> <input type="submit" name="Submit" value="Cancel" class="btn btn-small" style="position:relative" />
		</form>
        </div><!-- .content -->
    </div><!-- .primary -->
	<?php
	include('footer.php');
	exit();
	}
}
else if ($action == 9)
{
	restricted_access();
}
else
{
?>
<?php if ($config['total_videos'] == 0) : ?>
<div class="addfirstvideo"><img src="img/img-addfirstvideo.png" width="238" height="49" /></div>
<?php endif; ?>
<div class="row-fluid">
<div class="span8">
    <div class="pull-left">
	<?php if ( ! empty($_POST['keywords'])) : ?>
    <h4>SEARCH RESULTS FOR "<em><?php echo $_POST['keywords']; ?></em>" <a href="#" onClick="parent.location='videos.php'" class="opac5"><i class="icon-remove-sign"></i></a></h4>
	<?php endif; ?>
    </div>
</div><!-- .span8 -->
<div class="span4">
    <div class="pull-right">
    <form name="videos_per_page" action="videos.php" method="get" class="form-inline pull-right">
    <!--<input type="text" name="results" value="<?php echo $limit; ?>" size="2" onChange="this.form.submit()" />-->
    <label><small>Videos/page</small></label>
    <select name="results" class="smaller-select" onChange="this.form.submit()" >
    <option value="10" <?php if($limit == 10) echo 'selected="selected"'; ?>>10</option>
    <option value="20" <?php if($limit == 20) echo 'selected="selected"'; ?>>20</option>
    <option value="50" <?php if($limit == 50) echo 'selected="selected"'; ?>>50</option>
    <option value="70" <?php if($limit == 70) echo 'selected="selected"'; ?>>70</option>
    <option value="100" <?php if($limit == 100) echo 'selected="selected"'; ?>>100</option>
    </select>
    <?php
    // filter persistency
    if (strlen($_SERVER['QUERY_STRING']) > 0)
    {
        $pieces = explode('&', $_SERVER['QUERY_STRING']);
        foreach ($pieces as $k => $val)
        {
            $p = explode('=', $val);
            if ($p[0] != 'page' && $p[0] != 'results') :	
            ?>
            <input type="hidden" name="<?php echo $p[0];?>" value="<?php echo $p[1];?>" />
            <?php 
            endif;
        }
    }
    ?>
    </form>    
    </div>
</div><!-- .row-fluid-->
</div>
<div class="tablename">
    <div class="row-fluid">
        <div class="span8">
        	<div class="qsFilter pull-left">
        <div class="btn-group input-prepend">
          <div class="form-filter-inline">
			<?php if ( ! empty($_GET['filter'])) : ?>
			<button type="button" id="appendedInputButtons" class="btn btn-danger btn-strong" onClick="parent.location='videos.php'">Remove filter</button>
			<?php else : ?>
			<button type="button" id="appendedInputButtons" class="btn">Filter</button>
			<?php endif; ?>
        
              <form name="other_filter" action="videos.php" class="form-inline">
              <select name="URL" onChange="window.parent.location=this.form.URL.options[this.form.URL.selectedIndex].value">
                <option value="videos.php">by videos ...</option>
                <option value="videos.php?page=1&filter=mostviewed" <?php if ($filter == 'mostviewed') echo 'selected="selected"'; ?>>Most viewed</option>
                <option value="videos.php?filter=added&page=1" <?php if ($filter == 'added') echo 'selected="selected"'; ?> >Added date</option>
                <option value="videos.php?filter=restricted&page=1" <?php if ($filter == 'restricted') echo 'selected="selected"'; ?> >Geo-Restricted Videos</option>
                <option value="videos.php?filter=broken&page=1" <?php if ($filter == 'broken') echo 'selected="selected"'; ?>>Dead Videos</option>
                <option value="videos.php?filter=featured&page=1" <?php if ($filter == 'featured') echo 'selected="selected"'; ?>>Featured Videos</option>
                <option value="videos.php?filter=localhost&page=1" <?php if ($filter == 'localhost') echo 'selected="selected"'; ?>>Hosted locally</option>
                <option value="videos.php?page=1&filter=access" <?php if ($filter == 'access') echo 'selected="selected"'; ?>>Restricted access</option>
              </select>
              </form>
              <form name="source_filter" action="videos.php" method="get" class="form-inline">
              <input type="hidden" name="filter" value="source" />
              <select name="fv" onchange=submit()>
              <option value="">by video source...</option>
              <?php
              $sources = a_fetch_video_sources('source_name');
              foreach ($sources as $id => $src)
              {
                  $option = '';
                  if (is_int($id) && $id > 1 && $id != 44 && $id != 43)
                  {
                      $option = '<option value="'. $src['source_id'] .'" ';
                      if ($filter_value == $id && $filter == 'source')
                      {
                          $option .= ' selected="selected" ';
                      }
                      $option .= '>'. ucfirst($src['source_name']) .'</option>';
                      echo $option;
                  }
              }
              ?>
              </select>
              </form>
              <form name="category_filter" action="videos.php" method="get" class="form-inline">
              <input type="hidden" name="filter" value="category" />
              <?php
              $categories_dropdown_options = array(
                                                'attr_name' => 'fv',
                                                'attr_id' => 'select_move_to_category',
                                                'attr_class' => 'inline last-filter',
                                                'first_option_text' => 'by category..',
                                                'first_option_value' => '',
                                                'selected' => ($filter == 'category') ? $filter_value : '',
                                                'other_attr' => ' onchange=submit() '
                                                );
              $dd_html = categories_dropdown($categories_dropdown_options);
              if ($filter_value == 0 && $filter == 'category')
              {
                $dd_html = str_replace('</select>', '<option value="0" selected="selected">Uncategorized</option></select>', $dd_html);
              }
              else
              {
                $dd_html = str_replace('</select>', '<option value="0">Uncategorized</option></select>', $dd_html);
              }
              echo $dd_html;
              unset($dd_html);
              ?>
              </form>
          </div><!-- .form-filter-inline -->
        </div><!-- .btn-group -->
        </div><!-- .qsFilter -->
        </div>
        <div class="span4">
        <div class="pull-right">
            <form name="search" action="videos.php" method="post" class="form-search-listing form-inline">
            <div class="input-append">
            <input name="keywords" type="text" value="<?php echo $_POST['keywords']; ?>" size="30" class="search-query search-quez input-medium" placeholder="Enter keyword" id="form-search-input" />
            <select name="search_type" class="input-small">
                <option value="video_title" <?php echo ($_POST['search_type'] == 'video_title') ? 'selected="selected"' : '';?>>Title</option>
                <option value="uniq_id" <?php echo ($_POST['search_type'] == 'uniq_id') ? 'selected="selected"' : '';?>>Unique ID</option>
                <option value="submitted" <?php echo ($_POST['search_type'] == 'submitted') ? 'selected="selected"' : '';?>>Username</option>
            </select>
            <button type="submit" name="submit" class="btn" value="Search" id="submitFind"><i class="icon-search findIcon"></i><span class="findLoader"><img src="img/ico-loading.gif" width="16" height="16" /></span></button>
            </div>
            </form>
        </div>
        </div>
    </div>
</div>
<div class="clearfix"></div>
<form name="videos_checkboxes" id="videos_checkboxes" action="videos.php?page=<?php echo $page;?>&filter=<?php echo $filter;?>&fv=<?php echo $filter_value;?>" method="post">
 <table cellpadding="0" cellspacing="0" width="100%" class="table table-striped table-bordered pm-tables tablesorter">
  <thead>
   <tr>
	<th align="center" style="text-align:center" width="3%"><input type="checkbox" name="checkall" id="selectall" onclick="checkUncheckAll(this);"/></th>
	<th width="2%">&nbsp;</th>
	<th width="5%">Unique ID</th>
	<th width="3%">Status</th>	
	<th width="3%">&nbsp;</th>
    <th width="300">Video title</th>
    <th width="90"><a href="videos.php?filter=added&fv=<?php echo ($filter_value == 'desc' && $filter == 'added') ? 'asc' : 'desc';?>" rel="tooltip" title="Sort <?php echo ($filter_value == 'desc' && $filter == 'added') ? 'ascending' : 'descending';?>">Added</a></th>
	<th width="65"><a href="videos.php?filter=views&fv=<?php echo ($filter_value == 'desc' && $filter == 'views') ? 'asc' : 'desc';?>" rel="tooltip" title="Sort <?php echo ($filter_value == 'desc' && $filter == 'views') ? 'ascending' : 'descending';?>">Views</a></th>
	<th width="190">Category</th>
	<th style="width: 110px;">Comments</th>
    <th style="width: 90px;">Action</th>
   </tr>
  </thead>
  <tbody>
	<?php if ($pagination != '') : ?>
	<tr>
		<td colspan="11" class="tableFooter">
			<div class="pagination pull-right"><?php echo $pagination; ?></div>
		</td>
	</tr>
	<?php endif; ?>
	
	<?php echo $videos; ?>
	
	<?php if ($pagination != '') : ?>
	<tr>
		<td colspan="11" class="tableFooter">
			<div class="pagination pull-right"><?php echo $pagination; ?></div>
		</td>
	</tr>
	<?php endif; ?>
  </tbody>
 </table>

<div class="clearfix"></div>

<div id="stack-controls" class="list-controls">
<div class="pull-left form-inline" style="padding-top: 8px;">

<small>Move selected videos to</small>
<div class="input-append">
<?php 
$categories_dropdown_options = array(
                            'attr_name' => 'move_to_category',
                            'attr_id' => 'select_move_to_category',
							'attr_id' => '',
							'attr_class' => 'inline smaller-select',
                            'first_option_text' => 'category...',
                            'selected' => ($_POST['move_to_category']) ? $_POST['move_to_category'] : 0
                            );
echo categories_dropdown($categories_dropdown_options);
?>
<button type="submit" name="Submit" value="Move" data-loading-text="Moving..." class="btn btn-small" onClick="if ($('select[name=move_to_category] option:selected').val() == '' || $('select[name=move_to_category] option:selected').val() == '-1') {alert('Please select a category first.'); return false;}" rel="tooltip" title="Videos currently assigned to multiple categories, will be moved to the chosen category <em>only</em>." />Move</button>
</div>
</div>
<div class="btn-toolbar">
    <div class="btn-group dropup">
        <button class="btn btn-small btn-normal btn-strong dropdown-toggle" data-toggle="dropdown" href="#">Mark as
        <span class="caret"></span>
        </button>
        <ul class="dropdown-menu">
          <li><button type="submit" name="Submit" value="Mark as featured" class="btn btn-link-strong">Featured</button></li>
          <li><button type="submit" name="Submit" value="Mark as regular" class="btn btn-link-strong">Regular (non-Featured)</button></li>
          <li class="divider"></li>
          <li><button type="submit" name="Submit_restrict" value="Restrict access" class="btn btn-link-strong" rel="tooltip" data-placement="left" title="Private videos will be available only to registered users.">Private</button></li>
          <li><button type="submit" name="Submit_derestrict" value="Derestrict access" class="btn btn-link-strong" rel="tooltip" data-placement="left" title="Make selected videos <u>public</u>. Remove any viewing restrictions.">Public</button></li>
        </ul>
    </div>
    <div class="btn-group">
    	<button type="submit" name="VideoChecker" id="VideoChecker" value="Check status" class="btn btn-small btn-success btn-strong" onclick="javascript: return false;">Check status</button>
    </div>
    <div class="btn-group dropup">
        <button type="submit" name="Submit" value="Delete checked" class="btn btn-small btn-danger btn-strong" onClick="return confirm_delete_all();">Delete checked</button>
        <button class="btn  btn-small btn-danger dropdown-toggle" data-toggle="dropdown">
        <span class="caret"></span>
        </button>
        <ul class="dropdown-menu pull-right">
            <li><a href="videos.php?action=9" rel="tooltip" title="This will remove the entire video database.">Delete all videos</a></li>
        </ul>
    </div>
	<input type="hidden" name="filter" id="listing-filter" value="<?php echo $filter;?>" />
	<input type="hidden" name="fv" id="listing-filter_value"value="<?php echo $filter_value;?>" /> 
</div>
</div><!-- #list-controls -->

<?php
echo csrfguard_form('_admin_videos_listcontrols');
?>
</form>
	<hr />
	<div id="video_check_message" class="alert alert-info" style="display: none;"></div>
    </div><!-- .content -->
</div><!-- .primary -->
<?php
}	//	else - if($action == 9)
include('footer.php');
?>