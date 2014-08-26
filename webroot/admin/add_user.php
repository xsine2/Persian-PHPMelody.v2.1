<?php
$showm = '6';
$load_scrolltofixed = 1;
$_page_title = 'افزودن کاربر جدید';
include('header.php');

$inputs = array();

load_countries_list();

$errors = array();
if ($_POST['Submit'] != '')
{
	$required_fields = array('email' => 'آدرس ایمیل',
							 'username' => 'نام کاربری', 
							 'pass' => 'رمزعبور', 
							 'confirm_pass' => 'تکرار رمزعبور', 
							 'name' => 'نام شما'
							 );
	foreach ($_POST as $key => $value) 
	{
		$value = trim($value);
		if(array_key_exists(strtolower($key), $required_fields) && empty($value) )
		{
			$errors[$key] = '<em>'. $required_fields[$key]. '</em> یک فیلد ضروری می باشد.';
		}
	}
	
	if ($_POST['country'] == '-1' || $_POST['country'] == '')
	{
		$errors['country'] = 'لطفا نام کشور خود را انتخاب کنید .';
	}
	
	foreach($_POST as $key => $val)
	{
		$val = trim($val);
		$val = specialchars($val, 1);
		$inputs[$key] = $val;
	}
	
	// password, email & username validation
	if (count($errors) == 0)
	{
		$email = trim($_POST['email']);
		$username =	trim($_POST['username']);
		$username = sanitize_user($username, 0); // Since v2.0
		$pass =	$_POST['pass'];
		$conf_pass = $_POST['confirm_pass'];
		
		if (strcmp($pass, $conf_pass) != 0) 
		{ 
			$errors['pass'] = 'رمز عبور و تایید رمز عبور با هم یکی نیستند.';
		}
		
		if ($var = validate_email($email)) 
		{
			if ($var == 1) 
			{
				$errors['email'] = 'آدرس ایمیل معتبر نیست.';
			}
			
			if ($var == 2)
			{
				$sql = "SELECT username FROM pm_users WHERE email LIKE '". str_replace("\'", "''", $email) ."'";
				$result = mysql_query($sql);
				$u = mysql_fetch_assoc($result);
				mysql_free_result($result);
				
				$errors['email'] = 'این آدرس ایمیل استفاده می شود توسط <a href="'. _URL."/profile."._FEXT.'?u='.$u['username'] .'"  target="_blank">'. $u['username'] .'</a>.';
			}
		}
		
		if ($var = check_username($username)) 
		{ 
			if ($var == 1)
			{
				$errors['username'] = 'نام کاربری حداقل باید 4 حرف باشد.';
			}
			
			if ($var == 2)
			{
				$errors['username'] = 'نام کاربری شامل کاراکترهای غیرمعتبر است. آن فقط باید شامل حروف و اعداد باشد. شما می توانید نام های کاربری "غیر لاتین" را فعال بکنید از <strong>تنظیمات</strong> > <strong>تنظیمات کاربر</strong>.';
			}
			
			if ($var == 3)
			{
				$errors['username'] = 'این نام کاربری گرفته شده. نماش <a href="'. _URL."/profile."._FEXT.'?u='.$username .'" target="_blank">پروفایل</a>.';
			}
		}
	}
	
	if (count($errors) == 0)
	{
		$aboutme = removeEvilTags($_POST['aboutme']);
		$aboutme = word_wrap_pass($aboutme);
		$aboutme = secure_sql($aboutme);
		$aboutme = specialchars($aboutme, 1);
		$aboutme = str_replace('\n', "<br />", $aboutme);
		
		$sql = "INSERT INTO pm_users (username, password, email, name, gender, country, reg_date, last_signin, reg_ip, favorite, power, about, website, facebook, twitter)
				VALUES ('". secure_sql($username) ."', 
						'". md5($pass) ."', 
						'". $email ."', 
						'". secure_sql( trim($_POST['name']) ) ."', 
						'". secure_sql($_POST['gender']) ."', 
						'". secure_sql($_POST['country']) ."', 
						'". time() ."', 
						'". time() ."', 
						'127.0.0.1', 
						'". secure_sql($_POST['favorite']) ."',
						'". secure_sql($_POST['power']) ."',
						'". $aboutme ."',
						'". secure_sql($_POST['website']) ."',
						'". secure_sql($_POST['facebook']) ."',
						'". secure_sql($_POST['twitter']) ."')";
		if ( ! $result = mysql_query($sql))
		{
			$errors[] = 'در هنگام اضافه کردن این کاربر خطای دیتابیس رخ داده.'. mysql_error(); 
		}
		else
		{
			$user_id = mysql_insert_id();
			$success = 'اکانت کاربر ایجاد شد.<a href="'. _URL .'/admin/edit_user_profile.php?uid='. $user_id .'">ویرایش</a> or <a href="'. _URL."/profile."._FEXT.'?u='.$username .'">نمایش پروفایل</a>.'; 
		}
	}
	else
	{
		
	}
}
?>
<div id="adminPrimary">
    <div class="content">
<h2>اضافه کردن کاربر جدید</h2>

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
	
	<?php if ($success != '') : ?>
		<div class="alert alert-success"><?php echo $success;?></div>
		<hr />
		<a href="members.php" class="btn">&larr; Users</a> 
		<a href="add_user.php" class="btn">اضافه کردن کاربر دیگر</a>
	
	<?php else: ?>
	
		<?php if (count($errors) > 0) : ?>
		<div class="alert alert-danger">
			<ul>
			<?php foreach ($errors as $k => $error) : ?>
				<li><?php echo $error;?></li>
			<?php endforeach; ?>
			</ul>
		</div>
		<?php endif;?>
	<form name="edit_profile_form" method="POST" action="add_user.php" class="form-horizontal" onsubmit="return validateFormOnSubmit(this, 'لطفا فیلد های اجباری را پر نمایید (برجسته شده) ')">	

<table width="100%" border="0" cellpadding="0" cellspacing="0" class="table table-striped table-bordered pm-tables pm-tables-settings">
  <tr>
    <td width="15%">نام</td>
    <td width="85%"><input name="name" type="text" id="must" value="<?php echo $inputs['name']; ?>" /></td>
    </tr>
  <tr>
    <td>نام کاربری</td>
    <td><input type="text" name="username" id="must" value="<?php echo $inputs['username']; ?>" /></td>
    </tr>
  <tr>
    <td>رمز عبور</td>
    <td><input name="pass" type="password" id="must" value="<?php echo $inputs['pass'];?>" maxlength="32" /></td>
    </tr>
  <tr>
    <td>تکرار رمز عبور</td>
    <td><input name="confirm_pass" type="password" id="must" value="<?php echo $inputs['confirm_pass'];?>" maxlength="32" /></td>
    </tr>
  <tr>
    <td>ایمیل</td>
    <td><input type="text" name="email" id="must" value="<?php echo $inputs['email']; ?>" /></td>
    </tr>
  <tr>
    <td>گروه کاربر</td>
    <td>
    <select name="power">
		<option value="<?php echo U_ACTIVE;?>"  <?php if($inputs['power'] == U_ACTIVE) echo 'selected="selected"';?> >کاربر عادی</option>
	<?php if(is_admin()) : ?>
		<option value="<?php echo U_EDITOR;?>"  <?php if($inputs['power'] == U_EDITOR) echo 'selected="selected"';?> >سردبیر</option>
		<option value="<?php echo U_MODERATOR;?>"  <?php if($inputs['power'] == U_MODERATOR) echo 'selected="selected"';?> >مدیر</option>
		<option value="<?php echo U_ADMIN;?>"  <?php if($inputs['power'] == U_ADMIN) echo 'selected="selected"';?> >مدیر ارشد</option>
	<?php endif; ?>
    </select>
    </td>
    </tr>
  <tr>
    <td>ویدئوهای مورد علاقه</td>
    <td>
        <label><input name="favorite" type="radio" value="1" <?php if($inputs['favorite'] == 1 || ! isset($inputs['favorite'])) echo "checked"; ?> /> عمومی</label>
        <label><input name="favorite" type="radio" value="0" <?php if($inputs['favorite'] == 0 && isset($inputs['favorite'])) echo "checked"; ?> /> ویرایشگر</label>
    </td>
    </tr>
  <tr>
    <td>جنسیت</td>
    <td>
        <label><input name="gender" type="radio" value="male" <?php if($inputs['gender'] == "male" || $inputs['gender'] == '') echo "checked"; ?> />مرد </label>
        <label><input name="gender" type="radio" value="female" <?php if($inputs['gender'] == "female") echo "checked"; ?> />زن </label>
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
                    if( $inputs['country'] == $k )
                        $opt .= " selected ";
                    $opt .= ">".$v."</option>";
                    echo $opt;
                }
                ?>
        </select>
    </td>
    </tr>
  <tr>
    <td>درباره من</td>
    <td>
      <textarea name="aboutme" rows="4"><?php echo $inputs['aboutme']; ?></textarea>
    </td>
    </tr>
  <tr>
    <td>آدرس سایت</td>
    <td>
      <input type="text" name="website" size="45" value="<?php echo $inputs['website']; ?>" />
    </td>
    </tr>
  <tr>
    <td>آدرس فیس بوک</td>
    <td>
      <input type="text" name="facebook" size="45" value="<?php echo $inputs['facebook']; ?>" />
    </td>
    </tr>
  <tr>
    <td>آدرس تويتر </td>
    <td>
      <input type="text" name="twitter" size="45" value="<?php echo $inputs['twitter']; ?>" />
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
	    <button type="submit" name="Submit" value="Submit" class="btn btn-small btn-success btn-strong">اضافه کردن کاربر</button>
		</div>
    </div>
    </div><!-- #list-controls -->
	<?php endif; // form  ?>
</div><!-- .row-fluid -->
</form>

    </div><!-- .content -->
</div><!-- .primary -->
<?php
include('footer.php');
?>