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
session_start();
require('config.php');
require_once('include/functions.php');
require_once('include/user_functions.php');
require_once('include/islogged.php');

$whitelist	  = array('.jpg', '.gif', '.png', '.jpeg');
$allowed_type = array('image/png', 'image/gif', 'image/jpg', 'image/jpeg', 'image/pjpeg');

// define meta tags & common variables
$meta_title	= sprintf($lang['upload_avatar'], $userdata['name']);
$meta_description = '';
// end

if ($logged_in)
{
	$avatar	= _URL ."/". _UPFOLDER ."/avatars/". $userdata['avatar'];

	$avatar_upload_dir 	= _UPFOLDER ."/avatars/"; 

	if (isset($_POST['submit']))
	{ 
		$temp		 = explode(".", $_FILES['imagefile']['name']);
		$file_ext	 = '.'. strtolower($temp[count($temp) - 1]);
		if (strlen($file_ext) > 4)
		{
			$file_ext = '';
		}

		if (in_array($_FILES['imagefile']['type'], $allowed_type) && in_array($file_ext, $whitelist))
		{
			$rand		 = rand(343, 1000);
			$new_name	 = substr(md5($_FILES['imagefile']['name']), 1, 8) . $file_ext;
			$avatar_name = 'avatar'. $rand .'-'. $userdata['id'] . $file_ext;
		
			$copy = @copy($_FILES['imagefile']['tmp_name'], $avatar_upload_dir . $new_name); 
			if ($copy === TRUE)
			{
				$resize = resize_then_crop($avatar_upload_dir. $new_name, $avatar_upload_dir. $avatar_name, THUMB_W_AVATAR, THUMB_H_AVATAR, "255", "255", "255", $allowed_type);
				if($resize != false)
				{
					if ($userdata['avatar'] != 'default.gif' && $userdata['avatar'] != 'no_avatar.gif')
					{ 
						@unlink($avatar_upload_dir. $userdata['avatar']);
					}
					
					@mysql_query("UPDATE pm_users SET avatar = '". $avatar_name ."' WHERE username = '".$userdata['username']."'");
					
					if (_MOD_SOCIAL)
					{
						//ACT_TYPE_UPDATE_AVATAR
						log_activity(array(
								'user_id' => $userdata['id'],
								'activity_type' => ACT_TYPE_UPDATE_AVATAR,
								'object_id' => $userdata['id'],
								'object_type' => ACT_OBJ_PROFILE
								)
							);
					}
					
					$success = $lang['ua_msg3'];   // Resize successful
					
					$userdata['avatar_url'] = str_replace($userdata['avatar'], $avatar_name, $userdata['avatar_url']);
					$userdata['avatar'] = $avatar_name;
					$smarty->clear_assign('s_avatar_url');
					$smarty->assign('s_avatar_url', $userdata['avatar_url']);
				}
				else
				{
					$err = $lang['ua_msg4'];	// Error: Upload Failed
				}
				
				$ttemp = $avatar_upload_dir . $new_name;
				@unlink($ttemp); // delete temp image				
 			}
			else 
			{
				$err = $lang['ua_msg4'];	// Error: Upload Failed
			} 
		}
  		else
  		{
    		$err = $lang['ua_msg5'];	// Error: Filetype Is Wrong 
		}
  
		$smarty->assign('success_msg', $success);
		$smarty->assign('err_msg', $err);
		$smarty->assign('avatar', $avatar);
		$smarty->assign('username', $username);
		
		// --- DEFAULT SYSTEM FILES - DO NOT REMOVE --- //
		$smarty->assign('meta_title', $meta_title);
		$smarty->assign('meta_description', $meta_description);
		$smarty->assign('template_dir', $template_f);
		$smarty->display('profile-upload-avatar.tpl');
	}
	else
	{
		$smarty->assign('avatar', $avatar);
		$smarty->assign('username', $username);
	
		// --- DEFAULT SYSTEM FILES - DO NOT REMOVE --- //
		$smarty->assign('meta_title', $meta_title);
		$smarty->assign('meta_description', $meta_description);
		$smarty->assign('template_dir', $template_f);
		$smarty->display('profile-upload-avatar.tpl');
	}
}
else {
	header('Location: '. _URL .'/login.php');
	exit();
}
?>