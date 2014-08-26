<?php
$parts = explode('/', $_SERVER['SCRIPT_NAME']);
$submenu = array_pop($parts);
$submenu = str_replace('.php', '', $submenu);

switch ($submenu)
{
	default:
	case 'index':
		
		$menu = 'index';
	
	break;
	
	case 'videos':
	case 'modify':
	case 'addvideo':
	case 'embedvideo':
	case 'streamvideo':
	case 'import':
	case 'import_user':
	case 'reports':
	case 'approve':
	case 'approve_edit':
		
		$menu = 'videos';
		
		$submenu = ($submenu == 'approve_edit') ? 'approve' : $submenu;
		$submenu = ($submenu == 'modify') ? 'videos' : $submenu; 
		
	break;
	
	case 'articles':
	case 'article_manager':
		
		$menu = 'articles';
		$submenu = ($_GET['do'] == 'edit') ? 'articles' : $submenu; 
		
	break;
	
	case 'comments':
	case 'blacklist':
		
		$menu = 'comments';
		
		if ($submenu == 'blacklist')
		{
			break;
		}
		
		switch ($_GET['filter'])
		{
			default:
			
				$submenu = '';
			
			break;
			
			case 'videos':
				
				$submenu = 'comments-video';
				
			break;
			
			case 'articles':

				$submenu = 'comments-article';
	
			break;
			
			case 'flagged':
				
				$submenu = 'comments-flagged';
					
			break;
			
			case 'pending':
				
				$submenu = 'comments-pending';
			
			break;
		}
		
		if ($menu == 'blacklist')
		{
			$submenu = 'blacklist';
		}
		
	break;
	
	case 'cat_manager':
	case 'article_categories':
	case 'edit_category':
	
		$menu = 'categories';
		
		if ($submenu == 'edit_category')
		{
			$submenu = ($_GET['type'] == 'article') ? 'article_categories' : 'cat_manager';
		}
		
		
	break;
	
	case 'pages':
	case 'page_manager':
		
		$menu = 'pages';
		
		$submenu = ($_GET['do'] == 'edit') ? 'pages' : $submenu;
		
	break;
	
	case 'members':
	case 'add_user':
	case 'banlist':
	case 'activity-stream':
	case 'members_export':
	case 'edit_user_profile':
		
		$menu = 'users';
	
	break;
	
	case 'ad_manager':
	case 'prerollstatic_ad_manager':
	case 'videoads':
	case 'ad-report':
		
		$menu = 'ads';
		
	break;
	
	case 'statistics':
	case 'show_searches':
	case 'readlog':
	case 'sys_phpinfo':
		
		$menu = 'stats';
		
	break;

	case 'settings':
	case 'settings_theme':
	case 'db_backup':
	case 'sitemap':
	case 'video-sitemap':
		
		$menu = 'settings';
		
	break;
}

?>
<div id="wrapper">
    <div id="adminSecondary" class="sideNav-bg">
    <div id="adminmenushadow"></div>
    <ul id="sideNav" role="navigation">
    
    <?php
    if (is_moderator() || is_editor())
    {
        // Index
		?>
        <li class="pm-menu <?php echo ($menu == 'index') ? 'active' : ''; ?> pm-menu-first">
        <a href="index.php" class="pm-menu-parent"><div class="pm-sprite ico-dash-new"></div> <span>داشبرد سایت</span></a>
        </li>        
        <?php
		// Videos
		if ($mod_can['manage_videos']) 
		{
        ?>

        <li class="pm-menu has-subcats <?php echo ($menu == 'videos') ? 'active' : ''; ?>">
        <a href="videos.php" class="pm-menu-parent"><div class="pm-sprite ico-videos-new"></div> <span>ویدیوها</span></a><?php if($tab_video_total > 0) {?><span class="pm-menu-count"><?php echo pm_number_format($tab_video_total); ?></span><?php } ?>
        <ul class="pm-sub-menu">
            <li <?php echo ($submenu == 'addvideo') ? 'class="active"' : ''; ?>><a href="addvideo.php">ارسال ویدیو از لینک یوتیوب</a></li>
            <li <?php echo ($submenu == 'embedvideo') ? 'class="active"' : ''; ?>><a href="embedvideo.php">افزودن ویدیو با کد</a></li>
            <li <?php echo ($submenu == 'streamvideo') ? 'class="active"' : ''; ?>><a href="streamvideo.php">افزودن ویدیو از لینک</a></li>
            <li <?php echo ($submenu == 'import') ? 'class="active"' : ''; ?>><a href="import.php">دریافت ویدیو از یوتیوب</a></li>
            <li <?php echo ($submenu == 'import_user') ? 'class="active"' : ''; ?>><a href="import_user.php">دریافت ویدیوهای کاربر یوتیوب</a></li>
            <li <?php echo ($submenu == 'reports') ? 'class="active"' : ''; ?>><a href="reports.php">ویدیوهای گزارش شده</a><?php if($crps > 0) {?><span class="pm-submenu-count"><?php echo pm_number_format($crps); ?></span><?php } ?></li>
            <li <?php echo ($submenu == 'approve') ? 'class="active"' : ''; ?>><a href="approve.php">ویدیوهای منتظر تایید</a><?php if($vapprv > 0) {?><span class="pm-submenu-count"><?php echo pm_number_format($vapprv); ?></span><?php } ?></li>
            <?php $modframework->admin_submenu(2);?>
        </ul>
        </li>
        <?php
        }

        // Articles
        if ($mod_can['manage_articles'] || is_editor()) 
        {
        	if ( $config['mod_article'] == 1 ) 
			{
		?>
        <li class="pm-menu has-subcats <?php echo ($menu == 'articles') ? 'active' : ''; ?>">
        <a href="articles.php" class="pm-menu-parent"><div class="pm-sprite ico-articles-new"></div> <span>اخبار سایت</span></a>
        <ul class="pm-sub-menu">
        <li <?php echo ($submenu == 'article_manager') ? 'class="active"' : ''; ?>><a href="article_manager.php?do=new">ارسال خبر جدید</a></li>
        <li <?php echo ($submenu == 'articles') ? 'class="active"' : ''; ?>><a href="articles.php">مدیریت اخبار</a></li>
        </ul>
        </li>
    	<?php 
			}
		}
		// Comments
		if ($mod_can['manage_comments'])
		{
		?>
        <li class="pm-menu has-subcats <?php echo ($menu == 'comments') ? 'active' : ''; ?>">
        <a href="comments.php" class="pm-menu-parent"><div class="pm-sprite ico-comments-new"></div> <span>نظرات</span></a><?php if($tab_comments > 0) {?><span class="pm-menu-count"><?php echo pm_number_format($tab_comments); ?></span><?php } ?>
        <ul class="pm-sub-menu">
        <li <?php echo ($submenu == 'comments-video') ? 'class="active"' : ''; ?>><a href="comments.php?filter=videos">نظرات ویدیوها</a></li>
		<?php 
        if ( $config['mod_article'] == 1 ) {
        ?>
        <li <?php echo ($submenu == 'comments-article') ? 'class="active"' : ''; ?>><a href="comments.php?filter=articles">نظرات اخبار سایت</a></li>
		<?php } ?>
        <li <?php echo ($submenu == 'comments-flagged') ? 'class="active"' : ''; ?>><a href="comments.php?filter=flagged">نظرات پرچم دار شده</a><?php if($flagged_comments > 0) {?><span class="pm-submenu-count"><?php echo pm_number_format($flagged_comments); ?></span><?php } ?></li>
        <li <?php echo ($submenu == 'comments-pending') ? 'class="active"' : ''; ?>><a href="comments.php?filter=pending">نظرات در انتظار تایید</a><?php if($pending_comments > 0) {?><span class="pm-submenu-count"><?php echo pm_number_format($pending_comments); ?></span><?php } ?></li>
        <li <?php echo ($submenu == 'blacklist') ? 'class="active"' : ''; ?>><a href="blacklist.php">پیشگیری از سوء استفاده</a></li>
        </ul>
        </li>
        <?php
        }
        // Users
        if ($mod_can['manage_users'])
        {
		?>
        <li class="pm-menu has-subcats <?php echo ($menu == 'users') ? 'active' : ''; ?>">
        <a href="members.php" class="pm-menu-parent"><div class="pm-sprite ico-users-new"></div> <span>کاربران</span></a>
        <ul class="pm-sub-menu">
        <li <?php echo ($submenu == 'add_user') ? 'class="active"' : ''; ?>><a href="add_user.php">افزودن کاربر جدید</a></li>
		<li <?php echo ($submenu == 'banlist') ? 'class="active"' : ''; ?>><a href="banlist.php">کاربران بلوک شده</a></li>
		<?php if (_MOD_SOCIAL) : ?>
		<li <?php echo ($submenu == 'activity-stream') ? 'class="active"' : ''; ?>><a href="activity-stream.php">اطلاع رسان کاربران</a></li>
		<?php endif;?>
        <?php $modframework->admin_submenu(6);?>
        </ul>
        </li>
        <?php
        }

    } // end  if (is_moderator() || is_editor())
    else
    {
    ?>
    
        <li class="pm-menu <?php echo ($menu == 'index') ? 'active' : ''; ?>">
        <a href="index.php" class="pm-menu-parent"><div class="pm-sprite ico-dash-new"></div> <span>داشبرد سایت</span></a>
        </li>
        <li class="pm-menu has-subcats <?php echo ($menu == 'videos') ? 'active' : ''; ?>">
        <a href="videos.php" class="pm-menu-parent"><div class="pm-sprite ico-videos-new"></div> <span>ویدیو ها</span></a><?php if($tab_video_total > 0) {?><span class="pm-menu-count"><?php echo pm_number_format($tab_video_total); ?></span><?php } ?>
        <ul class="pm-sub-menu">
            <li <?php echo ($submenu == 'addvideo') ? 'class="active"' : ''; ?>><a href="addvideo.php">ارسال ویدیو از لینک یوتیوب</a></li>
            <li <?php echo ($submenu == 'embedvideo') ? 'class="active"' : ''; ?>><a href="embedvideo.php">افزودن ویدیو با کد</a></li>
            <li <?php echo ($submenu == 'streamvideo') ? 'class="active"' : ''; ?>><a href="streamvideo.php">افزودن ویدیو از لینک</a></li>
            <li <?php echo ($submenu == 'import') ? 'class="active"' : ''; ?>><a href="import.php">دریافت ویدیو از یوتیوب</a></li>
            <li <?php echo ($submenu == 'import_user') ? 'class="active"' : ''; ?>><a href="import_user.php">دریافت ویدیوهای کاربر یوتیوب</a></li>
            <li <?php echo ($submenu == 'reports') ? 'class="active"' : ''; ?>><a href="reports.php">ویدیوهای گزارش شده</a><?php if($crps > 0) {?><span class="pm-submenu-count"><?php echo pm_number_format($crps); ?></span><?php } ?></li>
            <li <?php echo ($submenu == 'approve') ? 'class="active"' : ''; ?>><a href="approve.php">ویدیوهای منتظر تایید</a><?php if($vapprv > 0) {?><span class="pm-submenu-count"><?php echo pm_number_format($vapprv); ?></span><?php } ?></li>
            <?php $modframework->admin_submenu(2);?>
        </ul>
        </li>
    	
        <?php if ( $config['mod_article'] == 1 ) { ?>
        <li class="pm-menu has-subcats <?php echo ($menu == 'articles') ? 'active' : ''; ?>">
        <a href="articles.php" class="pm-menu-parent"><div class="pm-sprite ico-articles-new"></div> <span>اخبار سایت</span></a>
        <ul class="pm-sub-menu">
        <li <?php echo ($submenu == 'article_manager') ? 'class="active"' : ''; ?>><a href="article_manager.php?do=new">ارسال خبر جدید</a></li>
        <li <?php echo ($submenu == 'articles') ? 'class="active"' : ''; ?>><a href="articles.php">مدیریت اخبار</a></li>
        </ul>
        </li>
    	<?php } ?>

        <li class="pm-menu has-subcats <?php echo ($menu == 'pages') ? 'active' : ''; ?>">
        <a href="pages.php" class="pm-menu-parent"><div class="pm-sprite ico-page-new"></div> <span>صفحات</span></a>
        <ul class="pm-sub-menu">
        <li <?php echo ($submenu == 'page_manager') ? 'class="active"' : ''; ?>><a href="page_manager.php?do=new">ساخت صفحه جدید</a></li>
        <li <?php echo ($submenu == 'pages') ? 'class="active"' : ''; ?>><a href="pages.php">مدیریت صفحات</a></li>
        </ul>
        </li>
    
        <li class="pm-menu has-subcats <?php echo ($menu == 'categories') ? 'active' : ''; ?>">
        <a href="cat_manager.php" class="pm-menu-parent"><div class="pm-sprite ico-cats-new"></div> <span>موضوعات</span></a>
        <ul class="pm-sub-menu">
        	<li <?php echo ($submenu == 'cat_manager') ? 'class="active"' : ''; ?>><a href="cat_manager.php">موضوعات ویدیوها</a></li>
            <?php if ( $config['mod_article'] == 1 ) { ?>
            <li <?php echo ($submenu == 'article_categories') ? 'class="active"' : ''; ?>><a href="article_categories.php">موضوعات اخبار سایت</a></li>
            <?php } ?>
        </ul>
        </li>

        <li class="pm-menu has-subcats <?php echo ($menu == 'comments') ? 'active' : ''; ?>">
        <a href="comments.php" class="pm-menu-parent"><div class="pm-sprite ico-comments-new"></div> <span>نظرات</span></a><?php if($tab_comments > 0) {?><span class="pm-menu-count"><?php echo pm_number_format($tab_comments); ?></span><?php } ?>
        <ul class="pm-sub-menu">
        <li <?php echo ($submenu == 'comments-video') ? 'class="active"' : ''; ?>><a href="comments.php?filter=videos">نظرات ویدیوها</a></li>
		<?php 
        if ($config['mod_article'] == '1' && (is_admin() || (is_moderator() && mod_can('manage_comments')))) {
        ?>
        <li <?php echo ($submenu == 'comments-article') ? 'class="active"' : ''; ?>><a href="comments.php?filter=articles">نظرات اخبار سایت</a></li>
		<?php } ?>
        <li <?php echo ($submenu == 'comments-flagged') ? 'class="active"' : ''; ?>><a href="comments.php?filter=flagged">نظرات پرچم دار شده</a><?php if($flagged_comments > 0) {?><span class="pm-submenu-count"><?php echo pm_number_format($flagged_comments); ?></span><?php } ?></li>
        <li <?php echo ($submenu == 'comments-pending') ? 'class="active"' : ''; ?>><a href="comments.php?filter=pending">نظرات در انتظار تایید</a><?php if($pending_comments > 0) {?><span class="pm-submenu-count"><?php echo pm_number_format($pending_comments); ?></span><?php } ?></li>
        <li <?php echo ($submenu == 'blacklist') ? 'class="active"' : ''; ?>><a href="blacklist.php">پیشگیری از سوء استفاده</a></li>
        <?php $modframework->admin_submenu(4);?>
        </ul>
        </li>
    
        <li class="pm-menu has-subcats <?php echo ($menu == 'users') ? 'active' : ''; ?>">
        <a href="members.php" class="pm-menu-parent"><div class="pm-sprite ico-users-new"></div> <span>کاربران</span></a>
        <ul class="pm-sub-menu">
        <li <?php echo ($submenu == 'add_user') ? 'class="active"' : ''; ?>><a href="add_user.php">افزودن کاربر جدید</a></li>
		<li <?php echo ($submenu == 'banlist') ? 'class="active"' : ''; ?>><a href="banlist.php">کاربران بلوک شده</a></li>
		<?php if (_MOD_SOCIAL) : ?>
		<li <?php echo ($submenu == 'activity-stream') ? 'class="active"' : ''; ?>><a href="activity-stream.php">اطلاع رسان کاربران</a></li>
		<?php endif;?>
		<?php
		if (is_admin())
		{
		?>
		<li><a href="members_export.php" rel="tooltip" data-placement="right" title="A *.CSV file will be generated after clicking this link.">لیست کاربران در اکسل</a></li>
		<?php } ?>
        <?php $modframework->admin_submenu(6);?>
        </ul>
        </li>

        <li class="pm-menu has-subcats <?php echo ($menu == 'ads') ? 'active' : ''; ?>">
        <a href="ad_manager.php" class="pm-menu-parent"><div class="pm-sprite ico-ads-new"></div> <span>تبلیغات</span></a>
        <ul class="pm-sub-menu">
        <li <?php echo ($submenu == 'ad_manager') ? 'class="active"' : ''; ?>><a href="ad_manager.php">تبلیغات بنری</a></li>
		<li <?php echo ($submenu == 'prerollstatic_ad_manager') ? 'class="active"' : ''; ?>><a href="prerollstatic_ad_manager.php">تبلیغات استاتیک بر روی ویدیوها</a>
        <li <?php echo ($submenu == 'videoads') ? 'class="active"' : ''; ?>><a href="videoads.php">ویدیوهای تبلیغاتی بر روی ویدیوها</a></li>
        <li <?php echo ($submenu == 'ad-report') ? 'class="active"' : ''; ?>><a href="ad-report.php">آنالیز تبلیغات سایت</a></li>
        <?php $modframework->admin_submenu(9);?>
        </ul>
        </li>
    
        <li class="pm-menu has-subcats <?php echo ($menu == 'stats') ? 'active' : ''; ?>">
        <a href="statistics.php" class="pm-menu-parent"><div class="pm-sprite ico-stats-new"></div> <span>آمار و ارقام سیستم</span></a><?php if($tab_internallog > 0) {?><span class="pm-menu-count"><?php echo pm_number_format($tab_internallog); ?></span><?php } ?>
        <ul class="pm-sub-menu">
        <li <?php echo ($submenu == 'show_searches') ? 'class="active"' : ''; ?>><a href="show_searches.php">تاریخچه جستجوها</a></li>
        <li <?php echo ($submenu == 'readlog') ? 'class="active"' : ''; ?>><a href="readlog.php">تاریخچه سیستم</a><?php if($tab_internallog > 0) {?><span class="pm-submenu-count"><?php echo pm_number_format($tab_internallog); ?></span><?php } ?></li>
        <li <?php echo ($submenu == 'sys_phpinfo') ? 'class="active"' : ''; ?>><a href="sys_phpinfo.php">تنظیمات پی اچ پی</a></li>
        <?php $modframework->admin_submenu(7);?>
        </ul>
        </li>
            
        <li class="pm-menu has-subcats <?php echo ($menu == 'settings') ? 'active' : ''; ?>">
        <a href="settings.php" class="pm-menu-parent"><div class="pm-sprite ico-settings-new"></div> <span>تنظیمات</span></a>
        <ul class="pm-sub-menu">
        <li <?php echo ($submenu == 'settings_theme') ? 'class="active"' : ''; ?>><a href="settings_theme.php">تنظیمات قالب</a></li>
        <li <?php echo ($submenu == 'db_backup') ? 'class="active"' : ''; ?>><a href="<?php echo csrfguard_url(_URL .'/admin/db_backup.php?restart=1', '_admin_backupdb');?>" rel="tooltip" data-placement="right" title="An *.SQL file will be generated after clicking this link.">بکآپ از دیتابیس</a></li>
        <li <?php echo ($submenu == 'sitemap') ? 'class="active"' : ''; ?>><a href="sitemap.php" rel="tooltip" data-placement="right" title="Large sitemaps will take a while to generate.">ایجاد نقشه سایت به طور منظم</a></li>	
        <li <?php echo ($submenu == 'video-sitemap') ? 'class="active"' : ''; ?>><a href="video-sitemap.php">ساخت سایت مپ</a></li>
        <?php $modframework->admin_submenu(8);?>
        </ul>
        </li>
	<?php
	} // end #subNav
	?>
		<?php
        if (is_admin() ) {
		$modframework->show_admin_menu();
		$modframework->trigger_hook('admin_menu');
		}
		?>
        <li class="pm-menu-last"></li>
    
    </ul><!-- .sideNav -->
    </div><!-- #sideNav -->
<?php
unset($parts, $menu, $submenu);