<?php /* Smarty version 2.6.20, created on 2014-06-24 07:05:44
         compiled from index.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'smarty_fewchars', 'index.tpl', 55, false),)), $this); ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'header.tpl', 'smarty_include_vars' => array('p' => 'index')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?> 
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
    padding-bottom: 5px;"><?php echo $this->_tpl_vars['lang']['featured']; ?>
 : </div> &nbsp; <?php echo $this->_tpl_vars['voth_title']; ?>
 </h2>
    <div style="clear:both"></div>
				<?php if ($this->_tpl_vars['display_preroll_ad'] == true): ?>
					<div id="preroll_placeholder">
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
						<br />
						<button class="btn btn-blue hide" id="preroll_skip_btn"><?php echo $this->_tpl_vars['lang']['preroll_ads_skip']; ?>
</button>
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
$this->_smarty_include(array('smarty_include_tpl_file' => "player.tpl", 'smarty_include_vars' => array('page' => 'index')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
				<?php endif; ?>
				</div>
				<?php if ($this->_tpl_vars['total_playingnow'] > 0): ?>
				<div id="pm-wn">
					<h2 class="upper-blue"><?php echo $this->_tpl_vars['lang']['vbwrn']; ?>
</h2>
					<div class="btn-group btn-group-sort pm-slide-control">
					<button class="btn btn-mini prev" id="pm-slide-prev"><i class="pm-vc-sprite arr-l"></i></button>
					<button class="btn btn-mini next" id="pm-slide-next"><i class="pm-vc-sprite arr-r"></i></button>
					</div>
					<div id="pm-slide">
					<!-- Carousel items -->
					<ul class="pm-ul-wn-videos clearfix" id="pm-ul-wn-videos">
					<?php $_from = $this->_tpl_vars['playingnow']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['k'] => $this->_tpl_vars['video_data']):
?>
					  <li>
						<div class="pm-li-wn-videos">
						<span class="pm-video-thumb pm-thumb-145 pm-thumb-top border-radius2">
						<a href="<?php echo $this->_tpl_vars['video_data']['video_href']; ?>
" class="pm-thumb-fix pm-thumb-145"><span class="pm-thumb-fix-clip"><img src="<?php echo $this->_tpl_vars['video_data']['thumb_img_url']; ?>
" alt="<?php echo $this->_tpl_vars['video_data']['attr_alt']; ?>
" width="160"><span class="vertical-align"></span></span></a>
						</span>
						<h3 dir="rtl"><a href="<?php echo $this->_tpl_vars['video_data']['video_href']; ?>
" class="pm-title-link"><?php echo smarty_fewchars(array('s' => $this->_tpl_vars['video_data']['video_title'],'length' => 25), $this);?>
</a></h3>
						</div>
					  </li>
					<?php endforeach; endif; unset($_from); ?>
					</ul>
					</div><!-- #pm-slide -->
				</div>
				<hr />
				<div class="clear-fix"></div>
		        <?php endif; ?>

			<div class="element-videos">
			<div class="btn-group btn-group-sort">
			<button class="btn btn-small" id="list"><i class="icon-th"></i> </button>
			<button class="btn btn-small" id="grid"><i class="icon-th-list"></i> </button>
			</div>
				<h2 class="upper-blue"><?php echo $this->_tpl_vars['lang']['new_videos']; ?>
</h2>
                <div style="clear:both"></div>
                <div style=" border-bottom: 5px #477E84 solid; margin-top: -10px;"></div>
				<ul class="pm-ul-browse-videos thumbnails" id="pm-grid">
				<?php $_from = $this->_tpl_vars['new_videos']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
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
" alt="<?php echo $this->_tpl_vars['video_data']['attr_alt']; ?>
" width="160"><span class="vertical-align"></span></span></a>
						</span>
						<h3 dir="rtl"><a href="<?php echo $this->_tpl_vars['video_data']['video_href']; ?>
" class="pm-title-link" title="<?php echo $this->_tpl_vars['video_data']['attr_alt']; ?>
"><?php echo smarty_fewchars(array('s' => $this->_tpl_vars['video_data']['video_title'],'length' => 25), $this);?>
</a></h3>
						<div class="pm-video-attr">
							<p class="pm-video-attr-author" style="margin: 0px;"><?php echo $this->_tpl_vars['lang']['articles_by']; ?>
 <a href="<?php echo $this->_tpl_vars['video_data']['author_profile_href']; ?>
"><?php echo $this->_tpl_vars['video_data']['author_name']; ?>
</a></p>
							<p class="pm-video-attr-since"><small><?php echo $this->_tpl_vars['lang']['added']; ?>
 <time datetime="<?php echo $this->_tpl_vars['video_data']['html5_datetime']; ?>
" title="<?php echo $this->_tpl_vars['video_data']['full_datetime']; ?>
"><?php echo $this->_tpl_vars['video_data']['time_since_added']; ?>
 <?php echo $this->_tpl_vars['lang']['ago']; ?>
</time></small></p>
							<p class="pm-video-attr-numbers"><small><?php echo $this->_tpl_vars['video_data']['views_compact']; ?>
 <?php echo $this->_tpl_vars['lang']['views']; ?>
 / <?php echo $this->_tpl_vars['video_data']['likes_compact']; ?>
 <?php echo $this->_tpl_vars['lang']['_likes']; ?>
</small></p>
						</div>
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
			</div><!-- .element-videos -->
			
			<div class="clearfix"></div>
			</div><!-- #primary -->
        </div><!-- .span8 -->

        <div class="span4" id="secondary">
        <?php if ($this->_tpl_vars['ad_5'] != ''): ?>
		<div class="widget">
        	<div class="pm-ad-zone" align="center"><?php echo $this->_tpl_vars['ad_5']; ?>
</div>
        </div><!-- .widget -->
        <?php endif; ?>

        <div class="widget">
            <div class="btn-group btn-group-sort pm-slide-control"  style="margin-left:-4px;">
            <button class="btn btn-mini next" id="pm-slide-top-next"><i class="pm-vc-sprite arr-r"></i></button>
            <button class="btn btn-mini prev" id="pm-slide-top-prev"><i class="pm-vc-sprite arr-l"></i></button>
            </div>
            <h4><?php echo $this->_tpl_vars['lang']['top_m_videos']; ?>
</h4>
            <div style="clear:both"></div>
            <div style=" border-bottom: 5px #477E84 solid; margin-top: -10px;"></div>
            <ul class="pm-ul-top-videos" id="pm-ul-top-videos">
			<?php $_from = $this->_tpl_vars['top_videos']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['k'] => $this->_tpl_vars['video_data']):
?>
			  <li>
				<div class="pm-li-top-videos">
				<span class="pm-video-thumb pm-thumb-106 pm-thumb-top border-radius2">
				<span class="pm-video-li-thumb-info">
				<?php if ($this->_tpl_vars['video_data']['yt_length'] != 0): ?><span class="pm-label-duration border-radius3 opac7"><?php echo $this->_tpl_vars['video_data']['duration']; ?>
</span><?php endif; ?>
				</span>
				<a href="<?php echo $this->_tpl_vars['video_data']['video_href']; ?>
" class="pm-thumb-fix pm-thumb-106"><span class="pm-thumb-fix-clip"><img src="<?php echo $this->_tpl_vars['video_data']['thumb_img_url']; ?>
" alt="<?php echo $this->_tpl_vars['video_data']['attr_alt']; ?>
" width="106"><span class="vertical-align"></span></span></a>
				</span>
				<h3 dir="rtl"><a href="<?php echo $this->_tpl_vars['video_data']['video_href']; ?>
" class="pm-title-link"><?php echo smarty_fewchars(array('s' => $this->_tpl_vars['video_data']['video_title'],'length' => 30), $this);?>
</a></h3>
				<span class="pm-video-attr-numbers">
                <small><?php echo $this->_tpl_vars['lang']['articles_by']; ?>
 <a href="<?php echo $this->_tpl_vars['video_data']['author_profile_href']; ?>
"><?php echo $this->_tpl_vars['video_data']['author_name']; ?>
</a></small> <br />
                <small><?php echo $this->_tpl_vars['video_data']['views_compact']; ?>
 <?php echo $this->_tpl_vars['lang']['views']; ?>
 / <?php echo $this->_tpl_vars['video_data']['likes_compact']; ?>
 <?php echo $this->_tpl_vars['lang']['_likes']; ?>
 (
                <time datetime="<?php echo $this->_tpl_vars['video_data']['html5_datetime']; ?>
" title="<?php echo $this->_tpl_vars['video_data']['full_datetime']; ?>
"><?php echo $this->_tpl_vars['video_data']['time_since_added']; ?>
 <?php echo $this->_tpl_vars['lang']['ago']; ?>
</time>
                )</small>
                </span>
				</div>
			  </li>
			<?php endforeach; endif; unset($_from); ?>
            </ul>
            <div class="clearfix"></div>
        </div><!-- .widget -->
        
        <?php if (( $this->_tpl_vars['show_tags'] == 1 ) && ( count ( $this->_tpl_vars['tags'] ) > 0 )): ?>
		<div class="widget">
			<h4><?php echo $this->_tpl_vars['lang']['tags']; ?>
</h4>
            <div style="clear:both"></div>
            <div style=" border-bottom: 5px #477E84 solid; margin-top: -10px;"></div>
            <div style="clear:both;"></div>
            <div style="margin-right:0px;">
            <?php $_from = $this->_tpl_vars['tags']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['k'] => $this->_tpl_vars['tag']):
?>
                <?php echo $this->_tpl_vars['tag']['href']; ?>

            <?php endforeach; endif; unset($_from); ?>
            </div>
        </div><!-- .widget -->
        <?php endif; ?>
        
        <?php if ($this->_tpl_vars['show_stats'] == 1): ?>
        <div class="widget">
        <h4><?php echo $this->_tpl_vars['lang']['site_stats']; ?>
</h4>
        <div style="clear:both"></div>
        <div style=" border-bottom: 5px #477E84 solid; margin-top: -10px;"></div>
        <ul class="pm-stats-data">
        	<li><a style="margin-right: -84px;" href="<?php echo @_URL; ?>
/memberlist.<?php echo @_FEXT; ?>
?do=online"><?php echo $this->_tpl_vars['lang']['online_users']; ?>
</a> <span class="pm-stats-count"><?php echo $this->_tpl_vars['stats']['online_users']; ?>
</span></li>
            <li><a href="<?php echo @_URL; ?>
/memberlist.<?php echo @_FEXT; ?>
"><?php echo $this->_tpl_vars['lang']['total_users']; ?>
</a> <span class="pm-stats-count"><?php echo $this->_tpl_vars['stats']['users']; ?>
</span></li>
            <li><?php echo $this->_tpl_vars['lang']['total_videos']; ?>
 <span class="pm-stats-count"><?php echo $this->_tpl_vars['stats']['videos']; ?>
</span></li>
        	<li><?php echo $this->_tpl_vars['lang']['videos_added_lw']; ?>
 <span class="pm-stats-count"><?php echo $this->_tpl_vars['stats']['videos_last_week']; ?>
</span></li>
        </ul>
		</div><!-- .widget -->
        <?php endif; ?>
        
        <?php if (@_MOD_ARTICLE == 1): ?>
        <div class="widget">
			<h4><?php echo $this->_tpl_vars['lang']['articles_latest']; ?>
</h4>
            <div style="clear:both"></div>
            <div style=" border-bottom: 5px #477E84 solid; margin-top: -10px;"></div>
            <ul class="pm-ul-home-articles" id="pm-ul-home-articles">
            <?php $_from = $this->_tpl_vars['articles']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['id'] => $this->_tpl_vars['article']):
?>
				<li <?php if ($this->_tpl_vars['article']['featured'] == '1'): ?>class="sticky-article"<?php endif; ?>>
				<article>
				<?php if ($this->_tpl_vars['article']['meta']['_post_thumb_show'] != ''): ?>
				<div class="pm-article-thumb">
					<a href="<?php echo $this->_tpl_vars['article']['link']; ?>
" class="pm-title-link" title="<?php echo $this->_tpl_vars['article']['title']; ?>
"><img src="<?php echo @_ARTICLE_ATTACH_DIR; ?>
/<?php echo $this->_tpl_vars['article']['meta']['_post_thumb_show']; ?>
" align="left" width="55" height="55" border="0" alt="<?php echo $this->_tpl_vars['article']['title']; ?>
"></a>
				</div>
				<?php endif; ?>
				<h6 dir="rtl" class="ellipsis"><a href="<?php echo $this->_tpl_vars['article']['link']; ?>
" class="pm-title-link" title="<?php echo $this->_tpl_vars['article']['title']; ?>
"><?php echo smarty_fewchars(array('s' => $this->_tpl_vars['article']['title'],'length' => 92), $this);?>
</a></h6>
				<p class="pm-article-preview">
					<?php if ($this->_tpl_vars['article']['meta']['_post_thumb_show'] == ''): ?>
						<span class="minDesc"><?php echo smarty_fewchars(array('s' => $this->_tpl_vars['article']['excerpt'],'length' => 130), $this);?>
</span>
					<?php else: ?>
						<span class="minDesc"><?php echo smarty_fewchars(array('s' => $this->_tpl_vars['article']['excerpt'],'length' => 100), $this);?>
</span>
					<?php endif; ?>
				</p>
				</article>
				</li>
            <?php endforeach; endif; unset($_from); ?>
            </ul>
        </div><!-- .widget -->
        <?php endif; ?>
		</div><!-- .span4 -->
      </div><!-- .row-fluid -->
    </div><!-- .container-fluid -->
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'footer.tpl', 'smarty_include_vars' => array('p' => 'index')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?> 