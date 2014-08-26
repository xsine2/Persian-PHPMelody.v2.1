<!DOCTYPE html>
<!--[if IE 7 | IE 8]>
<html class="ie" dir="ltr" lang="en">
<![endif]-->
<!--[if !(IE 7) | !(IE 8)  ]><!-->
<html dir="ltr" lang="en">
<!--<![endif]-->
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=1024,maximum-scale=1.0">
<title>{$meta_title}</title>
<meta http-equiv="X-UA-Compatible" content="IE=9; IE=8; IE=7; IE=edge,chrome=1">  
{if $no_index == '1'}
<meta name="robots" content="noindex,nofollow">
<META NAME="GOOGLEBOT" CONTENT="NOINDEX, NOFOLLOW">
{/if}
<meta name="title" content="{$meta_title}" />
<meta name="keywords" content="{$meta_keywords}" />
<meta name="description" content="{$meta_description}" />
<link rel="shortcut icon" href="{$smarty.const._URL}/{$smarty.const._UPFOLDER}/favicon.ico">
{if $tpl_name == "video-category"}
<link rel="alternate" type="application/rss+xml" title="{$meta_title}" href="{$smarty.const._URL}/rss.php?c={$cat_id}" />
{elseif $tpl_name == "video-top"}
<link rel="alternate" type="application/rss+xml" title="{$meta_title}" href="{$smarty.const._URL}/rss.php?feed=topvideos" />
{elseif $tpl_name == "article-category"}
<link rel="alternate" type="application/rss+xml" title="{$meta_title}" href="{$smarty.const._URL}/rss.php?c={$cat_id}&feed=articles" />
{else}
<link rel="alternate" type="application/rss+xml" title="{$meta_title}" href="{$smarty.const._URL}/rss.php" />
{/if}

<!--[if lt IE 9]>
<script src="//html5shim.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->
<link rel="stylesheet" type="text/css" media="screen" href="{$smarty.const._URL}/templates/{$template_dir}/css/bootstrap.min.css">
<link rel="stylesheet" type="text/css" media="screen" href="{$smarty.const._URL}/templates/{$template_dir}/css/bootstrap-responsive.min.css">
<!--[if lt IE 9]>
<script src="//css3-mediaqueries-js.googlecode.com/svn/trunk/css3-mediaqueries.js"></script>
<![endif]-->
<link rel="stylesheet" type="text/css" media="screen" href="{$smarty.const._URL}/templates/{$template_dir}/css/new-style.css">
<link rel="stylesheet" type="text/css" media="screen" href="{$smarty.const._URL}/templates/{$template_dir}/css/player.css">
<link rel="stylesheet" type="text/css" media="screen" href="{$smarty.const._URL}/templates/{$template_dir}/css/uniform.default.min.css">
<!--[if IE]>
{literal}
<link rel="stylesheet" type="text/css" media="screen" href="{/literal}{$smarty.const._URL}{literal}/templates/{/literal}{$template_dir}{literal}/css/new-style-ie.css">
{/literal}
<link href="//fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet" type="text/css">
<link href="//fonts.googleapis.com/css?family=Open+Sans:400italic" rel="stylesheet" type="text/css">
<link href="//fonts.googleapis.com/css?family=Open+Sans:700" rel="stylesheet" type="text/css">
<link href="//fonts.googleapis.com/css?family=Open+Sans:700italic" rel="stylesheet" type="text/css">
<![endif]-->

<script type="text/javascript">
 var MELODYURL = "{$smarty.const._URL}";
 var MELODYURL2 = "{$smarty.const._URL2}";
 var TemplateP = "{$smarty.const._URL}/templates/{$template_dir}";
 var _LOGGEDIN_ = {if $logged_in} true {else} false {/if};
</script>
{literal}
<script type="text/javascript">
 var pm_lang = {
	lights_off: "{/literal}{$lang.lights_off}{literal}",
	lights_on: "{/literal}{$lang.lights_on}{literal}",
	validate_name: "{/literal}{$lang.validate_name}{literal}",
	validate_username: "{/literal}{$lang.validate_username}{literal}",
	validate_pass: "{/literal}{$lang.validate_pass}{literal}",
	validate_captcha: "{/literal}{$lang.validate_captcha}{literal}",
	validate_email: "{/literal}{$lang.validate_email}{literal}",
	validate_agree: "{/literal}{$lang.validate_agree}{literal}",
	validate_name_long: "{/literal}{$lang.validate_name_long}{literal}",
	validate_username_long: "{/literal}{$lang.validate_username_long}{literal}",
	validate_pass_long: "{/literal}{$lang.validate_pass_long}{literal}",
	validate_confirm_pass_long: "{/literal}{$lang.validate_confirm_pass_long}{literal}",
	choose_category: "{/literal}{$lang.choose_category}{literal}",
 	validate_select_file: "{/literal}{$lang.upload_errmsg10}{literal}",
 	validate_video_title: "{/literal}{$lang.validate_video_title}{literal}",
	onpage_delete_favorite_confirm: "{/literal}{$lang.myfavorites_delete_alert_confirm}{literal}",
	please_wait: "{/literal}{$lang.please_wait}{literal}",
	// upload video page
	swfupload_status_uploaded: "{/literal}{$lang.swfupload_status_uploaded}{literal}",
	swfupload_status_pending: "{/literal}{$lang.swfupload_status_pending}{literal}",
	swfupload_status_queued: "{/literal}{$lang.swfupload_status_queued}{literal}",
	swfupload_status_uploading: "{/literal}{$lang.swfupload_status_uploading}{literal}",
	swfupload_file: "{/literal}{$lang.swfupload_file}{literal}",
	swfupload_btn_select: "{/literal}{$lang.swfupload_btn_select}{literal}",
	swfupload_btn_cancel: "{/literal}{$lang.swfupload_btn_cancel}{literal}",
	swfupload_status_error: "{/literal}{$lang.swfupload_status_error}{literal}",
	swfupload_error_oversize: "{/literal}{$lang.swfupload_error_oversize}{literal}",
	swfupload_friendly_maxsize: "{/literal}{$upload_limit}{literal}"
 }
 var theSummaries = new Array({/literal}{foreach from=$top_videos key=k item=video_data}"{smarty_fewchars s=$video_data.video_title length=120}",{/foreach}{literal}"تتت");
 var theSiteLinks = new Array({/literal}{foreach from=$top_videos key=k item=video_data}"{$video_data.video_href}",{/foreach}{literal}"ff");
</script>
{/literal}

<script type="text/javascript" src="{$smarty.const._URL}/templates/{$template_dir}/main_js/swfobject.js"></script>
<script type="text/javascript" src="{$smarty.const._URL}/templates/{$template_dir}/main_js/base.js"></script>
{if $facebook_image_src != ''}
    <link rel="image_src" href="{$facebook_image_src}" />
    <meta property="og:title" content="{$meta_title}" />
    <meta property="og:url" content="{$video_data.video_href}" />
    <meta property="og:description" content="{$meta_description}" />
    <meta property="og:image" content="{$facebook_image_src}" />
    {if $video_data.source_id == 1}
        <link rel="video_src" href="{$smarty.const._URL2}/videos.php?vid={$video_data.uniq_id}"/>
        <meta property="og:video:url" content="{$smarty.const._URL2}/videos.php?vid={$video_data.uniq_id}" />
    {/if}
{/if}
<style type="text/css">{$theme_customizations}</style>
{if isset($mm_header_inject)}{$mm_header_inject}{/if}
</head>
{if $tpl_name == "video-category"}
<body class="video-category catid-{$cat_id} page-{$gv_pagenumber}">
{elseif $tpl_name == "video-watch"}
<body class="video-watch videoid-{$video_data.id} author-{$video_data.author_user_id} source-{$video_data.source_id}{if $video_data.featured == 1} featured{/if}{if $video_data.restricted == 1} restricted{/if}">
{elseif $tpl_name == "article-category"}
<body class="article-category catid-{$cat_id}">
{elseif $tpl_name == "article-read"}
<body class="article-read articleid-{$article.id} author-{$article.author} {if $article.featured == 1} featured{/if}{if $article.restricted == 1} restricted{/if}">
{elseif $tpl_name == "page"}
<body class="page pageid-{$page.id} author-{$page.author}">
{else}
<body>
{/if}
{if $maintenance_mode}
	<div class="alert alert-danger" align="center"><strong>Currently running in maintenance mode.</strong></div>
{/if}
{if isset($mm_body_top_inject)}{$mm_body_top_inject}{/if}

 <div id="headerClock">
 <center>
 تاریخ امروز : <date style="letter-spacing: 2px;"></date>
 </center>
 </div>
 
 <div id="headerNews">
 <span>آخرین ویدیوها : </span><span id="theTicker" style="color:#fff;">&nbsp;</span>
 <script type="text/javascript" src="{$smarty.const._URL}/templates/{$template_dir}/main_js/ticker.js"></script>
 </div>
 
 <div style="clear:both;"></div>

<header class="wide-header" id="overview">
<div class="row-fluid fixed960">
    <div style="margin-top: 10px;float: right;">
	  {if $_custom_logo_url != ''}
	  	<a href="{$smarty.const._URL}/index.{$smarty.const._FEXT}" rel="home"><img src="{$_custom_logo_url}" alt="{$smarty.const._SITENAME}" title="{$smarty.const._SITENAME}" border="0" /></a>
	  {else}
      	<h1 class="site-title"><a href="{$smarty.const._URL}/index.{$smarty.const._FEXT}" rel="home">{$smarty.const._SITENAME}</a></h1>
	  {/if}
   </div>
   <div class="wide-header-text">
پارسیان کلیپ چیست!؟
پارسـیـان کلـیـپ یک سـامـانـه تحـت وب می باشد که به شما کمک می کند تا برای خود شبکه نمایش راه اندازی کنید و ویدیوهای مورد علاقـیتـان را از سامانه های اشتراک ویدیو در آن قرار دهید ! تا دیگران مـشاهده و نظراتشان را برای شما ارسـال کنـنـد.
   </div>
   <div class="wide-header-pad">
    {if $p == "article"}
    <form action="{$smarty.const._URL}/article.php" method="get" id="search" name="search" onSubmit="return validateSearch('true');">
    <div class="controls">
      <div class="input-append">
        <input class="span10 pm-search-field" id="appendedInputButton" size="16" name="keywords" type="text" placeholder="{$lang.submit_search}..." x-webkit-speech speech onwebkitspeechchange="this.form.submit();"><button class="btn" type="submit"><i class="icon-search"></i></button>
      </div>
    </div>
    </form>
    {else}
    <form action="{$smarty.const._URL}/search.php" method="get" id="search" name="search" onSubmit="return validateSearch('true');">
    <div class="controls">
      <div class="input-append">
        <input class="span10 pm-search-field" id="appendedInputButton" size="16" name="keywords" type="text" placeholder="{$lang.submit_search}..." x-webkit-speech="x-webkit-speech" onwebkitspeechchange="this.form.submit();" {if $smarty.const._SEARCHSUGGEST == 1}onblur="fill();" autocomplete="off"{/if}>
		<button class="btn" type="submit"><i class="icon-search"></i></button>
      </div>
      <div class="suggestionsBox" id="suggestions" style="display: none;">
          <div class="suggestionList input-xlarge" id="autoSuggestionsList">
          </div>
      </div>
    </div>
    </form>
    {/if}
   </div>

    <div class="hidden-phone">
    <div id="user-pane">
        <div class="user-data">
        {if $logged_in != '1'}
			<span class="avatar-img avatar-generic">
			<a class="primary ajax-modal" data-toggle="modal" data-backdrop="true" data-keyboard="true" href="#header-login-form" rel="tooltip" title="{$lang.login}"><img src="{$smarty.const._URL}/templates/{$template_dir}/img/pm-avatar.png" width="40" height="40" alt=""></a>
			</span>
			<span class="greet-links">
				<div class="ellipsis"><strong>{$lang._welcome}</strong></div>
				<span class=""><!--class="avatar-img"--><a class="primary ajax-modal" data-toggle="modal" data-backdrop="true" data-keyboard="true" href="#header-login-form">{$lang.login}</a>{if $allow_registration == '1'} / <a href="{$smarty.const._URL}/register.{$smarty.const._FEXT}">{$lang.register}</a>{/if}</span>
			</span>
			</div>
        {else}
			<span class="avatar-img">
			{if $smarty.const._MOD_SOCIAL && $logged_in && $notification_count > 0}
				<span class="notifications">{$notification_count}</span>
			{else}
			{/if}
			<a href="#" id="notification_counter" title="{$lang.notifications}"><img src="{$s_avatar_url}" width="40" height="40" alt=""></a>
			</span>
			
			<span class="greet-links">
			<div class="ellipsis"><strong><a href="{$smarty.const._URL}/profile.php?u={$s_username}">{$s_name}</a></strong></div>
			{if $smarty.const._ALLOW_USER_SUGGESTVIDEO == '1'}<a href="{$smarty.const._URL}/suggest.{$smarty.const._FEXT}">{$lang.suggest}</a>{/if}{if $smarty.const._ALLOW_USER_UPLOADVIDEO == '1' && $smarty.const._ALLOW_USER_SUGGESTVIDEO == '1'} / {/if}{if $smarty.const._ALLOW_USER_UPLOADVIDEO == '1'} <a href="{$smarty.const._URL}/upload.{$smarty.const._FEXT}">{$lang.upload}</a>{/if}
			</span>
			</div>
			
			<div class="user-menu dropdown">
			<a class="dropdown-toggle" data-toggle="dropdown" data-target="#" href="#"><i class="icon-chevron-down"></i></a>
				<ul class="dropdown-menu pull-right pm-ul-user-menu" role="menu" aria-labelledby="dLabel">
				{if $is_admin == 'yes' || $is_moderator == 'yes' || $is_editor == 'yes'}
				<li><a href="{$smarty.const._URL}/admin/index.php">{$lang.admin_area}</a></li>
				{/if}
				<li><a tabindex="-1" href="{$smarty.const._URL}/edit_profile.{$smarty.const._FEXT}">{$lang.edit_profile}</a></li>
				{if $smarty.const._ALLOW_USER_SUGGESTVIDEO == '1'}
				<li><a tabindex="-1" href="{$smarty.const._URL}/suggest.{$smarty.const._FEXT}">{$lang.suggest}</a></li>
				{/if}
				{if $smarty.const._ALLOW_USER_UPLOADVIDEO == '1'}
				<li><a tabindex="-1" href="{$smarty.const._URL}/upload.{$smarty.const._FEXT}">{$lang.upload_video}</a></li>
				{/if}
				<li><a tabindex="-1" href="{$smarty.const._URL}/favorites.php?a=show">{$lang.my_favorites}</a></li>
				<li><a tabindex="-1" href="{$smarty.const._URL}/memberlist.{$smarty.const._FEXT}">{$lang.members_list}</a></li>
				{if isset($mm_menu_logged_inject)}{$mm_menu_logged_inject}{/if}
				<li class="divider"></li>
				<li><a tabindex="-1" href="{$smarty.const._URL}/login.{$smarty.const._FEXT}?do=logout">{$lang.logout}</a></li>
				</ul>
			</div>
        {/if}
    
        {if ! $logged_in}
        <div class="modal hide" id="header-login-form" role="dialog" aria-labelledby="header-login-form-label"> <!-- login modal -->
            <div class="modal-header">
                 <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                  <h3 id="header-login-form-label">ورود به سایت</h3>
            </div>
            <div class="modal-body">
                <p></p>
                {include file="user-auth-login-form.tpl"}
            </div>
        </div>
        {/if}
	{if $smarty.const._MOD_SOCIAL && $logged_in}<!--//$notification_count > 0}-->
		<div class="hide" id="notification_temporary_display_container"></div>
	{/if}
	    </div><!--.user-data-->
    </div><!--#user-pane-->
</div>
</header>
<nav class="wide-nav">
    <div class="row-fluid fixed960">
        <span class="span12" style="height: -0px;">
		<div class="navbar">
              <div class="navbar-inner">
                <div class="container">
                  <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                  </a>
                  <div class="nav-collapse">
                    <ul class="nav">
                    
                      <li><a href="{$smarty.const._URL}/index.{$smarty.const._FEXT}" class="wide-nav-link"><i class="icon-home"></i>{$lang.homepage}</a></li>
                      <li class="dropdown">
                        <a href="#" class="dropdown-toggle wide-nav-link" data-toggle="dropdown"><i class="icon-th-list"></i>{$lang.category} <b class="caret"></b></a>
                        <ul class="dropdown-menu">
                        {dropdown_menu_video_categories max_levels=3}
                        </ul>
                      </li>
                      
                      {if $smarty.const._MOD_ARTICLE == 1}
                      <li class="dropdown">
                        <a href="#" class="dropdown-toggle wide-nav-link" data-toggle="dropdown"><i class="icon-home"></i>{$lang.articles} <b class="caret"></b></a>
                        <ul class="dropdown-menu">
                        {dropdown_menu_article_categories max_levels=3}
                        </ul>
                      </li>
					  {/if}
                      <li><a href="{$smarty.const._URL}/topvideos.{$smarty.const._FEXT}" class="wide-nav-link"><i class="icon-th"></i>{$lang.top_videos}</a></li>
                      <li><a href="{$smarty.const._URL}/newvideos.{$smarty.const._FEXT}" class="wide-nav-link"><i class="icon-th"></i>{$lang.new_videos}</a></li>
                      <li><a href="{$smarty.const._URL}/randomizer.php" rel="nofollow" class="wide-nav-link"><i class="icon-th"></i>{$lang.random_video}</a></li>
                      {if isset($mm_menu_always_inject1)}{$mm_menu_always_inject1}{/if}		
                      <li><a href="{$smarty.const._URL}/contact_us.{$smarty.const._FEXT}" class="wide-nav-link"><i class="icon-envelope"></i>{$lang.contact_us}</a></li>
                      {if isset($mm_menu_always_inject2)}{$mm_menu_always_inject2}{/if}		
                      {if $logged_in != 1 && isset($mm_menu_notlogged_inject)}{$mm_menu_notlogged_inject}{/if}
                    </ul>
                    {if is_array($links_to_pages) && !empty($links_to_pages) }
                    <ul class="nav pull-right pm-ul-pages">
                      <li class="dropdown">
                        <a href="#" class="dropdown-toggle wide-nav-link" data-toggle="dropdown"><i class="icon-home"></i>{$lang.pages} <b class="caret"></b></a>
                        <ul class="dropdown-menu">
                              {foreach from=$links_to_pages key=k item=page_data}
                                <li><a href="{$page_data.page_url}">{$page_data.title}</a></li>
                              {/foreach}
                        </ul>
                      </li>
					{if $logged_in != '1'}
					{if $allow_registration == '1'}
					  <li><a href="{$smarty.const._URL}/register.{$smarty.const._FEXT}" class="btn-register border-radius2"><i class="icon-home"></i>{$lang.register}</a></li>
					{/if}
					{/if}
                    </ul>
                    {/if}

                  </div><!-- /.nav-collapse -->
                </div>
              </div><!-- /navbar-inner -->
            </div><!-- /navbar -->
       </span>
    </div>
</nav>
<a id="top"></a>
{if $ad_1 != ''}
<div class="pm-ad-zone" align="center">{$ad_1}</div>
{/if}