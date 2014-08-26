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

//$showm = '8';
$_page_title = 'Update password';
include('header.php');

if(!empty($_POST['submit']) && $_POST['submit'] == 'Update password') 
{
	$username = $userdata['username'];
	$query = mysql_query("SELECT * FROM pm_users where username = '".$username."' AND power = '".U_ADMIN."'");
	$rows = mysql_num_rows($query);
	$r = mysql_fetch_array($query);
	
	// ** IF USER DOESN'T EXIST REDIRECT TO HOMEPAGE ** //
	if($rows == 0) {
		header("Location: "._URL.""); 
		exit;
	}
	
	if(isset($_POST['submit']) && $_POST['submit'] == "Update password" ) 
	{
		$current_pass = md5(trim($_POST['pass']));
		$sql_pass = $r['password'];
		
		if($current_pass == $sql_pass) 
		{
			$new_pass = trim($_POST['new_pass']);
			if(!empty($new_pass)) 
			{
				$new_pass = md5($new_pass);
				@mysql_query("UPDATE pm_users SET password = '".$new_pass."' WHERE username = '".$r['username']."' AND power = '1'");
				$info_msg = '<div class="alert alert-success">The admin password was updated. You will be asked shortly to log in with the new password.</div>';
			} 
			else 
			{
				$info_msg = '<div class="alert alert-error">Your new password must be at least 5 characters long. Safety before anything else.</div>';
			}
		} 
		elseif($current_pass != $sql_pass) 
		{
			$info_msg = '<div class="alert alert-error">Try again! This is not your current password.</div>';
		}
	}	
} 
else 
{
	$info_msg = '<div class="alert alert-info">Use a strong and memorable password. </div>';
}
?>
<div id="adminPrimary">
    <div class="content">
	<h2>Update Password</h2>

	<?php echo $info_msg; ?>
    <table width="100%" border="0" cellspacing="2" cellpadding="5">
      <tr>
        <td>
        <form name="register-form" method="post" action="password.php" class="form-horizontal">
        <div class="control-group">
        <label class="control-label">Current Password</label>
        <div class="controls">
        <input name="pass" type="password" maxlength="32" />
        </div>
        </div>
        
        <div class="control-group">
        <label class="control-label">New Password</label>
        <div class="controls">
        <input name="new_pass" type="password" maxlength="32" />
        </div>
        </div>
        <div class="control-group">
        <div class="controls">
        <input type="submit" name="submit" value="Update password" class="btn btn-success" />
        </div>
        </div>
        </form>
        </td>
        </tr>
    </table>
    </div><!-- .content -->
</div><!-- .primary -->
<?php
include('footer.php');
?>