{include file="header.tpl" p="detail" tpl_name="video-watch"}
<div id="wrapper">
{if $show_addthis_widget == '1'}
{include file='widget-addthis.tpl'}
{/if}
    <div class="container-fluid">
	<div class="row-fluid">
        <div class="span8" style="margin-right:15px;width: 659px;">
		<div id="primary" style="padding:0;" itemprop="video" itemscope itemtype="http://schema.org/VideoObject">
<div class="row-fluid">
	<div class="span12">
    	<div class="pm-video-head">
        {if $logged_in && $is_admin == 'yes'}
        <div class="btn-action-group pull-right">
        <a href="#" onclick="return confirm_action('Are you sure you want to delete this video?', '{$smarty.const._URL}/admin/modify.php?vid={$video_data.uniq_id}&a=1'); return false;" rel="tooltip" class="btn btn-mini btn-danger" title="{$lang.delete} ({$lang._admin_only})" target="_blank">{$lang.delete}</a> <a href="{$smarty.const._URL}/admin/modify.php?vid={$video_data.uniq_id}" rel="tooltip" class="btn btn-mini" title="{$lang.edit} ({$lang._admin_only})" target="_blank">{$lang.edit}</a>
        </div>
        {/if}
        <h1 class="entry-title" itemprop="name" style="float:right;margin: 0px;">{$video_data.video_title}
        {if $video_data.featured == 1}<span style="float:right;" class="label label-featured"><div style="font: 14px '';padding-bottom: 1px;">{$lang.featured}</div></span>{/if}
        </h1>
		<meta itemprop="duration" content="{$video_data.iso8601_duration}" />
		<meta itemprop="thumbnailUrl" content="{$video_data.thumb_img_url}" />
		<meta itemprop="contentURL" content="{$smarty.const._URL2}/videos.php?vid={$video_data.uniq_id}" />
		<meta itemprop="embedURL" content="{$video_data.embed_href}" />
		<meta itemprop="uploadDate" content="{$video_data.html5_datetime}" />

        <div class="row-fluid" style="width: 200px;float: left;">
            <div class="span6" style="float: left;width: 200px;">
            <ul class="pm-video-adjust">
                <li><a id="player_extend" href="#"><i class="icon-resize-full opac7"></i> {$lang.resize}</a></li>
                <li><div id="lights-div"><a class="lightOn" href="#">{$lang.lights_off}</a></div></li>
            <ul>
            </div>
        </div>
        </div><!--.pm-video-head-->
        <div style="clear:both"></div>
        <div class="pm-player-full-width">
	   	    <div id="video-wrapper">
            {if $display_preroll_ad == true}
            <div id="preroll_placeholder" class="border-radius4">
				<div class="preroll_countdown">
				{$lang.preroll_ads_timeleft} <span class="preroll_timeleft">{$preroll_ad_data.timeleft_start}</span>
				</div>
				{$preroll_ad_data.code}
				{if $preroll_ad_data.skip}
				<div class="preroll_skip_countdown">
				   {$lang.preroll_ads_skip_msg} <span class="preroll_skip_timeleft">{$preroll_ad_data.skip_delay_seconds}</span>
				</div>
				<div class="preroll_skip_button">
				<button class="btn btn-blue hide" id="preroll_skip_btn">{$lang.preroll_ads_skip}</button>
				</div>
				{/if}
				{if $preroll_ad_data.disable_stats == 0}
					<img src="{$smarty.const._URL}/ajax.php?p=stats&do=show&aid={$preroll_ad_data.id}&at={$smarty.const._AD_TYPE_PREROLL}" width="1" height="1" border="0" />
				{/if}
            </div>
            {else}
                        {include file="player.tpl" page="detail"}
            {/if}
	        </div><!--#video-wrapper-->


            <div class="pm-video-control">
            <div class="row-fluid">
                <div class="span6">
                    <button class="btn btn-small border-radius0 btn-video {if $bin_rating_vote_value == 1}active{/if}" id="bin-rating-like" type="button">{$lang._like}</button>
                    <button class="btn btn-small border-radius0 btn-video {if $bin_rating_vote_value == 0 && $bin_rating_vote_value !== false}active{/if}" id="bin-rating-dislike" type="button">{$lang._dislike}</button>
                </div>
                
                <div style="float: left;">
                	<div class="pull-right">
                        <button class="btn btn-small border-radius0 btn-video" type="button" data-toggle="button" id="pm-vc-share">{$lang._share}</button>
                        <input type="hidden" name="bin-rating-uniq_id" value="{$video_data.uniq_id}">
                        {if $logged_in}
                            
                            {if $isfavorite == '1'}
                            <!--{$lang.dp_alt_1}-->
                            <form name="addtofavorites" id="addtofavorites" class="form-inline" action="">
                                <input type="hidden" value="{$video_data.uniq_id}" name="fav_video_id">
                                <input type="hidden" value="{$s_user_id}" name="fav_user_id">
                                <button class="btn btn-small border-radius0 btn-video active" id="fav_save_button" type="button">{$lang.remove_from_fav}</button>
                            </form>
                            {elseif $smarty.const._FAV_LIMIT == $countfavorites}
                             <a href="{$smarty.const._URL}/favorites.php?a=show" class="btn btn-small border-radius0">{$lang.dp_alt_2}</a>
                            {else}
                            <form name="addtofavorites" id="addtofavorites" class="form-inline" action="">
                                <input type="hidden" value="{$video_data.uniq_id}" name="fav_video_id">
                                <input type="hidden" value="{$s_user_id}" name="fav_user_id">
                                <button class="btn btn-small border-radius0 btn-video" id="fav_save_button" type="button">{$lang.add_to_fav}</button>
                            </form>
                            {/if}
                        {else}
                          <!--{$lang.dp_alt_1}-->
                        {/if}
                        <button class="btn btn-small border-radius0 btn-video" type="button" data-toggle="button" id="pm-vc-report" title="{$lang.report_video}">ارسال گزارش</button>
					</div>
                </div>
            </div>
            </div><!--.pm-video-control-->

            <div id="bin-rating-response" class="hide well well-small"></div>
            <div id="bin-rating-like-confirmation" class="hide well well-small alert alert-well">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <p>{$lang.confirm_like}</p>
                <div class="row-fluid">
                    <div class="panel-1">
                    <a href="http://www.facebook.com/sharer.php?u={$facebook_like_href}&amp;t={$facebook_like_title}" onclick="javascript:window.open(this.href,
      '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;" rel="tooltip" title="اشتراک در فیسبوک"><i class="pm-vc-sprite facebook-icon"></i></a>
                    <a href="http://twitter.com/home?status=Watching%20{$facebook_like_title}%20on%20{$facebook_like_href}" onclick="javascript:window.open(this.href,
      '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;" rel="tooltip" title="اشتراک در توییتر"><i class="pm-vc-sprite twitter-icon"></i></a>
                    <a href="https://plus.google.com/share?url={$facebook_like_href}" onclick="javascript:window.open(this.href,
      '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;" rel="tooltip" title="اشتراک در گوگل پلاس"><i class="pm-vc-sprite google-icon"></i></a>
                    </div>
                    <div class="panel-3">
                    <div class="input-prepend"><span class="add-on">لینک</span><input name="share_video_link" id="share_video_link" type="text" value="{$video_data.video_href}" class="span11" onClick="SelectAll('share_video_link');"></div>
                    </div>
                </div>
            </div>
            <div id="bin-rating-dislike-confirmation" class="hide-dislike hide well well-small">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <p>{$lang.confirm_dislike}</p>
            </div><!--#bin-rating-like-confirmation-->
    
    
            <div id="pm-vc-report-content" class="hide well well-small alert alert-well">
                <div id="report-confirmation" class="hide"></div>
                <form name="reportvideo" action="" method="POST" class="form-inline">
                  <input type="hidden" id="name" name="name" class="input-small" value="{if $logged_in}{$s_name}{/if}">
                  <input type="hidden" id="email" name="email" class="input-small" value="{if $logged_in}{$s_email}{/if}">
                
                  <select name="reason" class="input-medium inp-small">
                    <option value="{$lang.report_form1}" selected="selected">{$lang.report_form1}</option>
                    <option value="{$lang.report_form4}">{$lang.report_form4}</option>
                    <option value="{$lang.report_form5}">{$lang.report_form5}</option>
                    <option value="{$lang.report_form6}">{$lang.report_form6}</option>
                    <option value="{$lang.report_form7}">{$lang.report_form7}</option>
                  </select>
                  
                  {if ! $logged_in}
                    <input type="text" name="imagetext" class="input-small inp-small" autocomplete="off" placeholder="{$lang.confirm_comment}">
                    <img src="{$smarty.const._URL}/include/securimage_show.php?sid={echo_securimage_sid}" id="securimage-report" alt="" class="border-radius3">
                    <button class="btn btn-small btn-link" onclick="document.getElementById('securimage-report').src = '{$smarty.const._URL}/include/securimage_show.php?sid=' + Math.random(); return false;"><i class="icon-refresh"></i> </button>
                  {/if}
                  <button type="submit" name="Submit" class="btn btn-danger" value="{$lang.submit_send}">{$lang.report_video}</button>
                  <input type="hidden" name="p" value="detail">
                  <input type="hidden" name="do" value="report">
                  <input type="hidden" name="vid" value="{$video_data.uniq_id}">
                </form>
            </div><!-- #pm-vc-report-content-->
    
            <div id="pm-vc-share-content" class="alert alert-well">
                <div class="row-fluid">
                    <div class="panel-3">
                    <a href="http://www.facebook.com/sharer.php?u={$facebook_like_href}&amp;t={$facebook_like_title}" onclick="javascript:window.open(this.href,
      '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;" rel="tooltip" title="اشتراک در فیسبوک"><i class="pm-vc-sprite facebook-icon"></i></a>
                    <a href="http://twitter.com/home?status=Watching%20{$facebook_like_title}%20on%20{$facebook_like_href}" onclick="javascript:window.open(this.href,
      '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;" rel="tooltip" title="اشتراک در توییتر"><i class="pm-vc-sprite twitter-icon"></i></a>
                    <a href="https://plus.google.com/share?url={$facebook_like_href}" onclick="javascript:window.open(this.href,
      '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;" rel="tooltip" title="اشتراک در گوگل پلاس"><i class="pm-vc-sprite google-icon"></i></a>  
                    </div>
                    <div class="panel-2">
                    <button class="btn border-radius0 btn-video" type="button" id="pm-vc-embed">{$lang._embed}</button>
                    <button class="btn border-radius0 btn-video" type="button" data-toggle="button" id="pm-vc-email"><i class="icon-envelope"></i> {$lang.email_video}</button>
                    </div>
                    <div class="panel-1">
                    <div class="input-prepend"><span class="add-on">لینک</span><input name="video_link" id="video_link" dir="ltr" type="text" value="{$video_data.video_href}" class="input-medium" onClick="SelectAll('video_link');"></div>
                    </div>
                </div>
    
                <div id="pm-vc-embed-content">
                  <hr />
                  <textarea name="pm-embed-code" style="margin: 0px 0px 9px;width: 637px;max-width: 637px;height: 92px;max-height: 92px;direction: ltr;font-family: monospace;" id="pm-embed-code" rows="3" class="span12" onClick="SelectAll('pm-embed-code');">{$embedcode_to_share}</textarea>
                </div>
                <div id="pm-vc-email-content">
                    <hr />
                    <div id="share-confirmation" class="hide well well-small"></div>
                    <form name="sharetofriend" action="" method="POST" class="form-inline">
                      <input type="text" id="name" name="name" class="input-small inp-small" value="{$s_name}" placeholder="{$lang.your_name}">
                      <input type="text" id="email" style="width: 180px;" name="email" class="input-small inp-small" placeholder="{$lang.friends_email}">
                      {if ! $logged_in}   
                          <input type="text" name="imagetext" class="input-small inp-small" autocomplete="off" placeholder="{$lang.confirm_comment}">
                          <img src="{$smarty.const._URL}/include/securimage_show.php?sid={echo_securimage_sid}" id="securimage-share" alt="">
                          <button class="btn btn-small btn-link" onclick="document.getElementById('securimage-share').src = '{$smarty.const._URL}/include/securimage_show.php?sid=' + Math.random(); return false;"><i class="icon-refresh"></i> </button>
                      {/if}
                      <input type="hidden" name="p" value="detail">
                      <input type="hidden" name="do" value="share">
                      <input type="hidden" name="vid" value="{$video_data.uniq_id}">
                      <button type="submit" name="Submit" class="btn btn-success">{$lang.submit_send}</button>
                    </form>
                </div>
            </div><!-- #pm-vc-share-content -->
            
			<div class="row-fluid pm-author-data">
                <div class="span2">
                    <span class="pm-avatar"><a href="{$video_data.author_profile_href}"><img src="{$video_data.author_avatar_url}" height="50" width="50" alt="" class="img-polaroid" border="0"></a></span>
                </div>

                <div style="width: 440px;float: right;padding-right: 10px;">
                	<div class="pm-submit-data">{$lang.articles_published} <time datetime="{$video_data.html5_datetime}" title="{$video_data.full_datetime}">{$video_data.time_since_added} {$lang.ago}</time> {$lang.articles_by} <a href="{$smarty.const._URL}/profile.{$smarty.const._FEXT}?u={$video_data.author_username}">{$video_data.author_name}</a> {$lang._in} {$category_name}</div>   
                    <div class="clearfix"></div>
                    {if $smarty.const._MOD_SOCIAL && $logged_in == '1' && $video_data.author_user_id != $s_user_id}
                        {include file="user-follow-button.tpl" profile_data=$video_data profile_user_id=$video_data.author_user_id}
                    {/if}
                </div>

                <div class="pm-video-views-count pull-right">
                    <span class="pm-vc-views">
                    <small>{$video_data.site_views_formatted} {$lang.views}</small>
                    </span>
                    <div class="clearfix"></div>
                    <div class="progress" title="{$video_data.up_vote_count_formatted} {$lang._likes}, {$video_data.down_vote_count_formatted} {$lang._dislikes}">
                      <div class="bar bar-success" id="rating-bar-up-pct" style="width: {$video_data.up_pct}%;"></div>
                      <div class="bar bar-danger" id="rating-bar-down-pct" style="width: {$video_data.down_pct}%;"></div>
                    </div>
                </div><!--.pm-video-control-->
            </div><!--.pm-author-data-->
        </div><!--end pm-player-full-width -->
	</div>
</div>

{if !empty($video_data.description)}
<h2 class="upper-blue">{$lang.description}</h2>
<div style="clear:both"></div>
<div style=" border-bottom: 5px #477E84 solid; margin-top: -10px;"></div>
<div class="description text-exp">
    <p itemprop="description">{$video_data.description}</p>
    <p class="show-more"><a href="#" class="show-now">{$lang.show_more}</a></p>
</div>
{/if}

{if !empty($tags)}
<div class="video-tags">
	<strong>{$lang.tags}</strong>: {$tags}
</div>
{/if}

{if $video_data.allow_comments == '1'}
<h2 class="upper-blue">{$lang.post_comment}</h2>
<div style="clear:both"></div>
<div style=" border-bottom: 5px #477E84 solid; margin-top: -10px;"></div>
<div style="clear:both"></div>
{include file='comment-form.tpl'}
{if ! $logged_in && ! $guests_can_comment}
	{$must_sign_in}
{/if}
{/if}

<h2 class="upper-blue">{$lang.comments}</h2>
<div style="clear:both"></div>
<div style=" border-bottom: 5px #477E84 solid; margin-top: -10px;"></div>
<div style="clear:both"></div>
<div class="pm-comments comment_box">
{if $video_data.allow_comments == '1'}
{if $comment_count == 0}
    <ul class="pm-ul-comments">
    	<li id="preview_comment"></li>
    </ul>
    <div id="be_the_first">{$lang.be_the_first}</div>
{else}
    <span id="comment-list-container">
		{include file="comment-list.tpl" tpl_name="video-watch"}
		<!-- comment pagination -->
		{if $comment_pagination_obj != ''}
			{include file="comment-pagination.tpl"}
		{/if}
	</span>
{/if}
{else}
	<div>{$lang.comments_disabled}</div>
{/if}
</div>
		</div><!-- #primary -->
        </div><!-- .span8 -->
        
        <div class="pm-sidebar">
		<div id="secondary" style="margin-left: 15px;width: 300px;margin-top: 4px;">
        {if $ad_3 != ''}{$ad_3}{/if}
        <div class="widget-related widget" id="pm-related">
          <ul class="nav nav-tabs" id="myTab">
            <li class="active"><a href="#bestincategory" data-target="#bestincategory" data-toggle="tab">{$lang.tab_related}</a></li>
            <li> / </li>
            <li><a href="#popular" data-target="#popular" data-toggle="tab">{$lang._popular}</a></li>
          </ul>
 
          <div id="pm-tabs" class="tab-content">
            <div class="tab-pane fade in active" id="bestincategory">
                <ul class="pm-ul-top-videos">
                
			{foreach from=$related_video_list key=k item=related_video_data}
			  <li>
				<div class="pm-li-top-videos">
				<span class="pm-video-thumb pm-thumb-106 pm-thumb-top border-radius2">
				<span class="pm-video-li-thumb-info">
				{if $related_video_data.duration != 0}<span class="pm-label-duration border-radius3 opac7">{$related_video_data.duration}</span>{/if}
				</span>
				<a href="{$related_video_data.video_href}" class="pm-thumb-fix pm-thumb-106"><span class="pm-thumb-fix-clip"><img src="{$related_video_data.thumb_img_url}" alt="{$related_video_data.attr_alt}" width="106"><span class="vertical-align"></span></span></a>
				</span>
				<h3 dir="rtl"><a href="{$related_video_data.video_href}" class="pm-title-link">{smarty_fewchars s=$related_video_data.video_title length=30}</a></h3>
				<span class="pm-video-attr-numbers">
                <small>{$lang.articles_by} <a href="{$video_data.author_profile_href}">{$related_video_data.author_name}</a></small> <br />
                <small>{$related_video_data.views_compact} {$lang.views} / {$related_video_data.likes_compact} {$lang._likes} (
                <time datetime="{$video_data.html5_datetime}" title="{$video_data.full_datetime}">{$related_video_data.time_since_added} {$lang.ago}</time>
                )</small>
                </span>
				</div>
			  </li>
            {foreachelse}
				  {$lang.top_videos_msg2}
			{/foreach}
           
                </ul>
            </div>
            <div class="tab-pane fade" id="popular">
                <ul class="pm-ul-top-videos">
                {foreach from=$popular_video_list key=k item=popular_video_data}
				  <li>
					<div class="pm-li-top-videos">
					<span class="pm-video-thumb pm-thumb-106 pm-thumb-top border-radius2">
					<span class="pm-video-li-thumb-info">
					{if $popular_video_data.duration != 0}<span class="pm-label-duration border-radius3 opac7">{$popular_video_data.duration}</span>{/if}
					</span>
                    <a href="{$popular_video_data.video_href}" class="pm-thumb-fix pm-thumb-106"><span class="pm-thumb-fix-clip"><img src="{$popular_video_data.thumb_img_url}" alt="{$popular_video_data.attr_alt}" width="106"><span class="vertical-align"></span></span></a>
					</span>
					<h3 dir="ltr"><a href="{$popular_video_data.video_href}" class="pm-title-link">{smarty_fewchars s=$popular_video_data.video_title length=30}</a></h3>
					<span class="pm-video-attr-numbers">
              		<small>{$lang.articles_by} <a href="{$popular_video_data.author_profile_href}">{$popular_video_data.author_name}</a></small> <br />
              	    <small>{$popular_video_data.views_compact} {$lang.views} / {$popular_video_data.likes_compact} {$lang._likes} (
                    <time datetime="{$popular_video_data.html5_datetime}" title="{$popular_video_data.full_datetime}">{$popular_video_data.time_since_added} {$lang.ago}</time>
                    )</small>
                    </span>
                   {if $popular_video_data.featured}
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
            </div>
          </div>
          
        </div><!-- .shadow-div -->
        
		</div><!-- #secondary -->
        </div><!-- #sidebar -->
      </div><!-- .row-fluid -->
    </div><!-- .container-fluid -->
{include file="footer.tpl" p="detail" tpl_name="video-watch"}