<?php /* Smarty version 2.6.20, created on 2014-06-24 07:09:09
         compiled from video-watch.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'echo_securimage_sid', 'video-watch.tpl', 141, false),array('function', 'smarty_fewchars', 'video-watch.tpl', 298, false),)), $this); ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "header.tpl", 'smarty_include_vars' => array('p' => 'detail','tpl_name' => "video-watch")));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<div id="wrapper">
<?php if ($this->_tpl_vars['show_addthis_widget'] == '1'): ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'widget-addthis.tpl', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php endif; ?>
    <div class="container-fluid">
	<div class="row-fluid">
        <div class="span8" style="margin-right:15px;width: 659px;">
		<div id="primary" style="padding:0;" itemprop="video" itemscope itemtype="http://schema.org/VideoObject">
<div class="row-fluid">
	<div class="span12">
    	<div class="pm-video-head">
        <?php if ($this->_tpl_vars['logged_in'] && $this->_tpl_vars['is_admin'] == 'yes'): ?>
        <div class="btn-action-group pull-right">
        <a href="#" onclick="return confirm_action('Are you sure you want to delete this video?', '<?php echo @_URL; ?>
/admin/modify.php?vid=<?php echo $this->_tpl_vars['video_data']['uniq_id']; ?>
&a=1'); return false;" rel="tooltip" class="btn btn-mini btn-danger" title="<?php echo $this->_tpl_vars['lang']['delete']; ?>
 (<?php echo $this->_tpl_vars['lang']['_admin_only']; ?>
)" target="_blank"><?php echo $this->_tpl_vars['lang']['delete']; ?>
</a> <a href="<?php echo @_URL; ?>
/admin/modify.php?vid=<?php echo $this->_tpl_vars['video_data']['uniq_id']; ?>
" rel="tooltip" class="btn btn-mini" title="<?php echo $this->_tpl_vars['lang']['edit']; ?>
 (<?php echo $this->_tpl_vars['lang']['_admin_only']; ?>
)" target="_blank"><?php echo $this->_tpl_vars['lang']['edit']; ?>
</a>
        </div>
        <?php endif; ?>
        <h1 class="entry-title" itemprop="name" style="float:right;margin: 0px;"><?php echo $this->_tpl_vars['video_data']['video_title']; ?>

        <?php if ($this->_tpl_vars['video_data']['featured'] == 1): ?><span style="float:right;" class="label label-featured"><div style="font: 14px '';padding-bottom: 1px;"><?php echo $this->_tpl_vars['lang']['featured']; ?>
</div></span><?php endif; ?>
        </h1>
		<meta itemprop="duration" content="<?php echo $this->_tpl_vars['video_data']['iso8601_duration']; ?>
" />
		<meta itemprop="thumbnailUrl" content="<?php echo $this->_tpl_vars['video_data']['thumb_img_url']; ?>
" />
		<meta itemprop="contentURL" content="<?php echo @_URL2; ?>
/videos.php?vid=<?php echo $this->_tpl_vars['video_data']['uniq_id']; ?>
" />
		<meta itemprop="embedURL" content="<?php echo $this->_tpl_vars['video_data']['embed_href']; ?>
" />
		<meta itemprop="uploadDate" content="<?php echo $this->_tpl_vars['video_data']['html5_datetime']; ?>
" />

        <div class="row-fluid" style="width: 200px;float: left;">
            <div class="span6" style="float: left;width: 200px;">
            <ul class="pm-video-adjust">
                <li><a id="player_extend" href="#"><i class="icon-resize-full opac7"></i> <?php echo $this->_tpl_vars['lang']['resize']; ?>
</a></li>
                <li><div id="lights-div"><a class="lightOn" href="#"><?php echo $this->_tpl_vars['lang']['lights_off']; ?>
</a></div></li>
            <ul>
            </div>
        </div>
        </div><!--.pm-video-head-->
        <div style="clear:both"></div>
        <div class="pm-player-full-width">
	   	    <div id="video-wrapper">
            <?php if ($this->_tpl_vars['display_preroll_ad'] == true): ?>
            <div id="preroll_placeholder" class="border-radius4">
				<div class="preroll_countdown">
				<?php echo $this->_tpl_vars['lang']['preroll_ads_timeleft']; ?>
 <span class="preroll_timeleft"><?php echo $this->_tpl_vars['preroll_ad_data']['timeleft_start']; ?>
</span>
				</div>
				<?php echo $this->_tpl_vars['preroll_ad_data']['code']; ?>

				<?php if ($this->_tpl_vars['preroll_ad_data']['skip']): ?>
				<div class="preroll_skip_countdown">
				   <?php echo $this->_tpl_vars['lang']['preroll_ads_skip_msg']; ?>
 <span class="preroll_skip_timeleft"><?php echo $this->_tpl_vars['preroll_ad_data']['skip_delay_seconds']; ?>
</span>
				</div>
				<div class="preroll_skip_button">
				<button class="btn btn-blue hide" id="preroll_skip_btn"><?php echo $this->_tpl_vars['lang']['preroll_ads_skip']; ?>
</button>
				</div>
				<?php endif; ?>
				<?php if ($this->_tpl_vars['preroll_ad_data']['disable_stats'] == 0): ?>
					<img src="<?php echo @_URL; ?>
/ajax.php?p=stats&do=show&aid=<?php echo $this->_tpl_vars['preroll_ad_data']['id']; ?>
&at=<?php echo @_AD_TYPE_PREROLL; ?>
" width="1" height="1" border="0" />
				<?php endif; ?>
            </div>
            <?php else: ?>
                        <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "player.tpl", 'smarty_include_vars' => array('page' => 'detail')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
            <?php endif; ?>
	        </div><!--#video-wrapper-->


            <div class="pm-video-control">
            <div class="row-fluid">
                <div class="span6">
                    <button class="btn btn-small border-radius0 btn-video <?php if ($this->_tpl_vars['bin_rating_vote_value'] == 1): ?>active<?php endif; ?>" id="bin-rating-like" type="button"><?php echo $this->_tpl_vars['lang']['_like']; ?>
</button>
                    <button class="btn btn-small border-radius0 btn-video <?php if ($this->_tpl_vars['bin_rating_vote_value'] == 0 && $this->_tpl_vars['bin_rating_vote_value'] !== false): ?>active<?php endif; ?>" id="bin-rating-dislike" type="button"><?php echo $this->_tpl_vars['lang']['_dislike']; ?>
</button>
                </div>
                
                <div style="float: left;">
                	<div class="pull-right">
                        <button class="btn btn-small border-radius0 btn-video" type="button" data-toggle="button" id="pm-vc-share"><?php echo $this->_tpl_vars['lang']['_share']; ?>
</button>
                        <input type="hidden" name="bin-rating-uniq_id" value="<?php echo $this->_tpl_vars['video_data']['uniq_id']; ?>
">
                        <?php if ($this->_tpl_vars['logged_in']): ?>
                            
                            <?php if ($this->_tpl_vars['isfavorite'] == '1'): ?>
                            <!--<?php echo $this->_tpl_vars['lang']['dp_alt_1']; ?>
-->
                            <form name="addtofavorites" id="addtofavorites" class="form-inline" action="">
                                <input type="hidden" value="<?php echo $this->_tpl_vars['video_data']['uniq_id']; ?>
" name="fav_video_id">
                                <input type="hidden" value="<?php echo $this->_tpl_vars['s_user_id']; ?>
" name="fav_user_id">
                                <button class="btn btn-small border-radius0 btn-video active" id="fav_save_button" type="button"><?php echo $this->_tpl_vars['lang']['remove_from_fav']; ?>
</button>
                            </form>
                            <?php elseif (@_FAV_LIMIT == $this->_tpl_vars['countfavorites']): ?>
                             <a href="<?php echo @_URL; ?>
/favorites.php?a=show" class="btn btn-small border-radius0"><?php echo $this->_tpl_vars['lang']['dp_alt_2']; ?>
</a>
                            <?php else: ?>
                            <form name="addtofavorites" id="addtofavorites" class="form-inline" action="">
                                <input type="hidden" value="<?php echo $this->_tpl_vars['video_data']['uniq_id']; ?>
" name="fav_video_id">
                                <input type="hidden" value="<?php echo $this->_tpl_vars['s_user_id']; ?>
" name="fav_user_id">
                                <button class="btn btn-small border-radius0 btn-video" id="fav_save_button" type="button"><?php echo $this->_tpl_vars['lang']['add_to_fav']; ?>
</button>
                            </form>
                            <?php endif; ?>
                        <?php else: ?>
                          <!--<?php echo $this->_tpl_vars['lang']['dp_alt_1']; ?>
-->
                        <?php endif; ?>
                        <button class="btn btn-small border-radius0 btn-video" type="button" data-toggle="button" id="pm-vc-report" title="<?php echo $this->_tpl_vars['lang']['report_video']; ?>
">ارسال گزارش</button>
					</div>
                </div>
            </div>
            </div><!--.pm-video-control-->

            <div id="bin-rating-response" class="hide well well-small"></div>
            <div id="bin-rating-like-confirmation" class="hide well well-small alert alert-well">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <p><?php echo $this->_tpl_vars['lang']['confirm_like']; ?>
</p>
                <div class="row-fluid">
                    <div class="panel-1">
                    <a href="http://www.facebook.com/sharer.php?u=<?php echo $this->_tpl_vars['facebook_like_href']; ?>
&amp;t=<?php echo $this->_tpl_vars['facebook_like_title']; ?>
" onclick="javascript:window.open(this.href,
      '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;" rel="tooltip" title="اشتراک در فیسبوک"><i class="pm-vc-sprite facebook-icon"></i></a>
                    <a href="http://twitter.com/home?status=Watching%20<?php echo $this->_tpl_vars['facebook_like_title']; ?>
%20on%20<?php echo $this->_tpl_vars['facebook_like_href']; ?>
" onclick="javascript:window.open(this.href,
      '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;" rel="tooltip" title="اشتراک در توییتر"><i class="pm-vc-sprite twitter-icon"></i></a>
                    <a href="https://plus.google.com/share?url=<?php echo $this->_tpl_vars['facebook_like_href']; ?>
" onclick="javascript:window.open(this.href,
      '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;" rel="tooltip" title="اشتراک در گوگل پلاس"><i class="pm-vc-sprite google-icon"></i></a>
                    </div>
                    <div class="panel-3">
                    <div class="input-prepend"><span class="add-on">لینک</span><input name="share_video_link" id="share_video_link" type="text" value="<?php echo $this->_tpl_vars['video_data']['video_href']; ?>
" class="span11" onClick="SelectAll('share_video_link');"></div>
                    </div>
                </div>
            </div>
            <div id="bin-rating-dislike-confirmation" class="hide-dislike hide well well-small">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <p><?php echo $this->_tpl_vars['lang']['confirm_dislike']; ?>
</p>
            </div><!--#bin-rating-like-confirmation-->
    
    
            <div id="pm-vc-report-content" class="hide well well-small alert alert-well">
                <div id="report-confirmation" class="hide"></div>
                <form name="reportvideo" action="" method="POST" class="form-inline">
                  <input type="hidden" id="name" name="name" class="input-small" value="<?php if ($this->_tpl_vars['logged_in']): ?><?php echo $this->_tpl_vars['s_name']; ?>
<?php endif; ?>">
                  <input type="hidden" id="email" name="email" class="input-small" value="<?php if ($this->_tpl_vars['logged_in']): ?><?php echo $this->_tpl_vars['s_email']; ?>
<?php endif; ?>">
                
                  <select name="reason" class="input-medium inp-small">
                    <option value="<?php echo $this->_tpl_vars['lang']['report_form1']; ?>
" selected="selected"><?php echo $this->_tpl_vars['lang']['report_form1']; ?>
</option>
                    <option value="<?php echo $this->_tpl_vars['lang']['report_form4']; ?>
"><?php echo $this->_tpl_vars['lang']['report_form4']; ?>
</option>
                    <option value="<?php echo $this->_tpl_vars['lang']['report_form5']; ?>
"><?php echo $this->_tpl_vars['lang']['report_form5']; ?>
</option>
                    <option value="<?php echo $this->_tpl_vars['lang']['report_form6']; ?>
"><?php echo $this->_tpl_vars['lang']['report_form6']; ?>
</option>
                    <option value="<?php echo $this->_tpl_vars['lang']['report_form7']; ?>
"><?php echo $this->_tpl_vars['lang']['report_form7']; ?>
</option>
                  </select>
                  
                  <?php if (! $this->_tpl_vars['logged_in']): ?>
                    <input type="text" name="imagetext" class="input-small inp-small" autocomplete="off" placeholder="<?php echo $this->_tpl_vars['lang']['confirm_comment']; ?>
">
                    <img src="<?php echo @_URL; ?>
/include/securimage_show.php?sid=<?php echo smarty_echo_securimage_sid(array(), $this);?>
" id="securimage-report" alt="" class="border-radius3">
                    <button class="btn btn-small btn-link" onclick="document.getElementById('securimage-report').src = '<?php echo @_URL; ?>
/include/securimage_show.php?sid=' + Math.random(); return false;"><i class="icon-refresh"></i> </button>
                  <?php endif; ?>
                  <button type="submit" name="Submit" class="btn btn-danger" value="<?php echo $this->_tpl_vars['lang']['submit_send']; ?>
"><?php echo $this->_tpl_vars['lang']['report_video']; ?>
</button>
                  <input type="hidden" name="p" value="detail">
                  <input type="hidden" name="do" value="report">
                  <input type="hidden" name="vid" value="<?php echo $this->_tpl_vars['video_data']['uniq_id']; ?>
">
                </form>
            </div><!-- #pm-vc-report-content-->
    
            <div id="pm-vc-share-content" class="alert alert-well">
                <div class="row-fluid">
                    <div class="panel-3">
                    <a href="http://www.facebook.com/sharer.php?u=<?php echo $this->_tpl_vars['facebook_like_href']; ?>
&amp;t=<?php echo $this->_tpl_vars['facebook_like_title']; ?>
" onclick="javascript:window.open(this.href,
      '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;" rel="tooltip" title="اشتراک در فیسبوک"><i class="pm-vc-sprite facebook-icon"></i></a>
                    <a href="http://twitter.com/home?status=Watching%20<?php echo $this->_tpl_vars['facebook_like_title']; ?>
%20on%20<?php echo $this->_tpl_vars['facebook_like_href']; ?>
" onclick="javascript:window.open(this.href,
      '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;" rel="tooltip" title="اشتراک در توییتر"><i class="pm-vc-sprite twitter-icon"></i></a>
                    <a href="https://plus.google.com/share?url=<?php echo $this->_tpl_vars['facebook_like_href']; ?>
" onclick="javascript:window.open(this.href,
      '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;" rel="tooltip" title="اشتراک در گوگل پلاس"><i class="pm-vc-sprite google-icon"></i></a>  
                    </div>
                    <div class="panel-2">
                    <button class="btn border-radius0 btn-video" type="button" id="pm-vc-embed"><?php echo $this->_tpl_vars['lang']['_embed']; ?>
</button>
                    <button class="btn border-radius0 btn-video" type="button" data-toggle="button" id="pm-vc-email"><i class="icon-envelope"></i> <?php echo $this->_tpl_vars['lang']['email_video']; ?>
</button>
                    </div>
                    <div class="panel-1">
                    <div class="input-prepend"><span class="add-on">لینک</span><input name="video_link" id="video_link" dir="ltr" type="text" value="<?php echo $this->_tpl_vars['video_data']['video_href']; ?>
" class="input-medium" onClick="SelectAll('video_link');"></div>
                    </div>
                </div>
    
                <div id="pm-vc-embed-content">
                  <hr />
                  <textarea name="pm-embed-code" style="margin: 0px 0px 9px;width: 637px;max-width: 637px;height: 92px;max-height: 92px;direction: ltr;font-family: monospace;" id="pm-embed-code" rows="3" class="span12" onClick="SelectAll('pm-embed-code');"><?php echo $this->_tpl_vars['embedcode_to_share']; ?>
</textarea>
                </div>
                <div id="pm-vc-email-content">
                    <hr />
                    <div id="share-confirmation" class="hide well well-small"></div>
                    <form name="sharetofriend" action="" method="POST" class="form-inline">
                      <input type="text" id="name" name="name" class="input-small inp-small" value="<?php echo $this->_tpl_vars['s_name']; ?>
" placeholder="<?php echo $this->_tpl_vars['lang']['your_name']; ?>
">
                      <input type="text" id="email" style="width: 180px;" name="email" class="input-small inp-small" placeholder="<?php echo $this->_tpl_vars['lang']['friends_email']; ?>
">
                      <?php if (! $this->_tpl_vars['logged_in']): ?>   
                          <input type="text" name="imagetext" class="input-small inp-small" autocomplete="off" placeholder="<?php echo $this->_tpl_vars['lang']['confirm_comment']; ?>
">
                          <img src="<?php echo @_URL; ?>
/include/securimage_show.php?sid=<?php echo smarty_echo_securimage_sid(array(), $this);?>
" id="securimage-share" alt="">
                          <button class="btn btn-small btn-link" onclick="document.getElementById('securimage-share').src = '<?php echo @_URL; ?>
/include/securimage_show.php?sid=' + Math.random(); return false;"><i class="icon-refresh"></i> </button>
                      <?php endif; ?>
                      <input type="hidden" name="p" value="detail">
                      <input type="hidden" name="do" value="share">
                      <input type="hidden" name="vid" value="<?php echo $this->_tpl_vars['video_data']['uniq_id']; ?>
">
                      <button type="submit" name="Submit" class="btn btn-success"><?php echo $this->_tpl_vars['lang']['submit_send']; ?>
</button>
                    </form>
                </div>
            </div><!-- #pm-vc-share-content -->
            
			<div class="row-fluid pm-author-data">
                <div class="span2">
                    <span class="pm-avatar"><a href="<?php echo $this->_tpl_vars['video_data']['author_profile_href']; ?>
"><img src="<?php echo $this->_tpl_vars['video_data']['author_avatar_url']; ?>
" height="50" width="50" alt="" class="img-polaroid" border="0"></a></span>
                </div>

                <div style="width: 440px;float: right;padding-right: 10px;">
                	<div class="pm-submit-data"><?php echo $this->_tpl_vars['lang']['articles_published']; ?>
 <time datetime="<?php echo $this->_tpl_vars['video_data']['html5_datetime']; ?>
" title="<?php echo $this->_tpl_vars['video_data']['full_datetime']; ?>
"><?php echo $this->_tpl_vars['video_data']['time_since_added']; ?>
 <?php echo $this->_tpl_vars['lang']['ago']; ?>
</time> <?php echo $this->_tpl_vars['lang']['articles_by']; ?>
 <a href="<?php echo @_URL; ?>
/profile.<?php echo @_FEXT; ?>
?u=<?php echo $this->_tpl_vars['video_data']['author_username']; ?>
"><?php echo $this->_tpl_vars['video_data']['author_name']; ?>
</a> <?php echo $this->_tpl_vars['lang']['_in']; ?>
 <?php echo $this->_tpl_vars['category_name']; ?>
</div>   
                    <div class="clearfix"></div>
                    <?php if (@_MOD_SOCIAL && $this->_tpl_vars['logged_in'] == '1' && $this->_tpl_vars['video_data']['author_user_id'] != $this->_tpl_vars['s_user_id']): ?>
                        <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "user-follow-button.tpl", 'smarty_include_vars' => array('profile_data' => $this->_tpl_vars['video_data'],'profile_user_id' => $this->_tpl_vars['video_data']['author_user_id'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
                    <?php endif; ?>
                </div>

                <div class="pm-video-views-count pull-right">
                    <span class="pm-vc-views">
                    <small><?php echo $this->_tpl_vars['video_data']['site_views_formatted']; ?>
 <?php echo $this->_tpl_vars['lang']['views']; ?>
</small>
                    </span>
                    <div class="clearfix"></div>
                    <div class="progress" title="<?php echo $this->_tpl_vars['video_data']['up_vote_count_formatted']; ?>
 <?php echo $this->_tpl_vars['lang']['_likes']; ?>
, <?php echo $this->_tpl_vars['video_data']['down_vote_count_formatted']; ?>
 <?php echo $this->_tpl_vars['lang']['_dislikes']; ?>
">
                      <div class="bar bar-success" id="rating-bar-up-pct" style="width: <?php echo $this->_tpl_vars['video_data']['up_pct']; ?>
%;"></div>
                      <div class="bar bar-danger" id="rating-bar-down-pct" style="width: <?php echo $this->_tpl_vars['video_data']['down_pct']; ?>
%;"></div>
                    </div>
                </div><!--.pm-video-control-->
            </div><!--.pm-author-data-->
        </div><!--end pm-player-full-width -->
	</div>
</div>

<?php if (! empty ( $this->_tpl_vars['video_data']['description'] )): ?>
<h2 class="upper-blue"><?php echo $this->_tpl_vars['lang']['description']; ?>
</h2>
<div style="clear:both"></div>
<div style=" border-bottom: 5px #477E84 solid; margin-top: -10px;"></div>
<div class="description text-exp">
    <p itemprop="description"><?php echo $this->_tpl_vars['video_data']['description']; ?>
</p>
    <p class="show-more"><a href="#" class="show-now"><?php echo $this->_tpl_vars['lang']['show_more']; ?>
</a></p>
</div>
<?php endif; ?>

<?php if (! empty ( $this->_tpl_vars['tags'] )): ?>
<div class="video-tags">
	<strong><?php echo $this->_tpl_vars['lang']['tags']; ?>
</strong>: <?php echo $this->_tpl_vars['tags']; ?>

</div>
<?php endif; ?>

<?php if ($this->_tpl_vars['video_data']['allow_comments'] == '1'): ?>
<h2 class="upper-blue"><?php echo $this->_tpl_vars['lang']['post_comment']; ?>
</h2>
<div style="clear:both"></div>
<div style=" border-bottom: 5px #477E84 solid; margin-top: -10px;"></div>
<div style="clear:both"></div>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'comment-form.tpl', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php if (! $this->_tpl_vars['logged_in'] && ! $this->_tpl_vars['guests_can_comment']): ?>
	<?php echo $this->_tpl_vars['must_sign_in']; ?>

<?php endif; ?>
<?php endif; ?>

<h2 class="upper-blue"><?php echo $this->_tpl_vars['lang']['comments']; ?>
</h2>
<div style="clear:both"></div>
<div style=" border-bottom: 5px #477E84 solid; margin-top: -10px;"></div>
<div style="clear:both"></div>
<div class="pm-comments comment_box">
<?php if ($this->_tpl_vars['video_data']['allow_comments'] == '1'): ?>
<?php if ($this->_tpl_vars['comment_count'] == 0): ?>
    <ul class="pm-ul-comments">
    	<li id="preview_comment"></li>
    </ul>
    <div id="be_the_first"><?php echo $this->_tpl_vars['lang']['be_the_first']; ?>
</div>
<?php else: ?>
    <span id="comment-list-container">
		<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "comment-list.tpl", 'smarty_include_vars' => array('tpl_name' => "video-watch")));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		<!-- comment pagination -->
		<?php if ($this->_tpl_vars['comment_pagination_obj'] != ''): ?>
			<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "comment-pagination.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		<?php endif; ?>
	</span>
<?php endif; ?>
<?php else: ?>
	<div><?php echo $this->_tpl_vars['lang']['comments_disabled']; ?>
</div>
<?php endif; ?>
</div>
		</div><!-- #primary -->
        </div><!-- .span8 -->
        
        <div class="pm-sidebar">
		<div id="secondary" style="margin-left: 15px;width: 300px;margin-top: 4px;">
        <?php if ($this->_tpl_vars['ad_3'] != ''): ?><?php echo $this->_tpl_vars['ad_3']; ?>
<?php endif; ?>
        <div class="widget-related widget" id="pm-related">
          <ul class="nav nav-tabs" id="myTab">
            <li class="active"><a href="#bestincategory" data-target="#bestincategory" data-toggle="tab"><?php echo $this->_tpl_vars['lang']['tab_related']; ?>
</a></li>
            <li> / </li>
            <li><a href="#popular" data-target="#popular" data-toggle="tab"><?php echo $this->_tpl_vars['lang']['_popular']; ?>
</a></li>
          </ul>
 
          <div id="pm-tabs" class="tab-content">
            <div class="tab-pane fade in active" id="bestincategory">
                <ul class="pm-ul-top-videos">
                
			<?php $_from = $this->_tpl_vars['related_video_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['k'] => $this->_tpl_vars['related_video_data']):
?>
			  <li>
				<div class="pm-li-top-videos">
				<span class="pm-video-thumb pm-thumb-106 pm-thumb-top border-radius2">
				<span class="pm-video-li-thumb-info">
				<?php if ($this->_tpl_vars['related_video_data']['duration'] != 0): ?><span class="pm-label-duration border-radius3 opac7"><?php echo $this->_tpl_vars['related_video_data']['duration']; ?>
</span><?php endif; ?>
				</span>
				<a href="<?php echo $this->_tpl_vars['related_video_data']['video_href']; ?>
" class="pm-thumb-fix pm-thumb-106"><span class="pm-thumb-fix-clip"><img src="<?php echo $this->_tpl_vars['related_video_data']['thumb_img_url']; ?>
" alt="<?php echo $this->_tpl_vars['related_video_data']['attr_alt']; ?>
" width="106"><span class="vertical-align"></span></span></a>
				</span>
				<h3 dir="rtl"><a href="<?php echo $this->_tpl_vars['related_video_data']['video_href']; ?>
" class="pm-title-link"><?php echo smarty_fewchars(array('s' => $this->_tpl_vars['related_video_data']['video_title'],'length' => 30), $this);?>
</a></h3>
				<span class="pm-video-attr-numbers">
                <small><?php echo $this->_tpl_vars['lang']['articles_by']; ?>
 <a href="<?php echo $this->_tpl_vars['video_data']['author_profile_href']; ?>
"><?php echo $this->_tpl_vars['related_video_data']['author_name']; ?>
</a></small> <br />
                <small><?php echo $this->_tpl_vars['related_video_data']['views_compact']; ?>
 <?php echo $this->_tpl_vars['lang']['views']; ?>
 / <?php echo $this->_tpl_vars['related_video_data']['likes_compact']; ?>
 <?php echo $this->_tpl_vars['lang']['_likes']; ?>
 (
                <time datetime="<?php echo $this->_tpl_vars['video_data']['html5_datetime']; ?>
" title="<?php echo $this->_tpl_vars['video_data']['full_datetime']; ?>
"><?php echo $this->_tpl_vars['related_video_data']['time_since_added']; ?>
 <?php echo $this->_tpl_vars['lang']['ago']; ?>
</time>
                )</small>
                </span>
				</div>
			  </li>
            <?php endforeach; else: ?>
				  <?php echo $this->_tpl_vars['lang']['top_videos_msg2']; ?>

			<?php endif; unset($_from); ?>
           
                </ul>
            </div>
            <div class="tab-pane fade" id="popular">
                <ul class="pm-ul-top-videos">
                <?php $_from = $this->_tpl_vars['popular_video_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['k'] => $this->_tpl_vars['popular_video_data']):
?>
				  <li>
					<div class="pm-li-top-videos">
					<span class="pm-video-thumb pm-thumb-106 pm-thumb-top border-radius2">
					<span class="pm-video-li-thumb-info">
					<?php if ($this->_tpl_vars['popular_video_data']['duration'] != 0): ?><span class="pm-label-duration border-radius3 opac7"><?php echo $this->_tpl_vars['popular_video_data']['duration']; ?>
</span><?php endif; ?>
					</span>
                    <a href="<?php echo $this->_tpl_vars['popular_video_data']['video_href']; ?>
" class="pm-thumb-fix pm-thumb-106"><span class="pm-thumb-fix-clip"><img src="<?php echo $this->_tpl_vars['popular_video_data']['thumb_img_url']; ?>
" alt="<?php echo $this->_tpl_vars['popular_video_data']['attr_alt']; ?>
" width="106"><span class="vertical-align"></span></span></a>
					</span>
					<h3 dir="ltr"><a href="<?php echo $this->_tpl_vars['popular_video_data']['video_href']; ?>
" class="pm-title-link"><?php echo smarty_fewchars(array('s' => $this->_tpl_vars['popular_video_data']['video_title'],'length' => 30), $this);?>
</a></h3>
					<span class="pm-video-attr-numbers">
              		<small><?php echo $this->_tpl_vars['lang']['articles_by']; ?>
 <a href="<?php echo $this->_tpl_vars['popular_video_data']['author_profile_href']; ?>
"><?php echo $this->_tpl_vars['popular_video_data']['author_name']; ?>
</a></small> <br />
              	    <small><?php echo $this->_tpl_vars['popular_video_data']['views_compact']; ?>
 <?php echo $this->_tpl_vars['lang']['views']; ?>
 / <?php echo $this->_tpl_vars['popular_video_data']['likes_compact']; ?>
 <?php echo $this->_tpl_vars['lang']['_likes']; ?>
 (
                    <time datetime="<?php echo $this->_tpl_vars['popular_video_data']['html5_datetime']; ?>
" title="<?php echo $this->_tpl_vars['popular_video_data']['full_datetime']; ?>
"><?php echo $this->_tpl_vars['popular_video_data']['time_since_added']; ?>
 <?php echo $this->_tpl_vars['lang']['ago']; ?>
</time>
                    )</small>
                    </span>
                   <?php if ($this->_tpl_vars['popular_video_data']['featured']): ?>
					 <span class="pm-video-li-info">
					    <span class="label label-featured"><?php echo $this->_tpl_vars['lang']['_feat']; ?>
</span>
					 </span>
				   <?php endif; ?>
				 </div>
			    </li>
				<?php endforeach; else: ?>
				  <?php echo $this->_tpl_vars['lang']['top_videos_msg2']; ?>

				<?php endif; unset($_from); ?>
                </ul>
            </div>
          </div>
          
        </div><!-- .shadow-div -->
        
		</div><!-- #secondary -->
        </div><!-- #sidebar -->
      </div><!-- .row-fluid -->
    </div><!-- .container-fluid -->
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "footer.tpl", 'smarty_include_vars' => array('p' => 'detail','tpl_name' => "video-watch")));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>