<?php
$showm = '9';
$_page_title = 'تبلیغات بنری';
include('header.php');

function manage_ad_form($action = 'addnew', $item = false)
{
	
	if (empty($item['id']))
	{
		$item['id'] = 0;
	}
	
	$target = '';
	switch($action)
	{
		case 'addnew':
			$target = 'ad_manager.php?act=addnew';
		break;
		case 'edit':
			$target = ($id != 0) ? 'ad_manager.php?act=edit&id='.$item['id'] : 'ad_manager.php?act=edit';
		break;
	}
	
	?>
	<form name="ad_manager" method="post" action="<?php echo $target; ?>">
	<table width="100%" border="0" cellpadding="4">
	  <tr>
		<td class="fieldtitle" width="10%">نام :</td>
		<td><input type="text" name="position" value="<?php echo $item['position']; ?>" size="40" /></td>
	  </tr>
	  <tr>
		<td class="fieldtitle" width="10%">توضیحات :</td>
		<td><input type="text" name="description" value="<?php echo $item['description']; ?>" size="40" /></td>
	  </tr>
	  <tr>
		<td class="fieldtitle" valign="top">کد html :</td>
		<td><textarea name="code" cols="60" rows="7"><?php echo $item['code']; ?></textarea></td>
	  </tr>
	  <tr>
		<td class="fieldtitle" valign="top">فعال کردن آمار</td>
		<td>
			<label><input type="radio" name="disable_stats" value="0" <?php echo ($item['disable_stats'] == 0) ? 'checked="checked"' : '';?>> بله</label> <label><input type="radio" name="disable_stats" value="1" <?php echo ($item['disable_stats'] == 1) ? 'checked="checked"' : '';?>> خیر</label>
		</td>
	  </tr>
	  <tr>
		<td>&nbsp;</td>
		<td><input type="submit" name="Submit" value="ذخیره اطلاعات" class="btn btn-success" /></td>
	  </tr>
	</table>
	</form>
	<?php
	return;
}


$action = $_GET['act'];

$total_ads = count_entries('pm_ads', '', '');

?>
<!-- create new ad zone form modal -->
<div class="modal hide fade" id="addNew" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="myModalLabel">ایجاد یک ناحیه تبلیغ</h3>
    </div>
    <form name="ad_manager" method="post" action="ad_manager.php?act=addnew">
        <div class="modal-body">
            <label>نام</label>
            <input type="text" name="position" value="" size="40" />
            
			<label>توضیحات</label>
            <input type="text" name="description" value="" size="40" />
            
			<label>کد html برای تبلیغ شما</label>
            <textarea name="code" cols="60" rows="7" class="span5"></textarea>
			
			<label>فعال کردن آمار</label>
			<label><input type="radio" name="disable_stats" value="0" checked="checked"> بله</label> 
			<label><input type="radio" name="disable_stats" value="1"> خیر</label>
        </div>
        <div class="modal-footer">
        <input type="hidden" name="active" value="1" />
        <button data-dismiss="modal" aria-hidden="true" class="btn btn-normal btn-strong">کنسل</button>
        <button type="submit" name="Submit" value="Submit" class="btn btn-success btn-strong" />ذخیره</button>
    </div>
    </form>
</div>

<div id="adminPrimary">
    <div class="row-fluid" id="help-assist">
        <div class="span12">
        <div class="tabbable tabs-left">
          <ul class="nav nav-tabs">
            <li class="active"><a href="#help-overview" data-toggle="tab">هفتگی</a></li>
            <li><a href="#help-onthispage" data-toggle="tab">کد tpl</a></li>
          </ul>
          <div class="tab-content">
            <div class="tab-pane fade in active" id="help-overview">
    		<p>مدیر  تبلیغ build-in به شما اجازه می دهد تا ناحیه های تبلیغ و را تعیین کنید و تبلیغات را با نواحی تبلیغ خودشان تعریف کنید.<br />یک ناحیه تبلیغ بر روی سایت شما  قصد دارد که تبلیغی را تعیین بکن. (مثال : هدر ، پایین ویدئو ، صفحه ثبت نام ، زیر مقاله و ...). زمانی که یک ناحیه تبلیغ بتماما ایجاد می شود شما باید کد تبلیغ را وارد نمایید.به وسیله نواحی تبلیغ شما به آسانی می توانید تبلیغ ها را ایجاد یا حذف نمایید.</p>
            </div>
            <div class="tab-pane fade" id="help-onthispage">
			<p>کد tpl یک متغیره معینی است که شما باید آنرا در قالب کنونی تان  استفاده نمایید. چندین پرست که با هر نصب php می آیند همانند لیست زیر وجود دارند.<br />در این حالت ، هیچ تغییر قالبی لازم نیست. فقط در کدتان آنرا کپی بکن. همین</p>
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
                <div class="floatL"><strong class="blue"><?php echo pm_number_format($total_ads); ?></strong><span>تبلیغ<?php echo ($total_ads > 1) ? 's' : '';?></span></div>
                <div class="blueImg"><div class="pm-sprite ico-ads-small"></div></div>
            </li>
        </ul><!-- .pageControls -->
    </div>
	<h2>تبلیغات بنری<a class="label opac5" href="#addNew" onclick="location.href='#addNew';" data-toggle="modal">+ اضافه کردن جدید</a></h2>
        
<?php

switch($action)
{
	case 'addnew':
		if($_GET['act'] == 'addnew')
		{
			if(isset($_POST['Submit']))
			{
				$arr_fields = array('position' => "Name", 'code' => "Code", 'active' => 'Status');
				$errors = '';
				foreach($_POST as $k => $v)
				{
					if(trim($v) == '' && array_key_exists($k, $arr_fields) === TRUE)
							$errors .= "<li>'".$arr_fields[$k]."' نباید خالی باشد .</li>";	
					//$_POST[$k] = str_replace('"', "", $v);
				}
				if($errors != '')
				{
					echo "<div class=\"alert alert-error\"><ul>".$errors."</ul></div>";
					echo manage_ad_form('addnew', $_POST);// 0, $_POST['position'], $_POST['code'], $_POST['active']);
				}
				else
				{
					$position = secure_sql($_POST['position']);
					$code = secure_sql($_POST['code']);
					$description = secure_sql($_POST['description']);
					$active = ($_POST['active'] == 1) ? 1 : 0;
					$disable_stats = (int) $_POST['disable_stats'];
					
					$query = mysql_query("INSERT INTO pm_ads SET position = '".$position."', description = '".$description."', code = '".$code."', active = '".$active."', disable_stats = '". $disable_stats ."'");
					if(!$query)
						echo "<div class=\"alert alert-error\">در هنگام ایجاد تبلیغ جدید در دیتابیس شما مشکلی به وجود آمد.<br />
							  <strong>خطای برگشتی :</strong> ".mysql_error()."</div>";
					else
					{
						$new_ad_id = mysql_insert_id();
						$msg = '<div class="alert alert-success">
						<h4>انجام شد!</h4>
						<p>ناحیه تبلیغ شما ایجاد شد. بخاطر اینکه این یک<strong>ناحیه</strong>است ، شما بید این ناحیه تبلیغ را  به مکان مورد نظر در قالب وارد کنید.</p>
						<ol>
						<li>مکان را برای این ناحیهتبلیغ جدید انتخاب کنید. (مثال :header.tpl, index.tpl, footer.tpl)</li>
						<li>کدهای زیر را هر جا که میخواهید با تبلیغ مربوط به آن ناحیه تبلیغ نمایش داده شود کپی کنید :<strong>{$ad_'.$new_ad_id.'}</strong></li>
						</ol>';
						if($_POST['active'] == 0)
							$msg .= "<br /><small>P.S فراموش نکن که آنرا فعال کنی</small>";
						$msg .= '</div>';
						$msg .= '<input name="continue" type="button" value="&larr; Return to Ad Manager" onClick="location.href=\'ad_manager.php\'" class="btn" />';
						
						echo $msg;
					}
				}
			}
			else
			{
				echo manage_ad_form('addnew');
			}
		}	
	break;
	
	case 'edit':
		$id = $_GET['id'];
		if($id <= 0 || !is_numeric($id) || $id == '')
			echo "<div class=\"alert alert-error\">شناسه یک مقدار معتبر نیست یا از دست رفته.</div>";
		
		else
		{
			if(isset($_POST['Submit']))
			{
				$arr_fields = array('position' => "Name");
				$errors = '';
				foreach($_POST as $k => $v)
				{
					if(trim($v) == '' && array_key_exists($k, $arr_fields) === TRUE)
							$errors .= "<div class=\"alert alert-error\"> '".$arr_fields[$k]."'فیلد نباید خالی باشد.</div>";	
					//$_POST[$k] = str_replace('"', "", $v);
				}				
				if($errors != '')
				{
					echo $errors."<br />";
					$_POST['id'] = $id;
					echo manage_ad_form('edit', $_POST);//, $id, $_POST['position'], $_POST['description'], $_POST['code'], $_POST['active']);
					echo "</div>";
					include('footer.php');
					exit();
				}
				$position = secure_sql($_POST['position']);
				$code = secure_sql($_POST['code']);
				$description = secure_sql($_POST['description']);
				$active = ($_POST['active'] == 1) ? 1 : 0;
				$disable_stats = (int) $_POST['disable_stats']; 
				
				$query = mysql_query("UPDATE pm_ads SET position = '".$position."',
														description = '".$description."',
														code = '".$code."',
														active = '".$active."',
														disable_stats = '". $disable_stats ."' 
													WHERE id='".$id."'");
				if ( ! $query)
				{
					echo "<div class=\"alert alert-error\">در هنگام اضافه کردن این تبلیغ در دیتابیس خطایی رخ داده.<br />
						  <strong>mysql بر می گرداند :</strong> ".mysql_error()."</div>";
				}
				else
				{
					echo "<div class=\"alert alert-success\">تبلیغ با موفقیت بروز شد .</a></div>";
					echo '<input name="continue" type="button" value="&larr; باگشت به صفحه مدیریت تبلیغات" onClick="location.href=\'ad_manager.php\'" class="btn" />';
				}
					
			}
			else
			{
				$query = mysql_query("SELECT * FROM pm_ads WHERE id='".$id."'");
				if(!$query)
				{
					echo "<div class=\"alert alert-error\">در هنگام گرفتن اطلاعات از دیتابیس شما اتفاقی رخ داده.<br />
							  <strong>mysql بر می گرداند :</strong> ".mysql_error()."</div>";
					echo "</div>";
					include('footer.php');
					exit();
				}
				
				$ad = mysql_fetch_assoc($query);
				if($ad['id'] == '')
					echo "<div class=\"alert alert-error\">انتخاب شده ها در دیتابیش ما پیدا نشده اند. شاید دیتابیس خراب شده.</div>";
				else
					echo manage_ad_form('edit', $ad); //$ad['id'], $ad['position'], $ad['description'], $ad['code'], $ad['active']);
			}
		}
	
	break;
	
	case 'delete':
	case 'activate':
	case 'deactivate':
	case '':
	default:
	
		$total_ads = count_entries('pm_ads', '', '');

		if($action == 'delete')
		{
			$id = $_GET['id'];
			if($id <= 0 || !is_numeric($id) || $id == '')
				echo "<div class=\"alert alert-error\">اشتباهی رخ داده و یا آی دی مورد نظر شما گم شده است.</div>";
			elseif(in_array($id, array(1, 2, 3, 4, 5, 6, 7)) !== FALSE)
			{
				echo "<div class=\"alert alert-error\">متاسفانه تبلیغ پیش فرض حذف نشده. شما می توانید آنها را غیرفعال کنید یا ناحیه های جدید ایجاد کنید.</div>";
			}
			else
			{
				$query = mysql_query("DELETE FROM pm_ads WHERE id = '".$id."'");
				if ( !$query )
				{
					echo "<div class=\"alert alert-error\">در هنگام حذف این ناحیه تبلیغ مشکلی رخ داده.<br /><strong>mysql بر می گرداند : </strong> ".mysql_error()."</div>";
				}
				else
				{
					
					$sql = "DELETE FROM pm_ads_log 
							WHERE ad_id = $id 
							  AND ad_type = ". _AD_TYPE_CLASSIC;
					@mysql_query($sql); 

					echo "<div class=\"alert alert-success\">ناحیه تبلیغ حذف شد.</div>";
				}
			}
		}
		if($action == 'activate' || $action == 'deactivate')
		{
			$id = $_GET['id'];
			if($id <= 0 || !is_numeric($id) || $id == '')
				echo "<div class=\"alert alert-error\">شناسه غیرمعتبر یا از دست رفته.</div>";
			else
			{	
				$sql = '';
				if($action == "activate")
					$sql = "UPDATE pm_ads SET active='1' WHERE id = '".$id."' LIMIT 1";
				else
					$sql = "UPDATE pm_ads SET active='0' WHERE id = '".$id."' LIMIT 1";
				
				$query = mysql_query($sql);
				if( !$query )
					echo "<div class=\"alert alert-error\">در هنگام فعال/غیرفعال کردن این ناحیه تبلیغ خطایی رخ داده.<br /><strong>mysql بر می گرداند :</strong> ".mysql_error()."</div>";
				else
				{
					if($action == "activate")
						echo "<div class=\"alert alert-success\">ناحیه تبلیغ الان فعال است.</div>";
					else
						echo "<div class=\"alert alert-success\">ناحیه تبلیغ غیرفعال شد.</div>";
				}
			}
		}
	?>


<div class="tablename">
<div class="qsFilter move-right pull-right">
<?php if ($action != 'addnew' && $action != 'edit') : ?>
<a href="#addNew" class="btn btn-success btn-strong" data-toggle="modal">ایجاد ناحیه تبلیغ جدید</a>
<?php endif; ?>
</div><!-- .qsFilter -->
</div>
<br />
<table cellpadding="0" cellspacing="0" width="100%" class="table table-striped table-bordered pm-tables tablesorter">
 <thead> 
  <tr>
    <th>نام</th>
	<th align="center" style="text-align:center" width="10%">کد tpl</th>
    <th align="center" style="text-align:center" width="15%">وضعیت</th>
	<th align="center" style="text-align:center; width: 120px;">رفتار</th>
  </tr>	
 </thead>
 <tbody>
  <?php
	 
	// display all ads
	$query = mysql_query("SELECT * FROM pm_ads ORDER BY id DESC");
	$i = 0;
	while($row = mysql_fetch_assoc($query))
	{	
		$clean_title = str_replace(array('"', "'"), array('', "\'"), $row['position']);
		$row_class = ($i++ % 2) ? 'table_row1' : 'table_row2';
		
		?>
		<tr class="<?php echo $row_class;?>">
			<td>
				<strong><?php echo $row['position']; ?></strong> <br /><em><small><?php echo $row['description']; ?></small></em>
			</td>
			<td align="center" style="text-align:center">
				{$ad_<?php echo $row['id']; ?>}
			</td>
			<td align="center" style="text-align:center">
				<small><?php if ($row['active'] == 1) :?>
					<span class="label label-success">فعال</span>
				<?php else : ?>
					<span class="label">غیرفعال</span>
				<?php endif; ?>
				</small>
			</td>
			<td align="center" class="table-col-action" style="text-align:center">
				<?php if ($row['active'] == 0) :?>
				 <a href="ad_manager.php?act=activate&id=<?php echo $row['id']; ?>" class="btn btn-mini btn-link" rel="tooltip" title="فعالسازی تبلیغ"><i class="icon-ok-sign"></i></a>
				<?php else : ?>
				 <a href="ad_manager.php?act=deactivate&id=<?php echo $row['id']; ?>" class="btn btn-mini btn-link" rel="tooltip" title="غیر فعال کردن تبلیغ"><i class="icon-remove-sign"></i></a>
				<?php endif; ?>
				<a href="#" class="adzone_update_<?php echo  $row['id'] ; ?> btn btn-mini btn-link" title="ویرایش"><i class="icon-pencil"></i> </a> <a href="#" onClick="delete_ad('<?php echo  $clean_title ; ?>', 'ad_manager.php?act=delete&id=<?php echo $row['id']; ?>')" class="btn btn-mini btn-link" rel="tooltip" title="حذف"><i class="icon-remove" ></i> </a>
			</td>
		</tr>
		<tr>
			<td colspan="5" style="margin:0;padding:0;">
				<div id="adzone_update_<?php echo  $row['id'] ; ?>" name="<?php echo  $row['id'] ; ?>">
					<div class="adzone_update_form" style="padding: 10px; margin: 10px;">
					<form name="adzone_update_<?php echo  $row['id'] ; ?>" method="post" action="ad_manager.php?act=edit&id=<?php echo $row['id']; ?>">
				 		<label>نام</label>
				 		<input type="text" name="position" value="<?php echo $row['position']; ?>" size="40" />
				 		
						<label>توضیحات</label>
						<input type="text" name="description" value="<?php echo $row['description']; ?>" size="40" />
						
						<label>کد html</label>
						<textarea name="code" cols="60" rows="4" style="width: 95%;" ><?php echo $row['code']; ?></textarea>
						
						<label>فعال کردن آمار</label>
						<label><input type="radio" name="disable_stats" value="0" <?php echo ($row['disable_stats'] == 0) ? 'checked="checked"' : '';?>> بله</label> 
						<label><input type="radio" name="disable_stats" value="1" <?php echo ($row['disable_stats'] == 1) ? 'checked="checked"' : '';?>> خیر</label>
						
						<input type="hidden" name="active" value="<?php echo $row['active']; ?>" />
						<input type="submit" name="Submit" value="ذخیره" class="btn btn-mini btn-success border-radius0" />
						<a href="#" id="adzone_update_<?php echo  $row['id'] ; ?>" class="btn-mini">کنسل</a>
					</form>
					</div>
				</div>
			</td>
		</tr>
		<?php
	}
	mysql_free_result($query);
	break;
}

?>
 </tbody>
</table>
</div>	<!-- end div id="content" -->
<?php
include('footer.php');
?>