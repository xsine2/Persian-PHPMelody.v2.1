<?php /* Smarty version 2.6.20, created on 2014-06-24 07:03:21
         compiled from profile-favorites.tpl */ ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'header.tpl', 'smarty_include_vars' => array('no_index' => '1','p' => 'favorites')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?> 
<div id="wrapper">
    <div class="container-fluid">
    <?php if ($this->_tpl_vars['logged_in'] == '1'): ?>
      <div class="row-fluid">
        <div class="span12 extra-space">
            <nav id="second-nav" class="tabbable" role="navigation">
                <ul class="nav nav-tabs pull-right">
                <li><a href="<?php echo @_URL; ?>
/edit_profile.<?php echo @_FEXT; ?>
"><?php echo $this->_tpl_vars['lang']['edit_profile']; ?>
</a></li>
                <li><a href="<?php echo @_URL; ?>
/upload_avatar.<?php echo @_FEXT; ?>
"><?php echo $this->_tpl_vars['lang']['update_avatar']; ?>
</a></li>
                <li class="active"><a href="<?php echo @_URL; ?>
/favorites.<?php echo @_FEXT; ?>
?a=show"><?php echo $this->_tpl_vars['lang']['my_favorites']; ?>
</a></li>
                <?php if (@_ALLOW_USER_SUGGESTVIDEO == '1'): ?>
                <li><a href="<?php echo @_URL; ?>
/suggest.<?php echo @_FEXT; ?>
"><?php echo $this->_tpl_vars['lang']['suggest']; ?>
</a></li>
                <?php endif; ?>
                <?php if (@_ALLOW_USER_UPLOADVIDEO == '1'): ?>
                <li><a href="<?php echo @_URL; ?>
/upload.<?php echo @_FEXT; ?>
"><?php echo $this->_tpl_vars['lang']['upload_video']; ?>
</a></li>
                <?php endif; ?>
                <li><a href="<?php echo @_URL; ?>
/memberlist.<?php echo @_FEXT; ?>
"><?php echo $this->_tpl_vars['lang']['members_list']; ?>
</a></li>
				<?php if (isset ( $this->_tpl_vars['mm_profilemenu_insert'] )): ?><?php echo $this->_tpl_vars['mm_profilemenu_insert']; ?>
<?php endif; ?>
                </ul>
            </nav><!-- #site-navigation -->
        </div>
      </div>
	  <?php endif; ?>
      <div class="row-fluid">
        <div class="span12 extra-space">
		<div id="primary">
        <a name="videoplayer" id="videoplayer"></a>
        <h1><?php echo $this->_tpl_vars['lang']['my_favorites']; ?>
</h1>
        
        <div class="row-fluid">
        	<div class="span8">
                <?php if ($this->_tpl_vars['action'] == 'show'): ?>
                    <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "player.tpl", 'smarty_include_vars' => array('page' => 'favorites')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
                <?php endif; ?>
                
                <?php if ($this->_tpl_vars['action'] == 'show'): ?>
                    <?php if (! empty ( $this->_tpl_vars['problem'] )): ?>
                    <?php echo $this->_tpl_vars['problem']; ?>

                    <?php endif; ?>
                <?php endif; ?>
                
        
                <?php if ($this->_tpl_vars['action'] == 'add' || $this->_tpl_vars['action'] == 'del'): ?>
                    <?php if (! empty ( $this->_tpl_vars['add_problem'] )): ?>
                        <?php echo $this->_tpl_vars['add_problem']; ?>
<br />
                        <a href="javascript:history.back()"><?php echo $this->_tpl_vars['lang']['return_to_ppage']; ?>
</a>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
            <div class="span4">
                <div class="widget">
                    <ul class="pm-ul-playlist-videos">
                    <?php $_from = $this->_tpl_vars['favorite_videos_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['favorite_foreach'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['favorite_foreach']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['k'] => $this->_tpl_vars['video_data']):
        $this->_foreach['favorite_foreach']['iteration']++;
?>
                      <li id="favorite-<?php echo $this->_foreach['favorite_foreach']['iteration']; ?>
">
                        <div class="pm-li-video">
                            <span class="pm-video-thumb pm-thumb-106 pm-thumb border-radius2">
                            <span class="pm-video-li-thumb-info">
                            <?php if ($this->_tpl_vars['video_data']['yt_length'] != 0): ?><span class="pm-label-duration border-radius3 opac7"><?php echo $this->_tpl_vars['video_data']['duration']; ?>
</span><?php endif; ?>
                            <?php if ($this->_tpl_vars['video_data']['mark_new']): ?><span class="label label-new"><?php echo $this->_tpl_vars['lang']['_new']; ?>
</span><?php endif; ?>
                            <?php if ($this->_tpl_vars['video_data']['mark_popular']): ?><span class="label label-pop"><?php echo $this->_tpl_vars['lang']['_popular']; ?>
</span><?php endif; ?>
                            </span>
                            <a href="<?php echo $this->_tpl_vars['video_data']['video_href']; ?>
" class="pm-thumb-fix pm-thumb-106" onClick="ajax_request('video', 'p=favorites&do=request&vid=<?php echo $this->_tpl_vars['video_data']['uniq_id']; ?>
', '#embed_Playerholder', 'html', true);return false;"><span class="pm-thumb-fix-clip"><img src="<?php echo $this->_tpl_vars['video_data']['thumb_img_url']; ?>
" alt="<?php echo $this->_tpl_vars['video_data']['attr_alt']; ?>
" width="106"><span class="vertical-align"></span></span></a>
                            </span>
                            
                            <h3 dir="ltr"><a href="<?php echo $this->_tpl_vars['video_data']['video_href']; ?>
" onClick="ajax_request('video', 'p=favorites&do=request&vid=<?php echo $this->_tpl_vars['video_data']['uniq_id']; ?>
', '#embed_Playerholder', 'html', true);return false;" class="pm-title-link" title="<?php echo $this->_tpl_vars['video_data']['attr_alt']; ?>
"><?php echo $this->_tpl_vars['video_data']['video_title']; ?>
</a></h3>
                            <div class="pm-video-attr">
                                <span class="pm-video-attr-author"><?php echo $this->_tpl_vars['lang']['articles_by']; ?>
 <a href="<?php echo $this->_tpl_vars['video_data']['author_profile_href']; ?>
"><?php echo $this->_tpl_vars['video_data']['author_name']; ?>
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
                            <p class="pm-video-attr-desc"></p>
                            
                            <?php if ($this->_tpl_vars['video_data']['featured']): ?>
                            <span class="pm-video-li-info">
                                <span class="label label-featured"><?php echo $this->_tpl_vars['lang']['_feat']; ?>
</span>
                            </span>
                            <?php endif; ?>
                            <?php if ($this->_tpl_vars['self']): ?>
                            <span class="li-controlers">
                            <button class="btn-mini btn-remove border-radius3" onclick="onpage_delete_favorite('<?php echo $this->_tpl_vars['video_data']['uniq_id']; ?>
', '#favorite-<?php echo $this->_foreach['favorite_foreach']['iteration']; ?>
'); return false;" rel="tooltip" title="<?php echo $this->_tpl_vars['lang']['delete_from_fav']; ?>
"><i class="icon-trash"></i></button>
                            </span>
                            <?php endif; ?>
                        </div>
                      </li>
                    <?php endforeach; else: ?>
                        <?php echo $this->_tpl_vars['lang']['top_videos_msg2']; ?>

                    <?php endif; unset($_from); ?>
                    </ul>
                </div><!-- /widget -->
            </div>
        </div>
        

        <?php if ($this->_tpl_vars['share_link'] != ''): ?>
        <h2 class="upper-blue"><?php echo $this->_tpl_vars['lang']['myfavorites_share']; ?>
</h2> 
        <div class="alert alert-well">
        <div class="row-fluid">
            <div class="span9 panel-1">
            <div class="input-prepend"><span class="add-on">URL</span><input name="video_link" id="video_link" type="text" value="<?php echo $this->_tpl_vars['share_link']; ?>
" class="span10 inp-small" onClick="SelectAll('video_link');"> </div>
            </div>

            <div class="span3" align="right">
            <a href="http://www.facebook.com/sharer.php?u=<?php echo $this->_tpl_vars['share_link']; ?>
&amp;t=<?php echo $this->_tpl_vars['meta_title']; ?>
" onclick="javascript:window.open(this.href,
'', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;" rel="tooltip" title="Share on FaceBook"><i class="pm-vc-sprite facebook-icon"></i></a>
            <a href="http://twitter.com/home?status=Watching%20<?php echo $this->_tpl_vars['meta_title']; ?>
%20on%20<?php echo $this->_tpl_vars['share_link']; ?>
" onclick="javascript:window.open(this.href,
'', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;" rel="tooltip" title="Share on Twitter"><i class="pm-vc-sprite twitter-icon"></i></a>
            <a href="https://plus.google.com/share?url=<?php echo $this->_tpl_vars['share_link']; ?>
" onclick="javascript:window.open(this.href,
'', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;" rel="tooltip" title="Share on Google+"><i class="pm-vc-sprite google-icon"></i></a>
            </div>
        </div>
        </div>
        <?php endif; ?>
		</div><!-- #primary -->
    </div><!-- #content -->
  </div><!-- .row-fluid -->
</div><!-- .container-fluid -->     
        
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'footer.tpl', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?> 