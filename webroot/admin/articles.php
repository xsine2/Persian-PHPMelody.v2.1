<?php
$showm = 'mod_article';
$load_scrolltofixed = 1;
$load_chzn_drop = 1;
$_page_title = 'مدیریت کردن مقالات';
include('header.php');
?>
<div id="adminPrimary">
    <div class="row-fluid" id="help-assist">
        <div class="span12">
        <div class="tabbable tabs-left">
          <ul class="nav nav-tabs">
            <li class="active"><a href="#help-overview" data-toggle="tab">نمای کلی</a></li>
            <li><a href="#help-onthispage" data-toggle="tab">فیلتر</a></li>
          </ul>
          <div class="tab-content">
            <div class="tab-pane fade in active" id="help-overview">
            <p>این ماژول وابسته به نیاز شما فعال یا غیرفعال می تواند بشود (برای غیرفعال کردن آن به صفحه تنظیمات بروید) . همچنین شما وابسته به نیازتان می توانید از ماژول مقالات  به عنوان دیتابیس مقالات یا وبلاگ استفاده کنید.
			با استفاده از رنک کاربری buit-in ، شما می توانید می توانید کاربران معمولی که می توانند مقالات یا وبلاگ شما را مدیریت بکنند را به این کار بگمارید.</p>
			<p>توجه :ارسال مطلب اختصاصی و مناسب به طور منظم به سئو سایت شما کمک خواهد کرد.</p>
            </div>
            <div class="tab-pane fade" id="help-onthispage">
            <p>در هنگام مواجهه با تعداد زیادی از داده های وارد شده صفحات فهرست همانند این یکی شامل ناحیه فیلتر است که بصورت دستی می اید.
			گزینه فیلتر همیشه نشانگر این است که آیکنی بر قسمت بالای سمت راست جدول لیست ها قرار داده شده. 
			با کلیک بر روی این آیکن همیشه فرم جستجو فرم و یک  فیلتر کشویی یا بیشتر معلوم می شود.</p>
            </div>
          </div>
        </div> <!-- /tabbable -->
        </div><!-- .span12 -->
    </div><!-- /help-assist -->
    <div class="content">
	<a href="#" id="show-help-assist">راهنما</a>
    <h2>مقالات <a class="label opac5" onClick="parent.location='article_manager.php?do=new'">+ اضافه کردن جدید</a></h2>

<?php
if ( ! $config['mod_article'])
{
  ?>
   <div class="alert alert-info">
	ماژول مقالات هم اکنون غیرفعال شده است. لطفا آنرا از '<a href="settings.php">صفحه تنظیمات</a> یا ماژول های در دسترس فعال کنید'.
   </div>
  </div>
  <?php
  include('footer.php');
  exit();
}
?>

<?php

$action	= (int) $_GET['action'];
$page	= (int) $_GET['page'];

if($page == 0)
	$page = 1;


$total_articles = 0;
$limit = 20;		//	articles per page
$from = $page * $limit - ($limit);

$filter = '';
$filters = array('public', 'private', 'mostviewed', 'category', 'sticky', 'restricted');
$filter_value = '';

if(in_array(strtolower($_GET['filter']), $filters) !== false)
{
	$filter = strtolower($_GET['filter']);
	$filter_value = $_GET['fv'];
	
	if ($filter == 'category' && $filter_value == '')
	{
		$filter = '';
	}
}


//	Batch delete
if ($_POST['submit'] == "Delete checked" && ! csrfguard_check_referer('_admin_articles'))
{
	echo '<div class="alert alert-error">رمز عبور اشتباه است یا سشن منقضی شده. لطفا صفحه را رفرش نمایید و از دوباره تلاش کنید.</div>';
}
else if ('' != $_POST['submit'] && $_POST['submit'] == "Delete checked")
{
	$total_checkboxes = count($_POST['checkboxes']);
	if($total_checkboxes > 0 && (is_admin() || (is_moderator() && mod_can('manage_articles'))))
	{
		$article_ids = array();
		foreach ($_POST['checkboxes'] as $k => $id)
		{
			$article_ids[] = (int) $id;
		}
		
		$result = mass_delete_articles($article_ids);
		
		if ($result['type'] == 'error')
		{
			echo '<div class="alert alert-error">'. $result['msg'] .'</div>';
		}
		else
		{
			echo '<div class="alert alert-success">'. $result['msg'] .'</div>';
		}
	}
	else if ($total_checkboxes > 0 && (is_editor() || (is_moderator() && mod_cannot('manage_articles'))))
	{
		echo '<div class="alert alert-error">شما اجازه حذف مقالات را ندارید.</div>';
	}
	else
	{
		echo '<div class="alert alert-info">هیچ گزینه ای انتخاب نشده است!</div>';
	}
}

if ('' != $_POST['submit'] && $_POST['submit'] == 'Search')
{
	$articles = list_articles($_POST['keywords'], $_POST['search_type'], $from , $limit); 
	$total_articles = count($articles);
}
else
{
	switch ($filter)
	{
		default:
		case 'mostviewed':
		
			$total_articles = $config['total_articles'];
		
		break;
		
		case 'private':
		
			$total_articles = count_entries('art_articles', 'status', '0');
		
		break;

		case 'public':
		
			$total_articles = count_entries('art_articles', 'status', '1');
		
		break;

		case 'sticky':
		
			$total_articles = count_entries('art_articles', 'featured', '1');
		
		break;

		case 'restricted':
		
			$total_articles = count_entries('art_articles', 'restricted', '1');
		
		break;
		
		case 'category':
		
			$filter_value = (int) $filter_value;
			if ($filter_value > 0)
			{
				$sql = "SELECT COUNT(*) as total_found 
						FROM art_articles  
						WHERE category LIKE '". $filter_value ."' 
						   OR category LIKE '". $filter_value .",%' 
						   OR category LIKE '%,". $filter_value ."' 
						   OR category LIKE '%,". $filter_value .",%'";
				$result = mysql_query($sql);
				$row = mysql_fetch_assoc($result);
				mysql_free_result($result);
				
				$total_articles = $row['total_found'];
				unset($row, $result, $sql);
			}
			else if ($_GET['fv'] == '0')
			{
				$total_articles = count_entries('art_articles', 'category', '0');
			}
			else
			{
				$total_articles = 0;
			}
		
		break;
	}
	$articles = list_articles('', '', $from , $limit, $filter, $filter_value); 
}

// generate smart pagination
$filename = 'articles.php';
$pagination = '';

if(!isset($_POST['submit'])) 
	$pagination = a_generate_smart_pagination($page, $total_articles, $limit, 5, $filename, '&filter='. $filter .'&fv='. $filter_value);


if ($_GET['action'] == "deleted") 
{
	echo '<div class="alert alert-success">نظرات حذف شده اند.</div>';
}

if ($_GET['action'] == "badtoken") 
{
	echo '<div class="alert alert-error">رمز عبور اشتباه است یا سشن منقضی شده. لطفا صفحه را رفرش نمایید و از دوباره تلاش کنید.</div>';
}

?>
<div class="clearfix"></div>
<div class="entry-count">
    <ul class="pageControls">
        <li>
            <div class="floatL"><strong class="blue"><?php echo pm_number_format($total_articles); ?></strong><span>مقاله (ها)</span></div>
            <div class="blueImg"><img src="img/ico-articles-new.png" width="18" height="18" alt="" /></div>
        </li>
    </ul><!-- .pageControls -->
</div>
<div id="display_result" style="display:none;"></div>

<?php if ( ! empty($_POST['keywords'])) : ?>
<div class="pull-left">
	<h4>نتیجه جستجو برای "<em><?php echo $_POST['keywords']; ?></em>" <a href="#" onClick="parent.location='articles.php'" class="opac5"><i class="icon-remove-sign"></i></a></h4>
</div>
<div class="clearfix"></div>
<?php endif; ?>

<div class="tablename">
<div class="row-fluid">
    <div class="span8">
        <div class="qsFilter pull-left">
            <div class="btn-group input-prepend">
                <div class="form-filter-inline">
                <form name="category_filter" action="articles.php" method="get" class="form-inline">
                <?php if ( ! empty($_GET['filter'])) : ?>
                <button type="button" id="appendedInputButtons" class="btn btn-danger btn-strong" onClick="parent.location='articles.php'">حذف کردن فیلتر</button>
                <?php else : ?>
                <button type="button" id="appendedInputButtons" class="btn">فیلتر کردن</button>
                <?php endif; ?>
                
                <select name="fv" onchange=submit()>
                <option value="articles.php">به واسطه موضوع...</option>
                <?php
                $categories = art_get_categories();
                
                foreach ($categories as $id => $cat)
                {
                    $option = '<option value="'. $id .'" ';
                    if ($filter_value == $id && $filter == 'category')
                    {
                        $option .= ' selected="selected" ';
                    }
                    $option .= '>'. $cat['name'] .'</option>';
                    echo $option;
                }
                ?>
                <option value="0" <?php echo ($_GET['fv'] == '0') ? 'selected="selected"' : '';?>>دسته بندی نشده</option>
                </select>
                <select name="URL" class="inline last-filter" onChange="window.parent.location=this.form.URL.options[this.form.URL.selectedIndex].value">
                <option value="articles.php">به واسطه  وضعیت...</option>
                <option value="articles.php?page=1&filter=mostviewed" <?php if ($_GET['filter'] == 'mostviewed') echo 'selected="selected"'; ?>>بیشترین نمایش</option>
                <option value="articles.php?page=1&filter=public" <?php if ($_GET['filter'] == 'public') echo 'selected="selected"'; ?>>انتشار یافته</option>
                <option value="articles.php?page=1&filter=private" <?php if ($_GET['filter'] == 'private') echo 'selected="selected"'; ?>>پیش نویس ها</option>
                <option value="articles.php?page=1&filter=sticky" <?php if ($_GET['filter'] == 'sticky') echo 'selected="selected"'; ?>>چسبید به صفحه اصلی</option>
                <option value="articles.php?page=1&filter=restricted" <?php if ($_GET['filter'] == 'restricted') echo 'selected="selected"'; ?>>محدود شده ها</option>
                </select>
                <input type="hidden" name="filter" value="category" />
                </form>
                </div><!-- .form-filter-inline -->
            </div><!-- .btn-group -->
        </div><!-- .qsFilter -->
    </div>
    <div class="span4">
        <div class="pull-right">
            <form name="search" action="articles.php" method="post" class="form-search-listing form-inline">
            <div class="input-append">
            <input name="keywords" type="text" value="<?php echo $_POST['keywords']; ?>" size="30" class="search-query search-quez input-medium" placeholder="کلمه کلیدی" id="form-search-input" />
            <select name="search_type" tabindex="1" class="input-small">
            <option value="title" <?php echo ($_POST['search_type'] == 'title') ? 'selected="selected"' : '';?>>عنوان</option>
            <option value="content" <?php echo ($_POST['search_type'] == 'content') ? 'selected="selected"' : '';?>>توضیحات</option>
            </select>
            <button type="submit" name="submit" class="btn" value="جستجو" id="submitFind"><i class="icon-search findIcon"></i><span class="findLoader"><img src="img/ico-loading.gif" width="16" height="16" /></span></button>
            </div>
            </form>
        </div>
    </div>
</div>
</div>
<div class="clearfix"></div>
<form name="articles_checkboxes" id="articles_checkboxes" action="articles.php?page=<?php echo $page;?>" method="post">
<table cellpadding="0" cellspacing="0" width="100%" class="table table-striped table-bordered pm-tables tablesorter">
 <thead>
   <tr>
	<th align="center" style="text-align:center" width="3%"><input type="checkbox" name="checkall" id="selectall" onclick="checkUncheckAll(this);"/></th>
	<th width="10"></th>
	<th width="40%">عنوان</th>
	<th width="5%">نمایش ها</th>
    <th width="16%">دسته ها</th>
	<th width="10%">مولف</th>	
    <th width="90">اضافه شده</th>
	<th width="150">نظرات</th>
    <th width="" style="width:90px;">عملیات</th>
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
  
  	<?php 
	
	/*
	 *  List articles
	 */ 
	if ( ! array_key_exists('type', $articles) && $total_articles > 0)
	{
		$alt = 1;
		
		foreach ($articles as $k => $article)
		{
			$col = ($alt % 2) ? 'table_row1' : 'table_row2';
			$alt++;
			
			$total_comments = count_entries('pm_comments', 'uniq_id', 'article-'.$article['id']);
			?>
			 
			<tr class="<?php echo $col;?>" id="article-<?php echo $article['id'];?>">
			 <td align="center" style="text-align:center" width="3%">
			 	<input name="checkboxes[]" type="checkbox" value="<?php echo $article['id']; ?>" />
			 </td>
			 <td align="center" style="text-align:center" width="20">
			    <?php if ($article['restricted'] == '1') : ?>
					<div class="pm-sprite ico-locked" rel="tooltip" align="absbottom" title="فقط کاربرانی که ثبت نام کرده اند اجازه خواندن این مقاله را دارند"></div>
				<?php endif; ?>
				<?php if ($article['status'] == 0) : ?>
					<a href="#" rel="tooltip" title="این یک مقاله بایگانی خصوصی است. فقط مدیران ارشد و کاربران معمولی می توانند این مقاله را ببینند"><i class="icon-eye-close"></i></a>
				<?php endif; ?>
			 </td>
			 <td>
			 	<?php if ($article['featured'] == '1') : ?>
					<a href="articles.php?page=1&filter=sticky" rel="tooltip" title="Click to list only sticky articles" /><span class="label label-info">چسبیده به صفحه اصلی</span></a> 
                <?php endif; ?>
				<a href="<?php echo _URL.'/article_read.php?a='. $article['id']; if ($article['status'] == 0 || $article['date'] > time()) echo '&mode=preview'; ?>" target="_blank"><?php echo $article['title']; ?></a>
				<?php if ($article['date'] > time()): ?>
					&mdash; <small>هنوز انتشار نیافته</small>
				<?php endif;?>
			 </td>
			 <td align="center" style="text-align:center"><?php echo pm_number_format($article['views']); ?></td>
			 <td>
			  <?php 
			 	$str = '';
				foreach ($article['category_as_arr'] as $id => $name)
				{
					if ($id != '' && $name != '')
					{
						$str .= '<a href="articles.php?filter=category&fv='. $id .'" title="مقالات را لیست کن از '. $name .' فقط">'. $name .'</a>, ';
					}
					
					if ($id == 0)
					{
						$name = 'Uncategorized';
						$str .= '<a href="articles.php?filter=category&fv='. $id .'" title="مقالات را لیست کن از '. $name .' فقط">'. $name .'</a>, ';
					}
				}
			 	echo substr($str, 0, -2);
			  ?>
			 </td>
			 <td align="center" style="text-align:center">
			  <?php 
			  	$author = fetch_user_advanced($article['author']);
				
				echo '<a href="edit_user_profile.php?uid='. $author['id'] .'" title="ویرایش">'. $author['username'] .'</a>';
			  ?>
			 </td>
			 <td align="center" style="text-align:center"><?php echo date('M d, Y', $article['date']); ?></td>
			 <td align="center" style="text-align:center"> 
             		 <a href="comments.php?vid=<?php echo 'article-'.$article['id'];?>" title="دیدن نظرات" class="b_view">نمایش</a> 
			  <?php 
			  if (is_admin() || (is_moderator() && mod_can('manage_comments')))
			  {
			  	?>
			  	| <a href="#" title="حذف کردن تمام نظرات" onClick='del_video_comments("article-<?php echo $article['id'];?>", "<?php echo $page;?>")'>حذف کردن (<?php echo $total_comments; ?>)</a>
				<?php
			  }
			  ?>
			 </td>
			 <td align="center" class="table-col-action" style="text-align:center; width: 90px;">
			  <a href="article_manager.php?do=edit&id=<?php echo $article['id'];?>" class="btn btn-mini btn-link" rel="tooltip" title="ویرایش"><i class="icon-pencil"></i></a>
			  <a href="#" onclick="onpage_delete_article('<?php echo $article['id']; ?>', '#display_result', '#article-<?php echo $article['id'];?>')" class="btn btn-mini btn-link" rel="tooltip" title="حذف"><i class="icon-remove"></i></a>
			  </td>
		    </tr>
			
			<?php
		}
	}
	else	//	Error?
	{
		if (strlen($articles['msg']) > 0)
		{
			echo '<div class="alert alert-error">'. $articles['msg'] .'</div>';
		}
		
		if ($total_articles == 0)
		{
			?>
			<tr>
			 <td colspan="9" align="center" style="text-align:center">
			 هیچ مقاله ای یافت نشد.
			 </td>
			</tr>
			<?php
		}
	}
	?>
	
	<?php if ($pagination != '') : ?>
  	<tr>
		<td colspan="6" class="tableFooter">
			<div class="pagination pull-right"><?php echo $pagination; ?></div>
		</td>
	</tr>
  	<?php endif; ?>
  </tbody>
 </table>

<div class="clearfix"></div>
<div id="stack-controls" class="list-controls">
<div class="btn-toolbar">
    <div class="btn-group">
	<button type="submit" name="submit" value="Delete checked" class="btn btn-small btn-danger btn-strong" onClick="return confirm_delete_all();">حذف انتخاب شده ها</button>
    </div>
</div>
</div><!-- #list-controls -->
<?php
echo csrfguard_form('_admin_articles');
?>
</form>
    </div><!-- .content -->
</div><!-- .primary -->
<?php
include('footer.php');
?>