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

$showm = '6';
$load_scrolltofixed = 1;
$_page_title = 'ویرایش کردن پروفایل کاربر';
include('header.php');

$modframework->trigger_hook('admin_edituser_top');
$error = '';
$the_user = array();
$user_id = '';
$errors = array();

$user_id = (int) $_GET['uid'];

$action = (int) $_GET['action'];

if( empty($user_id) || $user_id === '' || !is_numeric($user_id) )
{
	$error = "شناسه کاربر در آدرس شما معتبر نیست.";
}
else
{
	$the_user = fetch_user_advanced($user_id);
	
	$the_user['comment_count'] = count_entries('pm_comments', 'user_id', $the_user['id']);
	/*
    * Security fix to prevent moderators changing admin passwords (by Trace)
    */
   if($the_user['power'] == U_ADMIN && is_moderator()){
      $error = "شما نمی توانید ویرایش کنید این کاربر را چون که او رتبه بالاتری از شما دارد.";
      $the_user = null; $user_id = 0; unset($_POST['save']);
   }
}

if($action == 1 && is_array($the_user))	//	activate user account
{
	if($the_user['power'] == U_INACTIVE)
	{
		$sql = "UPDATE pm_users SET power = '".U_ACTIVE."' WHERE id='".$user_id."'";
		$result = @mysql_query($sql);
		if(!$result)
		{
			$info_msg = '<div class="alert alert-error">فعال کردن از دست رفت.<br />mysql بر می گرداند: '.mysql_error().'</div>';
		}
		else
		{
			$the_user['power'] = U_ACTIVE;
			
			if ($config['account_activation'] == AA_ADMIN)
			{
				require_once(ABSPATH ."include/class.phpmailer.php");
				
					//*** DEFINING E-MAIL VARS
					$mailsubject = sprintf($lang['mailer_subj7'], _SITENAME);
					
					$array_content[]=array("mail_username", $the_user['username']); 
					$array_content[]=array("mail_sitename", _SITENAME);
					$array_content[]=array("mail_loginurl", _URL.'/login.'. _FEXT);
					$array_content[]=array("mail_url", _URL);
					//*** END DEFINING E-MAIL VARS
				
				if(file_exists(ABSPATH .'email_template/'.$_language_email_dir.'/email_registration_approved.txt'))
				{
					$mail = send_a_mail($array_content, $the_user['email'], $mailsubject, ABSPATH .'email_template/'.$_language_email_dir.'/email_registration_approved.txt');
				}
				elseif(file_exists(ABSPATH .'/email_template/english/email_registration_approved.txt'))
				{
					$mail = send_a_mail($array_content, $the_user['email'], $mailsubject, ABSPATH .'email_template/english/email_registration_approved.txt');
				}
				elseif(file_exists(ABSPATH .'/email_template/email_registration_approved.txt'))
				{
					$mail = send_a_mail($array_content, $the_user['email'], $mailsubject, ABSPATH .'email_template/email_registration_approved.txt');
				}
				else
				{
					@log_error('Error: Email template "email_registration_approved.txt" not found!', 'Register Page', 1);
				}
				
				if($mail !== TRUE)
				{
					@log_error($mail, 'Register Page', 1);
					$info_msg = '<div class="alert">حساب فعال شده هر چند که ، ایمیل فعال سازی ارسال نشده.';
					$info_msg .= '<br />پیام خطا: <em>'. $mail .'</em>';
					$info_msg .= '</div>';
				}
				else
				{
					$info_msg = '<div class="alert alert-success">این حساب الان فعال است. یک ایمیل فعال سازی ارسال شده به <em>'. $the_user['email'] .'</em></div>';
				}
			}
			else
			{
				$info_msg = '<div class="alert alert-success">این حساب الان فعال است..</div>';
			}
		}
	}
	else
	{
		$info_msg = '<div class="alert alert-success">این حساب قبلا فعال شده.</div>';
	}
}
else if ($action == 9 && is_array($the_user))	//	delete all comments posted by this user
{
	if (is_moderator() && mod_cannot('manage_comments'))
	{
		echo '<div id="adminPrimary">';
		restricted_access();
		echo '</div>';
	}
	
	$sql = "DELETE FROM 
			pm_comments 
			WHERE user_id = '". $the_user['id'] ."'";
	
	$result = @mysql_query($sql);
	if ( ! $result)
	{
		$error = 'در هنگام تلاش برای حذف نظرات کاربران خزایی رخ داد.<br />mysql برمی گرداند: '.mysql_error();
	}
	else
	{
		@mysql_query("DELETE FROM pm_comments_reported WHERE user_id = '". $the_user['id'] ."'");
		$info_msg = '<div class="alert alert-success">تمام نظراتی که توسط این کاربر  ارسال شده بود حذف شد.</div>';
		$the_user['comment_count'] = count_entries('pm_comments', 'user_id', $user_id);
	}
}

if( isset($_POST['save']))
{

	$no_errors = 0;
	
	$post_username = trim($_POST['username']);
	$post_username = sanitize_user($post_username);

	if( check_username($post_username) == 3 && $post_username != $the_user['username'] )
	{
		$error = "Username is already in use";
		$no_errors++;
	}

	if( validate_email($_POST['email']) == 2 && $_POST['email'] != $the_user['email'] )
	{
		$error = "Email is already in use";
		$no_errors++;
	}

	if( $_POST['delete_avatar'] == 1 && $the_user['avatar'] != "default.gif" )
	{
		// delete avatar;
		if( unlink(ABSPATH."uploads/avatars/".$the_user['avatar']) === FALSE )
			$error = "آواتار کاربر حذف نشد.";
	}
	$modframework->trigger_hook('admin_edituser_validate');
	
	if( $no_errors == 0 )
	{	
		$sql = "UPDATE pm_users SET ";
		
		if( $_POST['new_pass'] != '' )
			$sql .= " password = '".md5($_POST['new_pass'])."', ";
		
		if( $_POST['delete_avatar'] == 1 )
			$sql .= " avatar = 'default.gif', ";
		
		$sql .= " username = '".secure_sql($post_username)."', name = '".secure_sql($_POST['name'])."', ";
		$sql .= " gender = '".$_POST['gender']."', country = '".$_POST['country']."', email = '".$_POST['email']."', ";
		$sql .= " about = '".secure_sql($_POST['aboutme'])."', favorite = '".$_POST['favorite']."', ";
		$sql .= " website = '". secure_sql($_POST['website']) ."', facebook = '". secure_sql($_POST['facebook']) ."', ";
		$sql .= " twitter = '". secure_sql($_POST['twitter']) ."', lastfm = '". secure_sql($_POST['lastfm']) ."' "; 
	
		if (is_admin() && isset($_POST['user_power']))
		{
			$sql .= ", power = '".$_POST['user_power']."'";
		}
		$modframework->trigger_hook('admin_edituser_sqlinsert');
		
		$sql .= " WHERE id = ".$the_user['id']."";

		$result = @mysql_query($sql);
		if( !$result )
			$error = "در هنگام بروزرسانی این کاربر خطایی رخ داده. <br /> mysql این خطا را برمی گرداند: ".mysql_error();
		$modframework->trigger_hook('admin_edituser_done');
			
		// Was the username changed? Update the pm_comments table with the new username too;
		if ($post_username != $the_user['username'])
		{
			$all_ids = '';

			// update pm_comments
			$sql = "SELECT id FROM pm_comments WHERE username = '".$the_user['username']."' AND user_id='".$the_user['id']."'";
			$result = mysql_query($sql);
			$total = mysql_num_rows($result);
			
			if($total > 0)
			{
				while($row = mysql_fetch_assoc($result))
				{
					$all_ids .= $row['id'] . ", ";
				}
				$all_ids = substr($all_ids, 0, -2);
				
				mysql_free_result($result);
				
				$sql = "UPDATE pm_comments SET username = '". secure_sql($post_username) ."' WHERE id IN(".$all_ids.")";
				$result = @mysql_query($sql);
			}
			
			unset($all_ids, $total, $result);
			$all_ids = '';
			
			// update pm_videos			
			$sql = "SELECT id FROM pm_videos WHERE submitted = '".$the_user['username']."'";
			$result = mysql_query($sql);
			$total = mysql_num_rows($result);
			
			if($total > 0)
			{
				while($row = mysql_fetch_assoc($result))
				{
					$all_ids .= $row['id'] . ", ";
				}
				
				$all_ids = substr($all_ids, 0, -2);
				
				mysql_free_result($result);
				
				$sql = "UPDATE pm_videos SET submitted = '". secure_sql($post_username) ."' WHERE id IN(".$all_ids.")";
				$result = @mysql_query($sql);
			}
		}
	}
}

load_countries_list();

?>
<div id="adminPrimary">
    <div class="content">
<h2>ویرایش کردن پروفایل کاربر <a href="<?php echo _URL.'/profile.php?u='. $the_user['username'];?>" title="View public profile" target="_blank"><?php echo ucfirst($the_user['username']); ?></a></h2>

<?php echo $info_msg; ?>
	<?php

		if( !isset($_POST['save']) && $error != '' )
			echo "<div class=\"alert alert-error\">".$error."</div>";
			
		else {
			if( isset($_POST['save']) && $no_errors > 0 )
				echo "<div class=\"alert alert-error\">".$error."</div>";
			elseif( isset($_POST['save']) && $no_errors == 0 )
			{
				echo "<div class=\"alert alert-success\">حساب با موفقیت بروزرسانی شد.</div>";
				echo '<a href="members.php" class="btn">&larr; Users</a>';
			}
			else
			{
	
	// check if banned.
	$sql = "SELECT COUNT(*) AS total, reason FROM pm_banlist WHERE user_id = '". $the_user['id'] ."'";
	if ($result = @mysql_query($sql))
	{
		$ban = mysql_fetch_assoc($result);
		mysql_free_result($result);
	}
	
	?>
	
	<form name="edit_profile_form" method="POST" action="<?php echo "edit_user_profile.php?uid=".$user_id; ?>" class="form-horizontal">
<!--	<table width="60%" border="0" cellspacing="1" cellpadding="3" align="center" style="text-align:center">-->
<?php
if ($ban['total'] > 0)
{
	$banlist_nonce = csrfguard_raw('_admin_banlist');
	?>
	<div class="alert alert-error">
		این حساب محرم شده.
		<?php if ($ban['reason'] != '') : ?>
		<strong>دلیل:</strong> <?php echo $ban['reason'];?>
		<?php endif; ?>
        <strong><a href="banlist.php?a=delete&uid=<?php echo $the_user['id'];?>&_pmnonce=<?php echo $banlist_nonce['_pmnonce'];?>&_pmnonce_t=<?php echo $banlist_nonce['_pmnonce_t'];?>">حذف از حالت بن شدگی</a></strong>
	</div>
	<?php
}
if ($the_user['power'] == U_INACTIVE && $action != 1)
{
	$members_nonce = csrfguard_raw('_admin_members');
	?>
	<div class="alert alert-warning">
		این حساب فعال نشده.  
        <strong><a href="edit_user_profile.php?uid=<?php echo $the_user['id'];?>&action=1&_pmnonce=<?php echo $members_nonce['_pmnonce'];?>&_pmnonce_t=<?php echo $members_nonce['_pmnonce_t'];?>" title="Activate account">اکنون فعال کن</a></strong>
	</div>
	<?php
}
?>
<style>
label input {
  line-height: 1em;
  padding: 0;
  margin: 0;
  margin-left: 4px;
  line-height: 0;
  top: -3px;
  position: relative;
  font-weight: normal;
}
</style>

<div class="row-fluid">
<div class="span8">
<table width="100%" border="0" cellpadding="0" cellspacing="0" class="table table-striped table-bordered pm-tables pm-tables-settings">
  <tr>
    <td width="15%">آواتار</td>
    <td width="85%">
      <span class="avatar_border"><img src="<?php echo _URL."/uploads/avatars/".$the_user['avatar']; ?>" border="0" alt="" class="img-polaroid" /></span>
      <?php if ($the_user['avatar'] != '' && $the_user['avatar'] != 'default.gif') : ?>
	  <label><input type="checkbox" class="checkbox" name="delete_avatar" value="1" /> این آواتار را حذف می کنید؟</label>
	  <?php endif; ?>
    </td>
  </tr>
  <tr>
    <td>نام</td>
    <td><input name="name" type="text" value="<?php echo $the_user['name']; ?>" /></td>
  </tr>
  <tr>
    <td>نامکاربری</td>
    <td><input type="text" name="username" value="<?php echo $the_user['username']; ?>" /></td>
  </tr>
  <tr>
    <td>رمز عبور جدید</td>
    <td>
      <input name="new_pass" type="password" maxlength="32" />
      <div class="help-block"><small>اگر نمی خواهید رمز عبور را تغییر بدهید  خالی بگذار</small></div>    
    </td>
  </tr>
  <tr>
    <td>ایمیل</td>
    <td><input type="text" name="email" value="<?php echo $the_user['email']; ?>" /></td>
  </tr>
  <tr>
    <td>گروه کاربری</td>
    <td>
      <select name="user_power">
        <?php
      
      if( $the_user['power'] == U_INACTIVE)
      {
          ?>
        <option value="<?php echo U_INACTIVE; ?>" <?php if($the_user['power'] == U_INACTIVE) echo 'selected="selected"';?>>کاربر غیرفعال</option>';
          <?php
      }
      
      if (is_admin())
      {
          ?>
        
        <option value="<?php echo U_ACTIVE;?>"  <?php if($the_user['power'] == U_ACTIVE) echo 'selected="selected"';?> >کاربر منظم</option>
        <option value="<?php echo U_EDITOR;?>"  <?php if($the_user['power'] == U_EDITOR) echo 'selected="selected"';?> >معمولی</option>
        <option value="<?php echo U_MODERATOR;?>"  <?php if($the_user['power'] == U_MODERATOR) echo 'selected="selected"';?> >مدیر</option>
        <option value="<?php echo U_ADMIN;?>"  <?php if($the_user['power'] == U_ADMIN) echo 'selected="selected"';?> >مدیر ارشد</option>
        
        <?php
      }
      else 
      {
          ?>
        
        <option value="<?php echo $the_user['power'];?>"  selected="selected">
          <?php
              switch ($the_user['power'])
              {
                  default:
                  case U_ACTIVE: 		echo 'Regular User';	break;
                  case U_EDITOR: 		echo 'Editor'; 			break;
                  case U_MODERATOR:	echo 'Moderator'; 		break;
                  case U_ADMIN:		echo 'Administrator';	break;
              } 
              ?>
          </option>
        
        <?php
      }
      ?>
        </select>
    </td>
  </tr>
  <tr>
    <td>ویدئوهای مورد علاقه</td>
    <td>
      <label><input name="favorite" type="radio" value="1" <?php if($the_user['favorite'] == 1) echo "checked"; ?> /> عمومی</label>
      <label><input name="favorite" type="radio" value="0" <?php if($the_user['favorite'] == 0) echo "checked"; ?> /> خصوصی</label>
    </td>
  </tr>
  <tr>
    <td>جنسیت</td>
    <td>
      <label><input name="gender" type="radio" value="male" <?php if($the_user['gender'] == "male") echo "checked"; ?> /> مرد</label>
      <label><input name="gender" type="radio" value="female" <?php if($the_user['gender'] == "female") echo "checked"; ?> /> زن</label>
    </td>
  </tr>
  <tr>
    <td>کشور</td>
    <td>
      <select name="country" size="1" >
        <option value="-1">یکی را انتخاب کن</option>
        <?php
                $opt = '';
                foreach($_countries_list as $k => $v)
                {
                    $opt = "<option value=\"".$k."\"";
                    if( $the_user['country'] == $k )
                        $opt .= " selected ";
                    $opt .= ">".$v."</option>";
                    echo $opt;
                }
                ?>
        </select>
    </td>
  </tr>
  <tr>
    <td>درباره</td>
    <td>
      <textarea name="aboutme" rows="4"><?php echo $the_user['about']; ?></textarea>
    </td>
  </tr>
  <tr>
    <td>آدرس سایت</td>
    <td>
      <input type="text" name="website" size="45" value="<?php echo $the_user['website']; ?>" />
    </td>
  </tr>
  <tr>
    <td>آدرس فیس بوک</td>
    <td>
      <input type="text" name="facebook" size="45" value="<?php echo $the_user['facebook']; ?>" />
    </td>
  </tr>
  <tr>
    <td>آدرس تویتر</td>
    <td>
      <input type="text" name="twitter" size="45" value="<?php echo $the_user['twitter']; ?>" />
    </td>
  </tr>
  <tr>
    <td>Last.fm URL</td>
    <td>
      <input type="text" name="lastfm" size="45" value="<?php echo $the_user['lastfm']; ?>" />
    </td>
  </tr>
    <?php 
  		$modframework->trigger_hook('admin_edituser_fieldsinject');
  	?>
  </table>
    <div class="clearfix"></div>

    <div id="stack-controls" class="list-controls">
    <div class="btn-toolbar">
        <div class="btn-group">
    	<button type="submit" name="save" value="Save" class="btn btn-small btn-success btn-strong">ذخیره کردن</button>
	    </div>
    </div>
    </div><!-- #list-controls -->
</div>
<div class="span4">  
    <table width="100%" border="0" cellpadding="0" cellspacing="0" class="table table-striped table-bordered pm-tables pm-tables-settings">
      <tr>
        <td>نظرات</td>
        <td>
        	<?php echo $the_user['comment_count'];?> نظر<?php echo ($the_user['comment_count'] == 1) ? '' : 's';?> 
            <?php if ($the_user['comment_count'] > 0) : ?>
				<a href="comments.php?keywords=<?php echo urlencode($the_user['username']);?>&search_type=username&submit=Search" class="btn btn-small">خواندن همه</a>
	            <?php if (is_admin() || (is_moderator() && mod_can('manage_comments'))) : ?>
	            <a href="edit_user_profile.php?action=9&uid=<?php echo $user_id;?>" onclick="return confirm_delete_all();" class="btn btn-small">حذف کردن همه</a>
	            <?php endif; ?>
			<?php endif; ?>
        </td>
      </tr>
      <tr>
        <td>تاریخ ثبت شده</td>
        <td><?php echo date('l, F j, Y g:i A', (int) $the_user['reg_date']);?></td>
      </tr>
	  <tr>
        <td>آیپی ثبت شده</td>
        <td><?php echo '<a href="'. _URL .'/admin/members.php?keywords='. $the_user['reg_ip'] .'&search_type=ip&submit=Search" title="Search users by this IP">'. $the_user['reg_ip'] .'</a>';?></td>
      </tr>
      <tr>
        <td>آخرین ورود</td>
        <td><?php echo date('l, F j, Y g:i A', (int) $the_user['last_signin']);?></td>
      </tr>
	  <tr>
        <td>آخرین آیپی وارد شده</td>
        <td><?php echo ($the_user['last_signin_ip'] != '') ? '<a href="'. _URL .'/admin/members.php?keywords='. $the_user['last_signin_ip'] .'&search_type=ip&submit=Search" title="Search users by this IP">'. $the_user['last_signin_ip'] .'</a>' : 'No IP yet';?></td>
      </tr>
      <tr>
        <td>دنبال کننده</td>
        <td><?php echo $the_user['followers_count'];?></td>
      </tr>
      <tr>
        <td>دنبال شونده</td>
        <td><?php echo $the_user['following_count'];?></td>
      </tr>
    </table>
</div>
</div><!-- .row-fluid -->
</form>


	<?php
			}
	 	} // end else
	?>
    </div><!-- .content -->
</div><!-- .primary -->
<?php
include('footer.php');
?>