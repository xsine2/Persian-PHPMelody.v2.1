<?php
$showm = '6';
$_page_title = 'کاربران محروم شده';
include('header.php');

$action		= $_GET['a'];
$page		= (int) $_GET['page'];
$userid		= (int) trim($_GET['uid']);

if(empty($page))
	$page = 1;
$limit = 20;
$from = $page * $limit - ($limit);


$total_members = count_entries('pm_banlist','','');

?>
<div id="adminPrimary">
    <div class="row-fluid" id="help-assist">
        <div class="span12">
        <div class="tabbable tabs-left">
          <ul class="nav nav-tabs">
            <li class="active"><a href="#help-overview" data-toggle="tab">نمای کلی</a></li>
          </ul>
          <div class="tab-content">
            <div class="tab-pane fade in active" id="help-overview">
            <p>این صفحه رکورد تمام کاربران محروم شده را فراهم می کند. <br />شما می توانید هر کاربری را توسط این صفحه هر کاربری را محروم بکنید.</p>
            </div>
          </div>
        </div> <!-- /tabbable -->
        </div><!-- .span12 -->
    </div><!-- /help-assist -->
    <div class="content">
	<a href="#" id="show-help-assist">راهنما</a>
    <div class="entry-count">
        <ul class="pageControls">
            <li>
                <div class="floatL"><strong class="blue"><?php echo pm_number_format($total_members); ?></strong><span>محروم (ها)</span></div>
                <div class="blueImg"><img src="img/ico-users-new.png" width="19" height="18" alt="" /></div>
            </li>
        </ul><!-- .pageControls -->
    </div>
	<h2>کاربران محروم شده</h2>
    
<?php echo $info_msg; ?>

<?php
switch($action)
{

	default:
	case 'delete':
	case 'show':
		
		if ($action == 'delete' && ! csrfguard_check_referer('_admin_banlist'))
		{
			echo '<div class="alert alert-error">رمز عبور اشتباه است یا سشن منقضی شده. لطفا صفحه را رفرش نمایید و از دوباره تلاش کنید.</div>';
		}
		else if($action == 'delete')
		{
			if(!empty($userid))
			{
				$sql = "DELETE FROM pm_banlist WHERE user_id = '".$userid."'";
				$result = mysql_query($sql);
				if(!$result)
				{
					$info_msg = '<div class="alert alert-error">خطایی رخ داده!<br />mysql برمی گرداند '.mysql_error().'</div>';
				}
				else
				{
					$info_msg = '<div class="alert alert-success">لیست محروم شده ها آپدیت شد!</div>';
				}
			}
			else
			{
				$info_msg = '<div class="alert alert-error">"'.$userid.'" یک شناسه کاربری معتبر نیست.</div>';
			}
			echo $info_msg;
		}
		
		if (isset($_POST['Submit']) && $action == 'ban' && ( ! csrfguard_check_referer('_admin_banlist')))
		{
			echo '<div class="alert alert-error">رمز عبور اشتباه است یا سشن منقضی شده. لطفا صفحه را رفرش نمایید و از دوباره تلاش کنید.</div>';
		}
		else if (isset($_POST['Submit']) && $action == 'ban')
		{
			$username = trim($_POST['username']);
			$reason = trim($_POST['reason']);
			$reason = nl2br($reason);
			$reason = secure_sql($reason);
			
			$sql = "SELECT id, power FROM pm_users WHERE username LIKE '".secure_sql($username)."'";
			$result = mysql_query($sql);
			if(!$result)
			{
				$info_msg = '<div class="alert alert-error">خطایی رخ داده! <br />گزارش mysql : '.mysql_error().'</div>';
			}
			else
			{
				if(mysql_num_rows($result) == 0)
				{
					$info_msg = '<div class="alert alert-error">کاربری پیدا نشد.</div>';
				}
				else
				{
					$info = mysql_fetch_assoc($result);
					if ($info['id'] == $userdata['id'])
					{
						$info_msg = '<div class="alert alert-error">شما نمی توانید این کار را بکنید.</div>';
					}
					else if ($info['power'] != U_ADMIN)
					{
						$sql = "INSERT INTO pm_banlist SET user_id = '".$info['id']."', reason ='".$reason."'";
						$result = mysql_query($sql);
						if ( ! $result)
						{
							$info_msg = '<div class="alert alert-error">خطایی رخ داده!<br />گزارش mysql : '.mysql_error().'</div>';
						}
						else
							$info_msg = '<div class="alert alert-success">لیست محروم شده ها با موفقیت بروزرسانی شد.</div>';
					}
					else
						$info_msg = '<div class="alert alert-error">حساب های مدیران ارشد نمی توانند محروم بشوند.</div>';
				}
			}
			echo $info_msg;
		}
		
		$banlist_nonce = csrfguard_raw('_admin_banlist');
		
		$banlist = a_list_banned($from, $limit);
		
		// generate smart pagination
		$filename = 'banlist.php';
	
		$pagination = '';
		$pagination = a_generate_smart_pagination($page, $total_members, $limit, 1, $filename, '');
?>
<div class="tablename">
<h6></h6>
<div class="qsFilter move-right">
<a href="#banUser" role="button" class="btn btn-danger btn-strong" data-toggle="modal">محروم کردن کاربر</a>
</div><!-- .qsFilter -->
</div>
<br />
<table cellpadding="0" cellspacing="0" width="100%" class="table table-striped table-bordered pm-tables tablesorter">
 <thead>
  <tr>
   <th width="35">شناسه</th>
   <th width="210">نام کار</th>
   <th>دلیل</th>
   <th width="" style="text-align:center; width: 90px;">اقدام</th>
  </tr>
 </thead>
 <tbody>
  <?php if ($pagination != '') : ?>
  <tr>
	<td colspan="6" class="tableFooter">
		<div class="pagination pull-right"><?php echo $pagination; ?></div>
	</td>
  </tr>
  <?php endif; ?>
  
  <?php echo $banlist; ?>
  
  <?php if ($pagination != '') : ?>
  <tr>
	<td colspan="6" class="tableFooter">
		<div class="pagination pull-right"><?php echo $pagination; ?></div>
	</td>
  </tr>
  <?php endif; ?>
 </tbody>
</table>

<div class="modal hide fade" id="banUser" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
<div class="modal-header">
<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
<h3 id="myModalLabel">محروم کردن کاربر</h3>
</div>
<form name="ban_user" action="banlist.php?a=ban" method="post">
<div class="modal-body">
<label>نام کاربری</label>
<input type="text" name="username" value="<?php echo $_POST['username'];?>" size="40" id="focusedInput" class="span5" />
<label>دلیل</label>
<textarea name="reason" cols="60" rows="3" class="span5"><?php echo $_POST['reason'];?></textarea>
</div>
<div class="modal-footer">
<a href="#" class="btn-mini" data-dismiss="modal" aria-hidden="true">کنسل</a>
<button type="submit" name="Submit" value="Ban" class="btn btn-small btn-danger btn-strong" />محروم کردن کاربر</button>
<input type="hidden" name="_pmnonce" id="_pmnonce<?php echo $banlist_nonce['_pmnonce'];?>" value="<?php echo $banlist_nonce['_pmnonce'];?>" />
<input type="hidden" name="_pmnonce_t" id="_pmnonce_t<?php echo $banlist_nonce['_pmnonce'];?>" value="<?php echo $banlist_nonce['_pmnonce_t'];?>" />
    
</div>
</form>
</div>
    
	<?php
	break;
}
?>
    </div><!-- .content -->
</div><!-- .primary -->
<?php
include('footer.php');
?>