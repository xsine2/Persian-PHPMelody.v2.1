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

// Session Cookie workaround
if (isset($_POST["PHPSESSID"])) {
	session_id($_POST["PHPSESSID"]);
} else if (isset($_GET["PHPSESSID"])) {
	session_id($_GET["PHPSESSID"]);
}

session_start();

require_once('../config.php');
require_once('../include/ffmpeg.php');
include_once('functions.php');
include_once( ABSPATH . 'include/user_functions.php');
include_once( ABSPATH . 'include/islogged.php');

$error_msg = '';
$allow = 1;

if ( ! $conn_id)
{
	if ( ! ($conn_id = db_connect()))
	{
		$allow = 0;
	}
}

$allowed_ext 	= array('.wmv','.mov','.qt','.3gp','.3gpp','.3g2','.3gp2','.mpg','.mpeg','.mp1',
                        '.mp2','.m1v','.m1a','.m2a','.mpa','.mpv','.mpv2','.mpe','.mp4','.m4a',
						'.m4p','.m4b','.m4r','.m4v','.avi','.flv','.f4v','.f4p','.f4a','.f4b',
						'.vob','.lsf','.lsx','.asf','.asr','.asx','.webm','.mkv');
$allowed_type 	= array('application/octet-stream');

$uploadDir 	= _VIDEOS_DIR_PATH;
$uploadFile = $uploadDir . basename($_FILES['Filedata']['name']);

$ext1 = explode('.', $_FILES['Filedata']['name']);
$ext2 = strtolower($ext1[ count($ext1)-1 ]);
$ext2 = '.'. $ext2;

if ( ! in_array($ext2, $allowed_ext) || ! in_array($_FILES['Filedata']['type'], $allowed_type))
{
	$uploadFile = str_replace($ext2, '.flv', $uploadFile);
	$allow = 0;
	
	if ( ! in_array($ext2, $allowed_ext))
	{
		$error_msg = 'Bad file type. You can upload only <code>'. implode(', ', $allowed_ext) .'</code> files.';
	}
	else
	{
		$error_msg = 'Bad file type. Please use the Flash Uploader.';
	}
}

if ( ! $logged_in || ( ! is_admin() && ! is_moderator() && ! is_editor()))
{
	$allow = 0;
	$error_msg = 'You do not have permission to upload videos.';
}

if (is_moderator() && mod_cannot('manage_videos'))
{
	$allow = 0;
	$error_msg = 'You do not have permission to manage and upload videos.';
}

if ( ! is_array($_FILES['Filedata']) || $_FILES['Filedata']['size'] == 0)
{
	$allow = 0;
	$error_msg = 'No file provided. File size: 0 bytes.';
}

$new_name  = md5($_FILES['Filedata']['name'].time());
$new_name  = substr($new_name, 0, 8);
$new_name .= $ext2;
$uploadFile = $uploadDir . $new_name;

if ($allow == 1)
{
	$move = @move_uploaded_file($_FILES['Filedata']['tmp_name'], $uploadFile);
	$CONVERT = new ffmpeg();
	$CONVERT->Convert($new_name);
	unlink($uploadFile);
	
	$FILENAME_EXE = substr(strrchr($new_name, "."), 1);
	$NN = 'MP4_'.str_replace('.'.$FILENAME_EXE,'',$new_name).'.mp4';
	
	if ($move !== false)
	{
		if ($_POST['doing'] == 'modify')
		{
			if ($_POST['uniq_id'] != '')
			{
				$sql = "SELECT url_flv 
						FROM pm_videos 
						WHERE uniq_id = '". secure_sql($_POST['uniq_id']) ."'";
				if ($result = mysql_query($sql))
				{
					$row = mysql_fetch_assoc($result);
					mysql_free_result($result);
					$sql = "UPDATE pm_videos 
							SET url_flv = '". $NN ."' 
							WHERE uniq_id = '". secure_sql($_POST['uniq_id']) ."'";
							
					mysql_query($sql);
					
					$removed = true;
					if ($row['url_flv'] != '' && file_exists(_VIDEOS_DIR_PATH . $row['url_flv']))
					{
						$removed = unlink(_VIDEOS_DIR_PATH . $row['url_flv']);
					}

					$html = '<span class="pull-right">';
					$html .= '<i class="icon-download opac7"></i> <strong><a href="'. _VIDEOS_DIR . $new_name .'" title="Download file">Download</a></strong>';
	            	$html .= '</span>';
					$html .= '<strong>'. $new_name .'</strong> <span class="label label-success">updated</span>';
					
					if ( ! $removed)
					{
						$html .= '<hr />';
						$html .= '<div class="alert alert-error">Could not remove <code>'. _VIDEOS_DIR_PATH . $row['url_flv'] .'</code> from your server.</div>';
					}

					exit($html);
				}
				else
				{
					$error_msg = 'Could not retrieve video data.';
				}
			}
			else
			{
				$error_msg = 'Missing video ID';
			}
		}
		else 
		{
			$uploadFile = $uploadDir . $NN;
			$uploadFile = str_replace("\\", "\\\\", $uploadFile);	// IIS path fix
			$result = update_config('last_video', $uploadFile);
			if (is_array($result))
			{
				$fp = @fopen('tmp.pm', "a");
				@fwrite($fp, $uploadFile);
				@fclose($fp);
			}
		}
	}
	else if ($move === FALSE)
	{
		$error_msg = 'Could not move uploaded file to <strong>'. _VIDEOS_DIR_PATH .'</strong>.';
	}	
}

if ($_FILES['Filedata']['error'] != 0)
{
	switch($_FILES['Filedata']['error'])
	{
		case UPLOAD_ERR_INI_SIZE:
			$error_msg = 'The uploaded file exceeds the upload_max_filesize directive in php.ini which is currently set at <strong>'. ini_get('upload_max_filesize') .'</strong>';
		break;
		
		case UPLOAD_ERR_FORM_SIZE:
			$error_msg = 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML/Flash upload form.';
		break;
		
		case UPLOAD_ERR_PARTIAL:
			$error_msg = 'The uploaded file was only partially uploaded. Possible cause: user cancelled the upload.';
		break;
	
		case UPLOAD_ERR_NO_FILE:
			$error_msg = 'No file was uploaded. Please select a file first.';
		break;
		
		case UPLOAD_ERR_NO_TMP_DIR:
			$error_msg = 'Missing a temporary folder. Please contact your hosting provider for this issue.';
		break;
		
		case UPLOAD_ERR_CANT_WRITE:
			$error_msg = 'Failed to write file to disk. Please contact your hosting provider for this issue.';
		break;
		
		case UPLOAD_ERR_EXTENSION:
			$error_msg = 'File upload stopped by extension. A PHP extension stopped the file upload. Can\'t tell which extension caused the file upload to stop.';
		break;
		
		default:
			$error_msg = 'Unknown upload error.';
		break;
	}
}

if ($error_msg != '')
{
	$log_msg = 'Failed to upload file <code>'. $_FILES['Filedata']['name'] .'</code>. Error issued:<br /> ';
	$log_msg .= '<i>'. $error_msg .'</i>';
	
	if (strpos($error_msg, "0 bytes") !== false)
	{
		$log_msg .= '<br />To upload files larger than <strong>'. readable_filesize(get_true_max_filesize()) .'</strong>, 
						you need to increase your server\'s <strong>upload_max_filesize</strong> and <strong>upload_max_filesize</strong> limits.';
		$log_msg .=  '<br />You can do it yourself by reading <a href="http://help.phpmelody.com/how-to-fix-the-video-uploading-process/" target="_blank">this how-to</a>, or by contacting your hosting provider.';
		$log_msg .=  '<br />Meanwhile you can upload the video(s) with an FTP client into the <strong>/uploads/videos/</strong> folder and add them to your site using the "<a href="addvideo.php">Add Video from URL</a>" page.';
	}

	log_error($log_msg, 'Upload video', 1);
}
if ($_POST['doing'] == 'modify')
{
	echo $error_msg;
}
exit();
?>