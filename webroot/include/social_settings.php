<?php

if ( ! defined('ABSPATH'))
{
	exit();
}

define('ACTIVITIES_PER_PAGE', 20);
define('NOTIFICATIONS_PER_PAGE', 7);
define('FOLLOW_PROFILES_PER_PAGE', 10);

/* Note for plugin/template developers: 
 * 1. Define your plugin's verbs and object/targe types with a short and unique prefix
 * (ex. if plugin's name = "Facebook Login", prefix would look like "fbl_" and activity might be "fbl_loggedin").
 * Non-prefix verbs are reserved for PHP Melody's core. 
 * 2. Define custom activities in a different file
 * 3. use activity_load_options() and activity_save_options() to add custom activities to pm_config.
 */
// reserved Activity Types (verbs)
define('ACT_TYPE_FOLLOW', 'follow');
define('ACT_TYPE_UNFOLLOW', 'unfollow');
define('ACT_TYPE_WATCH', 'watch');
define('ACT_TYPE_READ', 'read');
define('ACT_TYPE_COMMENT', 'comment');
define('ACT_TYPE_LIKE', 'like');
define('ACT_TYPE_DISLIKE', 'dislike');
define('ACT_TYPE_FAVORITE', 'favorite');
define('ACT_TYPE_JOIN', 'join'); // a.k.a. register
define('ACT_TYPE_UPLOAD_VIDEO', 'upload-video');
define('ACT_TYPE_SUGGEST_VIDEO', 'suggest-video');
define('ACT_TYPE_UPDATE_AVATAR', 'update-avatar');
define('ACT_TYPE_STATUS', 'status');
define('ACT_TYPE_SEND_VIDEO', 'send-video'); // sharing via email
define('ACT_TYPE_CREATE_PLAYLIST', 'create-playlist');
define('ACT_TYPE_UPDATE_PLAYLIST', 'update-playlist');

// reserved activity Object/Target types
define('ACT_OBJ_USER', 		'user');
define('ACT_OBJ_VIDEO', 	'video');
define('ACT_OBJ_COMMENT', 	'comment');
define('ACT_OBJ_ARTICLE',	'article');
define('ACT_OBJ_PROFILE', 	'profile');
define('ACT_OBJ_ACTIVITY', 	'activity');
define('ACT_OBJ_STATUS', 	'status');
define('ACT_OBJ_PLAYLIST', 	'playlist');

$default_activity_options = array(ACT_TYPE_FOLLOW => 1,
								  ACT_TYPE_UNFOLLOW => 0,
								  ACT_TYPE_WATCH => 0, 
								  ACT_TYPE_LIKE => 1,
								  ACT_TYPE_DISLIKE => 1,
								  ACT_TYPE_FAVORITE => 1,
								  ACT_TYPE_SEND_VIDEO => 0,
								  ACT_TYPE_UPLOAD_VIDEO => 1,
								  ACT_TYPE_SUGGEST_VIDEO => 1,
								  ACT_TYPE_READ => 0,
								  ACT_TYPE_COMMENT => 1,
								  ACT_TYPE_JOIN => 1,
								  ACT_TYPE_UPDATE_AVATAR => 1,
								  ACT_TYPE_STATUS => 1,
								  ACT_TYPE_CREATE_PLAYLIST => 1,
								  ACT_TYPE_UPDATE_PLAYLIST => 1
								);

$activity_labels = array(ACT_TYPE_FOLLOW 		=> 'دنبال کردن کاربر',
						  ACT_TYPE_UNFOLLOW 	=> 'دنبال نکردن کاربر',
						  ACT_TYPE_WATCH 		=> 'تماشا ویدیو',
						  ACT_TYPE_LIKE 		=> 'لایک ویدیو',
						  ACT_TYPE_DISLIKE	 	=> 'دیس لایک ویدیو',
						  ACT_TYPE_FAVORITE 	=> 'افزودن ویدیو به علاقه مندی ها',
						  ACT_TYPE_SEND_VIDEO 	=> 'ارسال ویدیو به دوستان',
						  ACT_TYPE_UPLOAD_VIDEO => 'آپلود ویدیو',
						  ACT_TYPE_SUGGEST_VIDEO => 'پیشنهاد ویدیو از سایت های اشتراک ویدیو',
						  ACT_TYPE_READ 		=> 'خواندن اخبار سایت',
						  ACT_TYPE_COMMENT 		=> 'ارسال نظر',
						  ACT_TYPE_JOIN 		=> 'عضویت در سایت',
						  ACT_TYPE_UPDATE_AVATAR => 'تغییر تصویر کاربری',
						  ACT_TYPE_STATUS 		=> 'ارسال استاتوس',
						  ACT_TYPE_CREATE_PLAYLIST => 'ساخت پلی لیست',
						  ACT_TYPE_UPDATE_PLAYLIST => 'افزودن ویدیو به پلی لیست'
						);


$notify_loggable_activity_types = array(ACT_TYPE_FOLLOW,
										ACT_TYPE_LIKE,
										ACT_TYPE_FAVORITE,
										ACT_TYPE_COMMENT
										);
