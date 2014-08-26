<?php /* Smarty version 2.6.20, created on 2014-06-28 17:18:12
         compiled from profile.tpl */ ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'header.tpl', 'smarty_include_vars' => array('p' => 'general')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?> 
<div id="wrapper" class="profile-page">
    <div class="container-fluid">
      <div class="row-fluid">
        <div class="span12">
		<div id="primary">
		<div class="span3">
        <div class="profile-avatar">
        <span class="img_polaroid"><img src="<?php echo $this->_tpl_vars['avatar']; ?>
" border="0" alt="" class="img-polaroid" width="180" height="180"></span>
		<?php if ($this->_tpl_vars['profile_data']['id'] == $this->_tpl_vars['s_user_id']): ?>
		<span class="profile-avatar-edit"><a href="<?php echo @_URL; ?>
/upload_avatar.<?php echo @_FEXT; ?>
"><?php echo $this->_tpl_vars['lang']['update_avatar']; ?>
</a></span>
		<?php endif; ?>
        </div>
        
        <div id="profile-tabs">
            <div class="tabbable tabs-left">
              <ul class="nav nav-tabs">
				<?php if ($this->_tpl_vars['profile_data']['id'] != $this->_tpl_vars['s_user_id']): ?>
					<li class="active"><a href="#pm-pro-about" data-toggle="tab"><?php echo $this->_tpl_vars['lang']['about_me']; ?>
</a></li>
				<?php else: ?>
					<li><a href="#pm-pro-about" data-toggle="tab"><?php echo $this->_tpl_vars['lang']['about_me']; ?>
</a></li>
				<?php endif; ?>
                <?php if (@_MOD_SOCIAL): ?>
                 <?php if ($this->_tpl_vars['s_user_id'] == $this->_tpl_vars['profile_data']['id']): ?>
					<li class="active"><a href="#pm-pro-activity-stream" data-toggle="tab"><?php echo $this->_tpl_vars['lang']['activity_newsfeed']; ?>
</a></li>
				 <?php endif; ?>
				 <?php if ($this->_tpl_vars['s_user_id'] == $this->_tpl_vars['profile_data']['id'] || $this->_tpl_vars['am_following']): ?>
					<li><a href="#pm-pro-user-activity" data-toggle="tab"><?php echo $this->_tpl_vars['lang']['my_activity']; ?>
</a></li>
				 <?php endif; ?>
                <?php else: ?>
					<li class="active"><a href="#pm-pro-about" data-toggle="tab"><?php echo $this->_tpl_vars['lang']['about_me']; ?>
</a></li>
				<?php endif; ?>
                <li><a href="#pm-pro-fav" data-toggle="tab"><?php echo $this->_tpl_vars['lang']['my_favorites']; ?>
</a></li>
                <li><a href="#pm-pro-own" data-toggle="tab"><?php echo $this->_tpl_vars['lang']['mysubmissions']; ?>
</a></li>
				<?php if (@_MOD_SOCIAL): ?>
					<li><a href="#pm-pro-followers" data-toggle="tab"><?php echo $this->_tpl_vars['lang']['activity_followers']; ?>
</a></li>
					<li><a href="#pm-pro-following" data-toggle="tab"><?php echo $this->_tpl_vars['lang']['activity_following']; ?>
</a></li>
				<?php endif; ?>
              </ul>
            </div> <!-- /tabbable -->
        </div>

        </div>
        <div class="span9 vertical-menu">
        	<div class="row-fluid">
            	<div class="span7">
                	<h2 class="username"><?php echo $this->_tpl_vars['full_name']; ?>
<?php if ($this->_tpl_vars['user_is_banned']): ?> <span class="label label-important"><?php echo $this->_tpl_vars['lang']['user_account_banned_label']; ?>
</span><?php endif; ?>
                    <?php if (@_MOD_SOCIAL && $this->_tpl_vars['logged_in'] == 1 && $this->_tpl_vars['s_user_id'] != $this->_tpl_vars['profile_data']['id']): ?>
                        <?php if ($this->_tpl_vars['profile_data']['is_following_me']): ?>
                            <span class="label pm-follows"><?php echo $this->_tpl_vars['lang']['follow_following_you']; ?>
</span>
                        <?php endif; ?>
                    <?php endif; ?>
					</h2>
                    
                    <ul class="pm-pro-counts">
                        <li><span class="count-number"><?php echo $this->_tpl_vars['total_submissions']; ?>
</span> <span class="count-what"><a href="#pm-pro-own" data-toggle="tab"><?php echo $this->_tpl_vars['lang']['videos']; ?>
</a></span></li>
						<?php if (@_MOD_SOCIAL): ?>
                        <li><span class="count-number"><?php echo $this->_tpl_vars['profile_data']['followers_count']; ?>
</span> <span class="count-what"><a href="#pm-pro-followers" data-toggle="tab"><?php echo $this->_tpl_vars['lang']['activity_followers']; ?>
</a></span></li>
						<li class="last-li"><span class="count-number"><?php echo $this->_tpl_vars['profile_data']['following_count']; ?>
</span> <span class="count-what"><a href="#pm-pro-following" data-toggle="tab"><?php echo $this->_tpl_vars['lang']['activity_following']; ?>
</a></span></li>
						<?php endif; ?>
                    </ul>
                </div>
            	<div class="span5">
                    <div align="right">
                        <?php if (@_MOD_SOCIAL && $this->_tpl_vars['logged_in'] == 1 && $this->_tpl_vars['s_user_id'] != $this->_tpl_vars['profile_data']['id']): ?>
                            <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'user-follow-button.tpl', 'smarty_include_vars' => array('profile_user_id' => $this->_tpl_vars['profile_data']['id'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

        	<div class="clearfix"></div>
		
		


<div class="tab-content">

			<?php if (@_MOD_SOCIAL && $this->_tpl_vars['profile_data']['id'] == $this->_tpl_vars['s_user_id']): ?>
            <div class="tab-pane fade" id="pm-pro-about">
			<?php else: ?>
			<div class="tab-pane fade in active" id="pm-pro-about">
			<?php endif; ?>
              <ul class="pm-pro-data">
                  <li><i class="icon-map-marker"></i> <?php echo $this->_tpl_vars['country']; ?>
</li>
                  <li><i class="icon-user"></i> <?php echo $this->_tpl_vars['reg_date']; ?>
</li>
                  <li><i class="icon-off"></i> <?php echo $this->_tpl_vars['status']; ?>
</li>
                  <li><i class="icon-eye-open"></i> <?php echo $this->_tpl_vars['last_seen']; ?>
</li>
              </ul>
            <h4><?php echo $this->_tpl_vars['lang']['about_me']; ?>
</h4>
            <?php if (! empty ( $this->_tpl_vars['about'] )): ?>
            <p><?php echo $this->_tpl_vars['about']; ?>
</p>
            <?php else: ?>
			<p><?php echo $this->_tpl_vars['lang']['profile_msg_about_empty']; ?>
</p>
            <?php endif; ?>
	  		<?php if (isset ( $this->_tpl_vars['mm_profile_info_inject'] )): ?><?php echo $this->_tpl_vars['mm_profile_info_inject']; ?>
<?php endif; ?>

			<h4><?php echo $this->_tpl_vars['lang']['_social']; ?>
</h4>
            <ul class="pm-pro-social">
			<?php if (isset ( $this->_tpl_vars['social_website'] ) && $this->_tpl_vars['social_website'] != ''): ?> 
				<li><a href="<?php echo $this->_tpl_vars['social_website']; ?>
" target="_blank" rel="nofollow"><i class="pm-vc-sprite ico_social_site"></i> <?php echo $this->_tpl_vars['social_website']; ?>
</a></li>
			<?php else: ?>
				<li><i class="pm-vc-sprite ico_social_site ico-social-none"></i> n/a</li>
			<?php endif; ?> 
			<?php if (isset ( $this->_tpl_vars['social_facebook'] ) && $this->_tpl_vars['social_facebook'] != ''): ?>
				<li><a href="<?php echo $this->_tpl_vars['social_facebook']; ?>
" target="_blank" rel="nofollow"><i class="pm-vc-sprite ico_social_fb"></i> <?php echo $this->_tpl_vars['social_facebook']; ?>
</a></li>
			<?php else: ?>
				<li><i class="pm-vc-sprite ico_social_fb ico-social-none"></i> n/a</li>
			<?php endif; ?>
			<?php if (isset ( $this->_tpl_vars['social_twitter'] ) && $this->_tpl_vars['social_twitter'] != ''): ?>
				<li><a href="<?php echo $this->_tpl_vars['social_twitter']; ?>
" target="_blank" rel="nofollow"><i class="pm-vc-sprite ico_social_twitter"></i> <?php echo $this->_tpl_vars['social_twitter']; ?>
</a></li>
			<?php else: ?>
				<li><i class="pm-vc-sprite ico_social_twitter ico-social-none"></i> n/a</li>
			<?php endif; ?>
			<?php if (isset ( $this->_tpl_vars['social_lastfm'] ) && $this->_tpl_vars['social_lastfm'] != ''): ?>
				<li><a href="<?php echo $this->_tpl_vars['social_lastfm']; ?>
" target="_blank" rel="nofollow"><i class="pm-vc-sprite ico_social_lastfm"></i> <?php echo $this->_tpl_vars['social_lastfm']; ?>
</a></li>
			<?php else: ?>
				<li><i class="pm-vc-sprite ico_social_lastfm ico-social-none"></i> n/a</li>
			<?php endif; ?>
			<?php if (isset ( $this->_tpl_vars['mm_profile_webfields_inject'] )): ?><?php echo $this->_tpl_vars['mm_profile_webfields_inject']; ?>
<?php endif; ?>
			</ul>

            </div>
            <div class="tab-pane fade" id="pm-pro-fav">
            <h4><?php echo $this->_tpl_vars['lang']['my_favorites']; ?>
</h4>

            <?php if ($this->_tpl_vars['favorite'] == 1): ?>
                <ul class="pm-ul-browse-videos thumbnails">
                <?php $_from = $this->_tpl_vars['fav_video_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['k'] => $this->_tpl_vars['video_data']):
?>
                  <li>
                    <div class="pm-li-video">
                        <span class="pm-video-thumb pm-thumb-145 pm-thumb border-radius2">
                        <span class="pm-video-li-thumb-info">
                        <?php if ($this->_tpl_vars['video_data']['yt_length'] != 0): ?><span class="pm-label-duration border-radius3 opac7"><?php echo $this->_tpl_vars['video_data']['duration']; ?>
</span><?php endif; ?>
                        <?php if ($this->_tpl_vars['video_data']['mark_new']): ?><span class="label label-new"><?php echo $this->_tpl_vars['lang']['_new']; ?>
</span><?php endif; ?>
                            <?php if ($this->_tpl_vars['video_data']['mark_popular']): ?><span class="label label-pop"><?php echo $this->_tpl_vars['lang']['_popular']; ?>
</span><?php endif; ?>
                        </span>
                        <a href="<?php echo $this->_tpl_vars['video_data']['video_href']; ?>
" class="pm-thumb-fix pm-thumb-145"><span class="pm-thumb-fix-clip"><img src="<?php echo $this->_tpl_vars['video_data']['thumb_img_url']; ?>
" alt="<?php echo $this->_tpl_vars['video_data']['video_title']; ?>
" width="145"><span class="vertical-align"></span></span></a>
                        </span>
                        
                        <h3 dir="ltr"><a href="<?php echo $this->_tpl_vars['video_data']['video_href']; ?>
" class="pm-title-link" title="<?php echo $this->_tpl_vars['video_data']['video_title']; ?>
"><?php echo $this->_tpl_vars['video_data']['video_title']; ?>
</a></h3>
                        <div class="pm-video-attr">
                            <span class="pm-video-attr-author"><?php echo $this->_tpl_vars['lang']['articles_by']; ?>
 <a href="<?php echo $this->_tpl_vars['video_data']['author_profile_href']; ?>
"><?php echo $this->_tpl_vars['video_data']['author_username']; ?>
</a></span>
                            <span class="pm-video-attr-since"><small><?php echo $this->_tpl_vars['lang']['added']; ?>
 <time datetime="<?php echo $this->_tpl_vars['video_data']['html5_datetime']; ?>
" title="<?php echo $this->_tpl_vars['video_data']['full_datetime']; ?>
"><?php echo $this->_tpl_vars['video_data']['time_since_added']; ?>
 <?php echo $this->_tpl_vars['lang']['ago']; ?>
</time></small></span>
                            <span class="pm-video-attr-numbers"><small><?php echo $this->_tpl_vars['video_data']['views_compact']; ?>
 <?php echo $this->_tpl_vars['lang']['views']; ?>
 / <?php echo $this->_tpl_vars['video_data']['likes_compact']; ?>
 <?php echo $this->_tpl_vars['lang']['_likes']; ?>
</small></span>
                        </div>
                        <p class="pm-video-attr-desc"><?php echo $this->_tpl_vars['video_data']['excerpt']; ?>
</p>
                        <?php if ($this->_tpl_vars['video_data']['featured']): ?>
                        <span class="pm-video-li-info">
                            <span class="label label-featured"><?php echo $this->_tpl_vars['lang']['_feat']; ?>
</span>
                        </span>
                        <?php endif; ?>
                    </div>
                  </li>
                <?php endforeach; else: ?>
                    <?php echo $this->_tpl_vars['lang']['profile_msg_list_empty']; ?>

                <?php endif; unset($_from); ?>
                </ul>
        	 <?php else: ?>
             <?php echo $this->_tpl_vars['lang']['favorites_msg2']; ?>

             <?php endif; ?> <!-- /$favorite -->
            </div>
            <div class="tab-pane fade" id="pm-pro-own">
            <h4><?php echo $this->_tpl_vars['lang']['submittedby']; ?>
 <?php echo $this->_tpl_vars['full_name']; ?>
</h4>

            <ul class="pm-ul-browse-videos thumbnails">
            <?php $_from = $this->_tpl_vars['submitted_video_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['k'] => $this->_tpl_vars['video_data']):
?>
              <li>
                <div class="pm-li-video<?php if ($this->_tpl_vars['video_data']['pending_approval']): ?> pending<?php endif; ?>">
                    <span class="pm-video-thumb pm-thumb-145 pm-thumb border-radius2">
                    <span class="pm-video-li-thumb-info">
                    <?php if ($this->_tpl_vars['video_data']['pending_approval']): ?><span class="label label-pending"><?php echo $this->_tpl_vars['lang']['pending_approval']; ?>
</span><?php endif; ?>
                    <?php if ($this->_tpl_vars['video_data']['yt_length'] != 0): ?><span class="pm-label-duration border-radius3 opac7"><?php echo $this->_tpl_vars['video_data']['duration']; ?>
</span><?php endif; ?>
                    <?php if ($this->_tpl_vars['video_data']['mark_new']): ?><span class="label label-new"><?php echo $this->_tpl_vars['lang']['_new']; ?>
</span><?php endif; ?>
                    <?php if ($this->_tpl_vars['video_data']['mark_popular']): ?><span class="label label-pop"><?php echo $this->_tpl_vars['lang']['_popular']; ?>
</span><?php endif; ?>
                    </span>
                    <a href="<?php echo $this->_tpl_vars['video_data']['video_href']; ?>
" class="pm-thumb-fix pm-thumb-145"><span class="pm-thumb-fix-clip"><img src="<?php echo $this->_tpl_vars['video_data']['thumb_img_url']; ?>
" alt="<?php echo $this->_tpl_vars['video_data']['video_title']; ?>
" width="145"><span class="vertical-align"></span></span></a>
                    </span>
                    
                    <h3 dir="ltr"><a href="<?php echo $this->_tpl_vars['video_data']['video_href']; ?>
" class="pm-title-link" title="<?php echo $this->_tpl_vars['video_data']['video_title']; ?>
"><?php echo $this->_tpl_vars['video_data']['video_title']; ?>
</a></h3>
                    <div class="pm-video-attr">
                        <span class="pm-video-attr-author"><?php echo $this->_tpl_vars['lang']['articles_by']; ?>
 <a href="<?php echo $this->_tpl_vars['video_data']['author_profile_href']; ?>
"><?php echo $this->_tpl_vars['video_data']['author_username']; ?>
</a></span>
                        <span class="pm-video-attr-since"><small><?php echo $this->_tpl_vars['lang']['added']; ?>
 <time datetime="<?php echo $this->_tpl_vars['video_data']['html5_datetime']; ?>
" title="<?php echo $this->_tpl_vars['video_data']['full_datetime']; ?>
"><?php echo $this->_tpl_vars['video_data']['time_since_added']; ?>
 <?php echo $this->_tpl_vars['lang']['ago']; ?>
</time></small></span>
                        <span class="pm-video-attr-numbers"><small><?php echo $this->_tpl_vars['video_data']['views_compact']; ?>
 <?php echo $this->_tpl_vars['lang']['views']; ?>
 / <?php echo $this->_tpl_vars['video_data']['likes_compact']; ?>
 <?php echo $this->_tpl_vars['lang']['_likes']; ?>
</small></span>
                    </div>
                    <p class="pm-video-attr-desc"><?php echo $this->_tpl_vars['video_data']['excerpt']; ?>
</p>
                    <?php if ($this->_tpl_vars['video_data']['featured']): ?>
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
            
            <?php if (count ( $this->_tpl_vars['submitted_video_list'] ) == 16): ?>
            <a href="search.php?keywords=<?php echo $this->_tpl_vars['username']; ?>
&btn=Search&t=user" class="btn btn-small" title="<?php echo $this->_tpl_vars['lang']['profile_watch_all']; ?>
"><?php echo $this->_tpl_vars['lang']['profile_watch_all']; ?>
</a>
            <?php endif; ?>
            </div>
			<?php if (@_MOD_SOCIAL): ?>
			<div class="tab-pane fade" id="pm-pro-followers">
			<h4><?php echo $this->_tpl_vars['lang']['activity_followers']; ?>
</h4>
				<div id="pm-pro-followers-content"></div>
			</div>
			
			<div class="tab-pane fade" id="pm-pro-following">
			<?php if (is_array ( $this->_tpl_vars['who_to_follow_list'] )): ?>
			<div class="pm-pro-suggest-follow">
				<a href="#" id="hide_who_to_follow" class="pm-pro-suggest-hide">&times; <?php echo $this->_tpl_vars['lang']['close']; ?>
</a>
				<h4><?php echo $this->_tpl_vars['lang']['follow_suggested']; ?>
</h4>
				<ul class="pm-ul-memberlist">
				<?php $_from = $this->_tpl_vars['who_to_follow_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['k'] => $this->_tpl_vars['user_data']):
?>
				  <li>
					<span class="pm-ml-username"><a href="<?php echo $this->_tpl_vars['user_data']['profile_url']; ?>
"><?php echo $this->_tpl_vars['user_data']['name']; ?>
</a><?php if ($this->_tpl_vars['user_data']['user_is_banned']): ?> <span class="label label-important"><?php echo $this->_tpl_vars['lang']['user_account_banned_label']; ?>
</span><?php endif; ?>
					<?php if (@_MOD_SOCIAL && $this->_tpl_vars['logged_in'] == '1' && $this->_tpl_vars['user_data']['id'] != $this->_tpl_vars['s_user_id']): ?>
					<?php if ($this->_tpl_vars['user_data']['is_following_me']): ?>
						<span class="label pm-follows"><?php echo $this->_tpl_vars['lang']['follow_following_you']; ?>
</span>
					<?php endif; ?>               
					<?php endif; ?>              
					</span>
					<span class="pm-ml-avatar"><a href="<?php echo $this->_tpl_vars['user_data']['profile_url']; ?>
"><img src="<?php echo $this->_tpl_vars['user_data']['avatar_url']; ?>
" alt="<?php echo $this->_tpl_vars['user_data']['username']; ?>
" width="60" height="60" border="0" class="img-polaroid"></a></span>
					<span class="pm-ml-country"><small><i class="icon-map-marker"></i> <?php echo $this->_tpl_vars['user_data']['country_label']; ?>
</small></span>
					<span class="pm-ml-lastseen"><small><i class="icon-eye-open"></i> <?php echo $this->_tpl_vars['user_data']['last_seen']; ?>
</small></span>
					
					<div class="pm-ml-buttons">
					<?php if (@_MOD_SOCIAL && $this->_tpl_vars['logged_in'] == '1' && $this->_tpl_vars['user_data']['id'] != $this->_tpl_vars['s_user_id']): ?>
						<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "user-follow-button.tpl", 'smarty_include_vars' => array('profile_data' => $this->_tpl_vars['user_data'],'profile_user_id' => $this->_tpl_vars['user_data']['id'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
					<?php endif; ?>
					</div>
					<div class="clearfix"></div>
				  </li>
				<?php endforeach; endif; unset($_from); ?>
				</ul>
			</div>
			<?php endif; ?>

			<h4><?php echo $this->_tpl_vars['lang']['activity_following']; ?>
</h4>
				<div id="pm-pro-following-content"></div>
			</div>

			<?php if ($this->_tpl_vars['s_user_id'] == $this->_tpl_vars['profile_data']['id'] || $this->_tpl_vars['am_following']): ?>
			<div class="tab-pane fade" id="pm-pro-user-activity"> 
			<h4><?php echo $this->_tpl_vars['lang']['my_activity']; ?>
</h4>
				<div id="pm-pro-user-activity-content"></div>
			</div>
			<?php endif; ?>
			
			<?php if ($this->_tpl_vars['s_user_id'] == $this->_tpl_vars['profile_data']['id']): ?>
			<div class="tab-pane fade in active" id="pm-pro-activity-stream">	
			<h4><?php echo $this->_tpl_vars['lang']['activity_newsfeed']; ?>
</h4>
                <form name="user-update-status" method="post" action="" onsubmit="update_status();return false;" >
                    <textarea class="span12" name="post-status" ></textarea>
                    <br />
                    <button type="submit" name="btn-update-status" class="btn btn-blue" /><?php echo $this->_tpl_vars['lang']['status_update']; ?>
</button>
                </form>
				<div id="pm-pro-activity-stream-content">
					<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'activity-stream.tpl', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
				</div>
			</div>
			<?php endif; ?>
			<?php endif; ?>
			
          </div><!-- /tab-content -->
          
        </div>

		<input type="hidden" name="profile_user_id" value="<?php echo $this->_tpl_vars['profile_data']['id']; ?>
" />
		</div><!-- #primary -->
        </div><!-- #content -->
      </div><!-- .row-fluid -->
    </div><!-- .container-fluid -->
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'footer.tpl', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>