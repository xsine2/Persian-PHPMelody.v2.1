<?php
session_start();
require_once('../config.php');
include_once('functions.php');
include_once( ABSPATH . 'include/user_functions.php');
include_once( ABSPATH . 'include/islogged.php');

if ( ! defined('U_ADMIN'))
{
	define('U_ADMIN', 1);
}

if ( ! $logged_in || ! (is_admin() || is_moderator() || is_editor()))
{
	exit('ورود ممنوع!');
}

$illegal_chars = array(">", "<", "&", "'", '"');

$message = '';
$page	 = '';


if ($_GET['p'] != '' || $_POST['p'] != '')
{
	$page = ($_GET['p'] != '') ? $_GET['p'] : $_POST['p'];
}

if ($_GET['do'] != '' || $_POST['do'] != '')
{
	$action = ($_GET['do'] != '') ? $_GET['do'] : $_POST['do'];
}

if ($page == '')
{
	exit('پارامتر صفحه الزامی است.');
}

switch ($page)
{
	case 'addvideo':
		
		switch ($action)
		{
			case 'checkurl':
				
				if ($_POST['url'] == '')
				{
					exit();
				}
				
				if ( ! $logged_in || ( ! is_admin() && ! is_moderator() && ! is_editor()))
				{
					exit();
				}
				if (is_editor() || (is_moderator() && !mod_can('manage_videos')))
				{
					exit();
				}
				
				$msg = '';
				$msg_color = '';
				
				$url = trim($_POST['url']);
				$url = secure_sql($url);
				$uniq_id = '';
				
				if (strpos($url, 'youtube.com'))
				{
					preg_match("/v=([^(\&|$)]*)/", $url, $matches);
					$url = 'http://www.youtube.com/watch?v='. $matches[1];
				}
			
				$sql = "SELECT uniq_id FROM pm_videos_urls 
						WHERE direct = '". $url ."'";
				$result = @mysql_query($sql);
				if ( ! $result)
				{
					$msg = 'MySQL error';
					$msg_color = 'red';
				}
				if (mysql_num_rows($result) > 0)
				{
					$row = mysql_fetch_assoc($result);
					$uniq_id = $row['uniq_id'];
					
					$msg = 'این آدرس قبلا به دیتابیس تان اضافه شده. <a href="modify.php?vid='. $uniq_id. '">ویرایش کردن</a> ویدئو.';
					$msg_color = 'red';
				}
				else
				{
					$msg = '';
					$msg_color = 'green';
				}
				mysql_free_result($result);
				
				if (strlen($msg) > 0)
				{
					echo '<small><i><span style="color: '. $msg_color .';">'. $msg .'</span></i></small>';
				}
				
				exit(); // the end
				
			break;
			
			case 'generate-video-slug':
				
				if ($_POST['video-title'] != '')
				{
					$text = trim($_POST['video-title']);
					$text = sanitize_title($text);
					$text = urldecode($text); 
					exit($text);
				}
				
				exit();
				
			break;
		}
		
	break;
	
	case 'metadata':
		
		if( ! (is_admin() || (is_moderator() && mod_can('manage_videos'))))
		{
			exit(json_encode(array('type' => 'error',
								   'html' => '<div class="alert alert-error" id="_error">شما نباید اجازه بدهید که این عمل انجام شود.</div>'
								  )
							)
				);
		}

		$response_type = 'success'; // success, error
		$error_msg = $html = '';
		
		switch ($action)
		{
			case 'add-meta':
				
				$meta_id = 0;
				
				if ($_POST['meta_key_select'] != '' && $_POST['meta_key_select'] != '_nokey')
				{
					$key = trim($_POST['meta_key_select']);
				}
				else
				{
					$key = trim($_POST['meta_key']);					
				}
				$key = substr($key, 0, 255);

				if (strlen($key) > 0)
				{
					$_POST['meta_value'] = str_replace('"', '&quot;', $_POST['meta_value']);
					$_POST['meta_key'] = $key;
					
					if (is_meta_key_reserved($key))
					{
						$error_msg = 'نام هایی که با زیرخط شروع می شوند "_" برای سیستم رزرو شده اند.'; 
					}
					else
					{
						$meta_id = add_meta((int) $_POST['item_id'], $_POST['item_type'], $key, $_POST['meta_value']);
					
						if ($meta_id)
						{
							$html = admin_custom_fields_row($meta_id, $_POST);
						}
					}
				}
				else
				{
					$error_msg = 'فیلد "نام سفارشی" الزامی است.';
				}
				
				if ($error_msg != '')
				{
					$html = '<div class="alert alert-error" id="_error_">'. $error_msg .'</div>';
					$response_type = 'error';
				}

				exit(json_encode(array('type' => $response_type, 'html' => $html, 'meta_id' => $meta_id)));
				
			break;

			case 'update-meta':
				
				$meta_id = (int) $_POST['meta_id'];
				
				if ( ! $meta_id)
				{
					$error_msg = 'meta_id غیرمعتبر است.';
				}
				else
				{
					if (is_meta_key_reserved($_POST['meta_key']))
					{
						$error_msg = 'نام هایی که با زیرخط شروع می شوند "_" برای سیستم رزرو شده اند.';
					}
					else
					{
						$_POST['meta_value'] = str_replace('"', '&quot;', $_POST['meta_value']);
						$update = update_meta(0, 0, $_POST['meta_key'], $_POST['meta_value'], $meta_id);
						
						if ($update)
						{
							$html = '<div class="alert alert-success">بروزرسانی شده</div>';
						}
						else
						{
							$error_msg = 'در هنگام بروزرسانی خطایی رخ داده. لطفا از دوباره تلاش کن.';
						}
					}
				}
				
				if ($error_msg != '')
				{
					$response_type = 'error';
					$html = '<div class="alert alert-error" id="_error_">'. $error_msg .'</div>';
				}

				exit(json_encode(array('type' => $response_type, 'html' => $html, 'meta_id' => $meta_id)));

			break;
						
			case 'delete-meta':
			
				$meta_id = (int) $_POST['meta_id'];
				
				if ($meta_id)
				{
					$deleted = delete_meta(0, 0, '', $meta_id);
				}
				
				exit(json_encode(array('type' => $response_type, 'html' => '', 'meta_id' => $meta_id)));
				
			break;
			
			default: 
				exit();
			break;
		}
		
	break; // end case 'metadata';

	case 'video-category-mgr':
	
		switch ($action)
		{
			case 'add-category': // AJAX for inline add category (on add/embed/edit video/stream)
				
				$ajax_msg = '';
				$response_type = 'success'; // success, error
				
				if ( ! $logged_in || ! is_admin())
				{
					$response_type = 'error';
					$ajax_msg = ($logged_in) ? 'ورود ممنون! شما مدیر ارشد نیستید.' : 'لطفا وارد شوید.';
				}
				
				$pattern = '/(^[a-z0-9_-]+)$/i';
				$parent_cid = (int) $_POST['category'];
				$tag = trim($_POST['tag']);
				$name = trim($_POST['name']); 
				$create_category_select_html = ''; // will hold the updated 'Create in' dropdown 
				
				$all_categories = load_categories();
				
				if ($parent_cid < 0)
				{
					$parent_cid = 0;
				}
				
				if (empty($tag) || empty($name))
				{
					$ajax_msg = '<code>نام دسته/code> و <code>اسلاگ</code> فیلدهای اجباری هستند.';
				}
				else
				{
					if( ! preg_match('/(^[a-z0-9_-]+)$/i', $tag)) 
					{
						$ajax_msg = 'لطفا مطمئن شو که اسلاگ به درستی تایپ شده (بدون فاصله ، فقط کاراکترهای انگلیسی یعنی a تا z وچک و بزرگ ، اعداد "_" و "-"(.';
					}
					
					if (count($all_categories) > 0)
					{
						foreach ($all_categories as $id => $c)
						{
							if ($c['tag'] == $tag)
							{
								$ajax_msg = 'این اسلاگ استفاده شده برای دسته<strong>'. $c['name'] .'</strong>';
								break;
							}
						}
					}
				}
			
				if ($ajax_msg == '')
				{
					// get position of the last category
					$sql = "SELECT MAX(position) as max  
								  FROM pm_categories 
							 WHERE parent_id = '". $parent_cid ."'";
					$result = mysql_query($sql);
					$row = mysql_fetch_assoc($result);
					
					mysql_free_result($result);
					
					$position = ($row['max'] > 0) ? ($row['max'] + 1) : 1;
					$sql = "INSERT INTO pm_categories (parent_id, tag, name, published_videos, total_videos, position, description, meta_tags) 
								 VALUES ('". $parent_cid ."', 
								 		 '". secure_sql($tag) ."', 
										 '". secure_sql($name) ."', 
										 0, 
										 0, 
										 ". $position .", 
										 '',
										 '')";
					$result = mysql_query($sql);
					if ( ! $result)
					{
						$response_type = 'error';
						$ajax_msg = 'در هنگام ایجاد دسته جدید خطایی رخ داده.<br /><strong>گزارش mysql :</strong>: '.mysql_error();
					}
					else
					{
						$_POST['current_selection'][] = mysql_insert_id();
						
						$categories_dropdown_options = array(
														'attr_name' => 'category[]',
														'attr_id' => 'main_select_category must',
														'attr_class' => 'category_dropdown span12',
														'select_all_option' => false,
														'spacer' => '&mdash;',
														'selected' => $_POST['current_selection'],
														'other_attr' => 'multiple="multiple"'
														);
						
						unset($_video_categories);
						$ajax_msg = categories_dropdown($categories_dropdown_options);
						$categories_dropdown_options = array(
														'first_option_text' => '&ndash; Parent Category &ndash;', 
														'first_option_value' => '-1',
														'attr_name' => 'add_category_parent_id',
														'attr_id' => '',
														'attr_class' => '',
														'select_all_option' => true,
														'spacer' => '&mdash;'
														);
						$create_category_select_html = categories_dropdown($categories_dropdown_options);
						$response_type = 'success';
					}
				}
				else
				{
					$response_type = 'error';
					$ajax_msg = '<div class="alert alert-error">'. $ajax_msg .'</div>';
				}
			
				exit(json_encode(array('message' => $ajax_msg, 'type' => $response_type, 'create-category-select-html' => $create_category_select_html)));
			break;
		}
		
	break;

	case 'page':
		
		switch ($action)
		{
			case 'delete':
				
				if( ! $logged_in || ! is_admin())
				{
					echo '<div class="alert">متاسفانه شما نباید دسترسی به این ناحیه را داشته باشید.</div>';
					exit();
				}
					
				if ( ! csrfguard_check_referer('_admin_pages'))
				{
					echo '<div class="alert alert-error">رمز عبور اشتباه است یا سشن منقضی شده. لطفا صفحه را رفرش نمایید و از دوباره تلاش کنید.</div>';
					exit();
				}
				
				$result = delete_page($_GET['id']);				
				if ($result['type'] == 'error')
				{
					echo '<div class="alert alert-error">'. $result['msg'] .'</div>';
				}
				else
				{
					echo csrfguard_form('_admin_pages');
					echo '<div class="alert alert-success">'. $result['msg'] .'</div>';
				}
				
				exit();
				
			break;
			
		}
		
	break;

	case 'article-category-mgr':
		
		// test permissions for moderators; editors and admins are allowed.
		if (is_moderator() && mod_cannot('manage_articles'))
		{
			echo '<div class="alert">متاسفانه شما نباید دسترسی به این ناحیه را داشته باشید.</div>';
			exit();
		}
		
		switch ($action)
		{

			case 'delete':	//	delete a article category
			
				if ( ! csrfguard_check_referer('_admin_catmanager'))
				{
					echo '<div class="alert alert-error">رمز عبور اشتباه است یا سشن منقضی شده. لطفا صفحه را رفرش نمایید و از دوباره تلاش کنید.</div>';
					exit();
				}
				
				$id = (int) $_GET['id']; 
				if ($id > 0)
				{
					$result = art_delete_category($id);
					if ($result['type'] == 'error')
					{
						echo '<div class="alert alert-error">'. $result['msg'] .'</div>';
					}
					else
					{
						echo csrfguard_form('_admin_catmanager');
						echo '<div class="alert alert-success">'. $result['msg'] .'</div>';
					}
				}
			
			break;
			
			case 'inline-add-category':
				
				$ajax_msg = '';
				$response_type = 'success'; // success, error
				
				$pattern = '/(^[a-z0-9_-]+)$/i';
				$parent_cid = (int) $_POST['category'];
				$tag = trim($_POST['tag']);
				$name = trim($_POST['name']); 
				$create_category_select_html = ''; // will hold the updated 'Create in' dropdown 

				$all_categories = load_categories(array('db_table' => 'art_categories'));
				
				if ($parent_cid < 0)
				{
					$parent_cid = 0;
				}
				if (empty($tag) || empty($name))
				{
					$ajax_msg = '<code>نام دسته</code> و <code>اسلاگ</code> فیدهای اجباری هستند.';
				}
				else
				{
					if( ! preg_match('/(^[a-z0-9_-]+)$/i', $tag)) 
					{
						$ajax_msg = 'لطفا مطمئن شو که اسلاگ به درستی تایپ شده (بدون فاصله ، فقط کاراکترهای انگلیسی یعنی a تا z وچک و بزرگ ، اعداد "_" و "-"(.';
					}
					if (count($all_categories) > 0)
					{
						foreach ($all_categories as $id => $c)
						{
							if ($c['tag'] == $tag)
							{
								$ajax_msg = 'این اسلاگ استفاده شده برای دسته<strong>'. $c['name'] .'</strong>.';
								break;
							}
						}
					}
				}
				
				if ($ajax_msg == '')
				{
					// get position of the last category
					$sql = "SELECT MAX(position) as max  
								  FROM pm_categories 
							 WHERE parent_id = '". $parent_cid ."'";
					$result = mysql_query($sql);
					$row = mysql_fetch_assoc($result);
					
					mysql_free_result($result);
					
					$position = ($row['max'] > 0) ? ($row['max'] + 1) : 1;
					
					$_POST['name'] = $name;
					$_POST['parent_id'] = $parent_cid;
					
					$result = art_insert_category($_POST);
					if ($result['type'] == 'error')
					{
						$ajax_msg = $result['msg'];
						$response_type = 'error';
					}
					else
					{
						$_POST['current_selection'][] = $result['id'];
						
						 $categories_dropdown_options = array(
	                        'db_table' => 'art_categories',
	                        'attr_name' => 'categories[]',
	                        'attr_id' => 'main_select_category',
							'attr_class' => 'category_dropdown span12',
	                        'select_all_option' => false,
	                        'spacer' => '&mdash;',
	                        'selected' => $_POST['current_selection'], 
	                        'other_attr' => 'multiple="multiple" size="3"',
	                        'option_attr_id' => 'check_ignore'
	                        );
						unset($_article_categories);
						$ajax_msg = categories_dropdown($categories_dropdown_options);
						$categories_dropdown_options = array(
										'db_table' => 'art_categories',
										'first_option_text' => '&ndash; Parent Category &ndash;', 
										'first_option_value' => '-1',
										'attr_name' => 'add_category_parent_id',
										'attr_id' => '',
										'attr_class' => '',
										'select_all_option' => true,
										'spacer' => '&mdash;'
										);
						$create_category_select_html = categories_dropdown($categories_dropdown_options); 
						$response_type = 'success';
					}
				}
				else
				{
					$response_type = 'error';
					$ajax_msg = '<div class="alert alert-error">'. $ajax_msg .'</div>';
				}

				exit(json_encode(array('message' => $ajax_msg, 'type' => $response_type, 'create-category-select-html' => $create_category_select_html)));
				
			break;
		}
		
	break;

	case 'articles':
		
		// test permissions for moderators; editors and admins are allowed.
		if (is_moderator() && mod_cannot('manage_articles'))
		{
			echo '<div class="alert">متاسفانه شما نباید به این ناحیه دسترسی داشته باشید.</div>';
			exit();
		}
				
		switch ($action)
		{
			case 'delete': // delete an article 
				
				if ( ! csrfguard_check_referer('_admin_articles'))
				{
					echo '<div class="alert alert-error">رمز عبور غیرمعتبر است یا سشن منقضی شده. لطفا صفحه را رفرش نمایید و از دوباره تلاش کنید.</div>';
					exit();
				}
					
				$id = (int) $_GET['id'];
				if ($id > 0)
				{
					$result = delete_article($id);
					
					if ($result['type'] == 'error')
					{
						echo '<div class="alert alert-error">'. $result['msg'] .'</div>';
					}
					else
					{
						// refresh token
						echo csrfguard_form('_admin_articles');
						echo '<div class="alert alert-success">'. $result['msg'] .'</div>';
					}
				}

			break;
			
			case 'generate-article-slug':
				
				if ($_POST['title'] != '')
				{
					$text = trim($_POST['title']);
					$text = sanitize_title($text);
					$text = urldecode($text); 

					exit($text);
				}
				
				exit();

			break;
			
			default: 
				exit();
			break;
		}
		
	break;

	case 'layout-settings': // settings_theme.php
		
		if ( ! is_admin())
		{
			$ajax_msg = ($logged_in) ? 'ورود ممنون!' : 'لطفا از دوباره وارد شوید.';
			$ajax_msg = '<div class="alert alert-error">'. $ajax_msg .'</div>';
			exit(json_encode(array('success' => false, 'msg' => $ajax_msg)));
		}
		
		switch ($action)
		{
			case 'delete-logo':
				
				if ($config['custom_logo_url'] == '')
				{
					exit(json_encode(array('success' => false, 'msg' => '')));
				}
				$tmp_parts = explode('/', $config['custom_logo_url']);
				$filename = array_pop($tmp_parts);
				
				if (is_writeable( ABSPATH . _UPFOLDER ))
				{
					$filepath = ABSPATH . _UPFOLDER .'/'. $filename;
				}
				else
				{
					$filepath = _THUMBS_DIR_PATH . $filename;
				}
				if (file_exists($filepath))
				{
					unlink($filepath);
				}
				update_config('custom_logo_url','');
				
				echo json_encode(array('success' => true,
										'msg' => '<div class="alert alert-info">لوگو حذف شد.</div>'
									  ));
				exit();
				
			break;
		}

	break;

	case 'settings': // settings.php
		
		if ( ! is_admin())
		{
			$ajax_msg = ($logged_in) ? 'ورود ممنوع!' : 'لطفا وارد شوید.';
			$ajax_msg = '<div class="alert alert-error">'. $ajax_msg .'</div>';
			
			echo json_encode(array('message' => $ajax_msg));
			exit();
		}
		
		switch ($action)
		{
			case 'testmail':
				
				extract($_POST);
				
				if (empty($mail_server) || empty($mail_port) || empty($mail_user) || empty($mail_pass) || empty($contact_email))
				{
					$error = true;
					$ajax_msg = 'لطفا تمام جزویات اجباری را پر نمایید.';
				}
				
				if ($error)
				{
					$ajax_msg = '<div class="alert alert-error">'. $ajax_msg .'</div>';
					echo json_encode(array('message' => $ajax_msg));
					exit();
				}
			
				require_once(ABSPATH .'include/class.phpmailer.php');
			
			
				$mail = new PHPMailer();
				$mail->SetLanguage("en", "include/");
			
				if ($mail_smtp == '1')
				{
					$mail->IsSMTP();
				}
			
				$mail->Subject = 'ایمیل آزمایشی از '. _SITENAME;
				$mail->Host 	= $mail_server;
				$mail->SMTPAuth = true;
				$mail->Port 	= $mail_port;
				$mail->Username = $mail_user;
				$mail->Password = $mail_pass;
				$mail->From 	= $contact_email;
				$mail->FromName = html_entity_decode(_SITENAME, ENT_QUOTES);
				$mail->CharSet = "UTF-8";
				$mail->AddAddress($contact_email);
				$mail->IsHTML(false);
			
				$mailcontent = "سلام!\n\n
				این یک ایمیل آزمایشی است که از پارسیان کلیپ قدرت گرفته ا از سوی سایت تان ارسال شده.\n
				اگر این ایمیل را دریافت کرده اید آسوده استراحت کنید. تنظیمات ایمیل شما تنظیم شده اند و پارسیان کلیپ می تواند ایمیل ها را ارسال بکند. \n آره!";
			
				$mail->Body = $mailcontent;
			
				if ( ! @$mail->Send())
				{
					$ajax_msg = '<div class="alert alert-error">'. $mail->ErrorInfo .'</div>';
				}
				else
				{
					$ajax_msg = '<div class="alert alert-success">ایمیل آزمایشی  با موفقیت تحویل داده شد به <strong>'. $contact_email .'</strong>. و برای تایید ایمیل وارد بخش اینباکس ایمیل تان شوید یا وارد بخش هرزنامه یا اسپم بشوید. فراموش نکنید که <strong>ذخیره کن</strong>تنظیمات را ، اگر همه چی درست بود.</div>';
				}
			
				echo json_encode(array('message' => $ajax_msg));
			
				exit();
				
			break;
		}
		
	break;
	
	case 'utilities':
		
		switch ($action)
		{
			case 'sanitize-title':
				
				if ($_POST['text'] != '')
				{
					$text = trim($_POST['text']);
					$text = sanitize_title($text);
					exit($text);
				}
				
				exit();
				
			break;
		}

	break;
	
	case 'readlog':
		
		if ( ! is_admin())
		{
			$ajax_msg = ($logged_in) ? 'ورود ممنوع!' : 'از دوباره وارد شوید.';
			$ajax_msg = '<div class="alert alert-error">'. $ajax_msg .'</div>';
			exit(json_encode(array('success' => false, 'msg' => $ajax_msg)));
		}
			
		switch ($action)
		{
			case 'mark-all-read':
				if ( ! csrfguard_check_referer('_admin_readlog'))
				{
					exit(json_encode(array('success' => false, 'msg' => '<div class="alert alert-error">رمز عبور اشتباه است یا سشن منقضی شده. لطفا صفحه را رفرش نمایید و از دوباره تلاش کنید.</div>')));
				}
				
				if (mysql_query("UPDATE pm_log SET msg_type = '0'"))
				{
					update_config('unread_system_messages', 0);
					exit(json_encode(array('success' => true, 'msg' => '')));
				}
				else
				{
					exit(json_encode(array('success' => false, 'msg' => '<div class="alert alert-error">در هنگام اجرای درخواست شما خطایی رخ داد.<br /><strong>گزارش mysql :</strong> '. mysql_error() .'</div>')));
				}
				
			break;
			
			case 'delete-all':
				
				if ( ! csrfguard_check_referer('_admin_readlog'))
				{
					exit(json_encode(array('success' => false, 'msg' => '<div class="alert alert-error">رمز عبور اشتباه است یا سشن منقضی شده. لطفا صفحه را رفرش نمایید و از دوباره تلاش کنید.</div>')));
				}
				
				if (mysql_query("TRUNCATE TABLE pm_log"))
				{
					exit(json_encode(array('success' => true, 'msg' => '')));
				}
				else
				{
					exit(json_encode(array('success' => false, 'msg' => '<div class="alert alert-error">در هنگام اجرای درخواست شما خطایی رخ داد.<br /><strong>گزارش mysql :</strong> '. mysql_error() .'</div>')));
				}
						
			break;
		}
		
	break;
	
	case 'searchlog':
		
		if ( ! is_admin())
		{
			$ajax_msg = ($logged_in) ? 'ورود ممنوع!' : 'لطفا وارد شوید.';
			$ajax_msg = '<div class="alert alert-error">'. $ajax_msg .'</div>';
			exit(json_encode(array('success' => false, 'msg' => $ajax_msg)));
		}
		
		switch ($action)
		{
			case 'delete-all':
				
				if ( ! csrfguard_check_referer('_admin_searchlog'))
				{
					exit(json_encode(array('success' => false, 'msg' => '<div class="alert alert-error">رمز عبور اشتباه است یا سشن منقضی شده. لطفا صفحه را رفرش نمایید و از دوباره تلاش کنید.</div>')));
				}
				
				if (mysql_query("TRUNCATE TABLE pm_searches"))
				{
					exit(json_encode(array('success' => true, 'msg' => '')));
				}
				else
				{
					exit(json_encode(array('success' => false, 'msg' => '<div class="alert alert-error">در هنگام اجرای درخواست شما خطایی رخ داد.<br /><strong>گزارش mysql :</strong> '. mysql_error() .'</div>')));
				}
				
			break;
		}
		
	break;
	
	default: 
		exit();
	break;
} // end switch ($page)

// always exit ajax requests
exit();
?>