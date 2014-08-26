<?php
$showm = '1';
//$_page_title = '';
include('header.php');
include_once('syndicate_news.php');
?>

<div id="adminPrimary">
    
    <div class="content">
    <h2>خلاصه آمار سایت</h2>
	<div class="row-fluid">
    <ul class="qsData">
        <li>
            <a href="videos.php">
                <span class="number"><?php echo $config['total_videos']; ?></span>
                <span class="head">ویدیو<?php echo ($config['total_videos'] == 1) ? '' : 'ها'; ?></span>
            </a>
        </li>
        <li>
            <a href="approve.php">
            	<?php
				$vapprv = count_entries('pm_temp', '', '');
				?>
				<span class="number <?php if($vapprv > 0) {?>qspending<?php } ?>"><?php echo $vapprv; ?></span>
                <span class="head <?php if($vapprv > 0) {?>qspending<?php } ?>">ویدیوهای در انتظار تایید</span>
            </a>
        </li>
        <li>
            <a href="reports.php">
                <span class="number <?php if($crps > 0) {?>qsreported<?php } ?>"><?php echo $crps; ?></span>
                <span class="head <?php if($crps > 0) {?>qsreported<?php } ?>">ویدیوهای گزارش شده</span>
            </a>
        </li>
        <li>
            <a href="comments.php">
                <span class="number"><?php echo $comments_count = count_entries('pm_comments', '', ''); ?></span>
                <span class="head">نظرات</span>
            </a>
        </li>
        <li>
            <a href="comments.php">
				<span class="number <?php if($capprv > 0) {?>qspending<?php } ?>"><?php echo $capprv; ?></span>
                <span class="head <?php if($capprv > 0) {?>qspending<?php } ?>">نظرات جدید</span>
            </a>
        </li>
        <li>
            <a href="members.php">
                <span class="number"><?php echo $member_count = count_entries('pm_users', '', ''); ?></span>
                <span class="head">کاربران سایت</span>
            </a>
        </li>
<?php
if (_MOD_ARTICLE == 1)
{
?>
        <li>
            <a href="articles.php">
                <span class="number"><?php echo $config['total_articles']; ?></span>
                <span class="head">اخبار سایت</span>
            </a>
        </li>
<?php
}
?>
        <li>
            <a href="pages.php">
                <span class="number"><?php echo $config['total_pages']; ?></span>
                <span class="head">صفحات</span>
            </a>
        </li>
    </ul>
	</div>
    
    </div><!-- .content -->
</div><!-- .primary -->
<?php
include('footer.php');
?>