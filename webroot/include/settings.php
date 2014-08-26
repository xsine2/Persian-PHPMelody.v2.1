<?php
require_once(ABSPATH .'include/functions.php');

// globals
$conn_id = db_connect();

if ( ! $conn_id)
{
	exit('<h1>Error establishing a database connection</h1>');
}

$time_now = time();
$config = get_config();

$_video_categories = null;
$_article_categories = null;
$_countries_list = array();

if ($config['mod_article'] == '1')
{
	require_once(ABSPATH .'include/article_functions.php');
}

if ($config['mod_social'] == '1')
{
	require_once(ABSPATH .'include/social_settings.php');
	require_once(ABSPATH .'include/social_functions.php');
}

//	Configs
$template_f = $config['template_f']; // Your current template this value should reflect the folder's name
define('_UPFOLDER', 'uploads'); // NO NEED TO EDIT THIS [!!!] The upload folder name for THUMBS & user AVATARS.
define('_EMAIL', $config['contact_mail']); // Your personal e-mail address (Contact form messages will be delivered to this email).
define('_THUMB_FROM', (int) $config['thumb_from']); // FETCH THUMBS FROM YOUTUBE OR LOCALHOST ? (1 = Youtube.com // 2 = Your server)
define('_BROWSER_PAGE', (int) $config['browse_page']); // Number of results per category page
define('_ISNEW_DAYS', (int) $config['isnew_days']); // How many days should a video stay marked as 'NEW'.
define('_ISPOPULAR', (int) $config['ispopular']); // Define the minimum number of views a video needs to become tagged as POPULAR
define('_STOPBADCOMMENTS', (int) $config['stopbadcomments']); // Don't post comments that contain bad words: bad_words.txt
define('_HTMLCOUNTER', stripslashes($config['counterhtml']));
define('_FAV_LIMIT', (int) $config['fav_limit']); // Favorite videos limit / user

define('_PM_VERSION', $config['version']); // PHP MELODY VERSION
define('_TPLFOLDER', $config['template_f']); // CURRENT YOUTUBE URL FOR THUMBS
define('_SEOMOD', (int) $config['seomod']);	// SHOW SEO FRIENDLY URLS OR NOT
define('_MOD_ARTICLE', (int) $config['mod_article']);
define('_ALLOW_USER_UPLOADVIDEO', (int) $config['allow_user_uploadvideo']);
define('_ALLOW_USER_SUGGESTVIDEO', (int) $config['allow_user_suggestvideo']);
define('_MOD_SOCIAL', (int) $config['mod_social']);

// ad types
define('_AD_TYPE_CLASSIC', 1);
define('_AD_TYPE_VIDEO', 2);
define('_AD_TYPE_PREROLL', 3);

if ( ! defined('_SITENAME')) // to avoid any possible issues with future versions
{
	define('_SITENAME', str_replace('"', '&quot;', $config['homepage_title'])); // "homepage_title" is the new "sitename". @since 1.9
}

define('_ISSMTP', (int) $config['issmtp']);

define('_USE_HQ_VIDS', (int) $config['use_hq_vids']);

if ($config['gzip'] == 1)
{
	if(substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip'))
		ob_start("ob_gzhandler");
	else
		ob_start();
}

if(_SEOMOD == '1')
	define('_FEXT', 'html');
else
	define('_FEXT', 'php');

define('_TOPVIDS', (int) $config['top_videos']);
define('_NEWVIDS', (int) $config['new_videos']);

//	Item types
define('IS_VIDEO', 1);
define('IS_ARTICLE', 2);
define('IS_PAGE', 3);

//	Comments moderation levels
define('MODERATE_ALL',    2);
define('MODERATE_GUESTS', 1);
define('MODERATE_NONE',   0);

//	Account activation levels
define('AA_DISABLED', 0);
define('AA_USER',     1);
define('AA_ADMIN',    2);

//	Users power levels
define('U_ACTIVE',   0); //	active, registered user
define('U_ADMIN',    1); // master
define('U_INACTIVE', 2); //	inactive, registered user
define('U_MODERATOR', 3);
define('U_EDITOR', 4);

//	Paths
define('_VIDEOS_DIR_PATH', ABSPATH . _UPFOLDER . "/videos/");
define('_THUMBS_DIR_PATH', ABSPATH . _UPFOLDER . "/thumbs/");
define('_VIDEOS_DIR', _URL ."/" . _UPFOLDER . "/videos/");
define('_THUMBS_DIR',  _URL ."/" . _UPFOLDER . "/thumbs/");
define('_NOTHUMB',  _URL .'/templates/'._TPLFOLDER.'/img/no-thumbnail.jpg');
define('_ARTICLE_ATTACH_DIR_PATH', ABSPATH . _UPFOLDER . "/articles/");
define('_ARTICLE_ATTACH_DIR', _URL .'/'. _UPFOLDER .'/articles/');

//	Thumbnail sizes (px)
define('THUMB_W_VIDEO', $config['thumb_video_w']);
define('THUMB_H_VIDEO', $config['thumb_video_h']);
define('THUMB_W_ARTICLE', $config['thumb_article_w']);
define('THUMB_H_ARTICLE', $config['thumb_article_h']);
define('THUMB_W_AVATAR', $config['thumb_avatar_w']);
define('THUMB_H_AVATAR', $config['thumb_avatar_h']);

$url2 = 'http://'.$_SERVER['HTTP_HOST'];
$temp = str_replace('http://', '', _URL);
$temp = explode("/", $temp);
$count = count($temp);
for($i = 1; $i < $count; $i++)
{
	$url2 .= "/".$temp[$i];
}
$url2 = rtrim($url2, "/");

define('_URL2', $url2);
unset($temp, $count, $url2);

/*
 *  Video Player configs
 */
//	'Index' player width and height
if($config['player_w_index'] != '')
	define('_PLAYER_W_INDEX', $config['player_w_index']);
else
	define('_PLAYER_W_INDEX', 430);
if($config['player_h_index'] != '')
	define('_PLAYER_H_INDEX', $config['player_h_index']);
else
	define('_PLAYER_H_INDEX', 344);

//	'My Favorites' player width and height
if($config['player_w_favs'] != '')
	define('_PLAYER_W_FAVS', $config['player_w_favs']);
else
	define('_PLAYER_W_FAVS', 575);
if($config['player_h_favs'] != '')
	define('_PLAYER_H_FAVS', $config['player_h_favs']);
else
	define('_PLAYER_H_FAVS', 466);

//	Default player width and height
if($config['player_w'] != '')
	define('_PLAYER_W', $config['player_w']);
else
	define('_PLAYER_W', 496);
if($config['player_h'] != '')
	define('_PLAYER_H', $config['player_h']);
else
	define('_PLAYER_W', 401);

//	Embed player width and height
if($config['player_w_embed'] != '')
	define('_PLAYER_W_EMBED', $config['player_w_embed']);
else
	define('_PLAYER_W_EMBED', 425);
if($config['player_h_embed'] != '')
	define('_PLAYER_H_EMBED', $config['player_h_embed']);
else
	define('_PLAYER_W_EMBED', 344);

if($config['player_autoplay'] == 1)
	define('_AUTOPLAY', 'true');
else
	define('_AUTOPLAY', 'false');

if($config['featured_autoplay'] == 1)
	define('_AUTOPLAY_FEATURED', 'true');
else
	define('_AUTOPLAY_FEATURED', 'false');

if($config['player_autobuff'] == 1)
	define('_AUTOBUFF', 'true');
else
	define('_AUTOBUFF', 'false');

if($config['player_bgcolor'] != '')
	define('_BGCOLOR', $config['player_bgcolor']);
else
	define('_BGCOLOR', '253133');

if($config['player_timecolor'] != '')
	define('_TIMECOLOR', $config['player_timecolor']);
else
	define('_TIMECOLOR', 'FFCC00');

if($config['player_watermarkshow'] == "always" || $config['player_watermarkshow'] == "fullscreen")
	define('_WATERMARKSHOW', $config['player_watermarkshow']);
else
	define('_WATERMARKSHOW', 'fullscreen');

if($config['player_watermarklink'] != '')
	define('_WATERMARKLINK', $config['player_watermarklink']);
else
	define('_WATERMARKLINK', _URL."/");

if($config['jwplayerskin'] != '')
	define('_JWSKIN', $config['jwplayerskin']);
else
	define('_JWSKIN', "glow.zip");

define('_WATERMARKURL', $config['player_watermarkurl']);

define('_SEARCHSUGGEST', $config['search_suggest']);

// Initialize SMARTY
require(ABSPATH . 'include/Smarty/Smarty.class.php');
$smarty = new Smarty;
$smarty->template_dir = 	ABSPATH . "templates/"._TPLFOLDER;	//NO trailing or preceding slash!
$smarty->compile_dir =		ABSPATH . "include/Smarty/templates_c"; 	//NO trailing or preceding slash!
$smarty->cache_dir =  		ABSPATH . "include/Smarty/cache"; 			//NO trailing or preceding slash!
$smarty->config_dir = 		ABSPATH . "include/Smarty/configs"; 		//NO trailing or preceding slash!

// Theme customizations & logo
apply_theme_customizations();
$smarty->assign('_custom_logo_url', $config['custom_logo_url']);
$smarty->assign('_footer_switch_ui_link', get_switch_ui_url());

// Cookie Settings
define('COOKIE_SUFX', md5(_URL));
define('COOKIE_PATH', preg_replace('|https?://[^/]+|i', '', _URL.'/' ));
define('COOKIE_NAME', 'melody_'.COOKIE_SUFX);
define('COOKIE_KEY', 'melody_key_'.COOKIE_SUFX);
define('COOKIE_TIME', 864000);		//	10 days
define('COOKIE_AUTHOR', 'guest_name_'.COOKIE_SUFX);
define('COOKIE_VIDEOAD', 'melody_vad_'.COOKIE_SUFX);
define('COOKIE_LANG', 'melody_lang_'.COOKIE_SUFX);
define('COOKIE_PREROLLAD', 'melody_pad_'.COOKIE_SUFX);
define('PREROLL_AD_HASH', substr(COOKIE_SUFX, 0, 12));

$parsed_url = parse_url(_URL);
if ($_SERVER['HTTPS'] == 'on' || $parsed_url['scheme'] == 'https')
{
	define('COOKIE_SECURE', true);
}
else
{
	define('COOKIE_SECURE', false);
}

if (version_compare(phpversion(), '5.2', '>='))
{
	define('COOKIE_DOMAIN', false);
	define('COOKIE_HTTPONLY', true);
}
else
{
	$cookie_domain = false;
	if ($parsed_url['host'] != 'localhost')
	{
		$pieces = explode('.', $parsed_url['host']);
		$pieces_count = count($pieces);

		$cookie_domain = '.'. $pieces[$pieces_count - 2] . '.'. $pieces[$pieces_count - 1];
		$cookie_domain .= '; HttpOnly';
		unset($pieces, $pieces_count);
	}
	define('COOKIE_DOMAIN', $cookie_domain);
	define('COOKIE_HTTPONLY', false);
	unset($cookie_domain);
}
unset($parsed_url);

//	Ads System
$config['show_ads'] = 1;
if($config['show_ads'] == 1)
{
	$ads = array();

	if ($result = mysql_query("SELECT * FROM pm_ads WHERE active = '1'"))
	{
		while($row = mysql_fetch_assoc($result))
		{
			$ads[$row['id']] = $row;
		}
		$total_ads = count($ads);
		if($total_ads != 0)
		{
			foreach($ads as $k => $v)
			{
				if($v['code'] != '')
				{
					if ($v['disable_stats'] == 0)
					{
						$v['code'] .= '<img src="'. _URL .'/ajax.php?p=stats&do=show&aid='. $v['id'] .'&at='. _AD_TYPE_CLASSIC .'" width="1" height="1" border="0" />';
					}
					
					$smarty->assign('ad_'.$v['id'], $v['code']);
				}
			}
		}
		mysql_free_result($result);
	}
}
$total_video_ads = 0;
if($config['total_videoads'] > 0)
{
	if(isset($_COOKIE[COOKIE_VIDEOAD]) && (strlen($_COOKIE[COOKIE_VIDEOAD]) == 12))
	{
		$total_video_ads = 0;
	}
	else
	{
		$total_video_ads = $config['total_videoads'];

		$video_ad = array();
		$sql = "SELECT id, hash FROM pm_videoads WHERE status='1' ORDER BY RAND() LIMIT 1";
		$result = @mysql_query($sql);
		if (mysql_num_rows($result) == 0)
		{
			$total_video_ads = 0;
		}
		else
		{
			$row = @mysql_fetch_assoc($result);
			$video_ad_hash =  $row['hash'];

			$smarty->assign('video_ad_hash', $video_ad_hash);
			if($row['redirect_type'] == 1)
			{
				// open window in same window
				$smarty->assign('video_ad_target', '_self');
			}
			else
			{
				$smarty->assign('video_ad_target', '_blank');
			}
			@mysql_free_result($result);
		}
	}
}
$smarty->assign('total_video_ads', $total_video_ads);

$smarty->assign('preroll_ad_hash', PREROLL_AD_HASH);

$default_language = 'persian';
$_language_email_dir = 'persian';

$langs = array();

//	Persian
$langs[1]["title"]	= "persian";
$langs[1]["file"]	= 'persian.php';
$langs[1]["email_dir"] = 'english';

$lang_id = 1; 	//	english by default

if($config['default_lang'] != 0 && @array_key_exists($config['default_lang'], $langs))
{
	$lang_id = $config['default_lang'];
	$_language_email_dir = $langs[ $lang_id ]["email_dir"];
}

if(isset($_COOKIE[COOKIE_LANG]))
{
	if(@array_key_exists($_COOKIE[COOKIE_LANG], $langs))
	{
		$lang_id = (int) $_COOKIE[COOKIE_LANG];
	}
}

if(@file_exists( ABSPATH . "include/lang/" . $langs[ $lang_id ]["file"]) === FALSE)
{
	$error = "Error: Language file not found.";
	if($lang_id > 1)
	{
		$lang_id = 1;
		if(@file_exists( ABSPATH . "include/lang/" . $langs[ $lang_id ]["file"]) === FALSE)
		{
			echo $error;
			exit();
		}
		else
		{
			@include_once(ABSPATH . "include/lang/" . $langs[ $lang_id ]["file"]);
		}
	}
}
else
{
	@include_once(ABSPATH . "include/lang/" . $langs[ $lang_id ]["file"]);
}

$smarty->assign('lang', $lang);
$smarty->assign('langs_array', $langs);
$smarty->assign('current_lang_id', $lang_id);

if ((int) $config['maintenance_mode'] == 1)
{
	if ( ! function_exists('is_user_logged_in'))
	{
		include(ABSPATH .'include/user_functions.php');
	}
	
	require_once(ABSPATH.'include/mmodframework.class.php');
	$modframework = new modframework();
	if(isset($config['mm_framework']) && $config['mm_framework'] != 0) $modframework->initframework();
	
	include(ABSPATH .'include/islogged.php');

	$x = explode('/', $_SERVER['SCRIPT_NAME']);
	$script_name = array_pop($x);
	$dir_name = array_pop($x);

	if ($dir_name != 'admin' && $userdata['power'] != U_ADMIN && $userdata['power'] != U_MODERATOR)
	{
		$smarty->assign('maintenance_display_message', ($config['maintenance_display_message'] != '') ? $config['maintenance_display_message'] : $lang['default_maintenance_message']);
		
		$smarty->assign('meta_title', ('' != $config['homepage_title']) ? $config['homepage_title'] : sprintf($lang['homepage_title'], _SITENAME));
		$smarty->display('maintenance.tpl');

		if ($conn_id)
		{
			mysql_close($conn_id);
		}

		exit();
	}
	
	$smarty->assign('maintenance_mode', true);
	// continue
}

$smarty->register_function('smarty_fewchars', 'smarty_fewchars');
$smarty->register_function('echo_securimage_sid', 'smarty_echo_securimage_sid');
$smarty->register_function('get_advanced_video_list', 'smarty_get_advanced_video_list', false);
$smarty->register_function('dropdown_menu_video_categories', 'smarty_html_list_categories');
$smarty->assign('allow_registration', $config['allow_registration']);

if (_MOD_ARTICLE)
{
	$smarty->register_function('dropdown_menu_article_categories', 'smarty_art_html_list_categories', false);
}
else
{
	function smarty_art_html_list_categories($params, &$smarty)
	{
		return '';
	}
	$smarty->register_function('dropdown_menu_article_categories', 'smarty_art_html_list_categories', false);
}

include(ABSPATH .'include/page_functions.php');

$smarty->register_function('get_video_meta_list', 'smarty_get_video_meta_list');
$smarty->register_function('get_video_meta', 'smarty_get_video_meta');
$smarty->register_function('get_article_meta_list', 'smarty_get_article_meta_list');
$smarty->register_function('get_article_meta', 'smarty_get_article_meta');
$smarty->register_function('get_page_meta_list', 'smarty_get_page_meta_list');
$smarty->register_function('get_page_meta', 'smarty_get_page_meta');

$links_to_pages = '';
if ($config['total_pages'] > 0)
{
	$links_to_pages = generate_footer_page_links();
	$smarty->assign('links_to_pages', $links_to_pages);
}

session_save_footprint();

$x = explode('/', $_SERVER['SCRIPT_NAME']);
$script_name = array_pop($x);

$smarty->assign('_script_name', $script_name);

unset($dir_name, $script_name, $x);
require_once(ABSPATH.'include/mmodframework.class.php');
$modframework = new modframework();
if(isset($config['mm_framework']) && $config['mm_framework'] != 0) $modframework->initframework();
