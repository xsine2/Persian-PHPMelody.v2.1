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
$_page_title = 'Generate sitemap';
include('header.php');

function clean_feed($input) 
{
	$original = array("<", ">", "&", '"', "'");
	$replaced = array("&lt;", "&gt;", "&amp;", "&quot;", "&apos;");
	$newinput = str_replace($original, $replaced, $input);
	return $newinput;
}

function generateMap () 
{	
	global $config;
	
	$query = "SELECT * FROM pm_videos ORDER BY added DESC";
	$categories = "SELECT * FROM pm_categories ORDER BY position ASC";

	$results	= mysql_query($query);
	$categories	= mysql_query($categories);

	if (mysql_num_rows($results) > 0)
	{
		$smap = "";
		$smap .= '<?xml version="1.0" encoding="UTF-8"'.'?'.'>'."\n";
		$smap .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\r\n";
		$smap .= "<url>\r\n";
		$smap .= "<loc>"._URL."/index."._FEXT."</loc>\r\n";
		$smap .= "<lastmod>".date('Y-m-d')."</lastmod>\r\n";
		$smap .= "</url>\r\n";
		$smap .= "<url>\r\n";
		$smap .= "<loc>"._URL."/newvideos."._FEXT."</loc>\r\n";
		$smap .= "<lastmod>".date('Y-m-d')."</lastmod>\r\n";
		$smap .= "</url>\r\n";
		$smap .= "<url>\r\n";
		$smap .= "<loc>"._URL."/topvideos."._FEXT."</loc>\r\n";
		$smap .= "<lastmod>".date('Y-m-d')."</lastmod>\r\n";
		$smap .= "</url>\r\n";
		$smap .= "<url>\r\n";
		$smap .= "<loc>"._URL."/register."._FEXT."</loc>\r\n";
		$smap .= "<lastmod>".date('Y-m-d')."</lastmod>\r\n";
		$smap .= "</url>\r\n";
		while ($row = mysql_fetch_array($categories))
		{
			if(_SEOMOD == 1)
				$cat_url = _URL.'/browse-'.$row['tag'].'-videos-1-date.html';
			else
				$cat_url = _URL.'/category.php?cat='.$row['tag'].'';	
		
			$smap .= "<url>\r\n";
			$smap .= "<loc>".clean_feed($cat_url)."</loc>\r\n";
			$smap .= "<lastmod>".date('Y-m-d')."</lastmod>\r\n";
			$smap .= "<changefreq>weekly</changefreq>\r\n";
			$smap .= "</url>\r\n";
		}
		while ($row = mysql_fetch_array($results))
		{
			$date = date('Y-m-d', $row['added']);
			$smap .= "<url>\r\n";
			$smap .= "<loc>".clean_feed( makevideolink($row['uniq_id'], $row['video_title'], $row['video_slug']) )."</loc>\r\n";
			$smap .= "<lastmod>".date('Y-m-d')."</lastmod>\r\n";
			$smap .= "<changefreq>weekly</changefreq>\r\n";
			$smap .= "</url>\r\n";
		}
		mysql_free_result($categories);
		mysql_free_result($results);
		
		if ($config['mod_article'] == 1)
		{
			//	categories
			$sql = "SELECT * FROM art_categories ORDER BY name ASC";
			$result = mysql_query($sql);
			
			if (mysql_num_rows($result) > 0)
			{
				while ($row = mysql_fetch_array($result))
				{
					$date = date('Y-m-d', $row['date']);
					$loc = art_make_link('category', array('id' => $row['id'], 'tag' => $row['tag']));
					
					$smap .= "<url>\r\n";
					$smap .= "<loc>".clean_feed( $loc )."</loc>\r\n";
					$smap .= "<lastmod>".date('Y-m-d')."</lastmod>\r\n";
					$smap .= "<changefreq>weekly</changefreq>\r\n";
					$smap .= "</url>\r\n";
				}
				mysql_free_result($result);
			}
			
			//	articles
			$sql = "SELECT * FROM art_articles WHERE status = '1' ORDER BY date DESC";
			$result = mysql_query($sql);
			
			if (mysql_num_rows($result) > 0)
			{
				while ($row = mysql_fetch_array($result))
				{
					$date = date('Y-m-d', $row['date']);
					$loc = art_make_link('article', $row);
					
					$smap .= "<url>\r\n";
					$smap .= "<loc>".clean_feed( $loc )."</loc>\r\n";
					$smap .= "<lastmod>".date('Y-m-d')."</lastmod>\r\n";
					$smap .= "<changefreq>weekly</changefreq>\r\n";
					$smap .= "</url>\r\n";
				}
				mysql_free_result($result);
			}
		}
		
		$smap .= "</urlset>";
		return $smap;
	}
	else
	{
		log_error('MySQL Error: '. mysql_error(), 'Generate Sitemap', 1) ;
		return "An error occurred.";
	}
}


$filename = ABSPATH . '/sitemap.xml';

// Let's make sure the file exists and is writable first.
if (is_writable($filename)) 
{
    if ( ! $handle = fopen($filename, 'w')) 
	{
         $info_msg = '<div class="alert alert-error">Cannot open file ('. $filename .')</div>';
    }
	else
	{
		$content = generateMap();

	    if(fwrite($handle, $content) === FALSE) 
		{
			$info_msg = '<div class="alert alert-error">Cannot write to file ('. $filename .')</div>';
		}

	}
	
	fclose($handle);
	
	if (strlen($info_msg) == 0)
	{
    	$info_msg = "<div class=\"alert alert-success\"><strong>Success!</strong><br /><br /> The sitemap was generated at: <em><strong>"._URL."/sitemap.xml</strong></em><br /><br /> We recommend updating this sitemap regularly.</div>";
	}
}
else 
{
    $info_msg = "<div class=\"alert alert-error\">The file $filename is not writable. Please CHMOD <strong>sitemap.xml</strong> to 0777.</div>";
}
?>
<div id="adminPrimary">
    <div class="row-fluid" id="help-assist">
        <div class="span12">
        <div class="tabbable tabs-left">
          <ul class="nav nav-tabs">
            <li class="active"><a href="#help-overview" data-toggle="tab">Overview</a></li>
          </ul>
          <div class="tab-content">
            <div class="tab-pane fade in active" id="help-overview">
    		<p>Creating a sitemap helps search engines better crawl and categorize your content. <br />PHP MELODY will generate a search engine friendly sitemap of your entire site automatically. The sitemap can be used in conjunction with <a href="https://www.google.com/webmasters/tools/home?hl=en" target="_blank">Google Webmaster Tools</a>. You can learn more about submitting your sitemap to Google <a href="http://support.google.com/sites/bin/answer.py?hl=en&answer=100283" title="_blank">here</a>. </p>
	    </div>
          </div>
        </div> <!-- /tabbable -->
        </div><!-- .span12 -->
    </div><!-- /help-assist -->
    <div class="content">
	<a href="#" id="show-help-assist">Help</a>
	<h2>Generate Sitemap</h2>

	<?php echo $info_msg; ?>
    
    </div><!-- .content -->
</div><!-- .primary -->
<?php
include('footer.php');
?>