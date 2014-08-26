<?php
session_start();
require('../config.php');
include_once(ABSPATH . 'include/user_functions.php');
include_once(ABSPATH . 'include/islogged.php');
include(ABSPATH .'admin/functions.php');

if ( ! $logged_in || ( ! is_admin() && ! is_moderator() && ! is_editor()))
{
	header("Location: "._URL. "/admin/login.php");
	exit();
}

$mod_can = array();
$restricted_access = array();

if (is_moderator())
{
	$mod_can = mod_can();
	$restricted_access = array( 'mod_pages',	// Pages
								'3',			// Categories 
								'7',			// Statistics
								'8',			// Settings
								'9',			// Ads
								'10');			// System

	if ( ! $mod_can['manage_videos']) 
		$restricted_access[] = '2';
	
	if ( ! $mod_can['manage_comments']) 
		$restricted_access[] = '4';
	
	if ( ! $mod_can['manage_users'])
		$restricted_access[] = '6';
	
	if ( ! $mod_can['manage_articles'])  
		$restricted_access[] = 'mod_article';
}

if (is_editor())
{
	$restricted_access = array( 'mod_pages',	// Pages
								'2', 			// Videos
								'3',			// Categories 
								'4',			// Comments
								'6', 			// Users
								'7',			// Statistics
								'8',			// Settings
								'9',			// Ads
								'10');			// System
	if ( ! _MOD_ARTICLE)
	{
		$restricted_access[] = 'mod_article';
	}
}

define('VS_UNCHECKED', 0);
define('VS_OK', 1);
define('VS_BROKEN', 2);
define('VS_RESTRICTED', 3);
define('VS_UNCHECKED_IMG', "vs_unchecked");
define('VS_OK_IMG', "vs_ok");
define('VS_BROKEN_IMG', "vs_broken");
define('VS_RESTRICTED_IMG', "vs_restricted");
define('VS_NOTAVAILABLE_IMG', "vs_na");

$upload_max_filesize = get_true_max_filesize();

if ($showm == '2')
{
	if (empty($_COOKIE['aa_videos_per_page']))
	{
		setcookie('aa_videos_per_page', 30, time()+(COOKIE_TIME * 100), COOKIE_PATH);
	}
	
	if ( ! empty($_GET['results']) && $_GET['results'] != $_COOKIE['aa_videos_per_page'])
	{
		$results = (int) $_GET['results'];
		$results = ($results <= 0) ? 30 : $results;
		setcookie('aa_videos_per_page', $results, time()+(COOKIE_TIME * 100), COOKIE_PATH);
		$_COOKIE['aa_videos_per_page'] = $results;
	}
}

?>
<?php
// Count important data
$vapprv = count_entries('pm_temp', '', '');
$crps = count_entries('pm_reports', 'r_type', '1');
$tab_video_total = $vapprv + $crps;
$capprv = count_entries('pm_comments', 'approved', '0');
$flagged_comments = count_entries('pm_comments', '1', '1\' AND report_count > \'0');
$pending_comments = count_entries('pm_comments', '1', '1\' AND approved = \'0');
$tab_comments = $capprv + $flagged_comments;
$tab_internallog = (int) $config['unread_system_messages'];

?>
<!DOCTYPE html>
<!--[if IE 7 | IE 8 | IE 9]>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<![endif]-->
<!--[if !(IE 7) | !(IE 8)  ]><!-->
<html dir="ltr" lang="en">
<!--<![endif]-->
<head>
<meta charset="UTF-8" />
<!--<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">-->

<meta name="viewport" content="width=1024,maximum-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="IE=9; IE=8; IE=7; IE=edge,chrome=1">
<title><?php echo ($_page_title != '') ? $_page_title : $config['homepage_title'] .' - Admin Area'; ?></title>

<link rel="shortcut icon" type="image/ico" href="img/favicon.ico" />
<!--[if lt IE 9]>
<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->
<link rel="stylesheet" type="text/css" media="screen" href="css/bootstrap.min.css" />
<link rel="stylesheet" type="text/css" media="screen" href="css/bootstrap-responsive.min.css" />

<link rel="stylesheet" type="text/css" media="screen" href="css/admin-wrap.css" />
<?php if($load_ibutton == 1): ?>
<link rel="stylesheet" type="text/css" media="screen" href="css/jquery.ibutton.css" />
<?php endif; ?>
<link rel="stylesheet" type="text/css" media="screen" href="css/admin.css" />
<!--[if IE]><link rel="stylesheet" type="text/css" href="css/admin-ie.css"/><![endif]-->
<?php if($load_chzn_drop == 1): ?>
<link rel="stylesheet" type="text/css" media="screen" href="css/chosen.css" />
<?php endif; ?>
<?php if($load_colorpicker == 1): ?>
<link rel="stylesheet" type="text/css" media="screen" href="css/bootstrap-colorpicker.css" />
<?php endif; ?>
<link rel="stylesheet" href="css/gritter.css" type="text/css" media="screen" charset="utf-8" />
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js" type="text/javascript"></script>
<?php if($showm == 2): ?>
<link rel="stylesheet" type="text/css" media="screen" href="css/video-src.css" />
<?php endif; ?>

<?php $modframework->trigger_hook('admin_header'); ?>

<?php if ($load_datepicker) : ?>
<link rel="stylesheet" type="text/css" media="screen" href="css/bootstrap-datepicker.min.css" />
<?php endif; ?>

<?php if ($load_flot) : ?>
<script type="text/javascript" src="js/jquery.flot.min.js"></script>
<script type="text/javascript" src="js/jquery.flot.resize.min.js"></script>
<?php endif; ?>
</head>
<body>
<div id="loading">در حال بارگذاری ...</div>
<!-- Masthead
================================================== -->
<header class="wide-header" id="overview">
<div class="row-fluid">
    <div class="span9">
      <h1><a href="<?php echo _URL;?>" rel="tooltip" data-placement="right" title="Switch to front-end"><?php if(_SITENAME != "PHP Melody") { echo _SITENAME; } else { echo $config['homepage_title']; } ?></a></h1>
   </div>
    <div class="span3">
        <div id="admin-pane" class="pull-right">
            <div class="user-data">
            <span class="user-avatar">
			<?php if (version_compare($official_version, $config['version']) == 1) : ?>
			<span class="user-notification">1</span>
			<?php endif; ?>
            
            <?php if(($tab_internallog > 0) && is_admin()) {?><span class="user-notification"><?php echo pm_number_format($tab_internallog); ?></span><?php }elseif(($tab_video_total > 0) && is_moderator() && $mod_can['manage_videos']) { ?><span class="user-notification"><?php echo $tab_video_total; ?></span><?php } ?>
            
            <span class="user-avatar-img"><img src="<?php echo _URL."/uploads/avatars/".$userdata['avatar']; ?>" height="27" width="27" border="0" class="img-rounded" alt="" /></span>
            </span>
            <span class="greet-links">
            <div class="ellipsis"><strong>سلام <?php echo ucwords($userdata['name']);?>!</strong></div>
            </span>
            </div>
            <div class="user-menu">
            <a class="dropdown-toggle" data-toggle="dropdown" href="#"><i class="icon-chevron-down"></i></a>
                <ul class="dropdown-menu pull-right pm-ul-user-menu" role="menu" aria-labelledby="dLabel" data-hover="dropdown" data-delay="100" data-close-others="false">
	                <?php if (is_admin()) : ?>
	                <?php if (version_compare($official_version, $config['version']) == 1) : ?>
	                <li><a tabindex="-1" href="#" style="color: #C30;">این نصب را بروزرسانی کن</a></li>
	                <?php endif; ?>
	                <?php if($tab_internallog > 0) : ?>
	                <li><a tabindex="-1" href="readlog.php">Recorded issues</a><span class="user-notification" style="left: 88%; top: 8px;"><?php echo $tab_internallog; ?></span></li>
	                <?php endif; ?>
	                <li><a tabindex="-1" href="settings.php">تنظیمات</a></li>
	                <li><a tabindex="-1" href="settings_theme.php">تنظیمات طرح</a></li>
	                <li><a tabindex="-1" href="password.php">تغییر رمز عبور</a></li>
	                <li class="divider"></li>
				<?php endif; ?>
                <li><a tabindex="-1" href="<?php echo _URL; ?>/login.php?do=logout">طرح بندی کردن</a></li>
                </ul>
            </div>
        </div>
    </div>
</div>
</header>
<a id="top"></a>

<?php include_once('sideNav.php'); ?>

<?php
if (is_array($restricted_access)  && in_array($showm, $restricted_access))
{
	restricted_access(true);
}

$official_version = cache_this('read_version', 'pm_version'); 
if (version_compare($official_version, $config['version']) == 1) : ?>
<div class="new-release-bar">
جدیدترین نسخه <strong>PHP Melody</strong> در دسترس است! <a href="http://www.96down.com/customer/?utm_source=install_header" target="_blank">برای دانلود بروزرسانی v<?php echo $official_version; ?> اینجا کلیک کن</a>.
</div>
<?php endif; ?>
<!--[if lt IE 9]>
<div class="alert alert-info alert-old-browser">The browser you are using could be limiting the potential of <?php echo _SITENAME; ?>. We strongly recommend that you upgrade to a newer/different browser.</div>
<![endif]-->