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

session_start();
require('config.php');
require_once('include/functions.php');
require_once('include/user_functions.php');
require_once('include/islogged.php');
require_once('include/article_functions.php');


// Let's see if there's a admin assigned 404 page
$page = array();
$page = get_page_by_name('404');

if (count($page) == 0)
{
	$smarty->assign('meta_title', "404 - Not found - "._SITENAME);
	$smarty->assign('template_dir', $template_f);
	$smarty->display('404.tpl');
}
else
{
	page_update_view_count($page['id']);
	$smarty->assign('page', $page);
	
	$smarty->assign('template_dir', $template_f);
	$smarty->assign('meta_title', htmlspecialchars($page['title'], ENT_QUOTES));
	$smarty->assign('meta_keywords', $page['meta_keywords']);
	$smarty->assign('meta_description', $page['meta_description']);
	
	$smarty->display('page.tpl');
}
?>