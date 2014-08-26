{include file='header.tpl' p="index"} 
<div id="wrapper">
    <div class="container-fluid">
      <div class="row-fluid">
        <div class="span8" style="margin-right: 15px;">
			<div id="primary" style="padding:0;">
				<div id="pm-featured" class="border-radius3">
				<h2>
                <div style="
    float: right;
    background-color: #DD6262;
    padding-left: 5px;
    padding-right: 5px;
    color: #fff;
    padding-bottom: 5px;">{$lang.featured} : </div> &nbsp; {$voth_title} </h2>
    <div style="clear:both"></div>
				{if $display_preroll_ad == true}
					<div id="preroll_placeholder">
						<div class="preroll_countdown">
							{$lang.preroll_ads_timeleft} <span class="preroll_timeleft">{$preroll_ad_data.timeleft_start}</span>
						</div>
						{$preroll_ad_data.code}
						
						{if $preroll_ad_data.skip}
						<div class="preroll_skip_countdown">
							{$lang.preroll_ads_skip_msg} <span class="preroll_skip_timeleft">{$preroll_ad_data.skip_delay_seconds}</span>
						</div>
						<br />
						<button class="btn btn-blue hide" id="preroll_skip_btn">{$lang.preroll_ads_skip}</button>
						{/if}
						{if $preroll_ad_data.disable_stats == 0}
							<img src="{$smarty.const._URL}/ajax.php?p=stats&do=show&aid={$preroll_ad_data.id}&at={$smarty.const._AD_TYPE_PREROLL}" width="1" height="1" border="0" />
						{/if}
					</div>
				{else}
					{include file="player.tpl" page="index"}
				{/if}
				</div>
				{if $total_playingnow > 0}
				<div id="pm-wn">
					<h2 class="upper-blue">{$lang.vbwrn}</h2>
					<div class="btn-group btn-group-sort pm-slide-control">
					<button class="btn btn-mini prev" id="pm-slide-prev"><i class="pm-vc-sprite arr-l"></i></button>
					<button class="btn btn-mini next" id="pm-slide-next"><i class="pm-vc-sprite arr-r"></i></button>
					</div>
					<div id="pm-slide">
					<!-- Carousel items -->
					<ul class="pm-ul-wn-videos clearfix" id="pm-ul-wn-videos">
					{foreach from=$playingnow key=k item=video_data}
					  <li>
						<div class="pm-li-wn-videos">
						<span class="pm-video-thumb pm-thumb-145 pm-thumb-top border-radius2">
						<a href="{$video_data.video_href}" class="pm-thumb-fix pm-thumb-145"><span class="pm-thumb-fix-clip"><img src="{$video_data.thumb_img_url}" alt="{$video_data.attr_alt}" width="160"><span class="vertical-align"></span></span></a>
						</span>
						<h3 dir="rtl"><a href="{$video_data.video_href}" class="pm-title-link">{smarty_fewchars s=$video_data.video_title length=25}</a></h3>
						</div>
					  </li>
					{/foreach}
					</ul>
					</div><!-- #pm-slide -->
				</div>
				<hr />
				<div class="clear-fix"></div>
		        {/if}

			<div class="element-videos">
			<div class="btn-group btn-group-sort">
			<button class="btn btn-small" id="list"><i class="icon-th"></i> </button>
			<button class="btn btn-small" id="grid"><i class="icon-th-list"></i> </button>
			</div>
				<h2 class="upper-blue">{$lang.new_videos}</h2>
                <div style="clear:both"></div>
                <div style=" border-bottom: 5px #477E84 solid; margin-top: -10px;"></div>
				<ul class="pm-ul-browse-videos thumbnails" id="pm-grid">
				{foreach from=$new_videos key=k item=video_data}
				  <li>
					<div class="pm-li-video">
						<span class="pm-video-thumb pm-thumb-145 pm-thumb border-radius2">
						<span class="pm-video-li-thumb-info">
						{if $video_data.yt_length != 0}<span class="pm-label-duration border-radius3 opac7">{$video_data.duration}</span>{/if}
						{if $video_data.mark_new}<span class="label label-new">{$lang._new}</span>{/if}
						{if $video_data.mark_popular}<span class="label label-pop">{$lang._popular}</span>{/if}
						</span>
						<a href="{$video_data.video_href}" class="pm-thumb-fix pm-thumb-145"><span class="pm-thumb-fix-clip"><img src="{$video_data.thumb_img_url}" alt="{$video_data.attr_alt}" width="160"><span class="vertical-align"></span></span></a>
						</span>
						<h3 dir="rtl"><a href="{$video_data.video_href}" class="pm-title-link" title="{$video_data.attr_alt}">{smarty_fewchars s=$video_data.video_title length=25}</a></h3>
						<div class="pm-video-attr">
							<p class="pm-video-attr-author" style="margin: 0px;">{$lang.articles_by} <a href="{$video_data.author_profile_href}">{$video_data.author_name}</a></p>
							<p class="pm-video-attr-since"><small>{$lang.added} <time datetime="{$video_data.html5_datetime}" title="{$video_data.full_datetime}">{$video_data.time_since_added} {$lang.ago}</time></small></p>
							<p class="pm-video-attr-numbers"><small>{$video_data.views_compact} {$lang.views} / {$video_data.likes_compact} {$lang._likes}</small></p>
						</div>
						{if $video_data.featured}
						<span class="pm-video-li-info">
							<span class="label label-featured">{$lang._feat}</span>
						</span>
						{/if}
					</div>
				  </li>
				{foreachelse}
					{$lang.top_videos_msg2}
				{/foreach}
				</ul>
			</div><!-- .element-videos -->
			
			<div class="clearfix"></div>
			</div><!-- #primary -->
        </div><!-- .span8 -->

        <div class="span4" id="secondary">
        {if $ad_5 != ''}
		<div class="widget">
        	<div class="pm-ad-zone" align="center">{$ad_5}</div>
        </div><!-- .widget -->
        {/if}

        <div class="widget">
            <div class="btn-group btn-group-sort pm-slide-control"  style="margin-left:-4px;">
            <button class="btn btn-mini next" id="pm-slide-top-next"><i class="pm-vc-sprite arr-r"></i></button>
            <button class="btn btn-mini prev" id="pm-slide-top-prev"><i class="pm-vc-sprite arr-l"></i></button>
            </div>
            <h4>{$lang.top_m_videos}</h4>
            <div style="clear:both"></div>
            <div style=" border-bottom: 5px #477E84 solid; margin-top: -10px;"></div>
            <ul class="pm-ul-top-videos" id="pm-ul-top-videos">
			{foreach from=$top_videos key=k item=video_data}
			  <li>
				<div class="pm-li-top-videos">
				<span class="pm-video-thumb pm-thumb-106 pm-thumb-top border-radius2">
				<span class="pm-video-li-thumb-info">
				{if $video_data.yt_length != 0}<span class="pm-label-duration border-radius3 opac7">{$video_data.duration}</span>{/if}
				</span>
				<a href="{$video_data.video_href}" class="pm-thumb-fix pm-thumb-106"><span class="pm-thumb-fix-clip"><img src="{$video_data.thumb_img_url}" alt="{$video_data.attr_alt}" width="106"><span class="vertical-align"></span></span></a>
				</span>
				<h3 dir="rtl"><a href="{$video_data.video_href}" class="pm-title-link">{smarty_fewchars s=$video_data.video_title length=30}</a></h3>
				<span class="pm-video-attr-numbers">
                <small>{$lang.articles_by} <a href="{$video_data.author_profile_href}">{$video_data.author_name}</a></small> <br />
                <small>{$video_data.views_compact} {$lang.views} / {$video_data.likes_compact} {$lang._likes} (
                <time datetime="{$video_data.html5_datetime}" title="{$video_data.full_datetime}">{$video_data.time_since_added} {$lang.ago}</time>
                )</small>
                </span>
				</div>
			  </li>
			{/foreach}
            </ul>
            <div class="clearfix"></div>
        </div><!-- .widget -->
        
        {if ($show_tags == 1) && (count($tags) > 0)}
		<div class="widget">
			<h4>{$lang.tags}</h4>
            <div style="clear:both"></div>
            <div style=" border-bottom: 5px #477E84 solid; margin-top: -10px;"></div>
            <div style="clear:both;"></div>
            <div style="margin-right:0px;">
            {foreach from=$tags item=tag key=k}
                {$tag.href}
            {/foreach}
            </div>
        </div><!-- .widget -->
        {/if}
        
        {if $show_stats == 1}
        <div class="widget">
        <h4>{$lang.site_stats}</h4>
        <div style="clear:both"></div>
        <div style=" border-bottom: 5px #477E84 solid; margin-top: -10px;"></div>
        <ul class="pm-stats-data">
        	<li><a style="margin-right: -84px;" href="{$smarty.const._URL}/memberlist.{$smarty.const._FEXT}?do=online">{$lang.online_users}</a> <span class="pm-stats-count">{$stats.online_users}</span></li>
            <li><a href="{$smarty.const._URL}/memberlist.{$smarty.const._FEXT}">{$lang.total_users}</a> <span class="pm-stats-count">{$stats.users}</span></li>
            <li>{$lang.total_videos} <span class="pm-stats-count">{$stats.videos}</span></li>
        	<li>{$lang.videos_added_lw} <span class="pm-stats-count">{$stats.videos_last_week}</span></li>
        </ul>
		</div><!-- .widget -->
        {/if}
        
        {if $smarty.const._MOD_ARTICLE == 1}
        <div class="widget">
			<h4>{$lang.articles_latest}</h4>
            <div style="clear:both"></div>
            <div style=" border-bottom: 5px #477E84 solid; margin-top: -10px;"></div>
            <ul class="pm-ul-home-articles" id="pm-ul-home-articles">
            {foreach from=$articles item=article key=id}
				<li {if $article.featured == '1'}class="sticky-article"{/if}>
				<article>
				{if $article.meta._post_thumb_show != ''}
				<div class="pm-article-thumb">
					<a href="{$article.link}" class="pm-title-link" title="{$article.title}"><img src="{$smarty.const._ARTICLE_ATTACH_DIR}/{$article.meta._post_thumb_show}" align="left" width="55" height="55" border="0" alt="{$article.title}"></a>
				</div>
				{/if}
				<h6 dir="rtl" class="ellipsis"><a href="{$article.link}" class="pm-title-link" title="{$article.title}">{smarty_fewchars s=$article.title length=92}</a></h6>
				<p class="pm-article-preview">
					{if $article.meta._post_thumb_show == ''}
						<span class="minDesc">{smarty_fewchars s=$article.excerpt length=130}</span>
					{else}
						<span class="minDesc">{smarty_fewchars s=$article.excerpt length=100}</span>
					{/if}
				</p>
				</article>
				</li>
            {/foreach}
            </ul>
        </div><!-- .widget -->
        {/if}
		</div><!-- .span4 -->
      </div><!-- .row-fluid -->
    </div><!-- .container-fluid -->
{include file='footer.tpl' p="index"} 