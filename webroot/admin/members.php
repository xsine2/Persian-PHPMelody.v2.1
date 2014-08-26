<?php
    $showm              = '6';
    $load_scrolltofixed = 1;
    $_page_title        = 'Users';
    include('header.php');
    $action = (int) $_GET['a'];
    $userid = (int) trim($_GET['uid']);
    $page   = (int) $_GET['page'];
    if (empty($page))
        $page = 1;
    $limit   = 20;
    $from    = $page * $limit - ($limit);
    $filter  = $filter_value = $search_type = $search_query = '';
    $filters = array(
        'power',
        'register',
        'followers',
        'following',
        'lastlogin',
        'register',
        'id'
    );
    if (in_array(strtolower($_GET['filter']), $filters) !== false) {
        $filter       = strtolower($_GET['filter']);
        $filter_value = $_GET['fv'];
    }
    if ($_POST['Submit'] != '' && !csrfguard_check_referer('_admin_members')) {
        $info_msg = '<div class="alert alert-error">توکن اشتباه و یا سیزن حذف شده است لطفا دوباره صفحه رو بارگذاری نمایید.</div>';
    } else if ($_POST['Submit'] == 'Activate checked' && (is_admin() || is_moderator())) {
        $user_ids = $_POST['user_ids'];
        if (count($user_ids) > 0) {
            $sql    = "UPDATE pm_users 
				SET power = '" . U_ACTIVE . "' 
				WHERE id IN (" . implode(',', $user_ids) . ") 
				  AND power = '" . U_INACTIVE . "'";
            $result = @mysql_query($sql);
            if (!$result) {
                $info_msg = '<div class="alert alert-error">خطایی هنگام بروزرسانی دیتابیس صورت گرفت .<br />خطای دیتابیس : ' . mysql_error() . '</div>';
            } else {
                $info_msg = '<div class="alert alert-success">اکانت کاربران انتخاب شده بروز شد .</div>';
            }
        } else {
            $info_msg = '<div class="alert alert-info">ابتدا یک چیزی را انتخاب کنید .</div>';
        }
    } else if ($_POST['Submit'] == 'Delete checked' && (is_admin() || is_moderator())) {
        $user_ids = $_POST['user_ids'];
        $total    = count($user_ids);
        if ($total > 0) {
            foreach ($user_ids as $k => $id) {
                if ($userdata['id'] == $id) {
                    unset($user_ids[$k]);
                    $total--;
                    break;
                }
            }
        }
        if ($total > 0) {
            $sql_in_user_ids = implode(',', $user_ids);
            if (is_admin()) {
                $sql = "DELETE FROM pm_users 
					WHERE id IN (" . $sql_in_user_ids . ") 
					  AND power != '" . U_ADMIN . "'";
            } else {
                $sql    = "SELECT id, power FROM pm_users WHERE id IN (" . $sql_in_user_ids . ")";
                $result = mysql_query($sql);
                while ($row = mysql_fetch_assoc($result)) {
                    if (!in_array($row['power'], array(
                        U_ACTIVE,
                        U_INACTIVE
                    ))) {
                        if (($key = array_search($row['id'], $user_ids)) !== false) {
                            unset($user_ids[$key]);
                        }
                    }
                }
                mysql_free_result($result);
                if (($key = array_search($userdata['id'], $user_ids)) !== false) {
                    unset($user_ids[$key]);
                }
                $sql = "DELETE FROM pm_users 
					WHERE id IN (" . $sql_in_user_ids . ")";
            }
            $user_ids_count = count($user_ids);
            $result         = ($user_ids_count > 0) ? @mysql_query($sql) : true;
            if (!$result) {
                $info_msg = '<div class="alert alert-error">خطایی هنگام بروزرسانی دیتابیس صورت گرفت .<br />خطای دیتابیس : ' . mysql_error() . '</div>';
            } else if ($user_ids_count > 0) {
                $affected_rows = mysql_affected_rows();
                @mysql_query('DELETE FROM pm_comments WHERE user_id IN (' . $sql_in_user_ids . ')');
                @mysql_query('DELETE FROM pm_comments_reported WHERE user_id IN (' . $sql_in_user_ids . ')');
                @mysql_query('DELETE FROM pm_favorites WHERE user_id IN (' . $sql_in_user_ids . ')');
                if (_MOD_SOCIAL && $affected_rows > 0) {
                    foreach ($user_ids as $k => $id) {
                        remove_all_related_activity($id, ACT_OBJ_USER);
                    }
                    follow_delete_user($user_ids);
                    foreach ($user_ids as $k => $uid) {
                        notifications_delete_user($uid);
                    }
                }
                if ($affected_rows == 0) {
                    $info_msg = '<div class="alert alert-success">هیچ اکنتی حذف نشد .</div>';
                } else if ($affected_rows > 1) {
                    $info_msg = '<div class="alert alert-success">' . $affected_rows . ' اکانت کاربری حذف شد.</div>';
                } else {
                    $info_msg = '<div class="alert alert-success">یک اکانت کاربری حذف شد.</div>';
                }
            } else {
                $info_msg = '<div class="alert alert-success">شما می توانید اکانت هایی را حذف کنید که در گروه های اکانت های <em>فعال</em> یا <em>غیر فعال</em> قرار داشته باشند .</div>';
            }
        } else {
            $info_msg = '<div class="alert alert-info">ابتدا یک چیزی را انتخاب کنید .</div>';
        }
    } else if ($_POST['Submit'] == 'Delete checked' && !(is_admin() || is_moderator())) {
        $info_msg = '<div class="alert-warning">متاسفم فقط مدیران ارشد قابلیت این کار را دارا هستند .</div>';
    }
    if ($action == 1 && !csrfguard_check_referer('_admin_members')) {
        $info_msg = '<div class="alert alert-error">توکن اشتباه و یا سیزن حذف شده است لطفا دوباره صفحه رو بارگذاری نمایید.</div>';
    } else if ($action == 1) {
        $query  = mysql_query("SELECT * FROM pm_users WHERE id = '" . $userid . "'");
        $result = mysql_fetch_array($query);
        if (is_moderator() && in_array($result['power'], array(
            U_ADMIN,
            U_MODERATOR,
            U_EDITOR
        ))) {
            $info_msg = '<div class="alert alert-info">متاسفانه شما نمی توانید این اکانت را حدغ نمایید.</div>';
        } else if ($result['power'] == U_ADMIN) {
            $info_msg = '<div class="alert alert-info">اکانت مدیران سایت قابل حذف شدن نیستند .</div>';
        } else {
            $result = @mysql_query("DELETE FROM pm_users WHERE id = '" . $userid . "'");
            if (!$result) {
                $info_msg = '<div class="alert alert-error">خطای دیتابیس : ' . mysql_error() . '</div>';
            } else {
                @mysql_query("DELETE FROM pm_comments WHERE user_id = '" . $userid . "'");
                @mysql_query("DELETE FROM pm_comments_reported WHERE user_id = '" . $userid . "'");
                @mysql_query("DELETE FROM pm_favorites WHERE user_id = '" . $userid . "'");
                if (_MOD_SOCIAL) {
                    remove_all_related_activity($userid, ACT_OBJ_USER);
                    follow_delete_user($userid);
                    notifications_delete_user($userid);
                }
                $info_msg = '<div class="alert alert-success">آی دی کاربر #<strong>' . $userid . '</strong> از این سایت حذف شد .</div>';
            }
        }
    }
    $members_nonce = csrfguard_raw('_admin_members');
    if (!empty($_POST['submit']) || !empty($_GET['submit']) || !empty($_POST['_pmnonce']) || !empty($_POST['_pmnonce_t'])) {
        $search_query  = ($_POST['keywords'] != '') ? trim($_POST['keywords']) : trim($_GET['keywords']);
        $search_type   = ($_POST['search_type'] != '') ? $_POST['search_type'] : $_GET['search_type'];
        $members       = a_list_users($search_query, $search_type, $from, $limit, $page);
        $total_members = $members['total'];
    } else {
        if (in_array($filter, array(
            'register',
            'followers',
            'following',
            'lastlogin',
            'register',
            'id'
        ))) {
            $total_members = count_entries('pm_users', '', '');
        } else {
            $total_members = count_entries('pm_users', $filter, $filter_value);
        }
        if ($total_members - $from == 1)
            $page--;
        $members = a_list_users('', '', $from, $limit, $page, $filter, $filter_value);
        if ($total_members - $from == 1)
            $page++;
    }
    $filename = 'members.php';
    $uri      = $_SERVER['REQUEST_URI'];
    $uri      = explode('?', $uri);
    $uri[1]   = str_replace(array(
        "<",
        ">",
        '"',
        "'",
        '/'
    ), '', $uri[1]);
    parse_str($uri[1], $temp);
    unset($temp['_pmnonce'], $temp['_pmnonce_t'], $temp['a'], $temp['a'], $temp['uid']);
    $uri[1]     = http_build_query($temp);
    $pagination = '';
    $pagination = a_generate_smart_pagination($page, $total_members, $limit, 1, $filename, $uri[1]);
?>
<div id="adminPrimary">
    <div class="row-fluid" id="help-assist">
        <div class="span12">
        <div class="tabbable tabs-left">
          <ul class="nav nav-tabs">
            <li class="active"><a href="#help-overview" data-toggle="tab">Overview</a></li>
            <li><a href="#help-onthispage" data-toggle="tab">Export to CSV</a></li>
            <li><a href="#help-bulk" data-toggle="tab">Filtering</a></li>
          </ul>
          <div class="tab-content">
            <div class="tab-pane fade in active" id="help-overview">
            <p>This page provides a quick overview of your users. Listings below contain each user's details such name, registration date, last login date, IP address and user rank/group.</p>
            <p>If the site requires you to approve each registered user, you will have to do so from this page. You can also approve registrations in bulk. To approve a user click the &quot;check&quot; icon from the &quot;Actions&quot; column.</p>
            <p>Note: Banned users will have a strikeout username.</p>
            </div>
            <div class="tab-pane fade" id="help-onthispage">
            <p>A sub-menu of this area, &quot;Export to CSV&quot; generates a CSV file compatible with Microsoft Outlook, GMAIL Contacts, Facebook Friends Import and so on.<br />
You can then import this CSV to your favorite service and use the list to get in touch with your users.</p>
            </div>
            <div class="tab-pane fade" id="help-bulk">
            <p>Listing pages such as this one contain a filtering area which comes in handy when dealing with a large number of entries. The filtering options is always represented by a gear icon positioned on the top right area of the listings table. Clicking this icon usually reveals a  search form and one or more drop-down filters.</p>
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
                <div class="floatL"><strong class="blue"><?php
    echo pm_number_format($total_members);
?></strong><span>کاربران</span></div>
                <div class="blueImg"><img src="img/ico-users-new.png" width="19" height="18" alt="" /></div>
            </li>
        </ul><!-- .pageControls -->
    </div>
	<h2>Users <a class="label opac5" href="add_user.php">+ افزودن کاربر جدید</a></h2>

<?php
    echo $info_msg;
?>

<?php
    if (!empty($_GET['keywords'])):
?>
<div class="pull-left">
	<h4>نتیجه جستجو برای "<em><?php
        echo $_GET['keywords'];
?></em>" <a href="#" onClick="parent.location='members.php'" class="opac5"><i class="icon-remove-sign"></i></a></h4>
</div>
<div class="clearfix"></div>
<?php
    endif;
?>

<div class="tablename">
<div class="row-fluid">
    <div class="span6">
        <div class="qsFilter pull-left">
            <form name="power_filter" action="members.php" method="get" class="form-inline">
            <input type="hidden" name="filter" value="power" />
            <div class="btn-group input-prepend">
            <div class="form-filter-inline">
            <?php
    if (!empty($filter)):
?>
            <button type="button" class="btn btn-danger btn-strong" onClick="parent.location='members.php'">حذف فیلتر</button>
            <?php
    else:
?>
            <button type="button" class="btn">فیلتر کن</button>
            <?php
    endif;
?>
            <select name="fv" class="inline last-filter" onchange="submit()">
            <option value="">توسط گروه</option>
            <option value="1" <?php
    if ($filter_value == '1')
        echo 'selected="selected"';
?> >مدیران</option>
            <option value="3" <?php
    if ($filter_value == '3')
        echo 'selected="selected"';
?> >ناظران سایت</option>
            <option value="4" <?php
    if ($filter_value == '4')
        echo 'selected="selected"';
?> >ویرایش گران</option>
            <option value="0" <?php
    if ($filter_value == '0')
        echo 'selected="selected"';
?> >کاربران معمولی</option>
            <option value="2" <?php
    if ($filter_value == '2')
        echo 'selected="selected"';
?> >کاربران غیر فعال</option>
            </select>
            </div>
            </div><!-- .btn-group -->
            </form>    
        </div><!-- .qsFilter -->
    </div>
    <div class="span6">
    	<div class="pull-right">
        <form name="search" action="members.php" method="get" class="form-search-listing form-inline">
            <div class="input-append">
            <input type="text" name="keywords" value="<?php
    echo $search_query;
?>" size="30" class="search-query search-quez input-medium" placeholder="کلمه کلیدی" id="form-search-input" />
            <select name="search_type" class="input-small">
             <option value="username" <?php
    echo ($search_type == "username") ? 'selected="selected"' : '';
?> >نام کاربری</option>
             <option value="fullname" <?php
    echo ($search_type == "fullname") ? 'selected="selected"' : '';
?> >نام شخص</option>
             <option value="email" <?php
    echo ($search_type == "email") ? 'selected="selected"' : '';
?> >آدرس ایمیل</option>
             <option value="ip" <?php
    echo ($search_type == "ip") ? 'selected="selected"' : '';
?> >آدرس آی پی</option>
            </select> 
            <button type="submit" name="submit" class="btn" value="جستجو" id="submitFind"><i class="icon-search findIcon"></i><span class="findLoader"><img src="img/ico-loading.gif" width="16" height="16" /></span></button>
            </div>
        </form>
        </div>
    </div>
</div>
</div>
<div class="clearfix"></div>
<form name="users_checkboxes" id="users_checkboxes" action="members.php?page=<?php
    echo $page;
?>&filter=<?php
    echo $filter;
?>&fv=<?php
    echo $filter_value;
?>" method="post">
<table cellpadding="0" cellspacing="0" width="100%" class="table table-striped table-bordered pm-tables tablesorter">
 <thead>
  <tr>
   <th align="center" style="text-align:center" width="3%"><input type="checkbox" name="checkall" id="selectall" onclick="checkUncheckAll(this);"/></th>
   <th width="50"><a href="members.php?filter=id&fv=<?php
    echo ($filter_value == 'desc' && $filter == 'id') ? 'asc' : 'desc';
?>" rel="tooltip" title="Sort <?php
    echo ($filter_value == 'desc' && $filter == 'id') ? 'ascending' : 'descending';
?>">آی دی</a></th>
   <th>نام کاربری</th>
   <th>نام شخص</th>
   <th>آدرس ایمیل</th>
   <th><a href="members.php?filter=register&fv=<?php
    echo ($filter_value == 'desc' && $filter == 'register') ? 'asc' : 'desc';
?>" rel="tooltip" title="Sort <?php
    echo ($filter_value == 'desc' && $filter == 'register') ? 'ascending' : 'descending';
?>">تاریخ عضویت</a></th>
   <th><a href="members.php?filter=followers&fv=<?php
    echo ($filter_value == 'desc' && $filter == 'followers') ? 'asc' : 'desc';
?>" rel="tooltip" title="Sort <?php
    echo ($filter_value == 'desc' && $filter == 'followers') ? 'ascending' : 'descending';
?>">دنبال کننده ها</a></th>
   <th><a href="members.php?filter=following&fv=<?php
    echo ($filter_value == 'desc' && $filter == 'following') ? 'asc' : 'desc';
?>" rel="tooltip" title="Sort <?php
    echo ($filter_value == 'desc' && $filter == 'following') ? 'ascending' : 'descending';
?>">دنبال شونده ها</a></th>
   <th><a href="members.php?filter=lastlogin&fv=<?php
    echo ($filter_value == 'desc' && $filter == 'lastlogin') ? 'asc' : 'desc';
?>" rel="tooltip" title="Sort <?php
    echo ($filter_value == 'desc' && $filter == 'lastlogin') ? 'ascending' : 'descending';
?>">آخرین بازدید</a></th>
   <th>آخرین آی پی</th>
   <th>گروه کاربری</th>
   <th style="width: 90px;">عملیات</th>
  </tr>
 </thead>
 <tbody>
  <?php
    if ($pagination != ''):
?>
  <tr>
	<td colspan="6" class="tableFooter">
		<div class="pagination pull-right"><?php
        echo $pagination;
?></div>
	</td>
  </tr>
  <?php
    endif;
?>
  
  <?php
    echo $members['users'];
?>
  
  <?php
    if ($pagination != ''):
?>
  <tr>
	<td colspan="6" class="tableFooter">
		<div class="pagination pull-right"><?php
        echo $pagination;
?></div>
	</td>
  </tr>
  <?php
    endif;
?>
 </tbody>
</table>

<div class="clearfix"></div>

<div id="stack-controls" class="list-controls">
<div class="btn-toolbar">
    <div class="btn-group">
    	<button type="submit" name="Submit" value="Activate checked" class="btn btn-small btn-success btn-strong">فعال کردن انتخاب شده ها</button>
    </div>
    <div class="btn-group">
    	<button type="submit" name="Submit" value="Delete checked" class="btn btn-small btn-danger btn-strong" onClick="return confirm_delete_all();">حذف انتخاب شده ها</button>
	</div>
</div>
</div><!-- #list-controls -->
<input type="hidden" name="_pmnonce" id="_pmnonce<?php
    echo $members_nonce['_pmnonce'];
?>" value="<?php
    echo $members_nonce['_pmnonce'];
?>" />
<input type="hidden" name="_pmnonce_t" id="_pmnonce_t<?php
    echo $members_nonce['_pmnonce'];
?>" value="<?php
    echo $members_nonce['_pmnonce_t'];
?>" />
<input type="hidden" name="filter" id="listing-filter" value="<?php
    echo $filter;
?>" />
<input type="hidden" name="fv" id="listing-filter_value" value="<?php
    echo $filter_value;
?>" />
<input type="hidden" name="search_type" id="listing-filter_search_type" value="<?php
    echo $search_type;
?>" />
<input type="hidden" name="keywords" id="listing-filter_keywords" value="<?php
    echo $search_query;
?>" />
</form>

    </div><!-- .content -->
</div><!-- .primary -->
<?php
    include('footer.php');
?>